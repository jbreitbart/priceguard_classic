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
    $erinnerung_preis = ereg_replace(",", ".", $erinnerung_preis);
    $sql = "UPDATE pricewatch_produkte SET ";
    $sql .= "NAME=\"" . addslashes($produkt_name) . "\", ";
    $sql .= "KATEGORIE=\"" . addslashes($produkt_kategorie) . "\", ";
    if ($erinnerung_immer == 1) {
        $sql .= "PREIS_NACHRICHT=-1 ";
    } else {
        $sql .= "PREIS_NACHRICHT=" . $erinnerung_preis . " ";
    } 
    $sql .= " WHERE ";
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
    global $asin, $produkt;
    $str = "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td class='ueberschrift'>Artikel-Daten &auml;ndern</td></tr>";
    $str .= "<tr><td>";
    $str .= "<p align='justify'>Hier k&ouml;nnen sie die Daten zu einem Artikel &auml;ndern.</p></td></tr>";
    $str .= "</table>";
    $str .= "<br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<form method=post action=" . link_s("produkte_eigenschaften.php") . "&ja=1&asin=" . $asin . ">";
    $str .= "<tr><td colspan='2' align='center'><a href='" . amazon_link($produkt[ASIN]) . "' target='_blank'><img src='" . $produkt[BILD_MITTEL] . "' border='0'></a></td>";
    $str .= "<tr><td colspan='2' align='center'>&nbsp;</td>";
    $str .= "<tr><td align='left'><b>Name:</b></td>";
    $str .= "<td align='left'><input type='text' class='input' name='produkt_name' value=\"" . htmlentities(stripslashes($produkt[NAME])) . "\" size='45' maxlength='250'></td></tr>";
    $str .= "<tr><td align='left'><b>Kategorie:</b></td>";
    $str .= "<td align='left'><input type='text' class='input' name='produkt_kategorie' value=\"" . htmlentities(stripslashes($produkt[KATEGORIE])) . "\" size='45' maxlength='250'></td></tr>";
    $str .= "<tr><td align='left'><b>ASIN:</b></td>";
    $str .= "<td align='left'>" . htmlentities(stripslashes($produkt[ASIN])) . "</td></tr>";
//    $str .= "<tr><td align='left'><b>Aktueller Preis (EUR):</b></td>";
//    $str .= "<td align='left'>" . htmlentities(stripslashes($produkt[PREIS_AKTUELL])) . "</td></tr>";
    $str .= "<tr><td align='left'><b>Erinnerungs-Preis (EUR):</b></td>";
    
if ($produkt[PREIS_NACHRICHT] != -1) {
        $str .= "<td align='left'><input type='text' class='input' name='erinnerung_preis' value=\"" . htmlentities(stripslashes($produkt[PREIS_NACHRICHT])) . "\" size='45' maxlength='250'></td></tr>";
        $str .= "<tr><td align='center' colspan='2'><input type='checkbox' name='erinnerung_immer' value='1'> <b>Bei jeder Preis &Auml;nderung erinnern.</b></td>";
    } else {
        $str .= "<td align='left'><input type='text' class='input' name='erinnerung_preis' value=\"\" size='45' maxlength='250'></td></tr>";
        $str .= "<tr><td align='center' colspan='2'><input type='checkbox' name='erinnerung_immer' value='1' checked> <b>Bei jeder Preis &Auml;nderung erinnern.</b></td>";
    } 

    $str .= "<tr><td colspan='2' align='center' valign='middle'>";
    $str .= "<input type='submit' class='forminput' name='Weiter' value='   Daten &auml;ndern   '></form>";
    $str .= "</td></tr></table>";
    $str .= "<br><br><br>";
    return $str;
} 

function main_fehler()
{
    global $sql;
    $str = "<br><table width='400' align='center' class='rand'>";
    $str .= "<tr><td class='ueberschrift'>Artikel-Daten &auml;ndern</td></tr>";
    $str .= "<tr><td>";
    $str .= "<p align='justify'>Leider ist ein Datenbankfehler aufgetreten. Bitte probieren sie es sp&auml;ter erneut. Sollte der Fehler dann weiterhin bestehen schicken sie bitte eine E-Mail an den <a href='mailto:jensman@jensmans-welt.de?body=" . $sql . "'>Webmaster</a>.</p>";
    $str .= "</table>";
    $str .= "<br><br><br>";
    return $str;
} 

?>
