<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {

            $table->foreignId('submitted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('paid_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->text('rejection_reason')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {

            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['paid_by']);

            $table->dropColumn([
                'submitted_by',
                'reviewed_by',
                'paid_by',
                'reviewed_at',
                'paid_at',
                'rejection_reason',
            ]);

        });
    }
};