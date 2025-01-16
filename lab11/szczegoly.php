<?php
include('cfg.php'); // Połączenie z bazą danych

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
            max-width: 100%;
            height: auto;
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
        <img src="<?php echo htmlspecialchars($produkt['zdjecie_link']); ?>" alt="<?php echo htmlspecialchars($produkt['tytul']); ?>">
        <p><?php echo nl2br(htmlspecialchars($produkt['opis'])); ?></p>
        <p class="cena"><?php echo number_format($produkt['cena_netto'], 2); ?> PLN</p>
        <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($produkt['kategoria']); ?></p>
        <p><strong>Gabaryt:</strong> <?php echo htmlspecialchars($produkt['gabaryt']); ?></p>
        <p><strong>Data wygaśnięcia:</strong> <?php echo date('d-m-Y', strtotime($produkt['data_wygasniecia'])); ?></p>
        <a href="sklep.php" class="powrot">Powrót do sklepu</a>
    </div>
</body>
</html>
