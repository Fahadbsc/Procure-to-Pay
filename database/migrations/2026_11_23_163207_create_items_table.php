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
    Schema::create('items', function (Blueprint $table) {
        $table->id();

        $table->string('code')->unique();
        $table->string('name');

        $table->enum('category', [
            'RAW_MATERIAL',
            'IT',
            'SERVICES',
            'CONSUMABLES'
        ]);

        $table->foreignId('base_uom_id')
            ->constrained('unit_of_measures');

        $table->decimal('gst_rate', 5, 2)->default(0);

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
