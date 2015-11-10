<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

if ($email != "") {
    $lk = connect();
    $db = mysql_db_query($mysql_db, "SELECT * FROM pricewatch_user WHERE EMAIL=\"" . addslashes(strtolower($email)) . "\"", $lk);
    mysql_close($lk);
    if ($daten = mysql_fetch_array($db, MYSQL_ASSOC)) {
        $erg = true;
        $erg_mail = sendemail(stripslashes($daten[EMAIL]), $name . ": Passwort", "Das Passwort lautet: " . stripslashes($daten[PASSWORT]));
    } else {
        $erg = false;
    } 
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
    global $email, $erg, $erg_mail;
    if ($email != "") {
        $str .= "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td class='ueberschrift'>Passwort vergessen</td></tr>";
        $str .= "<tr><td><p align='justify'>";
        if ($erg == false) {
            $str .= "Es wurde keine Benutzter mit der angegeben E-Mail Adresse gefunden.";
            $email = "";
        } else {
            if ($erg_mail == false) {
                $str .= "Leider gab es einen Fehler beim versenden der E-Mail. Bitte probieren sie es sp&auml;ter noch einmal.";
            } else {
                $str .= "Eine E-Mail mit ihrem Passwort wurde soeben verschickt.";
            } 
        } 
        $str .= "</p></td></tr>";
        $str .= "</table>";
    } 
    if ($email == "") {
        $str .= "<br><table width='400' align='center' class='rand'>";
        if ($erg != false) {
            $str .= "<tr><td class='ueberschrift'>Passwort vergessen</td></tr>";
        } 
        $str .= "<tr><td><p align='justify'>Tragen sie bitte unten ihre E-Mail Adresse ein und ihr Passwort wird ihnen dann zu geschickt.</p></td></tr>";
        $str .= "<tr><td><table width='75%' align='center'>";
        $str .= "<form method='post' action='pass_vergessen.php'>";
        $str .= "<tr><td>E-Mail:</td>";
        $str .= "<td align='center'><input type='text' name='email' class='input'></td></tr>";
        $str .= "<tr><td colspan='2' align='center'><input type='submit' name='Pass_senden' class='forminput' value='Passwort zusenden'></form></td></tr>";
        $str .= "</table></td></tr></table>";
    } 
    $str .= "<br><br><br>";
    return $str;
} 

?>