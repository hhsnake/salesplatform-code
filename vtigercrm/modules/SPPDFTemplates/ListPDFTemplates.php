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
require_once('include/database/PearDatabase.php');

global $adb;
$sql = "SELECT templateid, name, module 
        FROM sp_templates 
        ORDER BY templateid ASC";
$result = $adb->pquery($sql, array());

$edit="Edit  ";
$del="Del  ";
$bar="  | ";
$cnt=1;

$return_data = Array();
$num_rows = $adb->num_rows($result);


for($i=0;$i < $num_rows; $i++)
{	
  $emailtemplatearray=array();
  $templateid = $adb->query_result($result,$i,'templateid');
  $emailtemplatearray['templateid'] = $templateid;
  $emailtemplatearray['module'] = getTranslatedString($adb->query_result($result,$i,'module'));
  $emailtemplatearray['name'] = "<a href=\"index.php?action=DetailViewPDFTemplate&module=SPPDFTemplates&templateid=".$templateid."&parenttab=Tools\">".$adb->query_result($result,$i,'name')."</a>";
  $emailtemplatearray['edit'] = "<a href=\"index.php?action=EditPDFTemplate&module=SPPDFTemplates&templateid=".$templateid."&parenttab=Tools\">".$app_strings["LBL_EDIT_BUTTON"]."</a> | "
                             ."<a href=\"index.php?action=EditPDFTemplate&module=SPPDFTemplates&templateid=".$templateid."&isDuplicate=true&parenttab=Tools\">".$app_strings["LBL_DUPLICATE_BUTTON"]."</a>";
  
  $return_data []= $emailtemplatearray;	
}

require_once('include/utils/UserInfoUtil.php');
global $app_strings;
global $mod_strings;
global $theme,$default_charset;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
global $current_language;

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("PARENTTAB", getParentTab());
$smarty->assign("IMAGE_PATH",$image_path);

$smarty->assign("PDFTEMPLATES",$return_data);
$smarty->display("modules/SPPDFTemplates/ListPDFTemplates.tpl");


?>
