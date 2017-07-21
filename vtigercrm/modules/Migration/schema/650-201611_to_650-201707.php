<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

chdir(dirname(__FILE__) . '/../../../');
include_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
include_once 'include/utils/utils.php';

vimport('~~modules/com_vtiger_workflow/include.inc');
vimport('~~modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc');
vimport('~~modules/com_vtiger_workflow/VTEntityMethodManager.inc');
vimport('~~include/Webservices/Utils.php');
vimport('~~modules/Users/Users.php');

global $adb;

//Vtiger migration begin
//Vtiger migration end


//SalesPlatform.ru migration begin

Migration_Index_View::ExecuteQuery("CREATE INDEX idx_crmentityrel_crmid ON vtiger_crmentityrel(crmid)", array());
Migration_Index_View::ExecuteQuery("CREATE INDEX idx_crmentityrel_relcrmid ON vtiger_crmentityrel(relcrmid)", array());

Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET `masseditable`=1 WHERE `tabid`=43 AND `tablename`='vtiger_sp_actbillads'", array());
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET `masseditable`=1 WHERE `tabid`=43 AND `tablename`='vtiger_sp_actshipads'", array());
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET `masseditable`=1 WHERE `tabid`=43 AND `fieldname` IN ('actdate', 'account_id','salesorder_id', 'contact_id', 'description')", array());
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET `masseditable`=1 WHERE `tabid`=46 AND `tablename`='vtiger_sp_consignmentbillads'", array());
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET `masseditable`=1 WHERE `tabid`=46 AND `tablename`='vtiger_sp_consignmentshipads'", array());
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET `masseditable`=1 WHERE `tabid`=46 AND `fieldname` IN ('salesorder_id', 'consigmentdate', 'account_id', 'contact_id', 'description', 'has_goods_consignment', 'goods_consignment_no')", array());

$relationId = $adb->getUniqueID('vtiger_relatedlists');
$contactTabId = getTabid('Contacts');
$smsNotifierTabId = getTabId('SMSNotifier');

$query = 'SELECT max(sequence) as maxsequence FROM vtiger_relatedlists where tabid = ?';
$result = $adb->pquery($query, array($contactTabId));
$sequence = $adb->query_result($result, 0 ,'maxsequence');

$query = 'INSERT INTO vtiger_relatedlists (relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)';
Migration_Index_View::ExecuteQuery($query, array($relationId, $contactTabId,$smsNotifierTabId,'get_related_list',($sequence+1),'SMSNotifier',0));

$relationId = $adb->getUniqueID('vtiger_relatedlists');
$accountTabId = getTabid('Accounts');
$smsNotifierTabId = getTabId('SMSNotifier');

$query = 'SELECT max(sequence) as maxsequence FROM vtiger_relatedlists where tabid = ?';
$result = $adb->pquery($query, array($accountTabId));
$sequence = $adb->query_result($result, 0 ,'maxsequence');

$query = 'INSERT INTO vtiger_relatedlists (relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)';
Migration_Index_View::ExecuteQuery($query, array($relationId, $accountTabId,$smsNotifierTabId,'get_related_list',($sequence+1),'SMSNotifier',0));

$relationId = $adb->getUniqueID('vtiger_relatedlists');
$leadsTabId = getTabid('Leads');
$smsNotifierTabId = getTabId('SMSNotifier');

$query = 'SELECT max(sequence) as maxsequence FROM vtiger_relatedlists where tabid = ?';
$result = $adb->pquery($query, array($leadsTabId));
$sequence = $adb->query_result($result, 0 ,'maxsequence');

$query = 'INSERT INTO vtiger_relatedlists (relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)';
Migration_Index_View::ExecuteQuery($query, array($relationId, $leadsTabId,$smsNotifierTabId,'get_related_list',($sequence+1),'SMSNotifier',0));

if (defined('VTIGER_UPGRADE')) {
    
    Migration_Index_View::ExecuteQuery("UPDATE `vtiger_customview` SET `viewname`='Черновики БЗ' WHERE `viewname`='Черновики ЧаВо'", array());
    Migration_Index_View::ExecuteQuery("UPDATE `vtiger_customview` SET `viewname`='Опубликованные БЗ' WHERE `viewname`='Опубликованные ЧаВо'", array());
    Migration_Index_View::ExecuteQuery("UPDATE `vtiger_modentity_num` SET `prefix`='БЗ_' WHERE `prefix`='ЧаВо_'", array());

    $result = $adb->pquery("SELECT faq_no FROM vtiger_faq ", array());
    Migration_Index_View::ExecuteQuery("UPDATE `vtiger_faq` SET `faq_no`=CONCAT('БЗ_',?)", array(strrchr($result,"_")));
    
    
    /* Custom reports migrate */
    Migration_Index_View::ExecuteQuery("ALTER TABLE sp_custom_reports DROP COLUMN functionname", array());
    Migration_Index_View::ExecuteQuery("ALTER TABLE sp_custom_reports DROP COLUMN accountfilter", array());
    Migration_Index_View::ExecuteQuery("ALTER TABLE sp_custom_reports DROP COLUMN ownerfilter", array());
    Migration_Index_View::ExecuteQuery("ALTER TABLE sp_custom_reports DROP COLUMN datefilter", array());
}
//SalesPlatform.ru migration end

