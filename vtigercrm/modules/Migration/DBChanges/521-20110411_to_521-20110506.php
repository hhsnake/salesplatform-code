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

$migrationlog->debug("\n\nDB Changes from 5.2.1-20110411 to 5.2.1-20110506 -------- Starts \n\n");

require_once 'include/utils/CommonUtils.php';
global $adb;

ExecuteQuery("CREATE TABLE `sp_templates` (
  `templateid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `template` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `header_size` mediumint(8) NOT NULL DEFAULT '0',
  `footer_size` mediumint(8) NOT NULL DEFAULT '0',
  `page_orientation` char(1) COLLATE utf8_unicode_ci DEFAULT 'P',
  PRIMARY KEY (`templateid`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

ExecuteQuery('INSERT INTO `sp_templates` VALUES (19,\'Счет-фактура\',\'SalesOrder\',\'{header}\n\n<p style=\"font-size: 6pt; text-align: right; margin: 0; padding: 0\">Приложение №1<br/>\nк Правилам ведения журналов учета полученных и выставленных счетов-фактур,<br/>\nкниг покупок и книг продаж при расчетах по налогу на добавленную стоимость,<br/>\nутвержденным постановлением Правительства Российской Федерации от 2 декабря 2000 г. N 914<br/>\n(в редакции постановлений Правительства Российской Федерации от 15 марта 2001 г. N 189,<br/>\nот 27 июля 2002 г. N 575, от 16 февраля 2004 г. N 84, от 11 мая 2006 г. N 283, от 26 мая 2009 г. N 451)<br/>\n</p>\n<h1 style=\"font-size: 12pt; margin: 0; padding: 0\">Счет-фактура № {$salesorder_factura_no} от {$salesorder_factura_date} </h1>\n<p style=\"font-size: 8pt; margin: 0; padding: 0\">Продавец: {$orgName} <br/> \nАдрес: {$orgBillingAddress} <br/> \nИНН/КПП продавца: {$orgInn}/{$orgKpp} <br/> \nГрузоотправитель и его адрес: он же <br/> \nГрузополучатель и его адрес: {$account_accountname}, {$billingAddress} <br/> \nК платежно-расчетному документу № {$salesorder_payment_no} от {$salesorder_payment_date} <br/> \nПокупатель: {$account_accountname} <br/> \nАдрес:	{$billingAddress} <br/> \nИНН / КПП покупателя: {$account_inn}/{$account_kpp}<br/> </p>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center\">\n	<td width=\"200\" valign=\"middle\">Наименование товара (описание<br/>выполненных работ, оказанных услуг),<br/>имущественного права</td>\n	<td width=\"37\" valign=\"middle\">Единица<br/>изме-<br/>рения</td>\n	<td width=\"55\" valign=\"middle\">Коли-<br/>чество</td>\n	<td width=\"55\" valign=\"middle\">Цена (тариф)<br/>за единицу<br/>измерения</td>\n	<td width=\"80\" valign=\"middle\">Стоимость товаров (работ,<br/>услуг),<br/>имущественных<br/>прав, всего без<br/>налога</td>\n	<td width=\"35\" valign=\"middle\">В том<br/>числе<br/>акциз</td>\n	<td width=\"55\" valign=\"middle\">Налоговая<br/>ставка</td>\n	<td width=\"55\" valign=\"middle\">Сумма<br/>налога</td>\n	<td width=\"80\" valign=\"middle\">Стоимость товаров (работ,<br/>услуг),<br/>имущественных<br/>прав, всего с<br/>учетом налога</td>\n	<td width=\"55\" valign=\"middle\">Страна<br/>происхож-<br/>дения</td>\n	<td width=\"75\" valign=\"middle\">Номер<br/>таможенной<br/>декларации</td>\n	</tr>\n    <tr style=\"text-align: center\">\n	<td width=\"200\">1</td>\n	<td width=\"37\">2</td>\n	<td width=\"55\">3</td>\n	<td width=\"55\">4</td>\n	<td width=\"80\">5</td>\n	<td width=\"35\">6</td>\n	<td width=\"55\">7</td>\n	<td width=\"55\">8</td>\n	<td width=\"80\">9</td>\n	<td width=\"55\">10</td>\n	<td width=\"75\">11</td>\n    </tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"200\" style=\"padding: 3px\">{$productName}</td>\n	<td width=\"37\" style=\"text-align: center;padding: 3px\">{$productUnits}</td>\n	<td width=\"55\" style=\"text-align: right;padding: 3px\">{$productQuantity}</td>\n	<td width=\"55\" style=\"text-align: right;padding: 3px\">{$productPrice}</td>\n	<td width=\"80\" style=\"text-align: right;padding: 3px\">{$productNetTotal}</td>\n	<td width=\"35\" style=\"text-align: right;padding: 3px\">--</td>\n	<td width=\"55\" style=\"text-align: center;padding: 3px\">{$productTaxPercent}%</td>\n	<td width=\"55\" style=\"text-align: right;padding: 3px\">{$productTax}</td>\n	<td width=\"80\" style=\"text-align: right;padding: 3px\">{$productTotal}</td>\n	<td width=\"55\" style=\"padding-left: 3px\">{$manufCountry}</td>\n	<td width=\"75\" style=\"padding: 3px\">{$customsId}</td>\n    </tr>\n{/table_row}\n\n{summary}\n    <tr>\n	<td width=\"517\" colspan=7><span style=\"font-weight: bold\">Всего к оплате</span></td>\n	<td width=\"55\" style=\"text-align: right\">{$summaryTax}</td>\n	<td width=\"80\" style=\"text-align: right\">{$summaryGrandTotal}</td>\n    </tr>\n</table>\n{/summary}\n\n{ending}\n<p></p>\n<table border=\"0\" style=\"font-size: 8pt\">\n<tr>\n    <td width=\"200\" style=\"text-align: right\">Руководитель организации </td>\n    <td width=\"80\" style=\"text-align: center\"> __________ </td>\n    <td width=\"80\" style=\"text-align: center\"> <span style=\"text-decoration: underline\">{$orgDirector}</span></td>\n    <td style=\"text-align: right\">Главный бухгалтер </td>\n    <td width=\"80\" style=\"text-align: center\">  __________ </td>\n    <td width=\"80\" style=\"text-align: center\"> <span style=\"text-decoration: underline\">{$orgBookkeeper}</span></td>\n</tr>\n<tr style=\"font-size: 6pt; text-align: center\">\n    <td width=\"200\"></td><td width=\"80\"> (подпись) </td><td width=\"80\"> (ФИО)</td><td></td><td width=\"80\"> (подпись)</td><td width=\"80\"> (ФИО)</td> \n</tr>\n<tr>\n<td colspan=\"6\"><p></p></td>\n</tr>\n<tr>\n    <td width=\"200\" style=\"text-align: right\">Индивидуальный предприниматель </td>\n    <td width=\"80\" style=\"text-align: center\"> __________ </td>\n    <td width=\"80\" style=\"text-align: center\"> <span style=\"text-decoration: underline\">{$orgEntrepreneur}</span> </td>\n    <td colspan=\"3\" style=\"text-align: center\"> <span style=\"text-decoration: underline\">{$orgEntrepreneurreg}</span> </td>\n</tr>\n<tr style=\"font-size: 6pt; text-align: center\">\n    <td width=\"200\"></td><td width=\"80\"> (подпись) </td><td width=\"80\"> (ФИО) </td><td colspan=\"3\"> (реквизиты свидетельства о государственной <br/>регистрации индивидуального предпринимателя)</td> \n</tr>\n</table>\n<p></p>\n<p style=\"font-size: 6pt\">Примечание. Первый  экземпляр  -  покупателю,  второй   экземпляр - продавцу</p>\n{/ending}\',85,50,\'L\'),(20,\'Счет\',\'Invoice\',\'{header}\n\n<table border=\"1\" cellpadding=\"2\">\n<tr>\n<td colspan=\"2\" rowspan=\"2\" width=\"280\">{$orgBankName}<br/><span style=\"font-size: 8pt\">Банк получателя</span></td>\n<td width=\"50\">БИК</td>\n<td rowspan=\"2\" width=\"200\">{$orgBankId}<br/>{$orgCorrAccount}</td>\n</tr>\n<tr>\n<td width=\"50\">Сч. №</td>\n</tr>\n<tr>\n<td width=\"140\">ИНН {$orgInn}</td><td width=\"140\">КПП {$orgKpp}</td><td rowspan=\"2\" width=\"50\">Сч. №</td><td rowspan=\"2\" width=\"200\">{$orgBankAccount}</td>\n</tr>\n<tr>\n<td colspan=\"2\" width=\"280\">{$orgName}<br/><span style=\"font-size: 8pt\">Получатель</span></td>\n</tr>\n</table>\n\n<h1>Счет на оплату № {$invoice_no} от {$invoice_invoicedate}</h1>\n<hr size=\"2\">\n<table border=\"0\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}, ИНН {$orgInn}, КПП {$orgKpp},<br/>{$orgBillingAddress}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$account_accountname}, {$billingAddress}</span></td>\n</tr>\n</table>\n\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\">№</td>\n	<td width=\"260\">Товары (работы, услуги)</td>\n	<td width=\"70\">Кол-во</td>\n	<td width=\"30\">Ед.</td>\n	<td width=\"70\">Цена</td>\n	<td width=\"70\">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\">{$productNumber}</td>\n	<td width=\"260\">{$productName}</td>\n	<td width=\"70\">{$productUnits}</td>\n	<td width=\"30\" style=\"text-align: right\">{$productQuantity}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Итого:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Всего к оплате:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryGrandTotal}</td>\n    </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <p>Руководитель предприятия  __________ ( {$orgDirector} ) <br/>\n    <br/>\n    Главный бухгалтер  __________ ( {$orgBookkeeper} )\n    </p>\n{/ending}\',85,50,\'P\'),(21,\'Накладная\',\'SalesOrder\',\'{header}\n<h1 style=\"font-size: 14pt\">Расходная накладная № {$salesorder_no} от {$salesorder_factura_date} </h1>\n<hr>\n<table border=\"0\" style=\"font-size: 9pt\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$account_accountname}</span></td>\n</tr>\n</table>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\" rowspan=\"2\">№</td>\n	<td width=\"200\" rowspan=\"2\">Товар</td>\n	<td width=\"50\" rowspan=\"2\" colspan=\"2\">Мест</td>\n	<td width=\"60\" rowspan=\"2\" colspan=\"2\">Количество</td>\n	<td width=\"60\" rowspan=\"2\">Цена</td>\n	<td width=\"60\" rowspan=\"2\">Сумма</td>\n	<td width=\"70\">Номер ГТД</td>\n    </tr>\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"70\">Страна<br/>происхождения</td>\n    </tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\" rowspan=\"2\">{$productNumber}</td>\n	<td width=\"200\" rowspan=\"2\">{$productName}</td>\n	<td width=\"25\" rowspan=\"2\"></td>\n	<td width=\"25\" rowspan=\"2\">шт.</td>\n	<td width=\"30\" rowspan=\"2\" style=\"text-align: right\">{$productQuantityInt}</td>\n	<td width=\"30\" rowspan=\"2\">{$productUnits}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productNetTotal}</td>\n	<td width=\"70\">{$customsId}</td>\n    </tr>\n    <tr>\n	<td width=\"70\">{$manufCountry}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Итого:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <table border=\"0\">\n    <tr>\n	<td>Отпустил  __________ </td><td>Получил  __________ </td>\n    </tr>\n    </table>\n{/ending}\n\',50,0,\'P\'),(22,\'Предложение\',\'Quotes\',\'\n{header}\n\n<p style=\"font-weight: bold\">\n{$orgName}<br/>\nИНН {$orgInn}<br/>\nКПП {$orgKpp}<br/>\n{$orgBillingAddress}<br/>\nТел.: {$orgPhone}<br/>\nФакс: {$orgFax}<br/>\n{$orgWebsite}\n</p>\n\n<h1>Коммерческое предложение № {$quote_no}</h1>\n<p>Действительно до: {$quote_validtill}</p>\n<hr size=\"2\">\n\n<p style=\"font-weight: bold\">\n{$account_accountname}<br/>\n{$billingAddress}\n</p>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\">№</td>\n	<td width=\"260\">Товары (работы, услуги)</td>\n	<td width=\"70\">Кол-во</td>\n	<td width=\"30\">Ед.</td>\n	<td width=\"70\">Цена</td>\n	<td width=\"70\">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\">{$productNumber}</td>\n	<td width=\"260\">{$productName}</td>\n	<td width=\"70\">{$productUnits}</td>\n	<td width=\"30\" style=\"text-align: right\">{$productQuantity}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\">\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Итого:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p style=\"font-weight: bold\">\nВсего: {$summaryGrandTotal} руб. ( {$summaryGrandTotalLiteral} )\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <p>Руководитель предприятия  __________ ( {$orgDirector} ) <br/>\n    </p>\n{/ending}\n\',85,0,\'P\')');

ExecuteQuery("alter table `vtiger_salesorder` add `factura_no` varchar(100) default NULL");
ExecuteQuery("alter table `vtiger_salesorder` add `factura_date` date default NULL");
ExecuteQuery("alter table `vtiger_salesorder` add `payment_no` varchar(100) default NULL");
ExecuteQuery("alter table `vtiger_salesorder` add `payment_date` date default NULL");

ExecuteQuery("update vtiger_blocks set `sequence`=`sequence`+1 where tabid=(select tabid from vtiger_tab where name='SalesOrder') and `sequence`>1");

$query=$adb->pquery("select tabid from vtiger_tab where name='SalesOrder'",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows>0){
    $salesorder_tabid=$adb->query_result($query,0,'tabid');

    $max_block_id = $adb->getUniqueID("vtiger_blocks");

    ExecuteQuery("insert into vtiger_blocks values($max_block_id, $salesorder_tabid,
    	    'Facture Invoice', 2, 0, 0, 0, 0, 0, 1, 0)");

    ExecuteQuery("insert into vtiger_field(tabid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, 
    maximumlength, `sequence`, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) 

    values ($salesorder_tabid, 'factura_no', 'vtiger_salesorder', 1, 1, 'factura_no', 'Factura No',
    1, 2, 0, 100, 1, $max_block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL),
    
    ($salesorder_tabid, 'factura_date', 'vtiger_salesorder', 1, 5, 'factura_date', 'Factura Date',
    1, 2, 0, 100, 2, $max_block_id, 1, 'D~O', 3, NULL, 'BAS', 0, NULL),
    
    ($salesorder_tabid, 'payment_no', 'vtiger_salesorder', 1, 1, 'payment_no', 'Payment No',
    1, 2, 0, 100, 3, $max_block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL),
    
    ($salesorder_tabid, 'payment_date', 'vtiger_salesorder', 1, 5, 'payment_date', 'Payment Date',
    1, 2, 0, 100, 4, $max_block_id, 1, 'D~O', 3, NULL, 'BAS', 0, NULL)");
} else {
    $migrationlog->debug("Query Failed ==> $query \n");
}

    

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

	    ExecuteQuery("insert into vtiger_field(tabid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, 
    maximumlength, `sequence`, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) 

    values ($account_tabid, 'inn', 'vtiger_account', 1, 1, 'inn', 'INN',
    1, 2, 0, 30, $start_sequence, $block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL),

    ($account_tabid, 'kpp', 'vtiger_account', 1, 1, 'kpp', 'KPP',
    1, 2, 0, 30, " . ($start_sequence+1) . ", $block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL)");
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

	    ExecuteQuery("insert into vtiger_field(tabid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, 
    maximumlength, `sequence`, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) 

    values ($product_tabid, 'manuf_country', 'vtiger_products', 1, 1, 'manuf_country', 'Manuf. Country',
    1, 2, 0, 100, $start_sequence, $block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL),

    ($product_tabid, 'customs_id', 'vtiger_products', 1, 1, 'customs_id', 'Customs ID',
    1, 2, 0, 100, " . ($start_sequence+1) . ", $block_id, 1, 'V~O', 3, NULL, 'BAS', 0, NULL)");
	} else {
	    $migrationlog->debug("Query Failed ==> $query3 \n");
	}
    } else {
	$migrationlog->debug("Query Failed ==> $query2 \n");
    }
} else {
    $migrationlog->debug("Query Failed ==> $query \n");
}
    
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
        ExecuteQuery("insert into vtiger_tab(tabid,name,presence,tabsequence,tablabel,customized,ownedby,isentitytype) values ($pdftemplatestab,'SPPDFTemplates',0,$seq,'PDF Templates',0,1,0)");
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

ExecuteQuery("insert into vtiger_moduleowners values($pdftemplatestab,1)");

$migrationlog->debug("\n\nDB Changes from 5.2.1-20110411 to 5.2.1-20110506 -------- Ends \n\n");
?>
