<?php
$zeit_start = time();
$email = "";
// Anfangszeit ausgeben
ausgabe ("Scriptstart");

ini_set("include_path", ".:/kunden/116846_36208/pear/PEAR:/usr/local/lib/php");
require_once 'PEAR.php';
require_once 'Services/Amazon.php';

//ausgabe ("Deutsches Datum"); Deaktiviert wegen settype
//setlocale(LC_ALL, "ge");

ausgabe ("Amazon.inc einfgen");
include ("../../amazon.inc");

ausgabe ("Erstelle Variablen");
$produkt_amazon[1] = "1";
$mysql_host = "mysql4.priceguard.de";
$mysql_user="db116846_14";
$mysql_pass="Jensman23";
$mysql_db="db116846_14";
$name = "Amazon.de Pricewatch";

$amazon = new Services_Amazon("0VVNZKPPSV6JSP0ZKVG2", "8l6msSRF9jG37FPNvWU0r2uxoP2Gr7V/zJqXmjZo");
$amazon -> setLocale("DE");
$amazon_options = array();
$amazon_options['ResponseGroup'] = 'Offers';
$amazon_options['MerchantId'] = 'A3JWKAKR8XB7XF';



// Datenbank Verbindung aufbauen
$lk = connect_db();

// Daten aus der Datenbank auslesen
ausgabe ("USER aus Datenbank auslesen");

if (! $user_db = query_db("SELECT HIGH_PRIORITY ID, EMAIL FROM pricewatch_user")) {
    ausgabe ("ABRUCH");
    sendebericht();
    exit;
}

