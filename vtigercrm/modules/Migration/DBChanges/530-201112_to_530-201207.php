<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once 'include/utils/utils.php';

// SalesPlatform 5.3.0-201112 to 5.3.0-201207

$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.3.0-201112 to 5.3.0-201207 -------- Starts \n\n");

require_once 'include/utils/CommonUtils.php';
//global $adb;

changeDBCollation('utf8_general_ci');

create_tab_data_file();

// Add PriceBooks and SalesOrders to the customer portal
$priceBooksTabId = getTabid('PriceBooks');
$salesOrderTabId = getTabid('SalesOrder');
$result = $adb->pquery('select max(sequence)+1 as seq from vtiger_customerportal_tabs', array());
$seq =  $adb->query_result($result, 0, 'seq');
// SalesPlatform.ru begin Check initial seq value
$seq = $seq ? $seq : 1;
// SalesPlatform.ru end
ExecuteQuery("INSERT INTO `vtiger_customerportal_tabs` (`tabid`, `visible`, `sequence`) VALUES ($priceBooksTabId, 1, $seq)");
ExecuteQuery("INSERT INTO `vtiger_customerportal_prefs` (`tabid`, `prefkey`, `prefvalue`) VALUES ($priceBooksTabId, 'showrelatedinfo', 1)");
$seq++;
ExecuteQuery("INSERT INTO `vtiger_customerportal_tabs` (`tabid`, `visible`, `sequence`) VALUES ($salesOrderTabId, 1, $seq)");
ExecuteQuery("INSERT INTO `vtiger_customerportal_prefs` (`tabid`, `prefkey`, `prefvalue`) VALUES ($salesOrderTabId, 'showrelatedinfo', 1)");

// Add activity reminder popup type and title
ExecuteQuery("ALTER TABLE vtiger_activity_reminder_popup ADD rtype int(2) not null default 0");
ExecuteQuery("ALTER TABLE vtiger_activity_reminder_popup ADD title varchar(255) not null default ''");

// Fix Users phone fields type
ExecuteQuery("UPDATE vtiger_field SET uitype=11 WHERE tablename = 'vtiger_users' AND columnname LIKE 'phone_%'");

