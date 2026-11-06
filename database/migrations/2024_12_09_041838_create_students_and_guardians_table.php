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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name_native')->nullable();
            $table->string('name_international')->nullable();
            $table->string('phone')->nullable();
            $table->string('ehealth_code')->nullable();
            $table->string('phone_line_1')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('created_by')->index()->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->index()->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name_native')->nullable();
            $table->string('name_international')->nullable();
            $table->string('phone')->nullable();
            $table->string('ehealth_code')->nullable();
            $table->string('phone_line_1')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('students');
        Schema::dropIfExists('guardians');
    }
};
