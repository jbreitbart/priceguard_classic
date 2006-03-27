<?php
function asin_search($link)
{
    $asin = trim($link);
    if ($asin_pos = strpos($asin, "ASIN")) {
        $asin_pos = $asin_pos + 5;
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

function amazon_link ($asin)
{
    $str = "http://www.amazon.de/exec/obidos/ASIN/";
    $str .= $asin;
    $str .= "/gschaftshuonl-21";
    return $str;
}

function array_2_text($array)
{
    $z = serialize($array);
    $z = gzcompress($z);
    $z = base64_encode($z);
    $z = urlencode($z);
    return $z;
}

function text_2_array($cookie)
{
    $z = urldecode($cookie);
    $z = base64_decode($z);
    $z = gzuncompress($z);
    $z = unserialize($z);
    return $z;
}


function isDigital($var){
   return (!empty($var) && (is_string($var) || is_int($var)) && ctype_digit((string) $var));
}

?>