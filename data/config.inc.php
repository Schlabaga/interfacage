<?php

    $host = "127.0.0.1";
    $user = "root";
    $pass = ""; // a changer peut être
    $base = "PERSONNES_LOISIRS";
    $port = 3336; // à changer peut être


    $mysqli = mysqli_connect($host, $user, $pass,  $base, $port)
        or die("Connexion impossible : " . mysqli_connect_error());

    global $host;
    global $user;
    global $pass;
    global $base;
    global $mysqli;



