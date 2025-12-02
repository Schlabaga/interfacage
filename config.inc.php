<?php

    $host = "127.0.0.1";
    $user = "root";
    $pass = "root";
    $base = "PERSONNES_LOISIRS";


    $mysqli = mysqli_connect($host, $user, $pass,  $base,8889)
        or die("Connexion impossible : " . mysqli_connect_error());

    global $host;
    global $user;
    global $pass;
    global $base;
    global $mysqli;



