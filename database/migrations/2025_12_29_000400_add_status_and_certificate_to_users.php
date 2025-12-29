<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('status', ['pending','active','rejected'])->default('active')->after('role')->index();
            });
        }
        if (!Schema::hasColumn('users', 'certificate_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('certificate_path', 500)->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'certificate_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('certificate_path');
            });
        }
        if (Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            });
        }
    }
};
