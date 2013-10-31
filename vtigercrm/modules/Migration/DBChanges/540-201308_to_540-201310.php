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

$migrationlog->debug("\n\nDB Changes from 5.4.0-201308 to 5.4.0-201310 -------- Starts \n\n");

ExecuteQuery("UPDATE vtiger_links SET linkicon = 'themes/images/send_sms.png' WHERE linktype = 'DETAILVIEWBASIC' and linklabel = 'Send SMS'");
ExecuteQuery("UPDATE vtiger_links SET linkicon = 'themes/images/products.gif' WHERE linktype = 'DETAILVIEWBASIC' and linklabel = 'LBL_SHOW_ACCOUNT_HIERARCHY'");
ExecuteQuery("UPDATE vtiger_links SET linkicon = 'themes/images/actionGenerateInvoice_new.gif' WHERE linktype = 'DETAILVIEWBASIC' and linklabel = 'LBL_SP_ADD_SPPAYMENTS'");
ExecuteQuery("UPDATE vtiger_links SET linkicon = 'themes/images/actionGenerateQuote_new.gif' WHERE linktype = 'DETAILVIEWBASIC' and linklabel = 'LBL_ACCOUNTS_ADD_ACT'");
ExecuteQuery("UPDATE vtiger_links SET linkicon = 'themes/images/actionGenerateSalesOrder_new.gif' WHERE linktype = 'DETAILVIEWBASIC' and linklabel = 'LBL_ACCOUNTS_ADD_CONSIGNMENT'");
ExecuteQuery("UPDATE vtiger_links SET linkicon = 'themes/images/bookMark.gif' WHERE linktype = 'DETAILVIEWBASIC' and linklabel = 'Add Note'");
ExecuteQuery("UPDATE vtiger_links SET linkicon = 'themes/images/attachment.gif' WHERE linktype = 'DETAILVIEWBASIC' and linklabel = 'Add Project Task'");

$workflowtasks_entitymethod_id = $adb->getUniqueID("com_vtiger_workflowtasks_entitymethod");
ExecutePQuery("INSERT INTO `com_vtiger_workflowtasks_entitymethod` VALUES (?,'PurchaseOrder','UpdateInventoryPurchase','include/InventoryHandler.php','handleInventoryPurchase')", array($workflowtasks_entitymethod_id));

$tabid = getTabidDB('Vendors');
$ruleid = $adb->getUniqueID("vtiger_def_org_share");
ExecutePQuery("INSERT INTO vtiger_def_org_share VALUES(?,?,2,0)", array($ruleid, $tabid));
ExecutePQuery("INSERT INTO vtiger_org_share_action2tab VALUES(0, ?)", array($tabid));
ExecutePQuery("INSERT INTO vtiger_org_share_action2tab VALUES(1, ?)", array($tabid));
ExecutePQuery("INSERT INTO vtiger_org_share_action2tab VALUES(2, ?)", array($tabid));
ExecutePQuery("INSERT INTO vtiger_org_share_action2tab VALUES(3, ?)", array($tabid));
ExecutePQuery("UPDATE vtiger_tab SET ownedby=0 WHERE tabid = ?", array($tabid));

$res = $adb->pquery("SELECT blockid FROM vtiger_blocks WHERE blocklabel='LBL_VENDOR_INFORMATION' AND tabid=?", array($tabid));
if($adb->num_rows($res) > 0) {
    $blockid = $adb->query_result($res, 0, 'blockid');
    $fieldid = $adb->getUniqueID("vtiger_field");
    
    $res = $adb->pquery("SELECT MAX(sequence) as seq FROM vtiger_field WHERE block = ? AND tabid = ?", array($blockid, $tabid));
    if($adb->num_rows($res) > 0) {
	$seq = $adb->query_result($res, 0, 'seq') + 1;

	$res = $adb->pquery("SELECT MAX(quickcreatesequence) as qseq FROM vtiger_field WHERE tabid = ?", array($tabid));
	if($adb->num_rows($res) > 0) {
	    $qseq = $adb->query_result($res, 0, 'qseq') + 1;
	    
	    ExecutePQuery("INSERT INTO `vtiger_field` (`tabid`, `fieldid`, `columnname`, `tablename`, `generatedtype`, `uitype`, `fieldname`, `fieldlabel`, `readonly`, `presence`, `defaultvalue`, `maximumlength`, `sequence`, `block`, `displaytype`, `typeofdata`, `quickcreate`, `quickcreatesequence`, `info_type`, `masseditable`, `helpinfo`) VALUES (?, ?, 'smownerid', 'vtiger_crmentity', 1, '53', 'assigned_user_id', 'Assigned To', 1, 0, '', 100, ?, ?, 1, 'V~M', 0, ?, 'BAS', 1, NULL)",
		array($tabid, $fieldid, $seq, $blockid, $qseq));

            ExecutePQuery("INSERT INTO vtiger_def_org_field VALUES(?, ?, 0, 0)",
                array($tabid, $fieldid));
	}
    }
}

ExecuteQuery("ALTER TABLE `vtiger_links` MODIFY `linkurl` varchar(1024) default NULL");
ExecuteQuery("ALTER TABLE `vtiger_organizationdetails` ADD `okpo` varchar(100) default ''");

if(file_exists('modules/Act')) {
    updateVtlibModule('Act', 'packages/vtiger/optional/Act.zip');
}
if(file_exists('modules/Consignment')) {
    updateVtlibModule('Consignment', 'packages/vtiger/optional/Consignment.zip');
}
if(file_exists('modules/SPPayments')) {
    updateVtlibModule('SPPayments', 'packages/vtiger/optional/SPPayments.zip');
}

$tabid = getTabidDB('SPPayments');
if($tabid != '') {
    ExecutePQuery("UPDATE vtiger_field SET typeofdata = 'I~O' WHERE fieldname = 'doc_no' and tabid = ?",
        array($tabid));
}

$migrationlog->debug("\n\nDB Changes from 5.4.0-201308 to 5.4.0-201310 -------- Ends \n\n");
?>