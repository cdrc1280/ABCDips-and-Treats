"""
Back-office module generator.

Reproduces the reference module (Catalog → Categories: app/Domains/Catalog, pages/category,
tests/Feature/Catalog/Category) for another model from a spec in specs.py, file for file:
migration · model patch · Data · FilterData · Permission enum · Actions · Queries · Resource ·
Controller · routes · menu migration · permissions.ts · types · schemas · pages · tests.

Generated code is a starting point that follows the architecture exactly; modules with
real workflows get hand-written additions on top (see each module's Actions/).

Usage (repo root):
    scripts/t.sh php scripts/modgen/schema.php      # refresh schema.json (needs the toolbox)
    python3 scripts/modgen/modgen.py supplier ingredient
"""
import json
import os
import re
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.abspath(os.path.join(HERE, '..', '..'))
sys.path.insert(0, HERE)
from specs import SPECS  # noqa: E402

_schema_path = os.path.join(HERE, 'schema.json')
SCHEMA = json.load(open(_schema_path)) if os.path.exists(_schema_path) else {}


# ─── naming helpers ──────────────────────────────────────────────────────────
def camel(s):
    parts = re.split(r'[_\-\s]+', s)
    out = parts[0] + ''.join(p[:1].upper() + p[1:] for p in parts[1:])
    return out[:1].lower() + out[1:]


def studly(s):
    c = camel(s)
    return c[:1].upper() + c[1:]


def kebab(s):
    s = re.sub(r'([a-z0-9])([A-Z])', r'\1-\2', s)
    return re.sub(r'[_\s]+', '-', s).lower()


def snake(s):
    return kebab(s).replace('-', '_')


def prune_unused_private_statics(content):
    """Drop private static helpers (date/url/option...) a generated class never calls (keeps Larastan clean)."""
    for name in re.findall(r'private static function (\w+)\(', content):
        if re.search(r'(?:self|static)::' + name + r'\(', content):
            continue
        m = re.search(r'\n(?:[ \t]*/\*\*(?:(?!\*/).)*\*/\n)?[ \t]*private static function ' + name + r'\(', content, re.S)
        i = content.index('{', m.end())
        depth, j = 0, i
        while True:
            depth += {'{': 1, '}': -1}.get(content[j], 0)
            if depth == 0:
                break
            j += 1
        content = content[:m.start()] + content[j + 1:]
    return content


def w(path, content):
    if path.endswith('.php'):
        content = prune_unused_private_statics(content)
    full = os.path.join(ROOT, path)
    os.makedirs(os.path.dirname(full), exist_ok=True)
    with open(full, 'w') as f:
        f.write(content)


def php_str(v):
    return "'" + str(v).replace('\\', '\\\\').replace("'", "\\'") + "'"


def ts_str(v):
    return "'" + str(v).replace('\\', '\\\\').replace("'", "\\'") + "'"


NUMERIC = {'int', 'decimal', 'money'}
COLLECTION = {'json_list', 'items', 'tags', 'images', 'multiselect'}


def col_cast(c):
    t = c.get('type', 'string')
    return {'int': '(int)', 'relation': '(string)', 'decimal': '(float)', 'money': '(float)'}.get(t, '(string)')


def col_rules(c):
    t = c.get('type', 'string')
    r = ["'required'" if c.get('required') else "'nullable'"]
    if t == 'int':
        r.append("'integer'")
    elif t == 'relation':
        r = ["'bail'"] + r + ["'uuid'"]
    elif t in ('decimal', 'money'):
        r.append("'numeric'")
    else:
        r += ["'string'", f"'max:{c.get('max', 255)}'"]
    if 'min' in c:
        r.append(f"'min:{c['min']}'")
    if t == 'relation':
        r.append(f"Rule::exists('{c['table']}', 'id')")
    return r


def col_ts(c):
    return 'number | null' if c.get('type', 'string') in ('int', 'decimal', 'money') else 'string | null'


class Mod:
    def __init__(self, key, s):
        self.key = key
        self.s = s
        self.D = s['domain']
        self.M = s['model']
        self.T = s['table']
        self.thing = s.get('thing', self.M)
        self.P = s.get('plural', kebab(self.thing) + 's')
        self.Pc = self.P  # Wayfinder keeps the route-name segment (kebab) as the directory name
        self.PS = studly(self.P)
        self.var = camel(self.thing)
        self.page = kebab(self.thing)
        self.label = s.get('label', re.sub(r'(?<!^)([A-Z])', r' \1', self.thing))
        self.labels = s.get('labels', self.label + 's')
        self.fields = s['fields']
        for f in self.fields:
            f.setdefault('label', f['name'].replace('_', ' ').capitalize())
            f.setdefault('section', 'Details')
            f.setdefault('required', False)
        self.title = s['title_field']
        self.subtitle = s.get('subtitle_field')
        self.status_field = s.get('status_field')
        self.toggle = s.get('toggle_field')
        self.mode = s.get('mode', 'crud')
        self.ops = s.get('ops', ['view', 'create', 'edit', 'delete'] if self.mode == 'crud' else ['view', 'edit', 'delete'])
        cols = [c.split(':')[0] for c in SCHEMA.get(self.T, [])]
        self.has_soft_deletes = 'deleted_at' in cols

    def perm_case(self, op):
        return self.PS + studly(op)

    def perm_value(self, op):
        return f'{self.P}-{op}'

    @property
    def enum(self):
        return f'{self.thing}Permission'

    @property
    def route_base(self):
        return f'admin.{self.P}'

    def php_type(self, f):
        t = f['type']
        if t == 'relation':
            base = 'string'
        elif t == 'int' or (t == 'select' and f.get('int')):
            base = 'int'
        elif t in ('decimal', 'money'):
            base = 'float'
        elif t == 'bool':
            return 'bool'
        elif t == 'image':
            return '?UploadedFile'
        elif t in COLLECTION:
            return 'array'
        else:
            base = 'string'
        return base if (f['required'] and 'default' not in f) else '?' + base

    def data_fields(self):
        return [f for f in self.fields if not f.get('readonly')]


# ─── backend ─────────────────────────────────────────────────────────────────
def gen_migration_soft_deletes(m):
    if m.has_soft_deletes or m.s.get('no_soft_deletes'):
        return
    w(f'database/migrations/2026_11_14_12{m.s["seq"]:02d}00_add_soft_deletes_to_{m.T}_table.php', f'''<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{{
    public function up(): void
    {{
        if (! Schema::hasColumn('{m.T}', 'deleted_at')) {{
            Schema::table('{m.T}', function (Blueprint $table) {{
                $table->softDeletes();
            }});
        }}
    }}

    public function down(): void
    {{
        Schema::table('{m.T}', function (Blueprint $table) {{
            $table->dropSoftDeletes();
        }});
    }}
}};
''')


def gen_model_patch(m):
    path = os.path.join(ROOT, f'app/Domains/{m.D}/Models/{m.M}.php')
    s = open(path).read()
    if 'function scopeSearch' in s:
        return
    uniques = [f['name'] for f in m.fields if f.get('unique')]
    imports = ['App\\Shared\\Concerns\\LogsModelActivity', 'Illuminate\\Database\\Eloquent\\Builder'] + ([] if m.s.get('no_soft_deletes') else ['Illuminate\\Database\\Eloquent\\SoftDeletes']) + [
               f'App\\Domains\\{m.D}\\Data\\{m.thing}FilterData']
    traits = ['LogsModelActivity'] + ([] if m.s.get('no_soft_deletes') else ['SoftDeletes'])
    if uniques:
        imports.append('App\\Shared\\Concerns\\FreesUniqueValuesOnDelete')
        traits.append('FreesUniqueValuesOnDelete')
    for imp in imports:
        if f'use {imp};' not in s:
            s = re.sub(r'(namespace [^;]+;\n\n?)', lambda mo: mo.group(1) + f'use {imp};\n', s, count=1)
    class_body = re.search(r'class \w+[^{]*\{\n', s)
    after = s[class_body.end():]
    tm = re.match(r'(\s*use )([^;]+);', after)
    if tm:
        existing = [t.strip() for t in tm.group(2).split(',')]
        merged = sorted(set(existing + traits))
        after = after[:tm.start()] + tm.group(1) + ', '.join(merged) + ';' + after[tm.end():]
    else:
        after = '    use ' + ', '.join(sorted(traits)) + ';\n\n' + after
    s = s[:class_body.end()] + after
    if uniques and 'uniqueOnDelete' not in s:
        s = re.sub(r'(class \w+[^{]*\{\n\s*use [^;]+;\n)', lambda mo: mo.group(1) + f"\n    /** @var list<string> */\n    protected array $uniqueOnDelete = [{', '.join(php_str(u) for u in uniques)}];\n", s, count=1)

    kw = []
    for c in m.s['search']:
        if '.' in c:
            rel, col = c.split('.', 1)
            kw.append(f"->orWhereHas('{rel}', fn (Builder $r) => $r->whereLike('{col}', \"%{{$search}}%\"))")
        else:
            kw.append(f"->orWhereLike('{c}', \"%{{$search}}%\")")
    if kw[0].startswith('->orWhere('):
        kw[0] = kw[0].replace('->orWhere(', '->where(', 1)
    elif kw[0].startswith('->orWhereHas('):
        kw[0] = kw[0].replace('->orWhereHas(', '->whereHas(', 1)
    kw_code = '\n                '.join(kw)
    filters = []
    for flt in m.s.get('filters', []):
        prop = camel(flt['name'])
        col = flt.get('column', flt['name'])
        if flt['type'] == 'boolean':
            filters.append(f"            ->when($filters->{prop} !== null, fn (Builder $q) => $q->where('{col}', $filters->{prop}))")
        elif flt['type'] == 'date_from':
            filters.append(f"            ->when($filters->{prop}, fn (Builder $q, string $v) => $q->whereDate('{col}', '>=', $v))")
        elif flt['type'] == 'date_to':
            filters.append(f"            ->when($filters->{prop}, fn (Builder $q, string $v) => $q->whereDate('{col}', '<=', $v))")
        elif flt['type'] == 'scope':
            filters.append(f"            ->when($filters->{prop} !== null && $filters->{prop} !== '', fn (Builder $q) => {flt['php']})")
        else:
            filters.append(f"            ->when($filters->{prop} !== null && $filters->{prop} !== '', fn (Builder $q) => $q->where('{col}', $filters->{prop}))")
    filters_code = ('\n' + '\n'.join(filters)) if filters else ''
    scope = f'''
    /**
     * string → global search; array → {m.thing}FilterData fields.
     *
     * @param  Builder<{m.M}>  $query
     * @return Builder<{m.M}>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {{
        if (is_string($search)) {{
            return $query->where(fn (Builder $q) => $q
                {kw_code});
        }}

        $filters = {m.thing}FilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword)){filters_code};
    }}
}}
'''
    s = s.rstrip()
    assert s.endswith('}')
    s = s[:-1].rstrip() + '\n' + scope
    open(path, 'w').write(s)


def gen_filter_data(m):
    props = ['        public ?string $keyword = null,']
    prep = []
    for flt in m.s.get('filters', []):
        prop = camel(flt['name'])
        n = flt['name']
        if flt['type'] == 'boolean':
            props.append(f'        public ?bool ${prop} = null,')
            prep.append(f"""        if (array_key_exists('{n}', $properties) && ! is_bool($properties['{n}'])) {{
            $properties['{n}'] = in_array($properties['{n}'], ['', 'all', null], true) ? null : filter_var($properties['{n}'], FILTER_VALIDATE_BOOLEAN);
        }}""")
        elif flt['type'] == 'relation':
            props.append(f'        public ?string ${prop} = null,')
            prep.append(f"""        if (array_key_exists('{n}', $properties)) {{
            $properties['{n}'] = \\Illuminate\\Support\\Str::isUuid((string) $properties['{n}']) ? (string) $properties['{n}'] : null;
        }}""")
        elif flt.get('int'):
            props.append(f'        public ?int ${prop} = null,')
            prep.append(f"""        if (array_key_exists('{n}', $properties)) {{
            $properties['{n}'] = is_numeric($properties['{n}']) ? (int) $properties['{n}'] : null;
        }}""")
        else:
            props.append(f'        public ?string ${prop} = null,')
    w(f'app/Domains/{m.D}/Data/{m.thing}FilterData.php', f'''<?php

namespace App\\Domains\\{m.D}\\Data;

use Spatie\\LaravelData\\Attributes\\MapInputName;
use Spatie\\LaravelData\\Attributes\\MapOutputName;
use Spatie\\LaravelData\\Data;
use Spatie\\LaravelData\\Mappers\\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class {m.thing}FilterData extends Data
{{
    public function __construct(
{chr(10).join(props)}
    ) {{}}

    public static function prepareForPipeline(array $properties): array
    {{
{chr(10).join(prep)}

        return $properties;
    }}
}}
''')


