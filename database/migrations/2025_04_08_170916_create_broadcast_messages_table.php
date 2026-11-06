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
        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->integer('status')->nullable();
            $table->json('data')->nullable();
            $table->json('category_id')->nullable();
            $table->string('media_location')->nullable();
            $table->integer('type')->default(1)->comment('1 => telegram, 2 => ehealth');
            $table->integer('visibility')->nullable();
            $table->integer('module')->nullable();
            $table->boolean('allow_accept')->default(false);
            $table->string('tenant_id')->nullable();
            $table->integer('broadcast_message_tenant_id')->nullable();
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
        Schema::dropIfExists('broadcast_messages');
    }
};
