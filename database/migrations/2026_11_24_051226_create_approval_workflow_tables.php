
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Approval rules configured by company
        Schema::create('approval_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            // PR or PO
            $table->string('document_type', 20);

            // Optional cost centre restriction
            $table->foreignId('cost_centre_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('min_amount', 15, 2)->default(0);
            $table->decimal('max_amount', 15, 2)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'company_id',
                'document_type',
                'is_active'
            ], 'approval_rules_lookup_index');
        });

        // Ordered approvers configured in each rule
        Schema::create('approval_rule_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('approval_rule_id')
                ->constrained('approval_rules')
                ->cascadeOnDelete();

            $table->unsignedInteger('step_order');

            $table->foreignId('approver_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['approval_rule_id', 'step_order'],
                'approval_rule_step_order_unique'
            );
        });

        // Snapshot of the approval workflow for each document
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('document_type', 20);
            $table->unsignedBigInteger('document_id');

            $table->foreignId('requested_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedInteger('current_step')->default(1);

            $table->string('status', 20)->default('PENDING');

            $table->timestamps();

            $table->unique(
                ['document_type', 'document_id'],
                'approval_request_document_unique'
            );

            $table->index([
                'company_id',
                'status',
                'current_step'
            ], 'approval_request_status_index');
        });

        // Approver snapshot: later rule changes won't affect existing requests
        Schema::create('approval_request_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('approval_request_id')
                ->constrained('approval_requests')
                ->cascadeOnDelete();

            $table->unsignedInteger('step_order');

            $table->foreignId('approver_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('status', 20)->default('PENDING');

            $table->foreignId('acted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('acted_at')->nullable();

            $table->text('comments')->nullable();

            $table->timestamps();

            $table->unique(
                ['approval_request_id', 'step_order'],
                'approval_request_step_order_unique'
            );

            $table->index([
                'approver_user_id',
                'status'
            ], 'approval_request_approver_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_request_steps');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_rule_steps');
        Schema::dropIfExists('approval_rules');
    }
};