def rules_for(m, f):
    t = f['type']
    r = ["'required'" if f['required'] else "'nullable'"]
    if t == 'bool':
        r = ["'boolean'"]
    elif t in ('string', 'phone', 'code', 'slug', 'password'):
        r += ["'string'", f"'max:{f.get('max', 255)}'"]
    elif t == 'email':
        r += ["'email'", f"'max:{f.get('max', 255)}'"]
    elif t in ('text', 'richtext'):
        r += ["'string'", f"'max:{f.get('max', 5000 if t == 'text' else 100000)}'"]
    elif t == 'int':
        r.append("'integer'")
    elif t in ('decimal', 'money'):
        r.append("'numeric'")
    elif t in ('date', 'datetime'):
        r.append("'date'")
    elif t == 'time':
        r.append("'date_format:H:i'")
    elif t == 'select':
        r.append("Rule::in([" + ', '.join(php_str(o[0]) for o in f['options']) + "])")
    elif t == 'relation':
        r = ["'bail'"] + r + ["'uuid'"]
        r.append(f"Rule::exists('{f['relation']['table']}', 'id')" + ("->whereNull('deleted_at')" if f['relation'].get('soft') else ''))
    elif t == 'image':
        r = ["'nullable'", "'image'", "'mimes:jpg,jpeg,png,webp'", "'max:4096'"]
    if t == 'slug':
        r.append("'alpha_dash'")
    if 'min' in f and t in NUMERIC:
        r.append(f"'min:{f['min']}'")
    if 'max_value' in f:
        r.append(f"'max:{f['max_value']}'")
    if f.get('after'):
        r.append(f"'after_or_equal:{f['after']}'")
    for extra in f.get('rules', []):
        r.append(extra)
    if f.get('unique'):
        r.append(f"Rule::unique('{m.T}', '{f['name']}')->ignore($id)->whereNull('deleted_at')")
    return r


def php_default(f):
    if f['required'] and 'default' in f:
        dv = f['default']
        return php_str(dv) if isinstance(dv, str) else str(dv)
    return 'null'


def gen_data(m):
    fields = m.data_fields()

    def needs_value(f):
        return f['required'] and 'default' not in f and f['type'] not in ('bool', 'image')

    ordered = [f for f in fields if needs_value(f)] + [f for f in fields if not needs_value(f)]
    props, prep, rules = [], [], []
    has_image = has_slug = False
    for f in ordered:
        n, t, p = f['name'], f['type'], camel(f['name'])
        if t in ('json_list', 'items'):
            props.append(f'        public array ${p} = [],')
            prep.append(f"        $properties['{n}'] = array_values(array_filter((array) ($properties['{n}'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));")
            req = "'required', " if f['required'] else ''
            rules.append(f"            '{n}' => [{req}'array', 'max:{f.get('max', 100)}'],")
            for c in f['columns']:
                rules.append(f"            '{n}.*.{c['name']}' => [{', '.join(col_rules(c))}],")
            continue
        if t == 'multiselect':
            props.append(f'        public array ${p} = [],')
            prep.append(f"        $properties['{n}'] = array_values(array_unique(array_filter(array_map('strval', (array) ($properties['{n}'] ?? [])))));")
            req = "'required', " if f['required'] else ''
            rules.append(f"            '{n}' => [{req}'array'],")
            rules.append(f"            '{n}.*' => ['string', Rule::in({f['allowed']})],")
            continue
        if t == 'tags':
            props.append(f'        public array ${p} = [],')
            prep.append(f"        $properties['{n}'] = is_array($properties['{n}'] ?? null) ? array_values(array_filter(array_map('trim', $properties['{n}']))) : array_values(array_filter(array_map('trim', explode(',', (string) ($properties['{n}'] ?? '')))));")
            rules.append(f"            '{n}' => ['array', 'max:20'],")
            rules.append(f"            '{n}.*' => ['string', 'max:50'],")
            continue
        if t == 'images':
            has_image = True
            props.append(f'        public array ${p} = [],')
            props.append(f'        public array $keep{studly(n)} = [],')
            prep.append(f"        $properties['{n}'] = array_values(array_filter((array) ($properties['{n}'] ?? [])));")
            prep.append(f"        $properties['keep_{n}'] = array_values(array_filter((array) ($properties['keep_{n}'] ?? [])));")
            rules.append(f"            '{n}' => ['array', 'max:{f.get('max', 10)}'],")
            rules.append(f"            '{n}.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],")
            rules.append(f"            'keep_{n}' => ['array'],")
            rules.append(f"            'keep_{n}.*' => ['string', 'max:500'],")
            continue
        if t == 'image':
            has_image = True
            props.append(f'        public ?UploadedFile ${p} = null,')
            props.append(f'        public bool $remove{studly(n)} = false,')
            prep.append(f"        $properties['remove_{n}'] = filter_var($properties['remove_{n}'] ?? false, FILTER_VALIDATE_BOOLEAN);")
            rules.append(f"            '{n}' => [{', '.join(rules_for(m, f))}],")
            rules.append(f"            'remove_{n}' => ['boolean'],")
            continue
        if t == 'bool':
            dv = 'true' if f.get('default') else 'false'
            props.append(f"        public bool ${p} = {dv},")
            prep.append(f"        $properties['{n}'] = filter_var($properties['{n}'] ?? {dv}, FILTER_VALIDATE_BOOLEAN);")
        else:
            default = ''
            if not needs_value(f):
                default = ' = ' + php_default(f) if (f['required'] and 'default' in f) else ' = null'
            props.append(f'        public {m.php_type(f)} ${p}{default},')
            if t in NUMERIC or t == 'relation' or (t == 'select' and f.get('int')):
                cast = 'float' if t in ('decimal', 'money') else ('string' if t == 'relation' else 'int')
                prep.append(f"        $properties['{n}'] = ($properties['{n}'] ?? '') === '' ? {php_default(f)} : ({cast}) $properties['{n}'];")
            elif t == 'slug':
                has_slug = True
                src = f.get('from', m.title)
                sep = f.get('separator', '-')
                prep.append(f"        $properties['{n}'] = Str::slug(trim((string) ($properties['{n}'] ?? '')) ?: (string) ($properties['{src}'] ?? ''), '{sep}');")
            else:
                fn = {'code': 'strtoupper(trim(%s))', 'email': 'strtolower(trim(%s))', 'password': '%s'}.get(t, 'trim(%s)')
                expr = fn % f"(string) ($properties['{n}'] ?? '')"
                prep.append(f"        $properties['{n}'] = {expr} ?: {php_default(f)};")
        rules.append(f"            '{n}' => [{', '.join(rules_for(m, f))}],")
    msgs = '\n'.join(f"            '{k}' => __({php_str(v)})," for k, v in m.s.get('messages', {}).items())
    attrs = '\n'.join(f"            '{f['name']}' => __({php_str(f['label'].lower())})," for f in fields if f['type'] not in ('image', 'images'))
    uses = ['App\\Shared\\Support\\RouteModel', 'Illuminate\\Validation\\Rule', 'Spatie\\LaravelData\\Attributes\\MapName', 'Spatie\\LaravelData\\Data',
            'Spatie\\LaravelData\\Mappers\\SnakeCaseMapper', 'Spatie\\LaravelData\\Support\\Validation\\ValidationContext']
    if has_image:
        uses.append('Illuminate\\Http\\UploadedFile')
    if has_slug:
        uses.append('Illuminate\\Support\\Str')
    w(f'app/Domains/{m.D}/Data/{m.thing}Data.php', f'''<?php

namespace App\\Domains\\{m.D}\\Data;

{chr(10).join('use ' + u + ';' for u in sorted(uses))}

#[MapName(SnakeCaseMapper::class)]
final class {m.thing}Data extends Data
{{
    public function __construct(
{chr(10).join(props)}
    ) {{}}

    public static function prepareForPipeline(array $properties): array
    {{
{chr(10).join(prep)}

        return $properties;
    }}

    public static function rules(?ValidationContext $context = null): array
    {{
        $id = RouteModel::key('{m.var}');

        return [
{chr(10).join(rules)}
        ];
    }}

    public static function messages(mixed ...$args): array
    {{
        return [
{msgs}
        ];
    }}

    public static function attributes(mixed ...$args): array
    {{
        return [
{attrs}
        ];
    }}
}}
''')


def gen_enum(m):
    all_ops = m.ops + m.s.get('extra_ops', [])
    cases = '\n'.join(f"    case {m.perm_case(op)} = '{m.perm_value(op)}';" for op in all_ops)
    verbs = {'view': 'View', 'create': 'Create', 'edit': 'Edit', 'delete': 'Delete'}
    descs = '\n'.join(f"            self::{m.perm_case(op)} => '{verbs[op]} {m.labels.lower()}'," for op in m.ops)
    descs += ''.join(f"\n            self::{m.perm_case(op)} => {php_str(d)}," for op, d in m.s.get('extra_op_descriptions', {}).items())
    w(f'app/Domains/{m.D}/Enums/{m.enum}.php', f'''<?php

namespace App\\Domains\\{m.D}\\Enums;

use App\\Domains\\Access\\Contracts\\DefinesPermissions;

enum {m.enum}: string implements DefinesPermissions
{{
{cases}

    public function group(): string
    {{
        return '{m.s['permission_group']}';
    }}

    public function description(): string
    {{
        return match ($this) {{
{descs}
        }};
    }}
}}
''')
    reg = os.path.join(ROOT, 'app/Domains/Access/Support/PermissionRegistry.php')
    s = open(reg).read()
    line = f"        \\App\\Domains\\{m.D}\\Enums\\{m.enum}::class,"
    if line not in s:
        s = s.replace('    ];\n\n    /**\n     * @return list<DefinesPermissions&BackedEnum>', line + '\n    ];\n\n    /**\n     * @return list<DefinesPermissions&BackedEnum>', 1)
        open(reg, 'w').write(s)


def mapping_lines(m, update=False):
    out = []
    ind = '                ' if update else '            '
    for f in m.data_fields():
        n, p = f['name'], camel(f['name'])
        if f['type'] in ('items', 'tags', 'multiselect') or f.get('virtual'):
            continue
        if f['type'] == 'json_list':
            casts = ', '.join(f"'{c['name']}' => isset($row['{c['name']}']) && $row['{c['name']}'] !== '' ? {col_cast(c)} $row['{c['name']}'] : null" for c in f['columns'])
            out.append(f"{ind}'{n}' => array_map(fn (array $row) => [{casts}], $data->{p}),")
            continue
        if f['type'] == 'images':
            if update:
                out.append(f"{ind}'{n}' => $this->uploads->mergeList((array) ${m.var}->{n}, $data->keep{studly(n)}, $data->{p}, '{m.P}'),")
            else:
                out.append(f"{ind}'{n}' => array_map(fn ($file) => $this->uploads->store($file, '{m.P}'), $data->{p}),")
            continue
        if f['type'] == 'image':
            if update:
                out.append(f"{ind}'{n}' => $this->uploads->replace(${m.var}->{n}, $data->{p}, '{m.P}', $data->remove{studly(n)}),")
            else:
                out.append(f"{ind}'{n}' => $data->{p} ? $this->uploads->store($data->{p}, '{m.P}') : null,")
            continue
        if f.get('auto') and not update:
            out.append(f"{ind}'{n}' => $data->{p} ?? {f['auto']},")
            continue
        if (f.get('keep_on_update') or f.get('auto')) and update:
            out.append(f"{ind}'{n}' => $data->{p} ?? ${m.var}->{n},")
            continue
        out.append(f"{ind}'{n}' => $data->{p},")
    return out


