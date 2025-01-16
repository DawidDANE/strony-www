<?
function PokazPodstrone($id)
{
    $id_clear = htmlspecialchars($id);
    
    
    $query = "SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1";
    $result = mysqli_query($GLOBALS['conn'], $query);

    if (!$result) {
        die("Błąd zapytania: " . mysqli_error($GLOBALS['conn']));
    }

    $row = mysqli_fetch_assoc($result);

    
    if (empty($row['id'])) {
        return '[nie_znaleziono_strony]';
    } else {
        return $row['page_content'];
    }
}


if (isset($_GET['idp'])) {
    $id = $_GET['idp']; 
    
    $strona = PokazPodstrone($id);
} else {
    
    $strona = '[brak_strony_do_wyswietlenia]';
}


echo $strona;
?>