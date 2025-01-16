<?php
include('../cfg.php'); // Dołączenie konfiguracji bazy danych

// Sprawdzenie, czy przekazano parametr ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Pobranie i zabezpieczenie ID
    $query = "DELETE FROM page_list WHERE id = $id"; // Zapytanie SQL do usunięcia rekordu

    if (mysqli_query($conn, $query)) {
        echo "Podstrona została usunięta.";
    } else {
        echo "Błąd podczas usuwania: " . mysqli_error($conn);
    }
} else {
    echo "Nie podano ID do usunięcia.";
}

// Link powrotu do panelu administracyjnego
echo '<br><a href="admin.php">Powrót do panelu administracyjnego</a>';
?>