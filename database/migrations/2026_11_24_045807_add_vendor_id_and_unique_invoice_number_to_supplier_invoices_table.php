
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('company_id');
        });

        // Existing invoices: get vendor_id through purchase_orders
        DB::statement("
            UPDATE supplier_invoices si
            INNER JOIN purchase_orders po
                ON po.id = si.purchase_order_id
            SET si.vendor_id = po.vendor_id
        ");

        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->restrictOnDelete();

            $table->unique(
                ['vendor_id', 'supplier_invoice_number'],
                'supplier_invoice_vendor_number_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->dropUnique('supplier_invoice_vendor_number_unique');
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
        });
    }
};
