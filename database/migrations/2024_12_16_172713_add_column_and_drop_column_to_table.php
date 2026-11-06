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
            $table->dropColumn('billing_interval');
            $table->decimal('period')->nullable();
            $table->string('period_type')->nullable();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('billing_interval');
            $table->decimal('period')->nullable();
            $table->string('period_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['period', 'period_type']);
            $table->enum('billing_interval', ['monthly', 'yearly', 'quarterly'])->nullable();
        });
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('billing_interval', ['monthly', 'yearly', 'quarterly'])->nullable();
            $table->dropColumn(['period', 'period_type']);
        });
    }
};
