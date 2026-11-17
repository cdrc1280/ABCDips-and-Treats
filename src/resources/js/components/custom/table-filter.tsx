import { usePage } from '@inertiajs/react';
import { RotateCcw, Search, SlidersHorizontal } from 'lucide-react';
import { useState, type FormEvent } from 'react';

import { DatePicker } from '@/components/custom/date-picker';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import type { FilterField, Option } from '@/types';

interface Props<T extends object> {
    fields: readonly FilterField[];
    values: Partial<T>;
    onChange: (values: Partial<T>) => void;
    onApply: () => void;
    onReset: () => void;
    loading?: boolean;
}

const booleanOptions: Option<string>[] = [
    { label: 'Yes', value: '1' },
    { label: 'No', value: '0' },
];

/** True when a filter value is set (empty strings and nulls count as unset). */
const isActive = (value: unknown) => value !== undefined && value !== null && value !== '';

/**
 * Filter bar driven by schemas/{thing}/filter.ts. Phones get a "Filters" button that opens the same
 * fields in a bottom sheet instead of a screenful of selects; wider screens keep the inline bar.
 */
export function TableFilter<T extends object>({ fields, values, onChange, onApply, onReset, loading }: Props<T>) {
    const props = usePage().props as Record<string, unknown>;
    const record = values as Record<string, unknown>;
    const set = (name: string, value: unknown) => onChange({ ...values, [name]: value ?? undefined } as Partial<T>);
    const [sheetOpen, setSheetOpen] = useState(false);
    const activeCount = fields.filter((field) => isActive(record[field.name])).length;

    const submit = (event: FormEvent) => {
        event.preventDefault();
        setSheetOpen(false);
        onApply();
    };
    const reset = () => {
        setSheetOpen(false);
        onReset();
    };

    const renderFields = (idPrefix: string) =>
        fields.map((field) => {
            const id = `${idPrefix}-${field.name}`;
            const value = record[field.name];
            const options = (field.optionsProp ? (props[field.optionsProp] as Option[] | undefined) : field.options) ?? [];
            return (
                <div key={field.name} className="grid gap-1.5">
                    <Label htmlFor={id} className="text-xs text-muted-foreground">
                        {field.label}
                    </Label>
                    {field.type === 'text' && (
                        <Input
                            id={id}
                            value={(value as string) ?? ''}
                            placeholder={field.placeholder}
                            onChange={(e) => set(field.name, e.target.value)}
                        />
                    )}
                    {field.type === 'select' && (
                        <SelectDropdown
                            id={id}
                            clearable
                            value={(value as string | number) ?? null}
                            options={options}
                            placeholder={field.placeholder ?? 'All'}
                            onChange={(v) => set(field.name, v)}
                        />
                    )}
                    {field.type === 'boolean' && (
                        <SelectDropdown
                            id={id}
                            clearable
                            value={(value as string) ?? null}
                            options={booleanOptions}
                            placeholder="All"
                            onChange={(v) => set(field.name, v)}
                        />
                    )}
                    {field.type === 'date' && <DatePicker id={id} value={(value as string) ?? null} onChange={(v) => set(field.name, v)} />}
                </div>
            );
        });

    const buttons = (
        <>
            <Button type="submit" size="sm" disabled={loading} className="flex-1 lg:flex-none">
                <Search /> Apply
            </Button>
            <Button type="button" size="sm" variant="outline" disabled={loading} onClick={reset} className="flex-1 lg:flex-none">
                <RotateCcw /> Reset
            </Button>
        </>
    );

    return (
        <>
            {/* Phones: one button, fields in a bottom sheet. */}
            <div className="flex items-center gap-2 sm:hidden">
                <Button type="button" variant="outline" className="flex-1 justify-start" onClick={() => setSheetOpen(true)}>
                    <SlidersHorizontal /> Filters
                    {activeCount > 0 && (
                        <span className="ml-auto rounded-full bg-primary px-2 py-0.5 text-xs font-semibold text-primary-foreground tabular-nums">
                            {activeCount}
                        </span>
                    )}
                </Button>
                {activeCount > 0 && (
                    <Button type="button" variant="ghost" size="icon" aria-label="Clear filters" disabled={loading} onClick={reset}>
                        <RotateCcw />
                    </Button>
                )}
            </div>
            <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
                <SheetContent side="bottom" className="max-h-[85svh] gap-0 rounded-t-2xl p-0">
                    <SheetHeader className="border-b">
                        <SheetTitle>Filters</SheetTitle>
                        <SheetDescription className="sr-only">Narrow down the list.</SheetDescription>
                    </SheetHeader>
                    <form onSubmit={submit} className="flex min-h-0 flex-col">
                        <div className="grid gap-3 overflow-y-auto p-4">{renderFields('m-filter')}</div>
                        <div className="flex gap-2 border-t p-4 pb-[max(1rem,env(safe-area-inset-bottom))]">{buttons}</div>
                    </form>
                </SheetContent>
            </Sheet>

            {/* Tablet and up: inline bar. */}
            <form onSubmit={submit} className="hidden gap-3 rounded-xl border bg-card p-4 sm:grid sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                {renderFields('filter')}
                <div className="flex items-end gap-2 sm:col-span-2 lg:col-span-1 lg:col-start-auto">{buttons}</div>
            </form>
        </>
    );
}
