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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('gender')->nullable();
            $table->string('facebook_username')->nullable();
            $table->string('facebook')->nullable();
            $table->string('google_username')->nullable();
            $table->string('google')->nullable();
            $table->string('twitter_username')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkdin_username')->nullable();
            $table->string('linkdin')->nullable();
            $table->string('website')->nullable();
            $table->string('description')->nullable();
            $table->string('fax')->nullable();
            $table->string('primary_mobile')->nullable();
            $table->string('secondary_mobile')->nullable();
            $table->string('primary_telephone')->nullable();
            $table->string('secondary_telephone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->unsignedBigInteger('profileable_id');
            $table->string('profileable_type');
            $table->timestamps();
            $table->boolean('is_publish')->default(false);
            $table->boolean('ehealth_is_doctor_appointment')->default(false);
            $table->json('ehealth_consult_type')->nullable();
            $table->index(
                ['profileable_id', 'profileable_type'],
                'profiles_profileable_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
