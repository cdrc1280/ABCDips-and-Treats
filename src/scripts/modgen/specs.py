"""
Module specs for modgen.py.

Field types: string email phone code slug password text richtext int decimal money bool date datetime time
             select (options=[(value, label)], int=True for integer values) relation (relation={...}) image
Grid kinds:  title text text_muted money number qty date datetime status badge bool toggle count stars
Menu groups: (key, title, lucide icon, sort)
"""

INVENTORY = ('inventory', 'Inventory', 'boxes', 20)
SALES = ('sales', 'Orders & Sales', 'shopping-cart', 30)
PRODUCTION = ('production', 'Production & Purchasing', 'factory', 40)
HR = ('hr', 'HR & Payroll', 'users', 50)
CONTENT = ('content', 'Engagement', 'megaphone', 60)

SPECS = {}

SPECS['supplier'] = dict(
    seq=1, domain='Inventory', model='Supplier', table='suppliers', permission_group='Inventory',
    label='Supplier', labels='Suppliers', icon='Truck', menu_group=INVENTORY, menu_icon='truck', menu_sort=1,
    description='Companies you buy ingredients and packaging from.',
    create_description='Add a vendor for ingredients or packaging.',
    title_field='name', subtitle_field='contact_person',
    search=['name', 'contact_person', 'email', 'phone'], search_placeholder='Name, contact, email or phone',
    default_sort=('name', 'asc'),
    with_count=['ingredients', 'purchaseOrders'],
    fields=[
        dict(name='name', type='string', label='Company name', required=True),
        dict(name='contact_person', type='string', label='Contact person', required=True),
        dict(name='email', type='email', label='Email', required=True),
        dict(name='phone', type='phone', label='Phone', required=True, max=30),
        dict(name='payment_terms', type='string', label='Payment terms', required=True, default='Net 30', max=100,
             description='e.g. Net 30, COD, 50% down.'),
        dict(name='address', type='text', label='Address', required=True, section='Address & notes', max=1000),
        dict(name='notes', type='text', label='Notes', section='Address & notes'),
    ],
    section_icons={'Details': 'Building2', 'Address & notes': 'MapPin'},
    section_descriptions={'Details': 'Who you deal with and how you pay them.', 'Address & notes': 'Where deliveries come from.'},
    resource_extra=[
        ('ingredients_count', 'int', '(int) ($m->ingredients_count ?? $m->ingredients()->count())', 'number'),
        ('purchase_orders_count', 'int', '(int) ($m->purchase_orders_count ?? $m->purchaseOrders()->count())', 'number'),
        ('in_use', 'bool', '($m->ingredients_count ?? $m->ingredients()->count()) + ($m->purchase_orders_count ?? $m->purchaseOrders()->count()) > 0', 'boolean'),
    ],
    edit_load="->loadCount(['ingredients', 'purchaseOrders'])",
    grid=[
        ('name', 'Supplier', 'title'),
        ('email', 'Email', 'text', {'priority': 2}),
        ('phone', 'Phone', 'text', {'priority': 2}),
        ('payment_terms', 'Terms', 'text', {'priority': 3}),
        ('ingredients_count', 'Ingredients', 'count'),
        ('purchase_orders_count', 'POs', 'count', {'priority': 3}),
        ('created_at', 'Added', 'date', {'priority': 3}),
    ],
    summary=[
        ('total', 'Suppliers', 'Supplier::query()->count()', 'Vendors on file', None, 'Truck', 'primary'),
        ('with_ingredients', 'Supplying ingredients', "Supplier::query()->has('ingredients')->count()", 'Linked to at least one ingredient', None, 'Wheat', 'success'),
        ('open_pos', 'Open purchase orders', "\\App\\Domains\\Purchasing\\Models\\PurchaseOrder::query()->whereNotIn('status', ['received', 'cancelled'])->count()", 'Not yet received', None, 'ClipboardList', 'warning'),
    ],
    delete_guard=dict(
        when='$supplier->ingredients()->exists() || $supplier->purchaseOrders()->exists()',
        message="__(':name is still linked to ingredients or purchase orders.', ['name' => $supplier->name])",
    ),
    delete_hidden='row.in_use',
    edit_description="`${record.ingredients_count} ingredients · ${record.purchase_orders_count} purchase orders`",
    test=dict(
        record="['name' => \"Supplier {$n}\", 'contact_person' => 'Ana', 'email' => \"s{$n}@example.test\", 'phone' => '09171234567', 'address' => 'Cavite', 'payment_terms' => 'Net 30']",
        payload="['name' => 'Golden Mill', 'contact_person' => 'Ben Reyes', 'email' => 'SALES@goldenmill.test', 'phone' => '09170000000', 'payment_terms' => 'COD', 'address' => 'Imus, Cavite']",
        assert_db="['name' => 'Golden Mill', 'email' => 'sales@goldenmill.test', 'payment_terms' => 'COD']",
        update_payload="['name' => 'Golden Mill Corp', 'contact_person' => 'Ben', 'email' => 'b@mill.test', 'phone' => '0917', 'payment_terms' => 'Net 15', 'address' => 'Imus']",
        update_assert="['name' => 'Golden Mill Corp', 'payment_terms' => 'Net 15']",
        search_record="['name' => 'Flourish Trading']", search_keyword='flourish',
        guarded="        \\App\\Domains\\Inventory\\Models\\Ingredient::query()->create(['sku' => 'ING-1', 'name' => 'Flour', 'unit' => 'kg', 'supplier_id' => $record->id]);",
    ),
)

UNITS = [('grams', 'Grams (g)'), ('kg', 'Kilograms (kg)'), ('ml', 'Milliliters (ml)'), ('L', 'Liters (L)'), ('piece', 'Piece (pcs)'), ('box', 'Box / pack')]
SUPPLIER_REL = dict(table='suppliers', soft=True, display='supplier_name', expr='$m->supplier?->name',
                    options="\\App\\Shared\\Queries\\OptionsQuery::for(\\App\\Domains\\Inventory\\Models\\Supplier::class)", options_prop='supplier_options')
USER_OPTIONS = "\\App\\Shared\\Queries\\OptionsQuery::for(\\App\\Domains\\User\\Models\\User::class, 'name', fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', '!=', 'customer')), 'email')"

SPECS['ingredient'] = dict(
    seq=2, domain='Inventory', model='Ingredient', table='ingredients', permission_group='Inventory',
    label='Ingredient', labels='Ingredients', icon='Wheat', menu_group=INVENTORY, menu_icon='wheat', menu_sort=2,
    description='Raw materials, stock levels and what each unit costs.',
    create_description='Track a raw material, its pack price and reorder point.',
    title_field='name', subtitle_field='sku',
    search=['name', 'sku', 'supplier.name'], search_placeholder='Name, SKU or supplier',
    default_sort=('name', 'asc'), eager=['supplier'], with_count=['recipeIngredients'],
    filters=[
        dict(name='supplier_id', label='Supplier', type='relation', options_prop='supplier_options', options=SUPPLIER_REL['options']),
        dict(name='stock', label='Stock', type='scope', options=[('low', 'Low or out of stock'), ('ok', 'In stock')],
             php="$filters->stock === 'low' ? $q->whereColumn('stock_qty', '<=', 'min_stock_qty') : $q->whereColumn('stock_qty', '>', 'min_stock_qty')"),
    ],
    fields=[
        dict(name='name', type='string', label='Name', required=True, placeholder='e.g. Bread flour'),
        dict(name='sku', type='code', label='SKU', required=True, unique=True, max=50),
        dict(name='supplier_id', type='relation', label='Supplier', relation=SUPPLIER_REL),
        dict(name='unit', type='select', label='Stock unit', required=True, default='grams', options=UNITS, section='Pricing'),
        dict(name='item_unit', type='decimal', label='Pack size', required=True, default=1000, min=0.001, section='Pricing',
             description='How many stock units one purchased pack contains (e.g. 25000 g per sack).'),
        dict(name='item_price', type='money', label='Pack price', required=True, default=0, min=0, section='Pricing',
             description='Cost per unit is pack price ÷ pack size.'),
        dict(name='stock_qty', type='decimal', label='In stock', required=True, default=0, min=0, section='Stock'),
        dict(name='min_stock_qty', type='decimal', label='Minimum level', required=True, default=100, min=0, section='Stock',
             description='At or below this the ingredient shows as low stock.'),
        dict(name='reorder_qty', type='decimal', label='Reorder quantity', required=True, default=1000, min=0, section='Stock'),
    ],
    section_icons={'Details': 'Wheat', 'Pricing': 'Calculator', 'Stock': 'Package'},
    section_descriptions={'Details': 'What it is and who supplies it.', 'Pricing': 'Pack size and price drive recipe costing.', 'Stock': 'Current quantity and reorder point.'},
    resource_extra=[
        ('cost_per_unit', 'float', '(float) $m->cost_per_unit', 'number'),
        ('is_low_stock', 'bool', '(float) $m->stock_qty <= (float) $m->min_stock_qty', 'boolean'),
        ('used_in_recipes', 'int', '(int) ($m->recipe_ingredients_count ?? $m->recipeIngredients()->count())', 'number'),
    ],
    edit_load="->load('supplier')->loadCount('recipeIngredients')",
    grid=[
        ('name', 'Ingredient', 'title'),
        ('stock_qty', 'In stock', 'qty', {'unit': 'unit', 'low_when': 'row.is_low_stock'}),
        ('min_stock_qty', 'Minimum', 'qty', {'unit': 'unit', 'priority': 3}),
        ('cost_per_unit', 'Cost / unit', 'money', {'priority': 2}),
        ('supplier_name', 'Supplier', 'relation', {'priority': 2, 'sortable': True, 'sort_field': 'supplier.name'}),
        ('used_in_recipes', 'Recipes', 'count', {'priority': 3, 'sortable': False}),
    ],
    summary=[
        ('total', 'Ingredients', 'Ingredient::query()->count()', 'Tracked raw materials', None, 'Wheat', 'primary'),
        ('low', 'Low stock', "Ingredient::query()->whereColumn('stock_qty', '<=', 'min_stock_qty')->count()", 'At or below minimum', None, 'AlertTriangle', 'destructive'),
        ('value', 'Stock value', '$value', 'Quantity × cost per unit', "'₱'.number_format($value, 2)", 'Wallet', 'success'),
    ],
    summary_pre="        $value = (float) Ingredient::query()->get(['stock_qty', 'cost_per_unit'])->sum(fn ($i) => (float) $i->stock_qty * (float) $i->cost_per_unit);\n\n",
    transitions=[dict(
        name='restock', label='Add stock', icon='PackagePlus',
        prompt=dict(field='add_qty', label='Quantity received', type='number', min=0, step='0.001', rules="['required', 'numeric', 'gt:0', 'max:10000000']",
                    description="(row) => `Adds to the current ${row.stock_qty} ${row.unit} and logs a purchase movement.`"),
        uses='use App\\Services\\InventoryService;\n',
        php="            app(InventoryService::class)->addStockMovement($ingredient, 'purchase', (float) $value, 0.0, 'Restocked from the back office', auth()->user());",
        message="__(':qty :unit added to :name.', ['qty' => $value, 'unit' => $ingredient->unit, 'name' => $ingredient->name])",
        test=dict(data="['add_qty' => 250]", check="        $this->assertEquals(750, (float) $record->refresh()->stock_qty);\n        $this->assertDatabaseHas('stock_movements', ['ingredient_id' => $record->id, 'type' => 'purchase']);"),
    )],
    delete_guard=dict(when='$ingredient->recipeIngredients()->exists()',
                      message="__(':name is used in recipes. Remove it from those recipes first.', ['name' => $ingredient->name])"),
    delete_hidden='row.used_in_recipes > 0',
    edit_description="`${record.sku} · ${record.used_in_recipes} recipes`",
    preview_facts=[('Cost per unit', "data.item_unit && Number(data.item_unit) > 0 ? `₱${(Number(data.item_price) / Number(data.item_unit)).toFixed(4)}` : '—'"),
                   ('In stock', "`${data.stock_qty || 0} ${data.unit}`")],
    test=dict(
        record="['sku' => \"ING-{$n}\", 'name' => \"Ingredient {$n}\", 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 100, 'stock_qty' => 500, 'min_stock_qty' => 100, 'reorder_qty' => 1000]",
        payload="['sku' => 'ing-flour', 'name' => 'Bread flour', 'unit' => 'grams', 'item_unit' => '25000', 'item_price' => '1250', 'stock_qty' => '5000', 'min_stock_qty' => '2000', 'reorder_qty' => '25000']",
        assert_db="['sku' => 'ING-FLOUR', 'name' => 'Bread flour', 'cost_per_unit' => 0.05]",
        update_payload="['sku' => 'ING-X', 'name' => 'Cake flour', 'unit' => 'kg', 'item_unit' => '25', 'item_price' => '1000', 'stock_qty' => '10', 'min_stock_qty' => '5', 'reorder_qty' => '25']",
        update_assert="['name' => 'Cake flour', 'unit' => 'kg']",
        search_record="['name' => 'Muscovado sugar']", search_keyword='muscovado',
    ),
)

PACKAGING_TYPES = [('box', 'Box'), ('bag', 'Bag'), ('board', 'Board'), ('container', 'Container'), ('ribbon', 'Ribbon'), ('label', 'Label'),
                   ('sticker', 'Sticker'), ('tape', 'Tape'), ('wrap', 'Wrap'), ('other', 'Other')]

SPECS['packaging_material'] = dict(
    seq=3, domain='Inventory', model='PackagingMaterial', table='packaging_materials', permission_group='Inventory',
    label='Packaging material', labels='Packaging', icon='PackageOpen', menu_group=INVENTORY, menu_icon='package-open', menu_sort=3,
    plural='packaging-materials',
    description='Boxes, bags, labels and other packaging used per order.',
    create_description='Add a box, bag, label or other packaging item.',
    title_field='name', subtitle_field='sku', toggle_field='is_active',
    search=['name', 'sku', 'type'], search_placeholder='Name, SKU or type', default_sort=('name', 'asc'),
    filters=[dict(name='type', label='Type', type='select'), dict(name='is_active', label='Active', type='boolean')],
    fields=[
        dict(name='name', type='string', label='Name', required=True, placeholder='e.g. 8-inch cake box'),
        dict(name='sku', type='code', label='SKU', required=True, unique=True, max=50),
        dict(name='type', type='select', label='Type', required=True, default='box', options=PACKAGING_TYPES),
        dict(name='unit', type='string', label='Unit', required=True, default='pcs', max=20),
        dict(name='cost_per_unit', type='money', label='Cost per unit', required=True, default=0, min=0, section='Stock & cost'),
        dict(name='stock_qty', type='decimal', label='In stock', required=True, default=0, min=0, step='0.01', section='Stock & cost'),
        dict(name='min_stock_qty', type='decimal', label='Minimum level', required=True, default=10, min=0, step='0.01', section='Stock & cost'),
        dict(name='is_active', type='bool', label='Active', default=True, yes='In use', no='Retired', section='Stock & cost'),
        dict(name='notes', type='text', label='Notes', section='Stock & cost'),
    ],
    section_icons={'Details': 'PackageOpen', 'Stock & cost': 'Package'},
    resource_extra=[('is_low_stock', 'bool', '(float) $m->stock_qty <= (float) $m->min_stock_qty', 'boolean')],
    grid=[
        ('name', 'Item', 'title'),
        ('type', 'Type', 'badge'),
        ('stock_qty', 'In stock', 'qty', {'unit': 'unit', 'low_when': 'row.is_low_stock'}),
        ('cost_per_unit', 'Cost / unit', 'money', {'priority': 2}),
        ('is_active', 'Active', 'toggle'),
    ],
    summary=[
        ('total', 'Packaging items', 'PackagingMaterial::query()->count()', 'On file', None, 'PackageOpen', 'primary'),
        ('low', 'Low stock', "PackagingMaterial::query()->where('is_active', true)->whereColumn('stock_qty', '<=', 'min_stock_qty')->count()", 'Active items at or below minimum', None, 'AlertTriangle', 'destructive'),
        ('value', 'Stock value', '$value', 'Quantity × cost per unit', "'₱'.number_format($value, 2)", 'Wallet', 'success'),
    ],
    summary_pre="        $value = (float) PackagingMaterial::query()->get(['stock_qty', 'cost_per_unit'])->sum(fn ($i) => (float) $i->stock_qty * (float) $i->cost_per_unit);\n\n",
    transitions=[dict(
        name='restock', label='Add stock', icon='PackagePlus',
        prompt=dict(field='add_qty', label='Quantity received', type='number', min=1, step='1', rules="['required', 'numeric', 'gt:0', 'max:1000000']"),
        uses='use App\\Domains\\Inventory\\Models\\PackagingMovement;\n',
        php="""            PackagingMovement::query()->create([
                'packaging_material_id' => $packagingMaterial->id,
                'type' => 'in',
                'qty' => (float) $value,
                'unit_cost' => $packagingMaterial->cost_per_unit,
                'notes' => 'Restocked from the back office',
                'user_id' => auth()->id(),
            ]);
            $packagingMaterial->increment('stock_qty', (float) $value);""",
        message="__(':qty :unit added to :name.', ['qty' => $value, 'unit' => $packagingMaterial->unit, 'name' => $packagingMaterial->name])",
        test=dict(data="['add_qty' => 20]", check="        $this->assertEquals(70, (float) $record->refresh()->stock_qty);"),
    )],
    test=dict(
        record="['name' => \"Box {$n}\", 'sku' => \"PKG-{$n}\", 'type' => 'box', 'unit' => 'pcs', 'cost_per_unit' => 12, 'stock_qty' => 50, 'min_stock_qty' => 10, 'is_active' => true]",
        payload="['name' => 'Loaf box', 'sku' => 'pkg-loaf', 'type' => 'box', 'unit' => 'pcs', 'cost_per_unit' => '15.5', 'stock_qty' => '100', 'min_stock_qty' => '20', 'is_active' => '1']",
        assert_db="['sku' => 'PKG-LOAF', 'name' => 'Loaf box']",
        update_payload="['name' => 'Ribbon', 'sku' => 'PKG-R', 'type' => 'ribbon', 'unit' => 'm', 'cost_per_unit' => '2', 'stock_qty' => '5', 'min_stock_qty' => '1', 'is_active' => '0']",
        update_assert="['name' => 'Ribbon', 'type' => 'ribbon', 'is_active' => false]",
        search_record="['name' => 'Kraft bag']", search_keyword='kraft',
    ),
)

