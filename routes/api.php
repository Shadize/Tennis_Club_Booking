<?php

use App\Http\Controllers\CategorieController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MembreController;
use App\Http\Controllers\Php_Methods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlocageController;
use App\Http\Controllers\TerrainController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes pour l'authentification
Route::post('/register', [AuthController::class, 'register']); // Route pour l'inscription d'un nouvel utilisateur
Route::post('/login', [AuthController::class, 'login']); // Route pour la connexion d'un utilisateur existant
Route::post('/terrainC', [TerrainController::class, 'terrainC']);
Route::post('/membreC', [MembreController::class, 'membreC']);
Route::post('/check_Id', [MembreController::class, 'check_Id']);
Route::post('/getMembre', [MembreController::class, 'getMembre']);
Route::post('/getMembreByCredential', [MembreController::class, 'getMembreByCredential']);
Route::post('/getuser', [AuthController::class, 'getUser']);
Route::post('/updateMembre', [MembreController::class, 'updateMembre']);
Route::post('/supprimerMembre', [MembreController::class, 'supprimerMembre']);
Route::post('/changeCoti', [MembreController::class, 'changeCoti']);
Route::post('/updatepassword', [AuthController::class, 'updatepassword']);
Route::post('/updaterole', [AuthController::class, 'updaterole']);
Route::get('/listeMembre', [MembreController::class, 'index']); // Récupérer tous les membres
Route::post('/categorieC', [CategorieController::class, 'categorieC']);
Route::get('/deletecategorie/{tableName}', [CategorieController::class, 'deleteTablecategorie']);
Route::get('/listeCatégorie', [CategorieController::class, 'index']);
Route::post('/addfpc', [CategorieController::class, 'addfpc']);
Route::post('/listoffpc', [CategorieController::class, 'listoffpc']);
Route::post('/listoffpcbycat', [CategorieController::class, 'listoffpcbycat']);
Route::post('/listeMembreDispo', [MembreController::class, 'listeMembreDispo']);
Route::post('/supprimerfpc', [CategorieController::class, 'supprimerfpc']);


// Route pour les terrains
Route::get('/terrains', [TerrainController::class, 'index']); // Récupérer tous les terrains
Route::post('/terrains', [TerrainController::class, 'store']); // Créer un nouveau terrain
Route::get('/terrains/{id}', [TerrainController::class, 'show']); // Récupérer un terrain spécifique par son ID
Route::put('/terrains/{id}', [TerrainController::class, 'update']); // Mettre à jour un terrain spécifique par son ID
Route::delete('/terrains/{id}', [TerrainController::class, 'destroy']); // Supprimer un terrain spécifique par son ID

// Route pour les blocages
Route::post('/create-blocage', [BlocageController::class, 'blockTerrain']); // Créer une nouvelle entrée de Bloquer 
Route::delete('/bloquers/{bloquer}', [BlocageController::class, 'destroyBlock']);
Route::get('/checkBlocage', [BlocageController::class, 'checkBlockageBetweenDate']);
Route::get('/blockages/{terrain_id}', [BlocageController::class, 'checkBlockagesByTerrainId']);


// Routes Reservation Controller 

Route::get('/reservations', [ReservationController::class, 'index']);
Route::get('/reservationsByMember/{id}', [ReservationController::class, 'getReservationsByMember']);
Route::get('/reservationsByTerrain/{id}', [ReservationController::class, 'getReservationsByTerrain']);
Route::get('/getReservationsByDate', [ReservationController::class, 'getReservationsByDate']);
Route::get('/getReservationsByDateBetween', [ReservationController::class, 'getReservationsByDateBetween']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/reservations/{id}', [ReservationController::class, 'show']);
Route::put('/reservations', [ReservationController::class, 'update']);
Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']); // Route pour la déconnexion de l'utilisateur en cours de session

// Route pour récupérer les informations de l'utilisateur en cours de session
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});
