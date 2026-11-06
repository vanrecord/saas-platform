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
        $hasIndex = collect(Schema::getIndexes('sectors'))->pluck('name')->contains('sectors_code_unique');
        if ($hasIndex) {
            Schema::table('sectors', function (Blueprint $table) {
                $table->dropUnique('sectors_code_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
