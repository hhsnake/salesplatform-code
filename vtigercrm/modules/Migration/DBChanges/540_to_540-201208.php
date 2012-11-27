<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

/**
 * @author igor.struchkov@salesplatform.ru
 */

require_once 'include/utils/utils.php';

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.4.0 to 5.4.0-201208 -------- Starts \n\n");

require_once 'include/utils/CommonUtils.php';
//global $adb;

changeDBCollation('utf8_general_ci');

// 5.2.1-20110411

$query=$adb->pquery("select * from vtiger_language where prefix='ru_ru'",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows == 0){
    ExecuteQuery("insert into vtiger_language(name,prefix,label,lastupdated,sequence,isdefault,active) 
		  values('Русский','ru_ru','RU Русский',Now(),NULL,1,1)");
    ExecuteQuery("update vtiger_language_seq set id=LAST_INSERT_ID(id+1)");

}

ExecuteQuery("alter table `vtiger_organizationdetails` add `inn` varchar(30) default ''");
ExecuteQuery("alter table `vtiger_organizationdetails` add `kpp` varchar(30) default ''");

// 5.2.1-20110506
ExecuteQuery("CREATE TABLE `sp_templates` (
  `templateid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `module` varchar(64) NOT NULL,
  `template` mediumtext NOT NULL,
  `header_size` mediumint(8) NOT NULL DEFAULT '0',
  `footer_size` mediumint(8) NOT NULL DEFAULT '0',
  `page_orientation` char(1) DEFAULT 'P',
  PRIMARY KEY (`templateid`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8");

$adb->getUniqueID("sp_templates");
$adb->getUniqueID("sp_templates");
$adb->getUniqueID("sp_templates");
ExecuteQuery('INSERT INTO `sp_templates` VALUES (1,\'Счет\',\'Invoice\',\'{header}\n\n<p style="font-weight: bold; text-decoration: underline">{$orgName}</p>\n\n<p style="font-weight: bold">Адрес: {$orgBillingAddress}, тел.: {$orgPhone}</p>\n\n<div style="font-weight: bold; text-align: center">Образец заполнения платежного поручения</div>\n\n<table border="1" cellpadding="2">\n<tr>\n  <td width="140">ИНН {$orgInn}</td><td width="140">КПП {$orgKpp}</td><td rowspan="2" width="50"><br/><br/>Сч. №</td><td rowspan="2" width="200"><br/><br/>{$orgBankAccount}</td>\n</tr>\n<tr>\n<td colspan="2" width="280"><span style="font-size: 8pt">Получатель</span><br/>{$orgName}</td>\n</tr>\n<tr>\n<td colspan="2" rowspan="2" width="280"><span style="font-size: 8pt">Банк получателя</span><br/>{$orgBankName}</td>\n<td width="50">БИК</td>\n<td rowspan="2" width="200">{$orgBankId}<br/>{$orgCorrAccount}</td>\n</tr>\n<tr>\n<td width="50">Сч. №</td>\n</tr>\n</table>\n<br/>\n<h1 style="text-align: center">СЧЕТ № {$invoice_no} от {$invoice_invoicedate}</h1>\n<br/><br/>\n<table border="0">\n<tr>\n<td width="100">Плательщик:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>\n</tr>\n<tr>\n<td width="100">Грузополучатель:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>\n</tr>\n</table>\n\n{/header}\n\n{table_head}\n<table border="1" style="font-size: 8pt" cellpadding="2">\n    <tr style="text-align: center; font-weight: bold">\n	<td width="30">№</td>\n      <td width="260">Наименование<br/>товара</td>\n      <td width="65">Единица<br/>изме-<br/>рения</td>\n      <td width="35">Коли-<br/>чество</td>\n	<td width="70">Цена</td>\n	<td width="70">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width="30">{$productNumber}</td>\n      <td width="260">{$productName} {$productComment}</td>\n	<td width="65" style="text-align: center">{$productUnits}</td>\n	<td width="35" style="text-align: right">{$productQuantityInt}</td>\n	<td width="70" style="text-align: right">{$productPrice}</td>\n	<td width="70" style="text-align: right">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<table border="0" style="font-size: 8pt;font-weight: bold">\n    <tr>\n      <td width="460">\n        <table border="0" cellpadding="2">\n          <tr><td width="460" style="text-align: right">Итого:</td></tr>\n          <tr><td width="460" style="text-align: right">Сумма НДС:</td></tr>\n          <tr><td width="460" style="text-align: right">Всего к оплате:</td></tr>\n        </table>\n      </td>\n      <td width="70">\n        <table border="1" cellpadding="2">\n          <tr><td width="70" style="text-align: right">{$summaryNetTotal}</td></tr>\n          <tr><td width="70" style="text-align: right">{$summaryTax}</td></tr>\n          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>\n        </table>\n      </td>\n  </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n<br/>\n    <p>Руководитель предприятия  __________________ ( {$orgDirector} ) <br/>\n    <br/>\n    Главный бухгалтер  __________________ ( {$orgBookkeeper} )\n    </p>\n{/ending}\',110,50,\'P\'),(2,\'Накладная\',\'SalesOrder\',\'{header}\n<h1 style=\"font-size: 14pt\">Расходная накладная № {$salesorder_no}</h1>\n<hr>\n<table border=\"0\" style=\"font-size: 9pt\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$account_accountname}</span></td>\n</tr>\n</table>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\" rowspan=\"2\">№</td>\n	<td width=\"200\" rowspan=\"2\">Товар</td>\n	<td width=\"50\" rowspan=\"2\" colspan=\"2\">Мест</td>\n	<td width=\"60\" rowspan=\"2\" colspan=\"2\">Количество</td>\n	<td width=\"60\" rowspan=\"2\">Цена</td>\n	<td width=\"60\" rowspan=\"2\">Сумма</td>\n	<td width=\"70\">Номер ГТД</td>\n    </tr>\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"70\">Страна<br/>происхождения</td>\n    </tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\" rowspan=\"2\">{$productNumber}</td>\n	<td width=\"200\" rowspan=\"2\">{$productName}</td>\n	<td width=\"25\" rowspan=\"2\"></td>\n	<td width=\"25\" rowspan=\"2\">шт.</td>\n	<td width=\"30\" rowspan=\"2\" style=\"text-align: right\">{$productQuantityInt}</td>\n	<td width=\"30\" rowspan=\"2\">{$productUnits}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productNetTotal}</td>\n	<td width=\"70\">{$customsId}</td>\n    </tr>\n    <tr>\n	<td width=\"70\">{$manufCountry}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Итого:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <table border=\"0\">\n    <tr>\n	<td>Отпустил  __________ </td><td>Получил  __________ </td>\n    </tr>\n    </table>\n{/ending}\n\',50,0,\'P\'),(3,\'Предложение\',\'Quotes\',\'\n{header}\n\n<p style=\"font-weight: bold\">\n{$orgName}<br/>\nИНН {$orgInn}<br/>\nКПП {$orgKpp}<br/>\n{$orgBillingAddress}<br/>\nТел.: {$orgPhone}<br/>\nФакс: {$orgFax}<br/>\n{$orgWebsite}\n</p>\n\n<h1>Коммерческое предложение № {$quote_no}</h1>\n<p>Действительно до: {$quote_validtill}</p>\n<hr size=\"2\">\n\n<p style=\"font-weight: bold\">\n{$account_accountname}<br/>\n{$billingAddress}\n</p>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\">№</td>\n	<td width=\"260\">Товары (работы, услуги)</td>\n	<td width=\"70\">Ед.</td>\n	<td width=\"30\">Кол-во</td>\n	<td width=\"70\">Цена</td>\n	<td width=\"70\">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\">{$productNumber}</td>\n	<td width=\"260\">{$productName}</td>\n	<td width=\"70\">{$productUnits}</td>\n	<td width=\"30\" style=\"text-align: right\">{$productQuantity}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\">\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Итого:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p style=\"font-weight: bold\">\nВсего: {$summaryGrandTotal} руб. ( {$summaryGrandTotalLiteral} )\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <p>Руководитель предприятия  __________ ( {$orgDirector} ) <br/>\n    </p>\n{/ending}\n\',85,0,\'P\')');

ExecuteQuery("alter table `vtiger_account` add `inn` varchar(30) default ''");
ExecuteQuery("alter table `vtiger_account` add `kpp` varchar(30) default ''");

$query=$adb->pquery("select tabid from vtiger_tab where name='Accounts'",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows>0){
    $account_tabid=$adb->query_result($query,0,'tabid');

    $query2=$adb->pquery("select blockid from vtiger_blocks where tabid=$account_tabid and blocklabel='LBL_ACCOUNT_INFORMATION'",array());
    $numOfRows=$adb->num_rows($query2);
    if($numOfRows>0){
	$block_id=$adb->query_result($query2,0,'blockid');

	$query3=$adb->pquery("select (max(`sequence`)+1) as s from vtiger_field where tabid=$account_tabid and block=$block_id",array());
	$numOfRows=$adb->num_rows($query3);
	if($numOfRows>0){
	    $start_sequence=$adb->query_result($query3,0,'s');

	    ExecuteQuery("insert into vtiger_field(tabid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, defaultvalue,
    maximumlength, `sequence`, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo)

    values ($account_tabid, 'inn', 'vtiger_account', 1, 1, 'inn', 'INN',
    1, 2, '', 30, $start_sequence, $block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL),

    ($account_tabid, 'kpp', 'vtiger_account', 1, 1, 'kpp', 'KPP',
    1, 2, '', 30, " . ($start_sequence+1) . ", $block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL)");

	    ExecuteQuery("update vtiger_field_seq set id=LAST_INSERT_ID(id+2)");

	} else {
	    $migrationlog->debug("Query Failed ==> $query3 \n");
	}
    } else {
	$migrationlog->debug("Query Failed ==> $query2 \n");
    }
} else {
    $migrationlog->debug("Query Failed ==> $query \n");
}

ExecuteQuery("alter table `vtiger_products` add `manuf_country` varchar(100) default ''");
ExecuteQuery("alter table `vtiger_products` add `customs_id` varchar(100) default ''");

$query=$adb->pquery("select tabid from vtiger_tab where name='Products'",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows>0){
    $product_tabid=$adb->query_result($query,0,'tabid');

    $query2=$adb->pquery("select blockid from vtiger_blocks where tabid=$product_tabid and blocklabel='LBL_PRODUCT_INFORMATION'",array());
    $numOfRows=$adb->num_rows($query2);
    if($numOfRows>0){
	$block_id=$adb->query_result($query2,0,'blockid');

	$query3=$adb->pquery("select (max(`sequence`)+1) as s from vtiger_field where tabid=$product_tabid and block=$block_id",array());
	$numOfRows=$adb->num_rows($query3);
	if($numOfRows>0){
	    $start_sequence=$adb->query_result($query3,0,'s');

	    ExecuteQuery("insert into vtiger_field(tabid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, defaultvalue,
    maximumlength, `sequence`, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo)

    values ($product_tabid, 'manuf_country', 'vtiger_products', 1, 1, 'manuf_country', 'Manuf. Country',
    1, 2, '', 100, $start_sequence, $block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL),

    ($product_tabid, 'customs_id', 'vtiger_products', 1, 1, 'customs_id', 'Customs ID',
    1, 2, '', 100, " . ($start_sequence+1) . ", $block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL)");

	    ExecuteQuery("update vtiger_field_seq set id=LAST_INSERT_ID(id+2)");

	} else {
	    $migrationlog->debug("Query Failed ==> $query3 \n");
	}
    } else {
	$migrationlog->debug("Query Failed ==> $query2 \n");
    }
} else {
    $migrationlog->debug("Query Failed ==> $query \n");
}

ExecuteQuery("ALTER TABLE `vtiger_organizationdetails` ADD COLUMN `bankaccount` VARCHAR(1024) default ''");
ExecuteQuery("alter table `vtiger_organizationdetails` add `bankname` varchar(1024) default ''");
ExecuteQuery("alter table `vtiger_organizationdetails` add `bankid` varchar(30) default ''");
ExecuteQuery("alter table `vtiger_organizationdetails` add `corraccount` varchar(100) default ''");
ExecuteQuery("alter table `vtiger_organizationdetails` add `director` varchar(100) default ''");
ExecuteQuery("alter table `vtiger_organizationdetails` add `bookkeeper` varchar(100) default ''");
ExecuteQuery("alter table `vtiger_organizationdetails` add `entrepreneur` varchar(100) default ''");
ExecuteQuery("alter table `vtiger_organizationdetails` add `entrepreneurreg` varchar(100) default ''");

$query=$adb->pquery("select max(tabsequence) as s from vtiger_tab",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows>0){
    $seq=$adb->query_result($query,0,'s') + 1;

    $query=$adb->pquery("select max(tabid) as s from vtiger_tab",array());
    $numOfRows=$adb->num_rows($query);
    if($numOfRows>0){
        $pdftemplatestab = $adb->query_result($query,0,'s') + 1;
        ExecuteQuery("insert into vtiger_tab(tabid,name,presence,tabsequence,tablabel,customized,ownedby,isentitytype,parent) values ($pdftemplatestab,'SPPDFTemplates',0,-1,'PDF Templates',0,1,0,'Tools')");
    }else {
        $migrationlog->debug("Query Failed ==> $query \n");
    }
} else {
    $migrationlog->debug("Query Failed ==> $query \n");
}


$query=$adb->pquery("select max(sequence) as s from vtiger_parenttabrel",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows>0){
    $seq=$adb->query_result($query,0,'s') + 1;
    ExecuteQuery("insert into vtiger_parenttabrel(parenttabid,tabid,sequence) values (7,$pdftemplatestab,$seq)");
} else {
    $migrationlog->debug("Query Failed ==> $query \n");
}

// 5.2.1-20110624
$query=$adb->pquery("select * from vtiger_links where linklabel = 'LBL_CHECK_STATUS'",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows > 0){
    ExecuteQuery("update vtiger_links set linkurl = 'javascript:SMSNotifier.checkstatus(\\'tbl".'$WRAPPER_NAME$'."\\', ".'$RECORD$'.")' where linklabel = 'LBL_CHECK_STATUS'");
}

// 5.2.1-20110824
ExecuteQuery("alter table `vtiger_systems` add `server_tls` varchar(20) default NULL");
ExecuteQuery("alter table `vtiger_systems` add `from_name` varchar(200) DEFAULT ''");
ExecuteQuery("alter table `vtiger_systems` add `use_sendmail` varchar(5) DEFAULT 'false'");
ExecuteQuery("update vtiger_systems set use_sendmail='false'");

// 5.3.0-201207
// Add PriceBooks and SalesOrders to the customer portal
$priceBooksTabId = getTabid('PriceBooks');
$salesOrderTabId = getTabid('SalesOrder');
$result = $adb->pquery('select max(sequence)+1 as seq from vtiger_customerportal_tabs', array());
$seq =  $adb->query_result($result, 0, 'seq');
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
ExecuteQuery("insert into vtiger_tab(tabid,name,presence,tabsequence,tablabel,customized,ownedby,isentitytype,parent) values ($maxtabid,'SPModulePickList',0,-1,'SPModulePickList',0,0,0,null)");

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


$migrationlog->debug("\n\nDB Changes from 5.4.0 to 5.4.0-201208 -------- Ends \n\n");
?>