<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

$asin = $_GET["asin"];
$pricewatch = $_GET["pricewatch"];

if ($pricewatch == "") {
    HEADER("Location: " . link_s("index.php"));
} 

if ($asin == "") {
    HEADER("Location: " . link_s("produkte.php"));
} 

$lk = connect();

$produkt_db = mysql_db_query($mysql_db, "SELECT * FROM `pricewatch_produkte` WHERE ID =" . $user[ID] . " AND ASIN=\"" .$asin. "\"", $lk);

$produkt = mysql_fetch_array($produkt_db, MYSQL_ASSOC);


if (($ja == 1)) {
    $sql = "DELETE FROM pricewatch_produkte WHERE ";
    $sql .= "ID=" . $user[ID] . " AND ";
    $sql .= "ASIN=\"" . $produkt[ASIN] . "\" ";
    $sql .= "LIMIT 1";
    $erg = mysql_db_query($mysql_db, $sql, $lk);
    if ($erg == true) {
        HEADER("Location: " . link_s("produkte.php"));
    } else {
        $db_fehler = 1;
    } 
} 

mysql_close($lk);


$text = $head;
$text .= $body;
$text .= menu("");
if ($db_fehler != 1) {
    $text .= main();
} else {
    $text .= main_fehler();
} 

$text .= $foot;

echo gzipoutput($text);
// ===========================================================================
function main()
{
    global $produkt, $asin;
    $str = "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td class='ueberschrift'>Artikel l&ouml;schen</td></tr>";
    $str .= "<tr><td>";
    $str .= "<p align='justify'>Achtung: Das l&ouml;schen eines Artikels kann nicht r&uuml;ckg&auml;nig gemacht werden!</p></td></tr>";
    $str .= "</table>";
    $str .= "<br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td colspan='2' align='center'><b>Soll der folgende Artikel gel&ouml;scht werden?</b></td></tr>";
    $str .= "<tr><td align='center' width='50%'><a href='" . link_s("produkte_loeschen.php") . "&ja=1&asin=" . $asin . "'>Ja</a></td>";
    $str .= "<td align='center' width='50%'><a href='" . link_s("produkte.php") . "'>Nein</a></td></tr>";
    $str .= "</table>";
    $str .= "<br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td colspan='2' align='center'><img src='" . $produkt[BILD_MITTEL] . "'></td>";
    $str .= "<tr><td colspan='2' align='center'>&nbsp;</td>";
    $str .= "<tr><td align='left'><b>Name:</b></td>";
    $str .= "<td align='left'>" . htmlentities(stripslashes($produkt[NAME])) . "</td></tr>";
    $str .= "<tr><td align='left'><b>Kategorie:</b></td>";
    $str .= "<td align='left'>" . htmlentities(stripslashes($produkt[KATEGORIE])) . "</td></tr>";
    $str .= "<tr><td align='left'><b>ASIN:</b></td>";
    $str .= "<td align='left'>" . htmlentities(stripslashes($produkt[ASIN])) . "</td></tr>";
//    if ($produkt[PREIS_NACHRICHT_GESENDET] != "") {
//        $str .= "<tr><td align='center' colspan='2'><b>Es wurde bereits mindestens eine Meldung per E-Mail versendet.</b></td></tr>";
//    } 
    $str .= "</table>";
    $str .= "<br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td colspan='2' align='center'><b>Soll der oben stehende Artikel gel&ouml;scht werden?</b></td></tr>";
    $str .= "<tr><td align='center' width='50%'><a href='" . link_s("produkte_loeschen.php") . "&ja=1&asin=" . $asin . "'>Ja</a></td>";
    $str .= "<td align='center' width='50%'><a href='" . link_s("produkte.php") . "'>Nein</a></td></tr>";
    $str .= "</table>";
    $str .= "<br><br><br>";
    return $str;
} 

function main_fehler()
{
    global $sql;
    $str = "<br><table width='400' align='center' class='rand'>";
    $str .= "<tr><td class='ueberschrift'>Artikel l&ouml;schen</td></tr>";
    $str .= "<tr><td>";
    $str .= "<p align='justify'>Leider ist ein Datenbankfehler aufgetreten. Bitte probieren sie es sp&auml;ter erneut. Sollte der Fehler dann weiterhin bestehen schicken sie bitte eine E-Mail an den <a href='mailto:jensman@jensmans-welt.de?body=" . $sql . "'>Webmaster</a>.</p>";
    $str .= "</table>";
    $str .= "<br><br><br>";
    return $str;
} 

?>
