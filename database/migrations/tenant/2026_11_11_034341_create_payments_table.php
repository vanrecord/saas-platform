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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id')->nullable();
            $table->integer('medical_history_id')->nullable();
            $table->timestamp('date');
            $table->double('amount');
            $table->integer('amount_kh')->default(0);
            $table->integer('payment_method_id')->nullable();
            $table->double('credit_balance')->nullable();
            $table->double('deposit_balance')->nullable();
            $table->string('txnref')->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
