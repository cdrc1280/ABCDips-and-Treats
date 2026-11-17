import { ArrowDown, ArrowUp, ArrowUpDown, Columns3, Inbox, Loader2 } from 'lucide-react';
import { useMemo, useState, type ReactNode } from 'react';

import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { PHONE_QUERY, useMediaQuery } from '@/hooks/use-media-query';
import { useSavePreference } from '@/hooks/use-save-preference';
import { cn } from '@/lib/utils';
import type { ColumnDef, TablePreferences } from '@/types';

export interface DataTableProps<T> {
    columns: ColumnDef<T>[];
    rows: T[];
    rowKey: (row: T) => string | number;
    /** Kebab menu for each row, rendered in the frozen-left Actions column. */
    actions?: (row: T) => ReactNode;
    loading?: boolean;
    sort?: { field: string | null; dir: 'asc' | 'desc' | null };
    onSort?: (field: string) => void;
    /** Row number offset (pagination) for the S.No column. */
    offset?: number;
    tableName?: string;
    preferences?: TablePreferences;
    emptyMessage?: string;
    toolbar?: ReactNode;
}

const priorityClass = { 1: '', 2: 'hidden md:table-cell', 3: 'hidden xl:table-cell' } as const;

/**
 * Data table: frozen Actions + S.No columns, sortable headers, hideable/reorderable
 * columns saved per user, priority-based hiding on narrow screens, dimmed loading state.
 * Phones get card rows instead: the first column as the title, up to three more
 * always-visible columns as label/value pairs, and the row's action menu. No S.No.
 */
