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

// Pobieramy dane strony z bazy danych
$page = null; // Inicjujemy zmienną przed sprawdzeniem, czy strona istnieje
if (isset($_GET['id'])) {
    $id = $_GET['id'];
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

// Sprawdzamy, czy została przesłana zmiana
if (isset($_POST['submit'])) {
    EdytujPodstrone($conn);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Podstronę</title>
</head>
<body>
    <h1>Edytuj Podstronę</h1>

    <?php if ($page): // Sprawdzamy, czy dane strony zostały załadowane ?>
        <form method="post" action="edytuj.php">
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
        <p>Nie znaleziono strony do edycji.</p>
    <?php endif; ?>

</body>
</html>
