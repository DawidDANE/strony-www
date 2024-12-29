<?php
include('../cfg.php'); // Dołączenie pliku z konfiguracją

// Funkcja dodawania kategorii
function dodajKategorie($conn, $nazwa, $parent_id = 0) {
    $sql = "INSERT INTO categories (nazwa, parent_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nazwa, $parent_id);
    $stmt->execute();
    echo "Dodano kategorię: $nazwa<br>";
}

// Funkcja edytowania kategorii
function edytujKategorie($conn, $id, $nowaNazwa, $nowyParentId = 0) {
    $sql = "UPDATE categories SET nazwa = ?, parent_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $nowaNazwa, $nowyParentId, $id);
    $stmt->execute();
    echo "Zaktualizowano kategorię o ID: $id<br>";
}

// Funkcja usuwania kategorii
function usunKategorie($conn, $id) {
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "Usunięto kategorię o ID: $id<br>";
}

// Funkcja wyświetlania kategorii w formie drzewa
function pokazKategorie($conn, $parent_id = 0, $poziom = 0) {
    $sql = "SELECT * FROM categories WHERE parent_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($kategoria = $result->fetch_assoc()) {
        echo str_repeat("&nbsp;&nbsp;", $poziom) . "- " . $kategoria['nazwa'] . "<br>";
        pokazKategorie($conn, $kategoria['id'], $poziom + 1);
    }
}

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['akcja'])) {
        switch ($_POST['akcja']) {
            case 'dodaj':
                dodajKategorie($conn, $_POST['nazwa'], $_POST['parent_id']);
                break;
            case 'edytuj':
                edytujKategorie($conn, $_POST['id'], $_POST['nazwa'], $_POST['parent_id']);
                break;
            case 'usun':
                usunKategorie($conn, $_POST['id']);
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie kategoriami</title>
    <style>
        body {
            background-color: #1E90FF; /* Niebieskie tło */
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding-bottom: 50px; /* Dodajemy miejsce na przyciski na dole */
        }
        h1 {
            margin-top: 50px;
        }
        table {
            width: 50%;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #1E90FF;
            color: white;
        }
        form {
            margin: 20px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            display: inline-block;
        }
        input[type="text"], input[type="number"] {
            padding: 5px;
            margin: 5px;
            width: 200px;
        }
        button {
            padding: 5px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }

        /* Style dla przycisków w rogu */
        .bottom-left {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #FF5733;
            padding: 10px 20px;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .bottom-right {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #FF5733;
            padding: 10px 20px;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .bottom-left:hover, .bottom-right:hover {
            background-color: #e94e2f;
        }
    </style>
</head>
<body>
    <h1>Zarządzanie kategoriami</h1>

    <h2>Dodaj kategorię</h2>
    <form method="POST">
        <input type="hidden" name="akcja" value="dodaj">
        Nazwa: <input type="text" name="nazwa" required>
        Parent ID: <input type="number" name="parent_id" value="0">
        <button type="submit">Dodaj</button>
    </form>

    <h2>Edytuj kategorię</h2>
    <form method="POST">
        <input type="hidden" name="akcja" value="edytuj">
        ID: <input type="number" name="id" required>
        Nowa nazwa: <input type="text" name="nazwa" required>
        Nowy Parent ID: <input type="number" name="parent_id" value="0">
        <button type="submit">Edytuj</button>
    </form>

    <h2>Usuń kategorię</h2>
    <form method="POST">
        <input type="hidden" name="akcja" value="usun">
        ID: <input type="number" name="id" required>
        <button type="submit">Usuń</button>
    </form>

    <h2>Lista kategorii</h2>
    <?php pokazKategorie($conn); ?>

    <!-- Przycisk do przejścia do sklepu -->
    <button class="bottom-right" onclick="window.location.href='sklep.php';">Przejdź do sklepu</button>

    <!-- Przycisk do powrotu do panelu admina -->
    <button class="bottom-left" onclick="window.location.href='admin.php';">Powrót do panelu admina</button>
</body>
</html>
