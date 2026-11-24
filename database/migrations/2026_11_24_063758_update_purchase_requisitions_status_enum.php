
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE purchase_requisitions
            MODIFY status ENUM(
                'DRAFT',
                'PENDING',
                'SUBMITTED',
                'APPROVED',
                'REJECTED',
                'CONVERTED'
            ) NOT NULL DEFAULT 'DRAFT'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE purchase_requisitions
            SET status = 'PENDING'
            WHERE status IN ('SUBMITTED', 'CONVERTED')
        ");

        DB::statement("
            ALTER TABLE purchase_requisitions
            MODIFY status ENUM(
                'DRAFT',
                'PENDING',
                'APPROVED',
                'REJECTED'
            ) NOT NULL DEFAULT 'DRAFT'
        ");
    }
};
