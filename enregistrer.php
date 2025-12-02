<?php
require_once("functions.inc.php");
require_once("config.inc.php");

global $mysqli;

$nom_prenom = mysqli_real_escape_string($mysqli, $_POST['nom_prenom']);
$adresse = mysqli_real_escape_string($mysqli, $_POST['adresse'] ?? "");
$naissance = mysqli_real_escape_string($mysqli, $_POST['naissance'] ?? "");
$telephone = mysqli_real_escape_string($mysqli, $_POST['telephone'] ?? "");
$mail= mysqli_real_escape_string($mysqli, $_POST['mail'] ?? "");

// Insertion personne
query($mysqli, "INSERT INTO personnes (nom_prenom, date_naissance, adresse, telephone, email)
                VALUES ('$nom_prenom', '$naissance','$adresse', '$telephone', '$mail')");

$id_personne = mysqli_insert_id($mysqli);

// Loisirs cochés
if (!empty($_POST['loisirs'])) {
    foreach ($_POST['loisirs'] as $id_mot) {
        $id_mot = intval($id_mot);
        query($mysqli, "INSERT INTO personnes_loisirs (id_personne, id_mot) VALUES ($id_personne, $id_mot)");
    }
}

echo "Fiche enregistrée.";