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
        Schema::create('fait_partie_catégories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categorie_Id');
            $table->BigInteger('membre_Id');
            $table->foreign('categorie_Id')->references('id')->on('categories');
            $table->foreign('membre_Id')->references('numero_Affiliation')->on('membres');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fait_partie_catégories');
    }
};
