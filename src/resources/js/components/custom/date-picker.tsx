import { format, parseISO } from 'date-fns';
import { CalendarIcon, X } from 'lucide-react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Input } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';

interface Props {
    id?: string;
    /** Y-m-d */
    value: string | null | undefined;
    onChange: (value: string | null) => void;
    placeholder?: string;
    invalid?: boolean;
    clearable?: boolean;
    disabled?: boolean;
    /** Earliest selectable day, Y-m-d (a longer ISO string is cut to its date). */
    min?: string;
}

/** shadcn Calendar in a Popover. Every date field uses this, never a native `type="date"`. */
export function DatePicker({ id, value, onChange, placeholder = 'Pick a date', invalid, clearable = true, disabled, min }: Props) {
    const [open, setOpen] = useState(false);
    const date = value ? parseISO(value.slice(0, 10)) : undefined;
    const minDate = min ? parseISO(min.slice(0, 10)) : undefined;

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    id={id}
                    type="button"
                    variant="outline"
                    disabled={disabled}
                    aria-invalid={invalid || undefined}
                    className={cn('w-full min-w-0 justify-start font-normal', !date && 'text-muted-foreground')}
                >
                    <CalendarIcon className="size-4" />
                    <span className="truncate">{date ? format(date, 'MMM d, yyyy') : placeholder}</span>
                    {clearable && date && (
                        <X
                            className="ml-auto size-3.5 opacity-60 hover:opacity-100"
                            onClick={(event) => {
                                event.stopPropagation();
                                onChange(null);
                            }}
                        />
                    )}
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0" align="start">
                <Calendar
                    mode="single"
                    selected={date}
                    defaultMonth={date ?? minDate}
                    captionLayout="dropdown"
                    startMonth={minDate}
                    disabled={minDate ? { before: minDate } : undefined}
                    onSelect={(selected) => {
                        onChange(selected ? format(selected, 'yyyy-MM-dd') : null);
                        setOpen(false);
                    }}
                />
            </PopoverContent>
        </Popover>
    );
}

interface DateTimeProps extends Omit<Props, 'value' | 'onChange'> {
    /** Y-m-d\TH:i (the `datetime-local` shape the server already parses). */
    value: string | null | undefined;
    onChange: (value: string) => void;
    /** Time used when a day is picked before any time is set. */
    defaultTime?: string;
}

/** shadcn date + time: Calendar popover for the day, a time box beside it. Emits '' when cleared. */
export function DateTimePicker({ id, value, onChange, invalid, disabled, min, clearable = true, placeholder, defaultTime = '09:00' }: DateTimeProps) {
    const day = value ? value.slice(0, 10) : '';
    const time = value && value.length >= 16 ? value.slice(11, 16) : '';
    const minTime = min && day && min.slice(0, 10) === day && min.length >= 16 ? min.slice(11, 16) : undefined;

    return (
        <div className="grid grid-cols-[minmax(0,1fr)_7.5rem] gap-2">
            <DatePicker
                id={id}
                value={day || null}
                min={min}
                invalid={invalid}
                disabled={disabled}
                clearable={clearable}
                placeholder={placeholder}
                onChange={(next) => onChange(next ? `${next}T${time || defaultTime}` : '')}
            />
            <Input
                type="time"
                aria-label="Time"
                value={time}
                min={minTime}
                disabled={disabled || !day}
                aria-invalid={invalid || undefined}
                onChange={(event) => onChange(day && event.target.value ? `${day}T${event.target.value}` : day ? `${day}T${defaultTime}` : '')}
            />
        </div>
    );
}