def sync_lines(m, var):
    out = []
    for f in m.data_fields():
        p = camel(f['name'])
        if f['type'] == 'items':
            cols = ', '.join(f"'{c['name']}' => isset($row['{c['name']}']) && $row['{c['name']}'] !== '' ? {col_cast(c)} $row['{c['name']}'] : null" for c in f['columns'])
            cols += ''.join(f", '{k}' => {v}" for k, v in f.get('computed', {}).items())
            out.append(f"""            {var}->{f['relation']}()->delete();
            foreach ($data->{p} as $row) {{
                {var}->{f['relation']}()->create([{cols}]);
            }}""")
        if f['type'] == 'tags':
            out.append(f"""            {var}->{f['relation']}()->sync(collect($data->{p})
                ->map(fn (string $name) => \\{f['model']}::query()->firstOrCreate(['slug' => \\Illuminate\\Support\\Str::slug($name)], ['name' => $name])->id)
                ->all());""")
    if m.s.get('after_save'):
        out.append(m.s['after_save'].replace('$m', var))
    return '\n'.join(out)


def gen_actions(m):
    has_image = any(f['type'] in ('image', 'images') for f in m.fields)
    ctor = '    public function __construct(private readonly PublicUploads $uploads) {}\n\n' if has_image else ''
    up_use = 'use App\\Shared\\Services\\PublicUploads;\n' if has_image else ''
    if 'create' in m.ops:
        w(f'app/Domains/{m.D}/Actions/Create{m.thing}Action.php', f'''<?php

namespace App\\Domains\\{m.D}\\Actions;

use App\\Domains\\{m.D}\\Data\\{m.thing}Data;
use App\\Domains\\{m.D}\\Models\\{m.M};
{up_use}use Illuminate\\Support\\Facades\\DB;

final class Create{m.thing}Action
{{
{ctor}    public function execute({m.thing}Data $data): {m.M}
    {{
        return DB::transaction(function () use ($data) {{
{m.s['create_override'] if m.s.get('create_override') else f'''            ${m.var} = {m.M}::query()->create([
    {(chr(10) + '    ').join(mapping_lines(m))}{m.s.get('create_extra', '').replace(chr(10), chr(10) + '    ')}
            ]);
{sync_lines(m, '$' + m.var)}
            return ${m.var};'''}
        }});
    }}
}}
''')
    w(f'app/Domains/{m.D}/Actions/Update{m.thing}Action.php', f'''<?php

namespace App\\Domains\\{m.D}\\Actions;

use App\\Domains\\{m.D}\\Data\\{m.thing}Data;
use App\\Domains\\{m.D}\\Models\\{m.M};
{up_use}{'use App\\Shared\\Exceptions\\DomainRuleViolation;' + chr(10) if m.s.get('update_guard') else ''}use Illuminate\\Support\\Facades\\DB;

final class Update{m.thing}Action
{{
{ctor}    public function execute({m.M} ${m.var}, {m.thing}Data $data): {m.M}
    {{
{m.s.get('update_guard', '')}        return DB::transaction(function () use (${m.var}, $data) {{
            ${m.var}->update([
{chr(10).join(mapping_lines(m, True))}
            ]);
{sync_lines(m, '$' + m.var)}
            return ${m.var};
        }});
    }}
}}
''')
    if 'delete' in m.ops:
        guard = m.s.get('delete_guard')
        guard_code = f'''        if ({guard['when']}) {{
            throw new DomainRuleViolation({guard['message']});
        }}

''' if guard else ''
        w(f'app/Domains/{m.D}/Actions/Delete{m.thing}Action.php', f'''<?php

namespace App\\Domains\\{m.D}\\Actions;

use App\\Domains\\{m.D}\\Models\\{m.M};
use App\\Shared\\Exceptions\\DomainRuleViolation;
use Illuminate\\Support\\Facades\\DB;

final class Delete{m.thing}Action
{{
    public function execute({m.M} ${m.var}): void
    {{
{guard_code}        DB::transaction(fn () => ${m.var}->delete());
    }}
}}
''')
    for tr in m.s.get('transitions', []):
        cls = studly(tr['name']) + m.thing + 'Action'
        has_value = bool(tr.get('prompt'))
        sig = f'{m.M} ${m.var}' + (', mixed $value' if has_value else '')
        w(f'app/Domains/{m.D}/Actions/{cls}.php', f'''<?php

namespace App\\Domains\\{m.D}\\Actions;

use App\\Domains\\{m.D}\\Models\\{m.M};
use App\\Shared\\Exceptions\\DomainRuleViolation;
use Illuminate\\Support\\Facades\\DB;
{tr.get('uses', '')}
/** {tr['label']}. */
final class {cls}
{{
    public function execute({sig}): {m.M}
    {{
{tr.get('guard', '')}        DB::transaction(function () use (${m.var}{', $value' if has_value else ''}) {{
{tr['php']}
        }});

        return ${m.var}->refresh();
    }}
}}
''')


def gen_queries(m):
    q = f'{m.M}::query()' + (f"\n            {m.s['query_scope']}" if m.s.get('query_scope') else '') + '\n            ->search($filters->search)'
    if m.s.get('eager'):
        q += f"\n            ->with([{', '.join(php_str(x) for x in m.s['eager'])}])"
    if m.s.get('with_count'):
        q += f"\n            ->withCount([{', '.join(php_str(x) for x in m.s['with_count'])}])"
    sf, sd = m.s.get('default_sort', ('created_at', 'desc'))
    w(f'app/Domains/{m.D}/Queries/{m.thing}Queries.php', f'''<?php

namespace App\\Domains\\{m.D}\\Queries;

use App\\Domains\\{m.D}\\Models\\{m.M};
use App\\Shared\\Data\\FilterData;
use App\\Shared\\Queries\\Concerns\\SortsQueries;
use App\\Shared\\Queries\\OptionsQuery;
use Illuminate\\Pagination\\LengthAwarePaginator;

final class {m.thing}Queries
{{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, {m.M}>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {{
        $query = {q};

        $this->applySort($query, $filters->sortField ?? '{sf}', $filters->sortDir ?? '{sd}');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }}

    /**
     * @return array<int, array{{label: string, value: string}}>
     */
    public function getAllAsOptions(): array
    {{
        return OptionsQuery::for({m.M}::class, '{m.s.get('option_label', m.title)}'{(", fn ($query) => $query" + m.s['query_scope']) if m.s.get('query_scope') else ''});
    }}
}}
''')
    if not has_stats(m):
        return
    stats = '\n'.join(
        f"            new StatData('{k}', __({php_str(lbl)}), {expr}, __({php_str(desc)}){', ' + disp if disp else ''}),"
        for k, lbl, expr, desc, disp, *_ in m.s['summary'])
    w(f'app/Domains/{m.D}/Queries/{m.thing}SummaryQuery.php', f'''<?php

namespace App\\Domains\\{m.D}\\Queries;

use App\\Domains\\{m.D}\\Models\\{m.M};
use App\\Shared\\Resources\\StatData;
{m.s.get('summary_uses', '')}
final class {m.thing}SummaryQuery
{{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {{
{m.s.get('summary_pre', '')}        return [
{stats}
        ];
    }}
}}
''')


def has_stats(m):
    """Stat cards are opt-in: only where the number drives a decision (low stock, unpaid, pending…).
    Give the spec a non-empty 'summary' list to get the SummaryQuery, summary.ts and <StatCards>."""
    return bool(m.s.get('summary'))


def php_options(f):
    return '[' + ', '.join(f"{php_str(o[0])} => {php_str(o[1])}" for o in f['options']) + ']'


def resource_value(m, f):
    n, t, v = f['name'], f['type'], f'${m.var}'
    if t in ('decimal', 'money'):
        return f"(float) {v}->{n}" if (f['required'] or 'default' in f) else f"{v}->{n} === null ? null : (float) {v}->{n}"
    if t == 'relation':
        return f"(string) {v}->{n}" if (f['required'] or not column_nullable(m, n)) else f"{v}->{n} === null ? null : (string) {v}->{n}"
    if t == 'int' or (t == 'select' and f.get('int')):
        return f"(int) {v}->{n}" if f['required'] else f"{v}->{n} === null ? null : (int) {v}->{n}"
    if t == 'bool':
        # raw: read the stored column, bypassing an accessor that computes the value.
        return f"(bool) ({v}->getAttributes()['{n}'] ?? false)" if f.get('raw') else f"(bool) {v}->{n}"
    if t == 'date':
        return f"self::date({v}->{n})"
    if t == 'datetime':
        return f"self::dateTime({v}->{n})"
    if t == 'time':
        return f"{v}->{n} ? substr((string) {v}->{n}, 0, 5) : null"
    if t == 'select':
        return f"(string) {v}->{n}" if not column_nullable(m, n) else f"{v}->{n} === null ? null : (string) {v}->{n}"
    return f"{v}->{n}"


def column_nullable(m, name):
    """From schema.json ('col:type?' marks nullable); unknown columns count as nullable."""
    for c in SCHEMA.get(m.T, []):
        col, _, typ = c.partition(':')
        if col == name:
            return typ.endswith('?')
    return True


def resource_fields(m):
    """(prop, php type, value expr, ts type)."""
    out = [('id', 'string', f'(string) ${m.var}->id', 'string')]
    for f in m.fields:
        t = f['type']
        if t == 'password':
            continue
        if t == 'json_list':
            expr = f['resource_expr'].replace('$m', f'${m.var}') if f.get('resource_expr') else f"array_values((array) (${m.var}->{f['name']} ?? []))"
            out.append((f['name'], 'array', expr, 'Array<{ ' + ' '.join(f"{c['name']}: {col_ts(c)};" for c in f['columns']) + ' }>'))
            continue
        if t == 'items':
            cols = ', '.join(f"'{c['name']}' => $row->{c['name']}" for c in f['columns'])
            extra_cols = ''.join(f", '{k}' => {v.replace('$r', '$row')}" for k, v, _ in f.get('row_extra', []))
            ts_cols = ' '.join(f"{c['name']}: {col_ts(c)};" for c in f['columns']) + ''.join(f" {k}: {t2};" for k, _, t2 in f.get('row_extra', []))
            out.append((f['name'], 'array', f"${m.var}->{f['relation']}->map(fn ($row) => ['id' => $row->id, {cols}{extra_cols}])->values()->all()", 'Array<{ id: string; ' + ts_cols + ' }>'))
            continue
        if t == 'multiselect':
            out.append((f['name'], 'array', f['resource_expr'].replace('$m', f'${m.var}'), 'string[]'))
            continue
        if t == 'tags':
            out.append((f['name'], 'array', f"${m.var}->{f['relation']}->pluck('name')->values()->all()", 'string[]'))
            continue
        if t == 'images':
            out.append((f['name'], 'array', f"array_values((array) (${m.var}->{f['name']} ?? []))", 'string[]'))
            out.append((f['name'] + '_urls', 'array', f"array_map(fn ($path) => self::url($path), array_values((array) (${m.var}->{f['name']} ?? [])))", 'string[]'))
            continue
        nullable = not (f['required'] or 'default' in f) or t in ('date', 'datetime', 'time', 'image')
        if t in ('decimal', 'money'):
            pt, tt = 'float', 'number'
        elif t == 'relation':
            pt, tt = 'string', 'string'
        elif t == 'int' or (t == 'select' and f.get('int')):
            pt, tt = 'int', 'number'
        elif t == 'bool':
            pt, tt, nullable = 'bool', 'boolean', False
        else:
            pt, tt = 'string', 'string'
        out.append((f['name'], ('?' if nullable else '') + pt, resource_value(m, f), tt + (' | null' if nullable else '')))
        if t == 'image':
            out.append((f['name'] + '_url', '?string', f"self::url(${m.var}->{f['name']})", 'string | null'))
        if t == 'relation':
            rel = f['relation']
            out.append((rel['display'], '?string', rel['expr'].replace('$m', f'${m.var}'), 'string | null'))
        if t == 'select':
            out.append((f['name'] + '_label', '?string', f"self::option({php_options(f)}, ${m.var}->{f['name']})", 'string | null'))
    for name, pt, expr, tt in m.s.get('resource_extra', []):
        out.append((name, pt, expr.replace('$m', f'${m.var}'), tt))
    out.append(('created_at', '?string', f'${m.var}->created_at?->toIso8601String()', 'string | null'))
    out.append(('updated_at', '?string', f'${m.var}->updated_at?->toIso8601String()', 'string | null'))
    return out


