<?php
session_start();
include('../cfg.php'); // Dołączenie pliku z konfiguracją


// Funkcja edytująca stronę
function EdytujPodstrone($conn) {
    // Pobieramy dane z formularza
    $id = $_POST['id'];
    $page_title = $_POST['page_title'];
    $page_content = $_POST['page_content'];
    $status = isset($_POST['status']) ? 1 : 0; // 1 = aktywna, 0 = nieaktywna

    // Zaktualizowanie strony w bazie danych
    $sql = "UPDATE page_list SET page_title = ?, page_content = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssii', $page_title, $page_content, $status, $id);

    if ($stmt->execute()) {
        echo 'Strona została zaktualizowana!';
    } else {
        echo 'Błąd aktualizacji: ' . $conn->error;
    }
}

// Funkcja usuwająca stronę
function UsunPodstrone($conn, $id) {
    // Usuwamy stronę z głównej tabeli
    $sql = "DELETE FROM page_list WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo 'Strona została usunięta!';
    } else {
        echo 'Błąd usuwania: ' . $conn->error;
    }
}
echo '<a href="kategorie.php"><button class="logout-button">Przejdź do strony kategorii </button></a>';
// Funkcja dodająca nową podstronę
function DodajNowaPodstrone($conn) {
    // Pobieramy dane z formularza
    if (isset($_POST['submit_add'])) {
        $page_title = $_POST['page_title'];
        $page_content = $_POST['page_content'];
        $status = isset($_POST['status']) ? 1 : 0; // 1 = aktywna, 0 = nieaktywna

        // Wstawienie nowej strony do bazy danych
        $sql = "INSERT INTO page_list (page_title, page_content, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $page_title, $page_content, $status);

        if ($stmt->execute()) {
            echo 'Nowa strona została dodana!';
        } else {
            echo 'Błąd dodawania strony: ' . $conn->error;
        }
    }
}

// Sprawdzamy, czy została przesłana zmiana (edycja)
if (isset($_POST['submit'])) {
    EdytujPodstrone($conn);
}

// Sprawdzamy, czy została przesłana zmiana (dodawanie nowej strony)
if (isset($_POST['submit_add'])) {
    DodajNowaPodstrone($conn);
}

// Jeśli ID zostało przekazane do edycji
$page = null;
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $sql = "SELECT * FROM page_list WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $page = $result->fetch_assoc(); // Załaduj dane strony do zmiennej $page
    } else {
        echo 'Strona o takim ID nie istnieje.';
    }
}

// Jeśli ID zostało przekazane do usunięcia
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    UsunPodstrone($conn, $id);
}

// Funkcja wyświetlająca listę podstron
function ListaPodstron($conn) {
    // Pobranie danych z tabeli "page_list"
    $query = "SELECT  id, page_title, status, alias FROM page_list "; // Zapytanie SQL
    $result = mysqli_query($conn, $query); // Wykonanie zapytania

    // Sprawdzenie, czy zapytanie się powiodło
    if ($result) {
        // Rozpoczęcie tabeli HTML
        echo '<table class="styled-table">';
        echo '<thead>';
        echo '<tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Status</th>
                <th>Alias</th>
                <th>Akcje</th>
              </tr>';
        echo '</thead>';

        // Pętla do wyświetlania wyników
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . htmlspecialchars($row['page_title']) . '</td>';
            echo '<td>' . ($row['status'] == 1 ? 'Aktywna' : 'Nieaktywna') . '</td>';
            echo '<td>' . htmlspecialchars($row['alias']) . '</td>';
            echo '<td>
        <a href="admin.php?edit_id=' . $row['id'] . '">Edytuj</a> | 
        <a href="admin.php?delete_id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\')">Usuń</a>
      </td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        // Obsługa błędu w przypadku problemu z zapytaniem SQL
        echo 'Błąd w zapytaniu SQL: ' . mysqli_error($conn);
    }
}

// Obsługa wylogowania
if (isset($_GET['logout'])) {
    session_destroy(); // Zniszczenie sesji
    header('Location: admin.php'); // Przekierowanie do strony logowania
    exit();
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        .content-container {
            width: 80%;
            max-width: 1200px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        td {
            background-color: #ffffff;
        }

        .styled-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .styled-table tr:hover {
            background-color: #f1f1f1;
        }

        .logout-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #d32f2f;
        }

        .form-container {
            margin-top: 40px;
        }

        .form-container input[type="text"], .form-container textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true) {
        echo "<h1>Witaj w panelu administracyjnym!</h1>";
        echo '<div class="content-container">';
        echo '<h2>Lista podstron:</h2>';
        ListaPodstron($conn); // Wywołanie funkcji wyświetlającej listę podstron

        // Formularz dodawania nowej podstrony
        echo '<div class="form-container">';
        echo '<h2>Dodaj Nową Podstronę</h2>';
        echo '<form method="post" action="admin.php">';
        echo '<label for="page_title">Tytuł strony:</label>';
        echo '<input type="text" name="page_title" id="page_title" required />';

        echo '<label for="page_content">Treść strony:</label>';
        echo '<textarea name="page_content" id="page_content" required></textarea>';

        echo '<label for="status">Aktywna:</label>';
        echo '<input type="checkbox" name="status" id="status" />';

        echo '<input type="submit" name="submit_add" value="Dodaj stronę" />';
        echo '</form>';
        echo '</div>';

        // Formularz edycji strony
        if (isset($page)) {
            echo '<div class="form-container">';
            echo '<h2>Edytuj Podstronę</h2>';
            echo '<form method="post" action="admin.php">';
            echo '<input type="hidden" name="id" value="' . $page['id'] . '" />';
            echo '<label for="page_title">Tytuł strony:</label>';
            echo '<input type="text" name="page_title" id="page_title" value="' . $page['page_title'] . '" required />';

            echo '<label for="page_content">Treść strony:</label>';
            echo '<textarea name="page_content" id="page_content" required>' . $page['page_content'] . '</textarea>';

            echo '<label for="status">Aktywna:</label>';
            echo '<input type="checkbox" name="status" id="status" ' . ($page['status'] == 1 ? 'checked' : '') . ' />';

            echo '<input type="submit" name="submit" value="Zaktualizuj stronę" />';
            echo '</form>';
            echo '</div>';
        }

        echo '</div>';

        // Dodajemy przycisk "Wyloguj" w prawym dolnym rogu
        echo '<a href="admin.php?logout=true"><button class="logout-button">Wyloguj</button></a>';
echo '<a href="contact.php"><button class="logout-button" style="background-color: #4CAF50; bottom: 80px;">Przejdź do strony kontaktowej</button></a>';
    } else {
        echo '<div class="login-container">';
        echo '<p style="color: red;">Musisz się zalogować, aby uzyskać dostęp do panelu.</p>';

        // Formularz logowania
        echo '<h2>Logowanie</h2>';
        echo '<form method="post" action="admin.php">';
        echo '<label for="username">Login:</label>';
        echo '<input type="text" name="username" id="username" required /><br><br>';
        echo '<label for="password">Hasło:</label>';
        echo '<input type="password" name="password" id="password" required /><br><br>';
        echo '<input type="submit" name="login" value="Zaloguj się" />';
        echo '</form>';
        echo '</div>';
    }
    ?>
</body>
</html>
