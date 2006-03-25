<?php
/**
* @version $Id: priceguard.html.php stingrey Exp $
* @package priceguard
* @copyright (C) 2005 Jens Breitbart
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package priceguard
*/
class HTML_priceguard {

	function addProduct1 () {
	?>
	<form action="http://www.priceguard.de/component/option,com_priceguard/Itemid,39/" method="post" name="linkForm" id="linkForm">
	<div class="componentheading">Produkt hinzuf&uuml;gen 1/2</div>
	<table class="contentpane" width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
		<tr>
		<td valign="top" class="contentdescription">
			Bitte f&uuml;gen sie den Amazon.de Link von dem gew&uuml;nschten Produkt in das untere Feld ein und klicken anschlie&szlig;end auf weiter.<br/><br/>
			<b>Wichtig</b>: Der Link muss zum &Uuml;berblick des Artikels f&uuml;hren (siehe Bild rechts).
		</td>
		<td width="410">
			<div align="right">
			<?php
			echo mosAdminMenus::ImageCheck( 'produkt_neu.png', '/images/stories/shop/amazon/');
			?>
			</div>
		</td>
		</tr>
		<tr>
		<td colspan="2">
			<br/>
			<div align="center">
				Link: 
				<input class="inputbox" type="text" name="amazon_link" size="75" /> <br/><br/>
				<input class="button" type='submit' name='Weiter' value='   Weiter   '>
			</div>
			</form>
		</td>
		</tr>
	</table>
	<?php
	}

