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
        Schema::create('bill_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('bill_id');
            $table->unsignedBigInteger('usage_id');
            $table->decimal('amount', 10, 2);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bill_id')->references('id')->on('bills');
            $table->foreign('usage_id')->references('id')->on('usages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_details');
    }
};
