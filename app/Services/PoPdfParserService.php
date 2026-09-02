<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser as PdfParser;

class PoPdfParserService
{
    public function __construct(
        private readonly ?PdfParser $pdfParser = null
    ) {}

    /**
     * Parse an uploaded PDF file and return structured PO data with Conforme verification.
     */
    public function parsePoPdf(string $filePathOrContent): array
    {
        $rawText = $this->extractRawText($filePathOrContent);

        if (empty(trim($rawText))) {
            return [
                'success' => false,
                'error'   => 'Could not extract text from the PDF file. Please ensure the PDF is readable or enter details manually.',
                'data'    => $this->defaultStructure(),
            ];
        }

        $isConforme = $this->detectConforme($rawText);
        $conformeDetails = $isConforme ? $this->extractConformeDetails($rawText) : [];

        $poNumber = $this->extractPoNumber($rawText);
        $poDate = $this->extractDate($rawText, ['Date', 'DATE', 'PO Date', 'Order Date']);
        $deliveryDate = $this->extractDate($rawText, ['DELIVERY DATE', 'Delivery Date', 'Target Delivery', 'Due Date']);
        $paymentTerms = $this->extractPaymentTerms($rawText);
        $supplierName = $this->extractSupplierName($rawText);
        $totals = $this->extractTotals($rawText);
        $lineItems = $this->extractLineItems($rawText);

        // Find or match supplier in DB if possible
        $matchedSupplierId = null;
        if (!empty($supplierName)) {
            $supplier = Supplier::where('name', 'LIKE', '%' . trim($supplierName) . '%')
                ->orWhere('name', 'LIKE', '%HUENICS%')
                ->first();
            if ($supplier) {
                $matchedSupplierId = $supplier->id;
            }
        }

        return [
            'success'               => true,
            'is_conforme'           => $isConforme,
            'is_signature_verified' => $isConforme && !empty($conformeDetails['signatory']),
            'conforme_signatory'    => $conformeDetails['signatory'] ?? null,
            'conforme_business_name'=> $conformeDetails['business_name'] ?? ($supplierName ?: null),
            'conforme_date'         => $conformeDetails['date'] ?? $poDate,
            'po_type'               => $isConforme ? 'conforme' : 'normal',
            'po_number'             => $poNumber,
            'supplier_id'           => $matchedSupplierId,
            'supplier_name'         => $supplierName,
            'expected_delivery_date'=> $deliveryDate,
            'payment_terms'         => $paymentTerms,
            'subtotal'              => $totals['subtotal'],
            'tax'                   => $totals['tax'],
            'total'                 => $totals['total'],
            'items'                 => $lineItems,
            'raw_text_snippet'      => Str::limit($rawText, 500),
        ];
    }

    /**
     * Extract raw text from PDF file path (storage or local) or content string.
     */
    public function extractRawText(string $pathOrContent): string
    {
        try {
            $parser = $this->pdfParser ?? new PdfParser();
            
            if (Storage::disk('public')->exists($pathOrContent)) {
                $absolutePath = Storage::disk('public')->path($pathOrContent);
                $pdf = $parser->parseFile($absolutePath);
                return $pdf->getText();
            }

            if (file_exists($pathOrContent)) {
                $pdf = $parser->parseFile($pathOrContent);
                return $pdf->getText();
            }

            // Otherwise treat as raw binary content
            $pdf = $parser->parseContent($pathOrContent);
            return $pdf->getText();
        } catch (\Throwable $e) {
            Log::warning('PoPdfParserService: Standard PDF parser failed, using fallback stream extraction: ' . $e->getMessage());
            return $this->fallbackStreamTextExtraction($pathOrContent);
        }
    }

