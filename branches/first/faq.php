<?php
require_once("conf.php");
require_once("session.inc");
$pricewatch = session_id();

$lk = connect();
$kat_db = mysql_db_query($mysql_db, "SELECT * FROM pricewatch_faq_kat ORDER BY 'ID' ASC", $lk);
$fragen_db = mysql_db_query($mysql_db, "SELECT * FROM pricewatch_faq_fragen ORDER BY 'KAT' ASC, 'ID' ASC", $lk);
mysql_close($lk);

$text = $head;
$text .= $body;
$text .= menu("faq");
$text .= main();

$text .= $foot;

echo gzipoutput($text);
// ===========================================================================
function main()
{
    global $name, $kat_db, $fragen_db, $user;
    $str = "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td class='ueberschrift'>Frequently Asked Questions</td></tr>";
    $str .= "<tr><td>";
    $str .= "<p align='justify'>Hier finden sie die am meisten gestellten Fragen &uuml;ber $name. Sollten sie eine hier noch beantwortet Frage haben, dann finden sie am Ende dieser Webseite eine Frage zu stellen.</p></td></tr>";
    $str .= "</table>";
    $str .= "<br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td align='center'><a href='#fragen'>Frage stellen</a></td></tr>";
    $str .= "</table><br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $antworten = "<table width='400' align='center' class='rand'>";
    $kat_alt = -1;
    while ($frage = mysql_fetch_array($fragen_db, MYSQL_ASSOC)) {
        if ($kat_alt != $frage[KAT]) {
            if ($kat_alt != -1) {
                $str .= "</ul></td></tr>";
            } 
            $kat = mysql_fetch_array($kat_db, MYSQL_ASSOC);
            $kat_alt = $kat[ID];
            $str .= "<tr><td><b>" . htmlentities(stripslashes($kat[NAME])) . "</b></td></tr>";
            $str .= "<tr><td><ul>";
            $antworten .= "<tr><td class='ueberschrift'>" . htmlentities(stripslashes($kat[NAME])) . "</td></tr>";
        } 
        $str .= "<li><a href='#" . $frage[ID] . "'>" . htmlentities(stripslashes($frage[FRAGE])) . "</a></li>";
        $antworten .= "<tr height='10'><td></td></tr>";
        $antworten .= "<tr><td><b><a name='" . $frage[ID] . "'>" . htmlentities(stripslashes($frage[FRAGE])) . "</a></b></td></tr>";
        $antworten .= "<tr><td><p align='justify'>" . htmlentities(stripslashes($frage[ANTWORT])) . "</p></td></tr>";
    } // while
    $str .= "</table><br><br>";
    $antworten .= "</table>";
    $str .= $antworten;
    $str .= "<br>";
    $str .= "<table width='400' align='center' class='rand'>";
    $str .= "<tr><td class='ueberschrift'><a name='#fragen'>Frage stellen</a></td></tr>";
    $str .= "<tr><td>";
    $str .= "<table width='75%' border='0' align='center'>";
    $str .= "<form method='post' action='" . link_s("frage_stellen.php") . "'>";
    $str .= "<tr><td>Ihr E-Mail Adresse:</td>";
    $str .= "<td align='center'><input type='text' name='email' class='input' value='" . $user[EMAIL] . "' size='27'></td></tr>";
    $str .= "<tr><td>Ihr Frage:</td>";
    $str .= "<td><textarea name='frage' rows='7' cols='20'></textarea></td></tr>";
    $str .= "<tr><td colspan='2' align='center'><input type='submit' name='Einloggen' class='forminput' value='Frage abschicken'></fotm></tr>";
    $str .= "</table></td></tr>";
    $str .= "</table>";
    $str .= "<br><br><br>";
    return $str;
} 

?>