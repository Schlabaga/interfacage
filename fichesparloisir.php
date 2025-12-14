<?php
require_once("functions.inc.php");
include_once("config.inc.php");
global $mysqli;

// récupération de l'ID du mot-clé depuis l'URL
$id_mot = intval($_GET['id_mot'] ?? 0);

if ($id_mot === 0) {
    die("ID de loisir invalide.");
}

// récupération du nom du mot-clé et de sa catégorie
$resultMot = query($mysqli, "
    SELECT mc.mot_cle, c.nom_categorie 
    FROM mots_cles mc
    JOIN categories_loisir c ON mc.id_categorie = c.id_categorie
    WHERE mc.id_mot = $id_mot
");

$motInfo = mysqli_fetch_assoc($resultMot);

if (!$motInfo) {
    die("Loisir non trouvé.");
}

// récupération des personnes ayant ce loisir
$resultPersonnes = query($mysqli, "
    SELECT DISTINCT p.id_personne, p.nom_prenom
    FROM personnes p
    JOIN personnes_loisirs pl ON p.id_personne = pl.id_personne
    JOIN mots_cles mc ON pl.id_categorie = mc.id_categorie AND pl.mot_cle = mc.mot_cle
    WHERE mc.id_mot = $id_mot
    ORDER BY p.nom_prenom
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Personnes pratiquant : <?php echo htmlspecialchars($motInfo['mot_cle']); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>

<h1>Personnes pratiquant : <?php echo htmlspecialchars($motInfo['mot_cle']); ?></h1>
<p><em>Catégorie : <?php echo htmlspecialchars($motInfo['nom_categorie']); ?></em></p>

<?php if (mysqli_num_rows($resultPersonnes) > 0) : ?>
    <ul>
        <?php while ($personne = mysqli_fetch_assoc($resultPersonnes)) : ?>
            <li>
                <a href="?p=afficherfiche&id=<?php echo $personne['id_personne']; ?>">
                    <?php echo htmlspecialchars($personne['nom_prenom']); ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else : ?>
    <p>Aucune personne ne pratique ce loisir actuellement.</p>
<?php endif; ?>

<br>
<a href="?p=explorerloisirs">Retour à l'exploration des loisirs</a> |
<a href="index.php">Retour à l'accueil</a>

</body>
</html>