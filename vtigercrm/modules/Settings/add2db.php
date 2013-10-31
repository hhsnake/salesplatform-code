<?php

/* * *******************************************************************************
 * * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * 
 * ****************************************************************************** */

require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
global $upload_badext, $root_directory, $adb;

$uploaddir = $root_directory . "/test/logo/"; // set this to wherever
$saveflag = "true";
$error_flag = "";
$binFile = $_FILES['binFile']['name'];
$imageContent = file_get_contents($_FILES['binFile']['tmp_name']);
// SalesPlatform.ru begin Bad кedundancy сheck, see image_extensions_allowed checking
//if (preg_match('/(<\?(php)?)/i', $imageContent) == 0) {
// SalesPlatform.ru end
	$imageInfo = getimagesize($_FILES['binFile']['tmp_name']);
	$image_extensions_allowed = array('jpeg', 'png', 'jpg', 'pjpeg', 'x-png');
	
	if (!empty($imageInfo)) {
		if (isset($_REQUEST['binFile_hidden'])) {
			$filename = sanitizeUploadFileName(vtlib_purify($_REQUEST['binFile_hidden']), $upload_badext);
		} else {
			$binFile = sanitizeUploadFileName($binFile, $upload_badext);
			$filename = ltrim(basename(" " . $binFile));
		}

		$filetype = $_FILES['binFile']['type'];
		$filesize = $_FILES['binFile']['size'];
		$filetype_array = explode("/", $filetype);
		$file_type_val = strtolower($filetype_array[1]);

		if ($filesize != 0) {
		if (in_array($file_type_val, $image_extensions_allowed)) { //Checking whether the file is an image or not
				if (stristr($binFile, '.gif') != FALSE) {
					$savelogo = "false";
					$error_flag = "1";
				} else {
					$savelogo = "true";
				}
			} else {
				$savelogo = "false";
				$error_flag = "1";
			}
		} else {
			$savelogo = "false";
			if ($filename != "")
				$error_flag = "2";
		}
		$errorCode = $_FILES['binFile']['error'];
		if ($errorCode == 4) {
			$savelogo = "false";
			$error_flag = "5";
			$nologo_specified = "true";
		} else if ($errorCode == 2) {
			$error_flag = "3";
			$savelogo = "false";
			$nologo_specified = "false";
		} else if ($errorCode == 3) {
			$error_flag = "4";
			$savelogo = "false";
			$nologo_specified = "false";
		}

		if ($savelogo == "true") {
			move_uploaded_file($_FILES["binFile"]["tmp_name"], $uploaddir . $_FILES["binFile"]["name"]);
        // SalesPlatform.ru begin Save organization info even if there is no image
                }
        } else {
            $nologo_specified = "true";
        }
        // SalesPlatform.ru end

			$organization_name = vtlib_purify($_REQUEST['organization_name']);
			$org_name = vtlib_purify($_REQUEST['org_name']);
			$organization_address = from_html($_REQUEST['organization_address']);
			$organization_city = from_html($_REQUEST['organization_city']);
			$organization_state = from_html($_REQUEST['organization_state']);
			$organization_code = from_html($_REQUEST['organization_code']);
			$organization_country = from_html($_REQUEST['organization_country']);
			$organization_phone = from_html($_REQUEST['organization_phone']);
			$organization_fax = from_html($_REQUEST['organization_fax']);
			$organization_website = from_html($_REQUEST['organization_website']);
                        // SalesPlatform.ru begin
                        $organization_inn=from_html($_REQUEST['organization_inn']);
                        $organization_kpp=from_html($_REQUEST['organization_kpp']);
                        $organization_bankaccount=from_html($_REQUEST['organization_bankaccount']);
                        $organization_bankname=from_html($_REQUEST['organization_bankname']);
                        $organization_bankid=from_html($_REQUEST['organization_bankid']);
                        $organization_corraccount=from_html($_REQUEST['organization_corraccount']);
                        $organization_director=from_html($_REQUEST['organization_director']);
                        $organization_bookkeeper=from_html($_REQUEST['organization_bookkeeper']);
                        $organization_entrepreneur=from_html($_REQUEST['organization_entrepreneur']);
                        $organization_entrepreneurreg=from_html($_REQUEST['organization_entrepreneurreg']);
                        $organization_okpo=from_html($_REQUEST['organization_okpo']);
                        // SalesPlatform.ru end

			$organization_logoname = $filename;
			if (!isset($organization_logoname))
				$organization_logoname = "";

			$sql = "SELECT * FROM vtiger_organizationdetails WHERE organizationname = ?";
			$result = $adb->pquery($sql, array($org_name));
			$org_name = decode_html($adb->query_result($result, 0, 'organizationname'));
			$org_logo = $adb->query_result($result, 0, 'logoname');

			if ($org_name == '') {
				$organizationId = $adb->getUniqueID('vtiger_organizationdetails');
                                // SalesPlatform.ru begin
				$sql = "INSERT INTO vtiger_organizationdetails
				(organization_id,organizationname, address, city, state, code, country, phone, fax, website, inn, kpp, bankaccount, bankname, bankid, corraccount, director, bookkeeper, entrepreneur, entrepreneurreg, logoname, okpo) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				$params = array($organizationId, $organization_name, $organization_address, $organization_city, $organization_state, $organization_code,
					$organization_country, $organization_phone, $organization_fax, $organization_website, 
                                        $organization_inn, $organization_kpp, $organization_bankaccount,
                                        $organization_bankname, $organization_bankid, $organization_corraccount, $organization_director, $organization_bookkeeper, 
                                        $organization_entrepreneur, $organization_entrepreneurreg, $organization_logoname,$organization_okpo);
				//$sql = "INSERT INTO vtiger_organizationdetails
				//(organization_id,organizationname, address, city, state, code, country, phone, fax, website, logoname) values (?,?,?,?,?,?,?,?,?,?)";
				//$params = array($organizationId, $organization_name, $organization_address, $organization_city, $organization_state, $organization_code,
				//	$organization_country, $organization_phone, $organization_fax, $organization_website, $organization_logoname);
                                // SalesPlatform.ru end
			} else {
				if ($savelogo == "true") {
					$organization_logoname = $filename;
				} elseif ($savelogo == "false" && $error_flag == "") {
					$savelogo = "true";
					$organization_logoname = vtlib_purify($_REQUEST['PREV_FILE']);
				} else {
					$organization_logoname = vtlib_purify($_REQUEST['PREV_FILE']);
				}
				if ($nologo_specified == "true") {
					$savelogo = "true";
					$organization_logoname = $org_logo;
				}

                                // SalesPlatform.ru begin
				$sql = "UPDATE vtiger_organizationdetails
				SET organizationname = ?, address = ?, city = ?, state = ?, code = ?, country = ?, 
                                    phone = ?, fax = ?, website = ?, inn = ?, kpp = ?, bankaccount = ?, 
                                    bankname = ?, bankid = ?, corraccount = ?, director = ?, bookkeeper = ?, entrepreneur = ?, entrepreneurreg = ?,
                                    logoname = ?, okpo = ? WHERE organizationname = ?";
				$params = array($organization_name, $organization_address, $organization_city, $organization_state, $organization_code,
                                            $organization_country, $organization_phone, $organization_fax, $organization_website, 
                                            $organization_inn, $organization_kpp, $organization_bankaccount,
                                            $organization_bankname, $organization_bankid, $organization_corraccount, $organization_director, 
                                            $organization_bookkeeper, $organization_entrepreneur, $organization_entrepreneurreg,
                                            decode_html($organization_logoname), $organization_okpo, $org_name);
				//$sql = "UPDATE vtiger_organizationdetails
				//SET organizationname = ?, address = ?, city = ?, state = ?, code = ?, country = ?, 
				//phone = ?, fax = ?, website = ?, logoname = ? WHERE organizationname = ?";
				//$params = array($organization_name, $organization_address, $organization_city, $organization_state, $organization_code,
				//	$organization_country, $organization_phone, $organization_fax, $organization_website, decode_html($organization_logoname), $org_name);
                                // SalesPlatform.ru end
			}
			$adb->pquery($sql, $params);

			if ($savelogo == "true") {
				header("Location: index.php?parenttab=Settings&module=Settings&action=OrganizationConfig");
			} elseif ($savelogo == "false") {
				header("Location: index.php?parenttab=Settings&module=Settings&action=EditCompanyDetails&flag=" . $error_flag);
			}
// SalesPlatform.ru begin Save organization info even if there is no image
	//}
	//} else {
	//	$error_flag = 2;
	//	header("Location: index.php?parenttab=Settings&module=Settings&action=EditCompanyDetails&flag=" . $error_flag);
	//}

//}else{
//	$error_flag = 5;
//	header("Location: index.php?parenttab=Settings&module=Settings&action=EditCompanyDetails&flag=" . $error_flag);
//}
// SalesPlatform.ru end
?>
