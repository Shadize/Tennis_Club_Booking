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
        Schema::create('membres', function (Blueprint $table) {
            //$table->string('numero_Affiliation')->primary()->default('1234567');
            $table->BigInteger('numero_Affiliation')->primary();
            $table->unsignedBigInteger('user_Id')->nullable();
            $table->foreign('user_Id')->references('id')->on('users');
            $table->string('nom');
            $table->string('prenom');
            $table->boolean('actif');
            $table->unsignedBigInteger('supprimé_Par')->nullable();
            $table->foreign('supprimé_Par')->references('id')->on('users');
            $table->string('classement')->nullable();
            $table->boolean('ordre_De_Cotisation');
            $table->string('sexe');
            $table->date('date_De_Naissance')->nullable();
            $table->string('telephone');
            $table->string('email');
            $table->string('rue');
            $table->string('code_Postal');
            $table->string('localité');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membres');
    }
};
