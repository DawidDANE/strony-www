<?php
include('../cfg.php'); // Dołączenie pliku z konfiguracją

// Funkcja dodawania produktu
function dodajProdukt($conn, $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia) {
    // Przygotowanie zapytania SQL
    $sql = "INSERT INTO produkty (tytul, opis, cena_netto, podatek_vat, ilosc_sztuk, status_dostepnosci, kategoria, gabaryt, zdjecie_link, data_wygasniecia)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Sprawdzenie, czy zapytanie zostało przygotowane poprawnie
    if ($stmt = $conn->prepare($sql)) {
        // Bindowanie parametrów
        $stmt->bind_param("ssddissssss", $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia);
        
        // Wykonanie zapytania
        if ($stmt->execute()) {
            echo "Produkt został dodany pomyślnie!";
        } else {
            echo "Błąd przy dodawaniu produktu: " . $stmt->error;
        }
    } else {
        echo "Błąd w przygotowaniu zapytania: " . $conn->error;
    }
}

// Funkcja edytowania produktu
function edytujProdukt($conn, $id, $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia) {
    $sql = "UPDATE produkty SET tytul = ?, opis = ?, cena_netto = ?, podatek_vat = ?, ilosc_sztuk = ?, status_dostepnosci = ?, kategoria = ?, gabaryt = ?, zdjecie_link = ?, data_wygasniecia = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Błąd w przygotowaniu zapytania: ' . $conn->error);
    }
    $stmt->bind_param("ssddissssssi", $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia, $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Produkt został zaktualizowany pomyślnie.";
    } else {
        echo "Brak zmian w produkcie lub wystąpił problem przy zapisie.";
    }
}

// Funkcja usuwania produktu
function usunProdukt($conn, $id) {
    $sql = "DELETE FROM produkty WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Błąd w przygotowaniu zapytania: ' . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Produkt został usunięty.";
    } else {
        echo "Wystąpił problem przy usuwaniu produktu.";
    }
}

// Funkcja do przesyłania pliku
function uploadZdjecie() {
    // Sprawdzamy, czy plik został wysłany
    if (isset($_FILES['zdjecie']) && $_FILES['zdjecie']['error'] == 0) {
        // Definiujemy dozwolone rozszerzenia
        $dozwolone_rozszerzenia = ['jpg', 'jpeg', 'png'];
        $nazwa_pliku = $_FILES['zdjecie']['name'];
        $rozszerzenie = strtolower(pathinfo($nazwa_pliku, PATHINFO_EXTENSION));

        // Sprawdzamy, czy plik ma dozwolone rozszerzenie
        if (in_array($rozszerzenie, $dozwolone_rozszerzenia)) {
            // Tworzymy unikalną nazwę pliku
            $nowa_nazwa = uniqid('produkt_', true) . '.' . $rozszerzenie;
            $sciezka_docelowa = 'uploads/' . $nowa_nazwa;

            // Przenosimy plik do folderu 'uploads'
            if (move_uploaded_file($_FILES['zdjecie']['tmp_name'], $sciezka_docelowa)) {
                return $sciezka_docelowa;
            } else {
                echo "Błąd przy przesyłaniu pliku.";
                return null;
            }
        } else {
            echo "Nieobsługiwany format pliku. Dozwolone pliki to JPG, JPEG i PNG.";
            return null;
        }
    } else {
        return null;
    }
}

// Sprawdzamy, czy formularz jest wysłany i czy edytujemy czy dodajemy
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sprawdzamy, czy mamy id produktu
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $tytul = $_POST['tytul'];
    $opis = $_POST['opis'];
    $cena_netto = $_POST['cena_netto'];
    $podatek_vat = $_POST['podatek_vat'];
    $ilosc_sztuk = $_POST['ilosc_sztuk'];
    $status_dostepnosci = $_POST['status_dostepnosci'];
    $kategoria = $_POST['kategoria'];
    $gabaryt = $_POST['gabaryt'];
    $data_wygasniecia = $_POST['data_wygasniecia'];

    // Przesyłanie pliku
    $zdjecie_link = uploadZdjecie();
    
    if ($zdjecie_link) {
        if ($id) {
            // Edytowanie produktu
            edytujProdukt($conn, $id, $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia);
            // Przekierowanie po zapisaniu zmian
            header("Location: sklep.php");
            exit();
        } else {
            // Dodawanie nowego produktu
            dodajProdukt($conn, $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie_link, $data_wygasniecia);
            // Przekierowanie po dodaniu
            header("Location: sklep.php");
            exit();
        }
    }
}

