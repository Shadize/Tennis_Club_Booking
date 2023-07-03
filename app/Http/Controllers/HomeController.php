<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome'); // Assurez-vous que cela correspond au chemin de votre fichier de vue principale
    }
}
