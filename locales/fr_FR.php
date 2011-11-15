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

//Français

$LANG['plugin_nagiosql']['title'][0]="$title";

$LANG['plugin_relations']['params'][0]='Type';
$LANG['plugin_relations']['params'][1]='Parent';
$LANG['plugin_relations']['params'][2]='Enfants';
$LANG['plugin_relations']['params'][3]='Nom';
$LANG['plugin_relations']['params'][4]='Enfants du même parent';
$LANG['plugin_relations']['params'][5]='Supprimer';
$LANG['plugin_relations']['params'][6]='Modifier le parent...';

$LANG['plugin_relations']['errors'][0]='Désolé. Vous ne pouvez pas ajouter un descendant en tant que parent!';
$LANG['plugin_relations']['errors'][1]='Désolé. Vous ne pouvez pas ajouter un ancêtre comme un enfant!';
$LANG['plugin_relations']['errors'][2]='Impossible de purger les relations associées';
$LANG['plugin_relations']['errors'][3]='Relations associées également purgées';
$LANG['plugin_relations']['errors'][4]='Désolé. Un parent ne peut pas être son propre enfant !';

$LANG['plugin_nagiosql']['setup'][1] = 'Ce plugin requiert GLPI version 0.80 ou supérieur';

?>