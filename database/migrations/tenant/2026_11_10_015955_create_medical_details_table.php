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
        Schema::create('medical_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_history_id');
            $table->unsignedBigInteger('detailable_id');
            $table->string('detailable_type', 255);

            $table->integer('quantity')->default(0);

            $table->double('unit_price')->default(0);
            $table->double('discount')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->date('date')->nullable();

            $table->unsignedBigInteger('service_id')->nullable();

            $table->double('amount')->nullable();

            $table->text('note')->nullable();
            $table->string('discount_as', 255)->nullable();

            $table->double('cost')->default(0)->nullable();
            $table->double('cost_amount')->default(0);

            $table->boolean('is_lock')->default(false);
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
        Schema::dropIfExists('medical_details');
    }
};
