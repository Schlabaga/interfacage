<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Initialisation BDD Personnes</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>

<body>

<?php
  // On inclut juste le minimum nécessaire
  include("config.inc.php");
  include("functions.inc.php");
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
            include('install.php');
            break;
        case 'nouvellefiche':
            include('nouvellefiche.php');
            break;
        case 'listefiches':
            include('listefiches.php');
            break;
        case 'afficherfiche':
            include('afficherfiche.php');
            break;
        case 'modifierfiche':
            include('modifierfiche.php');
            break;
        case 'explorerloisirs':
            include('explorerloisirs.php');
            break;
        case 'fichesparloisir':
            include('fichesparloisir.php');
            break;
        default:
            echo "<p>Page non trouvée.</p>";
    }
}
?>
</main>
</body>
</html>