<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('support_messages') && !Schema::hasColumn('support_messages', 'status')) {
            Schema::table('support_messages', function (Blueprint $table) {
                $table->string('status', 20)->default('open'); // open, closed
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('support_messages') && Schema::hasColumn('support_messages', 'status')) {
            Schema::table('support_messages', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
