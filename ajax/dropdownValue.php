<?php
/* 
 * @version $Id$
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2011 by the EdenProject Development Team.
 
 ----------------------------------------------------------------------

 LICENSE

 This file is part of the nagiosql plugin for GLPI.

 Nagiosql plugin for GLPI is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License as published
 by the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ------------------------------------------------------------------------
 */

// ------------------------------------------------------------------------ 
// Original Author : E M Thornber (after Julien Dombre)
// Purpose of File :
// ------------------------------------------------------------------------  
 
// Direct access to file
if ( strpos($_SERVER['PHP_SELF'],"dropdownValue.php") ){
	define('GLPI_ROOT', '../../..');
	//$AJAX_INCLUDE=1;
	include (GLPI_ROOT."/inc/includes.php");
	header("Content-Type: text/html; charset=UTF-8");
	header_nocache();
}
if ( ! defined('GLPI_ROOT') ){
	die("Can not acces directly to this file");
}

checkCentralAccess();

// Make a select box with all glpi users
$type = $_POST['type_links'];

$where = " WHERE is_deleted = 0 ";

$itemtype = $_POST['itemtype'];

switch ($_POST['itemtype']){
	case 'Computer' :
		$datatable = 'glpi_computers';
		$where .= " AND computertypes_id = '$type' ";
		break;
	case 'NetworkEquipment' : 
		$datatable = 'glpi_networkequipments'; 
		$where .= " AND networkequipmenttypes_id = '$type' ";
		break;
	case 'Supplier' : 
		$datatable = 'glpi_suppliers'; 
		$where .= " AND suppliertypes_id = '$type' ";
		break;
	default : 
		echo 'Error - invalid type';
		return 0;
}

if (isset($_POST["entity_restrict"])&&$_POST["entity_restrict"]>=0){
	$where.=getEntitiesRestrictRequest("AND",$datatable,'',$_POST["entity_restrict"],false);
} else {
	$where.=getEntitiesRestrictRequest("AND",$datatable,'','',false);
}

if (isset($_POST['used'])) {
	$where .=" AND $datatable.id NOT IN (0";
	if (is_array($_POST['used'])) {
			$used=$_POST['used'];
		} else {
			$used=unserialize(stripslashes($_POST['used']));
		}
	foreach($used as $val)
		$where .= ",$val";
	$where .= ") ";
}

if ($_POST['searchText']!=$CFG_GLPI["ajax_wildcard"])
	$where.=" AND $datatable.name ".makeTextSearch($_POST['searchText']);

$NBMAX=$CFG_GLPI["dropdown_max"];
$LIMIT="LIMIT 0,$NBMAX";
if ( $_POST['searchText'] == $CFG_GLPI["ajax_wildcard"]) $LIMIT="";

$leftjoin='';
if( $_POST['myname'] == 'childID'){
	// Insure that the device does not already have a parent
	$leftjoin=" LEFT JOIN glpi_plugin_nagiosql_links AS pc "
		. "ON $datatable.id = pc.items_id "
		. "AND pc.itemtype='{$_POST['itemtype']}'";
	$where.=" AND ISNULL(parent_id) ";
}
$query = "SELECT $datatable.id, name, entities_id FROM $datatable $leftjoin ".
	"$where ORDER BY entities_id, name $LIMIT";

$result = $DB->query($query);
echo "<select name=\"".$_POST['myname']."\">";
echo "<option value=\"0\">-----</option>";

if ($DB->numrows($result)) {
	$prev=-1;
	while ($data=$DB->fetch_array($result)) {
		if ($data["entities_id"]!=$prev) {
			if ($prev>=0) {
				echo "</optgroup>";
			}
			$prev=$data["entities_id"];
			echo "<optgroup label=\"". Dropdown::getDropdownName("glpi_entities", $prev) ."\">";
		}
		$output = $data["name"];
		echo "<option value=\"".$data["id"]
			. "\" title=\"$output\">"
			. utf8_substr($output,0,$CFG_GLPI["cut"])."</option>";
	}
	if ($prev>=0) {
		echo "</optgroup>";
	}
}

echo "</select>";

?>