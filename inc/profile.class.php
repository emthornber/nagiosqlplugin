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
// Original Author : E M Thornber (after Philippe THOIREY)
// Purpose of File :
// ------------------------------------------------------------------------  
 
if (!defined('GLPI_ROOT')){
	die("Sorry. You can't access directly to this file");
	}

class PluginNagiosqlProfile extends CommonDBTM {

	// if profile deleted
	static function cleanProfiles(Profile $prof){
		$plugprof = new self();
		$plugprof->delete(array('id'=>$prof->getField("id")));
	}

	static function select(){
		$prof = new self();
		if ( $prof->getFromDB($_SESSION['glpiactiveprofile']['id']) ){
			$_SESSION["glpi_plugin_nagiosql_profile"] = $prof->fields;
		}
		else {
			 unset($_SESSION["glpi_plugin_nagiosql_profile"]);
		}
	}
	
	//profiles modification
	//function showForm($target,$ID){
	function showForm($ID, $options=array()){
		global $LANG;

		if ( ! haveRight("profile","r") ) return false;

		$target = $this->getFormURL();
		if ( isset($options['target']) ){
			$target = $options['target'];
		}

		$canedit = haveRight("profile","w");
		$prof = new Profile();
		if ($ID){
			$this->getFromDB($ID);
			$prof->getFromDB($ID);
		}
		echo "<form action='".$target."' method='post'>";
		echo "<table class='tab_cadre_fixe'>";

		echo "<tr><th colspan='2' align='center'><strong>".$LANG['plugin_nagiosql']['profile'][0]." ".$this->fields["name"]."</strong></th></tr>";
		
		echo "<tr class='tab_bg_2'>";
		echo "<td>".$LANG['plugin_nagiosql']['profile'][1].":</td><td>";

		// PT 20100920: to be adjusted
		if ( $prof->fields['interface'] != 'helpdesk' ){
			Profile::dropdownNoneReadWrite("nagiosql",$this->fields["nagiosql"],1,1,1);
		}
		else {
			echo $LANG['profiles'][12]; // No access;		
		}
		echo "</td>";
		echo "</tr>";

		if ($canedit){
			echo "<tr class='tab_bg_1'>";
			echo "<td align='center' colspan='2'>";
			echo "<input type='hidden' name='id' value=$ID>";
			echo "<input type='submit' name='update_user_profile' value=\"".$LANG['buttons'][7]."\" class='submit'>";
			echo "</td></tr>";
		}
		echo "</table></form>";

	}

	static function createAdminAccess($ID) {

		$myProf = new self();
		if ( ! $myProf->GetfromDB($ID) ){
			$Profile = new Profile();
			$Profile->GetfromDB($ID);
			$name = $Profile->fields["name"];

			$myProf->add(array('id' => $ID,
					'name' => $name,
					'nagiosql' => 'w'));
		}
	}
	function createUserAccess($Profile) {

		return $this->add(array('id'   => $Profile->getField('id'),
					'name' => $Profile->getField('name')));
	}
}

?>