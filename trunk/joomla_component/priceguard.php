<?php
/**
* @version $Id: priceguard.php stingrey Exp $
* @package priceguard
* @copyright (C) 2005 Jens Breitbart
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once ("priceguard.html.php");
require_once ("Services/AmazonECS4.php");

//$id = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
//$catid = intval( mosGetParam( $_REQUEST, 'catid', 0 ) );

// get parameters
$params = new stdClass();
if ( $Itemid ) {
	$menu = new mosMenu( $database );
	$menu->load( $Itemid );
	$params =& new mosParameters( $menu->params );
} else {
	die( 'Something is wrong here...' );
}

$task = $params->get( 'task' );

switch ($task) {
	case 'add_product':
	addProduct( );
	break;

	case 'show_products':
	showProducts( );
	break;

	case 'show_products_delete':
	showProductsDelete( );
	break;

	case 'create_category':
	createCategory( );
	break;

	default:
	showProducts( );
	break;
}

function addProduct ( ) {
	global $mainframe, $_POST;
	if (!isset($_POST[amazon_link])) {
		$mainframe->setPageTitle( preg_replace("/([\xC2\xC3])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)","Produkt hinzufügen 1/2" ));
		HTML_priceguard::addProduct1();
	}
	if (isset($_POST[amazon_link])) {
		$mainframe->setPageTitle( preg_replace("/([\xC2\xC3])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)","Produkt hinzufügen 1/2" ));
		$amazon = new Services_AmazonECS4("1R0VYFH9MMZM6SNJBD02");
		$amazon -> setLocale("DE");
		$options = array();
		$options['ResponseGroup'] = 'Small,Offers,Images';
		$result = $amazon->ItemLookup(asin_search($_POST[amazon_link]), $options);
		if (PEAR::isError($result)) {
			echo '<p>Fehler:<br/>';
			echo htmlspecialchars($result->message);
			echo '</p>';
		} elseif ($result["Request"]["IsValid"]!="True") {
			echo '<p>Fehler:<br/>';
			echo "Link nicht korrekt";
			echo '</p>';
		} else {
			HTML_priceguard::addProduct2( $result);
		}
	}
}


function showProducts ( ) {
	global $mainframe;
	$mainframe->setPageTitle( "Produkte anzeigen" );
}


function showProductsDelete ( ) {
	global $mainframe;
	$mainframe->setPageTitle( "Produkte löschen" );
}


function createCategory( ) {
	global $mainframe, $_POST, $my, $database;
	$success=false;
	if (isset($_POST[category_name])) {
		$query = "INSERT INTO priceguard_categories (mos_user_id, parent, name) VALUES (".$my->id.", ";
		if ($_POST["parent"]!=-1)
			$query.= $_POST["parent"]. ", \"";
		else
			$query.= "NULL, \"";
		$query.= $_POST["category_name"]. "\")";
		$database->setQuery ( $query );
		$success = $database->query();
	}

	$mainframe->setPageTitle( preg_replace("/([\xC2\xC3])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)","Kategorie erstellen" ));
	HTML_priceguard::createCategory($success);
}

function asin_search($link) {
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


?>
