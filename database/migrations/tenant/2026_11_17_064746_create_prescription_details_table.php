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
        Schema::create('prescription_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('medicine_id')->nullable();
            $table->unsignedInteger('prescription_id')->nullable();
            $table->double('dose')->default(1);
            $table->double('duration')->default(1);
            $table->double('dose_amount')->default(0);
            $table->boolean('morning')->default(false);
            $table->boolean('afternoon')->default(false);
            $table->boolean('evening')->default(false);
            $table->boolean('night')->default(false);
            $table->text('note')->nullable();
            $table->double('unit_dose')->default(0);
            $table->timestamps();

            $table->foreign('medicine_id')
                ->references('id')
                ->on('medicines')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('prescription_id')
                ->references('id')
                ->on('prescriptions')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_details');
    }
};
