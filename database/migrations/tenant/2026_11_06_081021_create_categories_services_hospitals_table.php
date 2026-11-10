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
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('hospital_id');
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('code')->nullable();
            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospitals')
                ->onDelete('cascade');
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->unsignedInteger('hospital_id');
            $table->text('description');
            $table->double('price');
            $table->timestamps();
            $table->double('discount')->default(0);
            $table->string('type', 255)->nullable();
            $table->double('cost')->default(0)->nullable();
            $table->integer('qty')->default(0)->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->double('commission')->default(0)->nullable();
            $table->string('commission_type', 255)->nullable();

            $table->boolean('status')->default(true);

            $table->bigInteger('code')->nullable();

            $table->unsignedInteger('category_id')->nullable();
            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospitals')
                ->onDelete('cascade');

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('hospitals');
        Schema::dropIfExists('suppliers');
    }
    
};
