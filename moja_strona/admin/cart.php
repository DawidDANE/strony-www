<?php
session_start(); // Start sesji

// Połączenie z bazą danych
include('../cfg.php');

// Sprawdzenie, czy żądanie ma odpowiednią akcję
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $id_prod = (int)$_GET['id']; // Pobranie ID produktu

    // Pobranie szczegółów produktu z bazy
    $sql = "SELECT * FROM produkty WHERE id = $id_prod";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $ilosc_sztuk = 1; // Domyślnie dodajemy 1 sztukę

        // Kod z przesłanego screena - zarządzanie sesją
        if (!isset($_SESSION['count'])) {
            $_SESSION['count'] = 1;
        } else {
            $_SESSION['count']++;
        }

        $nr = $_SESSION['count']; // Numer produktu w koszyku
        $_SESSION['prod'][$nr]['id_prod'] = $id_prod;
        $_SESSION['prod'][$nr]['ile_sztuk'] = $ilosc_sztuk;
        $_SESSION['prod'][$nr]['cena_netto'] = $product['cena_netto'];
        $_SESSION['prod'][$nr]['tytul'] = $product['tytul'];
        $_SESSION['prod'][$nr]['data'] = time();
    }
}

// Przekierowanie do koszyka po dodaniu produktu
header('Location: koszyk.php');
exit();