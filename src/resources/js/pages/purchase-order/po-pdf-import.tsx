import { router, usePage } from '@inertiajs/react';
import { FileUp, Loader2 } from 'lucide-react';
import { useState } from 'react';

import { InputError } from '@/components/custom/field';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { importPdf } from '@/routes/admin/purchase-orders';

import type { PurchaseOrderFormData } from './purchase-order-form';

interface PoImport {
    po_number: string | null;
    supplier_id: string | null;
    supplier_name: string | null;
    expected_delivery_date: string | null;
    payment_terms: string | null;
    tax: number;
    items: { ingredient_id: string | null; item_name: string; qty_ordered: number; unit_cost: number }[];
}

/** Hand-written panel on the PO create page: read a supplier PDF and prefill the form. */
export function PoPdfImport({ setData }: { setData: <K extends keyof PurchaseOrderFormData>(key: K, value: PurchaseOrderFormData[K]) => void }) {
    const flash = usePage().flash as { po_import?: PoImport };
    const [file, setFile] = useState<File | null>(null);
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState<string | undefined>();
    const [applied, setApplied] = useState<PoImport | null>(null);
    const [unmatched, setUnmatched] = useState<string[]>([]);

    // Apply each flashed import once.
    if (flash.po_import && flash.po_import !== applied) {
        const data = flash.po_import;
        setApplied(data);
        if (data.po_number) setData('po_number', data.po_number);
        if (data.supplier_id) setData('supplier_id', String(data.supplier_id));
        if (data.expected_delivery_date) setData('expected_delivery_date', data.expected_delivery_date);
        if (data.payment_terms) setData('payment_terms', data.payment_terms);
        setData('tax', String(data.tax ?? 0));
        setData(
            'items',
            data.items.map((item) => ({
                ingredient_id: item.ingredient_id ? String(item.ingredient_id) : '',
                qty_ordered: String(item.qty_ordered),
                unit_cost: String(item.unit_cost),
            })),
        );
        setUnmatched(data.items.filter((item) => !item.ingredient_id).map((item) => item.item_name));
    }

    const upload = () => {
        if (!file) return;
        router.post(
            importPdf().url,
            { pdf: file },
            {
                forceFormData: true,
                preserveState: true,
                preserveScroll: true,
                onStart: () => setBusy(true),
                onFinish: () => setBusy(false),
                onError: (errors) => setError(errors.pdf),
                onSuccess: () => setError(undefined),
            },
        );
    };

    return (
        <Card>
            <CardContent className="grid gap-2">
                <p className="text-sm font-medium">Have the supplier&apos;s PO or Conforme as a PDF?</p>
                <div className="flex flex-wrap items-center gap-2">
                    <Input
                        type="file"
                        accept="application/pdf"
                        className="max-w-xs"
                        onChange={(event) => setFile(event.target.files?.[0] ?? null)}
                        aria-invalid={!!error}
                    />
                    <Button type="button" variant="outline" size="sm" disabled={!file || busy} onClick={upload}>
                        {busy ? <Loader2 className="animate-spin" /> : <FileUp />} Read PDF
                    </Button>
                </div>
                <InputError message={error} />
                {applied && (
                    <p className="text-xs text-muted-foreground">
                        Filled from the PDF{applied.supplier_name ? ` (${applied.supplier_name})` : ''}.{' '}
                        {unmatched.length > 0 && <span className="text-warning-ink">Pick the ingredient for: {unmatched.join(', ')}.</span>}
                    </p>
                )}
            </CardContent>
        </Card>
    );
}