while ($user = mysql_fetch_array($user_db, MYSQL_ASSOC)) {
    $email_text = "";
    ausgabe ("Produkte von User " . $user[ID] . " auslesen");
    $produkte_db = query_db("SELECT HIGH_PRIORITY ASIN, NAME, PREIS_START, PREIS_AKTUELL, PREIS_NACHRICHT, PREIS_NACHRICHT_GESENDET FROM pricewatch_produkte WHERE ID=" . $user[ID]);

    while ($produkt = mysql_fetch_array($produkte_db, MYSQL_ASSOC)) {
        ausgabe ("Erstelle bzw. bearbeite Variablen");
        $user[EMAIL] = stripslashes($user[EMAIL]);
        $produkt[NAME] = stripslashes($produkt[NAME]);
        $db_AKTUELLEN_PREIS = 0;
        $db_PREIS_NACHRICHT_GESENDET = 0; 

        // Produkt bei Amazon.de suchen
        ausgabe ("Produkt bei Amazon.de suchen");
        if (amazon_asin_anfrage($produkt[ASIN])) {
            ausgabe ("Produkt bei Amazon.de gefunden");
			ausgabe ($produkt_amazon[OURPRICE]);
            settype ($produkt_amazon[OURPRICE], "double"); //Preis in Zahlen umwandeln fr Vergleichsoperationen 
            settype ($produkt[PREIS_START], "double"); //Preis in Zahlen umwandeln fr Vergleichsoperationen 
            settype ($produkt[PREIS_AKTUELL], "double"); //Preis in Zahlen umwandeln fr Vergleichsoperationen   
			ausgabe ($produkt_amazon[OURPRICE]);
            // =====================================================
            // ï¿½erprfe ob PREIS_AKTUELL==$produkt_amazon[OURPRICE]
            // Ja: Nachricht muï¿½nicht gesendet werden
            // Nein: AKTUELLEN PREIS muï¿½in DB aktualisiert werden
            // Nein: ï¿½erprfen ob Nachricht gesendet werden muï¿½            // Nein: PREIS_NACHRICHT==-1
            // Nein: Ja: Nachricht muï¿½gesendet werden
            // Nein: Ja: PREIS_NACHRICHT_GESENDET muï¿½in DB aktualisiert werden
            // Nein: Nein: $produkt_amazon[OURPRICE]<=PREIS_NACHRICHT ||Nicht mehr: UND PREIS_NACHRICHT_GESENDET!=$produkt_amazon[OURPRICE]
            // Nein: Nein: Ja: ||Nicht mehr: Nachricht muï¿½gesendet werden; Nachricht abhï¿½ig davon ob Preis gestiegen oder gefallen ist
            // Nein: Nein: Ja: PREIS_NACHRICHT_GESENDET muï¿½in DB aktualisiert werden
            // Nein: Nein: Nein: Nachricht muï¿½nicht gesendet werden
            // =====================================================
            if ($produkt[PREIS_AKTUELL] == $produkt_amazon[OURPRICE]) {
                ausgabe ("Aktueller Preis ist bereits in der Datenbank => Es muï¿½keine Nachricht gesendet werden");
            } else { // $produkt[PREIS_AKTUELL]==$produkt_amazon[OURPRICE]
                ausgabe ("Aktueller Preis und Datenbank stimmen nicht berein");
                $db_AKTUELLEN_PREIS = 1;
                if ($produkt[PREIS_NACHRICHT] == -1) {
                    ausgabe ("Meldung bei jeder Preisï¿½derung => Es muï¿½eine Nachricht gesendet werden");
                    $db_PREIS_NACHRICHT_GESENDET = 1;
                    $email_text .= produkt_email();
                } else { // $produkt[PREIS_NACHRICHT]==-1
                    // Nein: Nein: $produkt_amazon[OURPRICE]<=PREIS_NACHRICHT UND PREIS_NACHRICHT_GESENDET!=$produkt_amazon[OURPRICE]
                    // Nein: Nein: Ja: Nachricht muï¿½gesendet werden; Nachricht abhï¿½ig davon ob Preis gestiegen oder gefallen ist
                    // Nein: Nein: Ja: PREIS_NACHRICHT_GESENDET muï¿½in DB aktualisiert werden
                    // Nein: Nein: Nein: Nachricht muï¿½nicht gesendet werden
                    ausgabe ("Meldung nur bei Unterschreitung von bestimmten Preis");
                    if ($produkt_amazon[OURPRICE] <= $produkt[PREIS_NACHRICHT]) {
                        ausgabe ("Unterschreitung des Preises => Es muï¿½eine Nachricht gesendet werden");
                        $db_PREIS_NACHRICHT_GESENDET = 1;
                        $email_text .= produkt_email();
                    } else { // $produkt_amazon[OURPRICE]<=$produkt[PREIS_NACHRICHT]
                        ausgabe ("Preis nicht unterschritten => Es muï¿½keine Nachricht gesendet werden");
                    } //$produkt_amazon[OURPRICE]<=$produkt[PREIS_NACHRICHT]
                } //$produkt[PREIS_NACHRICHT]==-1
            } //$produkt[PREIS_AKTUELL]==$produkt_amazon[OURPRICE]  
            // Datenbank aktualisieren
            if ($db_AKTUELLEN_PREIS == 1) {
                ausgabe ("Erstelle Datenbank-Query");
                $sql = "UPDATE pricewatch_produkte SET PREIS_AKTUELL=" . $produkt_amazon[OURPRICE];
                if ($db_PREIS_NACHRICHT_GESENDET == 1) {
                    $sql .= ", PREIS_NACHRICHT_GESENDET=" . $produkt_amazon[OURPRICE];
                } 
                $sql .= " WHERE ID=" . $user[ID] . " AND ASIN=\"" . addslashes($produkt[ASIN]) . "\" LIMIT 1";
                query_db($sql);
            } 
        } else { // amazon_asin_anfrage($produkt[ASIN])
            ausgabe ("\nFEHLER");
            ausgabe ("Produkt " . $produkt[ASIN] . ", " . $produkt[NAME] . " bei Amazon.de nicht gefunden. User: " . $user[ID]);
            ausgabe ("FEHLER\n");
        } //amazon_asin_anfrage($produkt[ASIN])
    } // while $produkt=mysql_fetch_array($produkte_db, MYSQL_ASSOC)  
    // E-Mail senden?
    if ($email_text != "") {
        ausgabe ("E-Mail wird vorbereitet");
        $email_text = "Sehr geehrte Damen und Herren,\nwir möchten sie auf die folgenden Preisänderungen aufmerksam machen.\n\n" . $email_text . "Mit freundlichen Grüßen\nDer Webmaster\n\nAchtung: Die E-Mail wurde automatisch erstellt, bitte nicht darauf antworten.";
        $email_subject = $name . ": Preisänderungen";
        if (sendemail($user[EMAIL], $email_subject, $email_text)) {
            ausgabe ("E-Mail wurde erfolgreich gesendet");
        } else { // sendemail($user[EMAIL], $email_subject, $email_text)
            ausgabe ("\nFEHLER");
            ausgabe ("Fehler beim senden von E-Mail an User " . $user[ID] . "; E-Mail: " . $user[EMAIL]);
            ausgabe ("FEHLER\n");
        } //sendemail($user[EMAIL], $email_subject, $email_text)
    } //$email_text!=""	
} // while $user=mysql_fetch_array($user_db, MYSQL_ASSOC
// Datenbank optimieren
if ((strftime("%d") == "01") OR (strftime("%d") == "15")) {
    ausgabe ("Datenbank optimieren");
    query_db("OPTIMIZE TABLE pricewatch_produkte, pricewatch_user");
} 
// Datenbank Verbindung schlieï¿½n
$erg = close_db();
// Zeit ausgeben
ausgabe ("Scriptende");

