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
        Schema::table('domains', function (Blueprint $table) {
            $table->nestedSet();
            $table->integer('package_id')->nullable();
            $table->integer('sector_id')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('allow_create_domain')->default(false)->nullable();
            $table->integer('range')->nullable();
            $table->string('latlng')->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('district_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn([
                'parent_id',
                'left',
                'right',
                'depth',
                'package_id',
                'sector_id',
                'phone',
                'allow_create_domain',
                'range',
                'latlng',
                'province_id',
                'district_id'
            ]);
        });
    }
};
