<?php


    function query($link, $requete)
    {
        $resultat = mysqli_query($link, $requete) or die("$requete : " . mysqli_error($link));
        return ($resultat);
    }

