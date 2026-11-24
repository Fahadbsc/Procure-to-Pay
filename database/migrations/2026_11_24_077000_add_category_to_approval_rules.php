<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('approval_rules', fn(Blueprint $table) => $table->string('category',30)->nullable()->after('document_type')); }
    public function down(): void { Schema::table('approval_rules', fn(Blueprint $table) => $table->dropColumn('category')); }
};
