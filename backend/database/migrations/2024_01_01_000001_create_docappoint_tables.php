<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->enum('role', ['patient', 'doctor', 'admin'])->default('patient');
            $table->enum('insurance_type', ['cnss', 'ramed', 'private', 'none'])->default('none');
            $table->string('avatar')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('specialty');
            $table->string('city');
            $table->string('license_number')->unique();
            $table->decimal('consultation_fee', 8, 2)->default(0);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('bio')->nullable();
            $table->json('available_days')->default('["mon","tue","wed","thu","fri"]');
            $table->unsignedSmallInteger('slot_duration')->default(30);
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->boolean('accepts_cnss')->default(true);
            $table->boolean('accepts_ramed')->default(false);
            $table->boolean('accepts_private')->default(true);
            $table->timestamps();

            $table->index(['status', 'city']);
            $table->index(['status', 'specialty']);
        });

        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_booked')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();

            $table->unique(['doctor_id', 'date', 'start_time']);
            $table->index(['doctor_id', 'date', 'is_booked']);
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('slot_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->enum('status', ['pending','confirmed','completed','cancelled','declined'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->enum('insurance_type', ['cnss','ramed','private','none'])->default('none');
            $table->unsignedTinyInteger('patient_rating')->nullable();
            $table->text('patient_review')->nullable();
            $table->decimal('fee', 8, 2)->default(0);
            $table->boolean('sms_sent')->default(false);
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();

            $table->index(['patient_id', 'status']);
            $table->index(['doctor_id', 'scheduled_at']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('slots');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
    }
};
