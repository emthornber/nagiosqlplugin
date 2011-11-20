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
// Original Author : E M Thornber (after Ryan Foster)
// Purpose of File :
// ------------------------------------------------------------------------  
 
if (!defined('GLPI_ROOT')){
	die("Sorry. You can't access directly to this file");
	}

// CLASSES links

class PluginNagiosqlLink extends CommonDBTM {

	public $dohistory = true;

	static function getTypeName(){
		global $LANG;

		return $LANG['plugin_nagiosql']['title'][1];
	}

	function canCreate() {
		return plugin_nagiosql_haveRight('nagiosql', 'w');
	}

	function canView() {
		return plugin_nagiosql_haveRight('nagiosql', 'r');
	}

	static function dropdown($options=array()){
		global $DB,$LANG,$CFG_GLPI;

		// Defautl values
		$p['entity'] = '';
		$p['used'] = array();

		if ( is_array($options) && count($options) ){
         		foreach ($options as $key => $val) {
            			$p[$key] = $val;
			}
		}

		$rand = mt_rand();

		switch($p['itemtype']){
			case 'Computer' :
                $query = 'SELECT `id`, `name`
					FROM glpi_computertypes ORDER BY `name`';
				break;
			case 'NetworkEquipment' :
				$query = 'SELECT `id`, `name`
					FROM glpi_networkequipmenttypes ORDER BY `name`';
				break;
			case 'Supplier' :
				$query = 'SELECT `id`, `name`
					FROM glpi_suppliertypes ORDER BY `name`';
				break;
			default :
				return $rand;
				break;
		}

		$result=$DB->query($query);
		$type_links = "type_links" . $p['name'];

		echo "<select name='_type' id='$type_links'>\n";
		echo "<option value='0'>------</option>\n";
		while ( $data=$DB->fetch_assoc($result) ){
			echo "<option value='".$data['id']."'>".$data['name']."</option>\n";
		}
		echo "</select>\n";

		$params = array('type_links'=>'__VALUE__',
				'entity_restrict' => $p['entity'],
				'itemtype' => $p['itemtype'],
				'rand' => $rand,
				'myname' => $p['name'],
				'used' => $p['used']);

		ajaxUpdateItemOnSelectEvent($type_links,"show_".$p['name'].$rand,
			$CFG_GLPI["root_doc"]
			."/plugins/nagiosql/ajax/dropdownLinks.php",$params);

		echo "<span id='show_".$p['name']."$rand'>";
		$_POST["entity_restrict"] = $p['entity'];
		$_POST["type_nagiosql"] = 0;
		$_POST["itemtype"] = $p['itemtype'];
		$_POST["myname"] = $p['name'];
		$_POST["rand"] = $rand;
		$_POST["used"] = $p['used'];
		include (GLPI_ROOT."/plugins/nagiosql/ajax/dropdownLinks.php");
		echo "</span>\n";

		return $rand;
	}

	/**
	* Types that can have links
	*
	* @param $all boolean, all type, or only allowed ones
	*
	* @return array of types
	*/
	static function getTypes($all=false){

		static $types = array('Computer', 'NetworkEquipment', 'Supplier', 'Profile');

		$plugin = new Plugin();

		if ($all) return $types;

		// Only allowed types
		foreach ( $types as $key => $type ) {
			if ( ! class_exists($type) ) {
				continue;
			}
			$item = new $type();
			if ( !$item->canView() ) {
				unset($types[$key]);
			}
		}
		return $types;
	}

	static function getDeviceName($itemtype, $id){

		global $DB;

		switch($itemtype){
			case 'Computer' :
				$datatable='glpi_computers';
				break;
			case 'NetworkEquipment' :
				$datatable='glpi_networkequipments';
				break;
			case 'Supplier' :
				$datatable='glpi_suppliers';
				break;
        
		}
		$name = "";
		$query = "SELECT `name` FROM `$datatable` WHERE `id` =" . $id;
		$result = mysql_query($query);
       
		if ( $result ){
			$row = mysql_fetch_row($result);
			$name = $row[0];
		}
		return $name;
	}

