
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('purchase_orders', 'budget_committed_amount')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->decimal('budget_committed_amount', 15, 2)
                    ->default(0)
                    ->after('status');
            });
        }
    }

    public function down(): void
    {
        // Preserve existing PO data.
    }
};
