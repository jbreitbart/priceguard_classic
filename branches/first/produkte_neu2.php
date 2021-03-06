<?php
require_once("conf.php");
require_once("session.inc");

require_once 'PEAR.php';
require_once 'Services/Amazon.php';

$pricewatch = session_id();

$produkt[1] = "1";

if ($pricewatch == "") {
    HEADER("Location: " . link_s("index.php"));
} 

if ($asin == "") {
    HEADER("Location: " . link_s("index.php"));
} 

if (amazon_asin_anfrage($asin)) {
    //$produkt[OURPRICE] = substr($produkt[OURPRICE], 4);
    //$produkt[PRODUCTNAME] = ereg_replace("ü", "�", $produkt[PRODUCTNAME]);
    //$produkt[PRODUCTNAME] = ereg_replace("ö", "�", $produkt[PRODUCTNAME]);
    if (($produkt[PRODUCTNAME] == "") OR ($produkt[OURPRICE] == "")) {
        $asin_fehler = 1;
    } 
} else {
    $asin_fehler = 1;
} 

$text = $head;
$text .= $body;
$text .= menu("");
if ($asin_fehler != 1) {
    $text .= main();
} else {
    $text .= main_fehler();
} 

$text .= $foot;

echo gzipoutput($text);
// ===========================================================================
function main()
{
    global $asin, $produkt, $name;
    $str = "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td class='ueberschrift'>Neuen Artikel hinzuf&uuml;gen</td></tr>";
    if ($produkt[IMAGEURLMEDIUM] != "") {
        $str .= "<tr><td align='center'><br><img src='" . $produkt[IMAGEURLMEDIUM] . "'></td></tr>";
    } 
    $str .= "<tr><td>";
    $str .= "<p align='justify'>Bitte f&uuml;llen sie das unten angezeigte Formular aus. Die bereits eingetragenen Daten wurden von der Amazon.de Seite ermittelt, diese Daten k&ouml;nnen nat&uuml;rlich wie gew&uuml;scht ver&auml;ndert werden. Wir empfehlen allerdings den Preis unver&auml;ndert zu lassen.<br>In das Feld \"Erinnerungs-Preis\" tragen sie bitte den Betrag ein bei dessen Unterschreitung ihnen $name eine Meldung per E-Mail schicken soll. Wenn sie bei jeder &Auml;nderung, egal ob das Produkt teurer oder billiger geworden ist, eine Meldung erhalten wollen dann setzten sie bitte einen Hacken im untersten Feld.</p>";
    $str .= "</table>";
    $str .= "<br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr>";
    $str .= "<td align='center' wdith='25%'><form method='post' action='" . link_s("produkte_neu3.php") . "'>";
    $str .= "<b>Name:</b>";
    $str .= "</td>";
    $str .= "<td align='center'><input type='text' class='input' name='produkt_name' value=\"$produkt[PRODUCTNAME]\" size='45' maxlength='250'>";
    $str .= "</td></tr>";
    $str .= "<tr><td><b>Kategorie:</b></td>";
    $str .= "<td align='center'><input type='text' class='input' name='produkt_kategorie' value='$produkt[CATALOG]' size='45' maxlength='250'></td></tr>";
    $str .= "<tr><td><b>Preis (EUR):</b></td>";
    $str .= "<td align='center'><input type='text' class='input' name='produkt_preis' value='$produkt[OURPRICE]' size='45' maxlength='250'></td></tr>";
    $str .= "<tr><td><b>Erinnerungs-Preis (EUR):</b></td>";
    $str .= "<td align='center'><input type='text' class='input' name='erinnerung_preis' value='" . ($produkt[OURPRICE]-0.01) . "' size='45' maxlength='250'></td></tr>";
    $str .= "<tr><td colspan='2' align='center'><input type='checkbox' name='erinnerung_immer' value='1' checked> <b>Bei jeder Preis &Auml;nderung erinnern.</b></td></tr>";
    $str .= "<input type='hidden' name='produkt_bild_klein' value='$produkt[IMAGEURLSMALL]'>";
    $str .= "<input type='hidden' name='produkt_bild_mittel' value='$produkt[IMAGEURLMEDIUM]'>";
    $str .= "<tr height='30'><td colspan='2' align='center' valign='middle'>";
    $str .= "<input type='submit' class='forminput' name='Weiter' value='   Artikel eintragen   '></form>";
    $str .= "</td></tr></table>";
    $str .= "<br><br><br>";
    return $str;
} 

function main_fehler()
{
    $str = "<br><table width='400' align='center' class='rand'>";
    $str .= "<tr><td align='center'><b>Fehler</b></td></tr>";
    $str .= "<tr><td>";
    $str .= "<p align='justify'>Leider gab es einen Fehler bei der Identifikation des Artikels. Bitte &uuml;berpr&uuml;fen sie den eingetragenen Link bzw. die eingetragene ASIN und probieren es sp&auml;ter erneut. Sollten sie das Problem nicht l&ouml;sen k&ouml;nnen, dann schreiben sie bitte eine E-Mail an den <a href='mailto:jensman@jensmans-welt.de'>Webmaster</a> in der sie das Problem beschreiben.</p>";
    $str .= "</table>";
    $str .= "<br><br><br>";
    return $str;
} 

function amazon_asin_anfrage ($asin)
{
	global $produkt;
	$amazon = new Services_Amazon("0VVNZKPPSV6JSP0ZKVG2", "8l6msSRF9jG37FPNvWU0r2uxoP2Gr7V/zJqXmjZo");
	$amazon -> setLocale("DE");
	
	$options = array();
	$options['ResponseGroup'] = 'Images,Offers,Small';
	$options['MerchantId'] = 'A3JWKAKR8XB7XF';
	$result = $amazon->ItemLookup($asin, $options);

	//var_dump($result);

	if (PEAR::isError($result)) {
		return false;
	}

	$produkt[IMAGEURLSMALL] = $result["Item"][0]["SmallImage"]["URL"];
	$produkt[IMAGEURLMEDIUM] = $result["Item"][0]["MediumImage"]["URL"];
	$produkt[PRODUCTNAME] = $result["Item"][0]["ItemAttributes"]["Title"];
	$produkt[OURPRICE] = $result["Item"][0]["Offers"]["Offer"]["OfferListing"]["Price"]["Amount"];
	$produkt[OURPRICE] /= 100;
	$produkt[CATALOG] = $result["Item"][0]["ItemAttributes"]["ProductGroup"];

	return true;
}

?>
