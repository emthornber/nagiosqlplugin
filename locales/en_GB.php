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
// Original Author : E M Thornber
// Purpose of File :
// ------------------------------------------------------------------------

$title="NagiosQL";

// English

$LANG['plugin_nagiosql']['title'][0]="$title";

$LANG['plugin_nagiosql']['params'][0]='Type';
$LANG['plugin_nagiosql']['params'][1]='Parent';
$LANG['plugin_nagiosql']['params'][2]='Children';
$LANG['plugin_nagiosql']['params'][3]='Name';
$LANG['plugin_nagiosql']['params'][4]='Siblings';
$LANG['plugin_nagiosql']['params'][5]='Remove';
$LANG['plugin_nagiosql']['params'][6]='Change Parent to...';

$LANG['plugin_relations']['log'][0]='Linked to child with ID';
$LANG['plugin_relations']['log'][1]='Linked to parent with ID';
$LANG['plugin_relations']['log'][2]='Unlinked from child with ID';
$LANG['plugin_relations']['log'][3]='Unlinked from parent with ID';

$LANG['plugin_nagiosql']['errors'][0]='Sorry. You cannot add a descendant as a parent!';
$LANG['plugin_nagiosql']['errors'][1]='Sorry. You cannot add an ancestor as a child!';
$LANG['plugin_nagiosql']['errors'][2]='Failed to purge associated links';
$LANG['plugin_nagiosql']['errors'][3]='Also purged associated links';
$LANG['plugin_nagiosql']['errors'][4]='Sorry. A parent cannot be its own child!';

$LANG['plugin_nagiosql']['profile'][0] = 'Rights management';
$LANG['plugin_nagiosql']['profile'][1] = $title;
$LANG['plugin_nagiosql']['profile'][6] = 'List of profiles already configured';

$LANG['plugin_nagiosql']['setup'][1] = 'This plugin requires GLPI version 0.80 or higher';

?>