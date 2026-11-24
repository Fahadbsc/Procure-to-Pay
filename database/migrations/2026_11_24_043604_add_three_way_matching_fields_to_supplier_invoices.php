
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add matching status and reason to existing invoice items
        Schema::table('supplier_invoice_items', function (Blueprint $table) {

            if (!Schema::hasColumn('supplier_invoice_items', 'match_status')) {
                $table->string('match_status', 20)
                    ->default('PENDING');
            }

            if (!Schema::hasColumn('supplier_invoice_items', 'match_reason')) {
                $table->text('match_reason')->nullable();
            }
        });

        // Preserve existing invoice statuses and add MATCHED, ON_HOLD
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE supplier_invoices
                MODIFY COLUMN status
                ENUM(
                    'DRAFT',
                    'SUBMITTED',
                    'APPROVED',
                    'REJECTED',
                    'PAID',
                    'POSTED',
                    'MATCHED',
                    'ON_HOLD'
                )
                NOT NULL DEFAULT 'DRAFT'
            ");
        }
    }

    public function down(): void
    {
        // Preserve existing invoice data and statuses.
    }
};
