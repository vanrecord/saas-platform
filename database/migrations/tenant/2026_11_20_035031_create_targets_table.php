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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->foreignId('doctor_id')
                ->nullable()
                ->constrained('doctors')
                ->cascadeOnDelete();

            $table->double('amount')->nullable();
            $table->boolean('by_doctor')->nullable();
            $table->string('description', 255)->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('hospital_id')
                ->nullable()
                ->constrained('hospitals')
                ->cascadeOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('doctor_id');
            $table->index('hospital_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
