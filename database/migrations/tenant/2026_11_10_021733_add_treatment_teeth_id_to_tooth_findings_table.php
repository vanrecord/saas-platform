<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tooth_findings', function (Blueprint $table) {
            $table->bigInteger('treatment_teeth_id')->nullable();
            $table->foreign('treatment_teeth_id')
                ->references('id')
                ->on('treatment_teeths')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tooth_findings', function (Blueprint $table) {
            $table->dropColumn(['treatment_teeth_id']);
        });
    }
};
