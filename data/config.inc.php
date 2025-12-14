<?php

$host = "127.0.0.1";
$user = "root";
$pass = "";
$base = "PERSONNES_LOISIRS";
$port = 3306;

/* connexion SANS base */
$mysqli = mysqli_connect($host, $user, $pass, "", $port)
    or die("Connexion serveur impossible : " . mysqli_connect_error());

/* création de la base si elle n'existe pas */
$sql = "CREATE DATABASE IF NOT EXISTS `$base` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
if (!mysqli_query($mysqli, $sql)) {
    die("Création de la base impossible : " . mysqli_error($mysqli));
}

/* reconnexion AVEC la base */
mysqli_close($mysqli);

$mysqli = mysqli_connect($host, $user, $pass, $base, $port)
    or die("Connexion à la base impossible : " . mysqli_connect_error());

global $host, $user, $pass, $base, $mysqli;