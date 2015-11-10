<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

$produkt[1] = "1";

if ($pricewatch == "") {
    HEADER("Location: " . link_s("index.php"));
} 

if ($asin == "") {
    HEADER("Location: " . link_s("index.php"));
} 

$produkt_preis = ereg_replace(",", ".", $produkt_preis);
$erinnerung_preis = ereg_replace(",", ".", $erinnerung_preis);

$sql = "INSERT pricewatch_produkte (ID, ASIN, NAME, KATEGORIE, BILD_KLEIN, BILD_MITTEL, PREIS_START, PREIS_AKTUELL, PREIS_NACHRICHT) VALUES (";
$sql .= $user[ID] . ", ";
$sql .= "\"" . addslashes(trim($asin)) . "\", ";
$sql .= "\"" . addslashes(trim($produkt_name)) . "\", ";
$sql .= "\"" . addslashes(trim($produkt_kategorie)) . "\", ";
$sql .= "\"" . addslashes(trim($produkt_bild_klein)) . "\", ";
$sql .= "\"" . addslashes(trim($produkt_bild_mittel)) . "\", ";
$sql .= $produkt_preis . ", ";
$sql .= $produkt_preis . ", ";
if ($erinnerung_immer == 1) {
    $sql .= "-1";
} else {
    $sql .= "" . $erinnerung_preis;
} 
$sql .= ");";

$lk = connect();
$erg = mysql_db_query($mysql_db, $sql, $lk);
mysql_close($lk);

if ($erg == false) {
    echo $sql;
    $text = $head;
    $text .= $body;
    $text .= menu("");
    $text .= main();
    $text .= $foot;

    echo gzipoutput($text);
} else {
    HEADER("Location: " . link_s("produkte.php"));
} 
// ===========================================================================
function main()
{
    global $sql;
    $str = "<table width='400' align='center' class='rand' cellpadding='4'>";
    $str .= "<tr><td class='ueberschrift'>Neuen Artikel hinzuf&uuml;gen</td></tr>";
    $str .= "<tr><td>";
    $str .= "<p align='justify'>Leider ist ein Datenbankfehler aufgetreten. Bitte probieren sie es sp&auml;ter erneut. Sollte der Fehler dann weiterhin bestehen schicken sie bitte eine E-Mail an den <a href='mailto:jensman@jensmans-welt.de?body=" . $sql . "'>Webmaster</a>.</p>";
    $str .= "</table>";
    $str .= "<br><br><br>";
    return $str;
} 

?>