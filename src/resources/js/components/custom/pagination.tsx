import { ChevronLeft, ChevronRight } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Paginated } from '@/types';

interface Props {
    meta: Paginated<unknown>['meta'];
    perPageOptions: number[];
    onPage: (page: number) => void;
    onPerPage: (perPage: number) => void;
    disabled?: boolean;
}

export function Pagination({ meta, perPageOptions, onPage, onPerPage, disabled }: Props) {
    return (
        <div className="flex flex-col items-center justify-between gap-3 px-1 text-sm text-muted-foreground sm:flex-row">
            <div className="tabular-nums">
                {meta.total === 0 ? 'No records' : `Showing ${meta.from}–${meta.to} of ${meta.total.toLocaleString('en-US')}`}
            </div>
            <div className="flex items-center gap-2">
                <span className="hidden sm:inline">Rows</span>
                <Select value={String(meta.per_page)} onValueChange={(value) => onPerPage(Number(value))} disabled={disabled}>
                    <SelectTrigger size="sm" className="w-20">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        {perPageOptions.map((option) => (
                            <SelectItem key={option} value={String(option)}>
                                {option}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                <span className="tabular-nums">
                    Page {meta.current_page} / {Math.max(meta.last_page, 1)}
                </span>
                <Button
                    variant="outline"
                    size="icon"
                    className="size-8"
                    aria-label="Previous page"
                    disabled={disabled || meta.current_page <= 1}
                    onClick={() => onPage(meta.current_page - 1)}
                >
                    <ChevronLeft />
                </Button>
                <Button
                    variant="outline"
                    size="icon"
                    className="size-8"
                    aria-label="Next page"
                    disabled={disabled || meta.current_page >= meta.last_page}
                    onClick={() => onPage(meta.current_page + 1)}
                >
                    <ChevronRight />
                </Button>
            </div>
        </div>
    );
}
