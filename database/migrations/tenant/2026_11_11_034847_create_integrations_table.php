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
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->integer('medical_history_id')->nullable();
            $table->string('transaction_number')->nullable();
            $table->string('currency', 255)->nullable();
            $table->double('amount')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
            $table->integer('payment_method_id')->nullable();
            $table->double('rate')->nullable();
            $table->string('id_rnd', 255)->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('no action')
                ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
