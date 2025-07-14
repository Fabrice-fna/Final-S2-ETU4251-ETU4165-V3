<?php
$conn = mysqli_connect("localhost", "root", "", "emprunt");
//("localhost", "ETU004251", "FyocfeaX", "db_s2_ETU004251");
if (!$conn) {
die ("Erreur :" . mysqli_connect_error());
}