SPECS['coupon'] = dict(
    seq=4, domain='Sales', model='Coupon', table='coupons', permission_group='Orders & Sales',
    label='Coupon', labels='Coupons', icon='TicketPercent', menu_group=SALES, menu_icon='ticket-percent', menu_sort=5,
    description='Discount codes customers can apply at checkout.',
    create_description='Create a fixed or percentage discount code.',
    title_field='code', toggle_field='is_active',
    search=['code'], search_placeholder='Coupon code', default_sort=('created_at', 'desc'),
    filters=[dict(name='type', label='Type', type='select'), dict(name='is_active', label='Active', type='boolean')],
    fields=[
        dict(name='code', type='code', label='Code', required=True, unique=True, max=40, placeholder='SWEET10'),
        dict(name='type', type='select', label='Discount type', required=True, default='fixed', options=[('fixed', 'Fixed amount (₱)'), ('percent', 'Percentage (%)')]),
        dict(name='value', type='decimal', label='Value', required=True, min=0, step='0.01', description='Pesos for fixed, percent for percentage.'),
        dict(name='min_spend', type='money', label='Minimum spend', required=True, default=0, min=0, section='Limits'),
        dict(name='max_discount', type='money', label='Maximum discount', min=0, section='Limits', description='Caps percentage discounts. Leave empty for no cap.'),
        dict(name='usage_limit', type='int', label='Usage limit', min=1, section='Limits', description='Total redemptions allowed. Leave empty for unlimited.'),
        dict(name='starts_at', type='datetime', label='Starts', section='Schedule'),
        dict(name='expires_at', type='datetime', label='Expires', section='Schedule', after='starts_at'),
        dict(name='is_active', type='bool', label='Active', default=True, yes='Can be redeemed', no='Paused', section='Schedule'),
    ],
    messages={'value.max': 'A percentage discount cannot exceed 100%.'},
    section_icons={'Details': 'TicketPercent', 'Limits': 'Gauge', 'Schedule': 'CalendarClock'},
    resource_extra=[
        ('used_count', 'int', '(int) $m->used_count', 'number'),
        ('state', 'string', "match (true) { ! $m->is_active => 'inactive', $m->expires_at && $m->expires_at->isPast() => 'expired', $m->starts_at && $m->starts_at->isFuture() => 'scheduled', $m->usage_limit !== null && $m->used_count >= $m->usage_limit => 'exhausted', default => 'active' }", 'string'),
    ],
    grid=[
        ('code', 'Code', 'title'),
        ('type_label', 'Type', 'text', {'sortable': False}),
        ('value', 'Value', 'number'),
        ('used_count', 'Used', 'count', {'sortable': True}),
        ('state', 'Status', 'status', {'sortable': False, 'label_field': 'state'}),
        ('expires_at', 'Expires', 'datetime', {'priority': 2}),
        ('is_active', 'Active', 'toggle'),
    ],
    summary=[
        ('total', 'Coupons', 'Coupon::query()->count()', 'All codes', None, 'TicketPercent', 'primary'),
        ('active', 'Redeemable', "Coupon::query()->where('is_active', true)->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count()", 'Active and not expired', None, 'CheckCircle2', 'success'),
        ('redemptions', 'Redemptions', "(int) Coupon::query()->sum('used_count')", 'Times a code was applied', None, 'ShoppingBag', 'muted'),
    ],
    test=dict(
        record="['code' => \"CODE{$n}\", 'type' => 'fixed', 'value' => 50, 'min_spend' => 0, 'is_active' => true]",
        payload="['code' => 'sweet10', 'type' => 'percent', 'value' => '10', 'min_spend' => '500', 'max_discount' => '200', 'is_active' => '1']",
        assert_db="['code' => 'SWEET10', 'type' => 'percent']",
        update_payload="['code' => 'SWEET15', 'type' => 'percent', 'value' => '15', 'min_spend' => '0', 'is_active' => '0']",
        update_assert="['code' => 'SWEET15', 'is_active' => false]",
        search_record="['code' => 'BIRTHDAY']", search_keyword='birth',
    ),
)

SPECS['employee'] = dict(
    seq=5, domain='Employee', model='Employee', table='employees', permission_group='HR & Payroll',
    label='Employee', labels='Employees', icon='IdCard', menu_group=HR, menu_icon='id-card', menu_sort=1,
    description='Staff records, roles and monthly salary used by payroll.',
    create_description='Add a staff member. The employee number is generated if left empty.',
    title_field='first_name', subtitle_field='employee_number', toggle_field='is_active',
    search=['first_name', 'last_name', 'employee_number', 'email', 'phone'], search_placeholder='Name, number, email or phone',
    default_sort=('first_name', 'asc'), eager=['user'],
    filters=[dict(name='role_title', label='Role', type='select'), dict(name='employment_type', label='Employment', type='select'),
             dict(name='is_active', label='Active', type='boolean')],
    fields=[
        dict(name='first_name', type='string', label='First name', required=True, max=100),
        dict(name='last_name', type='string', label='Last name', required=True, max=100),
        dict(name='employee_number', type='code', label='Employee number', unique=True, max=30, keep_on_update=True,
             description='Leave empty to generate (EMP-0001).'),
        dict(name='email', type='email', label='Email', required=True),
        dict(name='phone', type='phone', label='Phone', max=30),
        dict(name='user_id', type='relation', label='Login account', section='Employment',
             relation=dict(table='users', display='user_email', expr='$m->user?->email', options=USER_OPTIONS, options_prop='user_options'),
             description='Link a back-office account, if this person signs in.'),
        dict(name='role_title', type='select', label='Role', required=True, default='Pastry Staff', section='Employment',
             options=[('Head Baker', 'Head Baker'), ('Assistant Baker', 'Assistant Baker'), ('Cashier', 'Cashier'), ('Pastry Staff', 'Pastry Staff'), ('Delivery Rider', 'Delivery Rider'), ('Manager', 'Manager')]),
        dict(name='employment_type', type='select', label='Employment type', required=True, default='full_time', section='Employment',
             options=[('full_time', 'Full-time regular'), ('part_time', 'Part-time flex')]),
        dict(name='basic_monthly_salary', type='money', label='Basic monthly salary', required=True, default=20000, min=0, section='Employment'),
        dict(name='hired_at', type='date', label='Hired on', required=True, section='Employment'),
        dict(name='is_active', type='bool', label='Active', default=True, yes='Employed', no='Inactive', section='Employment'),
    ],
    section_icons={'Details': 'IdCard', 'Employment': 'Briefcase'},
    resource_extra=[('full_name', 'string', '$m->full_name', 'string')],
    grid=[
        ('full_name', 'Employee', 'title', {'sort_field': 'first_name'}),
        ('role_title', 'Role', 'text'),
        ('employment_type_label', 'Type', 'text', {'priority': 3, 'sortable': False}),
        ('basic_monthly_salary', 'Monthly salary', 'money', {'priority': 2}),
        ('hired_at', 'Hired', 'date', {'priority': 3}),
        ('is_active', 'Active', 'toggle'),
    ],
    summary=[
        ('active', 'Active staff', "Employee::query()->where('is_active', true)->count()", 'Currently employed', None, 'Users', 'primary'),
        ('full_time', 'Full-time', "Employee::query()->where('is_active', true)->where('employment_type', 'full_time')->count()", 'Regular employees', None, 'Briefcase', 'success'),
        ('payroll', 'Monthly payroll', '$payroll', 'Basic salaries of active staff', "'₱'.number_format($payroll, 2)", 'Wallet', 'muted'),
    ],
    summary_pre="        $payroll = (float) Employee::query()->where('is_active', true)->sum('basic_monthly_salary');\n\n",
    delete_guard=dict(when='$employee->payrollItems()->exists()',
                      message="__(':name has payroll history. Mark them inactive instead.', ['name' => $employee->full_name])"),
    edit_description="`${record.employee_number} · ${record.role_title}`",
    test=dict(
        record="['first_name' => \"Staff{$n}\", 'last_name' => 'Cruz', 'email' => \"e{$n}@example.test\", 'role_title' => 'Cashier', 'employment_type' => 'full_time', 'basic_monthly_salary' => 18000, 'hired_at' => '2026-01-05', 'is_active' => true]",
        payload="['first_name' => 'Lia', 'last_name' => 'Santos', 'email' => 'lia@example.test', 'role_title' => 'Head Baker', 'employment_type' => 'full_time', 'basic_monthly_salary' => '25000', 'hired_at' => '2026-03-01', 'is_active' => '1']",
        assert_db="['first_name' => 'Lia', 'role_title' => 'Head Baker']",
        update_payload="['first_name' => 'Lia', 'last_name' => 'Reyes', 'email' => 'lia@example.test', 'role_title' => 'Manager', 'employment_type' => 'part_time', 'basic_monthly_salary' => '15000', 'hired_at' => '2026-03-01', 'is_active' => '0']",
        update_assert="['last_name' => 'Reyes', 'employment_type' => 'part_time', 'is_active' => false]",
        search_record="['first_name' => 'Marisol']", search_keyword='marisol',
    ),
)

EMPLOYEE_REL = dict(table='employees', soft=True, display='employee_name', expr='$m->employee?->full_name',
                    options="\\App\\Shared\\Queries\\OptionsQuery::for(\\App\\Domains\\Employee\\Models\\Employee::class, 'first_name', fn ($q) => $q->where('is_active', true), 'employee_number')",
                    options_prop='employee_options')
ATTENDANCE_STATUS = [('present', 'Present'), ('absent', 'Absent'), ('late', 'Late'), ('half_day', 'Half day'), ('on_leave', 'On leave')]

SPECS['attendance'] = dict(
    seq=6, domain='Employee', model='Attendance', table='attendances', permission_group='HR & Payroll',
    label='Attendance', labels='Attendance', icon='CalendarCheck', menu_group=HR, menu_icon='calendar-check', menu_sort=2,
    plural='attendances',
    description='Daily time records, overtime and holidays — the input for payroll.',
    create_description='Log one employee-day.',
    title_field='date', subtitle_field='status', status_field='status',
    search=['employee.first_name', 'employee.last_name', 'notes'], search_placeholder='Employee name or notes',
    default_sort=('date', 'desc'), eager=['employee'],
    filters=[
        dict(name='employee_id', label='Employee', type='relation', options_prop='employee_options', options=EMPLOYEE_REL['options']),
        dict(name='status', label='Status', type='select'),
        dict(name='from', label='From', type='date_from', column='date'),
        dict(name='to', label='To', type='date_to', column='date'),
    ],
    fields=[
        dict(name='employee_id', type='relation', label='Employee', required=True, relation=EMPLOYEE_REL),
        dict(name='date', type='date', label='Date', required=True),
        dict(name='status', type='select', label='Status', required=True, default='present', options=ATTENDANCE_STATUS),
        dict(name='time_in', type='time', label='Time in', section='Hours'),
        dict(name='time_out', type='time', label='Time out', section='Hours', rules=["'after:time_in'"]),
        dict(name='hours_worked', type='decimal', label='Hours worked', required=True, default=8, min=0, step='0.25', max_value=24, section='Hours'),
        dict(name='is_overtime', type='bool', label='Overtime', section='Hours', yes='Has overtime', no='No overtime'),
        dict(name='overtime_hours', type='decimal', label='Overtime hours', required=True, default=0, min=0, step='0.25', max_value=24, section='Hours'),
        dict(name='is_holiday', type='bool', label='Holiday', section='Hours', yes='Holiday pay applies', no='Regular day'),
        dict(name='notes', type='text', label='Notes', section='Hours'),
    ],
    messages={'time_out.after': 'Time out must be after time in.'},
    section_icons={'Details': 'CalendarCheck', 'Hours': 'Clock'},
    grid=[
        ('employee_name', 'Employee', 'title', {'subtitle': 'date', 'sortable': False}),
        ('date', 'Date', 'date'),
        ('status', 'Status', 'status'),
        ('hours_worked', 'Hours', 'number'),
        ('overtime_hours', 'OT hours', 'number', {'priority': 2}),
        ('is_holiday', 'Holiday', 'bool', {'priority': 3}),
    ],
    summary=[
        ('today', 'Present today', "Attendance::query()->whereDate('date', today())->whereIn('status', ['present', 'late', 'half_day'])->count()", 'Logged for today', None, 'UserCheck', 'success'),
        ('late', 'Late this month', "Attendance::query()->where('status', 'late')->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->count()", 'Since the 1st', None, 'Clock', 'warning'),
        ('overtime', 'Overtime this month', "(float) Attendance::query()->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->sum('overtime_hours')", 'Hours', None, 'Timer', 'muted'),
    ],
    messages_extra=None,
    test=dict(
        record_setup="        $employee = \\App\\Domains\\Employee\\Models\\Employee::query()->create(['first_name' => \"Emp{$n}\", 'last_name' => 'Dela Cruz', 'email' => \"emp{$n}@example.test\", 'hired_at' => '2026-01-01']);\n",
        record="['employee_id' => $employee->id, 'date' => '2026-11-0'.min(9, $n), 'status' => 'present', 'hours_worked' => 8, 'overtime_hours' => 0]",
        setup="        $employee = \\App\\Domains\\Employee\\Models\\Employee::query()->create(['first_name' => 'Ana', 'last_name' => 'Lim', 'email' => 'ana@example.test', 'hired_at' => '2026-01-01']);\n",
        payload="['employee_id' => $employee->id, 'date' => '2026-11-14', 'status' => 'late', 'time_in' => '08:15', 'time_out' => '17:00', 'hours_worked' => '7.75', 'overtime_hours' => '0', 'is_overtime' => '0', 'is_holiday' => '0']",
        assert_db="['employee_id' => $employee->id, 'status' => 'late']",
        update_payload="['employee_id' => $employee->id, 'date' => '2026-11-14', 'status' => 'absent', 'hours_worked' => '0', 'overtime_hours' => '0']",
        update_assert="['status' => 'absent']",
        search_record="['notes' => 'Doctor appointment']", search_keyword='doctor',
    ),
)
SPECS['attendance'].pop('messages_extra')

PRODUCT_OPTIONS = "\\App\\Shared\\Queries\\OptionsQuery::for(\\App\\Domains\\Catalog\\Models\\Product::class, 'name', null, 'sku')"
PRODUCT_REL = dict(table='products', display='product_name', expr='$m->product?->name', options=PRODUCT_OPTIONS, options_prop='product_options')

SPECS['review'] = dict(
    seq=7, domain='Review', model='Review', table='reviews', permission_group='Orders & Sales',
    label='Review', labels='Reviews', icon='Star', menu_group=SALES, menu_icon='star', menu_sort=6,
    description='Product and store reviews — approve, feature or hide them.',
    create_description='Add a review on behalf of a customer (e.g. from Facebook or in-store).',
    title_field='reviewer_name', subtitle_field='title', toggle_field='is_approved',
    search=['reviewer_name', 'reviewer_email', 'title', 'comment', 'product.name'], search_placeholder='Reviewer, title, comment or product',
    default_sort=('created_at', 'desc'), eager=['product'],
    filters=[
        dict(name='product_id', label='Product', type='relation', options_prop='product_options', options=PRODUCT_OPTIONS),
        dict(name='rating', label='Rating', type='select', int=True, options=[(5, '5 stars'), (4, '4 stars'), (3, '3 stars'), (2, '2 stars'), (1, '1 star')]),
        dict(name='is_approved', label='Approved', type='boolean'),
        dict(name='is_featured', label='Featured', type='boolean'),
    ],
    fields=[
        dict(name='product_id', type='relation', label='Product', relation=PRODUCT_REL, description='Leave empty for a store / service review.'),
        dict(name='rating', type='select', int=True, label='Rating', required=True, default=5, options=[(5, '★★★★★ 5'), (4, '★★★★ 4'), (3, '★★★ 3'), (2, '★★ 2'), (1, '★ 1')]),
        dict(name='reviewer_name', type='string', label='Reviewer name', required=True),
        dict(name='reviewer_email', type='email', label='Reviewer email', required=True),
        dict(name='title', type='string', label='Headline'),
        dict(name='comment', type='text', label='Review', required=True, max=3000),
        dict(name='is_approved', type='bool', label='Approved', default=True, yes='Shown publicly', no='Hidden', section='Visibility'),
        dict(name='is_featured', type='bool', label='Featured', section='Visibility', yes='Highlighted on the homepage', no='Not featured'),
        dict(name='is_verified_buyer', type='bool', label='Verified buyer', section='Visibility', yes='Bought this product', no='Unverified'),
        dict(name='is_anonymous', type='bool', label='Anonymous', section='Visibility', yes='Name hidden publicly', no='Name shown'),
    ],
    section_icons={'Details': 'Star', 'Visibility': 'Eye'},
    resource_extra=[('helpful_votes', 'int', '(int) $m->helpful_votes', 'number')],
    grid=[
        ('reviewer_name', 'Reviewer', 'title'),
        ('product_name', 'Product', 'relation', {'priority': 2}),
        ('rating', 'Rating', 'stars'),
        ('helpful_votes', 'Helpful', 'count', {'priority': 3}),
        ('is_featured', 'Featured', 'bool', {'priority': 2}),
        ('created_at', 'Posted', 'date', {'priority': 3}),
        ('is_approved', 'Approved', 'toggle'),
    ],
    summary=[
        ('total', 'Reviews', 'Review::query()->count()', 'All time', None, 'MessageSquare', 'primary'),
        ('average', 'Average rating', 'round((float) Review::query()->where(\'is_approved\', true)->avg(\'rating\'), 1)', 'Approved reviews', None, 'Star', 'warning'),
        ('pending', 'Hidden', "Review::query()->where('is_approved', false)->count()", 'Waiting for approval', None, 'EyeOff', 'muted'),
        ('featured', 'Featured', "Review::query()->where('is_featured', true)->count()", 'On the homepage', None, 'Sparkles', 'success'),
    ],
    transitions=[dict(name='feature', label='Toggle featured', icon='Sparkles',
                      php='            $review->update([\'is_featured\' => ! $review->is_featured]);',
                      message="$review->is_featured ? __('Review featured on the homepage.') : __('Review removed from the homepage.')",
                      test=dict(check="        $this->assertTrue($record->refresh()->is_featured);"))],
    test=dict(
        record="['reviewer_name' => \"Reviewer {$n}\", 'reviewer_email' => \"r{$n}@example.test\", 'rating' => 5, 'comment' => 'Lovely', 'is_approved' => true]",
        payload="['reviewer_name' => 'Joy', 'reviewer_email' => 'JOY@example.test', 'rating' => '4', 'title' => 'Moist!', 'comment' => 'Best banana bread in Cavite', 'is_approved' => '1']",
        assert_db="['reviewer_name' => 'Joy', 'reviewer_email' => 'joy@example.test', 'rating' => 4]",
        update_payload="['reviewer_name' => 'Joy', 'reviewer_email' => 'joy@example.test', 'rating' => '5', 'comment' => 'Edited', 'is_approved' => '0']",
        update_assert="['rating' => 5, 'is_approved' => false]",
        search_record="['comment' => 'The ube cheesecake was amazing']", search_keyword='ube',
    ),
)

