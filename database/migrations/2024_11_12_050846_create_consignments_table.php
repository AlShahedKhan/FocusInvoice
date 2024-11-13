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
        Schema::create('consignments', function (Blueprint $table) {
            $table->id();
            $table->string('consignment_id')->unique()->nullable();
            $table->string('size')->nullable();
            $table->string('type')->nullable();
            $table->dateTime('received_date')->nullable();
            $table->dateTime('release_date')->nullable();
            $table->enum('shipment_status', ['completed', 'pending', 'received', 'error'])->default('pending')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignments');
    }
};
