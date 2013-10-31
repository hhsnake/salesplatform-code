<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;

$smarty = new vtigerCRM_Smarty;
//error handling
if(isset($_REQUEST['flag']) && $_REQUEST['flag'] != '')
{
	$flag = vtlib_purify($_REQUEST['flag']);
	switch($flag)
	{
		case 1:
			$smarty->assign("ERRORFLAG","<font color='red'><B>".$mod_strings['LOGO_ERROR']."</B></font>");;
			break;
		case 2:
			$smarty->assign("ERRORFLAG","<font color='red'><B>".$mod_strings['Error_Message']."<ul><li><font color='red'>".$mod_strings['Invalid_file']."</font><li><font color='red'>".$mod_strings['File_has_no_data']."</font></ul></B></font>");
			break;
		case 3:
			$smarty->assign("ERRORFLAG","<B><font color='red'>".$mod_strings['Sorry'].",".$mod_strings['uploaded_file_exceeds_maximum_limit'].".".$mod_strings['try_file_smaller']."</font></B>");
			break;
		case 4:
			$smarty->assign("ERRORFLAG","<b>".$mod_strings['Problems_in_upload'].". ".$mod_strings['Please_try_again']." </b><br>");
			break;
		case 5:
			$smarty->assign("ERRORFLAG","<font color='red'><B>".$mod_strings['Error_Message']."<ul><li><font color='red'>".$mod_strings['Invalid_image']."</font><li><font color='red'>".$mod_strings['Image_corrupted']."</font></ul></B></font>");
			break;
		default:
			$smarty->assign("ERRORFLAG","");
		
	}
}
global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$sql="select * from vtiger_organizationdetails";
$result = $adb->pquery($sql, array());
$organization_name = str_replace('"','&quot;',$adb->query_result($result,0,'organizationname'));
$organization_address= $adb->query_result($result,0,'address');
$organization_city = $adb->query_result($result,0,'city');
$organization_state = $adb->query_result($result,0,'state');
$organization_code = $adb->query_result($result,0,'code');
$organization_country = $adb->query_result($result,0,'country');
$organization_phone = $adb->query_result($result,0,'phone');
$organization_fax = $adb->query_result($result,0,'fax');
$organization_website = $adb->query_result($result,0,'website');
// SalesPlatform.ru begin
$organization_inn = $adb->query_result($result,0,'inn');
$organization_kpp = $adb->query_result($result,0,'kpp');
$organization_bankaccount = $adb->query_result($result,0,'bankaccount');
$organization_bankname = $adb->query_result($result,0,'bankname');
$organization_bankid = $adb->query_result($result,0,'bankid');
$organization_corraccount = $adb->query_result($result,0,'corraccount');
$organization_director = $adb->query_result($result,0,'director');
$organization_bookkeeper = $adb->query_result($result,0,'bookkeeper');
$organization_entrepreneur = $adb->query_result($result,0,'entrepreneur');
$organization_entrepreneurreg = $adb->query_result($result,0,'entrepreneurreg');
$organization_okpo = $adb->query_result($result,0,'okpo');
// SalesPlatform.ru end
$organization_logoname = $adb->query_result($result,0,'logoname');


if (isset($organization_name))
	$smarty->assign("ORGANIZATIONNAME",$organization_name);
if (isset($organization_address))
	$smarty->assign("ORGANIZATIONADDRESS",$organization_address);
if (isset($organization_city))
	$smarty->assign("ORGANIZATIONCITY",$organization_city);
if (isset($organization_state))
	$smarty->assign("ORGANIZATIONSTATE",$organization_state);
if (isset($organization_code))
	$smarty->assign("ORGANIZATIONCODE",$organization_code);
if (isset($organization_country))
	$smarty->assign("ORGANIZATIONCOUNTRY",$organization_country);
if (isset($organization_phone))
	$smarty->assign("ORGANIZATIONPHONE",$organization_phone);
if (isset($organization_fax))
	$smarty->assign("ORGANIZATIONFAX",$organization_fax);
if (isset($organization_website))
	$smarty->assign("ORGANIZATIONWEBSITE",$organization_website);

// SalesPlatform.ru begin
if (isset($organization_inn))
	$smarty->assign("ORGANIZATIONINN",$organization_inn);
if (isset($organization_kpp))
	$smarty->assign("ORGANIZATIONKPP",$organization_kpp);
if (isset($organization_bankaccount))
	$smarty->assign("ORGANIZATIONBANKACCOUNT",$organization_bankaccount);
if (isset($organization_bankname))
	$smarty->assign("ORGANIZATIONBANKNAME",$organization_bankname);
if (isset($organization_bankid))
	$smarty->assign("ORGANIZATIONBANKID",$organization_bankid);
if (isset($organization_corraccount))
	$smarty->assign("ORGANIZATIONCORRACCOUNT",$organization_corraccount);
if (isset($organization_director))
	$smarty->assign("ORGANIZATIONDIRECTOR",$organization_director);
if (isset($organization_bookkeeper))
	$smarty->assign("ORGANIZATIONBOOKKEEPER",$organization_bookkeeper);
if (isset($organization_entrepreneur))
	$smarty->assign("ORGANIZATIONENTREPRENEUR",$organization_entrepreneur);
if (isset($organization_entrepreneurreg))
	$smarty->assign("ORGANIZATIONENTREPRENEURREG",$organization_entrepreneurreg);
if (isset($organization_okpo)) 
        $smarty->assign("ORGANIZATIONOKPO",$organization_okpo);
// SalesPlatform.ru end

if ($organization_logoname == '') 
	$organization_logoname = vtlib_purify($_REQUEST['prev_name']);
if (isset($organization_logoname)) 
	$smarty->assign("ORGANIZATIONLOGONAME",$organization_logoname);

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display('Settings/EditCompanyInfo.tpl');
?>
