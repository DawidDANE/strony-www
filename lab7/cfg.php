<?php

$host = 'localhost';      
$dbname = 'moja_strona';   
$username = 'root';       
$password = '';  
$login = 'admin'; 
$pass = 'haslo123';         


$conn = mysqli_connect($host, $username, $password, $dbname);


if (mysqli_connect_errno()) {
    die("Przerwane połączenie: " . mysqli_connect_error());
} else {
    echo 'Połączono z bazą danych!';
}
?>