def gen_resource(m):
    rf = resource_fields(m)
    props = '\n'.join(f'        public {pt} ${camel(n)},' for n, pt, _, _ in rf)
    args = '\n'.join(f'            {camel(n)}: {expr},' for n, _, expr, _ in rf)
    w(f'app/Domains/{m.D}/Resources/{m.thing}Resource.php', f'''<?php

namespace App\\Domains\\{m.D}\\Resources;

use App\\Domains\\{m.D}\\Models\\{m.M};
use Spatie\\LaravelData\\Attributes\\MapName;
use Spatie\\LaravelData\\Mappers\\SnakeCaseMapper;
use Spatie\\LaravelData\\Resource;

#[MapName(SnakeCaseMapper::class)]
final class {m.thing}Resource extends Resource
{{
    public function __construct(
{props}
    ) {{}}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel({m.M} ${m.var}, array $context = []): self
    {{
        return new self(
{args}
        );
    }}

    private static function date(mixed $value): ?string
    {{
        if (! $value) {{
            return null;
        }}

        return $value instanceof \\DateTimeInterface ? $value->format('Y-m-d') : substr((string) $value, 0, 10);
    }}

    private static function dateTime(mixed $value): ?string
    {{
        if (! $value) {{
            return null;
        }}

        return ($value instanceof \\DateTimeInterface ? \\Illuminate\\Support\\Carbon::instance($value) : \\Illuminate\\Support\\Carbon::parse((string) $value))->toIso8601String();
    }}

    private static function url(?string $path): ?string
    {{
        if (! $path) {{
            return null;
        }}

        return str_starts_with($path, 'http') ? $path : asset('storage/'.ltrim($path, '/'));
    }}

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {{
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }}
}}
''')


def option_props(m):
    """Option lists passed to form pages: (prop, php expr)."""
    out, seen = [], set()
    for f in m.fields:
        if f['type'] == 'relation':
            rel = f['relation']
            prop = rel.get('options_prop', snake(f['name'].replace('_id', '')) + '_options')
            if prop not in seen:
                out.append((prop, rel['options']))
                seen.add(prop)
        if f['type'] == 'multiselect' and f['options_prop'] not in seen:
            out.append((f['options_prop'], f['options']))
            seen.add(f['options_prop'])
        if f['type'] == 'items':
            for c in f['columns']:
                if c.get('type') == 'relation' and c['options_prop'] not in seen:
                    out.append((c['options_prop'], c['options']))
                    seen.add(c['options_prop'])
    return out


def filter_option_props(m):
    return [(fl['options_prop'], fl['options']) for fl in m.s.get('filters', []) if fl['type'] == 'relation']


def gen_controller(m):
    E = m.enum
    mw = [f"            new Middleware('can:'.{E}::{m.perm_case('view')}->value, only: ['index']),"]
    if 'create' in m.ops:
        mw.append(f"            new Middleware('can:'.{E}::{m.perm_case('create')}->value, only: ['create', 'store']),")
    edit_methods = ['edit', 'update'] + (['toggle'] if m.toggle else []) + \
        [camel(t['name']) for t in m.s.get('transitions', []) if t.get('permission', 'edit') == 'edit']
    mw.append(f"            new Middleware('can:'.{E}::{m.perm_case('edit')}->value, only: [{', '.join(php_str(x) for x in edit_methods)}]),")
    for t in m.s.get('transitions', []):
        if t.get('permission', 'edit') != 'edit':
            mw.append(f"            new Middleware('can:'.{E}::{m.perm_case(t['permission'])}->value, only: ['{camel(t['name'])}']),")
    if 'delete' in m.ops:
        mw.append(f"            new Middleware('can:'.{E}::{m.perm_case('delete')}->value, only: ['destroy']),")
    opts = option_props(m)
    opt_lines = ''.join(f"\n            '{p}' => fn () => {e}," for p, e in opts)
    idx_opts = ''.join(f"\n            '{p}' => fn () => {e}," for p, e in filter_option_props(m))
    title_expr = f"${m.var}->{m.title}"
    sf, sd = m.s.get('default_sort', ('created_at', 'desc'))
    uses = {
        f'App\\Domains\\{m.D}\\Actions\\Update{m.thing}Action', f'App\\Domains\\{m.D}\\Data\\{m.thing}Data',
        f'App\\Domains\\{m.D}\\Enums\\{E}', f'App\\Domains\\{m.D}\\Models\\{m.M}', f'App\\Domains\\{m.D}\\Queries\\{m.thing}Queries',
        f'App\\Domains\\{m.D}\\Resources\\{m.thing}Resource',
        'App\\Domains\\Preference\\Actions\\ResolveTableStateAction', 'App\\Http\\Controllers\\Controller', 'App\\Shared\\Attributes\\FormRules',
        'Illuminate\\Http\\RedirectResponse', 'Illuminate\\Http\\Request', 'Illuminate\\Routing\\Controllers\\HasMiddleware',
        'Illuminate\\Routing\\Controllers\\Middleware', 'Inertia\\Inertia', 'Inertia\\Response', 'Spatie\\LaravelData\\PaginatedDataCollection',
    }
    if 'create' in m.ops:
        uses.add(f'App\\Domains\\{m.D}\\Actions\\Create{m.thing}Action')
    if 'delete' in m.ops:
        uses.add(f'App\\Domains\\{m.D}\\Actions\\Delete{m.thing}Action')
    uses.update(m.s.get('controller_uses', []))
    summary_param = f', {m.thing}SummaryQuery $summary' if has_stats(m) else ''
    summary_prop = "\n            'summary' => Inertia::defer(fn (): array => $summary->execute())," if has_stats(m) else ''
    if has_stats(m):
        uses.add(f'App\\Domains\\{m.D}\\Queries\\{m.thing}SummaryQuery')
    methods = [f'''    public function index(Request $request, ResolveTableStateAction $resolveTableState, {m.thing}Queries $queries{summary_param}): Response
    {{
        $table = $resolveTableState->execute($request, $request->user(), '{snake(m.P)}', ['sort_field' => '{sf}', 'sort_dir' => '{sd}']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn ({m.M} ${m.var}) => {m.thing}Resource::fromModel(${m.var}));

        return Inertia::render('{m.page}/index', [
            'records' => {m.thing}Resource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),{summary_prop}{idx_opts}
        ]);
    }}''']
    if 'create' in m.ops:
        methods.append(f'''    #[FormRules({m.thing}Data::class)]
    public function create(): Response
    {{
        return Inertia::render('{m.page}/create', [{opt_lines}
        ]);
    }}

    public function store({m.thing}Data $data, Create{m.thing}Action $create): RedirectResponse
    {{
        ${m.var} = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => {title_expr}])]);

        return to_route('{m.route_base}.index');
    }}''')
    methods.append(f'''    #[FormRules({m.thing}Data::class)]
    public function edit({m.M} ${m.var}, {m.thing}Queries $queries): Response
    {{
        return Inertia::render('{m.page}/edit', [
            'record' => {m.thing}Resource::fromModel(${m.var}{m.s.get('edit_load', '')}),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),{opt_lines}
        ]);
    }}

    public function update({m.M} ${m.var}, {m.thing}Data $data, Update{m.thing}Action $update): RedirectResponse
    {{
        $update->execute(${m.var}, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => {title_expr}])]);

        return to_route('{m.route_base}.index');
    }}''')
    if m.toggle:
        methods.append(f'''    /** Inline switch on the index table. */
    public function toggle(Request $request, {m.M} ${m.var}): RedirectResponse
    {{
        ${m.var}->update(['{m.toggle}' => $request->boolean('{m.toggle}')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => ${m.var}->{m.toggle}
            ? __(':name is now active.', ['name' => {title_expr}])
            : __(':name is now inactive.', ['name' => {title_expr}])]);

        return back();
    }}''')
    for t in m.s.get('transitions', []):
        cls = studly(t['name']) + m.thing + 'Action'
        uses.add(f'App\\Domains\\{m.D}\\Actions\\{cls}')
        if t.get('prompt'):
            pr = t['prompt']
            methods.append(f'''    /** {t['label']}. */
    public function {camel(t['name'])}(Request $request, {m.M} ${m.var}, {cls} $action): RedirectResponse
    {{
        $value = $request->validate(['{pr['field']}' => {pr['rules']}])['{pr['field']}'];
        $action->execute(${m.var}, $value);

        Inertia::flash('toast', ['type' => 'success', 'message' => {t['message']}]);

        return back();
    }}''')
        else:
            methods.append(f'''    /** {t['label']}. */
    public function {camel(t['name'])}({m.M} ${m.var}, {cls} $action): RedirectResponse
    {{
        $action->execute(${m.var});

        Inertia::flash('toast', ['type' => 'success', 'message' => {t['message']}]);

        return back();
    }}''')
    if 'delete' in m.ops:
        methods.append(f'''    public function destroy({m.M} ${m.var}, Delete{m.thing}Action $delete): RedirectResponse
    {{
        $delete->execute(${m.var});

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => {title_expr}])]);

        return back();
    }}''')
    w(f'app/Domains/{m.D}/Controllers/{m.thing}Controller.php', f'''<?php

namespace App\\Domains\\{m.D}\\Controllers;

{chr(10).join('use ' + u + ';' for u in sorted(uses))}

class {m.thing}Controller extends Controller implements HasMiddleware
{{
    public static function middleware(): array
    {{
        return [
{chr(10).join(mw)}
        ];
    }}

{(chr(10) + chr(10)).join(methods)}
}}
''')


def gen_routes(m):
    path = os.path.join(ROOT, f'routes/admin/{kebab(m.D)}.php')
    s = open(path).read() if os.path.exists(path) else '<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n'
    ctrl = f'App\\Domains\\{m.D}\\Controllers\\{m.thing}Controller'
    if f'use {ctrl};' not in s:
        s = s.replace('use Illuminate\\Support\\Facades\\Route;', f'use {ctrl};\nuse Illuminate\\Support\\Facades\\Route;', 1)
    if f"prefix('{m.P}')" not in s:
        C = m.thing + 'Controller'
        r = [f"    Route::get('/', [{C}::class, 'index'])->name('index');"]
        if 'create' in m.ops:
            r += [f"    Route::get('create', [{C}::class, 'create'])->name('create');", f"    Route::post('/', [{C}::class, 'store'])->name('store');"]
        r += [f"    Route::get('{{{m.var}}}/edit', [{C}::class, 'edit'])->name('edit');", f"    Route::put('{{{m.var}}}', [{C}::class, 'update'])->name('update');"]
        if m.toggle:
            r.append(f"    Route::patch('{{{m.var}}}/toggle', [{C}::class, 'toggle'])->name('toggle');")
        for t in m.s.get('transitions', []):
            r.append(f"    Route::patch('{{{m.var}}}/{kebab(t['name'])}', [{C}::class, '{camel(t['name'])}'])->name('{kebab(t['name'])}');")
        if 'delete' in m.ops:
            r.append(f"    Route::delete('{{{m.var}}}', [{C}::class, 'destroy'])->name('destroy');")
        s = s.rstrip('\n') + f"\n\nRoute::prefix('{m.P}')->name('{m.P}.')->group(function () {{\n" + '\n'.join(r) + '\n});\n'
    open(path, 'w').write(s)


def gen_menu(m):
    g = m.s['menu_group']
    w(f'database/migrations/2026_11_14_13{m.s["seq"]:02d}00_add_{snake(m.P)}_menu_item.php', f'''<?php

use App\\Domains\\Menu\\Support\\MenuSeeder;
use Illuminate\\Database\\Migrations\\Migration;

return new class extends Migration
{{
    private const REFERENCE = '{g[0]}.{snake(m.P)}';

    public function up(): void
    {{
        MenuSeeder::add('{g[0]}', ['title' => '{g[1]}', 'icon' => '{g[2]}', 'sort_order' => {g[3]}], '{g[0]}');
        MenuSeeder::add('{g[0]}.{snake(m.P)}', [
            'title' => '{m.labels}',
            'icon' => '{m.s['menu_icon']}',
            'sort_order' => {m.s.get('menu_sort', 1)},
            'parent' => '{g[0]}',
            'permission' => '{m.perm_value('view')}',
            'route_name' => '{m.route_base}.index',
        ], self::REFERENCE);
    }}

    public function down(): void
    {{
        MenuSeeder::remove(self::REFERENCE);
    }}
}};
''')


