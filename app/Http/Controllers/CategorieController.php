<?php

namespace App\Http\Controllers;

use App\Models\Fait_Partie_Catégorie;
use App\Models\Membre;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Categorie;
use Illuminate\Support\Facades\DB;

class CategorieController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function categorieC(Request $request)
    {
        $catégorie = Categorie::create([

            'nom' => $request->nom,
            'age_Max' => $request->age_Max,
            'age_Min' => $request->age_Min,
            'sexe' => $request->sexe


        ]);
        return response()->json(['catégorie' => $catégorie]);
    }

    public function deleteTablecategorie($tableName)
    {
        DB::table($tableName)->delete();

        return "Le contenu de la table a été supprimé avec succès.";
    }

    public function index()
    {
        $catégories = Categorie::all();

        return response()->json($catégories);
    }

    public function addfpc(Request $request)
    {

        $fpc = Fait_Partie_Catégorie::create([

            'categorie_Id' => $request->categorie_Id,
            'membre_Id' => $request->membre_Id,

        ]);
        return response()->json(['catégorie' => $fpc]);
    }

    public function listoffpc(Request $request)
    {
        $fpc = Fait_Partie_Catégorie::where('membre_Id', $request->numero_Affiliation)->get();
       // $fpc = Fait_Partie_Catégorie::all();
        if (!$fpc) {
            throw ValidationException::withMessages([
                'id' => ['Les informations fournies sont incorrectes.'],
            ]);
        }
        return response()->json($fpc);


    }

    public function listoffpcbycat(Request $request)
    {
        $fpc = Fait_Partie_Catégorie::where('categorie_Id', $request->id)->get();
        // $fpc = Fait_Partie_Catégorie::all();
        if (!$fpc) {
            throw ValidationException::withMessages([
                'id' => ['Les informations fournies sont incorrectes.'],
            ]);
        }
        return response()->json($fpc);


    }

    public function supprimerfpc(Request $request)
    {
        Fait_Partie_Catégorie::where('membre_Id', $request->numero_Affiliation)->delete();
    }
}
