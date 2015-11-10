<?php
require_once("conf.php");

$text = $head;
$text .= $body;
$text .= menu("");
$text .= main();

$text .= $foot;

echo gzipoutput($text);
// ===========================================================================
function main()
{
    global $email, $pass, $pass2, $mysql_db, $name;
    if (($email != "") and ($pass != "")) { // Variablen nicht leer?
        if ((validateEmail($email))) { // Email in Ordnung?
            if ($pass == $pass2) { // Passwort nicht verschrieben?
                $lk = connect();
                $db = mysql_db_query($mysql_db, "SELECT * FROM pricewatch_user WHERE EMAIL=\"" . trim(addslashes(strtolower($email))) . "\" LIMIT 1", $lk);
                if (!$arr = mysql_fetch_array($db, MYSQL_ASSOC)) { // E-Mail schon eingetragen?
                    $db = mysql_db_query($mysql_db, "INSERT pricewatch_user (EMAIL, PASSWORT) VALUES (\"" . trim(addslashes(strtolower($email))) . "\", \"" . addslashes($pass) . "\")", $lk);
                    if ($db == true) { // DB Eintrag erfolgreich?
                        sendemail ($email, "Anmeldung bei $name", anmeldetext($email, $pass));
                        $str .= "<br><br><table width='400' align='center' class='rand'>";
                        $str .= "<tr><td>";
                        $str .= "<p align='center'><b>Anmeldung erfolgreich abgeschlossen!</b></p>";
                        $str .= "<p align='justify'>Sie k&ouml;nnen sich jetzt mit ihrer E-Mail und Passwort auf der Startseite einloggen. Zur Best&auml;tigung haben wir ihnen eine E-Mail mit den angegeben Daten geschickt.</p>";
                        $str .= "<p align='center'><a href='index.php'>Zur&uuml;ck zur Startseite</a></p>";
                        $str .= "</td></tr></table><br><br>";
                    } else { // DB Eintrag erfolgreich?
                        $fehler = "Es ist ein Datenbankproblem aufgetreten. Bitte probieren sie es sp&auml;ter noch einmal.";
                    } //DB Eintrag erfolgreich? - Else
                } else { // E-Mail schon eingetragen?
                    $fehler = "Die E-Mail Adresse ist bereits bei uns angemeldet!";
                } // E-Mail schon eingetragen? - Else
                mysql_close($lk);
            } else { // Passwort nicht verschrieben?
                $fehler = "Das Feld Passwort und das Feld Passwort Wiederholung stimmen nicht &uuml;berein!";
            } // Passwort nicht verschrieben? - Else
        } else { // Email in Ordnung?
            $fehler = "Bitte geben sie eine echte E-Mail Adresse an!";
        } // Email in Ordnung? - Else
    } // Variablen nicht leer?
    if ((isset($fehler)) OR ($email == "") OR ($pass == "")) {
        $str = "<br><br>";
        if (isset($fehler)) {
            $str .= "<table width='400' align='center' class='rand'>";
            $str .= "<tr><td>";
            $str .= "<p align='center'><b>Es ist ein Fehler aufgetreten!</b></p><p align='justify'>$fehler</p></td></tr>";
            $str .= "</table><br>";
        } 
        $str .= "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td>";
        $str .= "<p align='justify'>Bitte f&uuml;llen sie das folgende Formular <b>komplett</b> aus und klicken sie auf anmelden! Nach ihrer Anmeldung wird ihnen eine Best&auml;tigungs per E-Mail zugesandt.<br><br></p></td></tr>";
        $str .= "<tr><td><table width='75%' align='center'>";
        $str .= "<form method='post' action='anmelden.php'>";
        $str .= "<tr><td>E-Mail:</td>";
        $str .= "<td align='center'><input type='text' name='email' class='input' value='$email'></td></tr>";
        $str .= "<tr><td>Passwort:</td>";
        $str .= "<td align='center'><input type='password' name='pass' class='input'></td></tr>";
        $str .= "<tr><td>Passwort Wdh.:</td>";
        $str .= "<td align='center'><input type='password' name='pass2' class='input'></td></tr>";
        $str .= "<tr><td colspan='2' align='center'><br><input type='submit' name='Anmelden' class='forminput' value='    Anmelden    '></fotm></td></tr>";
        $str .= "</table></td></tr>";
        $str .= "</table>";
        $str .= "<br><br><br>";
    } 
    return $str;
} 

?>