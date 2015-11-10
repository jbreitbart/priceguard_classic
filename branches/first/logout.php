<?php
require_once("conf.php");
require_once("session.inc");

if ($pricewatch == "") {
    HEADER("Location: " . link_s("index.php"));
} 

session_destroy();
if ($pricewatch_cookie != "") {
    $date = date("r", strtotime("-1 day"));
    Header("Set-Cookie: pricewatch_cookie=deleted; expires=$date");
} 
$pricewatch = "";
HEADER("Location: " . link_s("index.php"));
exit;

?>