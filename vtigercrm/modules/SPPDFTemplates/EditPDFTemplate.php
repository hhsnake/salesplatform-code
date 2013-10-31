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
require_once('include/utils/utils.php');

global $app_strings;
global $mod_strings;
global $app_list_strings;
global $adb;
global $upload_maxsize;
global $theme,$default_charset;
global $current_language;
global $site_URL;
    
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;

if(isset($_REQUEST['templateid']) && $_REQUEST['templateid']!='')
{
  	$templateid = $_REQUEST['templateid'];
  	
  	$sql = "SELECT * FROM sp_templates WHERE sp_templates.templateid=?";
  	$result = $adb->pquery($sql, array($templateid));
  	$templateResult = $adb->fetch_array($result);

  	$select_module = $templateResult["module"];
}
else
{
    $templateid = "";
    
    if (isset($_REQUEST["return_module"]) && $_REQUEST["return_module"] != "") 
       $select_module = $_REQUEST["return_module"]; 
    else 
       $select_module = "";
       
    if (isset($_REQUEST["template"]))
    {
       $template_content = $_REQUEST["template"];
       $templateResult["template"] = $template_content;
    }
}

if(isset($_REQUEST["isDuplicate"]) && $_REQUEST["isDuplicate"]=="true")
{
  $smarty->assign("NAME", "");
  $smarty->assign("DUPLICATE_NAME", $templateResult["name"]);
}
else
{
  $smarty->assign("NAME", $templateResult["name"]);
}  

if (!isset($_REQUEST["isDuplicate"]) OR (isset($_REQUEST["isDuplicate"]) && $_REQUEST["isDuplicate"] != "true")) $smarty->assign("SAVETEMPLATEID", $templateid);
if ($templateid!="")
  $smarty->assign("EMODE", "edit");  

$smarty->assign("TEMPLATEID", $templateid);
$smarty->assign("MODULENAME", getTranslatedString($select_module));
$smarty->assign("SELECTMODULE", $select_module);
$smarty->assign("BODY", $templateResult["template"]);
$smarty->assign("HEADER_SIZE", $templateResult["header_size"]);
$smarty->assign("FOOTER_SIZE", $templateResult["footer_size"]);
$smarty->assign("PAGE_ORIENTATION", $templateResult["page_orientation"]);

$smarty->assign("MOD",$mod_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("PARENTTAB", getParentTab());


$Modulenames = Array(''=>$mod_strings["LBL_PLS_SELECT"]);
$sql = "SELECT tabid, name FROM vtiger_tab WHERE name IN ('SalesOrder', 'Invoice', 'Quotes', 'HelpDesk', 'Act', 'Consignment', 'PurchaseOrder', 'Potentials', 'SPPayments') ORDER BY name ASC";
$result = $adb->query($sql);
while($row = $adb->fetchByAssoc($result)){
  $Modulenames[$row['name']] = getTranslatedString($row['name']);
  $ModuleIDS[$row['name']] = $row['tabid'];
} 

$smarty->assign("MODULENAMES",$Modulenames);

$orientations['P'] = getTranslatedString('Portrait');
$orientations['L'] = getTranslatedString('Landscape');
$smarty->assign("PAGE_ORIENTATIONS",$orientations);

if ($templateid != "" || $select_module!="")
{
    $smarty->assign("SELECT_MODULE_FIELD",$SelectModuleFields[$select_module]);
}

$smarty->display('modules/SPPDFTemplates/EditPDFTemplate.tpl');

?>