export function DataTable<T>({
    columns,
    rows,
    rowKey,
    actions,
    loading,
    sort,
    onSort,
    offset = 0,
    tableName,
    preferences,
    emptyMessage = 'No records found.',
    toolbar,
}: DataTableProps<T>) {
    const phone = useMediaQuery(PHONE_QUERY);
    const save = useSavePreference(tableName ?? 'default');
    const [prefs, setPrefs] = useState<TablePreferences>(preferences ?? {});

    const visibleColumns = useMemo(() => {
        const hidden = new Set(prefs.hidden ?? []);
        const order = prefs.order ?? [];
        const ordered = [...columns].sort((a, b) => {
            const ia = order.indexOf(a.key);
            const ib = order.indexOf(b.key);
            return (ia === -1 ? Number.MAX_SAFE_INTEGER : ia) - (ib === -1 ? Number.MAX_SAFE_INTEGER : ib);
        });
        return ordered.filter((column) => !hidden.has(column.key));
    }, [columns, prefs]);

    const toggleColumn = (key: string, visible: boolean) => {
        const hidden = new Set(prefs.hidden ?? []);
        if (visible) hidden.delete(key);
        else hidden.add(key);
        const next = { ...prefs, hidden: [...hidden] };
        setPrefs(next);
        if (tableName) save(next);
    };

    const sortIcon = (column: ColumnDef<T>) => {
        const field = column.sortField ?? column.key;
        if (sort?.field !== field) return <ArrowUpDown className="size-3.5 opacity-40" />;
        return sort.dir === 'desc' ? <ArrowDown className="size-3.5" /> : <ArrowUp className="size-3.5" />;
    };

    const hideable = columns.filter((column) => column.hideable !== false);

    const [titleColumn, ...restColumns] = visibleColumns;
    const cardColumns = restColumns.filter((column) => (column.priorityLevel ?? 1) === 1).slice(0, 3);
    const empty = (
        <div className="flex flex-col items-center gap-2 text-muted-foreground">
            <Inbox className="size-8 opacity-60" />
            <span className="text-sm">{emptyMessage}</span>
        </div>
    );

    return (
        <div className="overflow-hidden rounded-xl border bg-card">
            <div className={cn('items-center gap-2 border-b px-3 py-2', phone && !toolbar && !loading ? 'hidden' : 'flex')}>
                <div className="flex flex-1 items-center gap-2">{toolbar}</div>
                {loading && <Loader2 className="size-4 animate-spin text-muted-foreground" aria-label="Loading" />}
                {hideable.length > 0 && (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="sm" className="text-muted-foreground">
                                <Columns3 /> Columns
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" className="w-52">
                            <DropdownMenuLabel>Visible columns</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            {hideable.map((column) => (
                                <DropdownMenuCheckboxItem
                                    key={column.key}
                                    checked={!(prefs.hidden ?? []).includes(column.key)}
                                    onCheckedChange={(checked) => toggleColumn(column.key, Boolean(checked))}
                                    onSelect={(event) => event.preventDefault()}
                                >
                                    {column.header}
                                </DropdownMenuCheckboxItem>
                            ))}
                        </DropdownMenuContent>
                    </DropdownMenu>
                )}
            </div>

            <div className="relative">
                {/* Refetch keeps the frame: rows stay put, dimmed, until the new page arrives. */}
                <div
                    className={cn('overflow-x-auto transition-opacity duration-200', loading && 'pointer-events-none opacity-55')}
                    aria-busy={loading}
                >
                    {phone ? (
                        <ul className="divide-y">
                            {rows.length === 0 ? (
                                <li className="flex h-40 items-center justify-center">{empty}</li>
                            ) : (
                                rows.map((row, index) => (
                                    <li key={rowKey(row)} className="grid gap-2 px-3 py-3">
                                        <div className="flex items-start gap-2">
                                            <div className="min-w-0 flex-1 text-sm">{titleColumn?.cell(row, index)}</div>
                                            {actions && <div className="-mt-1 -mr-1 shrink-0">{actions(row)}</div>}
                                        </div>
                                        {cardColumns.length > 0 && (
                                            <dl className="grid grid-cols-[auto_minmax(0,1fr)] gap-x-4 gap-y-1 text-sm">
                                                {cardColumns.map((column) => (
                                                    <div key={column.key} className="contents">
                                                        <dt className="text-xs text-muted-foreground">{column.header}</dt>
                                                        <dd className="min-w-0 text-right tabular-nums">{column.cell(row, index)}</dd>
                                                    </div>
                                                ))}
                                            </dl>
                                        )}
                                    </li>
                                ))
                            )}
                        </ul>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow className="hover:bg-transparent [&>th]:h-9 [&>th]:text-xs [&>th]:font-medium [&>th]:tracking-[0.06em] [&>th]:text-muted-foreground [&>th]:uppercase">
                                    {actions && <TableHead className="sticky left-0 z-[1] w-12 bg-card text-center">Actions</TableHead>}
                                    <TableHead className={cn('w-14 bg-card text-center', actions ? 'sticky left-12 z-[1]' : 'sticky left-0 z-[1]')}>
                                        S.No
                                    </TableHead>
                                    {visibleColumns.map((column) => {
                                        const field = column.sortField ?? column.key;
                                        return (
                                            <TableHead
                                                key={column.key}
                                                style={column.width ? { width: column.width } : undefined}
                                                className={cn(
                                                    priorityClass[column.priorityLevel ?? 1],
                                                    column.align === 'right' && 'text-right',
                                                    column.align === 'center' && 'text-center',
                                                )}
                                            >
                                                {column.sortable && onSort ? (
                                                    <button
                                                        type="button"
                                                        className={cn(
                                                            'inline-flex items-center gap-1 uppercase hover:text-foreground',
                                                            column.align === 'right' && 'flex-row-reverse',
                                                        )}
                                                        onClick={() => onSort(field)}
                                                    >
                                                        {column.header}
                                                        {sortIcon(column)}
                                                    </button>
                                                ) : (
                                                    column.header
                                                )}
                                            </TableHead>
                                        );
                                    })}
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {rows.length === 0 ? (
                                    <TableRow className="hover:bg-transparent">
                                        <TableCell colSpan={visibleColumns.length + (actions ? 2 : 1)} className="h-40 text-center">
                                            {empty}
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    rows.map((row, index) => (
                                        <TableRow key={rowKey(row)} className="group hover:bg-row-hover">
                                            {actions && (
                                                <TableCell className="sticky left-0 z-[1] bg-card text-center group-hover:bg-row-hover">
                                                    {actions(row)}
                                                </TableCell>
                                            )}
                                            <TableCell
                                                className={cn(
                                                    'bg-card text-center text-xs text-muted-foreground tabular-nums group-hover:bg-row-hover',
                                                    actions ? 'sticky left-12 z-[1]' : 'sticky left-0 z-[1]',
                                                )}
                                            >
                                                {offset + index + 1}
                                            </TableCell>
                                            {visibleColumns.map((column) => (
                                                <TableCell
                                                    key={column.key}
                                                    className={cn(
                                                        priorityClass[column.priorityLevel ?? 1],
                                                        column.align === 'right' && 'text-right tabular-nums',
                                                        column.align === 'center' && 'text-center',
                                                        column.className,
                                                    )}
                                                >
                                                    {column.cell(row, index)}
                                                </TableCell>
                                            ))}
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    )}
                </div>
            </div>
        </div>
    );
}
