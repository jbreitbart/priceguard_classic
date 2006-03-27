<?php
/**
* @version $Id: priceguard.php stingrey Exp $
* @package priceguard
* @copyright (C) 2005 Jens Breitbart
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once ("priceguard.html.php");
require_once ("tools.php");
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
	global $mainframe, $_POST, $database, $my;;

	//STEP 3
	//store in database
	if (isset($_POST["asin"])) {

		//stripslashes
		{
			$_POST["asin"]=stripslashes($_POST["asin"]);
			$_POST["amazon_price"]=stripslashes($_POST["amazon_price"]);
			$_POST["amazon_new_price"]=stripslashes($_POST["amazon_new_price"]);
			$_POST["amazon_used_price"]=stripslashes($_POST["amazon_used_price"]);

			$_POST["amazon_remind"] = stripslashes($_POST["amazon_remind"]);
			$_POST["amazon_3rd_new_remind"] = stripslashes($_POST["amazon_3rd_new_remind"]);
			$_POST["amazon_3rd_used_remind"] = stripslashes($_POST["amazon_3rd_used_remind"]);
		}

		//trim
		{
			$_POST["runtime"] = trim($_POST["runtime"]);
			$_POST["category_name"] = trim($_POST["category_name"]);
			$_POST["name"] = trim($_POST["name"]);

			$_POST["amazon_remind"] = trim($_POST["amazon_remind"]);
			$_POST["amazon_3rd_new_remind"] = trim($_POST["amazon_3rd_new_remind"]);
			$_POST["amazon_3rd_used_remind"] = trim($_POST["amazon_3rd_used_remind"]);
		}

		//remove all ',' and '.' from the prices
		{
			$chars = array(",", ".");
			$_POST["amazon_remind"] = str_replace($chars, "", $_POST["amazon_remind"]);
			$_POST["amazon_3rd_new_remind"] = str_replace($chars, "", $_POST["amazon_3rd_new_remind"]);
			$_POST["amazon_3rd_used_remind"] = str_replace($chars, "", $_POST["amazon_3rd_used_remind"]);
		}

		//check if the user wants to be reminded by all price changes... set the number to maximum :-)
		{
			//if not check partner => NULL
			//if check parter && checker everytime => 2147483647
			//else change nothing
			if ($_POST["check_amazon"]) {
				if ($_POST["amazon_remind_everytime"] || !isDigital($_POST["amazon_remind"]) ) $_POST["amazon_remind"]=2147483647;
			} else
				$_POST["amazon_remind"]="NULL";

			if ($_POST["check_amazon_3rd_new"]) {
				if ($_POST["amazon_3rd_new_remind_everytime"] || !isDigital($_POST["amazon_3rd_new_remind"]) ) $_POST["amazon_3rd_new_remind"]=2147483647;
			} else
				$_POST["amazon_3rd_new_remind"]="NULL";

			if ($_POST["check_amazon_3rd_used"]) {
				if ($_POST["amazon_3rd_used_remind_everytime"] || !isDigital($_POST["amazon_3rd_used_remind"]) ) $_POST["amazon_3rd_used_remind"]=2147483647;
			} else
				$_POST["amazon_3rd_used_remind"]="NULL";
		}

		//check if strings do NOT contain numbers and set NULL if needed
		{
			if (!isDigital($_POST["amazon_remind"])) $_POST["amazon_remind"]="NULL";
			if (!isDigital($_POST["amazon_3rd_new_remind"]))  $_POST["amazon_3rd_new_remind"]="NULL";
			if (!isDigital($_POST["amazon_3rd_used_remind"]))  $_POST["amazon_3rd_used_remind"]="NULL";
			if (!isDigital($_POST["amazon_price"])) $_POST["amazon_price"]="NULL";
			if (!isDigital($_POST["amazon_new_price"])) $_POST["amazon_new_price"]="NULL";
			if (!isDigital($_POST["amazon_used_price"])) $_POST["amazon_used_price"]="NULL";
			if (!isDigital($_POST["amazon_remind"])) $_POST["amazon_remind"]="NULL";
			if (!isDigital($_POST["amazon_3rd_new_remind"])) $_POST["amazon_3rd_new_remind"]="NULL";
			if (!isDigital($_POST["amazon_3rd_used_remind"])) $_POST["amazon_3rd_used_remind"]="NULL";
			if (!isDigital($_POST["runtime"])) $_POST["runtime"]="NULL";
		}

		if ($_POST[amazon_guard_availabillity]==1) $_POST[amazon_guard_availabillity] = "true";
		else $_POST[amazon_guard_availabillity] = "false";

		//create new category?
		if ($_POST["category"]=="NULL") {
			$query = "INSERT INTO priceguard_categories (mos_user_id, parent, name) VALUES (".$my->id.", ";
			$query.= $_POST["category"]. ", \"";
			$query.= $_POST["category_name"]. "\")";
			$database->setQuery ( $query );
			$success = $database->query();
			echo $query . "<br>";
			$X = "(SELECT id FROM priceguard_categories WHERE mos_user_id=".$my->id." AND name='".$_POST["category_name"]."' AND ";
			if ($_POST["category"]=="NULL") $X.= "parent IS NULL LIMIT 1)";
			else $X.= "parent=".$_POST["category"]." LIMIT 1)";
			$_POST["category"]=$X;
		}

		//insert our product in amazon_product
		$query  = "INSERT INTO amazon_product (asin, amazon_price, 3rdparty_new_price, 3rdparty_used_price, amazon_availabillity, image_small) VALUES (
		'".$_POST["asin"]."',
		".$_POST["amazon_price"].",
		".$_POST["amazon_new_price"].",
		".$_POST["amazon_used_price"].",
		'".$_POST["amazon_availability"]."',
		'".$_POST["amazon_picture_url"]."'
		);";

		$database->setQuery ( $query );
		$database->query();
		echo $query . "<br>";

		//insert user information into priceguard_product
		$query  = "INSERT INTO priceguard_product (priceguard_categories_id, mos_user_id, amazon_product_asin, name,   guard_availabillity, deadline, remind_price_amazon, remind_price_amazon_3rdparty_new, remind_price_amazon_3rdparty_used) VALUES (
		".$_POST["category"].",
		".$my->id.",
		'".$_POST["asin"]."',
		'".$_POST["name"]."',
		".$_POST["amazon_guard_availabillity"].",
		".$_POST["runtime"].",
		".$_POST["amazon_remind"].",
		".$_POST["amazon_3rd_new_remind"].",
		".$_POST["amazon_3rd_used_remind"].");";

		$database->setQuery ( $query );
		$database->query();
		echo $query . "<br>";
	}

	//STEP 1
	//ask for link
	if (!isset($_POST[amazon_link])) {
		$mainframe->setPageTitle( preg_replace("/([\xC2\xC3])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)","Produkt hinzufügen 1/2" ));
		HTML_priceguard::addProduct1();
	}

	//STEP 2
	//ask user for product information
	if (isset($_POST[amazon_link])) {
		$mainframe->setPageTitle( preg_replace("/([\xC2\xC3])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)","Produkt hinzufügen 2/2" ));
		$amazon = new Services_AmazonECS4("1R0VYFH9MMZM6SNJBD02", "gschaftshuonl-21");
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

?>
