<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE purchase_orders MODIFY status ENUM('DRAFT','SENT','APPROVED','PARTIALLY_RECEIVED','RECEIVED','CLOSED','CANCELLED') NOT NULL DEFAULT 'DRAFT'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("UPDATE purchase_orders SET status='APPROVED' WHERE status IN ('PARTIALLY_RECEIVED','RECEIVED','CLOSED')");
        DB::statement("ALTER TABLE purchase_orders MODIFY status ENUM('DRAFT','SENT','APPROVED','CANCELLED') NOT NULL DEFAULT 'DRAFT'");
    }
};
