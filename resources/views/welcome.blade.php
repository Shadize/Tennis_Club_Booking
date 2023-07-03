<!DOCTYPE html>
<!-- Définition du type de document HTML -->

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- Début de la balise HTML avec une balise lang pour définir la langue de la page -->

<head>
    <!-- En-tête de la page -->

    <meta charset="utf-8">
    <!-- Encodage des caractères de la page -->

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Définition de l'échelle d'affichage de la page pour les appareils mobiles -->

    <title>Tennis club book</title>
    <!-- Titre de la page -->

    @vite('resources\js\app.js')
    <!-- Utilisation de l'assistant vite pour charger le fichier app.js généré par Vite -->

</head>

<body style="background-color:  #f2f2f2;">
<!-- Corps de la page -->
@unless(Request::is('/'))
<main id="banniere"></main>
@endunless
    <main id="app">
    <!-- Définition de l'élément racine de l'application Vue -->

    </main>

</body>

</html>
<!-- Fin de la balise HTML -->