// Add SP ajax search module
ExecuteQuery("CREATE TABLE IF NOT EXISTS `sp_module_picklist_fields_rel` (
    `fieldid` int(19) NOT NULL,
    `srcfieldid` int(19) NOT NULL,
    KEY `fieldid_idx` (`fieldid`),
    KEY `srcfieldid_idx` (`srcfieldid`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");
$result = $adb->pquery('select max(tabid)+1 as maxtabid, max(tabsequence)+1 as seq from vtiger_tab', array());
$maxtabid =  $adb->query_result($result, 0, 'maxtabid');
$seq =  $adb->query_result($result, 0, 'seq');
ExecuteQuery("INSERT INTO vtiger_tab(tabid,name,presence,tabsequence,tablabel,modifiedby,modifiedtime,customized,ownedby,isentitytype,version)
              VALUES ($maxtabid,'SPModulePickList',0,$seq,'SPModulePickList',null,null,0,0,0,'1.0')");
ExecuteQuery("insert into vtiger_moduleowners values($maxtabid,1)");

// Add manufacturing country code and units code to Products
$productsModuleInstance = Vtiger_Module::getInstance('Products');

$blockInstance = Vtiger_Block::getInstance('LBL_PRODUCT_INFORMATION', $productsModuleInstance);
$fieldInstance = new Vtiger_Field();
$fieldInstance->name = 'manuf_country_code';
$fieldInstance->label = 'Mf. Country Code';
$fieldInstance->table = 'vtiger_products';
$fieldInstance->column = 'manuf_country_code';
$fieldInstance->columntype = 'VARCHAR(100)';
$fieldInstance->uitype = 1;
$blockInstance->addField($fieldInstance);

$blockInstance = Vtiger_Block::getInstance('LBL_STOCK_INFORMATION', $productsModuleInstance);
$fieldInstance = new Vtiger_Field();
$fieldInstance->name = 'unit_code';
$fieldInstance->label = 'Unit Code';
$fieldInstance->table = 'vtiger_products';
$fieldInstance->column = 'unit_code';
$fieldInstance->columntype = 'VARCHAR(100)';
$fieldInstance->uitype = 1;
$blockInstance->addField($fieldInstance);

// Add units code to Services
$servicesModuleInstance = Vtiger_Module::getInstance('Services');

$blockInstance = Vtiger_Block::getInstance('LBL_SERVICE_INFORMATION', $servicesModuleInstance);
$fieldInstance = new Vtiger_Field();
$fieldInstance->name = 'unit_code';
$fieldInstance->label = 'Unit Code';
$fieldInstance->table = 'vtiger_service';
$fieldInstance->column = 'unit_code';
$fieldInstance->columntype = 'VARCHAR(100)';
$fieldInstance->uitype = 1;
$blockInstance->addField($fieldInstance);

// Add comments to Potentials
$modCommentsTabId = getTabid('ModComments');
if($modCommentsTabId) {
    $result = $adb->pquery("select fieldid from vtiger_field where tabid=$modCommentsTabId and fieldname='related_to'", array());
    if($result && $adb->num_rows($result) > 0) {
        $fieldId = $adb->query_result($result, 0, 'fieldid');
        $potentialsTabId = getTabid('Potentials');
        $linkId = $adb->getUniqueID("vtiger_links");
        ExecuteQuery("insert into vtiger_links values($linkId, $potentialsTabId, 'DETAILVIEWWIDGET', 'DetailViewBlockCommentWidget', 'block://ModComments:modules/ModComments/ModComments.php', '', 0, NULL, NULL, NULL)");
        ExecuteQuery("insert into vtiger_fieldmodulerel values($fieldId, 'ModComments', 'Potentials', NULL, NULL)");
    }
}

updateVtlibModule('MailManager', 'packages/vtiger/mandatory/MailManager.zip');
updateVtlibModule('PBXManager', 'packages/vtiger/mandatory/PBXManager.zip');
updateVtlibModule('Services', 'packages/vtiger/mandatory/Services.zip');

if(file_exists('modules/CustomerPortal'))
    updateVtlibModule('CustomerPortal', 'packages/vtiger/optional/CustomerPortal.zip');
if(file_exists('modules/ModComments'))
    updateVtlibModule('ModComments', 'packages/vtiger/optional/ModComments.zip');
if(file_exists('modules/SMSNotifier'))
    updateVtlibModule('SMSNotifier', 'packages/vtiger/optional/SMSNotifier.zip');
if(file_exists('modules/Tooltip'))
    updateVtlibModule('Tooltip', 'packages/vtiger/optional/Tooltip.zip');

// Add PurchaseOrder PDF template
$result = $adb->pquery('select id from sp_templates_seq', array());
if($result && $adb->num_rows($result) > 0) {
    $templateid = $adb->query_result($result, 0, 'id') + 1;
    ExecuteQuery('update sp_templates_seq set id='.$templateid);
} else {
    $result = $adb->pquery('select max(templateid) as id from sp_templates', array());
    $templateid = $adb->query_result($result, 0, 'id') + 1;
    ExecuteQuery("create table `sp_templates_seq` (`id` int(11) not NULL) ENGINE=InnoDB  DEFAULT CHARSET=utf8");
    ExecuteQuery("insert into sp_templates_seq values($templateid)");
}
ExecuteQuery('INSERT INTO `sp_templates` VALUES ('.$templateid.',\'Заказ на закупку\',\'PurchaseOrder\',\'{header}\n<h1 style=\"font-size: 14pt\">Заказ на закупку № {$purchaseorder_no}</h1>\n<hr>\n<table border=\"0\" style=\"font-size: 9pt\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$vendor_vendorname}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}</span></td>\n</tr>\n</table>\n{/header}\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n<tr style=\"text-align: center; font-weight: bold\">\n<td width=\"30\">№</td>\n<td width=\"200\">Товар</td>\n<td width=\"60\" colspan=\"2\">Количество</td>\n<td width=\"60\">Цена</td>\n<td width=\"60\">Сумма</td>\n</tr>\n{/table_head}\n{table_row}\n<tr>\n<td width=\"30\">{$productNumber}</td>\n<td width=\"200\">{$productName}</td>\n<td width=\"30\" style=\"text-align: right\">{$productQuantityInt}</td>\n<td width=\"30\">{$productUnits}</td>\n<td width=\"60\" style=\"text-align: right\">{$productPrice}</td>\n<td width=\"60\" style=\"text-align: right\">{$productNetTotal}</td>\n</tr>\n{/table_row}\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n<tr>\n<td width=\"350\" style=\"text-align: right\">Итого:</td>\n<td width=\"60\" style=\"text-align: right\">{$summaryNetTotal}</td>\n</tr>\n<tr>\n<td width=\"350\" style=\"text-align: right\">Сумма НДС:</td>\n<td width=\"60\" style=\"text-align: right\">{$summaryTax}</td>\n</tr>\n</table>\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n{/summary}\n{ending}\n{/ending}\',50,0,\'P\')');

$migrationlog->debug("\n\nDB Changes from 5.3.0-201112 to 5.3.0-201207  -------- Ends \n\n");

?>