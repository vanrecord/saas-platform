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
        Schema::create('log_access_request_apis', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('request_method')->nullable();
            $table->text('request_url')->nullable();
            $table->json('request_param')->nullable();
            $table->json('response_data')->nullable();
            $table->json('response_message')->nullable();
            $table->integer('causer_id')->nullable();
            $table->string('causer_type')->nullable();
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
        Schema::dropIfExists('log_access_request_apis');
    }
};
