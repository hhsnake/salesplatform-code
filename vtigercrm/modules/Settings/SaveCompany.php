<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once("include/database/PearDatabase.php");

$organization_name= vtlib_purify($_REQUEST['organization_name']);
$org_name= vtlib_purify($_REQUEST['org_name']);
$organization_address= vtlib_purify($_REQUEST['organization_address']);
$organization_city= vtlib_purify($_REQUEST['organization_city']);
$organization_state= vtlib_purify($_REQUEST['organization_state']);
$organization_code= vtlib_purify($_REQUEST['organization_code']);
$organization_country= vtlib_purify($_REQUEST['organization_country']);
$organization_phone= vtlib_purify($_REQUEST['organization_phone']);
$organization_fax= vtlib_purify($_REQUEST['organization_fax']);
$organization_website= vtlib_purify($_REQUEST['organization_website']);
// SalesPlatform.ru begin
$organization_inn= vtlib_purify($_REQUEST['organization_inn']);
$organization_kpp= vtlib_purify($_REQUEST['organization_kpp']);
$organization_bankaccount= vtlib_purify($_REQUEST['organization_bankaccount']);
$organization_bankname= vtlib_purify($_REQUEST['organization_bankname']);
$organization_bankid= vtlib_purify($_REQUEST['organization_bankid']);
$organization_corraccount= vtlib_purify($_REQUEST['organization_corraccount']);
$organization_director= vtlib_purify($_REQUEST['organization_director']);
$organization_bookkeeper= vtlib_purify($_REQUEST['organization_bookkeeper']);
$organization_entrepreneur= vtlib_purify($_REQUEST['organization_entrepreneur']);
$organization_entrepreneurreg= vtlib_purify($_REQUEST['organization_entrepreneurreg']);
$organization_okpo=$_REQUEST['organization_okpo'];
// SalesPlatform.ru end

$sql="select * from vtiger_organizationdetails where organizationname = ?";
$result = $adb->pquery($sql, array($org_name));
$org_name = $adb->query_result($result,0,'organizationname');

if($org_name=='')
{
	$organizationId = $this->db->getUniqueID('vtiger_organizationdetails');
// SalesPlatform.ru begin
	$sql="insert into vtiger_organizationdetails(organization_id,organizationname, address, city, state, code, country, phone, fax, website, inn, kpp, bankaccount, bankname, bankid, corraccount, director, bookkeeper, entrepreneur, entrepreneurreg, okpo) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	//$sql="insert into vtiger_organizationdetails(organization_id,organizationname, address, city, state, code, country, phone, fax, website) values(?,?,?,?,?,?,?,?,?)";
	$params = array($organizationId, $organization_name, $organization_address, $organization_city, $organization_state, $organization_code, $organization_country, $organization_phone, $organization_fax, $organization_website, $organization_inn, $organization_kpp, $organization_bankaccount, $organization_bankname, $organization_bankid, $organization_corraccount, $organization_director, $organization_bookkeeper, $organization_entrepreneur, $organization_entrepreneurreg,$organization_okpo);
	//$params = array($organizationId, $organization_name, $organization_address, $organization_city, $organization_state, $organization_code, $organization_country, $organization_phone, $organization_fax, $organization_website);
// SalesPlatform.ru end
}
else
{
// SalesPlatform.ru begin
	$sql="update vtiger_organizationdetails set organizationname = ?, address = ?, city = ?, state = ?,  code = ?, country = ?,  phone = ?,  fax = ?,  website = ?, inn = ?, kpp = ?, bankaccount = ?, bankname = ?, bankid = ?, corraccount = ?, director = ?, bookkeeper = ?, entrepreneur = ?, entrepreneurreg = ?, okpo = ? where organizationname = ?";
	$params = array($organization_name, $organization_address, $organization_city, $organization_state, $organization_code, $organization_country, $organization_phone, $organization_fax, $organization_website, $organization_inn, $organization_kpp, $organization_bankaccount, $organization_bankname, $organization_bankid, $organization_corraccount, $organization_director, $organization_bookkeeper, $organization_entrepreneur, $organization_entrepreneurreg, $organization_okpo, $org_name);
	//$sql="update vtiger_organizationdetails set organizationname = ?, address = ?, city = ?, state = ?,  code = ?, country = ?,  phone = ?,  fax = ?,  website = ? where organizationname = ?";
	//$params = array($organization_name, $organization_address, $organization_city, $organization_state, $organization_code, $organization_country, $organization_phone, $organization_fax, $organization_website, $org_name);
// SalesPlatform.ru end
}	
$adb->pquery($sql, $params);

header("Location: index.php?module=Settings&action=OrganizationConfig");
?>
