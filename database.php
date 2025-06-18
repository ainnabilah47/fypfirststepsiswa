<?php

$servername = "15.235.141.254";
$username = "ain";
$password = "ain2025";
$dbname = "db_ain";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Sambungan ke pangkalan data gagal: " . mysqli_connect_error());
}
?>
