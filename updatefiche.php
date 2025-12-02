<?php
require_once("functions.inc.php");
require_once("config.inc.php");

global $mysqli;

$id_personne = intval($_POST['id_personne'] ?? 0);

if ($id_personne === 0) {
    die("ID de personne invalide.");
}

$nom_prenom = mysqli_real_escape_string($mysqli, $_POST['nom_prenom']);
$adresse = mysqli_real_escape_string($mysqli, $_POST['adresse'] ?? "");
$naissance = mysqli_real_escape_string($mysqli, $_POST['naissance'] ?? "");
$telephone = mysqli_real_escape_string($mysqli, $_POST['telephone'] ?? "");
$mail = mysqli_real_escape_string($mysqli, $_POST['mail'] ?? "");

// Mise à jour des informations de la personne
query($mysqli, "UPDATE personnes SET 
    nom_prenom = '$nom_prenom',
    date_naissance = '$naissance',
    adresse = '$adresse',
    telephone = '$telephone',
    email = '$mail'
    WHERE id_personne = $id_personne");

// Suppression des anciens loisirs
query($mysqli, "DELETE FROM personnes_loisirs WHERE id_personne = $id_personne");

// Insertion des nouveaux loisirs
if (!empty($_POST['loisirs'])) {
    foreach ($_POST['loisirs'] as $id_mot) {
        $id_mot = intval($id_mot);

        // Récupérer id_categorie et mot_cle depuis la table mots_cles
        $result = query($mysqli, "SELECT id_categorie, mot_cle FROM mots_cles WHERE id_mot = $id_mot");
        $mot_data = mysqli_fetch_assoc($result);

        if ($mot_data) {
            $id_categorie = $mot_data['id_categorie'];
            $mot_cle = mysqli_real_escape_string($mysqli, $mot_data['mot_cle']);

            query($mysqli, "INSERT INTO personnes_loisirs (id_personne, id_categorie, mot_cle) 
                            VALUES ($id_personne, $id_categorie, '$mot_cle')");
        }
    }
}

// redirection vers la fiche à jour
header("Location: index.php?p=afficherfiche&id=$id_personne");
exit;