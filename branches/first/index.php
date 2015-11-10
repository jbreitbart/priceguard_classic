<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

$text = $head;
$text .= $body;
$text .= menu("home");
$text .= main();

$text .= $foot;

echo gzipoutput($text);
// ===========================================================================
function main()
{
    global $user;
    $str = "<table width='400' align='center' class='rand' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>";
    $str .= "<tr><td><img src='bilder/logo.png'></td></tr></table><br><br>";
    if ($user != "") {
        $str .= "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td align='center'>";
        $str .= "Sie sind eingeloggt als:<br></td></tr>";
        $str .= "<tr><td><table width='75%' align='center'>";
        $str .= "<tr><td><b>ID</b>:</td>";
        $str .= "<td align='left'>" . $user[ID] . "</td></tr>";
        $str .= "<tr><td><b>E-Mail</b>:</td>";
        $str .= "<td align='left'>" . $user[EMAIL] . "</td></tr>";
        $str .= "<tr><td colspan='2' align='center'><form action='" . link_s("logout.php") . "'><input type='submit' name='Einloggen' class='forminput' value='    Ausloggen    '></fotm></td></tr>";
        $str .= "</table></td></tr>";
        $str .= "</table>";
    } else {
        $str .= "<table width='400' align='center' class='rand'>";
        $str .= "<tr><td align='center'>";
        $str .= "Bitte loggen sie sich ein:</td></tr>";
        $str .= "<tr><td><table width='75%' align='center'>";
        $str .= "<form method='post' action='login.php'>";
        $str .= "<tr><td>E-Mail:</td>";
        $str .= "<td align='center'><input type='text' name='email' class='input'></td></tr>";
        $str .= "<tr><td>Passwort:</td>";
        $str .= "<td align='center'><input type='password' name='pass' class='input'></td></tr>";
        $str .= "<tr><td colspan='2' align='left'><input type='checkbox' value='1' name='cookie'>Automatisch einloggen</td></tr>";
        $str .= "<tr><td colspan='2' align='center'><input type='submit' name='Einloggen' class='forminput' value='    Einloggen    '></form></td></tr>";
        $str .= "<tr><td align='center'><a href='anmelden.php'>Anmelden</a></td><td align='center'><a href='pass_vergessen.php'>Passwort vergessen?</a></td></tr>";
        $str .= "</table></td></tr>";
        $str .= "</table>";
    } 
    $str .= "<br><br><br>";
    return $str;
} 

?>