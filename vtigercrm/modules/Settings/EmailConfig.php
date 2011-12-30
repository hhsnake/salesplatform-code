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
// SalesPlatform.ru begin
require_once 'include/SalesPlatform/NetIDNA/idna_convert.class.php';
// SalesPlatform.ru end
global $mod_strings;
global $app_strings;
global $app_list_strings;

//Display the mail send status
$smarty = new vtigerCRM_Smarty;
if($_REQUEST['mail_error'] != '') {
	require_once("modules/Emails/mail.php");
	$error_msg = strip_tags(parseEmailErrorString($_REQUEST['mail_error']));
	$error_msg = $mod_strings['LBL_MAILSENDERROR'];
	$smarty->assign("ERROR_MSG",$mod_strings['LBL_TESTMAILSTATUS'].' <b><font class="warning">'.$error_msg.'</font></b>');
}

global $adb;
global $theme;
// SalesPlatform.ru begin
$idn = new idna_convert();
// SalesPlatform.ru end
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$sql="select * from vtiger_systems where server_type = ?";
$result = $adb->pquery($sql, array('email'));
$mail_server = $adb->query_result($result,0,'server');
// SalesPlatform.ru begin
//$mail_server_username = $adb->query_result($result,0,'server_username');
$mail_server_username = $idn->decode( $adb->query_result($result,0,'server_username') );
// SalesPlatform.ru end
$mail_server_password = $adb->query_result($result,0,'server_password');
$smtp_auth = $adb->query_result($result,0,'smtp_auth');
// SalesPlatform.ru begin
//$from_email_field = $adb->query_result($result, 0, 'from_email_field');
$from_email_field = $idn->decode( $adb->query_result($result, 0, 'from_email_field') );
// SalesPlatform.ru end
$servername = vtlib_purify($_REQUEST['server_name']);
$username = vtlib_purify($_REQUEST['server_user']);
// SalesPlatform.ru begin
$from_name = $adb->query_result($result, 0, 'from_name');
$mail_server_port = $adb->query_result($result,0,'server_port');
$mail_server_tls = $adb->query_result($result,0,'server_tls');
$use_sendmail = $adb->query_result($result,0,'use_sendmail');
// SalesPlatform.ru end

if(!empty($servername)) {
    $validInput = validateServerName($servername);
if(! $validInput) {
    $servername = '';
 }
    $smarty->assign("MAILSERVER",$servername);
} elseif(isset($mail_server)) {
    $smarty->assign("MAILSERVER",$mail_server);
}

if(!empty($username)) {
    $validInput = validateEmailId($username);
if(! $validInput) {
    $username = '';
 }
	$smarty->assign("USERNAME",$username);
} elseif(isset($mail_server_username)) {
	$smarty->assign("USERNAME",$mail_server_username);
}

if (isset($mail_server_password))
	$smarty->assign("PASSWORD",$mail_server_password);
if(isset($_REQUEST['from_email_field'])){

	$smarty->assign("FROM_EMAIL_FIELD",vtlib_purify($_REQUEST['from_email_field']));
} elseif(isset($from_email_field)) {
	$smarty->assign("FROM_EMAIL_FIELD",$from_email_field);
}
// SalesPlatform.ru begin
if(isset($_REQUEST['from_name'])){

	$smarty->assign("FROM_NAME",vtlib_purify($_REQUEST['from_name']));
} elseif(isset($from_name)) {
	$smarty->assign("FROM_NAME",$from_name);
}
// SalesPlatform.ru end
if(isset($_REQUEST['auth_check']))
{
	if($_REQUEST['auth_check'] == 'on')
                $smarty->assign("SMTP_AUTH",'checked');
        else
                $smarty->assign("SMTP_AUTH",'');
}
elseif (isset($smtp_auth))
{
	if($smtp_auth == 'true')
		$smarty->assign("SMTP_AUTH",'checked');
	else
		$smarty->assign("SMTP_AUTH",'');
}

// SalesPlatform.ru begin
if(isset($_REQUEST['port'])){

	$smarty->assign("MAILSERVERPORT",vtlib_purify($_REQUEST['port']));
} elseif(isset($mail_server_port)) {
	$smarty->assign("MAILSERVERPORT",$mail_server_port);
}

if(isset($_REQUEST['server_tls']))
    $server_tls = vtlib_purify($_REQUEST['server_tls']);
else
    $server_tls = $mail_server_tls;

if($server_tls != 'tls' && $server_tls != 'ssl')
    $smarty->assign("NOTLS",'checked');
else
    $smarty->assign("NOTLS",'');

if($server_tls == 'tls')
    $smarty->assign("TLS",'checked');
else
    $smarty->assign("TLS",'');

if($server_tls == 'ssl')
    $smarty->assign("SSL",'checked');
else
    $smarty->assign("SSL",'');

if(isset($_REQUEST['use_sendmail']))
{
	if($_REQUEST['use_sendmail'] == 'on')
                $smarty->assign("USE_SENDMAIL",'checked');
        else
                $smarty->assign("USE_SENDMAIL",'');
}
elseif (isset($use_sendmail))
{
	if($use_sendmail == 'true')
		$smarty->assign("USE_SENDMAIL",'checked');
	else
		$smarty->assign("USE_SENDMAIL",'');
}

// SalesPlatform.ru end

if(isset($_REQUEST['emailconfig_mode']) && $_REQUEST['emailconfig_mode'] != '')
	$smarty->assign("EMAILCONFIG_MODE",vtlib_purify($_REQUEST['emailconfig_mode']));
else
	$smarty->assign("EMAILCONFIG_MODE",'view');

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display("Settings/EmailConfig.tpl");
?>