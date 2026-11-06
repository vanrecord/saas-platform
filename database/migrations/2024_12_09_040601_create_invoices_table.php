<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->index()->nullable()->constrained('subscriptions')->onUpdate('cascade')->onDelete('cascade');
            $table->string('code')->nullable();
            $table->bigInteger('total')->nullable();
            $table->bigInteger('unit_price')->nullable();
            $table->bigInteger('discount')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('currency')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->enum('billing_interval', ['monthly', 'yearly', 'quarterly']);
            $table->integer('qty')->nullable();
            $table->foreignId('paid_by')->index()->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('paid_at')->nullable();
            $table->string('paid_name')->nullable();
            $table->dateTime('invoiced_at')->nullable();
            $table->string('payment_type')->nullable();
            $table->boolean('is_bank')->nullable();
            $table->bigInteger('year_series_code')->nullable();
            $table->string('prefix')->nullable();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('created_by')->index()->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->index()->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
