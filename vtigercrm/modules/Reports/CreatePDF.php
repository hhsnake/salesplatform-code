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
ini_set('max_execution_time','1800');
// SalesPlatform.ru begin
require_once("modules/Reports/SPReportRun.php");
//require_once("modules/Reports/ReportRun.php");
// SalesPlatform.ru end
require_once("modules/Reports/Reports.php");
//require('include/fpdf/fpdf.php');
require('include/tcpdf/tcpdf.php');
$language = $_SESSION['authenticated_user_language'].'.lang.php';
require_once("include/language/$language");
$reportid = vtlib_purify($_REQUEST["record"]);
$oReport = new Reports($reportid);
//Code given by Csar Rodrguez for Rwport Filter
$filtercolumn = $_REQUEST["stdDateFilterField"];
$filter = $_REQUEST["stdDateFilter"];
// SalesPlatform.ru begin
$oReportRun = new SPReportRun($reportid);
//$oReportRun = new ReportRun($reportid);
// SalesPlatform.ru end

$startdate = ($_REQUEST['startdate']);
$enddate = ($_REQUEST['enddate']);
if(!empty($startdate) && !empty($enddate) && $startdate != "0000-00-00" && 
		$enddate != "0000-00-00" ) {
	$date = new DateTimeField($_REQUEST['startdate']);
	$endDate = new DateTimeField($_REQUEST['enddate']);
	$startdate = $date->getDBInsertDateValue();//Convert the user date format to DB date format
	$enddate = $endDate->getDBInsertDateValue();//Convert the user date format to DB date format
}

$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);

// SalesPlatform.ru begin

$reportParams = array();

if(isset($_REQUEST["ownerFilter"])) {
    $selected_owner = vtlib_purify($_REQUEST["ownerFilter"]);
    $reportParams['{$owner}'] = $selected_owner;
} else {
    $reportParams['{$owner}'] = '';
}

if(isset($_REQUEST["accountFilter"])) {
    $selected_account = vtlib_purify($_REQUEST["accountFilter"]);
    $reportParams['{$account}'] = $selected_account;
} else {
    $reportParams['{$account}'] = '';
}

require_once 'include/Zend/Json.php';
$json = new Zend_Json();

$advft_criteria = $_REQUEST['advft_criteria'];
if(!empty($advft_criteria)) $advft_criteria = $json->decode($advft_criteria);
$advft_criteria_groups = $_REQUEST['advft_criteria_groups'];
if(!empty($advft_criteria_groups)) $advft_criteria_groups = $json->decode($advft_criteria_groups);

if(!empty($advft_criteria)) {
    $filtersql = $oReportRun->RunTimeAdvFilter($advft_criteria,$advft_criteria_groups,$reportParams);
} else {
    $filtersql = $oReportRun->getAdvFilterSql($reportid, $reportParams);
}

if($filterlist != '') {
    if($filtersql != '')
        $filtersql = $filterlist . ' AND ' . $filtersql;
    else
        $filtersql = $filterlist;
}

$pdf = $oReportRun->getReportPDF($filtersql, $reportParams);
//$pdf = $oReportRun->getReportPDF($filterlist);
// SalesPlatform.ru end
$pdf->Output('Reports.pdf','D');

exit();
?>
