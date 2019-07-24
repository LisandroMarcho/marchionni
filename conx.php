<?php
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "escuela";

    $link = mysqli_connect($host, $user, $pass, $db);
    mysqli_set_charset($link, 'utf8');