<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

if (($frage == "") OR (!validateEmail($email))) {
    $fehler = 1;
} else {
    $erg = mail("jensman@jensmans-welt.de", $name . ": FAQ Frage", stripslashes($frage), "FROM: " . stripslashes($email));
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
    global $erg, $fehler;
    $str = "<br><table width='400' align='center' class='rand'>";
    if ($erg == true) {
        $str .= "<tr><td class='ueberschrift'>Frage gesendet</td></tr>";
        $str .= "<tr><td align='center'>Sie werden bald eine Antwort erhalten.</td></tr>";
    } else {
        if ($fehler == 1) {
            $str .= "<tr><td class='ueberschrift'>Formular Fehler</td></tr>";
            $str .= "<tr><td align='center'>Bitte f&uuml;llen sie das Formular komplett aus.</td></tr>";
        } else {
            $str .= "<tr><td class='ueberschrift'>Frage nicht gesendet</td></tr>";
            $str .= "<tr><td align='center'>Bitte probieren sie es sp&auml;ter noch einmal.</td></tr>";
        } 
    } 
    $str .= "<tr><td align='center'><br><a href='" . link_s("faq.php") . "'>Zur&uuml;ck zum FAQ</a></td></tr>";
    $str .= "</table>";
    $str .= "<br><br><br>";
    return $str;
} 

?>