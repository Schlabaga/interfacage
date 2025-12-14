<?php
include_once("data/functions.inc.php");
include_once("data/config.inc.php");

global $host, $pass, $user, $base, $mysqli;



// récupération des catégories
$resCat = query($mysqli, "SELECT id_categorie, nom_categorie FROM categories_loisir ORDER BY nom_categorie");

// récupération des mots-clés
$resMots = query($mysqli, "SELECT id_mot, id_categorie, mot_cle FROM mots_cles ORDER BY mot_cle");

// regroupement en tableau associatif
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
<html>
<head>
    <meta charset="UTF-8">
    <title>Nouvelle fiche</title>
</head>
<body>

<h1>Nouvelle fiche</h1>

<form method="post" action="pages/enregistrer.php">

    <!-- coordonnées -->
    <p>Nom Prénom : <input type="text" name="nom_prenom" required></p>
    <p>Adresse : <input type="text" name="adresse"></p>
    <p>Date de naissance :  <input type="date" name="naissance"></p>
    <p>Téléphone : <input type="text" name="telephone"></p>
    <p>Email : <input type="email" name="mail" ></p>


    <hr>

    <!-- loisirs dynamiques -->
    <div>

        <?php foreach ($categories as $cat) : ?>
            <p><strong><?php echo htmlspecialchars($cat['nom']); ?> :</strong>

                <?php foreach ($cat['mots'] as $mot) : ?>
                    <label>
                        <input type="checkbox" name="loisirs[]" value="<?php echo $mot['id']; ?>">
                        <?php echo htmlspecialchars($mot['nom']); ?>
                    </label>,
                <?php endforeach; ?>

            </p>
        <?php endforeach; ?>

    </div>

    <br>
    <button type="submit">Enregistrer</button>
</form>

</body>
</html>