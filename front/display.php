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
 
if (!defined('GLPI_ROOT')) {
   define('GLPI_ROOT', '../../..');
}

require_once GLPI_ROOT."/inc/includes.php";

checkLoginUser();

popHeader($LANG['plugin_nagiosql']['title'][0], $_SERVER["PHP_SELF"]);

echo '<meta http-equiv ="refresh" content="30">';

$_SESSION['plugin_nagiosql']['service'] = $_GET;

$pNagiosqlDisplay = new PluginNagiosqlDisplay();
$pNagiosqlDisplay->displayCounters();
$pNagiosqlDisplay->showTabs();
echo "<style type='text/css'>
div#tabcontent {
   width:99%;
}

</style>";
$pNagiosqlDisplay->addDivForTabs();

popFooter();

?>