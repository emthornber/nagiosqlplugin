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
// Purpose of File : Used to initialize the plugin and define its actions.
// ------------------------------------------------------------------------

define ("PLUGIN_NAGIOSQL_VERSION","1.0.0");

// Init the hooks of the plugins -Needed
function plugin_init_nagiosql() {
	global $PLUGIN_HOOKS,$LANG,$CFG_GLPI;

	// Params : plugin name - string type - ID - Array of attributes
	Plugin::registerClass('PluginNagiosqlRelation');

	$PLUGIN_HOOKS['change_profile']['nagiosql'] = array('PluginNagiosqlProfile','select');

	// plugin enabled
	if ( class_exists('PluginNagiosqlRelation') ){
		$PLUGIN_HOOKS['item_purge']['nagiosql'] = array();
		foreach ( PluginNagiosqlRelation::getTypes(true) as $type ){
			$PLUGIN_HOOKS['item_purge']['nagiosql'][$type] =
						'plugin_item_purge_anything';
		}
	}
	
	if (isset($_SESSION["glpiID"])){
		// Display a menu entry ? No, just tabs and massive actions
		// depending on rights
		if ( plugin_nagiosql_haveRight('nagiosql','r') ){
			$PLUGIN_HOOKS['headings']['nagiosql']
					= 'plugin_get_headings_nagiosql';
			$PLUGIN_HOOKS['headings_action']['nagiosql']
					 = 'plugin_headings_actions_nagiosql';
		}

		if ( plugin_nagiosql_haveRight('nagiosql','w') ){
			$PLUGIN_HOOKS['use_massive_action']['nagiosql'] = 1;
		}
	}	
}


// Get the name and the version of the plugin - Needed
function plugin_version_nagiosql() {

	return array('name'           => 'Plugin Nagiosql',
				 'shortname'      => 'nagiosql',
                 'version'        => PLUGIN_NAGIOSQL_VERSION,
                 'author'         => '<a href="mailto:emthornber@theiet.org">E M Thornber</a>',
                 'homepage'       => 'https://github.com/emthornber/nagiosql',
                 'minGlpiVersion' => '0.80');
}


// Check prerequisites before install : may print errors or add to message after redirect
function plugin_nagiosql_check_prerequisites() {
	global $LANG;
	
	if (GLPI_VERSION >= 0.80) {
		return true;
	} else {
		// This plugin requires GLPI version 0.80 or higher
		echo $LANG['plugin_nagiosql']['setup'][1];
	}
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_nagiosql_check_config($verbose=false) {
	return true;
}

function plugin_nagiosql_haveRight($module,$right) {

	$matches=array( ""  => array("","r","w"), // ne doit pas arriver normalement
					"r" => array("r","w"),
					"w" => array("w"),
					"1" => array("1"),
					"0" => array("0","1"), // ne doit pas arriver non plus
	);
	if ( isset($_SESSION["glpi_plugin_nagiosql_profile"][$module] )
	&& in_array($_SESSION["glpi_plugin_nagiosql_profile"][$module],$matches[$right]) ) {
		return true;
	} else {
		return false;
	}
}

?>