# ─── frontend ────────────────────────────────────────────────────────────────
def gen_ts_permissions(m):
    p = os.path.join(ROOT, 'resources/js/lib/permissions.ts')
    s = open(p).read()
    if f'export const {m.enum} =' in s:
        return
    body = '\n'.join(f"    {m.perm_case(op)}: '{m.perm_value(op)}'," for op in m.ops + m.s.get('extra_ops', []))
    block = f"/** Mirrors `App\\Domains\\{m.D}\\Enums\\{m.enum}`. */\nexport const {m.enum} = {{\n{body}\n}} as const;\n\n"
    s = s.replace('type ValuesOf<T>', block + 'type ValuesOf<T>', 1)
    s = re.sub(r'(export type PermissionName =[^;]+);', lambda mo: mo.group(1) + f' | ValuesOf<typeof {m.enum}>', s, count=1)
    open(p, 'w').write(s)


def gen_ts_types(m):
    path = os.path.join(ROOT, f'resources/js/types/domains/{kebab(m.D)}.ts')
    s = open(path).read() if os.path.exists(path) else ''
    body = '\n'.join(f'    {n}: {tt};' for n, _, _, tt in resource_fields(m))
    block = f'/** Mirrors App\\Domains\\{m.D}\\Resources\\{m.thing}Resource (snake_case). */\nexport interface {m.thing}Record {{\n{body}\n}}\n'
    marker = f'/** Mirrors App\\Domains\\{m.D}\\Resources\\{m.thing}Resource'
    if marker in s:
        start = s.index(marker)
        end = s.index('\n}\n', start) + 3  # closing brace of the interface sits at column 0
        s = s[:start] + block + s[end:]
    else:
        s = s.rstrip('\n') + ('\n\n' if s else '') + block
    open(path, 'w').write(s)
    idx = os.path.join(ROOT, 'resources/js/types/index.ts')
    t = open(idx).read()
    line = f"export type * from './domains/{kebab(m.D)}';"
    if line not in t:
        t = t.replace("export type * from './domains/catalog';", f"export type * from './domains/catalog';\n{line}", 1)
        open(idx, 'w').write(t)


def ts_options(f):
    # Always strings: form data and query-string filters are strings; the Data class casts.
    return '[' + ', '.join(f"{{ value: {ts_str(o[0])}, label: {ts_str(o[1])} }}" for o in f['options']) + ']'


def gen_schemas(m):
    d = f'resources/js/schemas/{m.page}'
    flt = [f"    {{ name: 'keyword', label: 'Search', type: 'text', placeholder: {ts_str(m.s.get('search_placeholder', 'Search…'))} }},"]
    for fl in m.s.get('filters', []):
        if fl['type'] == 'boolean':
            flt.append(f"    {{ name: '{fl['name']}', label: {ts_str(fl['label'])}, type: 'boolean' }},")
        elif fl['type'] in ('select', 'scope'):
            f = next((x for x in m.fields if x['name'] == fl.get('column', fl['name'])), None)
            opts = ts_options(f) if (f and f.get('options') and 'options' not in fl) else ts_options({'options': fl['options']})
            flt.append(f"    {{ name: '{fl['name']}', label: {ts_str(fl['label'])}, type: 'select', options: {opts} }},")
        elif fl['type'] == 'relation':
            flt.append(f"    {{ name: '{fl['name']}', label: {ts_str(fl['label'])}, type: 'select', optionsProp: '{fl['options_prop']}' }},")
        elif fl['type'] in ('date_from', 'date_to'):
            flt.append(f"    {{ name: '{fl['name']}', label: {ts_str(fl['label'])}, type: 'date' }},")
    w(f'{d}/filter.ts', f'''import type {{ FilterField, SearchFieldsOf }} from '@/types';

export const {m.var}Filter = [
{chr(10).join(flt)}
] as const satisfies readonly FilterField[];

export type {m.thing}SearchField = SearchFieldsOf<typeof {m.var}Filter>;
''')

    imports, cells, route_imports = set(), [], set()
    for col in m.s['grid']:
        key, header, kind = col[0], col[1], col[2]
        opts = col[3] if len(col) > 3 else {}
        extra = f", priorityLevel: {opts['priority']}" if opts.get('priority') else ''
        if opts.get('sort_field'):
            extra += f", sortField: '{opts['sort_field']}'"
        sortable = opts.get('sortable', kind not in ('relation', 'text_muted'))
        if kind == 'title':
            imports.add("import { Link } from '@inertiajs/react';")
            route_imports.add('edit')
            sub = opts.get('subtitle', m.subtitle)
            subline = f"\n                {{row.{sub} && <span className=\"truncate text-xs text-muted-foreground\">{{row.{sub}}}</span>}}" if sub else ''
            cell = f'''(row) => (
            <Link href={{edit(row.id).url}} className="grid min-w-0 hover:underline">
                <span className="truncate font-medium">{{row.{key}}}</span>{subline}
            </Link>
        )'''
            cells.append(f"        {{ key: '{key}', header: {ts_str(header)}, sortable: true, hideable: false, cell: {cell}{extra} }},")
            continue
        if kind == 'money':
            imports.add("import { Money } from '@/renderers/cells/money';")
            cell, extra = f"(row) => <Money value={{row.{key}}} />", extra + ", align: 'right'"
        elif kind in ('number', 'qty'):
            imports.add("import { formatNumber } from '@/lib/format';")
            unit = opts.get('unit')
            cell = f"(row) => `${{formatNumber(row.{key})}}{(' ${row.' + unit + ' ?? \"\"}') if unit else ''}`"
            if opts.get('low_when'):
                imports.add("import { cn } from '@/lib/utils';")
                cell = f"(row) => <span className={{cn({opts['low_when']} && 'font-semibold text-destructive')}}>{{formatNumber(row.{key})}}{(' {row.' + unit + '}') if unit else ''}</span>"
            extra += ", align: 'right'"
        elif kind in ('date', 'datetime'):
            imports.add("import { DateFormatted } from '@/renderers/cells/date-formatted';")
            cell = f"(row) => <DateFormatted value={{row.{key}}}{' withTime' if kind == 'datetime' else ''} />"
        elif kind == 'status':
            imports.add("import { StatusBadge, toneFor } from '@/renderers/cells/badges';")
            lbl = opts.get('label_field', key + '_label')
            cell = f"(row) => <StatusBadge label={{row.{lbl} ?? row.{key}}} tone={{toneFor(String(row.{key}))}} />"
        elif kind == 'badge':
            imports.add("import { StatusBadge } from '@/renderers/cells/badges';")
            lbl = opts.get('label_field', key + '_label')
            cell = f"(row) => <StatusBadge label={{row.{lbl} ?? (row.{key} === null ? null : String(row.{key}))}} tone=\"info\" />"
        elif kind == 'bool':
            imports.add("import { BooleanBadge } from '@/renderers/cells/badges';")
            cell = f"(row) => <BooleanBadge value={{row.{key}}} yes={ts_str(opts.get('yes', 'Yes'))} no={ts_str(opts.get('no', 'No'))} />"
            extra += ", align: 'center'"
        elif kind == 'toggle':
            imports.add("import { StatusSwitch } from '@/renderers/cells/status-switch';")
            imports.add(f"import {{ {m.enum} }} from '@/lib/permissions';")
            route_imports.add('toggle')
            cell = f"(row) => <StatusSwitch checked={{row.{key}}} field=\"{key}\" url={{toggle(row.id).url}} permission={{{m.enum}.{m.perm_case('edit')}}} label={{`Toggle ${{row.{m.title}}}`}} />"
            extra += ", align: 'center'"
        elif kind == 'count':
            imports.add("import { CountChip } from '@/renderers/cells/badges';")
            cell, extra = f"(row) => <CountChip value={{row.{key}}} />", extra + ", align: 'right'"
        elif kind == 'stars':
            imports.add("import { Star } from 'lucide-react';")
            cell = f"(row) => (\n            <span className=\"inline-flex items-center gap-1 tabular-nums\">\n                <Star className=\"size-3.5 fill-amber-400 text-amber-400\" />\n                {{row.{key}}}\n            </span>\n        )"
        elif kind == 'text_muted':
            cell = f"(row) => <span className=\"line-clamp-1 max-w-xs text-muted-foreground\">{{row.{key} ?? '—'}}</span>"
        else:
            cell = f"(row) => row.{key} ?? '—'"
        cells.append(f"        {{ key: '{key}', header: {ts_str(header)}{', sortable: true' if sortable else ''}, cell: {cell}{extra} }},")
    if route_imports:
        imports.add(f"import {{ {', '.join(sorted(route_imports))} }} from '@/routes/admin/{m.Pc}';")
    merged = {}
    for line in imports:
        mo = re.match(r"import \{ (.+) \} from '(.+)';", line)
        if mo:
            merged.setdefault(mo.group(2), set()).update(x.strip() for x in mo.group(1).split(','))
        else:
            merged.setdefault(line, None)
    imports = {(f"import {{ {', '.join(sorted(v))} }} from '{k}';" if v is not None else k) for k, v in merged.items()}
    w(f'{d}/grid.tsx', f'''{chr(10).join(sorted(imports))}
import type {{ {m.thing}Record, ColumnDef }} from '@/types';

export function create{m.thing}GridSchema(): ColumnDef<{m.thing}Record>[] {{
    return [
{chr(10).join(cells)}
    ];
}}
''')

    ai = {'Pencil'}
    rows = [f"        {{ label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: {m.enum}.{m.perm_case('edit')} }},"]
    route_fns = {'edit'}
    for t in m.s.get('transitions', []):
        ai.add(t['icon'])
        fn = camel(kebab(t['name']))
        route_fns.add(fn)
        parts = [f"label: {ts_str(t['label'])}", f"icon: {t['icon']}", f"href: (row) => {fn}(row.id).url", "method: 'patch'",
                 f"permission: {m.enum}.{m.perm_case(t.get('permission', 'edit'))}"]
        if t.get('hidden'):
            parts.append(f"hidden: (row) => {t['hidden']}")
        if t.get('confirm'):
            parts += ['isWithConfirmation: true', f"confirmTitle: {ts_str(t['confirm'])}"]
            if t.get('confirm_description'):
                parts.append(f"confirmDescription: {ts_str(t['confirm_description'])}")
        if t.get('prompt'):
            pr = t['prompt']
            pp = [f"field: '{pr['field']}'", f"label: {ts_str(pr['label'])}", f"type: '{pr.get('type', 'number')}'"]
            if 'min' in pr:
                pp.append(f"min: {pr['min']}")
            if pr.get('step'):
                pp.append(f"step: '{pr['step']}'")
            if pr.get('description'):
                pp.append(f"description: {pr['description']}")
            if pr.get('options'):
                pp.append(f"options: {pr['options']}")
            parts.append('prompt: { ' + ', '.join(pp) + ' }')
        rows.append('        { ' + ', '.join(parts) + ' },')
    if 'delete' in m.ops:
        ai.add('Trash2')
        route_fns.add('destroy')
        hidden = m.s.get('delete_hidden')
        rows.append(f'''        {{
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: {m.enum}.{m.perm_case('delete')},{f"{chr(10)}            hidden: (row) => {hidden}," if hidden else ''}
            confirmTitle: 'Delete this {m.label.lower()}?',
            confirmDescription: (row) => `"${{row.{m.title}}}" will be removed. This can't be undone from the back office.`,
        }},''')
    page_actions = ''
    if 'create' in m.ops:
        ai.add('Plus')
        route_fns.add('create')
        page_actions = f"    {{ label: 'New {m.label.lower()}', icon: Plus, href: create().url, permission: {m.enum}.{m.perm_case('create')} }},\n"
    w(f'{d}/actions.ts', f'''import {{ {', '.join(sorted(ai))} }} from 'lucide-react';

import {{ {m.enum} }} from '@/lib/permissions';
import {{ {', '.join(sorted(route_fns))} }} from '@/routes/admin/{m.Pc}';
import type {{ {m.thing}Record, PageAction, RowAction }} from '@/types';

export const {m.var}ActionSchema: PageAction[] = [
{page_actions}];

export function create{m.thing}ActionSchema(): RowAction<{m.thing}Record>[] {{
    return [
{chr(10).join(rows)}
    ];
}}
''')
    if not has_stats(m):
        return
    icons = sorted({s[5] for s in m.s['summary']})
    entries = '\n'.join(f"    {s[0]}: {{ icon: {s[5]}, tone: '{s[6]}' }}," for s in m.s['summary'])
    w(f'{d}/summary.ts', f'''import {{ {', '.join(icons)} }} from 'lucide-react';

import type {{ StatConfig }} from '@/types';

export const {m.var}Summary: StatConfig = {{
{entries}
}};
''')


