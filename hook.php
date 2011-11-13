<?php
/*
 * @version $Id$
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2011 by the EdenProject Development Team.

 ----------------------------------------------------------------------

 LICENSE

 This file is part of the nagiosql plugin for GLPI.

 Nagiosql plugin for GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
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
// Original Author : E M Thornber after Ryan Foster (relations plugin)
// Purpose of File : Code for hooks etc.
// ------------------------------------------------------------------------

function plugin_nagiosql_install() {
	include (GLPI_ROOT . "/plugins/nagiosql/install/install.php");
	pluginNagiosqlInstall(PLUGIN_NAGIOSQL_VERSION);
	return true;
}

// Uninstall process for plugin : need to return true if succeeded
function plugin_monitoring_uninstall() {
	include (GLPI_ROOT . "/plugins/nagiosql/install/install.php");
	pluginNagiosqlUninstall();
	return true;
}


// what to do when an item within a relationship is purged
function plugin_item_purge_anything($item){

	global $LANG;

	$type = get_class($item);
	if ( in_array($type, PluginRelationsRelation::getTypes(true)) ){

		$id = $item->getField('id');
		if ( isset($id) ){

			global $DB;

			$query = "DELETE FROM glpi_plugin_relations_relations
				WHERE itemtype = '$type'
				AND ( parent_id = '$id' OR items_id = '$id');";
			$result = $DB->query($query);
			if ( $result == false ){
				addMessageAfterRedirect($LANG['plugin_relations']['errors'][2],true);
			}
			elseif ( $DB->affected_rows() > 0 ){
				addMessageAfterRedirect($LANG['plugin_relations']['errors'][3],true);
			}
			return true;
		}
	}
	return false;
}

// ???????????
// Define rights for the plugin types
function plugin_relations_haveTypeRight($type,$right){
	switch ($type){
		case PLUGIN_RELATIONS_TYPE :
			// 1 - All rights for all users
			// return true;
			// 2 - Similarity right : same right of computer
			return plugin_relations_haveRight('relations',$right);
			break;
	}
}

// Useless hook
//function plugin_item_purge_relations($parm){

//	if (in_array($parm['type'],array(COMPUTER_TYPE,NETWORKING_TYPE))){
//		$plugin_relations=new plugin_relations;
//		$plugin_relations->cleanItems($parm['ID'],$parm['type']);
//		return true;
//	}else
//		return false;
//}

// Define the the plugin heading that wil show up in a form tab
function plugin_get_headings_relations($item,$withtemplate){

	global $LANG;

	$type = get_Class($item);

	if ( in_array($type,PluginRelationsRelation::getTypes(true)) ){
		// template case
		$id = $item->getField('id');
		if ( $withtemplate || $id < 0 || $id == '' )
		return array();
		// Non template case
		else
		return array(1 => $LANG['plugin_relations']['title'][1]);
	}
	else
	return false;
}
// Define headings actions added by the plugin
// PT 20100913
function plugin_headings_actions_relations($item){

	$type = get_Class($item);

	if ($type == 'Profile') return array(1 => 'plugin_headings_relations');

	if ( in_array($type,PluginRelationsRelation::getTypes(true)) )

	return array(1 => array('PluginRelationsRelation','showAssociated'));
	else
	return false;
}

// relations of an action heading
function plugin_headings_relations($item,$withtemplate=0){
	global $CFG_GLPI, $LANG;

	$type = get_Class($item);
	$ID = $item->getField('id');

	switch ($type){
		case 'Profile' :
			$prof = new PluginRelationsProfile();
			if ( $prof->GetfromDB($ID) || $prof->createUserAccess($item) ){
				$prof->showForm($ID,
				array('target' => $CFG_GLPI["root_doc"]
				."/plugins/relations/front/profile.form.php")
				);
			}
			break;
	}
}

////// SEARCH FUNCTIONS ///////

// Define search options
function plugin_relations_getAddSearchOptions($itemtype){

	global $LANG;

	$sopt = array();

	if ( plugin_relations_haveRight("relations","r") ){

		if ( in_array($itemtype, PluginRelationsRelation::getTypes(true)) ){

			$sopt[2250]['table'] = 'glpi_plugin_relations_relations';
			$sopt[2250]['field'] = '';
			$sopt[2250]['linkfield'] = 'parent';
			$sopt[2250]['name']= $LANG['plugin_relations']['title'][1]
			. " - " . $LANG['plugin_relations']['params'][1];
			$sopt[2250]['datatype']	= 'itemlink';
			$sopt[2250]['itemlink_type'] = $itemtype;

			$sopt[2251]['table'] = 'glpi_plugin_relations_relations';
			$sopt[2251]['field'] = '';
			$sopt[2251]['linkfield'] = 'children';
			$sopt[2251]['name'] = $LANG['plugin_relations']['title'][1]
			. " - " . $LANG['plugin_relations']['params'][2];
			$sopt[2251]['forcegroupby'] = true;
			$sopt[2251]['datatype'] = 'itemlink';
			$sopt[2251]['itemlink_type'] = $itemtype;
			//$sopt[2251]["splititems"] = true;
		}
	}
		
	return $sopt;
}


////// SPECIFIC MODIF MASSIVE FUNCTIONS ///////

function plugin_relations_MassiveActions($type){

	global $LANG;

	if ( in_array($type, PluginRelationsRelation::getTypes(true)) ){
		return array('plugin_relations_set_parent'=>$LANG['plugin_relations']['params'][6]);
	}
	return array();
}

function plugin_relations_MassiveActionsDisplay($options){

	global $LANG;

	if ( $options['action'] == 'plugin_relations_set_parent' ){
		Dropdown::show($options['itemtype'], $options);
		echo '<input type="submit" name="massiveaction" class="submit" value="'.$LANG['buttons'][2].'" />';
	}
	return "";
}

function plugin_relations_MassiveActionsProcess($data){

	global $LANG, $DB;

	$items = $data["item"];
	$itemtype = $data['itemtype'];

	switch ($itemtype){
		case 'Computer' :
			$parent_id = $data['computers_id'];
			$table = 'glpi_computers';
			break;
		case 'NetworkEquipment':
			$parent_id = $data['networkequipments_id'];
			$table = 'glpi_networkequipments';
			break;
		case 'Supplier' :
			$parent_id = $data['suppliers_id'];
			$table = 'glpi_suppliers';
			break;
	}
	// a parent cannot be its own child
	if ( in_array($parent_id, array_keys($items)) ){
		addMessageAfterRedirect($LANG['plugin_relations']['errors'][4],false, ERROR,true);
	}
	elseif ( $parent_id == -1 ){
		$sql = "DELETE FROM glpi_plugin_relations_relations
			WHERE `itemtype` = '$itemtype'
			AND `items_id` IN ('".implode("','",array_keys($items)) ."');";
		$result = $DB->query($sql);
		// Logging to be added
	}
	else {
		$parent = PluginRelationsRelation::getDeviceName($itemtype, $parent_id);

		foreach ( $items as $child_id => $junk ){

			$child = PluginRelationsRelation::getDeviceName($itemtype, $child_id);

			$sql = "SELECT `id` FROM `glpi_plugin_relations_relations`
				WHERE `items_id` = '$child_id'
				AND `itemtype` = '$itemtype';";

			$result = $DB->query($sql);

			if( $DB->numrows($result) > 0 ) {
				$sql = "UPDATE glpi_plugin_relations_relations
						SET `parent_id` = '$parent_id' 
						WHERE `items_id` = '$child_id'
						AND `itemtype`='$itemtype';";
				$result = $DB->query($sql);
			}
			else {
				$sql = "INSERT INTO glpi_plugin_relations_relations
						(`parent_id`,`items_id`,`itemtype`) 
						VALUES
						('$parent_id','$child_id','$itemtype');";
				$result = $DB->query($sql);
			}
			// Logging
			PluginRelationsRelation::logChange(
			$itemtype,
			$child_id,
			$parent_id,
			$child,
			$parent,
			RELATIONS_LINK);
		}
	} // end of foreach
}


function plugin_relations_addSelect($type,$ID,$num){

	$out = '';
	switch($ID) {
		case 2250 :
			//$out = "parent_table.name AS ITEM_$num, ";
			$out = "CONCAT(parent_table.name,'$$',parent_table.id) AS ITEM_$num, ";
			break;
			//case 2251: $out = "GROUP_CONCAT( DISTINCT children_table.name SEPARATOR '$$$$') AS ITEM_$num, "; break;
		case 2251 :
			$out = "GROUP_CONCAT(DISTINCT CONCAT("
			. "children_table.name, '$$' , children_table.id) "
			. "SEPARATOR '$$$$') AS ITEM_$num, ";
			break;
	}
	return $out;
}

// Define how to join the tables when doing a search
function plugin_relations_addLeftJoin($type,$ref_table,$new_table,$linkfield,&$already_link_tables)
{
	if ( $new_table == 'glpi_plugin_relations_relations')
	{
		if( $linkfield == 'parent' ){
			$out= "LEFT JOIN glpi_plugin_relations_relations
				ON ( $ref_table.id = glpi_plugin_relations_relations.items_id ) \n";
			$out.= "LEFT JOIN $ref_table AS parent_table
				ON ( glpi_plugin_relations_relations.parent_id = parent_table.id
				AND parent_table.is_deleted = 0) \n";
		}
		else {
			$out= "LEFT JOIN glpi_plugin_relations_relations glpi_plugin_relations2
				ON ( $ref_table.id = glpi_plugin_relations2.parent_id ) \n"; 
			$out.= "LEFT JOIN $ref_table AS children_table
				ON (glpi_plugin_relations2.items_id = children_table.id
				AND children_table.is_deleted = 0) \n";
		}
		return $out;
	}
}

function plugin_relations_addWhere($link,$nott,$type,$ID,$val) {
	$out='';
	switch($ID) {
		case 2250: $out="$link (parent_table.name LIKE '%$val%')"; break;
		case 2251: $out="$link (children_table.name LIKE '%$val%')"; break;
	}
	return $out;
}

// Return the string that will be displayed in device views
function plugin_relations_giveItem($type,$ID,$data,$num) {

	$searchopt = &Search::getOptions($type);
	$form = getItemTypeFormURL($searchopt[$ID]["itemlink_type"]);
	$separator = "<br/>";

	$split = explode("$$$$",$data['ITEM_'.$num]);
	$count = 0;
	$string = "";

	for ( $i = 0; $i < count($split); $i++ ){
		if ( strlen(trim($split[$i])) > 0 ){
			$item = explode("$$",$split[$i]);
			if ( isset($item[1]) && $item[1] > 0 ){
				if ( $count ){
					$string .= $separator;
				}
				$count++;
				$string .= "<a id='PluginRelationsRelation' href='";
				$string .= $form . "?id=" . $item[1] . "'>";
				$string .= $item[0] . "</a>";
			}
		}
	}
	return $string;
}


?>