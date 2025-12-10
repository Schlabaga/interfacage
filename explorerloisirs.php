<?php
require_once("functions.inc.php");
include_once("config.inc.php");
global $mysqli;

// Récupération des catégories
$resultCategories = query($mysqli, "SELECT id_categorie, nom_categorie FROM categories_loisir ORDER BY nom_categorie");

// Récupération de tous les mots-clés regroupés par catégorie
$categories = [];
while ($cat = mysqli_fetch_assoc($resultCategories)) {
    $id_cat = $cat['id_categorie'];
    $categories[$id_cat] = [
        'nom' => $cat['nom_categorie'],
        'mots' => []
    ];

    // Récupérer les mots-clés pour cette catégorie
    $resultMots = query($mysqli, "SELECT id_mot, mot_cle FROM mots_cles WHERE id_categorie = $id_cat ORDER BY mot_cle");

    while ($mot = mysqli_fetch_assoc($resultMots)) {
        $categories[$id_cat]['mots'][] = [
            'id' => $mot['id_mot'],
            'nom' => $mot['mot_cle']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Explorer les fiches par loisir</title>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>
<body>

<h1>Explorer les fiches par loisir</h1>

<div class="loisirs-liste">
    <?php foreach ($categories as $cat) : ?>
        <p>
            <strong><?php echo htmlspecialchars($cat['nom']); ?> :</strong>
            <?php
            $liens = [];
            foreach ($cat['mots'] as $mot) {
                $liens[] = '<a href="?p=fichesparloisir&id_mot=' . $mot['id'] . '">'
                         . htmlspecialchars($mot['nom']) . '</a>';
            }
            echo implode(', ', $liens);
            ?>
        </p>
    <?php endforeach; ?>
</div>

<br>
<a href="index.php">Retour à l'accueil</a>

</body>
</html>