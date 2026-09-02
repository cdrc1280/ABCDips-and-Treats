<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create Quotations Table
        if (!Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->string('quotation_number', 32)->unique();
                $table->string('client_name')->nullable();
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
                $table->string('status')->default('draft'); // draft, sent, accepted, rejected, converted_to_po
                $table->date('quotation_date')->nullable();
                $table->date('valid_until')->nullable();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('tax', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Create Quotation Items Table
        if (!Schema::hasTable('quotation_items')) {
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
                $table->foreignId('ingredient_id')->nullable()->constrained('ingredients')->nullOnDelete();
                $table->string('item_description')->nullable();
                $table->decimal('qty', 12, 3)->default(1);
                $table->string('unit')->default('pcs');
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        // 3. Enhance Purchase Orders Table with Conforme and DR/SI Fields
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'quotation_id')) {
                $table->foreignId('quotation_id')->nullable()->after('supplier_id')->constrained('quotations')->nullOnDelete();
            }
            if (!Schema::hasColumn('purchase_orders', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('quotation_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'po_type')) {
                $table->string('po_type')->default('normal')->after('pdf_path'); // normal, conforme
            }
            if (!Schema::hasColumn('purchase_orders', 'is_conforme')) {
                $table->boolean('is_conforme')->default(false)->after('po_type');
            }
            if (!Schema::hasColumn('purchase_orders', 'is_signature_verified')) {
                $table->boolean('is_signature_verified')->default(false)->after('is_conforme');
            }
            if (!Schema::hasColumn('purchase_orders', 'conforme_signatory')) {
                $table->string('conforme_signatory')->nullable()->after('is_signature_verified');
            }
            if (!Schema::hasColumn('purchase_orders', 'conforme_business_name')) {
                $table->string('conforme_business_name')->nullable()->after('conforme_signatory');
            }
            if (!Schema::hasColumn('purchase_orders', 'conforme_date')) {
                $table->date('conforme_date')->nullable()->after('conforme_business_name');
            }
            if (!Schema::hasColumn('purchase_orders', 'payment_terms')) {
                $table->string('payment_terms')->nullable()->after('conforme_date');
            }
            if (!Schema::hasColumn('purchase_orders', 'delivery_receipt_no')) {
                $table->string('delivery_receipt_no')->nullable()->after('payment_terms');
            }
            if (!Schema::hasColumn('purchase_orders', 'sales_invoice_no')) {
                $table->string('sales_invoice_no')->nullable()->after('delivery_receipt_no');
            }
            if (!Schema::hasColumn('purchase_orders', 'dr_issued_at')) {
                $table->dateTime('dr_issued_at')->nullable()->after('sales_invoice_no');
            }
            if (!Schema::hasColumn('purchase_orders', 'si_issued_at')) {
                $table->dateTime('si_issued_at')->nullable()->after('dr_issued_at');
            }
            if (!Schema::hasColumn('purchase_orders', 'delivered_at')) {
                $table->dateTime('delivered_at')->nullable()->after('si_issued_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropColumn([
                'quotation_id',
                'pdf_path',
                'po_type',
                'is_conforme',
                'is_signature_verified',
                'conforme_signatory',
                'conforme_business_name',
                'conforme_date',
                'payment_terms',
                'delivery_receipt_no',
                'sales_invoice_no',
                'dr_issued_at',
                'si_issued_at',
                'delivered_at',
            ]);
        });

        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
