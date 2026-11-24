
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE purchase_orders
            MODIFY status ENUM(
                'DRAFT',
                'SENT',
                'APPROVED',
                'CANCELLED'
            ) NOT NULL DEFAULT 'DRAFT'
        ");
    }

    public function down(): void
    {
        // Keep existing PO statuses and data safe.
    }
};