$zeit_ende = time();
$zeit_dauer = $zeit_ende - $zeit_start;
ausgabe ("Dauer des Scriptes: " . $zeit_dauer . " Sekunden");
// E-Mail senden
// sendebericht();
exit;

// ===========================================================================
// ===========================================================================
function ausgabe($text)
{
    global $email;
    $text = "[" . strftime("%H:%M:%S") . "]: " . $text . "\n";
    $email .= $text;
    echo $text;
} 

function sendebericht ()
{
    global $email;
    $erg = mail("jensman@jensmans-welt.de", "Pricewatch Bericht: " . strftime("%d.%m.%Y"), $email, "FROM: noreply@priceguard.de");
    if ($erg == true) {
        ausgabe("E-Mail erfolgreich gesendet");
    } else {
        ausgabe ("Fehler beim senden der E-Mail");
    } 
    return $erg;
} 

function connect_db ()
{
    global $mysql_host, $mysql_user, $mysql_pass;
    if (! $lk = mysql_connect("$mysql_host", "$mysql_user", "$mysql_pass")) {
        ausgabe ("Die Verbindung zu " . $mysql_host . " konnte nicht hergestellt werden");
        ausgabe ("ABRUCH");
        sendebericht;
        exit;
    } else {
        ausgabe ("Die Verbindung zu " . $mysql_host . " wurde hergestellt");
    } 
    return $lk;
} 

function close_db ()
{
    global $lk, $mysql_host;
    $erg = mysql_close($lk);
    if ($erg == false) {
        ausgabe ("Die Verbindung zu " . $mysql_host . " konnte nicht geschlossen werden");
    } else {
        ausgabe ("Die Verbindung zu " . $mysql_host . " wurde geschlossen");
    } 
} 

function query_db($sql)
{
    global $lk, $mysql_db;
    ausgabe ("Datenbankquerie: " . $sql);
    if (! $ret = mysql_db_query($mysql_db, $sql, $lk)) {
        ausgabe ("Fehler bei der Ausfhrung von Datenbankquerie");
    } else {
        ausgabe ("Datenbankquerie erfolgrfeich ausgefhrt");
    } 
    return $ret;
} 

function amazon_asin_anfrage ($asin)
{
	global $produkt_amazon, $amazon, $amazon_options;
	$result = $amazon->ItemLookup($asin, $amazon_options);

	if (PEAR::isError($result)) {
		return false;
	}

	//var_dump($result);

	$produkt_amazon[OURPRICE] = $result["Item"][0]["Offers"]["Offer"]["OfferListing"]["Price"]["Amount"];
	$produkt_amazon[OURPRICE] /= 100;
	//echo "<br><br> ABCD <br><br>". $produkt_amazon[OURPRICE];
	return true;
} 

function produkt_email ()
{
    global $produkt, $produkt_amazon;
    ausgabe ("E-Mail Text wird erstellt");
    $str = "Produkt        : " . $produkt[NAME] . "\n";
    $str .= "Start Preis    : " . $produkt[PREIS_START] . " EUR" . "\n";
    $str .= "Aktueller Preis: " . $produkt_amazon[OURPRICE] . " EUR" . "\n";
    $str .= "Ersparniss     : " . ($produkt[PREIS_START] - $produkt_amazon[OURPRICE]) . " EUR" . "\n";
    $str .= amazon_link($produkt[ASIN]) . "\n\n";
    return $str;
} 

function sendemail ($ziel, $subject, $text)
{
    $erg = mail($ziel, $subject, $text, "FROM: noreply@priceguard.de");
    return $erg;
} 

?>
