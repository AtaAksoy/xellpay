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
        Schema::create('usages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sim_registration_id');
            $table->date('usage_date');
            $table->enum('feature_type', ['CALL', 'INTERNET', 'SMS']);
            $table->integer('feature_amount');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sim_registration_id')->references('id')->on('sim_registrations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usages');
    }
};
