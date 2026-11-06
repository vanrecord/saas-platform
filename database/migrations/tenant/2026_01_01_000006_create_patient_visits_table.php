<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('patient_visits')) {
            Schema::create('patient_visits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
                $table->string('visit_number')->unique();
                $table->string('workflow_option')->default('new_invoice'); // new_invoice, follow_up
                $table->string('invoice_reference')->nullable();
                $table->string('visit_type')->default('Initial Consultation');
                $table->unsignedBigInteger('doctor_id')->nullable();
                $table->string('doctor_name')->nullable();
                $table->text('chief_complaint')->nullable();
                $table->text('clinical_notes')->nullable();
                $table->string('reception_type')->default('Initial Visit (Checked In)');
                $table->decimal('outstanding_balance', 10, 2)->default(0.00);
                $table->string('status')->default('checked_in'); // checked_in, in_progress, completed, cancelled
                $table->timestamp('visited_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('patient_visits');
    }
};

