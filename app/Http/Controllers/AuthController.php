<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Valider les données d'inscription
            /*
            $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // La règle unique:users vérifie que l'adresse e-mail n'est pas déjà utilisée
            'password' => 'required|string|min:8|confirmed',
        ]);
            */

        // Créer un nouvel utilisateur
        $user = User::create([
            'rôle' => 'membre',
            'actif' => true,
            'mdp_Modifier' => false,
            'password' => Hash::make($request->password), // Le mot de passe est haché avant d'être stocké dans la base de données
        ]);

        // Retourner le token API
        return response()->json(['token' => $user->createToken('authToken')->plainTextToken, 'user' => $user]); // Générer un nouveau token API pour l'utilisateur et le retourner
    }

    public function login(Request $request)
    {
        // Valider les données de connexion
        /*
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
        */

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        $user = User::where('id', $request->id)->first();
        if (!$user || !Hash::check($request->password, $user->password)) { // Vérifier si l'utilisateur existe et si le mot de passe correspond au mot de passe haché stocké dans la base de données
            throw ValidationException::withMessages([
                'email' => ['Les informations d\'identification fournies sont incorrectes.'], // Retourner une exception de validation si les informations d'identification sont incorrectes
            ]);
}


        // Retourner le token API
        return response()->json(['token' => $user->createToken('authToken')->plainTextToken]); // Générer un nouveau token API pour l'utilisateur et le retourner
    }

    public function logout(Request $request)
    {
        // Révoquer tous les tokens API de l'utilisateur
        $request->user()->tokens()->delete(); // Supprimer tous les tokens API de l'utilisateur

        return response()->json(['message' => 'Déconnexion réussie']); // Retourner un message de succès
    }

    public function getUser(Request $request)
    {

        $user = User::where('id', $request->user_Id)->first();

        return response()->json(['rôle' => $user->rôle]);
    }

    public function  updatepassword(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if (!$user || !Hash::check($request->ancien_mdp, $user->password))
            return response()->json(['check' => false]);
        else
        {
            $user->password = Hash::make($request->nouveau_mdp);
            $user->mdp_Modifier = true;

            $user->save();
            return response()->json(['check' => true]);
        }
    }

    public function  updaterole(Request $request)
    {
        $user = User::where('id', $request->user_Id)->first();
        if (!$user)
            return response()->json(['check' => false]);
        else
        {
           // $user->rôle = $request->rôle;
            $user->rôle = $request->role;

            $user->save();
            return response()->json(['check' => true]);
        }
    }
}
