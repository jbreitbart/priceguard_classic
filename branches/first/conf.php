<?php
ini_set("include_path", ".:/kunden/116846_36208/pear/PEAR:/usr/local/lib/php");
include ("amazon.inc");

$mysql_host = "mysql4.priceguard.de";
$mysql_user="db116846_14";
$mysql_pass="xxxx";
$mysql_db="db116846_14";
$url = "http://www.priceguard.de";
$name = "Amazon.de Pricewatch";

$head = "<html>
<head>
<title>$name</title>
<link href='style.css' REL=stylesheet type='text/css' title='style'>
<script language='javascript'>
<!--
function menu_over(x) {
	x.style.backgroundColor='#E0DFE3';
	x.style.borderStyle='ridge';
}
function menu_out(x,r) {
	x.style.backgroundColor='#FAFAFA';
	x.style.borderStyle='dashed';
	if (r!=1) x.style.borderRight='';
}
function menu_click(x) {
	window.location.href=x;
}
// -->
</script>
</head>";

$body = "<body bgcolor='white' background='bilder/euro.gif'><a name='top'></a>";

$foot = "<table width='400' class='rand' align='center'><tr><td align='center' valign='middle'><a href=\"http://www.jensmans-welt.de/impressum/\">Impressum</a></td></tr></table>
<!-- Start of StatCounter Code -->
<script type='text/javascript'>
sc_project=3555720; 
sc_invisible=1; 
sc_partition=39; 
sc_security='481448d8'; 
</script>

<script type='text/javascript' src='http://www.statcounter.com/counter/counter_xhtml.js'></script><noscript><div class='statcounter'><a href='http://www.statcounter.com/' target='_blank'><img class='statcounter' src='http://c40.statcounter.com/3555720/0/481448d8/1/' alt='free page hit counter'></a></div></noscript>
<!-- End of StatCounter Code -->
<p align='center'><a href='#top'><img border='0' src='bilder/euroanim.gif' alt='Nach oben'></a></p></body></html>";
// =========================================================================================================================
function menu($wo)
{
    global $pricewatch;
    $str = "<table width='95%' align='center' cellpadding='0' cellspacing='0'>";
    $str .= "<tr height='40'>";
    $str .= "<td class='menu' width='20%' onMouseOver=menu_over(this) onMouseOut=menu_out(this,0) onclick=menu_click('" . link_s("index.php") . "')><a href='" . link_s("index.php") . "'>Home</a></td>";
    $str .= "<td class='menu' width='20%' onMouseOver=menu_over(this) onMouseOut=menu_out(this,0) onclick=menu_click('" . link_s("news.php") . "')><a href='" . link_s("news.php") . "'>News</a></td>";
    if ($pricewatch != "") {
        $str .= "<td class='menu' width='20%' onMouseOver=menu_over(this) onMouseOut=menu_out(this,0) onclick=menu_click('" . link_s("produkte.php") . "')><a href='" . link_s("produkte.php") . "'>&Uuml;berwachte Artikel</a></td>";
    } else {
        $str .= "<td class='menu_deak' width='20%'>&Uuml;berwachte Amazon.de Produkte</td>";
    } 
    $str .= "<td class='menu' width='20%' onMouseOver=menu_over(this) onMouseOut=menu_out(this,0) onclick=menu_click('" . link_s("faq.php") . "')><a href='" . link_s("faq.php") . "'>FAQ</a></td>";
    if ($pricewatch != "") {
        $str .= "<td class='menu_voll' width='20%' onMouseOver=menu_over(this) onMouseOut=menu_out(this,1) onclick=menu_click('" . link_s("account.php") . "')><a href='" . link_s("account.php") . "'>Account Einstellungen</a></td>";
    } else {
        $str .= "<td class='menu_voll_deak' width='20%'>Account Einstellungen</td>";
    } 
    $str .= "</tr></table>";
    $str .= "<br>";
    return $str;
} 

function link_s($ziel)
{
    global $pricewatch;
    if ($pricewatch != "") {
        $str = $ziel . "?pricewatch=$pricewatch";
    } else {
        $str = $ziel;
    } 
    return $str;
} 

function anmeldetext ($email, $pass)
{
    global $name, $url;
    $anmeldetext = "Vielen dank fr ihre Anmeldung bei $name. Sie haben sich mit folgenden Daten angemeldet.

E-Mail:   $email
Passwort: $pass

$url";
    return $anmeldetext;
} 

function connect()
{
    global $mysql_host, $mysql_user, $mysql_pass;
    if (! $linkid = mysql_connect("$mysql_host", "$mysql_user", "$mysql_pass")) {
        // echo "Die Verbindung zu ", $Mysql_host, " konnte nicht hergestellt werden<br>";
        exit;
    } 
    return $linkid;
} 

function gzipoutput($text)
{
    global $HTTP_ACCEPT_ENCODING;

    $returntext = $text;

    if (function_exists("crc32") and function_exists("gzcompress")) {
        if (strpos(" " . $HTTP_ACCEPT_ENCODING, "x-gzip")) {
            $encoding = "x-gzip";
        } 
        if (strpos(" " . $HTTP_ACCEPT_ENCODING, "gzip")) {
            $encoding = "gzip";
        } 

        if ($encoding) {
            header("Content-Encoding: $encoding");

            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
            header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache"); // HTTP/1.0
            $size = strlen($text);
            $crc = crc32($text);

            $returntext = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $returntext .= substr(gzcompress($text, 1), 0, -4);
            $returntext .= pack("V", $crc);
            $returntext .= pack("V", $size);
        } 
    } 
    return $returntext;
} 

function validateEmail($email)
{
    if (eregi("^([a-z]|[0-9]|\.|-|_)+@([a-z]|[0-9]|\.|-|_)+\.([a-z]|[0-9]){2,3}$", $email, $arr_vars) && !eregi("(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)", $email, $arr_vars)) return true;
    else return false;
} 

function array_2_cookie($array)
{
    $z = serialize($array);
    //$z = gzcompress($z);
    $z = base64_encode($z);
    $z = urlencode($z);
    return $z;
} 

function cookie_2_array($cookie)
{
    $z = urldecode($cookie);
    $z = base64_decode($z);
    //$z = gzuncompress($z);
    $z = unserialize($z);
    return $z;
} 

function sendemail ($ziel, $subject, $text)
{
    $erg = mail($ziel, $subject, $text, "FROM: no-reply@priceguard.de");
    return $erg;
} 

function tstamp_mysql_2_unix ($mysql)
{
	$datefromdb = $mysql;
	$year = substr($datefromdb,0,4);
	$mon  = substr($datefromdb,4,2);
	$day  = substr($datefromdb,6,2);
	$hour = substr($datefromdb,8,2);
	$min  = substr($datefromdb,10,2);
	$sec  = substr($datefromdb,12,2);
	
	return mktime($hour,$min,$sec,$mon,$day,$year);

    //return mktime($mysql[8] . $mysql[9], $mysql[10] . $mysql[11], $mysql[12] . $mysql[13], $mysql[4] . $mysql[5], $mysql[6] . $mysql[7], $mysql[0] . $mysql[1] . $mysql[2] . $mysql[3]);
} 

?>
