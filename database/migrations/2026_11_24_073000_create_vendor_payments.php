<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vendor_payments')) Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('paid_by')->constrained('users')->restrictOnDelete();
            $table->string('payment_number')->unique();
            $table->date('payment_date');
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->string('reference')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
        if (!Schema::hasTable('vendor_payment_allocations')) Schema::create('vendor_payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_invoice_id')->constrained()->restrictOnDelete();
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['vendor_payment_id', 'supplier_invoice_id'], 'vpa_payment_invoice_unique');
        });
        // MySQL may leave tables behind when a migration fails after DDL has
        // committed. Complete the missing index safely on a retry.
        $indexes = collect(DB::select('SHOW INDEX FROM vendor_payment_allocations'))
            ->pluck('Key_name')->all();
        if (!in_array('vpa_payment_invoice_unique', $indexes, true)) {
            Schema::table('vendor_payment_allocations', function (Blueprint $table) {
                $table->unique(['vendor_payment_id', 'supplier_invoice_id'], 'vpa_payment_invoice_unique');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('vendor_payment_allocations'); Schema::dropIfExists('vendor_payments'); }
};
