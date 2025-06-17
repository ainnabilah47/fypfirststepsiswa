<?php

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "firststepsiswa";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Sambungan ke pangkalan data gagal: " . mysqli_connect_error());
}
?>