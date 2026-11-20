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
        Schema::create('estimate_teeths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_detail_id')->index();
            $table->string('teeth_number', 50)->nullable();
            $table->boolean('class_one')->default(false);
            $table->boolean('class_two')->default(false);
            $table->boolean('class_three')->default(false);
            $table->boolean('class_four')->default(false);
            $table->boolean('class_five')->default(false);
            $table->boolean('class_six')->default(false);
            $table->string('tooth_type', 50)->nullable();
            $table->string('tooth_status', 50)->nullable();
            $table->timestamps();

            $table->foreign('estimate_detail_id')
                ->references('id')
                ->on('estimate_details')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_teeths');
    }
};
