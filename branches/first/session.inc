<?php

if (($pricewatch == "") AND ($pricewatch_cookie != "")) {
    $user_cookie = cookie_2_array($pricewatch_cookie);
    $lk = connect();
    $db = mysql_db_query($mysql_db, "SELECT ID, EMAIL FROM pricewatch_user WHERE EMAIL=\"" . addslashes(strtolower($user_cookie[EMAIL])) . "\" AND PASSWORT=\"" . addslashes($user_cookie[PASSWORT]) . "\" LIMIT 1", $lk);
    mysql_close($lk);
    if ($user = mysql_fetch_array($db, MYSQL_ASSOC)) { // User in Datenbank - Else
        $user[EMAIL] = stripslashes($user[EMAIL]);
        session_start();
        session_name("pricewatch");
        session_register("user");
        $pricewatch = session_id();
        setcookie("pricewatch_cookie", array_2_cookie($user_cookie), time() + 2592000, "", "", 0);
    } else { // User in Datenbank
        $date = date("r", strtotime("-1 day"));
        Header("Set-Cookie: pricewatch_cookie=deleted; expires=$date");
    } //User in Datenbank - Else
} 
if ($pricewatch != "") {
    session_start();
    session_name("pricewatch");
} 

?>