<?php
require_once("conf.php");

if (($email == "") OR ($pass == "")) {
    HEADER("Location: index.php");
    exit;
} 

$lk = connect();
$db = mysql_db_query($mysql_db, "SELECT ID, EMAIL FROM pricewatch_user WHERE EMAIL=\"" . addslashes(strtolower($email)) . "\" AND PASSWORT=\"" . addslashes($pass) . "\" LIMIT 1", $lk);
mysql_close($lk);

if ($user = mysql_fetch_array($db, MYSQL_ASSOC)) { // User in Datenbank - Else
    $user[EMAIL] = stripslashes($user[EMAIL]);
    session_start();
    session_name("pricewatch");
    session_register("user");
    $pricewatch = session_id();
    if ($cookie == 1) {
        $user_cookie[EMAIL] = $email;
        $user_cookie[PASSWORT] = $pass;
        setcookie("pricewatch_cookie", array_2_cookie($user_cookie), time() + 2592000, "", "", 0);
    } 
    HEADER("Location: " . link_s("index.php"));
} else { // User in Datenbank
    $text = $head;
    $text .= $body;
    $text .= menu("");
    $text .= main();
    $text .= $foot;
    echo gzipoutput($text);
} //User in Datenbank - Else
// ===========================================================================
function main()
{
    $str = "<br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td>";
    $str .= "<p><b>Es ist ein Fehler aufgetreten.</b></p><p align='justify'>E-Mail und/oder Passwort stimmt nicht. Bitte &uuml;berpr&uuml;fen sie die Daten und probieren es erneut.</p><br>";
    $str .= "<p align='center'><a href='index.php'>Zur&uuml;ck zur Startseite</a></p></td></tr></table>";
    $str .= "<br><br><br>";
    return $str;
} 

?>