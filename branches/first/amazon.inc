<?php

function search_str($link, $search_string) {
    if ($asin_pos = strpos($link, $search_string)) {

        $asin_pos = $asin_pos + strlen($search_string);
        $asin = substr($link, $asin_pos);
        if ($asin_pos = strpos($asin, "/")) {

            $asin_pos = strlen($asin) - $asin_pos;
            $asin_pos = $asin_pos * (-1);
            $asin = substr($asin, 0, $asin_pos);

            return $asin;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function asin_search($link) {
    $asin = trim($link);
    $result = search_str($asin, "/dp/");
    if (!$result) {
        $result = search_str($asin, "/gp/product/");
    }
    return $result;
}


/*
function asin_search($link)
{
    $asin = trim($link);
    //if ($asin_pos = strpos($asin, "ASIN")) {
    if ($asin_pos = strpos($asin, "product")) {
        //$asin_pos = $asin_pos + 5;
        $asin_pos = $asin_pos + 8;
        $asin = substr($asin, $asin_pos);
        if ($asin_pos = strpos($asin, "/")) {
            $asin_pos = strlen($asin) - $asin_pos;
            $asin_pos = $asin_pos * (-1);
            $asin = substr($asin, 0, $asin_pos);
            return $asin;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
*/

function amazon_link ($asin)
{
    $str = "http://www.amazon.de/exec/obidos/ASIN/";
    $str .= $asin;
    $str .= "/gschaftshuonl-21";
    return $str;
} 

?>
