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
        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->nullable();
            $table->foreignId('client_id')->nullable();

            $table->foreignId('patient_id')
                ->nullable()
                ->constrained('patients')
                ->noActionOnDelete();

            $table->smallInteger('status')->default(0);
            $table->double('amount')->default(0);
            $table->double('base_amount')->default(0);
            $table->string('currency', 3)->nullable();
            $table->double('rate')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->noActionOnDelete();
            $table->foreignId('verify_by')
                ->nullable()
                ->constrained('users')
                ->noActionOnDelete();
            $table->text('note')->nullable();
            $table->boolean('is_cash')->default(true);
            $table->boolean('is_verify')->default(false);
            $table->timestamp('date')->nullable();
            $table->timestamps();
            $table->boolean('is_ehealth')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_transfers');
    }
};
