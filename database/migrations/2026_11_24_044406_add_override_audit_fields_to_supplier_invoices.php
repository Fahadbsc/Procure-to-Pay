
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('supplier_invoices', 'override_comment')) {
                $table->text('override_comment')->nullable();
            }

            if (!Schema::hasColumn('supplier_invoices', 'overridden_by')) {
                $table->unsignedBigInteger('overridden_by')->nullable();
            }

            if (!Schema::hasColumn('supplier_invoices', 'overridden_at')) {
                $table->timestamp('overridden_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Preserve existing invoice data.
    }
};
