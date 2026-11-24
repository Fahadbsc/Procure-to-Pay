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
    Schema::create('purchase_requisition_items', function (Blueprint $table) {
        $table->id();

        $table->foreignId('purchase_requisition_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('item_id')
            ->constrained('items');

        $table->decimal('quantity', 12, 2);

        $table->decimal('estimated_price', 12, 2)->default(0);

        $table->text('remarks')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
};
