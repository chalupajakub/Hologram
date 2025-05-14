<?php
$server = "innodb.endora.cz";
$username = "jakubchalupa";
$password = "123456789";
$database = "hologram";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("Připojení selhalo: " . mysqli_connect_error());
}

//echo "Připojení k databázi bylo úspěšné.";
?>
