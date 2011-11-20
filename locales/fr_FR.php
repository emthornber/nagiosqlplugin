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

$LANG['plugin_nagiosql']['params'][0]='Type';
$LANG['plugin_nagiosql']['params'][1]='Parent';
$LANG['plugin_nagiosql']['params'][2]='Enfants';
$LANG['plugin_nagiosql']['params'][3]='Nom';
$LANG['plugin_nagiosql']['params'][4]='Enfants du même parent';
$LANG['plugin_nagiosql']['params'][5]='Supprimer';
$LANG['plugin_nagiosql']['params'][6]='Modifier le parent...';

$LANG['plugin_relations']['log'][0]="Associé à l\'enfant dont l\'ID est";
$LANG['plugin_relations']['log'][1]="Associé au parent dont l\'ID est";
$LANG['plugin_relations']['log'][2]="Dissocié de l\'enfant dont l\'ID est";
$LANG['plugin_relations']['log'][3]="Dissocié du parent dont l\'ID est";

$LANG['plugin_nagiosql']['errors'][0]='Désolé. Vous ne pouvez pas ajouter un descendant en tant que parent!';
$LANG['plugin_nagiosql']['errors'][1]='Désolé. Vous ne pouvez pas ajouter un ancêtre comme un enfant!';
$LANG['plugin_nagiosql']['errors'][2]='Impossible de purger les lien associées';
$LANG['plugin_nagiosql']['errors'][3]='Lien associées également purgées';
$LANG['plugin_nagiosql']['errors'][4]='Désolé. Un parent ne peut pas être son propre enfant !';

$LANG['plugin_nagiosql']['profile'][0] = 'Gestion des droits';
$LANG['plugin_nagiosql']['profile'][1] = $title;
$LANG['plugin_nagiosql']['profile'][6] = 'Listes des profils déjà configurés';

$LANG['plugin_nagiosql']['setup'][1] = 'Ce plugin requiert GLPI version 0.80 ou supérieur';
$LANG['plugin_relations']['setup'][3] = 'Configuration du plugin '.$title;
$LANG['plugin_relations']['setup'][4] = 'Installer le plugin '.$title;
$LANG['plugin_relations']['setup'][1] = 'Votre système contient des données du plugin '.$title.' Ces données ont été restaurées.';
$LANG['plugin_relations']['setup'][5] = 'Votre système contient des données d\'une version antérieure du plugin '.$title.' . Ces données vont être mises à jour lors de l\'activation du plugin.';
$LANG['plugin_relations']['setup'][6] = 'Désinstaller le plugin '.$title;
$LANG['plugin_relations']['setup'][8] = 'Attention, la désinstallation du plugin est irréversible.<br> Vous perdrez toutes les données.';
$LANG['plugin_relations']['setup'][7] = 'Le plugin $title a été désinstallé mais les données correspondantes n\'ont pas été supprimées.';
$LANG['plugin_relations']['setup'][9] = 'Cliquer ici pour supprimer toutes les données du plugin $title.';
$LANG['plugin_relations']['setup'][10] = 'Êtes-vous certain de vouloir supprimer toutes les données du plugin $title.';
$LANG['plugin_relations']['setup'][11] = "Mode d\'emploi";
$LANG['plugin_relations']['setup'][12] = 'FAQ';
$LANG['plugin_relations']['setup'][14] = "Merci de vous placer sur l\'entité racine (voir tous)";

?>