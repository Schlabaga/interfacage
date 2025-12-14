<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Initialisation BDD Personnes</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php
  // Inclusion depuis le dossier data
  include("data/config.inc.php");
  include("data/functions.inc.php");
?>

<h1>Gestion des Personnes et Loisirs</h1>

<main>
    <div>
        <a href="?p=install">Initialiser la BDD</a>
    </div>

    <div>
        <a href="?p=nouvellefiche">Nouvelle fiche</a>
    </div>

    <div>
        <a href="?p=listefiches">Liste des fiches</a>
    </div>

    <div>
        <a href="?p=explorerloisirs">Explorer les fiches par loisir</a>
    </div>

<?php

if (isset($_GET['p'])) {
    switch ($_GET['p']) {
        case 'install':
            include('pages/install.php');
            break;
        case 'nouvellefiche':
            include('pages/nouvellefiche.php');
            break;
        case 'listefiches':
            include('pages/listefiches.php');
            break;
        case 'afficherfiche':
            include('pages/afficherfiche.php');
            break;
        case 'modifierfiche':
            include('pages/modifierfiche.php');
            break;
        case 'explorerloisirs':
            include('pages/explorerloisirs.php');
            break;
        case 'fichesparloisir':
            include('pages/fichesparloisir.php');
            break;
        default:
            echo "<p>Page non trouvée.</p>";
    }
}
?>
</main>
</body>
</html>