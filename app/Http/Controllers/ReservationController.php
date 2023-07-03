<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \DateTime;


class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::all();

        return response()->json($reservations);
    }

    public function store(Request $request)
{

    if($request->reservation_type ==='simple')
    {
        $validatedData = $request->validate([
            'membre1' => 'required',
            'membre2' => 'required',
            'date' => 'required',
            'heureFin' => 'required',
            'heureDebut' => 'required',
            'heureFin' => 'required',
            'reservation_type' => 'required',
            'actif' => 'required',
            'reserver_par' => 'required',
            'terrain_Id' => 'required',
        ]);
    }
    else
    {
        $validatedData = $request->validate([
            'membre1' => 'required',
            'membre2' => 'required',
            'membre3' => 'required',
            'membre4' => 'required',
            'date' => 'required',
            'heureFin' => 'required',
            'heureDebut' => 'required',
            'heureFin' => 'required',
            'reservation_type' => 'required',
            'actif' => 'required',
            'reserver_par' => 'required',
            'terrain_Id' => 'required',
        ]);
    }
    

    // Convertis les valeurs 'heureDebut' et 'heureFin' en objets DateTime et ensuite en chaîne de caractères au format H:i:s
    $heureDebut = DateTime::createFromFormat('H:i', $request->heureDebut);
    $heureFin = DateTime::createFromFormat('H:i', $request->heureFin);

    // Vérifie que les conversions ont réussi
    if ($heureDebut === false || $heureFin === false) {
        return response()->json(['error' => 'Invalid time format'], 400);
    }

    // Ajoute les heures formatées aux données validées.
    $validatedData['heureDebut'] = $heureDebut->format('H:i');
    $validatedData['heureFin'] = $heureFin->format('H:i');

    $reservation = Reservation::create($validatedData);

    return response()->json($reservation, 201);
}


    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);

        return response()->json($reservation);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'heureDebut' => 'required',
            'heureFin' => 'required',
            'actif' => 'required',
            'reservation_type' => 'required',
            'supprimer_par' => 'required',
            'reserver_par' => 'required',
            'terrain_Id' => 'required',
        ]);

        $reservation = Reservation::withTrashed()->findOrFail($request->id);
        $reservation->update($validatedData);

        return response()->json($reservation, 200);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(null, 204);
    }

    public function getReservationsByMember($memberId)
{
    $reservations = Reservation::where('membre1', $memberId)
        ->with('user.membre', 'user2.membre', 'user3.membre', 'user4.membre')
        ->get();

    return response()->json($reservations);
}


    public function getReservationsByTerrain($terrainId)
    {

        Log::info("Terrain ID: " . $terrainId);

        $reservations = Reservation::where('terrain_Id', $terrainId)
        ->with('user.membre', 'user2.membre', 'user3.membre', 'user4.membre', 'reserver_par.membre')
        ->get();

        if ($reservations->isEmpty()) {
            error_log("Aucune réservation trouvée pour ce terrain.");
            return response()->json([], 200); // Renvoyer un tableau vide
        }

        Log::info("Reservations: " . $reservations);

        return response()->json($reservations, 200);
    }

    public function getReservationsByDate(Request $request)
    {


        $reservations = Reservation::where('date', $request->date)->get();

        if ($reservations->isEmpty()) {
            error_log("Aucune réservation trouvée à cette date.");
            return response()->json([], 200); // Renvoyer un tableau vide
        }

        return response()->json($reservations, 200);
    }

    public function getReservationsByDateBetween(Request $request)
{
    $startDate = $request->input('dateDebut');
    $endDate = $request->input('dateFin');
    $memberId = $request->input('membre_id');

    // Récupérez les réservations du membre dans la plage de dates spécifiée
    $reservations = Reservation::where('membre1', $memberId)
        ->whereBetween('date', [$startDate, $endDate])
        ->get();

    return response()->json($reservations);
}







    // Méthodes tests

    public function checkReservationChevauche(Request $request)
{
    $heureDebut = $request->heureDebut;
    $heureFin = $request->heureFin;
    $date = $request->date;
    $terrainId = $request->terrain_Id;

    $reservations = Reservation::where('terrain_Id', $terrainId)
        ->where('date', $date)
        ->where(function ($query) use ($heureDebut, $heureFin) {
            $query->where(function ($query) use ($heureDebut, $heureFin) {
                $query->where('heureDebut', '<=', $heureDebut)
                    ->where('heureFin', '>', $heureDebut);
            })->orWhere(function ($query) use ($heureDebut, $heureFin) {
                $query->where('heureDebut', '<', $heureFin)
                    ->where('heureFin', '>=', $heureFin);
            });
        })
        ->get();

    return !$reservations->isEmpty();
}

public function checkLimiteReservations($userId, $reservationType)
{
    // Détermine les limites de réservation en fonction du type de réservation (simple ou double)
    $limit = ($reservationType === 'simple') ? 2 : 4;

    $reservationsSemaine = Reservation::where('reserver_par', $userId)
        ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
        ->get();

    return $reservationsSemaine->sum('duree') <= $limit;
}



}
