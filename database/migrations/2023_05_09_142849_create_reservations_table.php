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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('terrain_Id');
            $table->date('date');
            $table->time('heureDebut');
            $table->time('heureFin');
            $table->boolean('actif');
            $table->string('reservation_type');
            $table->unsignedBigInteger('reserver_par');
            $table->unsignedBigInteger('supprimer_par')->nullable();
            $table->unsignedBigInteger('membre1');
            $table->unsignedBigInteger('membre2')->nullable();
            $table->unsignedBigInteger('membre3')->nullable();
            $table->unsignedBigInteger('membre4')->nullable();

            $table->foreign('terrain_Id')->references('id')->on('terrains');
            $table->foreign('reserver_par')->references('id')->on('users');
            $table->foreign('supprimer_par')->references('id')->on('users');
            $table->foreign('membre1')->references('id')->on('users');
            $table->foreign('membre2')->references('id')->on('users');
            $table->foreign('membre3')->references('id')->on('users');
            $table->foreign('membre4')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
