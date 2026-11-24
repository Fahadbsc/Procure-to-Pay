<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function(Blueprint $table){$table->foreignId('manager_id')->nullable()->after('company_id')->constrained('users')->nullOnDelete();});
        Schema::table('approval_request_steps', function(Blueprint $table){$table->timestamp('last_reminded_at')->nullable();});
    }
    public function down(): void {
        Schema::table('approval_request_steps', fn(Blueprint $table)=>$table->dropColumn('last_reminded_at'));
        Schema::table('users', function(Blueprint $table){$table->dropForeign(['manager_id']);$table->dropColumn('manager_id');});
    }
};
