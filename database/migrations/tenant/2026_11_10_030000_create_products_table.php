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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->unsignedInteger('hospital_id');
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->bigInteger('code')->nullable();
            $table->double('price')->default(0);
            $table->double('cost')->default(0);
            $table->integer('qty')->default(0);
            $table->boolean('status')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospitals')
                ->onDelete('cascade');

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('set null');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
