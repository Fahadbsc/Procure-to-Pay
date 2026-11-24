
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // First remove the foreign key
            $table->dropForeign([
                'purchase_requisition_id'
            ]);

            // Then remove the unique index
            $table->dropUnique(
                'purchase_orders_purchase_requisition_id_unique'
            );

            // Recreate foreign key without unique constraint
            $table->foreign('purchase_requisition_id')
                ->references('id')
                ->on('purchase_requisitions')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign([
                'purchase_requisition_id'
            ]);

            $table->unique('purchase_requisition_id');

            $table->foreign('purchase_requisition_id')
                ->references('id')
                ->on('purchase_requisitions')
                ->restrictOnDelete();
        });
    }
};
