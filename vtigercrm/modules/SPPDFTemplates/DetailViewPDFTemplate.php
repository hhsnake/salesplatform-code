<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/
 
require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/database/PearDatabase.php');
global $adb;
global $log;
global $mod_strings;
global $app_strings;
global $current_language;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_smarty;

$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("MOD", $mod_strings);

$smarty->assign("MODULE", 'Tools');
$smarty->assign("IMAGE_PATH", $image_path);

if(isset($_REQUEST['templateid']) && $_REQUEST['templateid']!='')
{
  	$tempid = $_REQUEST['templateid'];

  	$sql = "SELECT * FROM sp_templates WHERE sp_templates.templateid=?";
  	$result = $adb->pquery($sql, array($tempid));
  	$pdftemplateResult = $adb->fetch_array($result);

    $smarty->assign("NAME", $pdftemplateResult["name"]);
    $smarty->assign("TEMPLATEID", $pdftemplateResult["templateid"]);
    $smarty->assign("MODULENAME", getTranslatedString($pdftemplateResult["module"]));
    $smarty->assign("BODY", decode_html($pdftemplateResult["template"]));
    $smarty->assign("HEADER_SIZE", $pdftemplateResult["header_size"]);
    $smarty->assign("FOOTER_SIZE", $pdftemplateResult["footer_size"]);
    $smarty->assign("PAGE_ORIENTATION", $pdftemplateResult["page_orientation"]);
}

$smarty->display("modules/SPPDFTemplates/DetailViewPDFTemplate.tpl");

?>
