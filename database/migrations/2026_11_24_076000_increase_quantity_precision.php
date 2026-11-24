<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement('ALTER TABLE purchase_requisition_items MODIFY quantity DECIMAL(14,4) NOT NULL');
        DB::statement('ALTER TABLE purchase_order_items MODIFY quantity DECIMAL(14,4) NOT NULL');
        DB::statement('ALTER TABLE goods_receipt_items MODIFY received_quantity DECIMAL(14,4) NOT NULL, MODIFY accepted_quantity DECIMAL(14,4) NOT NULL, MODIFY rejected_quantity DECIMAL(14,4) NOT NULL');
        DB::statement('ALTER TABLE supplier_invoice_items MODIFY quantity DECIMAL(14,4) NOT NULL');
    }
    public function down(): void {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement('ALTER TABLE purchase_requisition_items MODIFY quantity DECIMAL(12,2) NOT NULL');
        DB::statement('ALTER TABLE purchase_order_items MODIFY quantity DECIMAL(12,2) NOT NULL');
        DB::statement('ALTER TABLE goods_receipt_items MODIFY received_quantity DECIMAL(12,2) NOT NULL, MODIFY accepted_quantity DECIMAL(12,2) NOT NULL, MODIFY rejected_quantity DECIMAL(12,2) NOT NULL');
        DB::statement('ALTER TABLE supplier_invoice_items MODIFY quantity DECIMAL(12,2) NOT NULL');
    }
};
