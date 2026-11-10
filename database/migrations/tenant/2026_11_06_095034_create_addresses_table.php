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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('capital')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('country_code', 3);
            $table->string('currency')->nullable();
            $table->string('currency_code')->nullable();
            $table->string('currency_sub_unit')->nullable();
            $table->string('currency_symbol', 3)->nullable();
            $table->string('full_name')->nullable();
            $table->string('iso_3166_2', 2);
            $table->string('iso_3166_3', 3);
            $table->string('name');
            $table->string('region_code', 3);
            $table->string('sub_region_code', 3);
            $table->boolean('eea')->default(false);
            $table->string('calling_code', 3)->nullable();
            $table->string('flag', 6)->nullable();
        });
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->integer('calling_code')->nullable();#country_code
            $table->string('prefix_code')->nullable();
            $table->string('name_kh');
            $table->string('name_en');
            $table->double('latitude')->nullable();
            $table->double('longtitude')->nullable();
            $table->string('province_type_kh')->nullable();
            $table->string('province_type_en')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('name_kh');
            $table->string('name_en');
            $table->string('type')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longtitude')->nullable();
            $table->string('postal')->nullable();
            $table->integer('province_id');
            $table->string('official_note',255)->nullable();
            $table->string('reference',255)->nullable();
            $table->string('description')->nullable();
            $table->index(['province_id', 'name_en','name_kh']);
            $table->timestamps();
            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->nullable()
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('name_kh');
            $table->string('name_en');
            $table->string('type')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longtitude')->nullable();
            $table->integer('district_id');
            $table->string('official_note',255)->nullable();
            $table->string('reference',255)->nullable();
            $table->string('description')->nullable();
            $table->index(['district_id', 'name_en','name_kh']);
            $table->timestamps();
            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->nullable()
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
        
        Schema::create('villages', function (Blueprint $table) {
            $table->id();

            $table->string('code')->nullable()->unique();
            $table->string('name_kh');
            $table->string('name_en');
            $table->string('type')->nullable();

            $table->double('latitude')->nullable();
            $table->double('longtitude')->nullable();

            $table->unsignedBigInteger('commune_id')->nullable();

            $table->string('official_note', 255)->nullable();
            $table->string('description')->nullable();
            $table->string('reference', 255)->nullable();

            $table->index(['commune_id', 'name_en', 'name_kh']);

            $table->timestamps();

            // Foreign key
            $table->foreign('commune_id')
                ->references('id')
                ->on('communes')
                ->onDelete('cascade')
                ->onUpdate('no action');
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('house_number', 10)->nullable();
            $table->string('street', 32)->nullable();
            $table->string('city', 32)->nullable();
            $table->string('zip_code', 6)->nullable();
            $table->integer('province_id');
            $table->integer('country_id');
            $table->integer('commune_id');
            $table->integer('district_id');
            $table->integer('village_id');
            $table->integer('addressable_id');
            $table->string('addressable_type');
            $table->timestamps();
            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('no action');
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->nullable()
                ->onDelete('no action');
            $table->foreign('commune_id')
                ->references('id')
                ->nullable()
                ->on('communes')
                ->onDelete('no action');
            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->nullable()
                ->onDelete('no action');
            $table->foreign('village_id')
                ->references('id')
                ->on('villages')
                ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('villages');
        Schema::dropIfExists('communes');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
    }
};
