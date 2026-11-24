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
    Schema::create('goods_receipts', function (Blueprint $table) {
        $table->id();

        $table->foreignId('company_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('purchase_order_id')
            ->constrained('purchase_orders')
            ->restrictOnDelete();

        $table->string('gr_number')->unique();

        $table->date('received_date');

        $table->foreignId('received_by')
            ->constrained('users')
            ->restrictOnDelete();

        $table->enum('status', [
            'DRAFT',
            'COMPLETED',
        ])->default('DRAFT');

        $table->text('remarks')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('goods_receipts');
}

};
