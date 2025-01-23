<?php


$host = 'localhost';      
$dbname = 'moja_strona';   
$username = 'root';       
$password = '';  
$login = 'admin'; 
$pass = 'haslo123';          // Login i hasło admina

// Połączenie z bazą danych
$conn = mysqli_connect($host, $username, $password, $dbname);

if (mysqli_connect_errno()) {
    die("Połączenie z bazą danych nie powiodło się: " . mysqli_connect_error());
} else {
    // echo 'Połączono z bazą danych!';  // Nie musimy tego wyświetlać na produkcji
}

// Logowanie użytkownika
if (isset($_POST['login'])) {
    // Pobieranie danych z formularza
    $username_input = $_POST['username'];
    $password_input = $_POST['password'];

    // Sprawdzanie, czy dane logowania są poprawne
    if ($username_input == $login && $password_input == $pass) {
        // Zalogowanie użytkownika
        $_SESSION['zalogowany'] = true;
        header('Location: admin.php');  // Przekierowanie na panel administracyjny
        exit();
    } else {
        echo '<p style="color: red;">Niepoprawny login lub hasło!</p>';
    }
}
?>