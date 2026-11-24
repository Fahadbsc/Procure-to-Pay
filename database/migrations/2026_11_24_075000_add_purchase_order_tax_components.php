<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('purchase_order_items', function (Blueprint $table) { $table->decimal('cgst_amount',15,2)->default(0); $table->decimal('sgst_amount',15,2)->default(0); $table->decimal('igst_amount',15,2)->default(0); }); }
    public function down(): void { Schema::table('purchase_order_items', fn(Blueprint $table) => $table->dropColumn(['cgst_amount','sgst_amount','igst_amount'])); }
};
