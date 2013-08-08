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

$migrationlog->debug("\n\nDB Changes from 5.4.0-201302 to 5.4.0-201308 -------- Starts \n\n");

$tabid = getTabidDB('Vendors');
$rel_tabid = getTabidDB('Documents');
$rel_tabid2 = getTabidDB('Calendar');
if(!empty($tabid) && !empty($rel_tabid) && !empty($rel_tabid2)) {
    $linkid = $adb->getUniqueID("vtiger_links");
    ExecuteQuery('INSERT INTO vtiger_links VALUES('.$linkid.', '.$tabid.', \'DETAILVIEWBASIC\', \'LBL_ADD_NOTE\', \'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$\', \'themes/images/bookMark.gif\', 0, NULL, NULL, NULL)');

    $rel_id = $adb->getUniqueID("vtiger_relatedlists");
    ExecuteQuery("INSERT INTO vtiger_relatedlists VALUES($rel_id, $tabid, $rel_tabid, 'get_attachments', 5, 'Documents', 0, 'add,select')");

    $rel_id = $adb->getUniqueID("vtiger_relatedlists");
    ExecuteQuery("INSERT INTO vtiger_relatedlists VALUES($rel_id, $tabid, $rel_tabid2, 'get_activities', 6, 'Activities', 0, 'add')");

    $rel_id = $adb->getUniqueID("vtiger_relatedlists");
    ExecuteQuery("INSERT INTO vtiger_relatedlists VALUES($rel_id, $tabid, $rel_tabid2, 'get_history', 7, 'Activity History', 0, 'add')");

    $linkid = $adb->getUniqueID("vtiger_links");
    ExecuteQuery('INSERT INTO vtiger_links VALUES('.$linkid.', '.$tabid.', \'DETAILVIEWBASIC\', \'LBL_VENDORS_ADD_EVENTS\', \'index.php?module=Calendar&action=EditView&return_module=Vendors&return_action=DetailView&activity_mode=Events&return_id=$RECORD$&parent_id=$RECORD$&parenttab=Sales\', \'themes/images/AddEvent.gif\', 0, NULL, NULL, NULL)');

    $linkid = $adb->getUniqueID("vtiger_links");
    ExecuteQuery('INSERT INTO vtiger_links VALUES('.$linkid.', '.$tabid.', \'DETAILVIEWBASIC\', \'LBL_VENDORS_ADD_TASK\', \'index.php?module=Calendar&action=EditView&return_module=Vendors&return_action=DetailView&activity_mode=Task&return_id=$RECORD$&parent_id=$RECORD$&parenttab=Sales\', \'themes/images/AddToDo.gif\', 0, NULL, NULL, NULL)');
}

$tabid = getTabidDB('ProjectTask');
if(!empty($tabid) && !empty($rel_tabid2)) {
    $rel_id = $adb->getUniqueID("vtiger_relatedlists");
    ExecuteQuery("INSERT INTO vtiger_relatedlists VALUES($rel_id, $tabid, $rel_tabid2, 'get_activities', 2, 'Activities', 0, 'add')");

    $rel_id = $adb->getUniqueID("vtiger_relatedlists");
    ExecuteQuery("INSERT INTO vtiger_relatedlists VALUES($rel_id, $tabid, $rel_tabid2, 'get_history', 3, 'Activity History', 0, 'add')");

    $linkid = $adb->getUniqueID("vtiger_links");
    ExecuteQuery('INSERT INTO vtiger_links VALUES('.$linkid.', '.$tabid.', \'DETAILVIEWBASIC\', \'LBL_PROJECTTASK_ADD_EVENTS\', \'index.php?module=Calendar&action=EditView&return_module=ProjectTask&return_action=DetailView&activity_mode=Events&return_id=$RECORD$&parent_id=$RECORD$&parenttab=Sales\', \'themes/images/AddEvent.gif\', 0, NULL, NULL, NULL)');

    $linkid = $adb->getUniqueID("vtiger_links");
    ExecuteQuery('INSERT INTO vtiger_links VALUES('.$linkid.', '.$tabid.', \'DETAILVIEWBASIC\', \'LBL_PROJECTTASK_ADD_TASK\', \'index.php?module=Calendar&action=EditView&return_module=ProjectTask&return_action=DetailView&activity_mode=Task&return_id=$RECORD$&parent_id=$RECORD$&parenttab=Sales\', \'themes/images/AddToDo.gif\', 0, NULL, NULL, NULL)');
}

