<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Models\Terrain;
use Brick\Math\BigInteger;
use Cassandra\Bigint;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Type\Integer;

class MembreController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function membreC(Request $request)
    {
        // Valider les données d'inscription
        $request->validate([
            'numero_Affiliation' => 'required|integer|min:1000000|max:9999999',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'sexe' => 'required|string|max:255|in:G,F,N',
            'telephone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rue' => 'required|string|max:255',
            'code_Postal' => 'required|string|max:255',
            'localite' => 'required|string|max:255',
        ]);

        // Créer un nouvel utilisateur
        $membre = Membre::create([

            'numero_Affiliation' => $request->numero_Affiliation,
            'user_Id' => $request->id,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'actif' => true,
            'ordre_De_Cotisation' => true,
            'date_De_Naissance' => $request->date_De_Naissance,
            'sexe' => $request->sexe,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'rue' => $request->rue,
            'code_Postal' => $request->code_Postal,
            'localité' => $request->localite,
            'classement' => $request->classement


        ]);
        return response()->json(['membre' => $membre]);
    }

    public function check_Id(Request $request)
    {
        $membre = membre::where('numero_Affiliation', $request->numero_Affiliation)->first();
        if (!$membre)
            return true;
        else
            return false;
    }

    public function getMembre(Request $request)
    {
        $membre = Membre::where('numero_Affiliation', $request->numero_Affiliation)->first();
        if (!$membre)
            throw ValidationException::withMessages([
                'numero_Affiliation' => ['Les informations d\'identification fournies sont incorrectes.'], // Retourner une exception de validation si les informations d'identification sont incorrectes
            ]);
        return response()->json(['membre' => $membre]);
    }

    public function getMembreByCredential(Request $request)
    {
        $membre = Membre::where('user_Id', $request->id)->first();
        if (!$membre)
            throw ValidationException::withMessages([
                'numero_Affiliation' => ['Les informations d\'identification fournies sont incorrectes.'], // Retourner une exception de validation si les informations d'identification sont incorrectes
            ]);
        return response()->json(['membre' => $membre]);
    }

    public function updateMembre(Request $request)
    {
        // Valider les données d'inscription
        $request->validate([
            'numero_Affiliation' => 'required|integer|min:1000000|max:9999999',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'sexe' => 'required|string|max:255|in:G,F,N',
            'telephone' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'rue' => 'required|string|max:255',
            'code_Postal' => 'required|string|max:255',
            'localité' => 'required|string|max:255',
        ]);



        $membre = Membre::where('numero_Affiliation', $request->numero_Affiliation)->first();
        if (!$membre)
            throw ValidationException::withMessages([
                'numero_Affiliation' => ['Les informations d\'identification fournies sont incorrectes.'], // Retourner une exception de validation si les informations d'identification sont incorrectes
            ]);
        else
        {
            $membre->nom = $request->nom;
            $membre->prenom = $request->prenom;
            $membre->sexe = $request->sexe;
            $membre->date_De_Naissance = $request->date_De_Naissance;
            $membre->telephone = $request->telephone;
            $membre->rue = $request->rue;
            $membre->code_Postal = $request->code_Postal;
            $membre->localité = $request->localité;
            $membre->email = $request->email;
            $membre->actif = $request->actif;
            $membre->supprimé_Par = $request->supprimé_Par;
            $membre->classement = $request->classement;
            $membre->ordre_De_Cotisation = $request->ordre_De_Cotisation;


            $membre->save();
            return response()->json($membre);
        }
    }

    public function index()
    {
        $membres = Membre::all();

        return response()->json($membres);
    }

    public function listeMembreDispo()
    {
        $membres = Membre::where('actif', 1)->where('ordre_De_Cotisation', 1 )->get();
        if(!$membres)
            throw ValidationException::withMessages([
                'numero_Affiliation' => ['Les informations d\'identification fournies sont incorrectes.'], // Retourner une exception de validation si les informations d'identification sont incorrectes
            ]);
        return response()->json($membres);
    }

    public function supprimerMembre(Request $request)
    {
        $membre = Membre::where('numero_Affiliation', $request->numero_Affiliation)->first();
        if (!$membre)
            throw ValidationException::withMessages([
                'numero_Affiliation' => ['Les informations d\'identification fournies sont incorrectes.'], // Retourner une exception de validation si les informations d'identification sont incorrectes
            ]);
        else
        {
            $membre->actif = $request->actif;
            $membre->supprimé_Par = $request->supprimé_Par;

            $membre->save();
            return response()->json($membre);
        }
    }

    public function changeCoti(Request $request)
    {
        $membre = Membre::where('numero_Affiliation', $request->numero_Affiliation)->first();
        if (!$membre)
            throw ValidationException::withMessages([
                'numero_Affiliation' => ['Les informations d\'identification fournies sont incorrectes.'], // Retourner une exception de validation si les informations d'identification sont incorrectes
            ]);
        else
        {
            $membre->ordre_De_Cotisation = $request->ordre_De_Cotisation;

            $membre->save();
            return response()->json($membre);
        }
    }




}
