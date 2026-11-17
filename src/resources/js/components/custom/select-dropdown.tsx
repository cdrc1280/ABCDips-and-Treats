import { Check, ChevronsUpDown, X } from 'lucide-react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import type { Option } from '@/types';

interface Props<V extends string | number> {
    id?: string;
    value: V | null | undefined;
    onChange: (value: V | null) => void;
    options: readonly Option<V>[];
    placeholder?: string;
    searchPlaceholder?: string;
    clearable?: boolean;
    disabled?: boolean;
    invalid?: boolean;
    className?: string;
}

/** Searchable select (popover + command). */
export function SelectDropdown<V extends string | number>({
    id,
    value,
    onChange,
    options,
    placeholder = 'Select…',
    searchPlaceholder = 'Search…',
    clearable = false,
    disabled,
    invalid,
    className,
}: Props<V>) {
    const [open, setOpen] = useState(false);
    const selected = options.find((option) => String(option.value) === String(value ?? ''));

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    id={id}
                    type="button"
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    aria-invalid={invalid || undefined}
                    disabled={disabled}
                    className={cn('w-full justify-between font-normal', !selected && 'text-muted-foreground', className)}
                >
                    <span className="truncate">{selected?.label ?? placeholder}</span>
                    <span className="flex items-center gap-1">
                        {clearable && selected && (
                            <X
                                className="size-3.5 opacity-60 hover:opacity-100"
                                onClick={(event) => {
                                    event.stopPropagation();
                                    onChange(null);
                                }}
                            />
                        )}
                        <ChevronsUpDown className="size-4 opacity-50" />
                    </span>
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-full max-w-[calc(100vw-2rem)] p-0 sm:min-w-100" align="start">
                <Command>
                    <CommandInput placeholder={searchPlaceholder} />
                    <CommandList>
                        <CommandEmpty>No results.</CommandEmpty>
                        <CommandGroup>
                            {options.map((option) => (
                                <CommandItem
                                    key={String(option.value)}
                                    value={`${option.label} ${option.value}`}
                                    onSelect={() => {
                                        onChange(option.value);
                                        setOpen(false);
                                    }}
                                >
                                    <Check className={cn('size-4', String(option.value) === String(value ?? '') ? 'opacity-100' : 'opacity-0')} />
                                    <span className="grid">
                                        <span>{option.label}</span>
                                        {option.description && <span className="text-xs text-muted-foreground">{option.description}</span>}
                                    </span>
                                </CommandItem>
                            ))}
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