	function addProduct2 ( $product ) {
//		echo '<p>Result:';
//		var_dump($product);
//		echo '</p>';
		global $database, $my;
		$query = "SELECT * FROM priceguard_categories WHERE mos_user_id=".$my->id;
		$database->setQuery ( $query );
		$database->query();
		$categories = $database->loadObjectList( "id" );
	?>
	<script LANGUAGE="JavaScript">
	<!--
	function de_activate(x) {
		x.disabled= !x.disabled
		if (x.disabled) x.value="immer"
		else x.value=""
	}
	
	function de_activate_complete(x,y,z) {
		if (!z.checked) {
			x.value="niemals"
			y.checked= false;
			y.disabled= true
			x.disabled= true
		} else {
			x.value="immer"
			y.checked= true;
			y.disabled= false
			x.disabled= true
		}
	}

	function de_activate_runtime(x) {
		x.disabled= !x.disabled
		if (x.disabled) x.value="unbegrenzt"
		else x.value=""
	}

	function cat_change() {
		if (document.priceForm.category.selectedIndex==0)
			document.priceForm.category_name.disabled=false;
		else
			document.priceForm.category_name.disabled=true;
	}
	//-->
	</script>
	<form action="http://www.priceguard.de/component/option,com_priceguard/Itemid,39/" method="post" name="priceForm" id="priceForm">
	<div class="componentheading">Produkt hinzuf&uuml;gen 2/2</div>
	<table class="contentpane" width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
		<tr>
		<td valign="top" class="contentdescription">
			Bitte f&uuml;gen sie den Amazon.de Link von dem gew&uuml;nschten Produkt in das untere Feld ein und klicken anschlie&szlig;end auf weiter.<br/><br/>
			<b>Wichtig</b>: Der Link muss zum &Uuml;berblick des Artikels f&uuml;hren (siehe Bild rechts).
		</td>
		<?php
		echo "<td height=\"" . $product["Item"]["MediumImage"]["Height"]["_content"].  "\" width=\"" . $product["Item"]["MediumImage"]["Width"]["_content"] ."\">";
		?>
			<div align="right">
			<img src=
			<?php
				echo "\"". $product["Item"]["MediumImage"]["URL"] . "\" height=\"" . $product["Item"]["MediumImage"]["Height"]["_content"].  "\" width=\"" . $product["Item"]["MediumImage"]["Width"]["_content"] ."\"";
			?>
			>
			</div>
		</td>
		</tr>
		<tr>
		<td colspan="2">
			<br/>
			<div align="center">
				<table class="contentpane" width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
				<tr><td colspan="3" align="center">
				<div class="componentheading">Produkt Daten - Allgemein</div>
				</td></tr>
				<tr>
				<td>
				Name:
				</td>
				<td colspan="2">
				<input class="inputbox" type="text" name="name" size="65" value="<?php echo preg_replace("/([\xC2\xC3])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)", $product["Item"]["ItemAttributes"]["Title"]); ?>"/>
				</td>
				</tr>
				<tr>
				<td>
				Kategorie:
				</td>
				<td colspan="2">
				<select class="inputbox" name="category" onChange="cat_change();" style="width: 480px">
				<option value="-1" />Neue Kategorie
				<?php
					if (isset($categories)) HTML_priceguard::parent_category_form($categories);
				?>
				</select>
				</td>
				</tr>
				<tr>
				<td>
				Kategorie-Name:
				</td>
				<td colspan="2">
				<input class="inputbox" type="text" name="category_name" size="65" value="<?php echo preg_replace("/([\xC2\xC3])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)", $product["Item"]["ItemAttributes"]["ProductGroup"]); ?>"/>
				</td>
				</tr>
				<tr>
				<td>
				Laufzeit:
				</td>
				<td>
				<input class="inputbox" type="text" name="runtime" size="25" value="unbegrenzt" disabled/>
				</td>
				<td>
				<input class="inputbox" type="checkbox" name="runtime_without_an_end" onclick="javascript:de_activate(document.priceForm.runtime)" checked/> unbegrenzt
				</td>
				</tr>

				<tr><td colspan="3" align="center">&nbsp;</td></tr>
				<tr><td colspan="3" align="center">
				<div class="componentheading">Anbieter - Amazon.de</div>
				</td></tr>
				<tr>
				<td>
				Preis:
				</td>
				<td>
				<?php echo $product["Item"]["Offers"]["Offer"]["OfferListing"]["Price"]["FormattedPrice"]; ?>
				</td>
				<td>
				<input class="inputbox" type="checkbox" name="check_amazon" onclick="javascript:de_activate_complete(document.priceForm.amazon_remind, document.priceForm.amazon_remind_everytime, document.priceForm.check_amazon)" checked/> &Uuml;berwachen
				</td>
				</tr>
				<tr>
				<td>
				Erinnerungspreis:
				</td>
				<td>
				<input class="inputbox" type="text" name="amazon_remind" size="25" value="immer" disabled/>
				</td>
				<td>
				<input class="inputbox" type="checkbox" name="amazon_remind_everytime" onclick="javascript:de_activate(document.priceForm.amazon_remind)" checked/> Bei jeder Preis&auml;nderung benachrichtigen
				</td>
				</tr>
				<tr>
				<td>
				Verf&uuml;gbarkeit:
				</td>
				<td colspan="2">
				<input class="inputbox" type="checkbox" name="amazon_guard_availabillity"/> Benachrichtigen wenn die Verf&uuml;gbarkeit sich &auml;ndert
				</td>
				</tr>

				<tr><td colspan="3" align="center">&nbsp;</td></tr>
				<tr><td colspan="3" align="center">
				<div class="componentheading">Anbieter - Amazon.de Alle Angebote (neu)</div>
				</td></tr>
				<tr>
				<td>
				Preis:
				</td>
				<td>
				<?php echo $product["Item"]["OfferSummary"]["LowestNewPrice"]["FormattedPrice"]; ?>
				</td>
				<td>
				<input class="inputbox" type="checkbox" name="check_amazon_3rd_new" onclick="javascript:de_activate_complete(document.priceForm.amazon_3rd_new_remind, document.priceForm.amazon_3rd_new_remind_everytime, document.priceForm.check_amazon_3rd_new)"/> &Uuml;berwachen
				</td>
				</tr>
				<tr>
				<td>
				Erinnerungspreis:
				</td>
				<td>
				<input class="inputbox" type="text" name="amazon_3rd_new_remind" size="25" value="niemals" disabled/>
				</td>
				<td>
				<input class="inputbox" type="checkbox" name="amazon_3rd_new_remind_everytime" onclick="javascript:de_activate(document.priceForm.amazon_3rd_new_remind)" disabled/> Bei jeder Preis&auml;nderung benachrichtigen
				</td>
				</tr>

				<tr><td colspan="3" align="center">&nbsp;</td></tr>
				<tr><td colspan="3" align="center">
				<div class="componentheading">Anbieter - Amazon.de Alle Angebote (gebraucht)</div>
				</td></tr>
				<tr>
				<td>
				Preis:
				</td>
				<td>
				<?php echo $product["Item"]["OfferSummary"]["LowestUsedPrice"]["FormattedPrice"]; ?>
				</td>
				<td>
				<input class="inputbox" type="checkbox" name="check_amazon_3rd_used" onclick="javascript:de_activate_complete(document.priceForm.amazon_3rd_used_remind, document.priceForm.amazon_3rd_used_remind_everytime, document.priceForm.check_amazon_3rd_used)"/> &Uuml;berwachen
				</td>
				</tr>
				<tr>
				<td>
				Erinnerungspreis:
				</td>
				<td>
				<input class="inputbox" type="text" name="amazon_3rd_used_remind" size="25" value="niemals" disabled/>
				</td>
				<td>
				<input class="inputbox" type="checkbox" name="amazon_3rd_used_remind_everytime" onclick="javascript:de_activate(document.priceForm.amazon_3rd_used_remind)" disabled/> Bei jeder Preis&auml;nderung benachrichtigen
				</td>
				</tr>
				<tr><td colspan="3" align="center">&nbsp;</td></tr>
				</table>
				<input class="button" type='submit' name='Weiter' value='   Weiter   '>
			</div>
			</form>
		</td>
		</tr>
	</table>
	<?php
	}
	
	function createCategory ( $success ) {
		global $database, $my;
		$query = "SELECT * FROM priceguard_categories WHERE mos_user_id=".$my->id;
		$database->setQuery ( $query );
		$database->query();
		$categories = $database->loadObjectList( "id" );
	?>
	<form action="http://www.priceguard.de/component/option,com_priceguard/Itemid,41/" method="post" name="categoryForm" id="categoryForm">
	<div class="componentheading">Kategorie erstellen</div>
	<?php
		if ($success) {
		?>
		<div align='center'><b>Kategorie erfolgreich erstellt!</b></div><br/><br/>
		<?php
		}
	?>
	<table class="contentpane" width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
		<tr>
		<td valign="top" class="contentdescription">
			Bitte f&uuml;gen sie den Amazon.de Link von dem gew&uuml;nschten Produkt in das untere Feld ein und klicken anschlie&szlig;end auf weiter.<br/><br/>
			<b>Wichtig</b>: Der Link muss zum &Uuml;berblick des Artikels f&uuml;hren (siehe Bild rechts).
		</td>
		<td width="410">
			<div align="right">
			<?php
			echo mosAdminMenus::ImageCheck( 'produkt_neu.png', '/images/stories/shop/amazon/');
			?>
			</div>
		</td>
		</tr>
		<tr>
		<td colspan="2">
			<br/>
			<div align="center">
				Parent: 
				<select name="parent">
				<option value="-1" />Oberste Ebene
				<?php
					if (isset($categories)) HTML_priceguard::parent_category_form($categories);
				?>
				</select><br /><br />
				Name: 
				<input class="inputbox" type="text" name="category_name" size="75" /> <br/><br/>
				<input class="button" type='submit' name='Weiter' value='   Weiter   '>
			</div>
			</form>
		</td>
		</tr>
	</table>
	<?php
	}

	function parent_category_form($categories) {
		foreach ($categories as $category) {
			if (!isset($category->parent)) {
				echo "<option VALUE=\"".$category->id."\" />-".$category->name;
				HTML_priceguard::parent_category_form_rec ($categories, $category->id, 2);
			}
		}
	}

	function parent_category_form_rec($categories, $key, $level) {
		foreach ($categories as $category) {
			if ($category->parent==$key) {
				echo "<option VALUE=\"".$category->id."\" />";
				for ($i=0; $i<$level; ++$i) {
					echo "-";
				}
				echo " ".$category->name;
				HTML_priceguard::parent_category_form_rec ($categories, $category->id, $level+1);
			}
		}
	}
}
?>
