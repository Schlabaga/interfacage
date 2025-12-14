<?php
require_once("functions.inc.php");
include_once("config.inc.php");
global $mysqli;

// si le formulaire est soumis (POST), on traite la mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_personne = intval($_POST['id_personne'] ?? 0);

    if ($id_personne > 0) {
        $nom_prenom = mysqli_real_escape_string($mysqli, $_POST['nom_prenom']);
        $adresse = mysqli_real_escape_string($mysqli, $_POST['adresse'] ?? "");
        $naissance = mysqli_real_escape_string($mysqli, $_POST['naissance'] ?? "");
        $telephone = mysqli_real_escape_string($mysqli, $_POST['telephone'] ?? "");
        $mail = mysqli_real_escape_string($mysqli, $_POST['mail'] ?? "");

        // gestion de la date : NULL si vide
        $dateSQL = (!empty($naissance)) ? "'$naissance'" : "NULL";
        $adresseSQL = (!empty($adresse)) ? "'$adresse'" : "NULL";
        $telephoneSQL = (!empty($telephone)) ? "'$telephone'" : "NULL";
        $mailSQL = (!empty($mail)) ? "'$mail'" : "NULL";

        // mise à jour des informations de la personne
        query($mysqli, "UPDATE personnes SET 
            nom_prenom = '$nom_prenom',
            date_naissance = $dateSQL,
            adresse = $adresseSQL,
            telephone = $telephoneSQL,
            email = $mailSQL
            WHERE id_personne = $id_personne");

        // suppression des anciens loisirs
        query($mysqli, "DELETE FROM personnes_loisirs WHERE id_personne = $id_personne");

        // insertion des nouveaux loisirs
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

        // redirection vers la fiche mise à jour
        header("Location: index.php?p=afficherfiche&id=$id_personne");
        exit;
    }
}

// récupération de l'ID depuis le GET
$id_personne = intval($_GET['id'] ?? 0);

if ($id_personne === 0) {
    die("ID de personne invalide.");
}

// récupération des informations de la personne
$resultPersonne = query($mysqli, "SELECT * FROM personnes WHERE id_personne = $id_personne");
$personne = mysqli_fetch_assoc($resultPersonne);

if (!$personne) {
    die("Personne non trouvée.");
}

// récupération des loisirs actuels de la personne
$resultLoisirsActuels = query($mysqli, "
    SELECT mc.id_mot
    FROM personnes_loisirs pl
    JOIN mots_cles mc ON pl.id_categorie = mc.id_categorie AND pl.mot_cle = mc.mot_cle
    WHERE pl.id_personne = $id_personne
");

$loisirsCoches = [];
while ($loisir = mysqli_fetch_assoc($resultLoisirsActuels)) {
    $loisirsCoches[] = $loisir['id_mot'];
}

// récupération des catégories
$resCat = query($mysqli, "SELECT id_categorie, nom_categorie FROM categories_loisir ORDER BY nom_categorie");

// récupération des mots-clés
$resMots = query($mysqli, "SELECT id_mot, id_categorie, mot_cle FROM mots_cles ORDER BY mot_cle");

// regroupement en tableau
$categories = [];
while ($cat = mysqli_fetch_assoc($resCat)) {
    $categories[$cat['id_categorie']] = [
        'nom' => $cat['nom_categorie'],
        'mots' => []
    ];
}

while ($mot = mysqli_fetch_assoc($resMots)) {
    $categories[$mot['id_categorie']]['mots'][] = [
        'id' => $mot['id_mot'],
        'nom' => $mot['mot_cle']
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la fiche de <?php echo htmlspecialchars($personne['nom_prenom']); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>
<body>

<h1>Modifier la fiche de <?php echo htmlspecialchars($personne['nom_prenom']); ?></h1>

<form method="post" action="?p=modifierfiche&id=<?php echo $id_personne; ?>">
    <input type="hidden" name="id_personne" value="<?php echo $id_personne; ?>">

    <!-- Coordonnées -->
    <p>Nom Prénom : <input type="text" name="nom_prenom" value="<?php echo htmlspecialchars($personne['nom_prenom']); ?>" required></p>
    <p>Adresse : <input type="text" name="adresse" value="<?php echo htmlspecialchars($personne['adresse'] ?? ''); ?>"></p>
    <p>Date de naissance : <input type="date" name="naissance" value="<?php echo htmlspecialchars($personne['date_naissance'] ?? ''); ?>"></p>
    <p>Téléphone : <input type="text" name="telephone" value="<?php echo htmlspecialchars($personne['telephone'] ?? ''); ?>"></p>
    <p>Email : <input type="email" name="mail" value="<?php echo htmlspecialchars($personne['email'] ?? ''); ?>"></p>

    <hr>

    <!-- Loisirs dynamiques -->
    <div>
        <?php foreach ($categories as $cat) : ?>
            <p><strong><?php echo htmlspecialchars($cat['nom']); ?> :</strong>

                <?php foreach ($cat['mots'] as $mot) : ?>
                    <label>
                        <input type="checkbox" name="loisirs[]" value="<?php echo $mot['id']; ?>"
                            <?php echo in_array($mot['id'], $loisirsCoches) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($mot['nom']); ?>
                    </label>,
                <?php endforeach; ?>

            </p>
        <?php endforeach; ?>
    </div>

    <br>
    <button type="submit">Enregistrer les modifications</button>
</form>

<br>
<a href="?p=afficherfiche&id=<?php echo $id_personne; ?>">Annuler</a>

</body>
</html>