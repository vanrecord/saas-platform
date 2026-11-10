<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create specialties table
        if (!Schema::hasTable('specialties')) {
            Schema::create('specialties', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Create doctor_specialties pivot table
        if (!Schema::hasTable('doctor_specialties')) {
            Schema::create('doctor_specialties', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('doctor_id');
                $table->unsignedBigInteger('specialty_id');
                $table->timestamps();

                $table->foreign('doctor_id')
                    ->references('id')->on('doctors')
                    ->onDelete('cascade');

                $table->foreign('specialty_id')
                    ->references('id')->on('specialties')
                    ->onDelete('cascade');

                $table->unique(['doctor_id', 'specialty_id']);
            });
        }

        // 3. Seed standard specialties if empty
        $existingCount = DB::table('specialties')->count();
        if ($existingCount === 0) {
            $defaultSpecialties = [
                ['name' => 'General Dentistry', 'code' => 'GEN-DENT', 'description' => 'General dental care and checkups.'],
                ['name' => 'Orthodontics', 'code' => 'ORTHO', 'description' => 'Alignment of teeth and dental braces.'],
                ['name' => 'Endodontics', 'code' => 'ENDO', 'description' => 'Root canal therapy and dental pulp care.'],
                ['name' => 'Periodontics', 'code' => 'PERIO', 'description' => 'Gum disease treatment and dental implants.'],
                ['name' => 'Prosthodontics', 'code' => 'PROSTHO', 'description' => 'Dentures, crowns, bridges, and dental restorations.'],
                ['name' => 'Oral & Maxillofacial Surgery', 'code' => 'ORAL-SURG', 'description' => 'Tooth extractions and oral surgery.'],
                ['name' => 'Pediatric Dentistry', 'code' => 'PED-DENT', 'description' => 'Dental treatment for children.'],
                ['name' => 'Cosmetic Dentistry', 'code' => 'COSM-DENT', 'description' => 'Teeth whitening and veneers.'],
                ['name' => 'Implantology', 'code' => 'IMPLANT', 'description' => 'Dental implant surgical placement.'],
                ['name' => 'General Medicine', 'code' => 'GEN-MED', 'description' => 'Primary general medical practice.'],
            ];

            foreach ($defaultSpecialties as $spec) {
                DB::table('specialties')->insert([
                    'name' => $spec['name'],
                    'code' => $spec['code'],
                    'description' => $spec['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 4. Seed doctor permissions for tenant guard
        $doctorPermissions = [
            'doctor:view',
            'doctor:create',
            'doctor:edit',
            'doctor:save',
            'doctor:delete',
        ];

        foreach ($doctorPermissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'tenant',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_specialties');
        Schema::dropIfExists('specialties');

        Permission::where('guard_name', 'tenant')
            ->whereIn('name', [
                'doctor:view',
                'doctor:create',
                'doctor:edit',
                'doctor:save',
                'doctor:delete',
            ])
            ->delete();
    }
};
