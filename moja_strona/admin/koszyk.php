<?php
session_start(); // Start sesji

// Funkcja wyświetlania koszyka
function pokazKoszyk() {
    if (isset($_SESSION['prod']) && count($_SESSION['prod']) > 0) {
        echo '<h1>Twój koszyk</h1>';
        echo '<table style="width: 100%; border: 1px solid #ccc; border-collapse: collapse;">';
        echo '<thead><tr><th>Tytuł</th><th>Cena Netto</th><th>Ilość</th><th>Cena Brutto</th><th>Akcja</th></tr></thead>';
        echo '<tbody>';
        $total = 0;

        foreach ($_SESSION['prod'] as $nr => $product) {
            $cena_netto = $product['cena_netto'];
            $ilosc = $product['ile_sztuk'];
            $cena_brutto = $cena_netto * 1.23; // Obliczenie ceny brutto (23% VAT)
            $total += $cena_brutto * $ilosc;

            echo '<tr>';
            echo '<td>' . htmlspecialchars($product['tytul']) . '</td>';
            echo '<td>' . number_format($cena_netto, 2) . ' PLN</td>';
            echo '<td>';
            echo '<form method="post" action="koszyk.php?action=update&nr=' . $nr . '">';
            echo '<input type="number" name="ilosc" value="' . $ilosc . '" min="1" style="width: 50px;">';
            echo '<button type="submit">Zaktualizuj</button>';
            echo '</form>';
            echo '</td>';
            echo '<td>' . number_format($cena_brutto * $ilosc, 2) . ' PLN</td>';
            echo '<td><a href="koszyk.php?action=remove&nr=' . $nr . '">Usuń</a></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '<h2>Łączna wartość koszyka: ' . number_format($total, 2) . ' PLN</h2>';
    } else {
        echo '<h1>Twój koszyk jest pusty</h1>';
    }
}

// Obsługa akcji w koszyku (aktualizacja lub usunięcie produktu)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $nr = (int)$_GET['nr'];

    if ($action == 'update' && isset($_POST['ilosc'])) {
        $_SESSION['prod'][$nr]['ile_sztuk'] = (int)$_POST['ilosc'];
    }

    if ($action == 'remove') {
        unset($_SESSION['prod'][$nr]); // Usunięcie produktu z koszyka
    }

    // Resetowanie numeracji, aby uniknąć problemów z "count"
    $_SESSION['prod'] = array_values($_SESSION['prod']);
    $_SESSION['count'] = count($_SESSION['prod']);
    header('Location: koszyk.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Koszyk</title>
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
    </style>
</head>
<body>
    <?php pokazKoszyk(); ?>
    <a href="sklep.php" style="display: block; margin: 20px auto; text-align: center; background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; width: 200px;">Wróć do sklepu</a>
</body>
</html>
