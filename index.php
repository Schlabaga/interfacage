<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Initialisation BDD Personnes</title>
    <meta charset="utf-8" />
    <link rel="stylesheet"  href="style.css" type="text/css"  media="screen" />
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

<?php

if (isset($_GET['p']) && $_GET['p'] === 'install') {
    include('install.php');
} elseif (isset($_GET["p"]) && $_GET['p'] === 'nouvellefiche'){
    include('nouvellefiche.php');
}
?>
</main>
</body>
</html>