def form_field_ts(m, f):
    n, t = f['name'], f['type']
    span = ' className="sm:col-span-2 2xl:col-span-3"' if f.get('span') == 'full' or t in ('text', 'richtext') else ''
    desc = f"\n                        <FieldDescription>{f['description']}</FieldDescription>" if f.get('description') else ''
    err = f"\n                        <InputError message={{errors.{n}}} />"
    label = f'<FieldLabel name="{n}">{f["label"]}</FieldLabel>'
    inv = f'aria-invalid={{!!errors.{n}}}'
    if t in ('string', 'email', 'phone', 'code', 'slug', 'password'):
        typ = {'email': 'email', 'phone': 'tel', 'password': 'password'}.get(t, 'text')
        ph = f' placeholder={ts_str(f["placeholder"])}' if f.get('placeholder') else ''
        inp = f'<Input id="{n}" type="{typ}" value={{data.{n}}} {inv} onChange={{(event) => setData(\'{n}\', event.target.value)}}{ph} />'
    elif t in ('text', 'richtext'):
        inp = f'<Textarea id="{n}" rows={{{10 if t == "richtext" else 3}}} value={{data.{n}}} {inv} onChange={{(event) => setData(\'{n}\', event.target.value)}} />'
    elif t in NUMERIC:
        step = {'int': '1', 'decimal': f.get('step', '0.001'), 'money': '0.01'}[t]
        mn = f' min={{{f["min"]}}}' if 'min' in f else ''
        inp = f'<Input id="{n}" type="number" inputMode="decimal" step="{step}"{mn} value={{data.{n}}} {inv} onChange={{(event) => setData(\'{n}\', event.target.value)}} />'
    elif t == 'bool':
        return f'''                    <Field{span}>
                        {label}
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="{n}" checked={{data.{n}}} onCheckedChange={{(checked) => setData('{n}', checked)}} />
                            <span className="text-sm text-muted-foreground">{{data.{n} ? {ts_str(f.get('yes', 'Yes'))} : {ts_str(f.get('no', 'No'))}}}</span>
                        </div>{desc}{err}
                    </Field>'''
    elif t == 'date':
        inp = f'<DatePicker id="{n}" value={{data.{n} || null}} invalid={{!!errors.{n}}} onChange={{(value) => setData(\'{n}\', value ?? \'\')}} />'
    elif t in ('datetime', 'time'):
        inp = f'<Input id="{n}" type="{"datetime-local" if t == "datetime" else "time"}" value={{data.{n}}} {inv} onChange={{(event) => setData(\'{n}\', event.target.value)}} />'
    elif t == 'select':
        clear = '' if f['required'] else ' clearable'
        inp = f'<SelectDropdown id="{n}"{clear} value={{data.{n} || null}} options={{{camel(n)}Options}} invalid={{!!errors.{n}}} onChange={{(value) => setData(\'{n}\', value === null ? \'\' : String(value))}} />'
    elif t == 'relation':
        prop = f['relation'].get('options_prop', snake(n.replace('_id', '')) + '_options')
        clear = '' if f['required'] else ' clearable'
        inp = f'<SelectDropdown<string> id="{n}"{clear} value={{data.{n} ? String(data.{n}) : null}} options={{options.{prop} ?? []}} invalid={{!!errors.{n}}} onChange={{(value) => setData(\'{n}\', value === null ? \'\' : String(value))}} />'
    elif t in ('json_list', 'items'):
        cols = []
        for c in f['columns']:
            ct = c.get('type', 'string')
            parts = [f"name: '{c['name']}'", f"label: {ts_str(c['label'])}"]
            if ct in ('int', 'decimal', 'money'):
                parts.append("type: 'number'")
                parts.append(f"step: '{ {'int': '1', 'decimal': c.get('step', '0.001'), 'money': '0.01'}[ct] }'")
                if 'min' in c:
                    parts.append(f"min: {c['min']}")
            if ct == 'relation':
                parts.append("type: 'select'")
                parts.append(f"options: options.{c['options_prop']} ?? []")
            if ct == 'select':
                parts.append("type: 'select'")
                parts.append(f"options: {ts_options(c)}")
            if c.get('placeholder'):
                parts.append(f"placeholder: {ts_str(c['placeholder'])}")
            if c.get('width'):
                parts.append(f"width: '{c['width']}'")
            cols.append('{ ' + ', '.join(parts) + ' }')
        summary = f"\n                            summary={{{f['summary']}}}" if f.get('summary') else ''
        return f'''                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="{n}">{f['label']}</FieldLabel>{desc}
                        <RepeaterField
                            name="{n}"
                            rows={{data.{n}}}
                            columns={{[{', '.join(cols)}]}}
                            onChange={{(rows) => setData('{n}', rows)}}
                            errors={{errors as Record<string, string | undefined>}}
                            addLabel={ts_str(f.get('add_label', 'Add row'))}{summary}
                        />
                    </Field>'''
    elif t == 'multiselect':
        inp = f'<MultiSelect id="{n}" value={{data.{n}}} options={{options.{f["options_prop"]} ?? []}} invalid={{!!errors.{n}}} onChange={{(value) => setData(\'{n}\', value)}} />'
    elif t == 'tags':
        inp = f'<Input id="{n}" value={{data.{n}}} {inv} onChange={{(event) => setData(\'{n}\', event.target.value)}} placeholder="e.g. bestseller, gift, chocolate" />'
        if not f.get('description'):
            desc = "\n                        <FieldDescription>Separate tags with commas.</FieldDescription>"
    elif t == 'images':
        return f'''                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="{n}">{f['label']}</FieldLabel>
                        {{(currentUrls?.{n}_list ?? []).length > 0 && (
                            <div className="flex flex-wrap gap-3">
                                {{(currentUrls?.{n}_list ?? []).map((item) => {{
                                    const kept = data.keep_{n}.includes(item.path);
                                    return (
                                        <button
                                            key={{item.path}}
                                            type="button"
                                            onClick={{() => setData('keep_{n}', kept ? data.keep_{n}.filter((p) => p !== item.path) : [...data.keep_{n}, item.path])}}
                                            className="relative rounded-lg border focus-visible:ring-2 focus-visible:ring-ring"
                                            aria-pressed={{!kept}}
                                            aria-label={{kept ? 'Remove image' : 'Keep image'}}
                                        >
                                            <img src={{item.url}} alt="" className={{`size-20 rounded-lg object-cover ${{kept ? '' : 'opacity-30 grayscale'}}`}} />
                                            {{!kept && <span className="absolute inset-0 flex items-center justify-center text-xs font-medium">Removed</span>}}
                                        </button>
                                    );
                                }})}}
                            </div>
                        )}}
                        <Input id="{n}" type="file" multiple accept="image/jpeg,image/png,image/webp" onChange={{(event) => setData('{n}', Array.from(event.target.files ?? []))}} />
                        <FieldDescription>Click a current image to remove it. New files are added to the end.</FieldDescription>
                        <InputError message={{errors.{n}}} />
                    </Field>'''
    elif t == 'image':
        return f'''                    <Field{span}>
                        <FieldLabel name="{n}">{f['label']}</FieldLabel>
                        {{currentUrls?.{n} && !data.remove_{n} && !data.{n} && (
                            <div className="flex items-center gap-3">
                                <img src={{currentUrls.{n}}} alt="" className="size-16 rounded-lg border object-cover" />
                                <Button type="button" variant="outline" size="sm" onClick={{() => setData('remove_{n}', true)}}>
                                    Remove
                                </Button>
                            </div>
                        )}}
                        <Input
                            id="{n}"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            {inv}
                            onChange={{(event) => {{
                                setData('{n}', event.target.files?.[0] ?? null);
                                setData('remove_{n}', false);
                            }}}}
                        />{desc}{err}
                    </Field>'''
    return f'''                    <Field{span}>
                        {label}
                        {inp}{desc}{err}
                    </Field>'''


def form_type_ts(f):
    return {'bool': 'boolean', 'image': 'File | null', 'json_list': 'Record<string, string>[]', 'items': 'Record<string, string>[]',
            'images': 'File[]', 'multiselect': 'string[]'}.get(f['type'], 'string')


def form_initial(f, from_record=False):
    n, t = f['name'], f['type']
    if t in ('json_list', 'items'):
        if not from_record:
            return '[]'
        keys = ', '.join(f"{c['name']}: row.{c['name']} === null || row.{c['name']} === undefined ? '' : String(row.{c['name']})" for c in f['columns'])
        return f'record.{n}.map((row) => ({{ {keys} }}))'
    if t == 'multiselect':
        return f'record.{n}' if from_record else '[]'
    if t == 'tags':
        return f"record.{n}.join(', ')" if from_record else "''"
    if t == 'images':
        return '[]'
    if t == 'bool':
        return f'record.{n}' if from_record else ('true' if f.get('default') else 'false')
    if t == 'image':
        return 'null'
    if from_record:
        if t == 'datetime':
            return f"record.{n} ? record.{n}.slice(0, 16) : ''"
        if t == 'password':
            return "''"
        return f"record.{n} === null || record.{n} === undefined ? '' : String(record.{n})"
    d = f.get('default')
    return ts_str(d) if d is not None else "''"


