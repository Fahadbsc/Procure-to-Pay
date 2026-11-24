<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('purchase_order_items', function(Blueprint $table){$table->foreignId('cost_centre_id')->nullable()->after('uom_id')->constrained()->restrictOnDelete();$table->decimal('budget_committed_amount',15,2)->nullable();}); }
    public function down(): void { Schema::table('purchase_order_items', function(Blueprint $table){$table->dropForeign(['cost_centre_id']);$table->dropColumn(['cost_centre_id','budget_committed_amount']);}); }
};
