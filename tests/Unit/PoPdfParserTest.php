<?php

namespace Tests\Unit;

use App\Services\PoPdfParserService;
use PHPUnit\Framework\TestCase;

class PoPdfParserTest extends TestCase
{
    private PoPdfParserService $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new PoPdfParserService();
    }

    public function test_it_detects_conforme_for_rustans_template(): void
    {
        $rustansSampleText = <<<TEXT
PURCHASE ORDER
SUPPLIER NAME: N4870 HUENICS INDUSTRIAL SALES INC.
PO#: 1000059500
DATE: 08/19/2026
PAYMENT TERMS: 30 Days
DELIVER TO: RUSTAN'S ALABANG
DELIVERY DATE: 08/19/2026

ITEM CODE ITEM DESCRIPTION QUANTITY UOM UNIT PRICE TOTAL
R0000009 Led AR111 bulb 12w,3000K 50 PCS 1,175.00 58,750.00

TOTAL PHP 58,750.00
Terms and Conditions:
1. A duly approved Purchase Order (PO) will be issued by the RCC Buyer...
8. RCC reserves the right to require the Supplier to return all documents...
I HEREBY ACCEPT THE TERMS AND CONDITIONS STATED ABOVE
Emmanuel Joshua B. Serrano
SIGNATURE OVER PRINTED NAME
DATE: 08/19/2026
TEXT;

        $isConforme = $this->parser->detectConforme($rustansSampleText);
        $this->assertTrue($isConforme, 'Should detect Conforme for Rustans PO');

        $poNumber = $this->parser->extractPoNumber($rustansSampleText);
        $this->assertEquals('1000059500', $poNumber);

        $conformeDetails = $this->parser->extractConformeDetails($rustansSampleText);
        $this->assertNotEmpty($conformeDetails['signatory']);
        $this->assertStringContainsString('Emmanuel Joshua', $conformeDetails['signatory']);

        $totals = $this->parser->extractTotals($rustansSampleText);
        $this->assertEquals(58750.00, $totals['total']);
    }

    public function test_it_detects_conforme_for_will_decena_template(): void
    {
        $wdaSampleText = <<<TEXT
WILL DECENA & ASSOCIATES, INC.
PURCHASE ORDER
WDAI No. 00092086
Date 08/15/2026
Supplier: HUENICS INDUSTRIAL SALES INC
Address: 1552 Diamante Street cor Zapiro Street San Andres Bukid Manila
Delivery Date: 08/22/2026
Delivery To: 4/F Rose Building No. 73 West Avenue, Quezon City
TERMS OF PAYMENT: 30 days PDC Inclusive of VAT

1 Box type surface mounted 10 pc 160.71 1,607.14
2 Crompton Led T8 Tube 18W 10 pc 200.89 2,008.93
Sub-Total: 3,616.07
VAT: 433.93
TOTAL 4,050.00

Conforme:
Please indicate your Business Name HUENICS INDUSTRIAL SALES INC.
Date: 8/18/2026
CHARMAYNE B. SERRANO
Print name under Signature
TEXT;

        $isConforme = $this->parser->detectConforme($wdaSampleText);
        $this->assertTrue($isConforme, 'Should detect Conforme for WDA PO');

        $poNumber = $this->parser->extractPoNumber($wdaSampleText);
        $this->assertEquals('00092086', $poNumber);

        $conformeDetails = $this->parser->extractConformeDetails($wdaSampleText);
        $this->assertNotEmpty($conformeDetails['signatory']);
        $this->assertStringContainsString('CHARMAYNE', $conformeDetails['signatory']);
        $this->assertStringContainsString('HUENICS', $conformeDetails['business_name']);

        $totals = $this->parser->extractTotals($wdaSampleText);
        $this->assertEquals(4050.00, $totals['total']);
        $this->assertEquals(3616.07, $totals['subtotal']);
        $this->assertEquals(433.93, $totals['tax']);
    }

    public function test_it_recognizes_standard_po_without_conforme(): void
    {
        $normalPoText = <<<TEXT
ABC BAKERY CORP
PURCHASE ORDER
PO Number: PO-20260826-9988
Date: 08/26/2026
Supplier: Premium Flour Co.
Expected Delivery: 08/30/2026
Payment Terms: Net 30

Flour Premium All-Purpose 100 kg 50.00 5,000.00
TOTAL 5,000.00
TEXT;

        $isConforme = $this->parser->detectConforme($normalPoText);
        $this->assertFalse($isConforme, 'Should recognize standard PO without Conforme as non-conforme');

        $poNumber = $this->parser->extractPoNumber($normalPoText);
        $this->assertEquals('PO-20260826-9988', $poNumber);
    }
}
