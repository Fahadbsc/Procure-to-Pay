<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  
public function up(): void
{
    Schema::create('vendors', function (Blueprint $table) {
        $table->id();

        $table->foreignId('company_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('code', 20);
        $table->string('name');
        $table->string('gstin', 15);
        $table->string('state');
        $table->unsignedInteger('payment_terms_days')->default(30);
        $table->boolean('is_active')->default(true);

        $table->timestamps();

        $table->unique(['company_id', 'code']);
        $table->unique(['company_id', 'gstin']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