SPECS['blog_post'] = dict(
    seq=8, domain='Content', model='BlogPost', table='blog_posts', permission_group='Engagement & Content',
    thing='BlogPost', label='Blog post', labels='Blog posts', icon='Newspaper', menu_group=CONTENT, menu_icon='newspaper', menu_sort=1,
    description='Journal and vlog posts shown on the storefront blog.',
    create_description='Write a journal post. Save it as a draft until it is ready.',
    title_field='title', subtitle_field='category', status_field='status',
    search=['title', 'slug', 'excerpt', 'category'], search_placeholder='Title, slug, excerpt or category',
    default_sort=('created_at', 'desc'), eager=['author'],
    filters=[dict(name='status', label='Status', type='select')],
    fields=[
        dict(name='title', type='string', label='Title', required=True),
        dict(name='slug', type='slug', label='URL slug', required=True, unique=True, description='Leave empty to generate from the title.'),
        dict(name='category', type='string', label='Category', required=True, default='General', max=100),
        dict(name='excerpt', type='text', label='Excerpt', max=600),
        dict(name='body', type='richtext', label='Body', required=True, section='Content', description='Markdown is supported.'),
        dict(name='cover_image', type='image', label='Cover image', section='Content'),
        dict(name='status', type='select', label='Status', required=True, default='draft', section='Publishing',
             options=[('draft', 'Draft'), ('published', 'Published'), ('archived', 'Archived')]),
        dict(name='published_at', type='datetime', label='Publish date', section='Publishing', description='Set automatically the first time it is published.'),
        dict(name='seo_title', type='string', label='SEO title', section='Publishing', max=70),
        dict(name='seo_description', type='text', label='SEO description', section='Publishing', max=160),
    ],
    create_extra="\n            'author_id' => auth()->id(),",
    section_icons={'Details': 'Newspaper', 'Content': 'FileText', 'Publishing': 'Send'},
    resource_extra=[('author_name', '?string', '$m->author?->name', 'string | null')],
    grid=[
        ('title', 'Post', 'title'),
        ('status', 'Status', 'status'),
        ('author_name', 'Author', 'relation', {'priority': 3}),
        ('published_at', 'Published', 'date', {'priority': 2}),
        ('updated_at', 'Updated', 'date', {'priority': 3}),
    ],
    summary=[
        ('published', 'Published', "BlogPost::query()->where('status', 'published')->count()", 'Live on the storefront', None, 'Send', 'success'),
        ('drafts', 'Drafts', "BlogPost::query()->where('status', 'draft')->count()", 'Not yet published', None, 'PenLine', 'warning'),
        ('total', 'All posts', 'BlogPost::query()->count()', 'Including archived', None, 'Newspaper', 'primary'),
    ],
    transitions=[dict(name='publish', label='Publish now', icon='Send', hidden="row.status === 'published'",
                      php="            $blogPost->update(['status' => 'published', 'published_at' => $blogPost->published_at ?? now()]);",
                      message="__(':title is now live on the blog.', ['title' => $blogPost->title])",
                      test=dict(record="['status' => 'draft']", check="        $this->assertSame('published', $record->refresh()->status);\n        $this->assertNotNull($record->published_at);"))],
    test=dict(
        record="['title' => \"Post {$n}\", 'slug' => \"post-{$n}\", 'body' => 'Hello', 'status' => 'draft', 'category' => 'General']",
        payload="['title' => 'Why we use real butter', 'slug' => '', 'category' => 'Baking', 'body' => 'Long story', 'status' => 'draft']",
        assert_db="['slug' => 'why-we-use-real-butter', 'category' => 'Baking']",
        update_payload="['title' => 'Real butter, always', 'slug' => 'real-butter', 'category' => 'Baking', 'body' => 'Edited', 'status' => 'archived']",
        update_assert="['slug' => 'real-butter', 'status' => 'archived']",
        search_record="['title' => 'Ube season is here']", search_keyword='ube',
    ),
)

SPECS['contact_message'] = dict(
    seq=9, domain='Content', model='ContactMessage', table='contact_messages', permission_group='Engagement & Content',
    label='Contact message', labels='Contact messages', icon='Mail', menu_group=CONTENT, menu_icon='mail', menu_sort=3,
    mode='edit-only',
    description='Messages sent from the storefront contact form.',
    title_field='subject', subtitle_field='name', status_field='status',
    search=['name', 'email', 'subject', 'message'], search_placeholder='Name, email, subject or message',
    default_sort=('created_at', 'desc'),
    filters=[dict(name='status', label='Status', type='select')],
    fields=[
        dict(name='name', type='string', label='Name', required=True),
        dict(name='email', type='email', label='Email', required=True),
        dict(name='phone', type='phone', label='Phone', max=30),
        dict(name='subject', type='string', label='Subject', required=True),
        dict(name='message', type='text', label='Message', required=True, max=5000),
        dict(name='status', type='select', label='Status', required=True, default='unread', section='Follow-up',
             options=[('unread', 'Unread'), ('read', 'Read'), ('replied', 'Replied'), ('archived', 'Archived')]),
        dict(name='admin_notes', type='text', label='Internal notes', section='Follow-up', description='Only visible to staff.'),
    ],
    section_icons={'Details': 'Mail', 'Follow-up': 'MessageSquareReply'},
    resource_extra=[('replied_at', '?string', '$m->replied_at?->toIso8601String()', 'string | null')],
    grid=[
        ('subject', 'Subject', 'title'),
        ('email', 'Email', 'text', {'priority': 2}),
        ('status', 'Status', 'status'),
        ('created_at', 'Received', 'datetime'),
    ],
    summary=[
        ('unread', 'Unread', "ContactMessage::query()->where('status', 'unread')->count()", 'Waiting for someone to read', None, 'MailWarning', 'warning'),
        ('replied', 'Replied', "ContactMessage::query()->where('status', 'replied')->count()", 'Answered', None, 'MailCheck', 'success'),
        ('week', 'This week', 'ContactMessage::query()->where(\'created_at\', \'>=\', now()->startOfWeek())->count()', 'New messages', None, 'Inbox', 'primary'),
    ],
    transitions=[
        dict(name='mark_read', label='Mark as read', icon='MailOpen', hidden="row.status !== 'unread'",
             php="            $contactMessage->update(['status' => 'read']);", message="__('Marked as read.')",
             test=dict(check="        $this->assertSame('read', $record->refresh()->status);")),
        dict(name='mark_replied', label='Mark as replied', icon='MailCheck', hidden="row.status === 'replied'",
             php="            $contactMessage->update(['status' => 'replied', 'replied_by' => auth()->id(), 'replied_at' => now()]);",
             message="__('Marked as replied.')",
             test=dict(check="        $this->assertSame('replied', $record->refresh()->status);\n        $this->assertNotNull($record->replied_at);")),
    ],
    test=dict(
        record="['name' => \"Guest {$n}\", 'email' => \"g{$n}@example.test\", 'subject' => \"Question {$n}\", 'message' => 'Do you deliver to Imus?', 'status' => 'unread']",
        update_payload="['name' => 'Guest', 'email' => 'g@example.test', 'subject' => 'Delivery', 'message' => 'Do you deliver?', 'status' => 'archived', 'admin_notes' => 'Answered by phone']",
        update_assert="['status' => 'archived', 'admin_notes' => 'Answered by phone']",
        search_record="['subject' => 'Wedding cake inquiry']", search_keyword='wedding',
    ),
)

SPECS['suggestion'] = dict(
    seq=10, domain='Content', model='Suggestion', table='suggestions', permission_group='Engagement & Content',
    label='Suggestion', labels='Suggestions', icon='Lightbulb', menu_group=CONTENT, menu_icon='lightbulb', menu_sort=2,
    mode='edit-only',
    description='Product ideas and feedback customers send from the storefront.',
    title_field='subject', subtitle_field='name', status_field='status',
    search=['name', 'email', 'subject', 'message'], search_placeholder='Name, email, subject or message',
    default_sort=('created_at', 'desc'),
    filters=[dict(name='status', label='Status', type='select'), dict(name='category', label='Category', type='select')],
    fields=[
        dict(name='name', type='string', label='Name', required=True),
        dict(name='email', type='email', label='Email', required=True),
        dict(name='category', type='select', label='Category', required=True, default='other',
             options=[('product_idea', 'Product idea'), ('service_feedback', 'Service feedback'), ('feature_request', 'Feature request'), ('other', 'Other')]),
        dict(name='subject', type='string', label='Subject', required=True),
        dict(name='message', type='text', label='Message', required=True, max=5000),
        dict(name='status', type='select', label='Status', required=True, default='new', section='Follow-up',
             options=[('new', 'New'), ('reviewing', 'Reviewing'), ('implemented', 'Implemented'), ('declined', 'Declined')]),
        dict(name='admin_notes', type='text', label='Internal notes', section='Follow-up'),
    ],
    section_icons={'Details': 'Lightbulb', 'Follow-up': 'ClipboardCheck'},
    grid=[
        ('subject', 'Suggestion', 'title'),
        ('category_label', 'Category', 'text', {'sortable': False, 'priority': 2}),
        ('status', 'Status', 'status'),
        ('created_at', 'Received', 'date'),
    ],
    summary=[
        ('new', 'New', "Suggestion::query()->where('status', 'new')->count()", 'Not reviewed yet', None, 'Sparkles', 'warning'),
        ('reviewing', 'Reviewing', "Suggestion::query()->where('status', 'reviewing')->count()", 'Being considered', None, 'Search', 'primary'),
        ('implemented', 'Implemented', "Suggestion::query()->where('status', 'implemented')->count()", 'Ideas we shipped', None, 'CheckCircle2', 'success'),
    ],
    transitions=[
        dict(name='mark_reviewing', label='Start reviewing', icon='Search', hidden="row.status !== 'new'",
             php="            $suggestion->update(['status' => 'reviewing']);", message="__('Moved to reviewing.')",
             test=dict(check="        $this->assertSame('reviewing', $record->refresh()->status);")),
        dict(name='mark_implemented', label='Mark implemented', icon='CheckCircle2', hidden="row.status === 'implemented'",
             php="            $suggestion->update(['status' => 'implemented']);", message="__('Marked as implemented. Nice!')",
             test=dict(check="        $this->assertSame('implemented', $record->refresh()->status);")),
    ],
    test=dict(
        record="['name' => \"Fan {$n}\", 'email' => \"f{$n}@example.test\", 'category' => 'product_idea', 'subject' => \"Idea {$n}\", 'message' => 'Mango float please', 'status' => 'new']",
        update_payload="['name' => 'Fan', 'email' => 'f@example.test', 'category' => 'other', 'subject' => 'Idea', 'message' => 'Mango float', 'status' => 'declined', 'admin_notes' => 'Seasonal only']",
        update_assert="['status' => 'declined', 'category' => 'other']",
        search_record="['subject' => 'Gluten-free brownies']", search_keyword='gluten',
    ),
)

BATCH_STATUS = [('planned', 'Planned'), ('in_prep', 'In prep'), ('baking', 'Baking'), ('completed', 'Completed'), ('cancelled', 'Cancelled')]
RECIPE_REL = dict(table='recipes', display='recipe_name', expr='$m->recipe?->name',
                  options="\\App\\Shared\\Queries\\OptionsQuery::for(\\App\\Domains\\Costing\\Models\\Recipe::class)", options_prop='recipe_options')

SPECS['production_batch'] = dict(
    seq=11, domain='Production', model='ProductionBatch', table='production_batches', permission_group='Production & Purchasing',
    label='Production batch', labels='Production batches', icon='CookingPot', menu_group=PRODUCTION, menu_icon='cooking-pot', menu_sort=1,
    plural='production-batches',
    description='Plan and complete bakes. Completing a batch deducts ingredients and adds finished stock.',
    create_description='Plan a bake from a recipe. Stock moves only when the batch is completed.',
    title_field='batch_number', subtitle_field='product_name', status_field='status',
    search=['batch_number', 'notes', 'product.name', 'recipe.name'], search_placeholder='Batch number, product or recipe',
    default_sort=('created_at', 'desc'), eager=['product', 'recipe', 'baker'],
    filters=[dict(name='status', label='Status', type='select'),
             dict(name='product_id', label='Product', type='relation', options_prop='product_options', options=PRODUCT_OPTIONS)],
    fields=[
        dict(name='batch_number', type='code', label='Batch number', unique=True, max=40,
             auto="'PB-'.now()->format('Ymd-His')", description='Leave empty to generate.'),
        dict(name='recipe_id', type='relation', label='Recipe', required=True, relation=RECIPE_REL),
        dict(name='product_id', type='relation', label='Product', required=True, relation=PRODUCT_REL),
        dict(name='planned_qty', type='int', label='Planned quantity', required=True, default=1, min=1),
        dict(name='actual_yield_qty', type='int', label='Actual yield', min=0, description='Fill in after baking; defaults to the planned quantity.'),
        dict(name='status', type='select', label='Status', required=True, default='planned', options=BATCH_STATUS, section='Team & notes',
             rules=["Rule::notIn(['completed'])"]),
        dict(name='baker_user_id', type='relation', label='Baker', section='Team & notes',
             relation=dict(table='users', display='baker_name', expr='$m->baker?->name', options=USER_OPTIONS, options_prop='baker_options')),
        dict(name='notes', type='text', label='Notes', section='Team & notes'),
    ],
    messages={'status.not_in': 'Use “Complete batch” to finish a batch — that is what moves the stock.'},
    section_icons={'Details': 'CookingPot', 'Team & notes': 'Users'},
    resource_extra=[('completed_at', '?string', '$m->completed_at?->toIso8601String()', 'string | null'),
                    ('is_completed', 'bool', "$m->status === 'completed'", 'boolean')],
    grid=[
        ('batch_number', 'Batch', 'title'),
        ('recipe_name', 'Recipe', 'relation', {'priority': 2}),
        ('planned_qty', 'Planned', 'number'),
        ('actual_yield_qty', 'Yield', 'number', {'priority': 2}),
        ('status', 'Status', 'status'),
        ('completed_at', 'Completed', 'datetime', {'priority': 3}),
    ],
    summary=[
        ('planned', 'Planned', "ProductionBatch::query()->where('status', 'planned')->count()", 'Not started', None, 'CalendarClock', 'muted'),
        ('active', 'In progress', "ProductionBatch::query()->whereIn('status', ['in_prep', 'baking'])->count()", 'Prepping or in the oven', None, 'Flame', 'warning'),
        ('completed_week', 'Completed this week', "ProductionBatch::query()->where('status', 'completed')->where('completed_at', '>=', now()->startOfWeek())->count()", 'Since Monday', None, 'CheckCircle2', 'success'),
    ],
    transitions=[dict(
        name='complete', label='Complete batch', icon='CheckCircle2', hidden='row.is_completed', permission='edit',
        confirm='Complete this batch?', confirm_description='Ingredients are deducted from inventory and the finished quantity is added to product stock.',
        guard="        if ($productionBatch->status === 'completed') {\n            throw new DomainRuleViolation(__('This batch is already completed.'));\n        }\n\n",
        uses='use App\\Services\\ProductionService;\n',
        php='            app(ProductionService::class)->completeBatch($productionBatch, $productionBatch->actual_yield_qty, auth()->user());',
        message="__('Batch :number completed — stock updated.', ['number' => $productionBatch->batch_number])",
    )],
    delete_guard=dict(when="$productionBatch->status === 'completed'",
                      message="__('Completed batches already moved stock and cannot be deleted.')"),
    delete_hidden='row.is_completed',
    test=dict(
        record_setup="""        $category = \\App\\Domains\\Catalog\\Models\\ProductCategory::query()->create(['name' => "Cat {$n}", 'slug' => "cat-{$n}"]);
        $product = \\App\\Domains\\Catalog\\Models\\Product::query()->create(['category_id' => $category->id, 'sku' => "P-{$n}", 'name' => "Loaf {$n}", 'slug' => "loaf-{$n}", 'price' => 100]);
        $recipe = \\App\\Domains\\Costing\\Models\\Recipe::query()->create(['product_id' => $product->id, 'name' => "Recipe {$n}"]);
""",
        record="['batch_number' => \"PB-{$n}\", 'recipe_id' => $recipe->id, 'product_id' => $product->id, 'planned_qty' => 10, 'status' => 'planned']",
        setup="""        $category = \\App\\Domains\\Catalog\\Models\\ProductCategory::query()->create(['name' => 'Breads', 'slug' => 'breads']);
        $product = \\App\\Domains\\Catalog\\Models\\Product::query()->create(['category_id' => $category->id, 'sku' => 'P-X', 'name' => 'Banana loaf', 'slug' => 'banana-loaf', 'price' => 350]);
        $recipe = \\App\\Domains\\Costing\\Models\\Recipe::query()->create(['product_id' => $product->id, 'name' => 'Banana loaf recipe']);
""",
        payload="['recipe_id' => $recipe->id, 'product_id' => $product->id, 'planned_qty' => '24', 'status' => 'planned']",
        assert_db="['product_id' => $product->id, 'planned_qty' => 24]",
        update_payload="['batch_number' => 'PB-EDIT', 'recipe_id' => $recipe->id, 'product_id' => $product->id, 'planned_qty' => '12', 'status' => 'baking']",
        update_assert="['batch_number' => 'PB-EDIT', 'status' => 'baking']",
        search_record="['batch_number' => 'PB-UBE-01']", search_keyword='ube',
    ),
)

