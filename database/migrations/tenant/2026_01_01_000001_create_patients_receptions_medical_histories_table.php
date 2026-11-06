<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 50)->nullable();
            $table->string('lastname', 50)->nullable();
            $table->integer('age')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('avatar', 50)->nullable();
            $table->string('phone', 255)->nullable();
            $table->text('address')->nullable();
            $table->integer('id_number')->nullable()->unique();
            $table->string('email', 255)->nullable();
            $table->string('profession', 255)->nullable();
            $table->date('dob')->nullable();
            $table->double('balance')->nullable();
            $table->boolean('card_activated')->default(false);
            $table->text('card_code')->nullable();
            $table->boolean('is_trail_card')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->integer('hospital_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->string('reception_number', 255)->unique();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->timestamp('reception_date');
            $table->string('visit_type', 30)->default('initial');
            $table->text('chief_complaint')->nullable();
            $table->string('status', 30)->default('checked_in');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id')
                ->references('id')->on('patients')
                ->onDelete('cascade');

            $table->foreign('doctor_id')
                ->references('id')->on('doctors')
                ->onDelete('set null');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null');
        });
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->text('description')->nullable();
            $table->text('diagnosis')->nullable();
            $table->string('medical_alert', 255)->nullable();
            $table->double('rate')->nullable();
            $table->double('discount_invoice')->nullable()->default(0);
            $table->integer('status')->nullable();
            $table->timestamp('date')->nullable();
            $table->integer('employee_id')->default(0);
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('discount_inv_as', 255)->nullable();
            $table->boolean('term')->nullable()->default(false);
            $table->string('chief_complaint', 255)->nullable();
            $table->unsignedBigInteger('estimate_id')->nullable();
            $table->string('type', 255)->default('invoice');
            $table->integer('id_usage')->nullable()->unique();
            $table->integer('requested_by')->nullable();
            $table->string('vattin', 255)->nullable();
            $table->boolean('show_vattin')->default(false);
            $table->string('prefix_invoice', 255)->nullable()->default('INV');
            $table->integer('invoice_num_year_series')->nullable();
            $table->string('prefix_format', 255)->nullable();
            $table->integer('prefix_id')->nullable();
            $table->string('visit_type', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('doctor_id')
                ->references('id')->on('doctors')
                ->onDelete('cascade');

            $table->foreign('patient_id')
                ->references('id')->on('patients')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('receptions');
        Schema::dropIfExists('medical_histories');
    }
    
};