def gen_pages(m):
    d = f'resources/js/pages/{m.page}'
    fields = m.data_fields()
    sections = []
    for f in fields:
        if f['section'] not in sections:
            sections.append(f['section'])
    sec_icons = m.s.get('section_icons', {})
    sec_desc = m.s.get('section_descriptions', {})
    blocks = []
    for sec in sections:
        fs = [f for f in fields if f['section'] == sec]
        blocks.append(f'''            <FormSection icon={{{sec_icons.get(sec, 'FileText')}}} title={ts_str(sec)} description={ts_str(sec_desc.get(sec, ''))} fields={{[{', '.join(f"'{f['name']}'" for f in fs)}]}} errors={{errors}}>
                <div className={{fieldGrid}}>
{chr(10).join(form_field_ts(m, f) for f in fs)}
                </div>
            </FormSection>''')
    form_type = '\n'.join(f'    {f["name"]}: {form_type_ts(f)};' for f in fields) + ''.join(f'\n    remove_{f["name"]}: boolean;' for f in fields if f['type'] == 'image') + ''.join(f'\n    keep_{f["name"]}: string[];' for f in fields if f['type'] == 'images')
    select_consts = '\n'.join(f"const {camel(f['name'])}Options = {ts_options(f)};" for f in fields if f['type'] == 'select')
    opt_props = option_props(m)
    string_props = {f['options_prop'] for f in fields if f['type'] == 'multiselect'}
    # Relation options carry UUID keys, multiselects carry names: both are strings.
    opt_type = ('{ ' + ' '.join(f"{p}?: Option<string>[];" for p, _ in opt_props) + ' }') if opt_props else 'Record<string, never>'
    uses = {'Field', 'FieldLabel', 'InputError', 'fieldGrid'}
    ftypes = {f['type'] for f in fields}
    if any(f.get('description') for f in fields) or ftypes & {'tags', 'images'}:
        uses.add('FieldDescription')
    imps = [f"import {{ {', '.join(sorted(set(sec_icons.get(s, 'FileText') for s in sections)))} }} from 'lucide-react';", '']
    if 'date' in ftypes:
        imps.append("import { DatePicker } from '@/components/custom/date-picker';")
    imps.append(f"import {{ {', '.join(sorted(uses))} }} from '@/components/custom/field';")
    imps.append("import { FormSection } from '@/components/custom/form-section';")
    if ftypes & {'json_list', 'items'}:
        imps.append("import { RepeaterField } from '@/components/custom/repeater-field';")
    if 'multiselect' in ftypes:
        imps.append("import { MultiSelect } from '@/components/custom/multi-select';")
    if ftypes & {'select', 'relation'}:
        imps.append("import { SelectDropdown } from '@/components/custom/select-dropdown';")
    if 'image' in ftypes:
        imps.append("import { Button } from '@/components/ui/button';")
    imps.append("import { Card } from '@/components/ui/card';")
    if ftypes & {'string', 'email', 'phone', 'code', 'slug', 'password', 'int', 'decimal', 'money', 'datetime', 'time', 'image', 'images', 'tags'}:
        imps.append("import { Input } from '@/components/ui/input';")
    if 'bool' in ftypes:
        imps.append("import { Switch } from '@/components/ui/switch';")
    if ftypes & {'text', 'richtext'}:
        imps.append("import { Textarea } from '@/components/ui/textarea';")
    if opt_props:
        imps.append("import type { Option } from '@/types';")
    image_names = [f['name'] for f in fields if f['type'] == 'image']
    gallery_names = [f['name'] for f in fields if f['type'] == 'images']
    cur_types = [f'{x}?: string | null;' for x in image_names] + [f'{x}_list?: {{ path: string; url: string }}[];' for x in gallery_names]
    cur_prop = ('\n    currentUrls?: { ' + ' '.join(cur_types) + ' };') if cur_types else ''
    w(f'{d}/{m.page}-form.tsx', f'''{chr(10).join(imps)}

export interface {m.thing}FormData {{
{form_type}
}}

export type {m.thing}FormOptions = {opt_type};

interface Props {{
    data: {m.thing}FormData;
    setData: <K extends keyof {m.thing}FormData>(key: K, value: {m.thing}FormData[K]) => void;
    errors: Partial<Record<keyof {m.thing}FormData, string>>;
    options: {m.thing}FormOptions;{cur_prop}
}}
{(chr(10) + select_consts + chr(10)) if select_consts else ''}
/** Presentational: create.tsx / edit.tsx own the state. */
export function {m.thing}Form({{ data, setData, errors, options{', currentUrls' if cur_types else ''} }}: Props) {{
    void options;
    return (
        <Card className="gap-0 py-0">
{chr(10).join(blocks)}
        </Card>
    );
}}
''')

    icon = m.s['icon']
    sub_line = f"\n                    {{data.{m.subtitle} && <p className=\"text-sm text-muted-foreground\">{{String(data.{m.subtitle})}}</p>}}" \
        if m.subtitle and any(f['name'] == m.subtitle for f in fields) else ''
    badge_imp = badge = ''
    if m.status_field and any(f['name'] == m.status_field for f in fields):
        badge_imp = "import { StatusBadge, toneFor } from '@/renderers/cells/badges';\n"
        badge = f"\n                            <StatusBadge label={{data.{m.status_field}}} tone={{toneFor(data.{m.status_field})}} />"
    elif m.toggle:
        badge_imp = "import { BooleanBadge } from '@/renderers/cells/badges';\n"
        badge = f"\n                            <BooleanBadge value={{data.{m.toggle}}} />"
    fact_lines = ''.join(f'''
                    <div className="flex justify-between gap-2 text-sm">
                        <span className="text-muted-foreground">{lbl}</span>
                        <span className="font-medium tabular-nums">{{{expr}}}</span>
                    </div>''' for lbl, expr in m.s.get('preview_facts', []))
    title_in_form = any(f['name'] == m.title for f in fields)
    title_expr = f"title ?? (data.{m.title} || 'New {m.label.lower()}')" if title_in_form else f"title ?? 'New {m.label.lower()}'"
    w(f'{d}/{m.page}-preview.tsx', f'''import {{ {icon} }} from 'lucide-react';

import {{ Card, CardContent }} from '@/components/ui/card';
{m.s.get('preview_imports', '')}{badge_imp}
import type {{ {m.thing}FormData }} from './{m.page}-form';

/** Sticky aside: live summary of the record being built. */
export function {m.thing}Preview({{ data, title }}: {{ data: {m.thing}FormData; title?: string }}) {{
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <{icon} className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{{{title_expr}}}</p>{badge}
                        </div>
                    </div>{sub_line}{fact_lines}
                </CardContent>
            </Card>
        </aside>
    );
}}
''')

    group_title = ts_str(m.s['menu_group'][1])
    crumbs = f"[{{ title: 'Home', href: dashboard().url }}, {{ title: {group_title} }}, {{ title: {ts_str(m.labels)}, href: index().url }}"
    stats = has_stats(m)
    stat_import = "import {{ StatCards }} from '@/components/custom/stat-cards';\n" if stats else ''
    summary_import = f"import {{ {m.var}Summary }} from '@/schemas/{m.page}/summary';\n" if stats else ''
    stat_type = ' StatData,' if stats else ''
    summary_prop = '\n    summary?: StatData[];' if stats else ''
    summary_arg = ', summary' if stats else ''
    stat_cards = f"\n                <StatCards stats={{summary}} config={{{m.var}Summary}} count={{{len(m.s.get('summary') or [])}}} />\n" if stats else ''
    w(f'{d}/index.tsx', f'''import {{ Head }} from '@inertiajs/react';

import {{ FilterableDataTable }} from '@/components/custom/filterable-data-table';
import {{ PageActions }} from '@/components/custom/page-actions';
import {{ PageHeader }} from '@/components/custom/page-header';
{stat_import}import {{ useDataTablePage }} from '@/hooks/use-data-table-page';
import {{ ActionDropdown }} from '@/renderers/cells/action-dropdown';
import {{ dashboard }} from '@/routes/admin';
import {{ index }} from '@/routes/admin/{m.Pc}';
import {{ create{m.thing}ActionSchema, {m.var}ActionSchema }} from '@/schemas/{m.page}/actions';
import {{ {m.var}Filter, type {m.thing}SearchField }} from '@/schemas/{m.page}/filter';
import {{ create{m.thing}GridSchema }} from '@/schemas/{m.page}/grid';
{summary_import}import type {{ {m.thing}Record, Filters, Paginated,{stat_type} TableState }} from '@/types';

interface Props {{
    records: Paginated<{m.thing}Record>;
    filters: Filters<{m.thing}SearchField>;
    table: TableState;{summary_prop}
}}

const title = {ts_str(m.labels)};

export default function {m.thing}Index({{ records, filters, table{summary_arg} }}: Props) {{
    const page = useDataTablePage<{m.thing}Record, {m.thing}SearchField>({{ url: index().url, records, filters, table }});
    const rowActions = create{m.thing}ActionSchema();

    return (
        <>
            <Head title={{title}} />
            <div className="grid gap-4">
                <PageHeader title={{title}} actions={{<PageActions actions={{{m.var}ActionSchema}} />}}>
                    {m.s['description']}
                </PageHeader>
{stat_cards}
                <FilterableDataTable<{m.thing}Record, {m.thing}SearchField>
                    {{...page.savedState}}
                    rowData={{page.rowData}}
                    meta={{page.meta}}
                    loading={{page.loading}}
                    filterFields={{{m.var}Filter}}
                    filterValues={{page.filterValues}}
                    setFilterValues={{page.setFilterValues}}
                    applyFilters={{() => page.applyFilters()}}
                    resetFilters={{page.resetFilters}}
                    goToPage={{page.goToPage}}
                    setPerPage={{page.setPerPage}}
                    sort={{page.currentSort}}
                    onSort={{page.handleSort}}
                    columns={{create{m.thing}GridSchema()}}
                    rowKey={{(row) => row.id}}
                    actions={{(row) => <ActionDropdown row={{row}} actions={{rowActions}} />}}
                    emptyMessage="No {m.labels.lower()} match these filters."
                />
            </div>
        </>
    );
}}

{m.thing}Index.layout = {{ breadcrumbs: [{{ title: 'Home', href: dashboard().url }}, {{ title: {group_title} }}, {{ title }}] }};
''')

    init_create = '\n'.join(f'    {f["name"]}: {form_initial(f)},' for f in fields) + ''.join(f'\n    remove_{f["name"]}: false,' for f in fields if f['type'] == 'image') + ''.join(f'\n    keep_{f["name"]}: [],' for f in fields if f['type'] == 'images')
    init_edit = '\n'.join(f'        {f["name"]}: {form_initial(f, True)},' for f in fields) + ''.join(f'\n        remove_{f["name"]}: false,' for f in fields if f['type'] == 'image') + ''.join(f'\n        keep_{f["name"]}: record.{f["name"]},' for f in fields if f['type'] == 'images')
    has_files = bool(image_names or gallery_names)
    opt_names = ', '.join(p for p, _ in opt_props)
    opt_obj = '{ ' + opt_names + ' }' if opt_props else '{}'
    cur_parts = [f'{x}: record.{x}_url' for x in image_names] + [f'{x}_list: record.{x}.map((path, i) => ({{ path, url: record.{x}_urls[i] }}))' for x in gallery_names]
    cur_urls = ('{ ' + ', '.join(cur_parts) + ' }') if cur_parts else None
    form_type_imp = f', type {m.thing}FormOptions' if opt_props else ''
    if 'create' in m.ops:
        w(f'{d}/create.tsx', f'''import {{ Head, useForm }} from '@inertiajs/react';
import {{ {icon}, Loader2 }} from 'lucide-react';
import type {{ FormEvent }} from 'react';

import {{ AppFooter }} from '@/components/custom/app-footer';
import {{ CreateHeader }} from '@/components/custom/create-header';
import {{ UnsavedChangesGuard }} from '@/components/custom/unsaved-changes-guard';
import {{ Button }} from '@/components/ui/button';
import {{ tabGridClass }} from '@/lib/layout';
import {{ dashboard }} from '@/routes/admin';
import {{ index, store }} from '@/routes/admin/{m.Pc}';

import {{ {m.thing}Form, type {m.thing}FormData{form_type_imp} }} from './{m.page}-form';
import {{ {m.thing}Preview }} from './{m.page}-preview';{f"{chr(10)}import {{ {m.s['create_panel'][0]} }} from './{m.s['create_panel'][1]}';" if m.s.get('create_panel') else ''}

const initial: {m.thing}FormData = {{
{init_create}
}};

export default function {m.thing}Create({f'{{ {opt_names} }}: {m.thing}FormOptions' if opt_props else ''}) {{
    const form = useForm<{m.thing}FormData>(initial);

    const submit = (event?: FormEvent) => {{
        event?.preventDefault();
        form.post(store().url, {{ {'forceFormData: true, ' if has_files else ''}onSuccess: () => form.setDefaults() }});
    }};

    return (
        <UnsavedChangesGuard dirty={{form.isDirty && !form.processing && !form.wasSuccessful}}>
            <Head title="New {m.label.lower()}" />
            <form onSubmit={{submit}} className="grid gap-4">
                <CreateHeader
                    backHref={{index().url}}
                    backLabel="{m.label} list"
                    icon={{{icon}}}
                    title="New {m.label.lower()}"
                    description={ts_str(m.s.get('create_description', ''))}
                    onClear={{() => form.reset()}}
                    clearDisabled={{!form.isDirty}}
                />
                <div className={{tabGridClass}}>
                    <{m.thing}Preview data={{form.data}} />
                    <div className="grid content-start gap-4">{f"{chr(10)}                        <{m.s['create_panel'][0]} setData={{form.setData}} />" if m.s.get('create_panel') else ''}
                        <{m.thing}Form data={{form.data}} setData={{form.setData}} errors={{form.errors}} options={{{opt_obj}}} />
                    </div>
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={{() => window.history.back()}}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={{form.processing}} onClick={{() => submit()}}>
                        {{form.processing && <Loader2 className="animate-spin" />}} Save {m.label.lower()}
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}}

{m.thing}Create.layout = {{ breadcrumbs: {crumbs}, {{ title: 'New {m.label.lower()}' }}] }};
''')

    submit_edit = f'''        form.transform((data) => ({{ ...data, _method: 'put' }}));
        form.post(update(record.id).url, {{ forceFormData: true, onSuccess: () => form.setDefaults() }});''' if has_files else \
        "        form.put(update(record.id).url, { onSuccess: () => form.setDefaults() });"
    del_menu = ''
    if 'delete' in m.ops:
        hidden = m.s.get('delete_hidden')
        cond = f" && !({hidden.replace('row.', 'record.')})" if hidden else ''
        del_menu = f'''
            {{can({m.enum}.{m.perm_case('delete')}){cond} && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={{() => setConfirmDelete(true)}}>
                            <Trash2 /> Delete {m.label.lower()}
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}}'''
    has_delete = 'delete' in m.ops
    delete_imports = '''import { ConfirmDialog } from '@/components/custom/confirm-dialog';
''' if has_delete else ''
    dd_import = "import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';\n" if has_delete else ''
    lucide = f"{icon}, Loader2" + (', MoreVertical, Trash2' if has_delete else '')
    react_imp = "import { useState, type FormEvent } from 'react';" if has_delete else "import type { FormEvent } from 'react';"
    perm_imports = "import { usePermissions } from '@/hooks/use-permissions';\n" if has_delete else ''
    perm_enum_imp = f"import {{ {m.enum} }} from '@/lib/permissions';\n" if has_delete else ''
    state_lines = '''    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
''' if has_delete else ''
    remove_fn = '''
    const remove = () =>
        router.delete(destroy(record.id).url, {
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
            onSuccess: () => router.visit(index().url),
        });
''' if has_delete else ''
    confirm_dialog = f'''
            <ConfirmDialog
                open={{confirmDelete}}
                onOpenChange={{setConfirmDelete}}
                title={{`Delete ${{record.{m.title}}}?`}}
                description="This {m.label.lower()} will be removed."
                confirmLabel="Delete {m.label.lower()}"
                processing={{deleting}}
                onConfirm={{remove}}
            />''' if has_delete else ''
    w(f'{d}/edit.tsx', f'''import {{ Deferred, Head, router, useForm }} from '@inertiajs/react';
import {{ {lucide} }} from 'lucide-react';
{react_imp}

import {{ AppFooter }} from '@/components/custom/app-footer';
{delete_imports}import {{ CreateHeader }} from '@/components/custom/create-header';
import {{ SelectDropdown }} from '@/components/custom/select-dropdown';
import {{ UnsavedChangesGuard }} from '@/components/custom/unsaved-changes-guard';
import {{ Button }} from '@/components/ui/button';
{dd_import}import {{ Skeleton }} from '@/components/ui/skeleton';
{perm_imports}import {{ tabGridClass }} from '@/lib/layout';
{perm_enum_imp}import {{ dashboard }} from '@/routes/admin';
import {{ {'destroy, ' if has_delete else ''}edit, index, update }} from '@/routes/admin/{m.Pc}';
import type {{ {m.thing}Record, Option }} from '@/types';

import {{ {m.thing}Form, type {m.thing}FormData{form_type_imp} }} from './{m.page}-form';
import {{ {m.thing}Preview }} from './{m.page}-preview';{f"{chr(10)}import {{ {m.s['edit_panel'][0]} }} from './{m.s['edit_panel'][1]}';" if m.s.get('edit_panel') else ''}

type Props = {{ record: {m.thing}Record; switcher?: Option<string>[] }}{' & ' + m.thing + 'FormOptions' if opt_props else ''};

export default function {m.thing}Edit({{ record, switcher{', ' + opt_names if opt_props else ''} }}: Props) {{
{state_lines}    const form = useForm<{m.thing}FormData>({{
{init_edit}
    }});

    const submit = (event?: FormEvent) => {{
        event?.preventDefault();
{submit_edit}
    }};
{remove_fn}
    const headerActions = (
        <div className="flex items-center gap-2">
            <Deferred data="switcher" fallback={{<Skeleton className="h-9 w-56" />}}>
                <SelectDropdown<string>
                    className="w-56"
                    value={{record.id}}
                    options={{switcher ?? []}}
                    searchPlaceholder="Jump to {m.label.lower()}…"
                    onChange={{(id) => id && id !== record.id && router.visit(edit(id).url)}}
                />
            </Deferred>{del_menu}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={{form.isDirty && !form.processing && !form.wasSuccessful}}>
            <Head title={{`Edit ${{record.{m.title}}}`}} />
            <form onSubmit={{submit}} className="grid gap-4">
                <CreateHeader
                    backHref={{index().url}}
                    backLabel="{m.label} list"
                    icon={{{icon}}}
                    title={{String(record.{m.title})}}
                    description={{{m.s.get('edit_description', "''")}}}
                    actions={{headerActions}}
                />
                <div className={{tabGridClass}}>
                    <{m.thing}Preview data={{form.data}} title={{String(record.{m.title})}} />
                    <div className="grid content-start gap-4">
                        <{m.thing}Form data={{form.data}} setData={{form.setData}} errors={{form.errors}} options={{{opt_obj}}}{f' currentUrls={{{cur_urls}}}' if cur_urls else ''} />{f"{chr(10)}                        <{m.s['edit_panel'][0]} record={{record}} />" if m.s.get('edit_panel') else ''}
                    </div>
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={{() => router.visit(index().url)}}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={{form.processing || !form.isDirty}} onClick={{() => submit()}}>
                        {{form.processing && <Loader2 className="animate-spin" />}} Save changes
                    </Button>
                </AppFooter>
            </form>{confirm_dialog}
        </UnsavedChangesGuard>
    );
}}

{m.thing}Edit.layout = {{ breadcrumbs: {crumbs}, {{ title: 'Edit' }}] }};
''')


