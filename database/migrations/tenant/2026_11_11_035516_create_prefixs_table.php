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
        Schema::create('prefixs', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_name', 255)->nullable();
            $table->string('prefix_format', 255)->nullable();
            $table->boolean('default_prefix')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefixs');
    }
};
