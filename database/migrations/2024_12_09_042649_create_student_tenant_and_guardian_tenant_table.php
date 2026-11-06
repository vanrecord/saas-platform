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
        Schema::create('student_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index()->nullable()->constrained('students')->onUpdate('cascade')->onDelete('cascade');
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('guardian_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->index()->nullable()->constrained('guardians')->onUpdate('cascade')->onDelete('cascade');
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('student_id')->index()->nullable()->constrained('students')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_tenant');
        Schema::dropIfExists('guardian_tenant');
    }
};
