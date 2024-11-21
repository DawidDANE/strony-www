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

// Sprawdzamy, czy została przesłana zmiana (edycja)
if (isset($_POST['submit'])) {
    EdytujPodstrone($conn);
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
    $query = "SELECT id, page_title, status, alias FROM page_list"; // Zapytanie SQL
    $result = mysqli_query($conn, $query); // Wykonanie zapytania

    // Sprawdzenie, czy zapytanie się powiodło
    if ($result) {
        // Rozpoczęcie tabeli HTML
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Status</th>
                <th>Alias</th>
                <th>Akcje</th>
              </tr>';

        // Pętla do wyświetlania wyników
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . htmlspecialchars($row['page_title']) . '</td>';
            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
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

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny</title>
</head>
<body>
    <?php
    if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true) {
        echo "<h1>Witaj w panelu administracyjnym!</h1>";
        echo '<h2>Lista podstron:</h2>';
        ListaPodstron($conn); // Wywołanie funkcji wyświetlającej listę podstron
    } else {
        echo '<p style="color: red;">Musisz się zalogować, aby uzyskać dostęp do panelu.</p>';
    }
    ?>

    <?php if ($page): // Sprawdzamy, czy dane strony zostały załadowane ?>
        <h2>Edytuj Podstronę</h2>
        <form method="post" action="admin.php">
            <input type="hidden" name="id" value="<?php echo $page['id']; ?>" /> <!-- Ukryte pole z ID strony -->

            <label for="page_title">Tytuł strony:</label>
            <input type="text" name="page_title" id="page_title" value="<?php echo htmlspecialchars($page['page_title']); ?>" required />
            <br><br>

            <label for="page_content">Treść strony:</label>
            <textarea name="page_content" id="page_content" required><?php echo htmlspecialchars($page['page_content']); ?></textarea>
            <br><br>

            <label for="status">Aktywna:</label>
            <input type="checkbox" name="status" id="status" <?php echo ($page['status'] == 1) ? 'checked' : ''; ?> />
            <br><br>

            <input type="submit" name="submit" value="Zaktualizuj stronę" />
        </form>
    <?php else: ?>
        <p>Wybierz stronę do edycji z listy powyżej.</p>
    <?php endif; ?>

</body>
</html>
