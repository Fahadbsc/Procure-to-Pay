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
    Schema::create('purchase_requisitions', function (Blueprint $table) {
        $table->id();

        $table->foreignId('company_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('pr_number')->unique();

        $table->foreignId('requested_by')
            ->constrained('users');

        $table->date('required_date');

        $table->text('purpose')->nullable();

        $table->enum('status', [
            'DRAFT',
            'PENDING',
            'APPROVED',
            'REJECTED',
        ])->default('DRAFT');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};