CUSTOM_STATUS = [('inquiry', 'Inquiry'), ('quoted', 'Quoted'), ('deposit_paid', 'Deposit paid'), ('in_production', 'In production'), ('ready', 'Ready'), ('completed', 'Completed'), ('cancelled', 'Cancelled')]

SPECS['custom_order'] = dict(
    seq=12, domain='Sales', model='CustomOrder', table='custom_orders', permission_group='Orders & Sales',
    label='Custom order', labels='Custom orders', icon='Cake', menu_group=SALES, menu_icon='cake', menu_sort=2,
    description='Custom cake inquiries — quote, schedule production and hand over.',
    create_description='Log a custom cake request taken by phone, chat or in-store.',
    title_field='reference_number', subtitle_field='customer_name', status_field='status',
    search=['reference_number', 'customer_name', 'customer_email', 'customer_phone', 'theme_description'], search_placeholder='Reference, customer or theme',
    default_sort=('event_date', 'asc'),
    filters=[dict(name='status', label='Status', type='select'), dict(name='from', label='Event from', type='date_from', column='event_date'),
             dict(name='to', label='Event to', type='date_to', column='event_date')],
    fields=[
        dict(name='reference_number', type='code', label='Reference', unique=True, max=40, auto="'CUST-'.now()->format('Ymd').'-'.strtoupper(\\Illuminate\\Support\\Str::random(4))",
             description='Leave empty to generate.'),
        dict(name='customer_name', type='string', label='Customer name', required=True),
        dict(name='customer_email', type='email', label='Email', required=True),
        dict(name='customer_phone', type='phone', label='Phone', required=True, max=30, rules=['new \\App\\Rules\\PhilippinePhone']),
        dict(name='event_date', type='date', label='Event date', required=True, section='Cake'),
        dict(name='servings_count', type='int', label='Servings', required=True, default=20, min=1, section='Cake'),
        dict(name='tiers_count', type='int', label='Tiers', required=True, default=1, min=1, max_value=10, section='Cake'),
        dict(name='flavor_preference', type='string', label='Flavor', section='Cake'),
        dict(name='frosting_type', type='string', label='Frosting', section='Cake'),
        dict(name='theme_description', type='text', label='Theme & design', required=True, section='Cake', max=3000),
        dict(name='budget_range_min', type='money', label='Budget from', min=0, section='Quote'),
        dict(name='budget_range_max', type='money', label='Budget to', min=0, section='Quote'),
        dict(name='quoted_price', type='money', label='Quoted price', min=0, section='Quote'),
        dict(name='status', type='select', label='Status', required=True, default='inquiry', options=CUSTOM_STATUS, section='Quote'),
        dict(name='staff_notes', type='text', label='Staff notes', section='Quote'),
    ],
    section_icons={'Details': 'User', 'Cake': 'Cake', 'Quote': 'Receipt'},
    grid=[
        ('reference_number', 'Order', 'title'),
        ('event_date', 'Event', 'date'),
        ('servings_count', 'Servings', 'number', {'priority': 2}),
        ('quoted_price', 'Quote', 'money', {'priority': 2}),
        ('status', 'Status', 'status'),
    ],
    summary=[
        ('open', 'Open inquiries', "CustomOrder::query()->whereIn('status', ['inquiry', 'quoted'])->count()", 'Waiting on a quote or reply', None, 'MessageCircleQuestion', 'warning'),
        ('upcoming', 'Next 7 days', "CustomOrder::query()->whereNotIn('status', ['completed', 'cancelled'])->whereBetween('event_date', [today(), today()->addDays(7)])->count()", 'Events coming up', None, 'CalendarClock', 'primary'),
        ('in_production', 'In production', "CustomOrder::query()->where('status', 'in_production')->count()", 'Being baked', None, 'Cake', 'muted'),
        ('completed', 'Completed', "CustomOrder::query()->where('status', 'completed')->count()", 'Delivered or picked up', None, 'CheckCircle2', 'success'),
    ],
    transitions=[
        dict(name='start_production', label='Start production', icon='Flame',
             hidden="!['inquiry', 'quoted', 'deposit_paid'].includes(row.status)",
             confirm='Start production?', confirm_description='Ingredients for this cake are deducted from inventory.',
             uses='use App\\Services\\InventoryDeductionService;\n',
             php="            app(InventoryDeductionService::class)->deductForCustomOrder($customOrder);\n            $customOrder->update(['status' => 'in_production']);",
             message="__(':ref is now in production.', ['ref' => $customOrder->reference_number])"),
        dict(name='complete', label='Mark completed', icon='CheckCircle2', hidden="['completed', 'cancelled'].includes(row.status)",
             php="            $customOrder->update(['status' => 'completed']);",
             message="__(':ref marked as completed.', ['ref' => $customOrder->reference_number])",
             test=dict(check="        $this->assertSame('completed', $record->refresh()->status);")),
    ],
    test=dict(
        record="['reference_number' => \"CUST-{$n}\", 'customer_name' => \"Client {$n}\", 'customer_email' => \"c{$n}@example.test\", 'customer_phone' => '09171234567', 'event_date' => '2026-12-20', 'servings_count' => 30, 'tiers_count' => 2, 'theme_description' => 'Pastel unicorn', 'status' => 'inquiry']",
        payload="['customer_name' => 'Rina', 'customer_email' => 'rina@example.test', 'customer_phone' => '09171234567', 'event_date' => '2026-12-24', 'servings_count' => '40', 'tiers_count' => '2', 'theme_description' => 'Christmas forest', 'status' => 'inquiry']",
        assert_db="['customer_name' => 'Rina', 'servings_count' => 40]",
        update_payload="['reference_number' => 'CUST-EDIT', 'customer_name' => 'Rina', 'customer_email' => 'rina@example.test', 'customer_phone' => '09171234567', 'event_date' => '2026-12-24', 'servings_count' => '50', 'tiers_count' => '3', 'theme_description' => 'Forest', 'quoted_price' => '4500', 'status' => 'quoted']",
        update_assert="['status' => 'quoted', 'quoted_price' => 4500]",
        search_record="['theme_description' => 'Dinosaur jungle']", search_keyword='dinosaur',
    ),
)

CATALOG = ('catalog', 'Catalog', 'store', 10)
CATEGORY_OPTIONS = "\\App\\Shared\\Queries\\OptionsQuery::for(\\App\\Domains\\Catalog\\Models\\ProductCategory::class)"
MONEY_ROW = dict(name='price_modifier', type='money', label='Price adjustment (₱)', width='10rem')

SPECS['product'] = dict(
    seq=13, domain='Catalog', model='Product', table='products', permission_group='Catalog',
    label='Product', labels='Products', icon='Croissant', menu_group=CATALOG, menu_icon='croissant', menu_sort=2,
    description='Everything on the menu: prices, stock, flavors, variations and photos.',
    create_description='Add something new to the menu.',
    title_field='name', subtitle_field='sku', toggle_field='is_active',
    search=['name', 'sku', 'barcode', 'slug', 'category.name'], search_placeholder='Name, SKU, barcode or category',
    default_sort=('created_at', 'desc'), eager=['category'],
    filters=[
        dict(name='category_id', label='Category', type='relation', options_prop='category_options', options=CATEGORY_OPTIONS),
        dict(name='is_active', label='Active', type='boolean'),
        dict(name='is_featured', label='Featured', type='boolean'),
        dict(name='stock', label='Stock', type='scope', options=[('low', 'Low or out of stock'), ('ok', 'In stock')],
             php="$filters->stock === 'low' ? $q->whereColumn('stock_qty', '<=', 'min_stock_qty') : $q->whereColumn('stock_qty', '>', 'min_stock_qty')"),
    ],
    fields=[
        dict(name='name', type='string', label='Name', required=True, placeholder='e.g. Classic Banana Bread Loaf'),
        dict(name='slug', type='slug', label='URL slug', required=True, unique=True, description='Leave empty to generate from the name.'),
        dict(name='category_id', type='relation', label='Category', required=True,
             relation=dict(table='product_categories', soft=True, display='category_name', expr='$m->category?->name', options=CATEGORY_OPTIONS, options_prop='category_options')),
        dict(name='sku', type='code', label='SKU', required=True, unique=True, max=50),
        dict(name='barcode', type='code', label='Barcode', max=64, description='Scanned at the POS.'),
        dict(name='short_description', type='text', label='Short description', max=500),
        dict(name='description', type='richtext', label='Full description'),
        dict(name='tags', type='tags', label='Tags', relation='tags', model='App\\Domains\\Catalog\\Models\\Tag'),
        dict(name='image_path', type='image', label='Main photo', section='Photos'),
        dict(name='gallery', type='images', label='Gallery', max=12, section='Photos'),
        dict(name='price', type='money', label='Price', required=True, min=0, section='Pricing & stock'),
        dict(name='sale_price', type='money', label='Sale price', min=0, section='Pricing & stock', rules=["'lt:price'"],
             description='Leave empty when not on sale.'),
        dict(name='sale_ends_at', type='datetime', label='Sale ends', section='Pricing & stock'),
        dict(name='stock_qty', type='int', label='In stock', required=True, default=50, min=0, section='Pricing & stock'),
        dict(name='min_stock_qty', type='int', label='Low-stock level', required=True, default=10, min=0, section='Pricing & stock'),
        dict(name='prep_time_minutes', type='int', label='Prep time (minutes)', required=True, default=30, min=0, section='Pricing & stock'),
        dict(name='variation_type', type='select', label='Variation type', required=True, default='none', section='Options',
             options=[('none', 'No variations'), ('assorted', 'Assorted (multi-flavor box)'), ('weight', 'Weight / grams'), ('pieces', 'Pieces'),
                      ('size', 'Size'), ('packaging', 'Packaging'), ('custom', 'Custom')]),
        dict(name='variations', type='json_list', label='Variations', section='Options', add_label='Add variation', max=30,
             columns=[dict(name='label', type='string', label='Label', required=True, placeholder='e.g. 500g / Box of 6', width='2fr'), MONEY_ROW]),
        dict(name='is_assorted', type='bool', label='Assorted box', section='Options', yes='Customers pick flavors', no='Single flavor'),
        dict(name='max_flavors', type='int', label='Max flavors per box', min=1, max_value=24, section='Options'),
        dict(name='flavor', type='string', label='Default flavor', section='Options', max=100),
        dict(name='flavors', type='json_list', label='Flavor choices', section='Options', add_label='Add flavor', max=30,
             columns=[dict(name='name', type='string', label='Flavor', required=True, placeholder='e.g. Ube, Dark chocolate', width='2fr'), MONEY_ROW]),
        dict(name='is_featured', type='bool', label='Featured', section='Visibility', yes='On the homepage', no='Not featured'),
        dict(name='is_best_seller', type='bool', label='Best seller (manual)', section='Visibility', raw=True, yes='Always shown as best seller', no='Automatic (by sales)'),
        dict(name='is_new_arrival', type='bool', label='New arrival (manual)', section='Visibility', raw=True, yes='Always shown as new', no='Automatic (first 30 days)'),
        dict(name='is_seasonal', type='bool', label='Seasonal', section='Visibility'),
        dict(name='is_limited', type='bool', label='Limited edition', section='Visibility'),
        dict(name='is_active', type='bool', label='Active', default=True, section='Visibility', yes='Sold on the storefront', no='Hidden'),
        dict(name='seo_title', type='string', label='SEO title', max=70, section='Visibility'),
        dict(name='seo_description', type='text', label='SEO description', max=160, section='Visibility'),
    ],
    messages={'sale_price.lt': 'The sale price must be lower than the regular price.'},
    section_icons={'Details': 'Croissant', 'Photos': 'Images', 'Pricing & stock': 'Tag', 'Options': 'Layers', 'Visibility': 'Eye'},
    section_descriptions={'Details': 'Name, category and description.', 'Photos': 'Main photo and gallery.', 'Pricing & stock': 'Price, sale and stock levels.',
                          'Options': 'Variations and flavor choices customers pick from.', 'Visibility': 'Badges and where it appears.'},
    resource_extra=[
        ('is_low_stock', 'bool', '$m->stock_qty <= $m->min_stock_qty', 'boolean'),
        ('is_on_sale', 'bool', '(bool) $m->is_on_sale', 'boolean'),
        ('has_orders', 'bool', '$m->orderItems()->exists()', 'boolean'),
    ],
    edit_load="->load(['category', 'tags'])",
    grid=[
        ('name', 'Product', 'title'),
        ('category_name', 'Category', 'relation', {'priority': 2, 'sortable': True, 'sort_field': 'category.name'}),
        ('price', 'Price', 'money'),
        ('sale_price', 'Sale', 'money', {'priority': 3}),
        ('stock_qty', 'Stock', 'qty', {'low_when': 'row.is_low_stock'}),
        ('is_featured', 'Featured', 'bool', {'priority': 3}),
        ('is_active', 'Active', 'toggle'),
    ],
    summary=[
        ('total', 'Products', 'Product::query()->count()', 'On the menu', None, 'Croissant', 'primary'),
        ('active', 'Active', "Product::query()->where('is_active', true)->count()", 'Sold on the storefront', None, 'Eye', 'success'),
        ('low', 'Low stock', "Product::query()->where('is_active', true)->whereColumn('stock_qty', '<=', 'min_stock_qty')->count()", 'Active products at or below their level', None, 'AlertTriangle', 'destructive'),
        ('on_sale', 'On sale', "Product::query()->whereNotNull('sale_price')->where(fn ($q) => $q->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>', now()))->count()", 'With an active sale price', None, 'BadgePercent', 'warning'),
    ],
    delete_guard=dict(when='$product->orderItems()->exists()',
                      message="__(':name appears in past orders. Turn it off instead of deleting it.', ['name' => $product->name])"),
    delete_hidden='row.has_orders',
    edit_description="`${record.sku} · ${record.category_name ?? 'No category'}`",
    test=dict(
        record_setup="        $category = \\App\\Domains\\Catalog\\Models\\ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);\n",
        record="['category_id' => $category->id, 'sku' => \"SKU-{$n}\", 'name' => \"Product {$n}\", 'slug' => \"product-{$n}\", 'price' => 250, 'stock_qty' => 20, 'min_stock_qty' => 5]",
        setup="        $category = \\App\\Domains\\Catalog\\Models\\ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);\n",
        payload="['name' => 'Ube Cheese Loaf', 'slug' => '', 'category_id' => $category->id, 'sku' => 'ube-01', 'price' => '380', 'stock_qty' => '12', 'min_stock_qty' => '4', 'prep_time_minutes' => '45', 'variation_type' => 'size', 'variations' => [['label' => 'Mini', 'price_modifier' => '-100'], ['label' => '', 'price_modifier' => '']], 'tags' => 'bestseller, ube', 'is_active' => '1']",
        assert_db="['slug' => 'ube-cheese-loaf', 'sku' => 'UBE-01', 'variation_type' => 'size']",
        update_payload="['name' => 'Ube Loaf', 'slug' => 'ube-loaf', 'category_id' => $category->id, 'sku' => 'UBE-01', 'price' => '400', 'sale_price' => '350', 'stock_qty' => '8', 'min_stock_qty' => '4', 'prep_time_minutes' => '45', 'variation_type' => 'none', 'flavors' => [['name' => 'Classic', 'price_modifier' => '0']], 'tags' => 'ube', 'is_active' => '1']",
        update_assert="['slug' => 'ube-loaf', 'sale_price' => 350]",
        search_record="['name' => 'Mocha Brownies']", search_keyword='mocha',
        guarded="        $order = \\App\\Domains\\Sales\\Models\\Order::query()->create(['order_number' => 'ORD-1', 'tracking_token' => 'tok', 'customer_name' => 'A', 'customer_email' => 'a@a.test', 'customer_phone' => '09170000000', 'fulfillment_type' => 'pickup', 'subtotal' => 250, 'total' => 250, 'payment_method' => 'gcash', 'status' => 'completed']);\n        $order->items()->create(['product_id' => $record->id, 'product_name' => $record->name, 'product_sku' => $record->sku, 'unit_price' => 250, 'qty' => 1, 'subtotal' => 250]);",
    ),
)

