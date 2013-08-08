<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once("include/Zend/Json.php");
@include("../PortalConfig.php");
if(!isset($_SESSION['customer_id']) || $_SESSION['customer_id'] == '')
{
	@header("Location: $Authenticate_Path/login.php");
	exit;
}
include("PriceBooks/index.html");
include("PriceBooks/Utils.php");

global $result;
$username = $_SESSION['customer_name'];
$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];

$onlymine=false;

if($_REQUEST['fun'] == '' || $_REQUEST['fun'] == 'home')
{
	// This is an archaic parameter list
	$match_condition = "";
	$where = "";
	$params = Array(Array('id'=>"$customerid", 'sessionid'=>"$sessionid", 'user_name' => "$username", 'onlymine' => $onlymine, 'where' => "$where", 'match' => "$match_condition"));	
	$result = $client->call('get_pricebooks_list', $params, $Server_Path, $Server_Path);
	include("PriceBooksList.php");
}
elseif($_REQUEST['fun'] == 'detail')
{	
	
	$pricebookid = Zend_Json::decode($_REQUEST['pricebookid']);
	$block = 'PriceBooks';
	include("PriceBookDetail.php");
}

echo '</table></td></tr></table></td></tr></table>';
?>