	static function addParent($parentID,$ID,$type){

		global $DB;

		if ( $parentID > 0 && $ID > 0 && isset($type) ){

			$query = "INSERT INTO glpi_plugin_nagiosql_links
				(parent_id,items_id,itemtype)
				VALUES ('$parentID','$ID','$type');";
               
			$result = $DB->query($query);

		}
	}
	static function isDescendant($testDevice, $currentDevice, $device_type){

		global $DB;

		$query = "SELECT items_id AS thisDevice FROM glpi_plugin_nagiosql_links
			WHERE itemtype = '$device_type' AND parent_id='$currentDevice'";
		$result = $DB->query($query);
		$found = false;

		while ( ( ! $found ) && ( $data = $DB->fetch_assoc($result) ) ){

			if ( $data['thisDevice'] == $testDevice )
				$found = true;
			else
				$found = PluginRelationsRelation::isDescendant($testDevice,
						$data['thisDevice'],$device_type);
		}
		return $found;
	}
	static function isAncestor($testDevice, $currentDevice, $device_type){

		global $DB;

		$query = "SELECT parent_id AS thisDevice FROM glpi_plugin_nagiosql_links
			WHERE itemtype = '$device_type' AND items_id = '$currentDevice';";
		$result = $DB->query($query);

		if ( $data = $DB->fetch_assoc($result) ){
			if ( $data['thisDevice'] == $testDevice )
				return true;
			else
				return PluginRelationsRelation::isAncestor($testDevice,
						$data['thisDevice'],$device_type);
		}
		else
			return false;
	}

	/**
	* Log event into the history
	* @param device_type the type of the item to inject
	* @param device_id the id of the inserted item
	* @param the action_type the type of action(add or update)
	*/
	static function logChange($device_type, $device_id, $parent_id, $child, $parent, $action_type){

		global $LANG;

		$parentchanges[0] = 0;
		$parentchanges[1] = "";
		$childchanges[0] = 0;
		$childchanges[1] = "";

		if ( $child ) $child = "(" . $child . ")";
		if ( $parent ) $parent = "(" . $parent . ")";

		if ( $action_type == RELATIONS_LINK ){

			$parentchanges[2] = $LANG["plugin_nagiosql"]['log'][0]
					. ' ' . $device_id
					. ' ' . $child;
			$childchanges[2] = $LANG["plugin_nagiosql"]['log'][1]
					. ' ' . $parent_id
					. ' ' . $parent;
        
		}
        
		else if ( $action_type == RELATIONS_UNLINK ){

			$parentchanges[2] = $LANG["plugin_nagiosql"]['log'][2]
					. ' ' . $device_id
					. ' ' . $child;
			$childchanges[2] = $LANG["plugin_nagiosql"]['log'][3]
					. ' ' . $parent_id
					. ' ' . $parent;
        
		}
		Log::history($parent_id, $device_type, $parentchanges, 0,
				HISTORY_LOG_SIMPLE_MESSAGE);
		Log::history($device_id, $device_type, $childchanges, 0,
				HISTORY_LOG_SIMPLE_MESSAGE);
	}

