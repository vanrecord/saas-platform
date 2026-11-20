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
        Schema::create('estimate_tooth_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dental_condition_id')->nullable()->constrained('dental_conditions')->nullOnDelete();
            $table->string('surface', 12); // top, bottom, left, right, center, whole
            $table->string('custom_color', 7)->nullable();
            $table->text('note')->nullable();
            $table->string('treatment_status')->default('observed');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->bigInteger('estimate_teeth_id')->nullable();
            $table->foreign('estimate_teeth_id')
                ->references('id')
                ->on('estimate_teeths')
                ->onDelete('cascade')
                ->onUpdate('no action');
            $table->timestamps();
            $table->softDeletes();
            $table->index([ 'surface']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_tooth_findings');
    }
};
