<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchase_requisition_items', function (Blueprint $table) {
            $table->foreignId('uom_id')->nullable()->after('item_id')->constrained('unit_of_measures')->restrictOnDelete();
        });
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->foreignId('uom_id')->nullable()->after('item_id')->constrained('unit_of_measures')->restrictOnDelete();
        });
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->foreignId('received_uom_id')->nullable()->after('purchase_order_item_id')->constrained('unit_of_measures')->restrictOnDelete();
        });
        Schema::table('supplier_invoice_items', function (Blueprint $table) {
            $table->foreignId('uom_id')->nullable()->after('item_id')->constrained('unit_of_measures')->restrictOnDelete();
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('igst_amount', 15, 2)->default(0);
        });
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->decimal('cgst_total', 15, 2)->default(0);
            $table->decimal('sgst_total', 15, 2)->default(0);
            $table->decimal('igst_total', 15, 2)->default(0);
        });
        DB::statement('UPDATE purchase_requisition_items pri JOIN items i ON i.id=pri.item_id SET pri.uom_id=i.base_uom_id WHERE pri.uom_id IS NULL');
        DB::statement('UPDATE purchase_order_items poi JOIN purchase_requisition_items pri ON pri.id=poi.purchase_requisition_item_id SET poi.uom_id=pri.uom_id WHERE poi.uom_id IS NULL');
        DB::statement('UPDATE supplier_invoice_items sii JOIN goods_receipt_items gri ON gri.id=sii.goods_receipt_item_id JOIN purchase_order_items poi ON poi.id=gri.purchase_order_item_id SET sii.uom_id=poi.uom_id WHERE sii.uom_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('supplier_invoices', fn (Blueprint $table) => $table->dropColumn(['cgst_total', 'sgst_total', 'igst_total']));
        Schema::table('supplier_invoice_items', function (Blueprint $table) { $table->dropForeign(['uom_id']); $table->dropColumn(['uom_id', 'cgst_amount', 'sgst_amount', 'igst_amount']); });
        Schema::table('goods_receipt_items', function (Blueprint $table) { $table->dropForeign(['received_uom_id']); $table->dropColumn('received_uom_id'); });
        Schema::table('purchase_order_items', function (Blueprint $table) { $table->dropForeign(['uom_id']); $table->dropColumn('uom_id'); });
        Schema::table('purchase_requisition_items', function (Blueprint $table) { $table->dropForeign(['uom_id']); $table->dropColumn('uom_id'); });
    }
};
