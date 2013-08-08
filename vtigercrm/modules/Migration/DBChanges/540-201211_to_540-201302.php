<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * @author igor.struchkov@salesplatform.ru
 */

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

if (!function_exists('getTabidDB')) {
    function getTabidDB($modulename) {
        global $adb;

        $res = $adb->pquery('select tabid from vtiger_tab where name=?', array($modulename));
        if($adb->num_rows($res) > 0) {
            return $adb->query_result($res, 0, 'tabid');
        }
        return '';
    }
}

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.4.0-201211 to 5.4.0-201302 -------- Starts \n\n");

ExecuteQuery("CREATE TABLE `sp_custom_reports` (                                                                                                          
  `reporttype` varchar(50) NOT NULL,                                                                                                        
  `datefilter` int(1) default 0,                                                                                                            
  `ownerfilter` int(1) default 0,                                                                                                           
  `accountfilter` int(1) default 0,                                                                                                         
  `functionname` varchar(255) NOT NULL,                                                                                                     
  PRIMARY KEY  (`reporttype`)                                                                                                               
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");


$tabid = getTabidDB('Consignment');
if(!empty($tabid)) {
    $blockid = $adb->getUniqueID("vtiger_blocks");

    ExecuteQuery("update vtiger_blocks set sequence=sequence+1 where tabid=$tabid and sequence > 1");
    ExecuteQuery("insert into vtiger_blocks values($blockid, $tabid, 'LBL_GOODS_CONSIGNMENT', 2, 0, 0, 0, 0, 0, 1, 0)");

    ExecuteQuery("ALTER TABLE `vtiger_sp_consignment` ADD `has_goods_consignment` varchar(3) default '0'");
    $field_id = $adb->getUniqueID("vtiger_field");
    ExecuteQuery("insert into vtiger_field values($tabid, $field_id, 'has_goods_consignment', 'vtiger_sp_consignment', 1, 56, 'has_goods_consignment', 'Has Goods Consignment', 1, 0, '', 100, 1, $blockid, 1, 'C~O', 3, NULL, 'BAS', 0, NULL)");
    ExecuteQuery("insert into vtiger_profile2field select profileid,$tabid, $field_id, 0, 1 from vtiger_profile");
    ExecuteQuery("insert into vtiger_def_org_field values($tabid, $field_id, 0, 1)");

    ExecuteQuery("ALTER TABLE `vtiger_sp_consignment` ADD `goods_consignment_no` int(19) default NULL");
    $field_id = $adb->getUniqueID("vtiger_field");
    ExecuteQuery("insert into vtiger_field values($tabid, $field_id, 'goods_consignment_no', 'vtiger_sp_consignment', 1, 1, 'goods_consignment_no', 'Goods Consignment No', 1, 0, '', 100, 2, $blockid, 1, 'V~O', 3, NULL, 'BAS', 0, NULL)");
    ExecuteQuery("insert into vtiger_profile2field select profileid,$tabid, $field_id, 0, 1 from vtiger_profile");
    ExecuteQuery("insert into vtiger_def_org_field values($tabid, $field_id, 0, 1)");
}

ExecuteQuery("ALTER TABLE vtiger_inventoryproductrel ADD prod_subtotal decimal(25, 3)");

$tabid = getTabidDB('HelpDesk');
if(!empty($tabid)) {
    ExecuteQuery("UPDATE `vtiger_field` SET typeofdata='N~O' WHERE fieldname='hours' AND tabid=$tabid");
}

$tabid = getTabidDB('Documents');
if(!empty($tabid)) {
    $linkid = $adb->getUniqueID("vtiger_links");
    ExecuteQuery('INSERT INTO `vtiger_links` (`linkid`, `tabid`, `linktype`, `linklabel`, `linkurl`, `linkicon`, `sequence`, `handler_path`, `handler_class`, `handler`) VALUES ('.$linkid.', '.$tabid.', \'DETAILVIEWWIDGET\', \'LBL_RELATED_TO\', \'module=Documents&action=DocumentsAjax&file=DetailViewAjax&recordid=$RECORD$&ajxaction=LOADRELATEDLISTWIDGET\', \'\', 0, NULL, NULL, NULL)');
}

if(file_exists('modules/Act')) {
    updateVtlibModule('Act', 'packages/vtiger/optional/Act.zip');
}
if(file_exists('modules/Consignment')) {
    updateVtlibModule('Consignment', 'packages/vtiger/optional/Consignment.zip');
}

$migrationlog->debug("\n\nDB Changes from 5.4.0-201211 to 5.4.0-201302 -------- Ends \n\n");
?>