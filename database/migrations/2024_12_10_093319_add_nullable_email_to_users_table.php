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
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique constraint on the email column
            $table->dropUnique(['email']);
        });

        Schema::table('users', function (Blueprint $table) {
            // Make the email column nullable
            $table->string('email')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            // Reapply the unique constraint
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique constraint on the email column
            $table->dropUnique(['email']);
        });

        Schema::table('users', function (Blueprint $table) {
            // Revert the email column to non-nullable
            $table->string('email')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            // Reapply the unique constraint
            $table->unique('email');
        });
    }
};
