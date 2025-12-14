<?php
require_once("functions.inc.php");
include_once("config.inc.php");
global $mysqli;

// récupération de toutes les personnes triées par nom_prenom
$result = query($mysqli, "SELECT id_personne, nom_prenom FROM personnes ORDER BY nom_prenom");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des fiches</title>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>
<body>

<h1>Liste des fiches</h1>

<ul>
    <?php while ($personne = mysqli_fetch_assoc($result)) : ?>
        <li>
            <a href="?p=afficherfiche&id=<?php echo $personne['id_personne']; ?>">
                <?php echo htmlspecialchars($personne['nom_prenom']); ?>
            </a>
        </li>
    <?php endwhile; ?>
</ul>

<br>
<a href="index.php">Retour à l'accueil</a>

</body>
</html>