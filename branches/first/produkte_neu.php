<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

if ($pricewatch == "") {
    HEADER("Location: " . link_s("index.php"));
} 

if ($asin_fehler == '1') {
    $asin = $link_asin;
    session_register("asin");
    HEADER("Location: " . link_s("produkte_neu2.php"));
    exit;
} 

if ($link_asin != "") {
    if ($asin = asin_search($link_asin)) {
        session_register("asin");
        HEADER("Location: " . link_s("produkte_neu2.php"));
        exit;
    } else {
        $asin_fehler = 1;
    } 
} 

session_unregister("asin");

$text = $head;
$text .= $body;
$text .= menu("");
$text .= main();

$text .= $foot;

echo gzipoutput($text);
// ===========================================================================
function main()
{
    global $link_asin, $asin_fehler;
    $str = "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td class='ueberschrift'>Neuen Artikel hinzuf&uuml;gen</td></tr>";
    if ($asin_fehler == 1) {
        $str .= "<tr><td align='center'><img src='bilder/produkte_neu_asin.png' alt='Beispiel Screenshot'></td></tr>";
        $str .= "<tr><td>";
        $str .= "<p align='justify'>Leider gab es einen Fehler bei der automatischen ASIN Erkennung. Bitte geben sie in das untere Feld die ASIN (=Amazon Standard Item Number) des gew&uuml;nschten Artikels ein. Die ASIN finden sie auf der Amazon.de Seite. Wenn sie weiterhin Probleme haben, dann melden sie sich bitte bei dem <a href='mailto:jensman@jensmans-welt.de'>Webmaster</a>.</p>";
    } else {
        $str .= "<tr><td align='center' ><img src='bilder/produkte_neu.png' alt='Beispiel Screenshot'></td></tr>";
        $str .= "<tr><td>";
        $str .= "<p align='justify'>Bitte f&uuml;gen sie den Link zu dem gew&uuml;nschten <a href='http://www.amazon.de/exec/obidos/redirect-home?tag=gschaftshuonl-21&site=home' target='_blank'>Amazon.de</a> Produkt in das untere Feld ein und klicken auf weiter.<br>Wichtig: Der Link muss zum &Uuml;berblick des Artikels f&uuml;hren (siehe Screenshot).</p></td></tr>";
    } 
    $str .= "</table>";
    $str .= "<br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr>";
    $str .= "<td align='center' wdith='25%'><form method='post' action='" . link_s("produkte_neu.php") . "'>";
    if ($asin_fehler == 1) {
        $str .= "<b>ASIN</b>";
    } else {
        $str .= "<b>Link:</b>";
    } 
    $str .= "</td>";
    $str .= "<td align='center'><input type='text' class='input' name='link_asin' value='$link_asin' size='50'>";
    $str .= "</td></tr>";
    if ($asin_fehler == 1) {
        $str .= "<input type='hidden' name='asin_fehler' value='1'>";
    } 
    $str .= "<tr height='30'><td colspan='2' align='center' valign='middle'>";
    $str .= "<input type='submit' class='forminput' name='Weiter' value='   Weiter   '></form>";
    $str .= "</td></tr></table>";
    $str .= "<br><br><br>";
    return $str;
} 

?>