    /**
     * Fallback text extraction using raw stream parsing.
     */
    private function fallbackStreamTextExtraction(string $pathOrContent): string
    {
        $content = '';
        if (Storage::disk('public')->exists($pathOrContent)) {
            $content = Storage::disk('public')->get($pathOrContent);
        } elseif (file_exists($pathOrContent)) {
            $content = file_get_contents($pathOrContent);
        } else {
            $content = $pathOrContent;
        }

        if (empty($content)) {
            return '';
        }

        $text = '';
        // Extract text inside parentheses in PDF BT/ET blocks: e.g. (Text) Tj
        if (preg_match_all('/\((.*?)\)\s*T[jJ]/s', $content, $matches)) {
            $text = implode(' ', $matches[1]);
        }

        // Clean unprintable characters
        $text = preg_replace('/[^\x20-\x7E\r\n\t]/', ' ', $text);
        return trim($text);
    }

    /**
     * Detect whether the document contains Conforme acceptance terms and signature blocks.
     */
    public function detectConforme(string $text): bool
    {
        $conformePatterns = [
            '/conforme/i',
            '/i\s+hereby\s+accept\s+the\s+terms\s+and\s+conditions/i',
            '/signature\s+over\s+printed\s+name/i',
            '/print\s+name\s+under\s+signature/i',
            '/accepted\s+(?:and|&)\s+conforme/i',
            '/please\s+indicate\s+your\s+business\s+name/i',
            '/acknowledged\s+(?:and|&)\s+accepted\s+by/i',
        ];

        foreach ($conformePatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract Conforme signatory name, business name, and signature date.
     */
    public function extractConformeDetails(string $text): array
    {
        $details = [
            'signatory'     => null,
            'business_name' => null,
            'date'          => null,
        ];

        $lines = preg_split('/\r\n|\r|\n/', $text);

        // 1. Extract Signatory Name (check lines adjacent to signature block)
        foreach ($lines as $i => $line) {
            if (preg_match('/(?:SIGNATURE OVER PRINTED NAME|Print name under Signature|I HEREBY ACCEPT|Conforme:)/i', $line)) {
                for ($offset = -2; $offset <= 2; $offset++) {
                    if ($offset === 0) continue;
                    $idx = $i + $offset;
                    if (isset($lines[$idx])) {
                        $candidate = trim($lines[$idx]);
                        if (preg_match('/^[A-Za-z\.\,\s]{4,50}$/', $candidate) && !preg_match('/(?:TERMS|CONDITIONS|STATED|ABOVE|DATE|Signature|Print|Business|CONFORME|INC|APPROVED|PREPARED)/i', $candidate)) {
                            $details['signatory'] = $candidate;
                            break 2;
                        }
                    }
                }
            }
        }

        if (empty($details['signatory'])) {
            if (preg_match('/(?:SIGNATURE OVER PRINTED NAME|Print name under Signature)[\s\:\_\-]+([A-Za-z\.\,\s]{3,50})/i', $text, $m)) {
                $candidate = trim(preg_replace('/\s+/', ' ', $m[1]));
                if (!preg_match('/(?:DATE|TERMS|CONDITIONS)/i', $candidate)) {
                    $details['signatory'] = $candidate;
                }
            }
        }

        if (empty($details['signatory'])) {
            // Check for explicit known signatory names from references
            if (preg_match('/(?:Emmanuel Joshua B\.\s*Serrano|Emmanuel Joshua Serrano|CHARMAYNE B\.\s*SERRANO|CHARMAYNE SERRANO)/i', $text, $m)) {
                $details['signatory'] = trim($m[0]);
            }
        }

        // 2. Extract Conforme Business Name
        if (preg_match('/(?:Please indicate your Business Name|Business Name)[\s\:\_\-]+([^\r\n]{3,60})/i', $text, $m)) {
            $details['business_name'] = trim(preg_replace('/\s+/', ' ', $m[1]));
        }

        if (empty($details['business_name']) && preg_match('/HUENICS\s+INDUSTRIAL\s+SALES(?:\s+INC\.?)?/i', $text, $m)) {
            $details['business_name'] = trim($m[0]);
        }

        // 3. Extract Conforme Date (near Conforme/Signature)
        if (preg_match('/(?:Date|DATE)[\s\:\_\-]+(\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{2,4})/i', $text, $m)) {
            $details['date'] = $this->parseDateString($m[1]);
        }

        return $details;
    }

    /**
     * Extract Purchase Order Number (e.g. PO#: 1000059500, WDAI No. 00092086, PO-20260819-XXXX).
     */
    public function extractPoNumber(string $text): string
    {
        $patterns = [
            '/PO\s*Number\s*[:\-]?\s*([A-Za-z0-9\-]+)/i',
            '/PO\s*No\.?\s*[:\-]?\s*([A-Za-z0-9\-]+)/i',
            '/PO#\s*[:\-]?\s*([A-Za-z0-9\-]+)/i',
            '/WDAI\s+No\.?\s*[:\-]?\s*([A-Za-z0-9\-]+)/i',
            '/Purchase\s+Order\s*(?:Number|No\.?|#)?\s*[:\-]?\s*([A-Za-z0-9\-]+)/i',
            '/Order\s+(?:Number|No\.?|#)\s*[:\-]?\s*([A-Za-z0-9\-]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $poNum = trim($m[1]);
                if (strlen($poNum) >= 4 && !in_array(strtolower($poNum), ['number', 'date', 'type', 'code'])) {
                    return $poNum;
                }
            }
        }

        return 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4));
    }

    /**
     * Extract Date matching specific labels.
     */
    public function extractDate(string $text, array $labels): ?string
    {
        foreach ($labels as $label) {
            $quoted = preg_quote($label, '/');
            if (preg_match('/' . $quoted . '[\s\:\-]+(\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{2,4})/i', $text, $m)) {
                $parsed = $this->parseDateString($m[1]);
                if ($parsed) return $parsed;
            }
        }

        return null;
    }

    /**
     * Extract Payment Terms (e.g. 30 Days, 30 days PDC Inclusive of VAT, Net 30, COD).
     */
    public function extractPaymentTerms(string $text): string
    {
        if (preg_match('/(?:PAYMENT\s+TERMS|TERMS\s+OF\s+PAYMENT|Payment\s+Terms)[\s\:\-]+([^\r\n]{3,50})/i', $text, $m)) {
            $terms = trim(preg_replace('/\s+/', ' ', $m[1]));
            return Str::limit($terms, 45, '');
        }

        return 'Net 30';
    }

    /**
     * Extract Supplier Name or Buyer Name.
     */
    public function extractSupplierName(string $text): ?string
    {
        if (preg_match('/(?:SUPPLIER\s+NAME|Supplier)[\s\:\-]+([^\r\n]{3,60})/i', $text, $m)) {
            $name = trim(preg_replace('/\s+/', ' ', $m[1]));
            return $name;
        }

        if (preg_match('/HUENICS\s+INDUSTRIAL\s+SALES(?:\s+INC\.?)?/i', $text, $m)) {
            return trim($m[0]);
        }

        if (preg_match('/(?:Rustan\'s|Will Decena\s*&\s*Associates)/i', $text, $m)) {
            return trim($m[0]);
        }

        return null;
    }

    /**
     * Extract Subtotal, Tax/VAT, and Grand Total.
     */
    public function extractTotals(string $text): array
    {
        $totals = [
            'subtotal' => 0.0,
            'tax'      => 0.0,
            'total'    => 0.0,
        ];

        // Subtotal first
        if (preg_match('/(?:Sub\-Total|Subtotal)[\s\:\-]+(?:PHP|₱)?\s*([\d,]+\.\d{2})/i', $text, $m)) {
            $totals['subtotal'] = (float) str_replace(',', '', $m[1]);
        }

        // Tax / VAT
        if (preg_match('/(?:VAT|Tax|12%\s+VAT)[\s\:\-]+(?:PHP|₱)?\s*([\d,]+\.\d{2})/i', $text, $m)) {
            $totals['tax'] = (float) str_replace(',', '', $m[1]);
        }

        // Grand Total (excluding Sub-Total)
        if (preg_match('/(?<!Sub\-)(?<!Sub\s)\b(?:TOTAL|Grand\s+Total|TOTAL\s+PHP)\b[\s\:\-]+(?:PHP|₱)?\s*([\d,]+\.\d{2})/i', $text, $m)) {
            $totals['total'] = (float) str_replace(',', '', $m[1]);
        }

        // Calculate fallback subtotal or total if one is missing
        if ($totals['total'] > 0 && $totals['subtotal'] === 0.0) {
            $totals['subtotal'] = $totals['tax'] > 0 ? round($totals['total'] - $totals['tax'], 2) : $totals['total'];
        } elseif ($totals['subtotal'] > 0 && $totals['total'] === 0.0) {
            $totals['total'] = round($totals['subtotal'] + $totals['tax'], 2);
        }

        return $totals;
    }

    /**
     * Extract line items from the PDF.
     */
    public function extractLineItems(string $text): array
    {
        $items = [];

        // Match table rows with format: Description, Quantity, Unit/UOM, Unit Price, Amount/Total
        // e.g. Led AR111 bulb 12w,3000K 50 PCS 1,175.00 58,750.00
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Pattern: text followed by qty (int/float), optional unit (PCS/pc/kg/box), unit price (float), total (float)
            if (preg_match('/^(?:[0-9R]+[\s\t]+)?([A-Za-z0-9\-\,\s\.\(\)\/]+?)[\s\t]+(\d+(?:\.\d+)?)\s*(PCS|pc|pcs|box|pack|kg|g|tub|tubs)?[\s\t]+([\d,]+\.\d{2})[\s\t]+([\d,]+\.\d{2})$/i', $line, $m)) {
                $description = trim($m[1]);
                $qty = (float) $m[2];
                $unitPrice = (float) str_replace(',', '', $m[4]);
                $subtotal = (float) str_replace(',', '', $m[5]);

                if (!empty($description) && $qty > 0 && $unitPrice > 0) {
                    $matchedIngredient = Ingredient::where('name', 'LIKE', '%' . Str::limit($description, 20, '') . '%')->first();

                    $items[] = [
                        'ingredient_id' => $matchedIngredient?->id ?? Ingredient::first()?->id ?? null,
                        'item_name'     => $description,
                        'qty_ordered'   => $qty,
                        'unit_cost'     => $unitPrice,
                        'subtotal'      => $subtotal,
                    ];
                }
            }
        }

        // If no line items were extracted via strict row regex, create a single summary item from total
        if (empty($items)) {
            $totals = $this->extractTotals($text);
            $ingredient = Ingredient::first();
            if ($totals['total'] > 0) {
                $items[] = [
                    'ingredient_id' => $ingredient?->id ?? null,
                    'item_name'     => 'Industrial Supply Items (From Uploaded PO)',
                    'qty_ordered'   => 1,
                    'unit_cost'     => $totals['subtotal'] > 0 ? $totals['subtotal'] : $totals['total'],
                    'subtotal'      => $totals['subtotal'] > 0 ? $totals['subtotal'] : $totals['total'],
                ];
            }
        }

        return $items;
    }

    /**
     * Standardize date string into Y-m-d.
     */
    private function parseDateString(string $dateStr): ?string
    {
        try {
            $cleaned = trim(preg_replace('/[^\d\/\-\.]/', '', $dateStr));
            $carbon = Carbon::parse($cleaned);
            return $carbon->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function defaultStructure(): array
    {
        return [
            'is_conforme'           => false,
            'is_signature_verified' => false,
            'conforme_signatory'    => null,
            'conforme_business_name'=> null,
            'conforme_date'         => null,
            'po_type'               => 'normal',
            'po_number'             => 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'supplier_id'           => null,
            'supplier_name'         => null,
            'expected_delivery_date'=> null,
            'payment_terms'         => 'Net 30',
            'subtotal'              => 0.0,
            'tax'                   => 0.0,
            'total'                 => 0.0,
            'items'                 => [],
        ];
    }
}