INGREDIENT_OPTIONS = "\\App\\Shared\\Queries\\OptionsQuery::for(\\App\\Domains\\Inventory\\Models\\Ingredient::class, 'name', null, 'unit')"
PACKAGING_OPTIONS = "\\App\\Shared\\Queries\\OptionsQuery::for(\\App\\Domains\\Inventory\\Models\\PackagingMaterial::class, 'name', fn ($q) => $q->where('is_active', true), 'unit')"
PCT = lambda name, label, default: dict(name=name, type='decimal', label=label, required=True, default=default, min=0, max_value=1000, step='0.01', section='Pricing')

SPECS['recipe'] = dict(
    seq=14, domain='Costing', model='Recipe', table='recipes', permission_group='Catalog',
    label='Recipe', labels='Recipes', icon='ChefHat', menu_group=CATALOG, menu_icon='chef-hat', menu_sort=3,
    description='Bills of materials per product — drives costing, production and stock deduction.',
    create_description='List what one batch needs. Costs update from current ingredient prices.',
    title_field='name', subtitle_field='product_name',
    search=['name', 'product.name'], search_placeholder='Recipe or product', default_sort=('name', 'asc'),
    eager=['product', 'recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'],
    filters=[dict(name='product_id', label='Product', type='relation', options_prop='product_options', options=PRODUCT_OPTIONS)],
    fields=[
        dict(name='name', type='string', label='Recipe name', required=True),
        dict(name='product_id', type='relation', label='Product', required=True, relation=PRODUCT_REL),
        dict(name='yield_qty', type='int', label='Yield per batch', required=True, default=1, min=1, description='Pieces one batch makes.'),
        dict(name='prep_time_minutes', type='int', label='Prep time (min)', required=True, default=20, min=0),
        dict(name='baking_time_minutes', type='int', label='Baking time (min)', required=True, default=40, min=0),
        dict(name='instructions', type='richtext', label='Method'),
        dict(name='ingredients', type='items', label='Ingredients', relation='recipeIngredients', section='Ingredients', add_label='Add ingredient',
             columns=[dict(name='ingredient_id', type='relation', label='Ingredient', required=True, table='ingredients', options_prop='ingredient_options', options=INGREDIENT_OPTIONS, width='2fr'),
                      dict(name='qty_required', type='decimal', label='Quantity', required=True, min=0, width='8rem'),
                      dict(name='unit', type='select', label='Unit', required=True, options=UNITS, width='9rem')],
             row_extra=[('line_cost', '(float) $r->line_cost', 'number'), ('ingredient_name', '$r->ingredient?->name', 'string | null')]),
        dict(name='packaging', type='items', label='Packaging', relation='recipePackagings', section='Packaging', add_label='Add packaging',
             columns=[dict(name='packaging_material_id', type='relation', label='Item', table='packaging_materials', options_prop='packaging_options', options=PACKAGING_OPTIONS, width='2fr'),
                      dict(name='name', type='string', label='Label', required=True, width='2fr'),
                      dict(name='unit', type='string', label='Unit', required=True, width='6rem', placeholder='pcs'),
                      dict(name='package_qty', type='decimal', label='Pack qty', required=True, min=0, width='7rem'),
                      dict(name='package_cost', type='money', label='Pack cost', required=True, min=0, width='8rem'),
                      dict(name='qty_used', type='decimal', label='Used', required=True, min=0, width='7rem')]),
        PCT('overhead_pct', 'Overhead %', 40), PCT('markup_pct', 'Markup %', 66), PCT('labor_pct', 'Labor %', 60),
    ],
    section_icons={'Details': 'ChefHat', 'Ingredients': 'Wheat', 'Packaging': 'PackageOpen', 'Pricing': 'Calculator'},
    section_descriptions={'Ingredients': 'Quantities for one batch; units are converted to each ingredient’s stock unit.',
                          'Pricing': 'Overhead on raw materials, then markup and labor on total cost (the bakery’s costing sheet).'},
    resource_extra=[
        ('ingredient_cost', 'float', '(float) $m->total_ingredient_cost', 'number'),
        ('packaging_cost', 'float', '(float) $m->total_packaging_cost', 'number'),
        ('total_cost', 'float', '(float) $m->total_cost', 'number'),
        ('unit_cost', 'float', '(float) $m->unit_cost', 'number'),
        ('unit_selling_price', 'float', '(float) $m->unit_selling_price', 'number'),
        ('gross_margin', 'float', '(float) $m->gross_margin_percentage', 'number'),
        ('product_price', '?float', '$m->product ? (float) $m->product->price : null', 'number | null'),
    ],
    edit_load="->load(['product', 'recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'])",
    grid=[
        ('name', 'Recipe', 'title'),
        ('yield_qty', 'Yield', 'number', {'priority': 2}),
        ('unit_cost', 'Unit cost', 'money', {'sortable': False}),
        ('unit_selling_price', 'Suggested price', 'money', {'sortable': False}),
        ('product_price', 'Current price', 'money', {'sortable': False, 'priority': 2}),
    ],
    summary=[
        ('total', 'Recipes', 'Recipe::query()->count()', 'Costed products', None, 'ChefHat', 'primary'),
        ('without', 'Products without a recipe', "\\App\\Domains\\Catalog\\Models\\Product::query()->doesntHave('recipe')->count()", 'Cannot be costed or produced', None, 'AlertTriangle', 'warning'),
    ],
    transitions=[dict(
        name='apply_price', label='Use suggested price', icon='BadgeCheck', confirm='Update the product price?',
        confirm_description='Sets the product’s selling price to this recipe’s suggested unit price.',
        php="            $recipe->product?->update(['price' => round((float) $recipe->unit_selling_price, 2)]);",
        message="__(':product now sells for ₱:price.', ['product' => $recipe->product?->name, 'price' => number_format((float) $recipe->unit_selling_price, 2)])",
    )],
    delete_guard=dict(when='$recipe->productionBatches()->exists()',
                      message="__(':name has production batches and cannot be deleted.', ['name' => $recipe->name])"),
    edit_description="`Unit cost ₱${record.unit_cost.toFixed(2)} · suggested ₱${record.unit_selling_price.toFixed(2)} · margin ${record.gross_margin.toFixed(1)}%`",
    test=dict(
        record_setup="""        $category = \\App\\Domains\\Catalog\\Models\\ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);
        $product = \\App\\Domains\\Catalog\\Models\\Product::query()->create(['category_id' => $category->id, 'sku' => "P-{$n}", 'name' => "Loaf {$n}", 'slug' => "loaf-{$n}", 'price' => 100]);
""",
        record="['product_id' => $product->id, 'name' => \"Recipe {$n}\", 'yield_qty' => 10]",
        setup="""        $category = \\App\\Domains\\Catalog\\Models\\ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);
        $product = \\App\\Domains\\Catalog\\Models\\Product::query()->create(['category_id' => $category->id, 'sku' => 'P-R', 'name' => 'Banana loaf', 'slug' => 'banana-loaf', 'price' => 300]);
        $flour = \\App\\Domains\\Inventory\\Models\\Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 80]);
""",
        payload="['name' => 'Banana loaf (batch)', 'product_id' => $product->id, 'yield_qty' => '8', 'prep_time_minutes' => '20', 'baking_time_minutes' => '55', 'overhead_pct' => '40', 'markup_pct' => '66', 'labor_pct' => '60', 'ingredients' => [['ingredient_id' => (string) $flour->id, 'qty_required' => '1', 'unit' => 'kg']], 'packaging' => [['packaging_material_id' => '', 'name' => 'Loaf box', 'unit' => 'pcs', 'package_qty' => '50', 'package_cost' => '500', 'qty_used' => '8']]]",
        assert_db="['name' => 'Banana loaf (batch)', 'yield_qty' => 8]",
        update_payload="['name' => 'Banana loaf v2', 'product_id' => $product->id, 'yield_qty' => '10', 'prep_time_minutes' => '20', 'baking_time_minutes' => '55', 'overhead_pct' => '40', 'markup_pct' => '50', 'labor_pct' => '60', 'ingredients' => [['ingredient_id' => (string) $flour->id, 'qty_required' => '500', 'unit' => 'grams']]]",
        update_assert="['name' => 'Banana loaf v2', 'markup_pct' => 50]",
        search_record="['name' => 'Ube halaya swirl']", search_keyword='halaya',
    ),
)

SPECS['product_costing'] = dict(
    seq=15, domain='Costing', model='ProductCosting', table='product_costings', permission_group='Catalog',
    label='Product costing', labels='Product costing', icon='Calculator', menu_group=CATALOG, menu_icon='calculator', menu_sort=4,
    plural='product-costings',
    description='Costing sheets: ingredients and packaging per yield, with overhead, markup and labor.',
    create_description='Build a costing sheet — link a product or cost something new.',
    title_field='name', subtitle_field='yield_unit',
    search=['name', 'product.name', 'yield_unit'], search_placeholder='Sheet or product', default_sort=('created_at', 'desc'),
    eager=['product', 'costingItems.ingredient', 'costingItems.packagingMaterial'],
    fields=[
        dict(name='name', type='string', label='Sheet name', description='Defaults to the product name.'),
        dict(name='product_id', type='relation', label='Product', relation=PRODUCT_REL),
        dict(name='yield_qty', type='decimal', label='Yield', required=True, default=1, min=0.001),
        dict(name='yield_unit', type='string', label='Yield unit', required=True, default='tub (70g)', max=50),
        dict(name='items', type='items', label='Cost lines', relation='costingItems', section='Cost lines', add_label='Add line',
             columns=[dict(name='group', type='select', label='Group', required=True, options=[('ingredient', 'Ingredient'), ('packaging', 'Packaging')], width='8rem'),
                      dict(name='ingredient_id', type='relation', label='Ingredient', table='ingredients', options_prop='ingredient_options', options=INGREDIENT_OPTIONS, width='2fr'),
                      dict(name='packaging_material_id', type='relation', label='Packaging', table='packaging_materials', options_prop='packaging_options', options=PACKAGING_OPTIONS, width='2fr'),
                      dict(name='name', type='string', label='Label', width='2fr'),
                      dict(name='unit', type='string', label='Unit', required=True, width='5rem'),
                      dict(name='package_amount', type='decimal', label='Pack size', required=True, min=0, width='7rem'),
                      dict(name='package_price', type='money', label='Pack price', required=True, min=0, width='7rem'),
                      dict(name='qty_used', type='decimal', label='Used', required=True, min=0, width='6rem')],
             row_extra=[('line_cost', '(float) $r->line_cost', 'number')]),
        PCT('overhead_pct', 'Overhead %', 40), PCT('markup_pct', 'Markup %', 66), PCT('labor_pct', 'Labor %', 60),
    ],
    section_icons={'Details': 'Calculator', 'Cost lines': 'ListOrdered', 'Pricing': 'Percent'},
    resource_extra=[
        ('raw_cost', 'float', '(float) $m->raw_cost', 'number'),
        ('total_cost', 'float', '(float) $m->total_cost', 'number'),
        ('selling_price', 'float', '(float) $m->selling_price', 'number'),
        ('price_per_piece', 'float', '(float) $m->price_per_piece', 'number'),
    ],
    edit_load="->load(['product', 'costingItems.ingredient', 'costingItems.packagingMaterial'])",
    grid=[
        ('name', 'Costing sheet', 'title'),
        ('total_cost', 'Total cost', 'money', {'sortable': False}),
        ('selling_price', 'Selling price', 'money', {'sortable': False}),
        ('price_per_piece', 'Per piece', 'money', {'sortable': False, 'priority': 2}),
        ('updated_at', 'Updated', 'date', {'priority': 3}),
    ],
    summary=[('total', 'Costing sheets', 'ProductCosting::query()->count()', 'Saved sheets', None, 'Calculator', 'primary')],
    edit_description="`Cost ₱${record.total_cost.toFixed(2)} · sells ₱${record.selling_price.toFixed(2)} · ₱${record.price_per_piece.toFixed(2)} each`",
    test=dict(
        record="['name' => \"Sheet {$n}\", 'yield_qty' => 12, 'yield_unit' => 'pcs']",
        payload="['name' => 'Leche flan tubs', 'yield_qty' => '20', 'yield_unit' => 'tub (70g)', 'overhead_pct' => '40', 'markup_pct' => '66', 'labor_pct' => '60', 'items' => [['group' => 'ingredient', 'name' => 'Eggs', 'unit' => 'pcs', 'package_amount' => '30', 'package_price' => '270', 'qty_used' => '12']]]",
        assert_db="['name' => 'Leche flan tubs', 'yield_unit' => 'tub (70g)']",
        update_payload="['name' => 'Leche flan', 'yield_qty' => '24', 'yield_unit' => 'tub', 'overhead_pct' => '30', 'markup_pct' => '60', 'labor_pct' => '50', 'items' => []]",
        update_assert="['name' => 'Leche flan', 'overhead_pct' => 30]",
        search_record="['name' => 'Cassava cake']", search_keyword='cassava',
    ),
)

LINE_TOTALS = "            $m->update(['subtotal' => round((float) $m->items()->sum('subtotal'), 2), 'total' => round((float) $m->items()->sum('subtotal') + (float) $m->tax, 2)]);"

SPECS['quotation'] = dict(
    seq=16, domain='Purchasing', model='Quotation', table='quotations', permission_group='Production & Purchasing',
    label='Quotation', labels='Quotations', icon='FileSpreadsheet', menu_group=PRODUCTION, menu_icon='file-spreadsheet', menu_sort=2,
    description='Supplier quotes you can compare and convert into purchase orders.',
    create_description='Record a supplier quote. Convert it to a PO once accepted.',
    title_field='quotation_number', subtitle_field='supplier_name', status_field='status',
    search=['quotation_number', 'client_name', 'notes', 'supplier.name'], search_placeholder='Number, supplier or notes', default_sort=('created_at', 'desc'),
    eager=['supplier', 'items.ingredient'],
    filters=[dict(name='status', label='Status', type='select'),
             dict(name='supplier_id', label='Supplier', type='relation', options_prop='supplier_options', options=SUPPLIER_REL['options'])],
    fields=[
        dict(name='quotation_number', type='code', label='Quotation number', unique=True, max=40, keep_on_update=True, description='Leave empty to generate.'),
        dict(name='supplier_id', type='relation', label='Supplier', relation=SUPPLIER_REL),
        dict(name='client_name', type='string', label='Contact / client name'),
        dict(name='status', type='select', label='Status', required=True, default='draft',
             options=[('draft', 'Draft'), ('sent', 'Sent'), ('accepted', 'Accepted'), ('rejected', 'Rejected'), ('converted_to_po', 'Converted to PO')]),
        dict(name='quotation_date', type='date', label='Quote date'),
        dict(name='valid_until', type='date', label='Valid until', after='quotation_date'),
        dict(name='items', type='items', label='Items', relation='items', section='Items', add_label='Add item',
             columns=[dict(name='ingredient_id', type='relation', label='Ingredient', table='ingredients', options_prop='ingredient_options', options=INGREDIENT_OPTIONS, width='2fr'),
                      dict(name='item_description', type='string', label='Description', width='2fr'),
                      dict(name='qty', type='decimal', label='Qty', required=True, min=0, width='7rem'),
                      dict(name='unit', type='string', label='Unit', required=True, width='6rem'),
                      dict(name='unit_price', type='money', label='Unit price', required=True, min=0, width='8rem')],
             computed={'subtotal': "round((float) ($row['qty'] ?? 0) * (float) ($row['unit_price'] ?? 0), 2)"},
             summary="(row) => `₱${(Number(row.qty || 0) * Number(row.unit_price || 0)).toFixed(2)}`"),
        dict(name='tax', type='money', label='Tax', required=True, default=0, min=0, section='Totals'),
        dict(name='notes', type='text', label='Notes', section='Totals'),
    ],
    after_save=LINE_TOTALS,
    section_icons={'Details': 'FileSpreadsheet', 'Items': 'ListOrdered', 'Totals': 'Receipt'},
    resource_extra=[('subtotal', 'float', '(float) $m->subtotal', 'number'), ('total', 'float', '(float) $m->total', 'number'),
                    ('is_converted', 'bool', "$m->status === 'converted_to_po'", 'boolean')],
    edit_load="->load(['supplier', 'items.ingredient'])",
    grid=[
        ('quotation_number', 'Quotation', 'title'),
        ('status', 'Status', 'status'),
        ('quotation_date', 'Date', 'date', {'priority': 2}),
        ('valid_until', 'Valid until', 'date', {'priority': 3}),
        ('total', 'Total', 'money'),
    ],
    summary=[
        ('open', 'Open quotes', "Quotation::query()->whereIn('status', ['draft', 'sent'])->count()", 'Draft or sent', None, 'FileSpreadsheet', 'warning'),
        ('accepted', 'Accepted', "Quotation::query()->where('status', 'accepted')->count()", 'Ready to convert', None, 'CheckCircle2', 'success'),
        ('converted', 'Converted', "Quotation::query()->where('status', 'converted_to_po')->count()", 'Turned into POs', None, 'ArrowRightCircle', 'primary'),
    ],
    transitions=[dict(
        name='convert', label='Convert to PO', icon='ArrowRightCircle', hidden='row.is_converted',
        confirm='Create a purchase order from this quotation?', confirm_description='A draft PO is created with the same supplier, items and totals.',
        uses='use App\\Domains\\Purchasing\\Models\\PurchaseOrder;\n',
        guard="""        if ($quotation->status === 'converted_to_po') {
            throw new DomainRuleViolation(__('This quotation was already converted.'));
        }
        if (! $quotation->supplier_id) {
            throw new DomainRuleViolation(__('Pick a supplier on the quotation before converting it.'));
        }
        if ($quotation->items()->count() === 0 || $quotation->items()->whereNull('ingredient_id')->exists()) {
            throw new DomainRuleViolation(__('Every item needs an ingredient before it can go on a purchase order.'));
        }

""",
        php="""            $po = PurchaseOrder::query()->create([
                'supplier_id' => $quotation->supplier_id,
                'quotation_id' => $quotation->id,
                'status' => PurchaseOrder::STATUS_DRAFT,
                'subtotal' => $quotation->subtotal,
                'tax' => $quotation->tax,
                'total' => $quotation->total,
                'notes' => "Generated from quotation {$quotation->quotation_number}",
            ]);
            foreach ($quotation->items as $item) {
                $po->items()->create([
                    'ingredient_id' => $item->ingredient_id,
                    'qty_ordered' => $item->qty,
                    'qty_received' => 0,
                    'unit_cost' => $item->unit_price,
                    'subtotal' => round((float) $item->qty * (float) $item->unit_price, 2),
                ]);
            }
            $quotation->update(['status' => 'converted_to_po']);""",
        message="__(':number converted to a purchase order.', ['number' => $quotation->quotation_number])",
    )],
    delete_guard=dict(when="$quotation->status === 'converted_to_po'", message="__('Converted quotations are linked to a purchase order and cannot be deleted.')"),
    delete_hidden='row.is_converted',
    edit_description="`${record.supplier_name ?? 'No supplier'} · ₱${record.total.toFixed(2)}`",
    test=dict(
        record="['quotation_number' => \"QT-{$n}\", 'status' => 'draft', 'tax' => 0]",
        setup="        $flour = \\App\\Domains\\Inventory\\Models\\Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg']);\n",
        payload="['status' => 'draft', 'tax' => '12', 'items' => [['ingredient_id' => (string) $flour->id, 'qty' => '10', 'unit' => 'kg', 'unit_price' => '55.5']]]",
        assert_db="['status' => 'draft', 'subtotal' => 555, 'total' => 567]",
        update_payload="['quotation_number' => 'QT-EDIT', 'status' => 'sent', 'tax' => '0', 'items' => [['ingredient_id' => (string) $flour->id, 'qty' => '2', 'unit' => 'kg', 'unit_price' => '50']]]",
        update_assert="['status' => 'sent', 'total' => 100]",
        search_record="['notes' => 'Holiday butter order']", search_keyword='holiday',
    ),
)

