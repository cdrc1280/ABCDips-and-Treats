import { Check, ChevronsUpDown, X } from 'lucide-react';
import { useState } from 'react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import type { Option } from '@/types';

interface Props {
    id?: string;
    value: string[];
    onChange: (value: string[]) => void;
    options: readonly Option<string>[];
    placeholder?: string;
    invalid?: boolean;
}

/** Searchable multi-select with removable chips. */
export function MultiSelect({ id, value, onChange, options, placeholder = 'Select…', invalid }: Props) {
    const [open, setOpen] = useState(false);
    const toggle = (item: string) => onChange(value.includes(item) ? value.filter((v) => v !== item) : [...value, item]);
    const labelOf = (item: string) => options.find((option) => option.value === item)?.label ?? item;

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
                    className="h-auto min-h-9 w-full justify-between py-1.5 font-normal"
                >
                    <span className="flex flex-wrap gap-1">
                        {value.length === 0 && <span className="text-muted-foreground">{placeholder}</span>}
                        {value.map((item) => (
                            <Badge key={item} variant="secondary" className="gap-1">
                                {labelOf(item)}
                                <X
                                    className="size-3 opacity-60 hover:opacity-100"
                                    onClick={(event) => {
                                        event.stopPropagation();
                                        toggle(item);
                                    }}
                                />
                            </Badge>
                        ))}
                    </span>
                    <ChevronsUpDown className="size-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-full max-w-[calc(100vw-2rem)] p-0 sm:min-w-80" align="start">
                <Command>
                    <CommandInput placeholder="Search…" />
                    <CommandList>
                        <CommandEmpty>No results.</CommandEmpty>
                        <CommandGroup>
                            {options.map((option) => (
                                <CommandItem key={option.value} value={`${option.label} ${option.value}`} onSelect={() => toggle(option.value)}>
                                    <Check className={cn('size-4', value.includes(option.value) ? 'opacity-100' : 'opacity-0')} />
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
