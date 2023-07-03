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
        Schema::create('reserver_pours', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('membre_Id');
            $table->unsignedBigInteger('reservation_Id');
            $table->foreign('membre_Id')->references('numero_Affiliation')->on('membres');
            $table->foreign('reservation_Id')->references('id')->on('reservations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserver_pours');
    }
};
