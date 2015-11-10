<?php
require_once("conf.php");
require_once("session.inc");
setlocale(LC_ALL, "german");
$pricewatch = session_id();

$lk = connect();
$news_db = mysql_db_query($mysql_db, "SELECT UEBERSCHRIFT, NACHRICHT, UNIX_TIMESTAMP(DATUM) AS DATUM FROM pricewatch_news ORDER BY DATUM DESC LIMIT 5", $lk);
mysql_close($lk);

$text = $head;
$text .= $body;
$text .= menu("news");
$text .= main();

$text .= $foot;

echo gzipoutput($text);
// ===========================================================================
function main()
{
    global $user, $news_db;
    $str = "<table width='400' align='center' class='rand' cellpadding='0' cellspacing='0'>";
    $str .= "<tr><td class='ueberschrift'>News</td></tr>";
//    $str .= "<tr><td><p align='justify'></p></td></tr>";
	$str .= "</table><br><br>";
    $str .= "<table width='400' align='center' class='rand'>";
	$i=0;
	$anz=mysql_num_rows($news_db);
	while($arr=mysql_fetch_array($news_db, MYSQL_ASSOC)){
		if ($i!=0) {
		    $str .= "<tr><td height='12'></td></tr>";
			$str .= "</tr>";
			$str .= "</table>";
			$str .= "</td></tr>";
		    $str .= "<tr><td height='10'></td></tr>";		
		}
		$i++;		
		$str .= "<tr><td>";
		$str .= "<table width='100%' border='0' align='center'";
		//if ($anz!=$i) {
		//    $str .= " class='news_rahmen2'";
		//}
		$str .= ">";
		$str .= "<tr><td><table width='100%' align='center' class='news_rahmen'>";
		$str .= "<tr>";
		$str .= "<td valign='bottom' class='news_uebershrift'><p align='justify'><b>".stripslashes($arr[UEBERSCHRIFT])."</b></p></td>";
		$str .= "<td align='right' width='1%'><nobr>".strftime("%d. %b %y",$arr[DATUM])."</nobr></td>";
		$str .= "</tr></table></td></tr>";
		$str .= "<tr>";
		$str .= "<td><p align='justify'>".nl2br(stripslashes($arr[NACHRICHT]))."</p></td>";
	} // while
	$str .= "</tr>";
	$str .= "</table>";
	$str .= "</td></tr>";	
    $str .= "</table>";
    $str .= "<br><br><br>";
    return $str;
} 

?>