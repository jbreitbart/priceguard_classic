<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

error_reporting(0);

if ($pricewatch == "") {
    HEADER("Location: " . link_s("index.php"));
} 

session_unregister("asin");

$lk = connect();
$produkte_db = mysql_db_query($mysql_db, "SELECT NAME, ASIN, KATEGORIE, BILD_KLEIN FROM `pricewatch_produkte` WHERE ID =" . $user[ID] . " ORDER BY `KATEGORIE` ASC, `NAME` ASC", $lk);
mysql_close($lk);

$text = $head;
$text .= $body;
$text .= menu("produkte");
$text .= main();

$text .= $foot;

echo gzipoutput($text);

// ===========================================================================

function main()
{
    global $name, $produkte_db;
    $str = "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td class='ueberschrift'>&Uuml;berwachte Artikel</td></tr>";
    $str .= "<tr><td>";
    $str .= "<p align='justify'>Auf dieser Seite finden sie eine &Uuml;berischt aller von $name f&uuml;r sie &uuml;berwachten Produkte. Um sich ein Produkt bei Amazon.de anzusehen klicken sie einfach auf das entsprechende Bild.<br>Alle rot markierten Produkte sind im moment billiger oder gleich teuer wie der von ihnen festgesezte \"Erinnerungs-Preis\".</p></td></tr>";
    $str .= "</table>";
    $str .= "<br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td align='center'><a href='" . link_s("produkte_neu.php") . "'>Neuen Artikel hinzuf&uuml;gen</a></td></tr>";
    $str .= "</table><br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $kategorie = "";
    while ($arr = mysql_fetch_array($produkte_db, MYSQL_ASSOC)) {
        if ($kategorie != $arr[KATEGORIE]) {
            $kategorie = $arr[KATEGORIE];
            $str .= "<tr><td height='30' colspan='2' class='ueberschrift'>" . $arr[KATEGORIE] . "</td></tr>";
        } 
        $str .= "<tr>";
        // Bilder erprfen
         $size = getimagesize(stripslashes($arr[BILD_KLEIN]));
        $str .= "<td width='1%' align='center'><a href='" . amazon_link($arr[ASIN]) . "' target='_blank'>";
        if ($arr[BILD_KLEIN] != "") {
            $str .= "<img src='" . stripslashes($arr[BILD_KLEIN]) . "' border='0' alt='" . htmlentities(stripslashes($arr[NAME])) . " bei Amazon.de'>";
        } else {
            $str .= "Amazon.de";
        } 
        $str .= "</a></td>";
        $str .= "<td><b>" . htmlentities(stripslashes($arr[NAME])) . "</b></td>";
        $str .= "<td width='1%'><a href='" . link_s("produkte_eigenschaften.php") . "&asin=" .$arr[ASIN]. "'><img alt='Artikel-Daten &auml;ndern' src='bilder/eigenschaften.png' border='0'></a></td>";
        $str .= "<td width='1%'><a href='" . link_s("produkte_loeschen.php") . "&asin=" .$arr[ASIN]. "'><img alt='Artikel L&ouml;schen' src='bilder/loeschen.png' border='0'></a></td>";
        $str .= "</tr>";
    } // while
    if ($kategorie == "") {
        $str .= "<tr><td align='center'><b>Im moment werden keine Produkte &uuml;berwacht</b></td></tr>";
    } 
    $str .= "</table><br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td align='center'><a href='" . link_s("produkte_neu.php") . "'>Neuen Artikel hinzuf&uuml;gen</a></td></tr>";
    $str .= "</table>";
    $str .= "<br><br><br>";
    return $str;
} 

?>