SPECS['purchase_order'] = dict(
    seq=17, domain='Purchasing', model='PurchaseOrder', table='purchase_orders', permission_group='Production & Purchasing',
    label='Purchase order', labels='Purchase orders', icon='ClipboardList', menu_group=PRODUCTION, menu_icon='clipboard-list', menu_sort=3,
    description='Orders to suppliers. Receiving a PO adds its items to ingredient stock.',
    create_panel=('PoPdfImport', 'po-pdf-import'),
    create_description='Order ingredients from a supplier. Stock changes only when the PO is received.',
    title_field='po_number', subtitle_field='supplier_name', status_field='status',
    search=['po_number', 'notes', 'delivery_receipt_no', 'sales_invoice_no', 'supplier.name'], search_placeholder='PO number, supplier, DR or SI', default_sort=('created_at', 'desc'),
    eager=['supplier', 'items.ingredient'],
    filters=[dict(name='status', label='Status', type='select'),
             dict(name='supplier_id', label='Supplier', type='relation', options_prop='supplier_options', options=SUPPLIER_REL['options'])],
    fields=[
        dict(name='po_number', type='code', label='PO number', unique=True, max=40, keep_on_update=True, description='Leave empty to generate.'),
        dict(name='supplier_id', type='relation', label='Supplier', required=True, relation=SUPPLIER_REL),
        dict(name='status', type='select', label='Status', required=True, default='draft', options=[('draft', 'Draft'), ('sent', 'Sent'), ('cancelled', 'Cancelled')],
             description='Use “Receive” to mark it received — that updates stock.'),
        dict(name='expected_delivery_date', type='date', label='Expected delivery'),
        dict(name='payment_terms', type='string', label='Payment terms', max=100),
        dict(name='items', type='items', label='Items', relation='items', required=True, section='Items', add_label='Add item',
             columns=[dict(name='ingredient_id', type='relation', label='Ingredient', required=True, table='ingredients', options_prop='ingredient_options', options=INGREDIENT_OPTIONS, width='2fr'),
                      dict(name='qty_ordered', type='decimal', label='Qty ordered', required=True, min=0.001, width='8rem'),
                      dict(name='unit_cost', type='money', label='Unit cost', required=True, min=0, width='8rem')],
             computed={'subtotal': "round((float) ($row['qty_ordered'] ?? 0) * (float) ($row['unit_cost'] ?? 0), 2)", 'qty_received': '0'},
             summary="(row) => `₱${(Number(row.qty_ordered || 0) * Number(row.unit_cost || 0)).toFixed(2)}`"),
        dict(name='tax', type='money', label='Tax', required=True, default=0, min=0, section='Totals & documents'),
        dict(name='delivery_receipt_no', type='string', label='Delivery receipt no.', max=100, section='Totals & documents'),
        dict(name='sales_invoice_no', type='string', label='Sales invoice no.', max=100, section='Totals & documents'),
        dict(name='notes', type='text', label='Notes', section='Totals & documents'),
    ],
    messages={'items.required': 'Add at least one item.'},
    after_save=LINE_TOTALS,
    section_icons={'Details': 'ClipboardList', 'Items': 'ListOrdered', 'Totals & documents': 'Receipt'},
    resource_extra=[('subtotal', 'float', '(float) $m->subtotal', 'number'), ('total', 'float', '(float) $m->total', 'number'),
                    ('received_at', '?string', '$m->received_at?->toIso8601String()', 'string | null'),
                    ('is_closed', 'bool', "in_array($m->status, ['received', 'cancelled'], true)", 'boolean')],
    edit_load="->load(['supplier', 'items.ingredient'])",
    grid=[
        ('po_number', 'PO', 'title'),
        ('status', 'Status', 'status'),
        ('expected_delivery_date', 'Expected', 'date', {'priority': 2}),
        ('received_at', 'Received', 'date', {'priority': 3, 'sortable': True}),
        ('total', 'Total', 'money'),
    ],
    summary=[
        ('open', 'Open POs', "PurchaseOrder::query()->whereIn('status', ['draft', 'sent'])->count()", 'Not received yet', None, 'ClipboardList', 'warning'),
        ('open_value', 'Open value', '$open', 'Total of open POs', "'₱'.number_format($open, 2)", 'Wallet', 'primary'),
        ('received_month', 'Received this month', "PurchaseOrder::query()->where('status', 'received')->where('received_at', '>=', now()->startOfMonth())->count()", 'Since the 1st', None, 'PackageCheck', 'success'),
    ],
    summary_pre="        $open = (float) PurchaseOrder::query()->whereIn('status', ['draft', 'sent'])->sum('total');\n\n",
    transitions=[dict(
        name='receive', label='Receive', icon='PackageCheck', hidden='row.is_closed',
        confirm='Mark this PO as received?', confirm_description='Every item is added to ingredient stock at the PO unit cost.',
        guard="        if (in_array($purchaseOrder->status, ['received', 'cancelled'], true)) {\n            throw new DomainRuleViolation(__('Only open purchase orders can be received.'));\n        }\n\n",
        uses='use App\\Services\\PurchasingService;\n',
        php='            app(PurchasingService::class)->receivePurchaseOrder($purchaseOrder, auth()->user());',
        message="__(':number received — stock updated.', ['number' => $purchaseOrder->po_number])",
    )],
    delete_guard=dict(when="$purchaseOrder->status === 'received'", message="__('Received purchase orders already moved stock and cannot be deleted.')"),
    delete_hidden="row.status === 'received'",
    edit_description="`${record.supplier_name ?? ''} · ₱${record.total.toFixed(2)}`",
    test=dict(
        record_setup="        $supplier = \\App\\Domains\\Inventory\\Models\\Supplier::query()->firstOrCreate(['email' => 'mill@example.test'], ['name' => 'Mill', 'contact_person' => 'A', 'phone' => '0917', 'address' => 'Cavite']);\n",
        record="['po_number' => \"PO-{$n}\", 'supplier_id' => $supplier->id, 'status' => 'draft', 'tax' => 0]",
        setup="""        $supplier = \\App\\Domains\\Inventory\\Models\\Supplier::query()->firstOrCreate(['email' => 'mill@example.test'], ['name' => 'Mill', 'contact_person' => 'A', 'phone' => '0917', 'address' => 'Cavite']);
        $flour = \\App\\Domains\\Inventory\\Models\\Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg']);
""",
        payload="['supplier_id' => $supplier->id, 'status' => 'draft', 'tax' => '0', 'items' => [['ingredient_id' => (string) $flour->id, 'qty_ordered' => '25', 'unit_cost' => '48']]]",
        assert_db="['supplier_id' => $supplier->id, 'total' => 1200]",
        update_payload="['po_number' => 'PO-EDIT', 'supplier_id' => $supplier->id, 'status' => 'sent', 'tax' => '0', 'items' => [['ingredient_id' => (string) $flour->id, 'qty_ordered' => '10', 'unit_cost' => '50']]]",
        update_assert="['status' => 'sent', 'total' => 500]",
        search_record="['notes' => 'Rush butter delivery']", search_keyword='rush',
    ),
)

PAY_COL = lambda name, label: dict(name=name, type='money', label=label, min=0, width='7rem')
ROW_NUM = lambda key: f"(float) ($row['{key}'] ?? 0)"
GROSS = ' + '.join(ROW_NUM(k) for k in ['basic_pay', 'overtime_pay', 'bonuses'])
DEDUCT = ' + '.join(ROW_NUM(k) for k in ['sss_deduction', 'philhealth_deduction', 'pagibig_deduction', 'withholding_tax'])

SPECS['payroll'] = dict(
    seq=18, domain='Employee', model='Payroll', table='payrolls', permission_group='HR & Payroll',
    label='Payroll', labels='Payroll', icon='Banknote', menu_group=HR, menu_icon='banknote', menu_sort=3, plural='payrolls',
    description='Pay runs per period, generated from attendance. Adjust lines, approve, then mark paid.',
    create_description='Pick the period — basic pay, overtime and statutory deductions are computed from attendance.',
    title_field='payroll_number', subtitle_field='status', status_field='status',
    search=['payroll_number', 'notes'], search_placeholder='Payroll number or notes', default_sort=('period_start', 'desc'),
    eager=['items.employee'], with_count=['items'],
    filters=[dict(name='status', label='Status', type='select')],
    fields=[
        dict(name='payroll_number', type='code', label='Payroll number', unique=True, max=40, keep_on_update=True, description='Generated automatically.'),
        dict(name='period_start', type='date', label='Period start', required=True),
        dict(name='period_end', type='date', label='Period end', required=True, after='period_start'),
        dict(name='status', type='select', label='Status', required=True, default='draft', options=[('draft', 'Draft'), ('approved', 'Approved')],
             description='Use “Mark paid” to close the run.'),
        dict(name='notes', type='text', label='Notes'),
        dict(name='items', type='items', label='Pay lines', relation='items', section='Pay lines', add_label='Add employee',
             columns=[dict(name='employee_id', type='relation', label='Employee', required=True, table='employees', options_prop='employee_options', options=EMPLOYEE_REL['options'], width='2fr'),
                      dict(name='basic_pay', type='money', label='Basic', required=True, min=0, width='7rem'),
                      PAY_COL('overtime_pay', 'Overtime'), PAY_COL('bonuses', 'Bonuses'), PAY_COL('sss_deduction', 'SSS'),
                      PAY_COL('philhealth_deduction', 'PhilHealth'), PAY_COL('pagibig_deduction', 'Pag-IBIG'), PAY_COL('withholding_tax', 'Tax')],
             computed={'gross_pay': f'round({GROSS}, 2)', 'total_deductions': f'round({DEDUCT}, 2)', 'net_pay': f'round(({GROSS}) - ({DEDUCT}), 2)'},
             row_extra=[('employee_name', '$r->employee?->full_name', 'string | null'), ('net_pay', '(float) $r->net_pay', 'number')],
             summary="(row) => { const n = (k: string) => Number(row[k] || 0); return `₱${(n('basic_pay') + n('overtime_pay') + n('bonuses') - n('sss_deduction') - n('philhealth_deduction') - n('pagibig_deduction') - n('withholding_tax')).toFixed(2)}`; }",
             description='Net pay = basic + overtime + bonuses − deductions. Totals are recalculated on save.'),
    ],
    create_override="""            $payroll = app(\\App\\Services\\PayrollService::class)->generatePayroll($data->periodStart, $data->periodEnd);
            $payroll->update(['notes' => $data->notes]);

            return $payroll;""",
    after_save="            $m->update(['total_gross' => round((float) $m->items()->sum('gross_pay'), 2), 'total_deductions' => round((float) $m->items()->sum('total_deductions'), 2), 'total_net' => round((float) $m->items()->sum('net_pay'), 2)]);",
    update_guard="        if ($payroll->status === 'paid') {\n            throw new DomainRuleViolation(__('Paid payroll is locked.'));\n        }\n\n",
    section_icons={'Details': 'Banknote', 'Pay lines': 'Users'},
    resource_extra=[('total_gross', 'float', '(float) $m->total_gross', 'number'), ('total_deductions', 'float', '(float) $m->total_deductions', 'number'),
                    ('total_net', 'float', '(float) $m->total_net', 'number'), ('items_count', 'int', '(int) ($m->items_count ?? $m->items()->count())', 'number'),
                    ('paid_at', '?string', '$m->paid_at?->toIso8601String()', 'string | null'), ('is_paid', 'bool', "$m->status === 'paid'", 'boolean')],
    edit_load="->load('items.employee')",
    grid=[
        ('payroll_number', 'Payroll', 'title'),
        ('period_start', 'From', 'date'),
        ('period_end', 'To', 'date'),
        ('items_count', 'Staff', 'count', {'priority': 2}),
        ('total_net', 'Net pay', 'money', {'sortable': True}),
        ('status', 'Status', 'status'),
    ],
    summary=[
        ('draft', 'Drafts', "Payroll::query()->where('status', 'draft')->count()", 'Not approved yet', None, 'PenLine', 'warning'),
        ('approved', 'Approved, unpaid', "Payroll::query()->where('status', 'approved')->count()", 'Waiting for payout', None, 'BadgeCheck', 'primary'),
        ('paid_ytd', 'Paid this year', '$paid', 'Net pay released since January', "'₱'.number_format($paid, 2)", 'Banknote', 'success'),
    ],
    summary_pre="        $paid = (float) Payroll::query()->where('status', 'paid')->whereYear('paid_at', now()->year)->sum('total_net');\n\n",
    transitions=[
        dict(name='approve', label='Approve', icon='BadgeCheck', hidden="row.status !== 'draft'",
             guard="        if ($payroll->status !== 'draft') {\n            throw new DomainRuleViolation(__('Only draft payroll can be approved.'));\n        }\n\n",
             php="            $payroll->update(['status' => 'approved']);", message="__(':number approved.', ['number' => $payroll->payroll_number])",
             test=dict(record="['status' => 'draft']", check="        $this->assertSame('approved', $record->refresh()->status);")),
        dict(name='mark_paid', label='Mark paid', icon='Banknote', hidden='row.is_paid', confirm='Mark this payroll as paid?',
             confirm_description='The pay run is locked after this.',
             guard="        if ($payroll->status === 'paid') {\n            throw new DomainRuleViolation(__('This payroll is already paid.'));\n        }\n\n",
             uses='use App\\Services\\PayrollService;\n',
             php='            app(PayrollService::class)->approveAndPay($payroll);', message="__(':number marked as paid.', ['number' => $payroll->payroll_number])",
             test=dict(record="['status' => 'approved']", check="        $this->assertSame('paid', $record->refresh()->status);\n        $this->assertNotNull($record->paid_at);")),
    ],
    delete_guard=dict(when="$payroll->status === 'paid'", message="__('Paid payroll cannot be deleted.')"),
    delete_hidden='row.is_paid',
    edit_description="`${record.period_start} → ${record.period_end} · net ₱${record.total_net.toFixed(2)}`",
    test=dict(
        record="['payroll_number' => \"PAY-{$n}\", 'period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft']",
        setup="        $employee = \\App\\Domains\\Employee\\Models\\Employee::query()->create(['first_name' => 'Ana', 'last_name' => 'Lim', 'email' => 'ana@example.test', 'hired_at' => '2026-01-01', 'basic_monthly_salary' => 20000, 'is_active' => true]);\n",
        payload="['period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft']",
        assert_db="['status' => 'draft', 'total_net' => 9200]",
        update_payload="['period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'approved', 'items' => [['employee_id' => (string) $employee->id, 'basic_pay' => '10000', 'overtime_pay' => '500', 'bonuses' => '0', 'sss_deduction' => '450', 'philhealth_deduction' => '250', 'pagibig_deduction' => '100', 'withholding_tax' => '0']]]",
        update_assert="['status' => 'approved', 'total_gross' => 10500, 'total_deductions' => 800, 'total_net' => 9700]",
        search_record="['notes' => '13th month']", search_keyword='13th',
    ),
)

ORDER_STATUS = [('pending', 'Pending'), ('confirmed', 'Confirmed'), ('preparing', 'Preparing'), ('packaging', 'Packaging'),
                ('out_for_delivery', 'Out for delivery'), ('ready_for_pickup', 'Ready for pickup'), ('completed', 'Completed'),
                ('cancelled', 'Cancelled'), ('refunded', 'Refunded'), ('archived', 'Archived')]
PAYMENT_STATUS = [('pending', 'Pending'), ('awaiting_payment', 'Awaiting payment'), ('paid', 'Paid'), ('failed', 'Failed'), ('refunded', 'Refunded')]
ORDER_ROW = ("Array<{ id: number; product_name: string; product_sku: string; qty: number; unit_price: number; subtotal: number; "
             "options: Record<string, unknown> | null }>")
