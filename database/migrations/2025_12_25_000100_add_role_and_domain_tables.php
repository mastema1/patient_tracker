<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add role column to users if not exists
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('patient')->index();
            });
        }

        // seizure_logs table
        if (!Schema::hasTable('seizure_logs')) {
            Schema::create('seizure_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
                $table->dateTime('timestamp');
                $table->unsignedInteger('duration');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'timestamp']);
            });
        }

        // medical_files table
        if (!Schema::hasTable('medical_files')) {
            Schema::create('medical_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
                $table->string('file_path', 500);
                $table->dateTime('upload_date')->useCurrent();
                $table->timestamps();
                $table->index(['patient_id', 'upload_date']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('medical_files')) {
            Schema::dropIfExists('medical_files');
        }
        if (Schema::hasTable('seizure_logs')) {
            Schema::dropIfExists('seizure_logs');
        }
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            });
        }
    }
};
