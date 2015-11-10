<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

if ($pricewatch == "") {
    HEADER("Location: " . link_s("index.php"));
} 

if ($fertig == 1) {
    $fehler = "";
    $email1 = addslashes(trim($email1));
    $email2 = addslashes(trim($email2));
    $pass1 = addslashes($pass1);
    $pass2 = addslashes($pass2);
    $pass_alt = addslashes($pass_alt);
    $lk = connect();
    $db = mysql_db_query($mysql_db, "SELECT * FROM pricewatch_user WHERE EMAIL=\"" . addslashes(strtolower($user[EMAIL])) . "\" AND PASSWORT=\"" . addslashes($pass_alt) . "\" LIMIT 1", $lk);
    if (mysql_fetch_array($db, MYSQL_ASSOC)) {
        if ($email1 == $email2) {
            if (validateEmail($email1)) {
                if ($email1 != "") {
                    $email_aendern = 1;
                } 
            } else {
                $fehler = "Bitte geben sie eine echte E-Mail Adresse ein.";
            } 
        } else {
            $fehler = "Die eingegebenen E-Mail Adressen stimmen nicht &uuml;berein.";
        } 
        if ($pass1 == $pass2) {
            if ($pass1 != "") {
                $pass_aendern = 1;
            } 
        } else {
            $fehler = "Die eingebenen Passw&ouml;rter stimmen nicht &uuml;berein.";
        } 
    } else {
        $fehler = "Bitte geben sie das richtige Passwort ein.";
    } 
    if (($email_aendern == 1) AND ($pass_aendern == 1)) {
        $sql = "UPDATE pricewatch_user SET EMAIL=\"" . $email1 . "\", PASSWORT=\"" . $pass1 . "\" WHERE ID=\"" . $user[ID] . "\" LIMIT 1";
        $lk = connect();
        $erg = mysql_db_query($mysql_db, $sql, $lk);
        mysql_close($lk);
        if ($erg == true) {
            $meldung = "Ihre E-Mail Adresse und ihr Passwort sind ge&auml;ndert.";
        } else {
            $fehler = "Leider gibt es im moment ein Problem mit der Datenbank. Bitte probieren sie es sp&auml;ter noch einmal.";
        } 
        $email_aendern = 0;
        $pass_aendern = 0;
    } 
    if ($email_aendern == 1) {
        $sql = "UPDATE pricewatch_user SET EMAIL=\"" . $email1 . "\" WHERE ID=\"" . $user[ID] . "\" LIMIT 1";
        $lk = connect();
        $erg = mysql_db_query($mysql_db, $sql, $lk);
        mysql_close($lk);
        if ($erg == true) {
            $meldung = "Ihre E-Mail Adresse ist ge&auml;ndert.";
        } else {
            $fehler = "Leider gibt es im moment ein Problem mit der Datenbank. Bitte probieren sie es sp&auml;ter noch einmal.";
        } 
        $email_aendern = 0;
    } 
    if ($pass_aendern == 1) {
        $sql = "UPDATE pricewatch_user SET PASSWORT=\"" . $pass1 . "\" WHERE ID=\"" . $user[ID] . "\" LIMIT 1";
        $erg = mysql_db_query($mysql_db, $sql, $lk);
        mysql_close($lk);
        if ($erg == true) {
            $meldung = "Ihr Passwort ist ge&auml;ndert.";
        } else {
            $fehler = "Leider gibt es im moment ein Problem mit der Datenbank. Bitte probieren sie es sp&auml;ter noch einmal.";
        } 
        $pass_aendern = 0;
    } 
} 

if ($meldung != "") { // Ausloggen
    session_destroy();
    if ($pricewatch_cookie != "") {
        $date = date("r", strtotime("-1 day"));
        Header("Set-Cookie: pricewatch_cookie=deleted; expires=$date");
    } 
    $pricewatch = "";
} 

$text = $head;
$text .= $body;
$text .= menu("account");
$text .= main();

$text .= $foot;

echo gzipoutput($text);
// ===========================================================================
function main()
{
    global $name, $user, $fehler, $meldung;
    if ($meldung == "") {
        $str = "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td class='ueberschrift'>Account Einstellungen</td></tr>";
        $str .= "<tr><td>";
        $str .= "<p align='justify'>Hier k&ouml;nnen sie die E-Mail Adresse und Passwort ihres Accounts &auml;ndern. Wenn sie ihr E-Mail Adresse oder das Passwort nicht &auml;ndern wollen, dann lassen sie bitte die entsprechenden Felder leer. <b>Das alte Passwort mu&szlig; immer eintragen werden!</b></p></td></tr>";
        $str .= "</table>";
        $str .= "<br><br>";
        if ($fehler != "") {
            $str .= "<table width='400' align='center' class='rand'>";
            $str .= "<tr><td align='center'><b>Es ist ein Fehler augetreten!</b></td></tr>";
            $str .= "<tr><td>";
            $str .= "<p align='justify'>" . $fehler . "</p></td></tr>";
            $str .= "</table>";
            $str .= "<br><br>";
        } 
        $str .= "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td><table width='85%' align='center'>";
        $str .= "<form method='post' action='" . link_s("account.php") . "&fertig=1'>";
        $str .= "<tr height='18'><td>Aktuelle E-Mail:</td>";
        $str .= "<td align='center'>" . htmlentities(stripslashes($user[EMAIL])) . "</td></tr>";
        $str .= "<tr><td>Neue E-Mail:</td>";
        $str .= "<td align='center'><input type='text' name='email1' class='input'></td></tr>";
        $str .= "<tr><td>Neue E-Mail Wdh.:</td>";
        $str .= "<td align='center'><input type='text' name='email2' class='input'></td></tr>";
        $str .= "<tr><td>Neues Passwort:</td>";
        $str .= "<td align='center'><input type='password' name='pass1' class='input'></td></tr>";
        $str .= "<tr><td>Neues Passwort Wdh.:</td>";
        $str .= "<td align='center'><input type='password' name='pass2' class='input'></td></tr>";
        $str .= "<tr><td>Altes Passwort:</td>";
        $str .= "<td align='center'><input type='password' name='pass_alt' class='input'></td></tr>";
        $str .= "<tr><td colspan='2' align='center'><br><input type='submit' name='Anmelden' class='forminput' value='Daten &auml;ndern'></form></td></tr>";
        $str .= "</table></td></tr>";
        $str .= "</table><br><br>";
        $str .= "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td class='ueberschrift'>Account l&ouml;schen</td></tr>";
        $str .= "<tr><td>";
        $str .= "<p align='justify'><b>Achtung: Das l&ouml;schen eines Accounts kann nicht r&uuml;ckg&auml;nig gemacht werden!</b></p></td></tr>";
        $str .= "<tr><td align='center'><br><form method='post' action='" . link_s("account_loeschen.php") . "'><input type='submit' name='loeschen' class='forminput' value='Account l&ouml;schen'></form></td></tr>";
        $str .= "</table>";
    } else {
        $str .= "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td><p align='center'><b>$meldung</b></p></td></tr>";
        $str .= "<tr><td><p align='justify'> Sie wurden aus Sicherheitsgr&uuml;nden ausgeloggt, k&ouml;nnen sich aber nat&uuml;rlich mit ihren neuen Daten sofort wieder einloggen.</p></td></tr>";
        $str .= "<tr><td align='center'><br><a href='index.php'>Zur Startseite</a></td></tr>";
        $str .= "</table>";
    } 
    $str .= "<br><br><br>";
    return $str;
} 

?>