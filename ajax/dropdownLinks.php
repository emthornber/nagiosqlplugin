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
 
if ( preg_match("/dropdownLinks.php/", $_SERVER['PHP_SELF']) ){
	define('GLPI_ROOT', '../../..');
	$AJAX_INCLUDE=1;
	include (GLPI_ROOT.'/inc/includes.php');
	header('Content-Type: text/html; charset=UTF-8');
	header_nocache();
}

checkCentralAccess();

// Make a select box
if ( isset($_POST['type_links']) ){
	$rand=$_POST['rand'];

	switch( $_POST['itemtype'] ){
		case 'Computer' :
			$table = 'glpi_computers';
			$mytype = 'computertypes_id';
			break;
		case 'NetworkEquipment' :
			$table = 'glpi_networkequipments';
			$mytype = 'networkequipmenttypes_id';
			break;
		case 'Supplier' :
			$table = 'glpi_suppliers';
			$mytype = 'suppliertypes_id';
			break;
		default:
			$table='';
			break;
	}
	$use_ajax = false;
	if ($CFG_GLPI['use_ajax'] && 
		countElementsInTable($table,"$table.$mytype='".$_POST['type_links'].
		"' ".getEntitiesRestrictRequest('AND',$table,'',$_POST['entity_restrict'],false) )>$CFG_GLPI['ajax_limit_count'])
	{
		$use_ajax=true;
	}

	$params = array('searchText'=>'__VALUE__',
			'type_links'=>$_POST['type_links'],
			'entity_restrict'=>$_POST['entity_restrict'],
			'itemtype'=>$_POST['itemtype'],
			'rand'=>$_POST['rand'],
			'myname'=>$_POST['myname'],
			'used'=>$_POST['used']
			);
	
	$default='<select name="'.$_POST['myname'].'"><option value="0">------</option></select>';
	ajaxDropdown($use_ajax,'/plugins/nagiosql/ajax/dropdownValue.php',$params,$default,$rand);

}		

?>