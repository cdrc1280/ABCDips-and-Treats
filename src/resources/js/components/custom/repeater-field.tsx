import { Plus, Trash2 } from 'lucide-react';

import { InputError } from '@/components/custom/field';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import type { Option } from '@/types';

export interface RepeaterColumn {
    name: string;
    label: string;
    type?: 'text' | 'number' | 'select';
    step?: string;
    min?: number;
    placeholder?: string;
    options?: readonly Option<string | number>[];
    /** Grid width, e.g. '2fr' (default 1fr). */
    width?: string;
}

type Row = Record<string, string>;

interface Props {
    name: string;
    rows: Row[];
    columns: RepeaterColumn[];
    onChange: (rows: Row[]) => void;
    /** Laravel validation errors, keyed like `flavors.0.name`. */
    errors: Record<string, string | undefined>;
    addLabel?: string;
    emptyText?: string;
    /** Computed read-only value shown at the end of each row (e.g. line total). */
    summary?: (row: Row) => string;
}

/** Editable list of rows (JSON column or hasMany child rows). Stacks on mobile. */
export function RepeaterField({ name, rows, columns, onChange, errors, addLabel = 'Add row', emptyText = 'Nothing added yet.', summary }: Props) {
    const blank = (): Row => Object.fromEntries(columns.map((column) => [column.name, '']));
    const update = (index: number, key: string, value: string) => onChange(rows.map((row, i) => (i === index ? { ...row, [key]: value } : row)));
    const template = `${columns.map((column) => column.width ?? '1fr').join(' ')}${summary ? ' 8rem' : ''} 2.5rem`;

    return (
        <div className="grid gap-2">
            {rows.length === 0 && <p className="rounded-lg border border-dashed p-4 text-center text-sm text-muted-foreground">{emptyText}</p>}
            {rows.length > 0 && (
                <div className="hidden gap-2 px-1 text-xs font-medium text-muted-foreground md:grid" style={{ gridTemplateColumns: template }}>
                    {columns.map((column) => (
                        <span key={column.name}>{column.label}</span>
                    ))}
                    {summary && <span className="text-right">Total</span>}
                    <span />
                </div>
            )}
            {rows.map((row, index) => (
                <div
                    key={index}
                    className="grid gap-2 rounded-lg border p-3 md:items-start md:border-0 md:p-0"
                    style={{ gridTemplateColumns: undefined }}
                >
                    <div className="grid gap-2 md:[grid-template-columns:var(--cols)]" style={{ ['--cols' as string]: template }}>
                        {columns.map((column) => {
                            const key = `${name}.${index}.${column.name}`;
                            const error = errors[key];
                            return (
                                <div key={column.name} className="grid gap-1">
                                    <Label htmlFor={key} className="text-xs text-muted-foreground md:sr-only">
                                        {column.label}
                                    </Label>
                                    {column.type === 'select' ? (
                                        <SelectDropdown
                                            id={key}
                                            value={row[column.name] || null}
                                            options={(column.options ?? []).map((option) => ({ ...option, value: String(option.value) }))}
                                            invalid={!!error}
                                            onChange={(value) => update(index, column.name, value === null ? '' : String(value))}
                                        />
                                    ) : (
                                        <Input
                                            id={key}
                                            type={column.type === 'number' ? 'number' : 'text'}
                                            inputMode={column.type === 'number' ? 'decimal' : undefined}
                                            step={column.step}
                                            min={column.min}
                                            placeholder={column.placeholder}
                                            value={row[column.name] ?? ''}
                                            aria-invalid={!!error}
                                            onChange={(event) => update(index, column.name, event.target.value)}
                                        />
                                    )}
                                    <InputError message={error} />
                                </div>
                            );
                        })}
                        {summary && <div className="flex h-9 items-center justify-end text-sm font-medium tabular-nums">{summary(row)}</div>}
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            className={cn('text-muted-foreground hover:text-destructive')}
                            aria-label="Remove row"
                            onClick={() => onChange(rows.filter((_, i) => i !== index))}
                        >
                            <Trash2 />
                        </Button>
                    </div>
                </div>
            ))}
            <InputError message={errors[name]} />
            <div>
                <Button type="button" variant="outline" size="sm" onClick={() => onChange([...rows, blank()])}>
                    <Plus /> {addLabel}
                </Button>
            </div>
        </div>
    );
}