HISTORY_ROW = 'Array<{ id: number; from_status: string | null; to_status: string; comment: string | null; changed_by: string | null; created_at: string | null }>'

SPECS['order'] = dict(
    seq=19, domain='Sales', model='Order', table='orders', permission_group='Orders & Sales',
    label='Order', labels='Orders', icon='ShoppingBag', menu_group=SALES, menu_icon='shopping-bag', menu_sort=1,
    mode='edit-only', ops=['view', 'edit'],
    description='Storefront and POS orders. Move them through the kitchen and out the door.',
    title_field='order_number', subtitle_field='customer_name', status_field=None,
    search=['order_number', 'customer_name', 'customer_email', 'customer_phone', 'payment_reference'],
    search_placeholder='Order no., customer, email, phone or payment ref.', default_sort=('created_at', 'desc'),
    with_count=['items'],
    filters=[dict(name='status', label='Status', type='select', options=ORDER_STATUS),
             dict(name='payment_status', label='Payment', type='select', options=PAYMENT_STATUS),
             dict(name='fulfillment_type', label='Fulfillment', type='select', options=[('delivery', 'Delivery'), ('pickup', 'Pickup')]),
             dict(name='from', label='From', type='date_from', column='created_at'),
             dict(name='to', label='To', type='date_to', column='created_at')],
    fields=[
        dict(name='order_number', type='string', label='Order number', required=True, readonly=True),
        dict(name='customer_name', type='string', label='Customer name', required=True),
        dict(name='customer_email', type='email', label='Email', required=True),
        dict(name='customer_phone', type='phone', label='Phone', required=True, max=30, rules=['new \\App\\Rules\\PhilippinePhone']),
        dict(name='delivery_address', type='text', label='Delivery address', section='Delivery', max=1000),
        dict(name='city', type='string', label='City', section='Delivery', max=100),
        dict(name='postal_code', type='string', label='Postal code', section='Delivery', max=20),
        dict(name='scheduled_time', type='datetime', label='Scheduled for', section='Delivery'),
        dict(name='notes', type='text', label='Customer notes', section='Delivery'),
        dict(name='payment_status', type='select', label='Payment status', required=True, options=PAYMENT_STATUS, section='Payment'),
        dict(name='payment_reference', type='string', label='Payment reference', section='Payment', max=120,
             description='GCash / bank reference number, if paid manually.'),
    ],
    section_icons={'Details': 'User', 'Delivery': 'Truck', 'Payment': 'Wallet'},
    resource_extra=[
        ('status', 'string', '$m->status', 'string'),
        ('status_label', '?string', "self::option(" + "[" + ", ".join(f"'{v}' => '{l}'" for v, l in ORDER_STATUS) + "], $m->status)", 'string | null'),
        ('fulfillment_type', 'string', '$m->fulfillment_type', "'delivery' | 'pickup'"),
        ('delivery_mode', 'string', '$m->delivery_mode', 'string'),
        ('pooling_status', 'string', '$m->pooling_status', 'string'),
        ('payment_method', 'string', '$m->payment_method', 'string'),
        ('subtotal', 'float', '(float) $m->subtotal', 'number'),
        ('discount_amount', 'float', '(float) $m->discount_amount', 'number'),
        ('coupon_code', '?string', '$m->coupon_code', 'string | null'),
        ('delivery_fee', 'float', '(float) $m->delivery_fee', 'number'),
        ('total', 'float', '(float) $m->total', 'number'),
        ('paid_at', '?string', '$m->paid_at ? \\Illuminate\\Support\\Carbon::parse((string) $m->paid_at)->toIso8601String() : null', 'string | null'),
        ('items_count', 'int', '(int) ($m->items_count ?? $m->items()->count())', 'number'),
        ('tracking_token', 'string', '$m->tracking_token', 'string'),
        ('items', 'array', "$m->relationLoaded('items') ? $m->items->map(fn ($i) => ['id' => $i->id, 'product_name' => $i->product_name, 'product_sku' => $i->product_sku, 'qty' => (int) $i->qty, 'unit_price' => (float) $i->unit_price, 'subtotal' => (float) $i->subtotal, 'options' => $i->options])->values()->all() : []", ORDER_ROW),
        ('history', 'array', "$m->relationLoaded('statusHistories') ? $m->statusHistories->sortByDesc('id')->map(fn ($h) => ['id' => $h->id, 'from_status' => $h->from_status, 'to_status' => $h->to_status, 'comment' => $h->comment, 'changed_by' => $h->changedBy?->name, 'created_at' => $h->created_at?->toIso8601String()])->values()->all() : []", HISTORY_ROW),
    ],
    edit_load="->load(['items', 'statusHistories.changedBy'])",
    edit_panel=('OrderDetails', 'order-details'),
    grid=[
        ('order_number', 'Order', 'title'),
        ('fulfillment_type', 'Type', 'badge', {'label_field': 'fulfillment_type', 'priority': 2}),
        ('payment_status', 'Payment', 'status', {'label_field': 'payment_status_label'}),
        ('total', 'Total', 'money', {'sortable': True}),
        ('status', 'Status', 'status', {'sortable': True, 'label_field': 'status_label'}),
        ('created_at', 'Placed', 'datetime', {'priority': 2}),
    ],
    summary=[
        ('today', 'Orders today', "Order::query()->whereDate('created_at', today())->count()", 'All channels', None, 'ShoppingBag', 'primary'),
        ('open', 'In progress', "Order::query()->whereIn('status', ['pending', 'confirmed', 'preparing', 'packaging', 'out_for_delivery', 'ready_for_pickup'])->count()", 'Not completed yet', None, 'ChefHat', 'warning'),
        ('awaiting', 'Awaiting payment', "Order::query()->whereIn('payment_status', ['pending', 'awaiting_payment'])->whereNotIn('status', ['cancelled', 'refunded', 'archived'])->count()", 'Unpaid, still active', None, 'Clock', 'muted'),
        ('revenue_today', 'Sales today', '$today', 'Paid orders placed today', "'₱'.number_format($today, 2)", 'Wallet', 'success'),
    ],
    summary_pre="        $today = (float) Order::query()->whereDate('created_at', today())->where('payment_status', 'paid')->sum('total');\n\n",
    transitions=[dict(
        name='change_status', label='Change status', icon='ArrowRightLeft', hidden="row.status === 'archived'",
        prompt=dict(field='status', label='New status', type='select', rules="['required', 'in:" + ",".join(v for v, _ in ORDER_STATUS) + "']",
                    options="[" + ", ".join(f"{{ value: '{v}', label: '{l}' }}" for v, l in ORDER_STATUS) + "]",
                    description="(row) => `Currently ${row.status_label ?? row.status}. Cancelling or refunding returns ingredients to stock.`"),
        guard="        if ($value === $order->status) {\n            throw new DomainRuleViolation(__('The order is already :status.', ['status' => $value]));\n        }\n\n",
        php="""            try {
                $order->transitionTo((string) $value, __('Updated from the back office'), auth()->id());
            } catch (\\DomainException $e) {
                throw new DomainRuleViolation($e->getMessage());
            }""",
        message="__(':number is now :status.', ['number' => $order->order_number, 'status' => str_replace('_', ' ', $value)])",
        test=dict(record="['status' => 'pending']", data="['status' => 'confirmed']",
                  check="        $this->assertSame('confirmed', $record->refresh()->status);\n        $this->assertDatabaseHas('order_status_histories', ['order_id' => $record->id, 'from_status' => 'pending', 'to_status' => 'confirmed']);"),
    )],
    edit_description="`${record.items_count} items · ₱${record.total.toFixed(2)} · ${record.payment_method.toUpperCase()}`",
    preview_facts=[('Customer', 'data.customer_email'), ('Payment', 'data.payment_status')],
    test=dict(
        record="['order_number' => \"ORD-{$n}\", 'tracking_token' => \"tok-{$n}\", 'customer_name' => \"Customer {$n}\", 'customer_email' => \"c{$n}@example.test\", 'customer_phone' => '09171234567', 'fulfillment_type' => 'pickup', 'subtotal' => 300, 'total' => 300, 'payment_method' => 'gcash', 'payment_status' => 'paid', 'status' => 'pending']",
        update_payload="['customer_name' => 'Edited Name', 'customer_email' => 'e@example.test', 'customer_phone' => '09171234567', 'payment_status' => 'paid', 'payment_reference' => 'GC-123']",
        update_assert="['customer_name' => 'Edited Name', 'payment_reference' => 'GC-123']",
        search_record="['customer_name' => 'Imelda Santos']", search_keyword='imelda',
    ),
)

SPECS['delivery_pool'] = dict(
    seq=20, domain='Sales', model='DeliveryPool', table='delivery_pools', permission_group='Orders & Sales',
    label='Delivery pool', labels='Delivery pools', icon='Route', menu_group=SALES, menu_icon='route', menu_sort=3,
    description='Group nearby pooling orders, split one shipping fee, then settle so they can be prepared.',
    create_description='Group nearby pooling orders and set each order’s share of the shipping fee.',
    title_field='pool_code', subtitle_field='zone_name', status_field='status',
    search=['pool_code', 'zone_name', 'city', 'barangay', 'province'], search_placeholder='Pool code, zone or area', default_sort=('created_at', 'desc'),
    eager=['orders'], with_count=['orders'],
    filters=[dict(name='status', label='Status', type='select', options=[('open', 'Open'), ('building', 'Building'), ('settled', 'Settled'), ('dispatched', 'Dispatched')])],
    fields=[
        dict(name='pool_code', type='code', label='Pool code', unique=True, max=40, auto="'POOL-'.now()->format('Ymd').'-'.strtoupper(\\Illuminate\\Support\\Str::random(4))",
             description='Leave empty to generate.'),
        dict(name='zone_name', type='string', label='Zone name', placeholder='e.g. Imus – Anabu'),
        dict(name='region', type='string', label='Region', max=100),
        dict(name='province', type='string', label='Province', max=100),
        dict(name='city', type='string', label='City', max=100),
        dict(name='barangay', type='string', label='Barangay', max=100),
        dict(name='total_delivery_fee', type='money', label='Total delivery fee', required=True, default=0, min=0, section='Orders',
             description='Lalamove / rider fee for the whole trip.'),
        dict(name='status', type='select', label='Status', required=True, default='open', section='Orders',
             options=[('open', 'Open'), ('building', 'Building')], description='Use “Settle” to finalise the fees.'),
        dict(name='allocations', type='json_list', label='Orders in this pool', virtual=True, section='Orders', add_label='Add order',
             columns=[dict(name='order_id', type='relation', label='Order', required=True, table='orders', options_prop='order_options',
                           options='app(\\App\\Domains\\Sales\\Queries\\PoolableOrderQueries::class)->options(request()->route(\'deliveryPool\'))', width='3fr'),
                      dict(name='assigned_fee', type='money', label='Shipping share (₱)', required=True, min=0, width='10rem')],
             resource_expr="$m->orders->map(fn ($o) => ['order_id' => $o->id, 'assigned_fee' => (float) $o->delivery_fee])->values()->all()"),
        dict(name='notes', type='text', label='Notes', section='Orders'),
    ],
    update_guard="        if (in_array($deliveryPool->status, ['settled', 'dispatched'], true)) {\n            throw new DomainRuleViolation(__('Settled pools are locked.'));\n        }\n\n",
    after_save="""            $keep = collect($data->allocations)->pluck('order_id')->map(fn ($id) => (int) $id)->filter()->unique();
            \\App\\Domains\\Sales\\Models\\Order::query()->where('delivery_pool_id', $m->id)->whereNotIn('id', $keep)
                ->update(['delivery_pool_id' => null, 'pooling_status' => \\App\\Domains\\Sales\\Models\\Order::POOLING_AWAITING_ASSIGNMENT, 'delivery_fee' => 0]);
            foreach ($data->allocations as $row) {
                \\App\\Domains\\Sales\\Models\\Order::query()->whereKey((int) $row['order_id'])->where('delivery_mode', \\App\\Domains\\Sales\\Models\\Order::MODE_POOLING)
                    ->update(['delivery_pool_id' => $m->id, 'pooling_status' => \\App\\Domains\\Sales\\Models\\Order::POOLING_POOLED, 'delivery_fee' => round((float) ($row['assigned_fee'] ?? 0), 2)]);
            }""",
    section_icons={'Details': 'MapPin', 'Orders': 'Route'},
    resource_extra=[('orders_count', 'int', '(int) ($m->orders_count ?? $m->orders()->count())', 'number'),
                    ('allocated_fees', 'float', "(float) $m->orders->sum('delivery_fee')", 'number'),
                    ('shared_fee_per_order', 'float', '(float) $m->shared_fee_per_order', 'number'),
                    ('settled_at', '?string', '$m->settled_at ? \\Illuminate\\Support\\Carbon::parse((string) $m->settled_at)->toIso8601String() : null', 'string | null'),
                    ('is_locked', 'bool', "in_array($m->status, ['settled', 'dispatched'], true)", 'boolean')],
    edit_load="->load('orders')->loadCount('orders')",
    grid=[
        ('pool_code', 'Pool', 'title'),
        ('city', 'City', 'text', {'priority': 2}),
        ('orders_count', 'Orders', 'count'),
        ('total_delivery_fee', 'Trip fee', 'money'),
        ('status', 'Status', 'status'),
        ('created_at', 'Created', 'date', {'priority': 3}),
    ],
    summary=[
        ('waiting', 'Orders waiting for a pool', "\\App\\Domains\\Sales\\Models\\Order::query()->where('delivery_mode', 'pooling')->whereNull('delivery_pool_id')->whereNotIn('status', ['cancelled', 'refunded', 'archived'])->count()", 'Pooling orders not assigned yet', None, 'Hourglass', 'warning'),
        ('open', 'Open pools', "DeliveryPool::query()->whereIn('status', ['open', 'building'])->count()", 'Not settled', None, 'Route', 'primary'),
        ('settled', 'Settled', "DeliveryPool::query()->where('status', 'settled')->count()", 'Fees finalised', None, 'CheckCircle2', 'success'),
    ],
    transitions=[dict(
        name='settle', label='Settle pool', icon='HandCoins', hidden='row.is_locked',
        confirm='Settle this pool?', confirm_description='Each order’s total is recalculated with its shipping share and the orders move to Confirmed.',
        guard="""        if (in_array($deliveryPool->status, ['settled', 'dispatched'], true)) {
            throw new DomainRuleViolation(__('This pool is already settled.'));
        }
        if (! $deliveryPool->orders()->exists()) {
            throw new DomainRuleViolation(__('Add at least one order to the pool before settling it.'));
        }

""",
        uses='use App\\Domains\\Sales\\Models\\Order;\n',
        php="""            $orders = $deliveryPool->orders()->get();
            foreach ($orders as $order) {
                $fee = (float) $order->delivery_fee;
                $order->update([
                    'total' => max(0.0, round((float) $order->subtotal - (float) $order->discount_amount + $fee, 2)),
                    'pooling_status' => Order::POOLING_SETTLED,
                ]);
                if ($order->status === Order::STATUS_PENDING) {
                    $order->transitionTo(Order::STATUS_CONFIRMED, __('Delivery pool :code settled — shipping share ₱:fee.', ['code' => $deliveryPool->pool_code, 'fee' => number_format($fee, 2)]), auth()->id());
                }
            }
            $deliveryPool->update([
                'shared_fee_per_order' => round($orders->avg('delivery_fee') ?? 0, 2),
                'status' => 'settled',
                'settled_at' => now(),
            ]);""",
        message="__('Pool :code settled for :count orders.', ['code' => $deliveryPool->pool_code, 'count' => $deliveryPool->orders()->count()])",
    )],
    delete_guard=dict(when="$deliveryPool->orders()->exists()", message="__('Remove the orders from this pool before deleting it.')"),
    delete_hidden='row.orders_count > 0',
    edit_description="`${record.orders_count} orders · trip ₱${record.total_delivery_fee.toFixed(2)} · allocated ₱${record.allocated_fees.toFixed(2)}`",
    test=dict(
        record="['pool_code' => \"POOL-{$n}\", 'zone_name' => \"Zone {$n}\", 'total_delivery_fee' => 300, 'status' => 'open']",
        setup="        $order = \\App\\Domains\\Sales\\Models\\Order::query()->create(['order_number' => 'ORD-P1', 'tracking_token' => 'tp1', 'customer_name' => 'Pia', 'customer_email' => 'p@x.test', 'customer_phone' => '09171234567', 'fulfillment_type' => 'delivery', 'delivery_mode' => 'pooling', 'pooling_status' => 'awaiting_assignment', 'subtotal' => 500, 'total' => 500, 'payment_method' => 'gcash', 'payment_status' => 'paid', 'status' => 'pending']);\n",
        payload="['zone_name' => 'Imus', 'total_delivery_fee' => '200', 'status' => 'open', 'allocations' => [['order_id' => (string) $order->id, 'assigned_fee' => '80']]]",
        assert_db="['zone_name' => 'Imus', 'total_delivery_fee' => 200]",
        update_payload="['pool_code' => 'POOL-EDIT', 'zone_name' => 'Imus', 'total_delivery_fee' => '250', 'status' => 'building', 'allocations' => [['order_id' => (string) $order->id, 'assigned_fee' => '90']]]",
        update_assert="['pool_code' => 'POOL-EDIT', 'status' => 'building']",
        search_record="['zone_name' => 'Bacoor Molino']", search_keyword='molino',
    ),
)

