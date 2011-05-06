<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

require_once('include/utils/utils.php');
global $adb;

$templatename = vtlib_purify($_REQUEST["templatename"]);
$modulename = from_html($_REQUEST["modulename"]);
$templateid = vtlib_purify($_REQUEST["templateid"]);
$body = fck_from_html($_REQUEST["body"]);
$header_size = fck_from_html($_REQUEST["header_size"]);
$footer_size = fck_from_html($_REQUEST["footer_size"]);
$page_orientation = fck_from_html($_REQUEST["page_orientation"]);

if(isset($templateid) && $templateid !='')
{
	$sql = "update sp_templates set name =?, module =?, template =?, header_size =?, footer_size =?, page_orientation =? where templateid =?";
	$params = array($templatename, $modulename, $body, $header_size, $footer_size, $page_orientation, $templateid);
	$adb->pquery($sql, $params);

}
else
{
	$templateid = $adb->getUniqueID('sp_templates');
	$sql2 = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,templateid) values (?,?,?,?,?,?,?)";
	$params2 = array($templatename, $modulename, $body, $header_size, $footer_size, $page_orientation, $templateid);
	$adb->pquery($sql2, $params2);
}

header("Location:index.php?module=SPPDFTemplates&action=DetailViewPDFTemplate&parenttab=Tools&templateid=".$templateid);

exit;
?>
