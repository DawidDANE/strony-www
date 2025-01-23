<?php
include('../cfg.php'); // Dołączenie pliku z konfiguracją

// Sprawdzenie, czy ID produktu zostało przekazane w URL
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Pobranie danych produktu z bazy danych
    $sql = "SELECT * FROM produkty WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $produkt = $result->fetch_assoc();
    } else {
        echo "Produkt nie został znaleziony.";
        exit;
    }
} else {
    echo "Brak ID produktu.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Szczegóły produktu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            color: #333;
            padding: 20px;
        }
        .produkt-detail {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #fff;
            width: 60%;
            margin: 0 auto;
        }
        .produkt-detail img {
            width: 100px;  /* Ustawienie szerokości na 50px */
            height: 100px; /* Ustawienie wysokości na 50px */
            object-fit: cover; /* Dopasowanie obrazu do rozmiaru */
        }
        h1 {
            color: #4CAF50;
        }
        p {
            font-size: 16px;
        }
        .cena {
            font-size: 20px;
            color: #d32f2f;
        }
        .powrot {
            margin-top: 20px;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .powrot:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="produkt-detail">
        <h1><?php echo htmlspecialchars($produkt['tytul']); ?></h1>

        <?php
        // Debugowanie, sprawdzenie, czy zdjęcie jest dostępne
        if ($produkt['zdjecie']) {
            
            // Sprawdzenie, czy dane w bazie są poprawne
            $imageData = base64_encode($produkt['zdjecie']);
            

            // Wyświetlanie zdjęcia z bazy danych
            echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="' . htmlspecialchars($produkt['tytul']) . '">';
        } else {
            echo '<p>Brak zdjęcia w bazie danych.</p>';
        }
        ?>

        <p><?php echo nl2br(htmlspecialchars($produkt['opis'])); ?></p>
        <p class="cena"><?php echo number_format($produkt['cena_netto'], 2); ?> PLN</p>
        <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($produkt['kategoria']); ?></p>
        <p><strong>Gabaryt:</strong> <?php echo htmlspecialchars($produkt['gabaryt']); ?></p>
        <p><strong>Data wygaśnięcia:</strong> <?php echo date('d-m-Y', strtotime($produkt['data_wygasniecia'])); ?></p>
        <a href="sklep.php" class="powrot">Powrót do sklepu</a>
    </div>
</body>
</html>
