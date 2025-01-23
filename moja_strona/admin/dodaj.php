<?php
include('../cfg.php'); // Dołączenie pliku z konfiguracją

// Funkcja dodawania produktu
function dodajProdukt($conn, $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia) {
    $sql = "INSERT INTO produkty (tytul, opis, cena_netto, podatek_vat, ilosc_sztuk, status_dostepnosci, kategoria, gabaryt, zdjecie_link, data_wygasniecia)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Błąd w przygotowaniu zapytania: ' . $conn->error);
    }
    $stmt->bind_param("ssddisssss", $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia);
    if ($stmt->execute()) {
        echo "Produkt został dodany pomyślnie!";
    } else {
        echo "Błąd przy dodawaniu produktu: " . $stmt->error;
    }
}

// Sprawdzenie, czy formularz został przesłany
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tytul = $_POST['tytul'];
    $opis = $_POST['opis'];
    $cena_netto = $_POST['cena_netto'];
    $podatek_vat = $_POST['podatek_vat'];
    $ilosc_sztuk = $_POST['ilosc_sztuk'];
    $status_dostepnosci = $_POST['status_dostepnosci'];
    $kategoria = $_POST['kategoria'];
    $gabaryt = $_POST['gabaryt'];
    $zdjecie_link = $_POST['zdjecie_link']; // Przechowywanie URL zdjęcia
    $data_wygasniecia = $_POST['data_wygasniecia'];

    // Dodawanie produktu do bazy danych
    dodajProdukt($conn, $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia);

    // Przekierowanie po dodaniu produktu
    header("Location: sklep.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj Produkt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            color: #333;
        }
        .formularz {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .formularz h2 {
            text-align: center;
        }
        .formularz input, .formularz textarea, .formularz select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .formularz button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .formularz button:hover {
            background-color: #45a049;
        }
        .formularz .powrot {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            text-align: center;
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .formularz .powrot:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="formularz">
        <h2>Dodaj Produkt</h2>
        <form action="dodaj.php" method="POST">
            <label for="tytul">Tytuł:</label>
            <input type="text" id="tytul" name="tytul" required>

            <label for="opis">Opis:</label>
            <textarea id="opis" name="opis" required></textarea>

            <label for="cena_netto">Cena Netto:</label>
            <input type="number" id="cena_netto" name="cena_netto" step="0.01" required>

            <label for="podatek_vat">Podatek VAT:</label>
            <input type="number" id="podatek_vat" name="podatek_vat" step="0.01" required>

            <label for="ilosc_sztuk">Ilość Sztuk:</label>
            <input type="number" id="ilosc_sztuk" name="ilosc_sztuk" required>

            <label for="status_dostepnosci">Status Dostępności:</label>
            <select id="status_dostepnosci" name="status_dostepnosci" required>
                <option value="dostepny">Dostępny</option>
                <option value="niedostepny">Niedostępny</option>
                <option value="w_trakcie">W trakcie</option>
            </select>

            <label for="kategoria">Kategoria:</label>
            <input type="text" id="kategoria" name="kategoria" required>

            <label for="gabaryt">Gabaryt:</label>
            <input type="text" id="gabaryt" name="gabaryt">

            <label for="zdjecie_link">Link do Zdjęcia (URL):</label>
            <input type="text" id="zdjecie_link" name="zdjecie_link">

            <label for="data_wygasniecia">Data Wygaśnięcia:</label>
            <input type="date" id="data_wygasniecia" name="data_wygasniecia" required>

            <button type="submit">Dodaj Produkt</button>
        </form>
        <a href="sklep.php" class="powrot">Powrót</a>
    </div>
</body>
</html>