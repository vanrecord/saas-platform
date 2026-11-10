<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable();
            $table->boolean('is_super_admin')->nullable()->default(false);
            $table->string('confirmation_code')->nullable();
            $table->string('settings')->nullable();
            $table->integer('hospital_id')->nullable();
            $table->bigInteger('e_health_physician_code')->nullable();
            $table->integer('level_access')->nullable();
            $table->boolean('is_api')->nullable()->default(false);
            $table->boolean('active')->nullable()->default(false);
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
            $table->dropColumn(["username",'is_super_admin','confirmation_code','settings','hospital_id','e_health_physician_code','level_access','is_api','active']);
        });
    }
}
