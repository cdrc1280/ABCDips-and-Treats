import { Download, History, Printer, Receipt } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableFooter, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { formatDate, money, titleCase } from '@/lib/format';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { invoice } from '@/routes/order';
import type { OrderRecord } from '@/types';

/** Hand-written panel on the order edit page: line items, totals, invoice links and status history. */
export function OrderDetails({ record }: { record: OrderRecord }) {
    const optionText = (options: Record<string, unknown> | null) =>
        options
            ? Object.entries(options)
                  .filter(([key, value]) => value !== null && value !== '' && !key.startsWith('is_'))
                  .map(([key, value]) => `${titleCase(key)}: ${Array.isArray(value) ? value.join(', ') : String(value)}`)
                  .join(' · ')
            : '';

    return (
        <>
            <Card>
                <CardHeader className="flex flex-row items-center justify-between gap-2">
                    <CardTitle className="flex items-center gap-2 text-base">
                        <Receipt className="size-4 text-muted-foreground" /> Items
                    </CardTitle>
                    <div className="flex gap-2">
                        <Button variant="outline" size="sm" asChild>
                            <a href={invoice(record.id).url} target="_blank" rel="noreferrer">
                                <Printer /> Print invoice
                            </a>
                        </Button>
                        <Button variant="outline" size="sm" asChild>
                            <a href={invoice(record.id, { query: { print: 1 } }).url} target="_blank" rel="noreferrer">
                                <Download /> PDF
                            </a>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent className="overflow-x-auto px-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="pl-6">Product</TableHead>
                                <TableHead className="text-right">Qty</TableHead>
                                <TableHead className="text-right">Price</TableHead>
                                <TableHead className="pr-6 text-right">Subtotal</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {record.items.map((item) => (
                                <TableRow key={item.id}>
                                    <TableCell className="pl-6">
                                        <div className="font-medium">{item.product_name}</div>
                                        <div className="text-xs text-muted-foreground">
                                            {item.product_sku}
                                            {optionText(item.options) && ` · ${optionText(item.options)}`}
                                        </div>
                                    </TableCell>
                                    <TableCell className="text-right tabular-nums">{item.qty}</TableCell>
                                    <TableCell className="text-right tabular-nums">{money(item.unit_price)}</TableCell>
                                    <TableCell className="pr-6 text-right tabular-nums">{money(item.subtotal)}</TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                        <TableFooter>
                            <TableRow>
                                <TableCell colSpan={3} className="pl-6 text-right text-muted-foreground">
                                    Subtotal
                                </TableCell>
                                <TableCell className="pr-6 text-right tabular-nums">{money(record.subtotal)}</TableCell>
                            </TableRow>
                            {record.discount_amount > 0 && (
                                <TableRow>
                                    <TableCell colSpan={3} className="pl-6 text-right text-muted-foreground">
                                        Discount{record.coupon_code ? ` (${record.coupon_code})` : ''}
                                    </TableCell>
                                    <TableCell className="pr-6 text-right tabular-nums">−{money(record.discount_amount)}</TableCell>
                                </TableRow>
                            )}
                            <TableRow>
                                <TableCell colSpan={3} className="pl-6 text-right text-muted-foreground">
                                    Delivery fee
                                </TableCell>
                                <TableCell className="pr-6 text-right tabular-nums">{money(record.delivery_fee)}</TableCell>
                            </TableRow>
                            <TableRow>
                                <TableCell colSpan={3} className="pl-6 text-right font-semibold">
                                    Total
                                </TableCell>
                                <TableCell className="pr-6 text-right font-semibold tabular-nums">{money(record.total)}</TableCell>
                            </TableRow>
                        </TableFooter>
                    </Table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2 text-base">
                        <History className="size-4 text-muted-foreground" /> Status history
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    {record.history.length === 0 ? (
                        <p className="text-sm text-muted-foreground">No status changes yet.</p>
                    ) : (
                        <ol className="relative grid gap-4 border-l pl-5">
                            {record.history.map((entry) => (
                                <li key={entry.id} className="relative">
                                    <span className="absolute top-1.5 -left-[1.6rem] size-2.5 rounded-full border-2 border-background bg-primary" />
                                    <div className="flex flex-wrap items-center gap-2">
                                        <StatusBadge label={entry.to_status} tone={toneFor(entry.to_status)} />
                                        {entry.from_status && (
                                            <span className="text-xs text-muted-foreground">from {titleCase(entry.from_status)}</span>
                                        )}
                                    </div>
                                    <p className="mt-1 text-xs text-muted-foreground">
                                        {formatDate(entry.created_at, true)}
                                        {entry.changed_by && ` · ${entry.changed_by}`}
                                    </p>
                                    {entry.comment && <p className="mt-1 text-sm">{entry.comment}</p>}
                                </li>
                            ))}
                        </ol>
                    )}
                </CardContent>
            </Card>
        </>
    );
}
