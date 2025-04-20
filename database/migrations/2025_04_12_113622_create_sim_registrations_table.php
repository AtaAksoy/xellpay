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
        Schema::create('sim_registrations', function (Blueprint $table) {
            $table->id();

            $table->string('phone_number')->unique();
            $table->unsignedBigInteger('subscriber_id');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subscriber_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sim_registrations');
    }
};