$tabid = getTabidDB('HelpDesk');
if(!empty($tabid)) {
    $linkid = $adb->getUniqueID("vtiger_links");
    ExecuteQuery('INSERT INTO `vtiger_links` (`linkid`, `tabid`, `linktype`, `linklabel`, `linkurl`, `linkicon`, `sequence`, `handler_path`, `handler_class`, `handler`) VALUES ('.$linkid.', '.$tabid.', \'DETAILVIEWWIDGET\', \'LBL_RELATED_TO\', \'module=HelpDesk&action=HelpDeskAjax&file=DetailViewAjax&recordid=$RECORD$&ajxaction=LOADRELATEDLISTWIDGET\', \'\', 0, NULL, NULL, NULL)');
}

$tabid = getTabidDB('Accounts');
$rel_tabid = getTabidDB('Act');
if(!empty($tabid) && !empty($rel_tabid)) {
    $linkid = $adb->getUniqueID("vtiger_links");
    $seq = $adb->query_result($adb->query("SELECT max(sequence)+1 as seq FROM vtiger_links WHERE tabid = $tabid"), 0, 'seq');
    ExecuteQuery('INSERT INTO vtiger_links VALUES('.$linkid.', '.$tabid.', \'DETAILVIEWBASIC\', \'LBL_ACCOUNTS_ADD_ACT\', \'index.php?module=Act&action=EditView&return_module=Accounts&return_action=DetailView&convertmode=accountstoact&createmode=link&return_id=$RECORD$&parent_id=$RECORD$&parenttab=Sales\', \'themes/images/actionGenerateInvoice.gif\', '.$seq.', NULL, NULL, NULL)');
}

$rel_tabid = getTabidDB('Consignment');
if(!empty($tabid) && !empty($rel_tabid)) {
    $linkid = $adb->getUniqueID("vtiger_links");
    $seq = $adb->query_result($adb->query("SELECT max(sequence)+1 as seq FROM vtiger_links WHERE tabid = $tabid"), 0, 'seq');
    ExecuteQuery('INSERT INTO vtiger_links VALUES('.$linkid.', '.$tabid.', \'DETAILVIEWBASIC\', \'LBL_ACCOUNTS_ADD_CONSIGNMENT\', \'index.php?module=Consignment&action=EditView&return_module=Accounts&return_action=DetailView&convertmode=acctoconsignment&createmode=link&return_id=$RECORD$&parent_id=$RECORD$&parenttab=Sales\', \'themes/images/actionGenerateInvoice.gif\', '.$seq.', NULL, NULL, NULL)');
}

$cvid = $adb->query_result($adb->query("SELECT cvid FROM `vtiger_customview` WHERE entitytype = 'Emails' AND viewname = 'All'"), 0, 'cvid');
$seq = $adb->query_result($adb->query("SELECT max(columnindex)+1 as seq FROM vtiger_cvcolumnlist WHERE cvid = $cvid"), 0, 'seq');
ExecuteQuery("INSERT INTO vtiger_cvcolumnlist (cvid, columnindex, columnname) VALUES ($cvid, $seq, 'vtiger_activity:time_start:time_start:Emails_Time_Start:V')");

$migrationlog->debug("\n\nDB Changes from 5.4.0-201302 to 5.4.0-201308 -------- Ends \n\n");
?>