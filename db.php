<?php
$host = "localhost";
$name = "mdc";
$user = "sis_user";
$pass = "system";

$db = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

