<?php
require_once("functions.inc.php");
include_once("config.inc.php");
global $mysqli;

// récupération de l'id dans  l'URL
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

// récupération des loisirs de la personne regroupés par catégorie
$resultLoisirs = query($mysqli, "
    SELECT c.nom_categorie, pl.mot_cle
    FROM personnes_loisirs pl
    JOIN categories_loisir c ON pl.id_categorie = c.id_categorie
    WHERE pl.id_personne = $id_personne
    ORDER BY c.nom_categorie, pl.mot_cle
");

// regroupement des loisirs par catégorie
$loisirs = [];
while ($loisir = mysqli_fetch_assoc($resultLoisirs)) {
    $loisirs[$loisir['nom_categorie']][] = $loisir['mot_cle'];
}

// formatage de la date de naissance (si elle existe) sinon rien
$dateFormatee = "Non renseignée";
if (!empty($personne['date_naissance']) && $personne['date_naissance'] !== '0000-00-00') {
    $dateNaissance = new DateTime($personne['date_naissance']);
    $dateFormatee = $dateNaissance->format('d/m/Y');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de <?php echo htmlspecialchars($personne['nom_prenom']); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>
<body>

<h1>Fiche de <?php echo htmlspecialchars($personne['nom_prenom']); ?></h1>

<div class="fiche">
    <h2>Coordonnées</h2>
    <p><strong>Nom Prénom :</strong> <?php echo htmlspecialchars($personne['nom_prenom']); ?></p>
    <p><strong>Date de naissance :</strong> <?php echo htmlspecialchars($dateFormatee); ?></p>
    <p><strong>Adresse :</strong> <?php echo htmlspecialchars($personne['adresse'] ?: 'Non renseignée'); ?></p>
    <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($personne['telephone'] ?: 'Non renseigné'); ?></p>
    <p><strong>Email :</strong> <?php echo htmlspecialchars($personne['email'] ?: 'Non renseigné'); ?></p>

    <?php if (!empty($loisirs)) : ?>
        <h2>Loisirs</h2>
        <?php foreach ($loisirs as $categorie => $motsCles) : ?>
            <p>
                <strong><?php echo htmlspecialchars($categorie); ?> :</strong>
                <?php echo htmlspecialchars(implode(', ', $motsCles)); ?>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<br>
<a href="?p=modifierfiche&id=<?php echo $id_personne; ?>">
    <button type="button">Modifier la fiche</button>
</a>

<br><br>
<a href="?p=listefiches">Retour à la liste</a> |
<a href="index.php">Retour à l'accueil</a>

</body>
</html>