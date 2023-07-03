<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bloquer;
use App\Models\Terrain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class TerrainController extends Controller
{
    public function index()
    {
        // Récupère tous les terrains avec les relations "blockers"
        $terrains = Terrain::with('blockers')->get();

        return response()->json($terrains);
    }

    public function store(Request $request)
    {
        /*
        $validatedData = $request->validate([
            'blocked' => 'required|boolean',
        ]);
        */

        // Crée un nouveau terrain
        $terrain = Terrain::create();

        return response()->json($terrain, 201);
    }

    public function show($id)
    {
        // Récupère un terrain spécifique en utilisant son ID
        $terrain = Terrain::findOrFail($id);

        return response()->json($terrain);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'blocked' => 'required|boolean',
        ]);

        // Récupère un terrain spécifique en utilisant son ID
        $terrain = Terrain::findOrFail($id);

        // Met à jour les données du terrain avec les données validées
        $terrain->update($validatedData);

        return response()->json($terrain, 200);
    }

    public function destroy($id)
    {
        // Récupère un terrain spécifique en utilisant son ID
        $terrain = Terrain::findOrFail($id);

        // Supprime le terrain
        $terrain->delete();

        return response()->json(null, 204);
    }
}
