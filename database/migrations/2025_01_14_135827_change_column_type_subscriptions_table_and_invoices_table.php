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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('total')->nullable()->change();
            $table->decimal('unit_price')->nullable()->change();
            $table->decimal('discount')->nullable()->change();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('total')->nullable()->change();
            $table->decimal('unit_price')->nullable()->change();
            $table->decimal('discount')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->bigInteger('total')->nullable()->change();
            $table->bigInteger('unit_price')->nullable()->change();
            $table->bigInteger('discount')->nullable()->change();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->bigInteger('total')->nullable()->change();
            $table->bigInteger('unit_price')->nullable()->change();
            $table->bigInteger('discount')->nullable()->change();
        });
    }
};
