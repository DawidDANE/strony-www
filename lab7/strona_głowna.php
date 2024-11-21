<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); 
include('cfg.php');

// Zmienna $strona powinna być zdefiniowana przed jej użyciem
$strona = '';  // Inicjalizujemy zmienną na początku

if (isset($_GET['idp'])) {
    $id = $_GET['idp'];
    
    // Pobieramy tytuł strony z bazy danych na podstawie 'idp'
    $sql = "SELECT page_title FROM page_list WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $page_title = $row['page_title'];  // Ustawiamy tytuł strony z bazy
    } else {
        $page_title = "Filmy Oscarowe";  // Tytuł domyślny, jeśli nie znaleziono strony
    }
} else {
    $page_title = "Filmy Oscarowe";  // Tytuł domyślny, jeśli brak parametru 'idp'
}

// Określamy, którą stronę załadować w zależności od 'idp'
if ($_GET['idp'] == 'glowna' || $_GET['idp'] == '') {
    $strona = __DIR__ . '/html/strona_głowna.html';
} elseif ($_GET['idp'] == 'lista') {
    $strona = __DIR__ . '/html/lista.html';
} elseif ($_GET['idp'] == 'galeria') {
    $strona = __DIR__ . '/html/galeria.html';
} elseif ($_GET['idp'] == 'o_nas') {
    $strona = __DIR__ . '/html/o_nas.html';
} elseif ($_GET['idp'] == 'filmy') {
    $strona = __DIR__ . '/html/filmy.html';
} elseif ($_GET['idp'] == 'kontakt') {
    $strona = __DIR__ . '/html/kontakt.html';
} else {
    $strona = __DIR__ . '/html/strona_głowna.html';
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title> <!-- Dynamiczny tytuł -->
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/timedate.js" defer></script>
    <script src="js/kolorujtlo.js" defer></script>
</head>
<body onload="startclock()">
    <header>
        <h1>Filmy Oscarowe</h1>
        <nav>
            <ul class="menu">
                <li><a href="strona_głowna.php?idp=glowna">Strona Główna</a></li>
                <li><a href="strona_głowna.php?idp=lista">Lista Filmów</a></li>
                <li><a href="strona_głowna.php?idp=galeria">Galeria</a></li>
                <li><a href="strona_głowna.php?idp=o_nas">O Nas</a></li>
                <li><a href="strona_głowna.php?idp=filmy">Filmy</a></li>
                <li><a href="strona_głowna.php?idp=kontakt">Kontakt</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <?php
        // Teraz zmienna $strona jest zawsze zdefiniowana, więc możemy ją załadować
        if (file_exists($strona)) {
            include($strona);
        } else {
            echo 'Ścieżka: ' . $strona . '<br>';
            echo 'Plik nie istnieje.';
        }
        ?>
    </main>
    <footer>
        <p>© 2025 Filmy Oscarowe. Wszystkie prawa zastrzeżone.</p>
    </footer>

    <form method="post" name="background">
        <input type="button" value="Żółty" onclick="changeBackground('#FFFF00')">
        <input type="button" value="Czarny" onclick="changeBackground('#000000')">
        <input type="button" value="Biały" onclick="changeBackground('#FFFFFF')">
        <input type="button" value="Zielony" onclick="changeBackground('#00FF00')">
        <input type="button" value="Niebieski" onclick="changeBackground('#0000FF')">
        <input type="button" value="Pomarańczowy" onclick="changeBackground('#FF8000')">
        <input type="button" value="Szary" onclick="changeBackground('#C0C0C0')">
        <input type="button" value="Czerwony" onclick="changeBackground('#FF0000')">
    </form>

    <div id="animacjaTestowa1" class="test-block" style="width: 100px; height: 50px; background-color: red; border: 2px solid black; text-align: center; line-height: 50px; cursor: pointer;">
        Kliknij mnie
    </div>
    <div id="animacjaTestowa2" class="test-block" style="width: 100px; height: 50px; background-color: blue; border: 2px solid black; text-align: center; line-height: 50px; cursor: pointer; margin-top: 20px;">
        Najedź na mnie
    </div>
    <div id="animacjaTestowa3" class="test-block" style="width: 100px; height: 50px; background-color: green; border: 2px solid black; text-align: center; line-height: 50px; cursor: pointer; margin-top: 20px;">
        Klikaj, abym urósł
    </div>

    <script>
        $(document).ready(function () {
            // Animacja dla pierwszego przycisku
            $("#animacjaTestowa1").on("click", function() {
                $(this).animate({
                    width: "500px",
                    height: "100px",
                    opacity: 0.6,
                    fontSize: "1.5em",
                    borderWidth: "5px"
                }, 1500);
            });

            // Animacja dla drugiego przycisku (najedź na mnie)
            $("#animacjaTestowa2").on("mouseover", function() {
                $(this).animate({
                    width: "300px",
                    height: "75px",
                    opacity: 0.8,
                    fontSize: "1.2em",
                    borderWidth: "3px"
                }, 1000);
            });

            $("#animacjaTestowa2").on("mouseout", function() {
                $(this).animate({
                    width: "100px",
                    height: "50px",
                    opacity: 1,
                    fontSize: "1em",
                    borderWidth: "2px"
                }, 1000);
            });

            // Animacja dla trzeciego przycisku (klikaj, abym urósł)
            $("#animacjaTestowa3").on("click", function() {
                if (!$(this).is(":animated")) { 
                    $(this).animate({
                        width: "+=50",
                        height: "+=10",
                        opacity: "+=0.1"
                    }, {
                        duration: 3000
                    });
                }
            });
        });
    </script>

    <?php
    $nr_indeksu = '169230';
    $nrGrupy = '4';
    echo 'Autor: Dawid Danelczyk ' . $nr_indeksu . ' grupa ' . $nrGrupy . ' <br /><br />';
    ?>
</body>
</html>
