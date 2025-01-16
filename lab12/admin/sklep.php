<?php
include('../cfg.php'); // Dołączenie pliku z konfiguracją

// Funkcja wyświetlania produktów
function pokazProdukty($conn) {
    $sql = "SELECT * FROM produkty WHERE status_dostepnosci = 'dostepny' ORDER BY data_utworzenia DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table style="width: 100%; border: 1px solid #ccc; border-collapse: collapse;">';
        echo '<thead><tr><th>Tytuł</th><th>Opis</th><th>Cena Netto</th><th>Kategoria</th><th>Gabaryt</th><th>Akcja</th></tr></thead>';
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['tytul']) . '</td>';
            echo '<td>' . htmlspecialchars($row['opis']) . '</td>';
            echo '<td>' . number_format($row['cena_netto'], 2) . ' PLN</td>';
            echo '<td>' . htmlspecialchars($row['kategoria']) . '</td>';
            echo '<td>' . htmlspecialchars($row['gabaryt']) . '</td>';
            echo '<td>
                <a href="szczegoly.php?id=' . $row['id'] . '">Zobacz szczegóły</a> | 
                <a href="edycja.php?id=' . $row['id'] . '">Edytuj</a> | 
                <a href="cart.php?action=add&id=' . $row['id'] . '">Dodaj do koszyka</a>
            </td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo 'Brak dostępnych produktów.';
    }
}

// Wywołanie funkcji
pokazProdukty($conn);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Sklep - Produkty</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            color: #333;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border: 1px solid #ccc;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .button-container {
            position: fixed;
            bottom: 20px;
        }
        .powrot {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .powrot:hover {
            background-color: #45a049;
        }
        .edytuj {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #FFA500;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .edytuj:hover {
            background-color: #ff8c00;
        }
    </style>
</head>
<body>
    <h1>Witamy w naszym sklepie!</h1>
    <p>Wybierz produkt, aby zobaczyć szczegóły.</p>

    <!-- Przycisk powrotu do admin.php -->
    <a href="admin.php" class="powrot">Powrót do panelu administracyjnego</a>
    
    <!-- Przycisk edytowania produktu (pojawi się w prawym dolnym rogu) -->
    <a href="edycja.php" class="edytuj">Dodaj Produkt</a>
</body>
</html>
