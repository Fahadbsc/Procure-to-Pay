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
    Schema::create('purchase_orders', function (Blueprint $table) {
        $table->id();

        $table->foreignId('company_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('purchase_requisition_id')
            ->unique()
            ->constrained()
            ->restrictOnDelete();

        $table->foreignId('vendor_id')
            ->constrained()
            ->restrictOnDelete();

        $table->string('po_number')->unique();

        $table->date('order_date');

        $table->date('expected_delivery_date')->nullable();

        $table->enum('status', [
            'DRAFT',
            'SENT',
            'CANCELLED',
        ])->default('DRAFT');

        $table->text('terms_conditions')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('purchase_orders');
}

};
