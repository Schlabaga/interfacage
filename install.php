<?php

    include_once("functions.inc.php");
    include_once("config.inc.php");
    global $host, $pass, $user;

    $db_name = "PERSONNES_LOISIRS";

    // Connexion à la BDD
    $mysqli = mysqli_connect($host, $user, $pass,  null,8889)
        or die("Connexion impossible : " . mysqli_connect_error());

    query($mysqli, "CREATE DATABASE IF NOT EXISTS `".$db_name."`");

    mysqli_select_db($mysqli, $db_name)
        or die("Sélection de la base de données impossible : " . mysqli_error($mysqli));

    query($mysqli, "DROP TABLE IF EXISTS `personnes_loisirs`");
    query($mysqli, "DROP TABLE IF EXISTS `mots_cles`");
    query($mysqli, "DROP TABLE IF EXISTS `personnes`");
    query($mysqli, "DROP TABLE IF EXISTS `categories_loisir`");

    // =================================================================
    // 1. TABLE `personnes` (Coordonnées) - AVEC CHAMPS OPTIONNELS
    // =================================================================

    query($mysqli, "CREATE TABLE `personnes` (
        `id_personne` INT UNSIGNED NOT NULL,
        `nom_prenom` VARCHAR(255) NOT NULL,
        `date_naissance` DATE DEFAULT NULL,
        `adresse` VARCHAR(255) DEFAULT NULL,
        `telephone` VARCHAR(20) DEFAULT NULL,
        `email` VARCHAR(100) DEFAULT NULL
    )");

    query($mysqli, "ALTER TABLE `personnes`
        ADD PRIMARY KEY (`id_personne`)");

    query($mysqli, "ALTER TABLE `personnes`
        MODIFY `id_personne` INT UNSIGNED NOT NULL AUTO_INCREMENT");

    // =================================================================
    // 2. TABLE `categories_loisir`
    // =================================================================

    query($mysqli, "CREATE TABLE `categories_loisir` (
        `id_categorie` INT UNSIGNED NOT NULL,
        `nom_categorie` VARCHAR(50) NOT NULL
    )");

    query($mysqli, "ALTER TABLE `categories_loisir`
        ADD PRIMARY KEY (`id_categorie`),
        ADD UNIQUE KEY `nom_categorie` (`nom_categorie`)");

    query($mysqli, "ALTER TABLE `categories_loisir`
        MODIFY `id_categorie` INT UNSIGNED NOT NULL AUTO_INCREMENT");

    // =================================================================
    // TABLE `mots_cles` (Mots-clés liés aux catégories)
    // =================================================================

    query($mysqli, "CREATE TABLE `mots_cles` (
        `id_mot` INT UNSIGNED NOT NULL,
        `id_categorie` INT UNSIGNED NOT NULL,
        `mot_cle` VARCHAR(50) NOT NULL
    )");

    query($mysqli, "ALTER TABLE `mots_cles`
        ADD PRIMARY KEY (`id_mot`),
        ADD FOREIGN KEY (`id_categorie`) REFERENCES `categories_loisir`(`id_categorie`) ON DELETE CASCADE");

    query($mysqli, "ALTER TABLE `mots_cles`
        MODIFY `id_mot` INT UNSIGNED NOT NULL AUTO_INCREMENT");

    // =================================================================
    // 3. TABLE `personnes_loisirs` (Association)
    // =================================================================

    query($mysqli, "CREATE TABLE `personnes_loisirs` (
        `id_pers_loisir` INT UNSIGNED NOT NULL,
        `id_personne` INT UNSIGNED NOT NULL,
        `id_categorie` INT UNSIGNED NOT NULL,
        `mot_cle` VARCHAR(50) NOT NULL
    )");

    query($mysqli, "ALTER TABLE `personnes_loisirs`
            ADD PRIMARY KEY (`id_pers_loisir`),
            ADD UNIQUE KEY `lien_unique` (`id_personne`, `id_categorie`, `mot_cle`),
            ADD FOREIGN KEY (`id_personne`) REFERENCES `personnes`(`id_personne`) ON DELETE CASCADE,
            ADD FOREIGN KEY (`id_categorie`) REFERENCES `categories_loisir`(`id_categorie`) ON DELETE CASCADE");

    query($mysqli, "ALTER TABLE `personnes_loisirs`
        MODIFY `id_pers_loisir` INT UNSIGNED NOT NULL AUTO_INCREMENT");

    echo "Initialisation de la BDD terminée. Insertion des données...<br><br>";

    // =================================================================
    // LECTURE DES FICHIERS
    // =================================================================

    // Lecture des fiches personnes
    $lignes = file("Fiches.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $fichesListe = [];

    foreach ($lignes as $ligne) {
        $elements = explode("|", $ligne);
        $fichesListe[] = $elements;
    }

    // Lecture des loisirs
    $lignesLoisirs = file("Loisirs.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $loisirsStructure = [];

    foreach ($lignesLoisirs as $ligne) {
        $ligneNettoyee = trim($ligne);

        if (!empty($ligneNettoyee)) {
            $parties = explode(" : ", $ligneNettoyee, 2);

            if (count($parties) === 2) {
                $nomCategorie = trim($parties[0]);
                $listeMotsClesBrute = trim($parties[1]);
                $motsClesArray = explode(", ", $listeMotsClesBrute);
                $motsClesFiltres = array_map('trim', $motsClesArray);

                $loisirsStructure[$nomCategorie] = $motsClesFiltres;
            }
        }
    }

    // =================================================================
    // INSERTION DES PERSONNES
    // =================================================================

    if (!empty($fichesListe)) {
        $valuesPersonnes = [];

        foreach ($fichesListe as $fiche) {
            $nom = mysqli_real_escape_string($mysqli, $fiche[0]);

            // Conversion de la date pour SQL
            $dateOriginal = $fiche[1];
            $dateParts = explode("/", $dateOriginal);
            $dateMySQL = $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];
            $date = mysqli_real_escape_string($mysqli, $dateMySQL);

            $adresse = mysqli_real_escape_string($mysqli, $fiche[2]);
            $tel = mysqli_real_escape_string($mysqli, $fiche[3]);
            $email = mysqli_real_escape_string($mysqli, $fiche[4]);

            $valuesPersonnes[] = "('$nom', '$date', '$adresse', '$tel', '$email')";
        }

        $sqlPersonnes = "INSERT INTO `personnes` (`nom_prenom`, `date_naissance`, `adresse`, `telephone`, `email`) VALUES "
                      . implode(", ", $valuesPersonnes);

        query($mysqli, $sqlPersonnes);
        echo count($fichesListe) . " personnes insérées.<br>";
    }

    // =================================================================
    // INSERTION OPTIMISÉE DES CATÉGORIES (INSERT MULTIPLE)
    // =================================================================

    if (!empty($loisirsStructure)) {
        $valuesCategories = [];

        foreach (array_keys($loisirsStructure) as $categorie) {
            $cat = mysqli_real_escape_string($mysqli, $categorie);
            $valuesCategories[] = "('$cat')";
        }

        $sqlCategories = "INSERT INTO `categories_loisir` (`nom_categorie`) VALUES "
                       . implode(", ", $valuesCategories);

        query($mysqli, $sqlCategories);
        echo count($loisirsStructure) . " catégories insérées.<br>";
    }

    // =================================================================
    // RÉCUPÉRATION DES IDs POUR LES ASSOCIATIONS
    // =================================================================

    // Créer un mapping email -> id_personne
    $mapPersonnes = [];
    $result = mysqli_query($mysqli, "SELECT `id_personne`, `email` FROM `personnes`");
    while ($row = mysqli_fetch_assoc($result)) {
        $mapPersonnes[$row['email']] = $row['id_personne'];
    }

    // Créer un mapping nom_categorie -> id_categorie
    $mapCategories = [];
    $result = mysqli_query($mysqli, "SELECT `id_categorie`, `nom_categorie` FROM `categories_loisir`");
    while ($row = mysqli_fetch_assoc($result)) {
        $mapCategories[$row['nom_categorie']] = $row['id_categorie'];
    }

    // =================================================================
    // INSERTION DES MOTS-CLES PAR CATEGORIE
    // =================================================================

    foreach ($loisirsStructure as $categorie => $motsCles) {
        $id_categorie = $mapCategories[$categorie];
        foreach ($motsCles as $mot) {
            $motSQL = mysqli_real_escape_string($mysqli, $mot);
            query($mysqli, "INSERT INTO `mots_cles` (`id_categorie`, `mot_cle`) VALUES ($id_categorie, '$motSQL')");
        }
    }

    echo "Mots-clés insérés.<br>";

    // =================================================================
    // INSERTION DES ASSOCIATIONS PERSONNES-LOISIRS
    // =================================================================

    $valuesAssociations = [];

    foreach ($fichesListe as $fiche) {
        $email = $fiche[4];
        $id_personne = $mapPersonnes[$email];

        // $fiche[5] contient les loisirs
        if (isset($fiche[5]) && !empty($fiche[5])) {
            // Les loisirs sont séparés par "/" (Cuisine:mot1,mot2/Sport:mot3)
            $categoriesLoisirs = explode("/", $fiche[5]);

            foreach ($categoriesLoisirs as $catLoisir) {
                $parts = explode(":", $catLoisir);
                if (count($parts) === 2) {
                    $categorie = trim($parts[0]);
                    $motsCles = explode(",", $parts[1]);

                    foreach ($motsCles as $mot) {
                        $mot = trim($mot);
                        if (isset($mapCategories[$categorie])) {
                            $id_categorie = $mapCategories[$categorie];
                            $motCle = mysqli_real_escape_string($mysqli, $mot);
                            $valuesAssociations[] = "($id_personne, $id_categorie, '$motCle')";
                        }
                    }
                }
            }
        }
    }

    // Insert final
    if (!empty($valuesAssociations)) {
        $sqlAssociations = "INSERT INTO `personnes_loisirs` (`id_personne`, `id_categorie`, `mot_cle`) VALUES "
                         . implode(", ", $valuesAssociations);

        query($mysqli, $sqlAssociations);
        echo count($valuesAssociations) . " associations personnes-loisirs insérées.<br>";
    }

    echo "<br>Insertion terminée avec succès !";

    mysqli_close($mysqli);

?>