<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->string('setting_key',100)->index()->unique('key');
			$table->text('setting_value',65535)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
