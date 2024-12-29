<?php
// contact.php
// Wersja projektu: v1.7
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Metoda PokazKontakt() - generuje formularz kontaktowy
function PokazKontakt() {
    echo '<div class="form-container">
        <div class="form">
            <h2>Formularz Kontaktowy</h2>
            <form action="contact.php" method="post">
                <label for="temat">Temat:</label>
                <input type="text" id="temat" name="temat"><br>
                <label for="tresc">Treść:</label>
                <textarea id="tresc" name="tresc"></textarea><br>
                <label for="email">Twój Email:</label>
                <input type="email" id="email" name="email"><br>
                <input type="submit" value="Wyślij">
            </form>
        </div>

        <div class="form">
            <h2>Przypomnienie Hasła</h2>
            <form action="contact.php" method="post">
                <label for="email_haslo">Twój Email:</label>
                <input type="email" id="email_haslo" name="email_haslo"><br>
                <input type="submit" value="Przypomnij Hasło">
            </form>
        </div>
    </div>';

    // Przycisk powrotu do panelu administracyjnego
    echo '<a href="admin.php"><button class="back-button">Powrót do panelu administracyjnego</button></a>';
}

// 2. Metoda WyslijMailKontakt() - wysyła e-mail za pomocą standardowej poczty serwera
function WyslijMailKontakt($odbiorca) {
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '[nie_wypelniles_pola]';
        echo PokazKontakt(); // Ponowne wywołanie formularza
    } else {
        $mail['subject'] = $_POST['temat'];
        $mail['body'] = $_POST['tresc'];
        $mail['sender'] = $_POST['email'];
        $mail['recipient'] = $odbiorca;

        $header = "From: Formularz kontaktowy <".$mail['sender'].">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
        $header .= "X-Sender: ".$mail['sender']."\n";
        $header .= "X-Mailer: PHP\n";
        $header .= "X-Priority: 3\n";
        $header .= "Return-Path: <".$mail['sender'].">\n";

        if (mail($mail['recipient'], $mail['subject'], $mail['body'], $header)) {
            echo '[wiadomosc_wyslana]';
        } else {
            echo '[blad_wysylki_maila]';
        }
    }
}

// 3. Metoda PrzypomnijHaslo() - wysyła e-mail z przypomnieniem hasła
function PrzypomnijHaslo($odbiorca, $haslo) {
    $mail['subject'] = 'Przypomnienie hasła';
    $mail['body'] = "Twoje hasło to: $haslo";
    $mail['sender'] = 'admin@example.com'; // Ustalony adres nadawcy
    $mail['recipient'] = $odbiorca;

    $header = "From: Panel admina <".$mail['sender'].">\n";
    $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
    $header .= "X-Sender: ".$mail['sender']."\n";
    $header .= "X-Mailer: PHP\n";
    $header .= "X-Priority: 3\n";
    $header .= "Return-Path: <".$mail['sender'].">\n";

    if (mail($mail['recipient'], $mail['subject'], $mail['body'], $header)) {
        echo '[haslo_wyslane]';
    } else {
        echo '[blad_wysylki_hasla]';
    }
}

// Logika wywołania funkcji
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['temat'], $_POST['tresc'], $_POST['email'])) {
        WyslijMailKontakt('odbiorca@example.com'); // Podaj właściwy adres odbiorcy
    } elseif (isset($_POST['email_haslo'])) {
        PrzypomnijHaslo($_POST['email_haslo'], 'przykładowe_haslo'); // Ustaw odpowiedni sposób pobrania hasła
    }
} else {
    PokazKontakt(); // Wyświetl formularz kontaktowy
}
?>

<style>
/* Styl dla formularzy */
.form-container {
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.form {
    width: 45%; /* Określ szerokość formularza */
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
}

form label {
    display: block;
    margin-bottom: 5px;
}

form input, form textarea {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

input[type="submit"] {
    width: auto;
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}

.back-button {
    background-color: #008CBA; /* Kolor tła przycisku */
    color: white; /* Kolor tekstu */
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 20px;
}

.back-button:hover {
    background-color: #005f73; /* Kolor przycisku po najechaniu */
}
</style>