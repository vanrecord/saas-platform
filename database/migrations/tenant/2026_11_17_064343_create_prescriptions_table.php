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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id')->nullable();
            $table->text('prescription')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('medical_history_id')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('medical_history_id')
                ->references('id')
                ->on('medical_histories')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
