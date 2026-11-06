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
        Schema::table('broadcast_messages', function (Blueprint $table) {
            $table->boolean('is_required_accept')->default(false);
            $table->timestamp('expire_accept_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('broadcast_messages', function (Blueprint $table) {
            $table->dropColumn(['is_required_accept', 'expire_accept_date']);
        });
    }
};
