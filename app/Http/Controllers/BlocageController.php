<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bloquer;

class BlocageController extends Controller
{
    public function show($terrainId)
    {
        // Récupère tous les blocages liés à un terrain spécifique
        $blocages = Bloquer::where('terrain_id', $terrainId)
                            ->with('user') // Permet de récupéré l'user et ces infos
                            ->get();

        return response()->json($blocages);
    }

    public function checkBlockagesByTerrainId(Request $request)
    {
        $terrainId = $request->terrain_id;

        // Vérifie s'il y a des blocages pour un terrain spécifique
        $blockages = Bloquer::where('terrain_Bloquer', $terrainId)
                            ->with('user.membre') //Permet de récupéré l'user et ces infos liés à son attribut membre
                            ->get();

        return response()->json($blockages);
    }

    public function blockTerrain(Request $request)
    {
        $validatedData = $request->validate([
            'dateDebut' => 'required',
            'dateFin' => 'required',
            'raison' => 'required',
            'terrain_Bloquer' => 'required',
            'bloquer_Par' => 'required',
        ]);

        // Crée un nouveau blocage en utilisant les données validées
        $bloquer = Bloquer::create($validatedData);

        return response()->json($bloquer, 201);
    }

    public function destroyBlock(Bloquer $bloquer)
    {
        // Supprime un blocage spécifique
        $bloquer->delete();

        return response()->json('Le blocage a été supprimé avec succès.');
    }

    public function checkBlockageBetweenDate(Request $request)
    {
        // Vérifie s'il y a des blocages pour un terrain spécifique et une période donnée
        $blockages = Bloquer::where('terrain_Bloquer', $request->id)
                            ->where(function ($query) use ($request) {
                                $query->where('dateDebut', '<=', $request->dateFin)
                                      ->where('dateFin', '>=', $request->dateDebut)
                                      ->orWhere('dateDebut', '>=', $request->dateDebut)
                                      ->where('dateFin', '<=', $request->dateFin);
                            })
                            ->get();

        if ($blockages->count() > 0) {
            return response()->json(['exists' => true], 200);
        }

        return response()->json(['exists' => false], 200);
    }







    // Methodes tests 



    public function checkTerrainBloqueParDateReservation($id, $date)
    {
        $blockages = Bloquer::where('terrain_Bloquer', $id)->get();

        foreach ($blockages as $blockage) {
            $dateDebut = $blockage->dateDebut;
            $dateFin = $blockage->dateFin;

            if ($date >= $dateDebut && $date <= $dateFin) {
                return true; // Le terrain est bloqué à la date sélectionnée
            }
        }

        return false; // Le terrain n'est pas bloqué à la date sélectionnée
    }

    
}
