<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend users with optional profile fields
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone', 50)->nullable();
                $table->string('address', 255)->nullable();
                $table->string('specialty', 120)->nullable();
                $table->text('bio')->nullable();
                $table->text('case_categories')->nullable();
            });
        }

        // Facilities
        if (!Schema::hasTable('facilities')) {
            Schema::create('facilities', function (Blueprint $table) {
                $table->id();
                $table->string('name', 200);
                $table->string('type', 30); // cabinet, hospital, clinic
                $table->string('address', 255)->nullable();
                $table->string('city', 120)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
                $table->index(['type', 'name']);
            });
        }

        // Doctor to Facility pivot
        if (!Schema::hasTable('doctor_facility')) {
            Schema::create('doctor_facility', function (Blueprint $table) {
                $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
                $table->primary(['doctor_id', 'facility_id']);
            });
        }

        // Hospitalizations (Medical History)
        if (!Schema::hasTable('hospitalizations')) {
            Schema::create('hospitalizations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('facility_id')->nullable()->constrained('facilities')->nullOnDelete();
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'start_date']);
            });
        }

        // Clinical notes (by doctor about patient)
        if (!Schema::hasTable('clinical_notes')) {
            Schema::create('clinical_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
                $table->text('content');
                $table->timestamps();
                $table->index(['doctor_id', 'patient_id']);
            });
        }

        // Medical Feed posts (doctor-only), with comments
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
                $table->text('content');
                $table->boolean('is_anonymous')->default(false);
                $table->timestamps();
                $table->index('doctor_id');
            });
        }

        if (!Schema::hasTable('post_comments')) {
            Schema::create('post_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
                $table->text('content');
                $table->timestamps();
                $table->index(['post_id', 'doctor_id']);
            });
        }

        // Support messages & feedback comments
        if (!Schema::hasTable('support_messages')) {
            Schema::create('support_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('subject', 200);
                $table->text('message');
                $table->boolean('is_private')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('feedback_comments')) {
            Schema::create('feedback_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('visibility', 20)->default('public'); // public or private
                $table->text('content');
                $table->timestamps();
                $table->index(['visibility']);
            });
        }

        // Appointments
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
                $table->string('requested_by', 20); // doctor or patient
                $table->string('status', 20)->default('pending'); // pending, accepted, declined, canceled, completed
                $table->dateTime('scheduled_at')->nullable();
                $table->text('reason')->nullable();
                $table->timestamps();
                $table->index(['doctor_id', 'patient_id', 'status']);
            });
        }

        // Private consultations: conversations and messages
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['doctor_id', 'patient_id']);
            });
        }

        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->text('content')->nullable();
                $table->string('attachment_path', 500)->nullable();
                $table->timestamps();
                $table->index(['conversation_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('feedback_comments');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('clinical_notes');
        Schema::dropIfExists('hospitalizations');
        Schema::dropIfExists('doctor_facility');
        Schema::dropIfExists('facilities');

        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['phone','address','specialty','bio','case_categories']);
            });
        }
    }
};
