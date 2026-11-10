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
        Schema::create('treatment_teeths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_history_id')->nullable();
            $table->unsignedBigInteger('medical_detail_id')->nullable();
            $table->string('teeth_number')->nullable();
            $table->boolean('class_one')->default(false);
            $table->boolean('class_two')->default(false);
            $table->boolean('class_three')->default(false);
            $table->boolean('class_four')->default(false);
            $table->boolean('class_five')->default(false);
            $table->boolean('class_six')->default(false);
            $table->string('tooth_type')->nullable();
            $table->string('tooth_status')->nullable();
            $table->timestamps();
            $table->foreign('medical_detail_id')
                ->references('id')
                ->on('medical_details')
                ->onDelete('cascade')
                ->onUpdate('no action');
            $table->foreign('medical_history_id')
                ->references('id')
                ->on('medical_histories')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_teeths');
    }
};
