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

$LANG['plugin_relations']['params'][0]='Type';
$LANG['plugin_relations']['params'][1]='Parent';
$LANG['plugin_relations']['params'][2]='Children';
$LANG['plugin_relations']['params'][3]='Name';
$LANG['plugin_relations']['params'][4]='Siblings';
$LANG['plugin_relations']['params'][5]='Remove';
$LANG['plugin_relations']['params'][6]='Change Parent to...';

$LANG['plugin_relations']['errors'][0]='Sorry. You cannot add a descendant as a parent!';
$LANG['plugin_relations']['errors'][1]='Sorry. You cannot add an ancestor as a child!';
$LANG['plugin_relations']['errors'][2]='Failed to purge associated relationships';
$LANG['plugin_relations']['errors'][3]='Also purged associated relationships';
$LANG['plugin_relations']['errors'][4]='Sorry. A parent cannot be its own child!';

$LANG['plugin_nagiosql']['setup'][1] = 'This plugin requires GLPI version 0.80 or higher';

?>