	/**
	* Show links associated to a device
	*
	* Called from the device form (nagiosql tab)
	*
	* @param $itemtype : type of the device
	* @param $ID of the device
	* @param $withtemplate : not used, always empty
	*
	**/
	static function showAssociated($item,$withtemplate=''){

		global $DB,$CFG_GLPI,$LANG;

		$display_entity = isMultiEntitiesMode();
		$numcols = 4;
		if ( $display_entity ) $numcols++;

		$ID = $item->getField('id');
		$itemtype = get_Class($item);
		$canread = $item->can($ID,'r');
		$canedit = $item->can($ID,'w');

		$entity = $item->getEntityID();

		$showgroup=true;

		switch($itemtype){

			case 'Computer' :
				$form='computer';
				$datatable='glpi_computers'; 
				$typetable='glpi_computertypes'; 
				$morefrom=', glpi_computertypes AS t';
				break;

			case 'NetworkEquipment' : 
				$form='networkequipment';
				$datatable='glpi_networkequipments'; 
				$typetable='glpi_networkequipmenttypes'; 
				$morefrom=', glpi_networkequipmenttypes AS t';
				break;
			case 'Supplier' : 
				$form='supplier';
				$datatable='glpi_suppliers'; 
				$typetable='glpi_suppliertypes'; 
				$morefrom=', glpi_suppliertypes AS t';
				$showgroup=false;
				break;
			default: 
				echo 'Error - invalid type';
				exit;
			}
			if( $showgroup ){
				$moreselect=", g.name AS grp";
				$morejoin=" LEFT JOIN glpi_groups AS g ON d.groups_id = g.id";
			}
			else {
				$numcols--;
				$moreselect="";
				$morejoin="";
			}

			// PT 20100914
		$query1 = "SELECT pc.*, d.name, e.id AS entID, e.name AS entity, t.name AS type"
			. $moreselect .
			" FROM glpi_plugin_nagiosql_links AS pc, $datatable AS d".
			" LEFT JOIN glpi_entities AS e ON d.entities_id = e.id".
			" LEFT JOIN $typetable AS t ON d." . $form . "types_id = t.id"
			. $morejoin .
			" WHERE pc.items_id = '$ID'".
    			" AND d.is_deleted = 0 ".
			" AND pc.itemtype = '$itemtype'".
			" AND pc.parent_id = d.id";

		$query3 = "SELECT pc.*, d.name, e.id AS entID, e.name AS entity, t.name AS type"
			.$moreselect.
			" FROM glpi_plugin_nagiosql_links AS pc, $datatable AS d".
			" LEFT JOIN glpi_entities AS e ON d.entities_id = e.id".
			" LEFT JOIN $typetable AS t ON d." . $form . "types_id = t.id"
			. $morejoin .
			" WHERE pc.parent_id = '$ID'" .
			" AND d.is_deleted = 0 ".
			" AND pc.itemtype = '$itemtype'".
			" AND pc.items_id = d.id".
			" ORDER BY name";

		$result3 = $DB->query($query3);

		$used = array($ID);

		while ( $data = $DB->fetch_array($result3) )
			$used[] = $data["items_id"];

		if($DB->numrows($result3))
			$DB->data_seek($result3,0);

		if ( $withtemplate != 2 ) 
			echo "<form method='post' action=\"".$CFG_GLPI["root_doc"]."/plugins/nagiosql/front/link.form.php\">";
		echo "<div align='center'><table class='tab_cadre_fixe'>";

		// Parent
		echo "<tr><th colspan='".$numcols."'>".$LANG["plugin_nagiosql"]["params"][1].":</th></tr>";
		echo "<tr><th>".$LANG["plugin_nagiosql"]['params'][3]."</th>";
		if ($display_entity)
			echo "<th>".$LANG["entity"][0]."</th>";
		echo "<th>".$LANG["plugin_nagiosql"]["params"][0]."</th>";

		if( $showgroup )
			echo "<th>".$LANG["common"][35]."</th>";

		if( plugin_nagiosql_haveRight("nagiosql","w") )
			echo "<th>&nbsp;</th>";
		echo "</tr>";

		echo '<tr class="tab_bg_1">';

		$result = $DB->query($query1);
		if ( $DB->numrows($result) > 0 ){
			$data=$DB->fetch_array($result);
			echo '<td align="center"><a href="'.$CFG_GLPI["root_doc"].'/front/'.$form.'.form.php?id='.$data['parent_id'].'">',$data['name'];
			if ( $_SESSION["glpiis_ids_visible"] ) echo " (".$data["parent_id"].")";
			echo '</a></td>';
			if ($display_entity){
				if ( $data['entID'] == 0 )
					echo "<td align='center'>".$LANG["entity"][2]."</td>";
				else
					echo "<td align='center'>".$data['entity']."</td>";
			}
			echo '<td align="center">'.$data['type'].'</td>';

			if($showgroup)
				echo '<td align="center">'.$data['grp'].'</td>';

			if ( plugin_nagiosql_haveRight("nagiosql","w") )
				if ( $withtemplate < 2 )
					echo "<td align='center' class='tab_bg_2'><a href='".$CFG_GLPI["root_doc"]."/plugins/nagiosql/front/link.form.php?deletelinks=deletelinks&amp;id=".$data['id']."'>".$LANG["plugin_nagiosql"]['params'][5]."</a></td>";

			$parent = $data["parent_id"];
			$used[] = $parent;

			// Siblings
			$query2 = "SELECT pc.*, d.name, e.id AS entID, e.name AS entity, t.name AS type"
				. $moreselect .
				" FROM glpi_plugin_nagiosql_links AS pc, $datatable AS d".
				" LEFT JOIN glpi_entities AS e ON d.entities_id = e.id".
				" LEFT JOIN $typetable AS t ON d." . $form . "types_id = t.id"
				. $morejoin.
				" WHERE pc.items_id = d.id".
      				" AND d.is_deleted = 0 ".
				" AND pc.items_id <> '$ID'".
				" AND pc.itemtype = '$itemtype'".
				" AND pc.parent_id ='$parent'".
				" ORDER BY name";

			$result2 = $DB->query($query2);
			if ( $DB->numrows($result2) > 0 ){
				echo "<tr><th colspan='".$numcols."'>".
					'<a href="'.$CFG_GLPI["root_doc"].'/front/'.$form.'.php'.
					'?contains[0]='.urlencode($data['name']).
					'&field[0]=2250&sort=1&is_deleted=0 ">'.
					$LANG["plugin_nagiosql"]["params"][4]."</a>:</th></tr>";

				while ( $data=$DB->fetch_array($result2) ){
					echo '<tr class="tab_bg_1"><td align="center"><a href="'.$CFG_GLPI["root_doc"].'/front/'.$form.'.form.php?id='.$data['items_id'].'">'.$data['name'];
					if ($_SESSION["glpiis_ids_visible"]) echo " (".$data["items_id"].")";
					echo '</a></td>';
					if ($display_entity){
						if ($data['entID']==0)
							echo "<td align='center'>".$LANG["entity"][2]."</td>";
						else
							echo "<td align='center'>".$data['entity']."</td>";
					}
					echo '<td align="center">'.$data['type'].'</td>';

					if($showgroup)
						echo '<td align="center">'.$data['grp'].'</td>';

					if ( plugin_nagiosql_haveRight("nagiosql","w") )
						echo "<td>&nbsp;</td>";
					echo '</tr>';
				}
			}
		}
		elseif ( plugin_nagiosql_haveRight("nagiosql","w") ){

			echo '<td align="center" colspan="'.($numcols-1).'">';
			echo '<input type="hidden" name="id" value="'.$ID.'">';
			echo '<input type="hidden" name="type" value="'.$itemtype.'">';

			PluginRelationsRelation::dropdown(array('name'   => "parentID",
								'itemtype' => $itemtype,
								'entity' => $data['entity'],
								'used'   => $used));

			echo '</td>';
			echo '<td align="center"><input class="submit" type="submit" value="'.$LANG["buttons"][8].'" name="add"/></td>';
		}

		// Children
		$query4="SELECT `name` FROM `$datatable` WHERE `id` = '$ID'";
		$result4 = $DB->query($query4);
		$thisdata = $DB->fetch_array($result4);

		echo "<tr><th colspan='".$numcols."'>".
			'<a href="'.$CFG_GLPI["root_doc"].'/front/'.$form.'.php'.
			'?contains[0]='.urlencode($thisdata['name']).
			'&field[0]=2250&sort=1&is_deleted=0 ">'.
			$LANG["plugin_nagiosql"]["params"][2]."</a>:</th></tr>";
		echo "<tr><th>".$LANG["plugin_nagiosql"]['params'][3]."</th>";
		if ($display_entity)
			echo "<th>".$LANG["entity"][0]."</th>";
		echo "<th>".$LANG["plugin_nagiosql"]["params"][0]."</th>";
		if($showgroup)
			echo "<th>".$LANG["common"][35]."</th>";
		if(plugin_nagiosql_haveRight("nagiosql","w"))
			echo "<th>&nbsp;</th>";
		echo "</tr>";

		if ( $DB->numrows($result3) >0 ){
			while ($data=$DB->fetch_array($result3))
			{
				echo '<tr class="tab_bg_1"><td align="center"><a href="'.$CFG_GLPI["root_doc"].'/front/'.$form.'.form.php?id='.$data['items_id'].'">'.$data['name'];
				if ($_SESSION["glpiis_ids_visible"]) echo " (".$data["items_id"].")";
				echo '</a></td>';
				if ($display_entity){
					if ($data['entID']==0)
						echo "<td align='center'>".$LANG["entity"][2]."</td>";
					else
						echo "<td align='center'>".$data['entity']."</td>";
				}
				echo '<td align="center">'.$data['type'].'</td>';
				if($showgroup)
					echo '<td align="center">'.$data['grp'].'</td>';
				if(plugin_nagiosql_haveRight("nagiosql","w"))
					if ($withtemplate<2)
						echo "<td align='center' class='tab_bg_2'><a href='".$CFG_GLPI["root_doc"]."/plugins/nagiosql/front/link.form.php?deletelinks=deletelinks&amp;id=".$data['id']."'>".$LANG["plugin_nagiosql"]['params'][5]."</a></td>";
				echo '</tr>';
			}
		}
		if ( plugin_nagiosql_haveRight("nagiosql","w") ){
			echo '<tr class="tab_bg_1">';
			echo '<td align="center" colspan="'.($numcols-1).'">';
			echo '<input type="hidden" name="id" value="'.$ID.'">';
			echo '<input type="hidden" name="type" value="'.$itemtype.'">';
			
			PluginRelationsRelation::dropdown(array('name'   => "childID",
								'itemtype' => $itemtype,
								'entity' => $data['entity'],
								'used'   => $used));

			echo '</td>';
			echo '<td align="center"><input class="submit" type="submit" value="'.$LANG["buttons"][8].'" name="additem"/></td>';
			echo '</tr>';
		}

	
		if ( ! empty($withtemplate) )
			echo "<input type='hidden' name='is_template' value='1'>";

		echo "</table></div>";
		echo "</form>";

	}
}

?>