SPECS['chat_escalation'] = dict(
    seq=21, domain='Content', model='ChatEscalation', table='chat_escalations', permission_group='Engagement & Content',
    label='Support escalation', labels='Support escalations', icon='MessagesSquare', menu_group=CONTENT, menu_icon='messages-square', menu_sort=4,
    plural='chat-escalations', mode='edit-only',
    description='AI-chat conversations customers asked to hand over to a person.',
    title_field='guest_name', subtitle_field='guest_email', status_field='status',
    search=['guest_name', 'guest_email', 'admin_notes', 'user.name', 'user.email'], search_placeholder='Customer name or email', default_sort=('updated_at', 'desc'),
    eager=['user'],
    filters=[dict(name='status', label='Status', type='select', options=[('open', 'Open'), ('in_progress', 'In progress'), ('resolved', 'Resolved')])],
    fields=[
        dict(name='guest_name', type='string', label='Customer name'),
        dict(name='guest_email', type='email', label='Customer email'),
        dict(name='status', type='select', label='Status', required=True, default='open', options=[('open', 'Open'), ('in_progress', 'In progress'), ('resolved', 'Resolved')]),
        dict(name='admin_notes', type='text', label='Internal notes', description='Only visible to staff.'),
    ],
    section_icons={'Details': 'MessagesSquare'},
    resource_extra=[
        ('customer_name', 'string', "$m->user?->name ?? $m->guest_name ?? 'Guest'", 'string'),
        ('messages', 'array', "array_values(array_map(fn ($msg) => ['role' => (string) ($msg['role'] ?? 'user'), 'content' => (string) ($msg['content'] ?? ''), 'time' => $msg['time'] ?? null], (array) ($m->conversation ?? [])))",
         "Array<{ role: 'user' | 'assistant' | 'admin'; content: string; time: string | null }>"),
        ('messages_count', 'int', 'count((array) ($m->conversation ?? []))', 'number'),
        ('last_message', '?string', "collect((array) ($m->conversation ?? []))->last()['content'] ?? null", 'string | null'),
    ],
    edit_panel=('ChatThread', 'chat-thread'),
    grid=[
        ('customer_name', 'Customer', 'title', {'subtitle': 'last_message', 'sortable': False}),
        ('messages_count', 'Messages', 'count', {'sortable': False}),
        ('status', 'Status', 'status'),
        ('updated_at', 'Last activity', 'datetime'),
    ],
    summary=[
        ('open', 'Open', "ChatEscalation::query()->where('status', 'open')->count()", 'Waiting for a reply', None, 'MessageCircleWarning', 'warning'),
        ('in_progress', 'In progress', "ChatEscalation::query()->where('status', 'in_progress')->count()", 'Being handled', None, 'MessagesSquare', 'primary'),
        ('resolved', 'Resolved', "ChatEscalation::query()->where('status', 'resolved')->count()", 'Closed conversations', None, 'CheckCircle2', 'success'),
    ],
    transitions=[
        dict(name='reply', label='Reply', icon='Reply',
             prompt=dict(field='message', label='Your reply', type='text', rules="['required', 'string', 'max:2000']"),
             php="""            $conversation = (array) ($chatEscalation->conversation ?? []);
            $conversation[] = ['role' => 'admin', 'content' => trim((string) $value), 'time' => now()->format('M j, g:i A')];
            $chatEscalation->update(['conversation' => $conversation, 'status' => 'in_progress']);""",
             message="__('Reply sent.')",
             test=dict(data="['message' => 'Hi! Your cake is on the way.']",
                       check="        $record->refresh();\n        $this->assertSame('in_progress', $record->status);\n        $conversation = $record->conversation;\n        $this->assertSame('admin', end($conversation)['role']);")),
        dict(name='resolve', label='Mark resolved', icon='CheckCircle2', hidden="row.status === 'resolved'",
             php="""            $conversation = (array) ($chatEscalation->conversation ?? []);
            $conversation[] = ['role' => 'admin', 'content' => __('This conversation was marked as resolved by our team.'), 'time' => now()->format('M j, g:i A')];
            $chatEscalation->update(['conversation' => $conversation, 'status' => 'resolved']);""",
             message="__('Conversation resolved.')",
             test=dict(check="        $this->assertSame('resolved', $record->refresh()->status);")),
        dict(name='reopen', label='Reopen', icon='RotateCcw', hidden="row.status !== 'resolved'",
             php="            $chatEscalation->update(['status' => 'open']);", message="__('Conversation reopened.')",
             test=dict(record="['status' => 'resolved']", check="        $this->assertSame('open', $record->refresh()->status);")),
    ],
    test=dict(
        record="['guest_name' => \"Guest {$n}\", 'guest_email' => \"g{$n}@example.test\", 'conversation' => [['role' => 'user', 'content' => 'Can I talk to a person?']], 'status' => 'open']",
        update_payload="['guest_name' => 'Guest', 'guest_email' => 'g@example.test', 'status' => 'in_progress', 'admin_notes' => 'Called back']",
        update_assert="['status' => 'in_progress', 'admin_notes' => 'Called back']",
        search_record="['guest_name' => 'Lorna Diaz']", search_keyword='lorna',
    ),
)

SYSTEM = ('system', 'System', 'settings', 70)
STAFF_SCOPE = "->whereHas('roles', fn ($r) => $r->where('name', '!=', 'customer'))"
CUSTOMER_SCOPE = "->whereHas('roles', fn ($r) => $r->where('name', 'customer'))"

SPECS['user'] = dict(
    seq=22, domain='User', model='User', table='users', permission_group='Access',
    thing='User', label='Staff account', labels='Staff accounts', icon='UserCog', menu_group=SYSTEM, menu_icon='user-cog', menu_sort=1,
    plural='users',
    description='People who sign in to the back office, and what their roles let them do.',
    create_description='Create a sign-in for a staff member and choose their roles.',
    title_field='name', subtitle_field='email',
    search=['name', 'email', 'phone'], search_placeholder='Name, email or phone', default_sort=('name', 'asc'),
    query_scope=STAFF_SCOPE, eager=['roles'],
    filters=[dict(name='role', label='Role', type='scope', options=[('super_admin', 'Super admin'), ('admin', 'Admin'), ('manager', 'Manager'), ('staff', 'Staff')],
                  php="$q->whereHas('roles', fn ($r) => $r->where('name', $filters->role))")],
    fields=[
        dict(name='name', type='string', label='Full name', required=True),
        dict(name='email', type='email', label='Email', required=True, unique=True),
        dict(name='phone', type='phone', label='Phone', max=30),
        dict(name='roles', type='multiselect', label='Roles', required=True, options_prop='role_options',
             options='\\App\\Domains\\Access\\Support\\AssignableRoles::options()', allowed='\\App\\Domains\\Access\\Support\\AssignableRoles::names()',
             resource_expr="$m->getRoleNames()->values()->all()", description='Permissions come from roles — manage them under Roles.'),
        dict(name='password', type='password', label='Password', section='Sign-in', keep_on_update=True,
             rules=["Rule::requiredIf(fn () => request()->route('user') === null)", "'confirmed'", '\\Illuminate\\Validation\\Rules\\Password::defaults()'],
             description='Leave empty to keep the current password.'),
        dict(name='password_confirmation', type='password', label='Confirm password', section='Sign-in', virtual=True),
    ],
    create_extra="\n            'email_verified_at' => now(),",
    after_save="            $m->syncRoles($data->roles);\n            app(\\Spatie\\Permission\\PermissionRegistrar::class)->forgetCachedPermissions();",
    update_guard="""        if ($user->hasRole('super_admin') && ! auth()->user()?->hasRole('super_admin')) {
            throw new DomainRuleViolation(__('Only a super admin can change a super admin account.'));
        }
        if ($user->is(auth()->user()) && ! in_array('super_admin', $data->roles, true) && $user->hasRole('super_admin')) {
            throw new DomainRuleViolation(__('You cannot remove your own super admin role.'));
        }

""",
    section_icons={'Details': 'UserCog', 'Sign-in': 'KeyRound'},
    resource_extra=[('email_verified', 'bool', '$m->hasVerifiedEmail()', 'boolean'),
                    ('roles_label', 'string', "$m->getRoleNames()->map(fn ($r) => ucwords(str_replace('_', ' ', $r)))->implode(', ')", 'string'),
                    ('is_self', 'bool', '$m->is(auth()->user())', 'boolean')],
    edit_load="->load('roles')",
    grid=[
        ('name', 'Name', 'title'),
        ('roles_label', 'Roles', 'text', {'sortable': False}),
        ('phone', 'Phone', 'text', {'priority': 3}),
        ('email_verified', 'Verified', 'bool', {'sortable': False, 'priority': 2}),
        ('created_at', 'Added', 'date', {'priority': 3}),
    ],
    summary=[
        ('staff', 'Staff accounts', "User::query()->whereHas('roles', fn ($r) => $r->where('name', '!=', 'customer'))->count()", 'Can sign in to the back office', None, 'UserCog', 'primary'),
        ('admins', 'Admins', "User::query()->whereHas('roles', fn ($r) => $r->whereIn('name', ['admin', 'super_admin']))->count()", 'Full access', None, 'ShieldCheck', 'warning'),
    ],
    delete_guard=dict(when="$user->is(auth()->user()) || ($user->hasRole('super_admin') && ! auth()->user()?->hasRole('super_admin'))",
                      message="__('You cannot delete your own account or a super admin account.')"),
    delete_hidden='row.is_self',
    edit_description="`${record.email} · ${record.roles_label}`",
    test=dict(
        record_setup="        \\Spatie\\Permission\\Models\\Role::findOrCreate('staff', 'web');\n",
        record="['name' => \"Staff {$n}\", 'email' => \"staff{$n}@example.test\", 'password' => 'Secret!123']",
        record_after="        $model->assignRole('staff');",
        setup="        \\Spatie\\Permission\\Models\\Role::findOrCreate('manager', 'web');\n        \\Spatie\\Permission\\Models\\Role::findOrCreate('staff', 'web');\n",
        payload="['name' => 'Mae Torres', 'email' => 'MAE@example.test', 'roles' => ['manager'], 'password' => 'Bread!2026x', 'password_confirmation' => 'Bread!2026x']",
        assert_db="['email' => 'mae@example.test', 'name' => 'Mae Torres']",
        update_payload="['name' => 'Mae T.', 'email' => 'mae@example.test', 'roles' => ['staff'], 'password' => '', 'password_confirmation' => '']",
        update_assert="['name' => 'Mae T.']",
        search_record="['name' => 'Rogelio Bautista']", search_keyword='rogelio',
    ),
)

SPECS['customer'] = dict(
    seq=23, domain='User', model='User', table='users', permission_group='Orders & Sales',
    thing='Customer', label='Customer', labels='Customers', icon='Users', menu_group=SALES, menu_icon='users', menu_sort=4,
    plural='customers', mode='edit-only', ops=['view', 'edit'],
    description='Storefront accounts — contact details and purchase history.',
    title_field='name', subtitle_field='email',
    search=['name', 'email', 'phone', 'city'], search_placeholder='Name, email, phone or city', default_sort=('created_at', 'desc'),
    query_scope=CUSTOMER_SCOPE, with_count=['orders'],
    fields=[
        dict(name='name', type='string', label='Full name', required=True),
        dict(name='email', type='email', label='Email', required=True, unique=True),
        dict(name='phone', type='phone', label='Phone', max=30),
        dict(name='street_address', type='string', label='Street address', section='Address'),
        dict(name='barangay', type='string', label='Barangay', section='Address', max=100),
        dict(name='city', type='string', label='City', section='Address', max=100),
        dict(name='province', type='string', label='Province', section='Address', max=100),
        dict(name='region', type='string', label='Region', section='Address', max=100),
    ],
    section_icons={'Details': 'User', 'Address': 'MapPin'},
    resource_extra=[('orders_count', 'int', '(int) ($m->orders_count ?? $m->orders()->count())', 'number'),
                    ('total_spent', 'float', "(float) $m->orders()->where('payment_status', 'paid')->sum('total')", 'number'),
                    ('email_verified', 'bool', '$m->hasVerifiedEmail()', 'boolean')],
    edit_load="->loadCount('orders')",
    grid=[
        ('name', 'Customer', 'title'),
        ('city', 'City', 'text', {'priority': 2}),
        ('orders_count', 'Orders', 'count'),
        ('total_spent', 'Spent', 'money', {'sortable': False}),
        ('email_verified', 'Verified', 'bool', {'sortable': False, 'priority': 3}),
        ('created_at', 'Joined', 'date', {'priority': 2}),
    ],
    summary=[
        ('total', 'Customers', "User::query()->whereHas('roles', fn ($r) => $r->where('name', 'customer'))->count()", 'Storefront accounts', None, 'Users', 'primary'),
        ('new_month', 'New this month', "User::query()->whereHas('roles', fn ($r) => $r->where('name', 'customer'))->where('created_at', '>=', now()->startOfMonth())->count()", 'Since the 1st', None, 'UserPlus', 'success'),
        ('repeat', 'Repeat buyers', "User::query()->whereHas('roles', fn ($r) => $r->where('name', 'customer'))->has('orders', '>=', 2)->count()", 'Two or more orders', None, 'Repeat', 'warning'),
    ],
    edit_description="`${record.orders_count} orders · ₱${record.total_spent.toFixed(2)} spent`",
    test=dict(
        record_setup="        \\Spatie\\Permission\\Models\\Role::findOrCreate('customer', 'web');\n",
        record="['name' => \"Customer {$n}\", 'email' => \"cust{$n}@example.test\", 'password' => 'Secret!123']",
        record_after="        $model->assignRole('customer');",
        update_payload="['name' => 'Edited', 'email' => 'edited@example.test', 'city' => 'Imus']",
        update_assert="['name' => 'Edited', 'city' => 'Imus']",
        search_record="['name' => 'Corazon Aquino']", search_keyword='corazon',
    ),
)

SPECS['role'] = dict(
    seq=24, domain='Access', model='Role', table='roles', permission_group='Access', no_soft_deletes=True,
    label='Role', labels='Roles', icon='ShieldCheck', menu_group=SYSTEM, menu_icon='shield-check', menu_sort=2,
    description='Bundles of permissions you assign to staff accounts.',
    create_description='Group the permissions a job needs, then assign the role to staff.',
    title_field='name', ops=['view', 'create', 'edit', 'delete'],
    search=['name'], search_placeholder='Role name', default_sort=('name', 'asc'),
    eager=['permissions'], with_count=['users', 'permissions'],
    fields=[
        dict(name='name', type='slug', label='Role name', required=True, unique=False, separator='_',
             rules=["Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($id)"], description='Lowercase, e.g. pastry_lead.'),
        dict(name='permissions', type='multiselect', label='Permissions', options_prop='permission_options',
             options='\\App\\Domains\\Access\\Support\\PermissionRegistry::options()', allowed='\\App\\Domains\\Access\\Support\\PermissionRegistry::names()',
             resource_expr="$m->permissions->pluck('name')->values()->all()", description='Super admins always have every permission.'),
    ],
    create_extra="\n            'guard_name' => 'web',",
    after_save="            $m->syncPermissions($data->permissions);\n            app(\\Spatie\\Permission\\PermissionRegistrar::class)->forgetCachedPermissions();",
    update_guard="        if ($role->isSystem() && $data->name !== $role->name) {\n            throw new DomainRuleViolation(__('Built-in roles cannot be renamed.'));\n        }\n        if ($role->name === 'super_admin') {\n            throw new DomainRuleViolation(__('The super admin role always has every permission and cannot be edited.'));\n        }\n\n",
    section_icons={'Details': 'ShieldCheck'},
    resource_extra=[('users_count', 'int', '(int) ($m->users_count ?? $m->users()->count())', 'number'),
                    ('permissions_count', 'int', '(int) ($m->permissions_count ?? $m->permissions()->count())', 'number'),
                    ('is_system', 'bool', '$m->isSystem()', 'boolean')],
    edit_load="->load('permissions')->loadCount(['users', 'permissions'])",
    grid=[
        ('name', 'Role', 'title'),
        ('permissions_count', 'Permissions', 'count'),
        ('users_count', 'Users', 'count'),
        ('is_system', 'Built-in', 'bool', {'sortable': False}),
    ],
    summary=[('total', 'Roles', 'Role::query()->count()', 'Defined roles', None, 'ShieldCheck', 'primary'),
             ('custom', 'Custom roles', "Role::query()->whereNotIn('name', Role::SYSTEM)->count()", 'Created by you', None, 'Sparkles', 'success')],
    delete_guard=dict(when='$role->isSystem() || $role->users()->exists()', message="__('Built-in roles and roles still assigned to someone cannot be deleted.')"),
    delete_hidden='row.is_system || row.users_count > 0',
    edit_description="`${record.permissions_count} permissions · ${record.users_count} users`",
    test=dict(
        record="['name' => \"custom_role_{$n}\", 'guard_name' => 'web']",
        setup="        \\Spatie\\Permission\\Models\\Permission::findOrCreate('dashboard-view', 'web');\n        \\Spatie\\Permission\\Models\\Permission::findOrCreate('orders-view', 'web');\n",
        payload="['name' => 'Pastry Lead', 'permissions' => ['dashboard-view', 'orders-view']]",
        assert_db="['name' => 'pastry_lead', 'guard_name' => 'web']",
        update_payload="['name' => 'custom_role_x', 'permissions' => ['orders-view']]",
        update_assert="['name' => 'custom_role_x']",
        search_record="['name' => 'night_baker']", search_keyword='night',
    ),
)


# Stat cards only where the number drives a decision (docs/project/design-direction.md, back office).
# These modules had them removed on 2026-11-17; an empty summary makes modgen skip the
# SummaryQuery, schemas/<module>/summary.ts and <StatCards>.
for _key in ('supplier', 'coupon', 'employee', 'blog_post', 'recipe', 'product_costing', 'user', 'customer', 'role'):
    SPECS[_key]['summary'] = []
