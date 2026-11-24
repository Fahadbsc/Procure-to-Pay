<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE supplier_invoices MODIFY COLUMN status ENUM(
            'DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED', 'PAID', 'POSTED', 'MATCHED', 'ON_HOLD', 'PARTIALLY_PAID'
        ) NOT NULL DEFAULT 'DRAFT'");
    }

    public function down(): void
    {
        // Keep the status available so existing partially paid invoices remain valid.
    }
};
