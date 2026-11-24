
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE supplier_invoices
            MODIFY status ENUM(
                'DRAFT',
                'SUBMITTED',
                'APPROVED',
                'REJECTED',
                'PAID',
                'POSTED'
            ) NOT NULL DEFAULT 'DRAFT'
        ");
    }

    public function down(): void
    {
        // Preserve existing invoice statuses and data.
    }
};
