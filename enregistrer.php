<?php
require_once("functions.inc.php");
require_once("config.inc.php");

global $mysqli;

$nom_prenom = mysqli_real_escape_string($mysqli, $_POST['nom_prenom']);
$adresse = mysqli_real_escape_string($mysqli, $_POST['adresse'] ?? "");
$naissance = mysqli_real_escape_string($mysqli, $_POST['naissance'] ?? "");
$telephone = mysqli_real_escape_string($mysqli, $_POST['telephone'] ?? "");
$mail = mysqli_real_escape_string($mysqli, $_POST['mail'] ?? "");

// si la date est présente on la prend, sinon NULL
$dateSQL = (!empty($naissance)) ? "'$naissance'" : "NULL";
$adresseSQL = (!empty($adresse)) ? "'$adresse'" : "NULL";
$telephoneSQL = (!empty($telephone)) ? "'$telephone'" : "NULL";
$mailSQL = (!empty($mail)) ? "'$mail'" : "NULL";

// insertion personne
query($mysqli, "INSERT INTO personnes (nom_prenom, date_naissance, adresse, telephone, email)
                VALUES ('$nom_prenom', $dateSQL, $adresseSQL, $telephoneSQL, $mailSQL)");

$id_personne = mysqli_insert_id($mysqli);

// loisirs cochés
if (!empty($_POST['loisirs'])) {
    foreach ($_POST['loisirs'] as $id_mot) {
        $id_mot = intval($id_mot);

        // on récpère les catégories et les mots clés
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

echo "Fiche enregistrée avec succès !<br>";
echo "<a href='index.php?p=afficherfiche&id=$id_personne'>Voir la fiche</a> | ";
echo "<a href='index.php'>Retour à l'accueil</a>";
?>