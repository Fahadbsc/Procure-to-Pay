<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('purchase_requisitions', 'cost_centre_id')) {
            Schema::table('purchase_requisitions', function (Blueprint $table) {
                $table->foreignId('cost_centre_id')
                    ->nullable()
                    ->after('company_id')
                    ->constrained('cost_centres')
                    ->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Existing PR data protection: do not drop this column.
    }
};