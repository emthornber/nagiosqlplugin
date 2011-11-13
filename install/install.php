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
// Original Author : E M Thornber
// Purpose of File :
// ------------------------------------------------------------------------  
 
function pluginNagiosqlInstall($version) {
   global $DB,$LANG,$CFG_GLPI;

   // ** Insert in DB
   $DB_file = GLPI_ROOT."/plugins/nagiosql/install/mysql/plugin_nagiosql-"
              .$version."-empty.sql";
   $DBf_handle = fopen($DB_file, "rt");
   $sql_query = fread($DBf_handle, filesize($DB_file));
   fclose($DBf_handle);
   foreach ( explode(";\n", "$sql_query") as $sql_line ) {
      if (get_magic_quotes_runtime()) $sql_line=stripslashes_deep($sql_line);
      if (!empty($sql_line)) $DB->query($sql_line);
   }

   // Required as autoload doesn't work for inactive plugin
   include_once(GLPI_ROOT."/plugins/nagiosql/inc/profile.class.php");
   
   PluginNagiosqlProfile::createAdminAccess($_SESSION['glpiactiveprofile']['id']);
}

function pluginNagiosqlUninstall() {
   global $DB;

   $query = "SHOW TABLES;";
   $result=$DB->query($query);
   while ($data=$DB->fetch_array($result)) {
      if (strstr($data[0],"glpi_plugin_nagiosql_")) {
         $query_delete = "DROP TABLE `".$data[0]."`;";
         $DB->query($query_delete) or die($DB->error());
      }
   }
}

?>