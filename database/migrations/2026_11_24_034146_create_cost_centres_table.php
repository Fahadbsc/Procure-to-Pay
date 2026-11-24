
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cost_centres')) {
            Schema::create('cost_centres', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('code', 20);
                $table->string('name');
                $table->boolean('is_active')->default(true);

                $table->timestamps();

                $table->unique(['company_id', 'code']);
            });
        }
    }

    public function down(): void
    {
        // Existing table must not be deleted.
    }
};
