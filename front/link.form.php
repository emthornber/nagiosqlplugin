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
 
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT.'/inc/includes.php');

if ( ! isset($_GET['id']) ) $_GET['id'] = '';

$PluginNagiosql = new PluginNagiosqlLink();

// Parent form
if ( isset($_POST['add']) && isset($_POST['type']) && $_POST['parentID'] > 0 ){

	if ( plugin_nagiosql_HaveRight('nagiosql','w') ){

		if ( PluginNagiosqlLink::isDescendant($_POST['parentID'],
							$_POST['id'],$_POST['type']) )
			addMessageAfterRedirect($LANG["plugin_nagiosql"]['errors'][0],true);
		else {
			PluginNagiosqlLink::addParent(
							$_POST['parentID'],
							$_POST['id'],
							$_POST['type']);
			// Logging here
			$child = PluginNagiosqlLink::getDeviceName($_POST['type'],
									$_POST['id']);
			$parent = PluginNagiosqlLink::getDeviceName($_POST['type'],
									$_POST['parentID']);
			PluginNagiosqlLink::logChange(
							$_POST['type'],
							$_POST['id'],
							$_POST['parentID'],
							$child,
							$parent,
							NAGIOSQL_LINK);
		}
	}
	glpi_header($_SERVER['HTTP_REFERER']);
}
// Child form
else if ( isset($_POST['additem']) && isset($_POST['type']) && $_POST['childID'] > 0 ){

	if ( plugin_nagiosql_HaveRight('relations','w') ){

		if ( PluginNagiosqlLink::isAncestor($_POST['childID'],
							$_POST['id'],$_POST['type']) )
			addMessageAfterRedirect($LANG["plugin_nagiosql"]['errors'][1],true);
		else {
			PluginNagiosqlLink::addParent(
							$_POST['id'],
							$_POST['childID'],
							$_POST['type']);
			// Logging here
			$child = PluginNagiosqlLink::getDeviceName($_POST['type'],
									$_POST['childID']);
			$parent = PluginNagiosqlLink::getDeviceName($_POST['type'],
									$_POST['id']);
			PluginNagiosqlLink::logChange(
							$_POST['type'],
							$_POST['childID'],
							$_POST['id'],
							$child,
							$parent,
							NAGIOSQL_LINK);
		}
	}
	glpi_header($_SERVER['HTTP_REFERER']);
}
// Deletion link
else if ( isset($_GET['deletelinks']) ){

	$PluginNagiosql->check($_GET['id'],'w');

	$DB = new DB;
	$query = "SELECT * FROM glpi_plugin_nagiosql_links WHERE id='"
		. $_GET['id'] . "'";
	$result = $DB->query($query);

	if ( $data = $DB->fetch_array($result) ){

		$PluginNagiosql->delete($_GET);

		// Logging
		$child = PluginNagiosqlLink::getDeviceName($data['itemtype'],
								$data['items_id']);
		$parent = PluginNagiosqlLink::getDeviceName($data['itemtype'],
								$data['parent_id']);
		PluginNagiosqlLink::logChange(
						$data['itemtype'],
						$data['items_id'],
						$data['parent_id'],
						$child,
						$parent,
						NAGIOSQL_UNLINK);

	}

	glpi_header($_SERVER['HTTP_REFERER']);
}

?>