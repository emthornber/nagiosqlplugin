<?php
/* 
 * @version $Id$
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2011 by the EdenProject Development Team.
 
 ----------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
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
// Original Author : E M Thornber
// Purpose of File :
// ------------------------------------------------------------------------  
 
// Init the hooks of the plugins -Needed
function plugin_init_nagiosql() {
   global $PLUGIN_HOOKS,$LANG,$CFG_GLPI;

   // Params : plugin name - string type - ID - Array of attributes
   // No specific information passed so not needed
   //Plugin::registerClass('PluginNagiosqlExample',
   //                      array('classname'              => 'PluginNagiosqlExample',
   //                        ));

   // Params : plugin name - string type - ID - Array of attributes
   Plugin::registerClass('PluginNagiosqlDropdown');

   Plugin::registerClass('PluginNagiosqlExample',
                         array('notificationtemplates_types' => true));

   // Display a menu entry ?
   if (isset($_SESSION["glpi_plugin_nagiosql_profile"])) { // Right set in change_profile hook
      $PLUGIN_HOOKS['menu_entry']['nagiosql'] = 'front/nagiosql.php';

      $PLUGIN_HOOKS['submenu_entry']['nagiosql']['options']['optionname']['title'] = "Search";
      $PLUGIN_HOOKS['submenu_entry']['nagiosql']['options']['optionname']['page'] = '/plugins/nagiosql/front/nagiosql.php';
      $PLUGIN_HOOKS['submenu_entry']['nagiosql']['options']['optionname']['links']['search'] = '/plugins/nagiosql/front/nagiosql.php';
      $PLUGIN_HOOKS['submenu_entry']['nagiosql']['options']['optionname']['links']['add'] = '/plugins/nagiosql/front/nagiosql.form.php';
      $PLUGIN_HOOKS['submenu_entry']['nagiosql']['options']['optionname']['links']['config'] = '/plugins/nagiosql/index.php';
      $PLUGIN_HOOKS['submenu_entry']['nagiosql']['options']['optionname']['links']["<img  src='".$CFG_GLPI["root_doc"]."/pics/menu_showall.png' title='".$LANG['plugin_nagiosql']["test"]."' alt='".$LANG['plugin_nagiosql']["test"]."'>"] = '/plugins/nagiosql/index.php';
      $PLUGIN_HOOKS['submenu_entry']['nagiosql']['options']['optionname']['links'][$LANG['plugin_nagiosql']["test"]] = '/plugins/nagiosql/index.php';

      $PLUGIN_HOOKS["helpdesk_menu_entry"]['nagiosql'] = true;
   }

   // Config page
   if (haveRight('config','w')) {
      $PLUGIN_HOOKS['config_page']['nagiosql'] = 'config.php';
   }

   // Init session
   //$PLUGIN_HOOKS['init_session']['nagiosql'] = 'plugin_init_session_nagiosql';
   // Change profile
   $PLUGIN_HOOKS['change_profile']['nagiosql'] = 'plugin_change_profile_nagiosql';
   // Change entity
   //$PLUGIN_HOOKS['change_entity']['nagiosql'] = 'plugin_change_entity_nagiosql';

   // Onglets management
   $PLUGIN_HOOKS['headings']['nagiosql']        = 'plugin_get_headings_nagiosql';
   $PLUGIN_HOOKS['headings_action']['nagiosql'] = 'plugin_headings_actions_nagiosql';

   // Item action event // See define.php for defined ITEM_TYPE
   $PLUGIN_HOOKS['pre_item_update']['nagiosql'] = array('Computer'=>'plugin_pre_item_update_nagiosql');
   $PLUGIN_HOOKS['item_update']['nagiosql']     = array('Computer'=>'plugin_item_update_nagiosql');

   // Example using a method in class
   $PLUGIN_HOOKS['pre_item_add']['nagiosql'] = array('Computer' => array('PluginNagiosqlExample',
                                                                        'pre_item_add_nagiosql'));
   $PLUGIN_HOOKS['item_add']['nagiosql']     = array('Computer' => array('PluginNagiosqlExample',
                                                                        'item_add_nagiosql'));

   $PLUGIN_HOOKS['pre_item_delete']['nagiosql'] = array('Computer'=>'plugin_pre_item_delete_nagiosql');
   $PLUGIN_HOOKS['item_delete']['nagiosql']     = array('Computer'=>'plugin_item_delete_nagiosql');

   // Example using the same function
   $PLUGIN_HOOKS['pre_item_purge']['nagiosql'] = array('Computer'=>'plugin_pre_item_purge_nagiosql',
                                                      'Phone'=>'plugin_pre_item_purge_nagiosql');
   $PLUGIN_HOOKS['item_purge']['nagiosql']     = array('Computer'=>'plugin_item_purge_nagiosql',
                                                      'Phone'=>'plugin_item_purge_nagiosql');

   // Example with 2 different functions
   $PLUGIN_HOOKS['pre_item_restore']['nagiosql'] = array('Computer'=>'plugin_pre_item_restore_nagiosql',
                                                         'Phone'=>'plugin_pre_item_restore_nagiosql2');
   $PLUGIN_HOOKS['item_restore']['nagiosql']     = array('Computer'=>'plugin_item_restore_nagiosql');

   // Add event to GLPI core itemtype, event will be raised by the plugin.
   // See plugin_example_uninstall for cleanup of notification
   $PLUGIN_HOOKS['item_get_events']['nagiosql'] = array('NotificationTargetTicket'=>'plugin_nagiosql_get_events');

   // Add datas to GLPI core itemtype for notifications template.
   $PLUGIN_HOOKS['item_get_datas']['nagiosql'] = array('NotificationTargetTicket'=>'plugin_nagiosql_get_datas');

   $PLUGIN_HOOKS['item_transfer']['nagiosql'] = 'plugin_item_transfer_nagiosql';

   //redirect appel http://localhost/glpi/index.php?redirect=plugin_example_2 (ID 2 du form)
   $PLUGIN_HOOKS['redirect_page']['nagiosql'] = 'nagiosql.form.php';

   //function to populate planning
   $PLUGIN_HOOKS['planning_populate']['nagiosql'] = 'plugin_planning_populate_nagiosql';

   //function to display planning items
   $PLUGIN_HOOKS['display_planning']['nagiosql'] = 'plugin_display_planning_nagiosql';

   // Massive Action definition
   $PLUGIN_HOOKS['use_massive_action']['nagiosql'] = 1;

   $PLUGIN_HOOKS['assign_to_ticket']['nagiosql'] = 1;

   // Add specific files to add to the header : javascript or css
   $PLUGIN_HOOKS['add_javascript']['nagiosql'] = 'nagiosql.js';
   $PLUGIN_HOOKS['add_css']['nagiosql']        = 'nagiosql.css';

   // Retrieve others datas from LDAP
   //$PLUGIN_HOOKS['retrieve_more_data_from_ldap']['example']="plugin_retrieve_more_data_from_ldap_example";

   // Reports
   $PLUGIN_HOOKS['reports']['nagiosql'] = array('report.php'       => 'New Report',
                                               'report.php?other' => 'New Report 2');

   // Stats
   $PLUGIN_HOOKS['stats']['nagiosql'] = array('stat.php'       => 'New stat',
                                             'stat.php?other' => 'New stats 2',);
}


// Get the name and the version of the plugin - Needed
function plugin_version_nagiosql() {

   return array('name'           => 'Plugin nagiosql',
                'version'        => '1.0.0',
                'author'         => 'E M Thornber',
                'homepage'       => 'https://forge.indepnet.net/projects/show/nagiosql',
                'minGlpiVersion' => '0.80');
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_nagiosql_check_prerequisites() {

   if (GLPI_VERSION >= 0.80) {
      return true;
   } else {
      echo "GLPI version not compatible need at least 0.80";
   }
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_nagiosql_check_config($verbose=false) {
   global $LANG;

   if (true) { // Your configuration check
      return true;
   }
   if ($verbose) {
      echo $LANG['plugins'][2];
   }
   return false;
}

?>