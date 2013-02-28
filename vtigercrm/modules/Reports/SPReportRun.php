<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

require_once("modules/Reports/ReportRun.php");

function getCustomReportsList() {
    global $adb;
    $reportList = array();

    $res = $adb->query('SELECT reporttype FROM sp_custom_reports');
    for($i = 0; $i < $adb->num_rows($res); $i++) {
        $reportList[] = $adb->query_result($res, $i, 'reporttype');
    }

    return $reportList;
}

function getCustomReportsListWithDateFilter() {
    global $adb;
    $reportList = array();

    $res = $adb->query('SELECT reporttype FROM sp_custom_reports WHERE datefilter=1');
    for($i = 0; $i < $adb->num_rows($res); $i++) {
        $reportList[] = $adb->query_result($res, $i, 'reporttype');
    }

    return $reportList;
}

function getCustomReportsListWithOwnerFilter() {
    global $adb;
    $reportList = array();

    $res = $adb->query('SELECT reporttype FROM sp_custom_reports WHERE ownerfilter=1');
    for($i = 0; $i < $adb->num_rows($res); $i++) {
        $reportList[] = $adb->query_result($res, $i, 'reporttype');
    }

    return $reportList;
}

function getCustomReportsListWithAccountFilter() {
    global $adb;
    $reportList = array();

    $res = $adb->query('SELECT reporttype FROM sp_custom_reports WHERE accountfilter=1');
    for($i = 0; $i < $adb->num_rows($res); $i++) {
        $reportList[] = $adb->query_result($res, $i, 'reporttype');
    }

    return $reportList;
}

class SPReportRun extends ReportRun
{
	function sGetSQLforReport($reportid,$filtersql,$type='',$chartReport=false,$params=array())
	{
            global $adb;

            if(in_array($this->reporttype, getCustomReportsList())) {
                $stdfiltersql = $filtersql;
                $filterhash = md5($stdfiltersql);

                if($stdfiltersql != '') {
                    $matches = array();
                    preg_match("/\\(date.date between '([0-9 :\-]+)' and '([0-9 :\-]+)'\\)( AND .*)?/", $stdfiltersql, $matches);
                    $startdate = $matches[1];
                    $enddate = $matches[2];

                    $adv_filter_sql = $matches[3];
                } else {
                    $startdate = '';
                    $enddate = '';
                    $adv_filter_sql = '';
                }

                $res = $adb->pquery('SELECT functionname FROM sp_custom_reports WHERE reporttype=?',
                        array($this->reporttype));
                if($adb->num_rows($res) > 0) {
                    $functionname = $adb->query_result($res, 0, 'functionname');

                    require_once "modules/Reports/sp_custom_reports/".$this->reporttype.".inc.php";
                    return $functionname($startdate, $enddate, $filterhash, $params);
                } else {
                    return '';
                }
            }
            else
                return parent::sGetSQLforReport ($reportid, $filtersql, $type, $chartReport, $params);
        }

        function getReportCaption($reportid,$filterlist,$params) {
            return '';
        }

}

?>
