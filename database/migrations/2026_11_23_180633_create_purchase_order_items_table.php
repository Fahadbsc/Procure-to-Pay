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
    Schema::create('purchase_order_items', function (Blueprint $table) {
        $table->id();

        $table->foreignId('purchase_order_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('purchase_requisition_item_id')
            ->constrained('purchase_requisition_items')
            ->restrictOnDelete();

        $table->foreignId('item_id')
            ->constrained('items')
            ->restrictOnDelete();

        $table->decimal('quantity', 12, 2);

        $table->decimal('unit_price', 12, 2);

        $table->decimal('gst_rate', 5, 2)->default(0);

        $table->text('remarks')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('purchase_order_items');
}

};
