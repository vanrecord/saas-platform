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
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('doctor_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('patient_id')->index();

            $table->string('diagnosis', 255)->nullable();
            $table->string('medical_alert', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('reason')->nullable();

            $table->double('rate')->nullable();
            $table->double('discount_invoice')->default(0);

            $table->timestamp('estimate_at')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->boolean('is_completed')->default(false);
            $table->integer('status')->nullable()->index();
            $table->integer('employee_id')->default(0)->index();
            $table->boolean('term')->default(false);

            $table->unsignedInteger('approved_by')->nullable()->index();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('closed_by')->nullable()->index();
            $table->unsignedInteger('rejected_by')->nullable()->index();
            $table->integer('code')->nullable()->index();
            $table->string('discount_inv_as', 255)->nullable();
            $table->string('chief_complaint', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('closed_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('rejected_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
