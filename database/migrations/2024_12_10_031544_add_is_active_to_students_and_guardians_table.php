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
        Schema::table('students', function (Blueprint $table) {
            $table->string('sex')->nullable();
            $table->integer('ehealth_connection')->nullable();
            $table->boolean('is_active')->default(false);
        });
        Schema::table('guardians', function (Blueprint $table) {
            $table->integer('ehealth_connection')->nullable();
            $table->boolean('is_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['is_active','ehealth_connection','sex']);
        });
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropColumn(['is_active','ehealth_connection']);
        });
    }
};
