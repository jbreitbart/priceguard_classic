<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

if ($pricewatch == "") {
    HEADER("Location: " . link_s("index.php"));
} 

if ($sicher == 1) {
    $lk = connect(); 
    // Account löschen
    $account_db = mysql_db_query($mysql_db, "DELETE FROM pricewatch_user WHERE ID=\"" . $user[ID] . "\" LIMIT 1", $lk);
    if ($account_db == true) {
        // Produkt Erinnertungen löschen
        $produkt_db = mysql_db_query($mysql_db, "DELETE FROM pricewatch_produkte WHERE ID=\"" . $user[ID] . "\"", $lk);
        $geloescht = 1;
        if ($produkt_db == false) {
            sendemail("jensman@jensmans-welt.de", "" . $name . ": Fehler beim Account l&ouml;schen", "Daten in pricewatch_user gelöscht, Problem beim löschen der entsprechenden Produkte. ID=" . $user[ID]);
        } 
    } else {
        $geloescht = 0;
    } 
    mysql_close($lk);
} 

if ($geloescht == 1) { // Ausloggen
    session_destroy();
    if ($pricewatch_cookie != "") {
        $date = date("r", strtotime("-1 day"));
        Header("Set-Cookie: pricewatch_cookie=deleted; expires=$date");
    } 
    $pricewatch = "";
} 

$text = $head;
$text .= $body;
$text .= menu("");
$text .= main();

$text .= $foot;

echo gzipoutput($text);
// ===========================================================================
function main()
{
    global $name, $sicher, $geloescht;
    if ($geloescht == 1) {
        $str = "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td class='ueberschrift'>Account gel&ouml;schen</td></tr>";
        $str .= "<tr><td>";
        $str .= "<p align='justify'>Ihr Account und alle dazugeh&ouml;rigen Daten wurden gel&ouml;scht.</p></td></tr>";
        $str .= "</table>";
    } 
    if (($sicher == 1) AND ($geloescht == 0)) {
        $str = "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td class='ueberschrift'>Datenbank Fehler</td></tr>";
        $str .= "<tr><td>";
        $str .= "<p align='justify'>Leider gibt es ein Problem mit der Datenbank, bitte probieren sie es sp&auml;ter noch einmal. Ihr Account wurde noch nicht gel&ouml;scht.</p></td></tr>";
        $str .= "</table>";
    } 
    if ($sicher != 1) {
        $str = "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td class='ueberschrift'>Account l&ouml;schen</td></tr>";
        $str .= "<tr><td>";
        $str .= "<p align='justify'>Wenn sie ihren Account l&ouml;schen dann werden ihre E-Mail Adresse und alle von ihnen gesetzte Produkt-&Uuml;berwachungen gel&ouml;scht. Dies kann auch nicht vom Webseitenbetreiber r&uuml;ckg&auml;nig gemacht werden, l&ouml;schen sie ihren Account also nur wenn sie sich sicher sind das er nicht mehr gebraucht wird.</p></td></tr>";
        $str .= "</table>";
        $str .= "<br><br>";
        $str .= "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td class='ueberschrift'><a href='" . link_s("account_loeschen.php") . "&sicher=1'>Account endg&uuml;ltig l&ouml;schen!</a></td></tr>";
        $str .= "</table>";
    } 
    $str .= "<br><br><br>";
    return $str;
} 

?>