// Usuwanie produktu
if (isset($_GET['delete_id'])) {
    $id = (int) $_GET['delete_id'];
    usunProdukt($conn, $id);
    // Przekierowanie po usunięciu
    header("Location: sklep.php");
    exit();
}

// Pobieranie danych produktu, jeśli edytujemy istniejący produkt
$produkt = null;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $sql = "SELECT * FROM produkty WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produkt = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edycja Produktu</title>
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
        <h2><?php echo $produkt ? "Edytuj Produkt" : "Dodaj Produkt"; ?></h2>
        <form action="edycja.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $produkt['id'] ?? ''; ?>">
            <label for="tytul">Tytuł:</label>
            <input type="text" id="tytul" name="tytul" value="<?php echo $produkt['tytul'] ?? ''; ?>" required>

            <label for="opis">Opis:</label>
            <textarea id="opis" name="opis" required><?php echo $produkt['opis'] ?? ''; ?></textarea>

            <label for="cena_netto">Cena Netto:</label>
            <input type="number" id="cena_netto" name="cena_netto" step="0.01" value="<?php echo $produkt['cena_netto'] ?? ''; ?>" required>

            <label for="podatek_vat">Podatek VAT:</label>
            <input type="number" id="podatek_vat" name="podatek_vat" step="0.01" value="<?php echo $produkt['podatek_vat'] ?? ''; ?>" required>

            <label for="ilosc_sztuk">Ilość Sztuk:</label>
            <input type="number" id="ilosc_sztuk" name="ilosc_sztuk" value="<?php echo $produkt['ilosc_sztuk'] ?? ''; ?>" required>

            <label for="status_dostepnosci">Status Dostępności:</label>
            <select id="status_dostepnosci" name="status_dostepnosci" required>
                <option value="dostepny" <?php echo ($produkt['status_dostepnosci'] ?? '') == 'dostepny' ? 'selected' : ''; ?>>Dostępny</option>
                <option value="niedostepny" <?php echo ($produkt['status_dostepnosci'] ?? '') == 'niedostepny' ? 'selected' : ''; ?>>Niedostępny</option>
                <option value="w_trakcie" <?php echo ($produkt['status_dostepnosci'] ?? '') == 'w_trakcie' ? 'selected' : ''; ?>>W trakcie</option>
            </select>

            <label for="kategoria">Kategoria:</label>
            <input type="text" id="kategoria" name="kategoria" value="<?php echo $produkt['kategoria'] ?? ''; ?>" required>

            <label for="gabaryt">Gabaryt:</label>
            <input type="text" id="gabaryt" name="gabaryt" value="<?php echo $produkt['gabaryt'] ?? ''; ?>">

            <label for="zdjecie">Wybierz Zdjęcie (JPG, PNG):</label>
            <input type="file" id="zdjecie" name="zdjecie">

            <label for="data_wygasniecia">Data Wygaśnięcia:</label>
            <input type="date" id="data_wygasniecia" name="data_wygasniecia" value="<?php echo $produkt['data_wygasniecia'] ?? ''; ?>" required>

            <button type="submit"><?php echo $produkt ? "Zaktualizuj Produkt" : "Dodaj Produkt"; ?></button>

            <?php if ($produkt): ?>
                <a href="?delete_id=<?php echo $produkt['id']; ?>" class="powrot">Usuń Produkt</a>
            <?php endif; ?>
        </form>
		<a href="sklep.php" class="edytuj">powrot</a>
    </div>
	
</body>
</html>
