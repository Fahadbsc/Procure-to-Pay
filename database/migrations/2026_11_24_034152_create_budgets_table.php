
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('cost_centre_id')
                ->constrained('cost_centres')
                ->cascadeOnDelete();

            // Example: 2026-27
            $table->string('financial_year', 9);

            $table->decimal('budget_amount', 15, 2)->default(0);
            $table->decimal('committed_amount', 15, 2)->default(0);
            $table->decimal('spent_amount', 15, 2)->default(0);

            $table->timestamps();

            $table->unique([
                'company_id',
                'cost_centre_id',
                'financial_year'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
