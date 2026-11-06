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
        Schema::create('provinces', function(Blueprint $table)
        {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->integer('calling_code')->nullable();#country_code
            $table->string('prefix_code')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_kh')->nullable();
            $table->string('province_type_kh')->nullable();
            $table->string('province_type_en')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->nullable()->default(true);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->string('code')->nullable()->unique();
            $table->string('name_en')->nullable();
            $table->string('name_kh')->nullable();
            $table->string('type')->nullable();
            $table->string('reference',255)->nullable();
            $table->string('official_note',255)->nullable();
            $table->text('description')->nullable();
            $table->index(['province_id', 'name_en','name_kh']);
            $table->boolean('is_active')->nullable()->default(true);

            $table->softDeletes();
            $table->timestamps();

        });

        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->string('name_en')->nullable();
            $table->string('name_kh')->nullable();
            $table->string('type')->nullable();
            $table->string('reference',255)->nullable();
            $table->string('official_note',255)->nullable();
            $table->text('description')->nullable();
            $table->index(['district_id', 'name_en','name_kh']);
            $table->boolean('is_active')->nullable()->default(true);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->string('name_en')->nullable();
            $table->string('name_kh')->nullable();
            $table->string('type')->nullable();
            $table->string('reference',255)->nullable();
            $table->string('official_note',255)->nullable();
            $table->string('code')->nullable()->unique();
            $table->index(['commune_id', 'name_en','name_kh']);
            $table->boolean('is_active')->nullable()->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('communes');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
    }
};
