<?php
require '../cfg.php'; // Dołączenie pliku z konfiguracją bazy danych

// Funkcja do edycji produktu
function edytujProdukt($conn, $id, $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia) {
    // Przygotowanie zapytania SQL do aktualizacji produktu
    $sql = "UPDATE produkty SET
            tytul = ?,
            opis = ?,
            cena_netto = ?,
            podatek_vat = ?,
            ilosc_sztuk = ?,
            status_dostepnosci = ?,
            kategoria = ?,
            gabaryt = ?,
            zdjecie_link = ?,
            data_wygasniecia = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Błąd w przygotowaniu zapytania: ' . $conn->error);
    }

    // Przypisanie danych do zapytania
    $stmt->bind_param("ssddisssssi", $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia, $id);

    // Wykonanie zapytania
    if ($stmt->execute()) {
        echo "Produkt został zaktualizowany pomyślnie!";
    } else {
        echo "Błąd przy edytowaniu produktu: " . $stmt->error;
    }
}

// Sprawdzenie czy formularz został przesłany
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $tytul = $_POST['tytul'];
    $opis = $_POST['opis'];
    $cena_netto = floatval($_POST['cena_netto']);
    $podatek_vat = floatval($_POST['podatek_vat']);
    $ilosc_sztuk = intval($_POST['ilosc_sztuk']);
    $status_dostepnosci = $_POST['status_dostepnosci'];
    $kategoria = $_POST['kategoria'];
    $gabaryt = $_POST['gabaryt'];
    $zdjecie_link = $_POST['zdjecie_link']; // Pobieranie URL zdjęcia z formularza
    $data_wygasniecia = $_POST['data_wygasniecia'];

    // Wywołanie funkcji do edycji produktu
    edytujProdukt($conn, $id, $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia);
}

// Pobranie danych produktu do edycji
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM produkty WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Błąd w przygotowaniu zapytania: ' . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produkt = $result->fetch_assoc();

    if (!$produkt) {
        die('Nie znaleziono produktu o podanym ID.');
    }
} else {
    die('Nie podano ID produktu.');
}
?>

<!-- Formularz edycji produktu -->
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $produkt['id']; ?>">

    <label>Tytuł:
        <input type="text" name="tytul" value="<?php echo htmlspecialchars($produkt['tytul']); ?>" required>
    </label><br>

    <label>Opis:
        <textarea name="opis" required><?php echo htmlspecialchars($produkt['opis']); ?></textarea>
    </label><br>

    <label>Cena netto:
        <input type="number" step="0.01" name="cena_netto" value="<?php echo $produkt['cena_netto']; ?>" required>
    </label><br>

    <label>Podatek VAT:
        <input type="number" step="0.01" name="podatek_vat" value="<?php echo $produkt['podatek_vat']; ?>" required>
    </label><br>

    <label>Ilość sztuk:
        <input type="number" name="ilosc_sztuk" value="<?php echo $produkt['ilosc_sztuk']; ?>" required>
    </label><br>

    <label>Status dostępności:
        <select name="status_dostepnosci" required>
            <option value="dostepny" <?php echo $produkt['status_dostepnosci'] === 'dostepny' ? 'selected' : ''; ?>>Dostępny</option>
            <option value="niedostepny" <?php echo $produkt['status_dostepnosci'] === 'niedostepny' ? 'selected' : ''; ?>>Niedostępny</option>
        </select>
    </label><br>

    <label>Kategoria:
        <input type="text" name="kategoria" value="<?php echo htmlspecialchars($produkt['kategoria']); ?>" required>
    </label><br>

    <label>Gabaryt:
        <input type="text" name="gabaryt" value="<?php echo htmlspecialchars($produkt['gabaryt']); ?>" required>
    </label><br>

    <label>URL zdjęcia:
        <input type="text" name="zdjecie_link" value="<?php echo htmlspecialchars($produkt['zdjecie_link']); ?>" required>
    </label><br>

    <label>Data wygaśnięcia:
        <input type="date" name="data_wygasniecia" value="<?php echo $produkt['data_wygasniecia']; ?>" required>
    </label><br>

    <button type="submit">Zapisz zmiany</button>
</form>

<!-- Przycisk przekierowujący do strony dodawania produktu -->
<a href="dodaj.php" style="display: inline-block; margin-top: 20px; padding: 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Dodaj Nowy Produkt</a>

</body>
</html>