# ─── tests ───────────────────────────────────────────────────────────────────
def gen_test(m):
    t = m.s['test']
    base = f'/admin/{m.P}'
    required = [f['name'] for f in m.fields if f['required'] and 'default' not in f and f['type'] not in ('bool', 'image')]
    index_test_suffix = 'and_deferred_summary' if has_stats(m) else 'without_stat_cards'
    index_test_deferred = (
        f"\n                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', {len(m.s['summary'])}))" if has_stats(m) else ''
    )
    create_tests = ''
    if 'create' in m.ops:
        create_tests = f'''
    public function test_store_persists_and_flashes_a_toast(): void
    {{
{t.get('setup', '')}
        $this->actingAs($this->admin())
            ->post('{base}', {t['payload']})
            ->assertSessionHasNoErrors()
            ->assertRedirect('{base}')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('{m.T}', {t['assert_db']});
    }}

'''
        if required:
            create_tests += f'''
    public function test_store_validates_required_fields(): void
    {{
        $this->actingAs($this->admin())
            ->post('{base}', [])
            ->assertSessionHasErrors([{', '.join(php_str(n) for n in required)}]);
    }}
'''
        uf = next((f for f in m.fields if f.get('unique')), None)
        if uf:
            create_tests += f'''
    public function test_unique_{uf['name']}_ignores_soft_deleted_rows(): void
    {{
{t.get('setup', '')}
        $existing = $this->record();
        $payload = array_merge({t['payload']}, ['{uf['name']}' => $existing->{uf['name']}]);

        $this->actingAs($this->admin())->post('{base}', $payload)->assertSessionHasErrors(['{uf['name']}']);

        $existing->delete();
        $this->post('{base}', $payload)->assertSessionHasNoErrors();
    }}
'''
    view_only_create = f"\n        $this->get('{base}/create')->assertForbidden();" if 'create' in m.ops else ''
    view_only_delete = f"\n        $this->delete(\"{base}/{{$record->id}}\")->assertForbidden();" if 'delete' in m.ops else ''
    delete_test = ''
    if 'delete' in m.ops:
        delete_test = f'''
    public function test_delete_soft_deletes(): void
    {{
        $record = $this->record({t.get('deletable', '[]')});

        $this->actingAs($this->admin())->delete("{base}/{{$record->id}}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->{'assertModelMissing' if m.s.get('no_soft_deletes') else 'assertSoftDeleted'}($record);
    }}
'''
        if m.s.get('delete_guard') and t.get('guarded'):
            delete_test += f'''
    public function test_delete_is_refused_while_in_use(): void
    {{
        $record = $this->record();
{t['guarded']}

        $this->actingAs($this->admin())->from('{base}')->delete("{base}/{{$record->id}}")->assertRedirect('{base}')->assertInertiaFlash('toast.type', 'error');

        $this->assertNotSoftDeleted($record);
    }}
'''
    transition_tests = ''
    for tr in m.s.get('transitions', []):
        if tr.get('test'):
            transition_tests += f'''
    public function test_{snake(tr['name'])}(): void
    {{
        $record = $this->record({tr['test'].get('record', '[]')});
{tr['test'].get('setup', '')}
        $this->actingAs($this->admin())->from('{base}')->patch("{base}/{{$record->id}}/{kebab(tr['name'])}", {tr['test'].get('data', '[]')})->assertSessionHasNoErrors()->assertRedirect('{base}');

{tr['test']['check']}
    }}
'''
    guest_post = f"\n        $this->post('{base}', [])->assertRedirect('/login');" if 'create' in m.ops else f"\n        $this->put('{base}/1', [])->assertRedirect('/login');"
    w(f'tests/Feature/{m.D}/{m.thing}/{m.thing}Test.php', f'''<?php

namespace Tests\\Feature\\{m.D}\\{m.thing};

use App\\Domains\\Access\\Enums\\AccessPermission;
use App\\Domains\\{m.D}\\Enums\\{m.enum};
use App\\Domains\\{m.D}\\Models\\{m.M};
{'' if m.M == 'User' else 'use App' + chr(92) + 'Domains' + chr(92) + 'User' + chr(92) + 'Models' + chr(92) + 'User;' + chr(10)}use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Inertia\\Testing\\AssertableInertia as Assert;
use Tests\\TestCase;
{t.get('uses', '')}
/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class {m.thing}Test extends TestCase
{{
    use RefreshDatabase;

    private function admin({m.enum} ...$permissions): User
    {{
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: {m.enum}::cases()));
    }}

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): {m.M}
    {{
        static $n = 0;
        $n++;
{t.get('record_setup', '')}
        $model = {m.M}::query()->create(array_merge({t['record']}, $attributes));
{t.get('record_after', '')}
        return $model;
    }}

    public function test_guests_are_redirected_to_login(): void
    {{
        $this->get('{base}')->assertRedirect('/login');{guest_post}
    }}

    public function test_user_without_permission_gets_403(): void
    {{
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('{base}')->assertForbidden();
    }}

    public function test_view_only_user_cannot_change_anything(): void
    {{
        $record = $this->record();
        $this->actingAs($this->admin({m.enum}::{m.perm_case('view')}));

        $this->get('{base}')->assertOk();{view_only_create}
        $this->get("{base}/{{$record->id}}/edit")->assertForbidden();
        $this->put("{base}/{{$record->id}}", [])->assertForbidden();{view_only_delete}
    }}

    public function test_index_renders_records_{index_test_suffix}(): void
    {{
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('{base}')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('{m.page}/index')
                ->has('records.data', 2)
                ->where('table.name', '{snake(m.P)}')
                ->missing('summary'){index_test_deferred});
    }}
{create_tests}
    public function test_edit_renders_and_update_persists(): void
    {{
        $record = $this->record();
{t.get('setup', '')}
        $this->actingAs($this->admin())
            ->get("{base}/{{$record->id}}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('{m.page}/edit')->where('record.id', $record->id));

        $this->put("{base}/{{$record->id}}", {t['update_payload']})
            ->assertSessionHasNoErrors()
            ->assertRedirect('{base}')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('{m.T}', array_merge(['id' => $record->id], {t['update_assert']}));
    }}
{delete_test}{transition_tests}
    public function test_index_search_finds_by_keyword(): void
    {{
        $this->record({t['search_record']});
        $this->record();

        $this->actingAs($this->admin())
            ->get('{base}?search[keyword]={t['search_keyword']}')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }}
}}
''')


def run(key):
    m = Mod(key, SPECS[key])
    for step in (gen_migration_soft_deletes, gen_model_patch, gen_filter_data, gen_data, gen_enum, gen_actions, gen_queries,
                 gen_resource, gen_controller, gen_routes, gen_menu, gen_ts_permissions, gen_ts_types, gen_schemas, gen_pages, gen_test):
        step(m)
    print('generated', key)


if __name__ == '__main__':
    for k in sys.argv[1:]:
        run(k)
