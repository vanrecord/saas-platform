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
        Schema::create('estimate_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id')->index();

            $table->unsignedBigInteger('detailable_id')->nullable()->index();
            $table->string('detailable_type', 255)->nullable();
            $table->unsignedBigInteger('service_id')->nullable()->index();

            $table->date('date')->nullable();
            $table->integer('quantity')->default(1);
            $table->double('unit_price')->default(0);
            $table->double('cost')->nullable()->default(0);
            $table->double('cost_amount')->default(0);
            $table->double('discount')->default(0);
            $table->string('discount_as', 50)->default('%');
            $table->double('amount')->default(0);
            $table->text('note')->nullable();

            $table->integer('status')->default(1)->index(); // 1 = Draft, 2 = Approved, 3 = Rejected, 4 = Completed, 5 = Verified, 6 = Partially Approved, 7 = Partially Rejected
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable()->index();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejected_note')->nullable();

            $table->unsignedBigInteger('medical_detail_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estimate_id')
                ->references('id')
                ->on('estimates')
                ->onDelete('cascade');

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('rejected_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('medical_detail_id')
                ->references('id')
                ->on('medical_details')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_details');
    }
};
