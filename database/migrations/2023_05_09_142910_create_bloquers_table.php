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
        Schema::create('bloquers', function (Blueprint $table) {
            $table->id();
            $table->date('dateDebut');
            $table->date('dateFin');
            $table->string('raison');
            $table->unsignedBigInteger('bloquer_Par');
            $table->unsignedBigInteger('terrain_Bloquer');
            $table->timestamps();

            $table->foreign('bloquer_Par')->references('id')->on('users'); // Remplacez 'users' par la table appropriée pour les utilisateurs
            $table->foreign('terrain_Bloquer')->references('id')->on('terrains');
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloquers');
    }
};
