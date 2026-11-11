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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('print')->default(false);
            $table->boolean('is_sponsor')->default(false);
            $table->string('provider')->nullable();
            $table->string('order')->nullable();
            $table->text('datas')->nullable();
            $table->string('register_ref')->nullable();
            $table->string('currency')->nullable();
            $table->string('partner_name')->nullable();
            $table->boolean('is_disable')->default(false);
            $table->boolean('is_activate')->default(false);
            $table->boolean('is_default_payment')->default(false);
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
