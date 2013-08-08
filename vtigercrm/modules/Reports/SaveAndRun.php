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
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/CustomView/CustomView.php');
require_once("config.php");
require_once('modules/Reports/Reports.php');
require_once('include/logging.php');
// SalesPlatform.ru begin
require_once("modules/Reports/SPReportRun.php");
//require_once("modules/Reports/ReportRun.php");
// SalesPlatform.ru end
require_once('include/utils/utils.php');
require_once('Smarty_setup.php');

global $adb,$mod_strings,$app_strings;

$reportid = vtlib_purify($_REQUEST["record"]);
$folderid = vtlib_purify($_REQUEST["folderid"]);
$now_action = vtlib_purify($_REQUEST['action']);
$filtercolumn = vtlib_purify($_REQUEST["stdDateFilterField"]);
$filter = vtlib_purify($_REQUEST["stdDateFilter"]);

$sql = "select * from vtiger_report where reportid=?";
$res = $adb->pquery($sql, array($reportid));
$Report_ID = $adb->query_result($res,0,'reportid');
if(empty($folderid)) {
	$folderid = $adb->query_result($res,0,'folderid');
}
$reporttype = $adb->query_result($res,0,'reporttype');
$showCharts = false;
if($reporttype == 'summary'){
	$showCharts = true;
}
//END Customization
$numOfRows = $adb->num_rows($res);

if($numOfRows > 0) {
    // SalesPlatform.ru begin
    if (!empty($_REQUEST['startdate']) && !empty($_REQUEST['enddate'])) {
        $date = new DateTimeField($_REQUEST['startdate']);
        $endDate = new DateTimeField($_REQUEST['enddate']);
        $startdate = $date->getDBInsertDateValue();
        $enddate = $endDate->getDBInsertDateValue();
    }
    // SalesPlatform.ru end

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
// SalesPlatform.ru end

	global $primarymodule,$secondarymodule,$orderbylistsql,$orderbylistcolumns,$ogReport;
	//added to fix the ticket #5117
	global $current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	$ogReport = new Reports($reportid);
	$primarymodule = $ogReport->primodule;
	$restrictedmodules = array();
	if($ogReport->secmodule!='')
		// SalesPlatform.ru begin PHP 5.4 migration
		$rep_modules = explode(":",$ogReport->secmodule);
		//$rep_modules = split(":",$ogReport->secmodule);
		// SalesPlatform.ru end
	else
		$rep_modules = array();

        // SalesPlatform.ru begin: Fixed default time filter for custom reports
	$ogReport->getSelectedStandardCriteria($reportid);
        // SalesPlatform.ru end

	array_push($rep_modules,$primarymodule);
	$modules_permitted = true;
	$modules_export_permitted = true;
	foreach($rep_modules as $mod){
		if(isPermitted($mod,'index')!= "yes" || vtlib_isModuleActive($mod)==false){
			$modules_permitted = false;
			$restrictedmodules[] = $mod;
		}
		if(isPermitted("$mod",'Export','')!='yes')
			$modules_export_permitted = false;
	}

	if(isPermitted($primarymodule,'index') == "yes" && $modules_permitted == true) {
// SalesPlatform.ru begin
                $oReportRun = new SPReportRun($reportid);
                if(in_array($reporttype, getCustomReportsListWithDateFilter())) {
                    if(empty($filtercolumn)) $filtercolumn = 'date:date:date:date';
                    if(empty($filter)) $filter = $ogReport->stdselectedfilter;
                    if(empty($startdate)) $startdate = '0000-00-00';
                    if(empty($enddate)) $enddate = '0000-00-00';
                }
//                $oReportRun = new ReportRun($reportid);
// SalesPlatform.ru end
		$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);

		require_once 'include/Zend/Json.php';
		$json = new Zend_Json();

		$advft_criteria = $_REQUEST['advft_criteria'];
		if(!empty($advft_criteria)) $advft_criteria = $json->decode($advft_criteria);
		$advft_criteria_groups = $_REQUEST['advft_criteria_groups'];
		if(!empty($advft_criteria_groups)) $advft_criteria_groups = $json->decode($advft_criteria_groups);

		if($_REQUEST['submode'] == 'saveCriteria') {
			updateAdvancedCriteria($reportid,$advft_criteria,$advft_criteria_groups);
		}

                // SalesPlatform.ru begin
                $filtersql = $oReportRun->RunTimeAdvFilter($advft_criteria,$advft_criteria_groups,$reportParams);
                //$filtersql = $oReportRun->RunTimeAdvFilter($advft_criteria,$advft_criteria_groups);
                // SalesPlatform.ru end

		$list_report_form = new vtigerCRM_Smarty;
		//Monolithic phase 6 changes
		if($showCharts == true){
			$list_report_form->assign("SHOWCHARTS",$showCharts);
			require_once 'modules/Reports/CustomReportUtils.php';
			require_once 'include/ChartUtils.php';

			$groupBy = $oReportRun->getGroupingList($reportid);
			if(!empty($groupBy)){
				foreach ($groupBy as $key => $value) {
					//$groupByConditon = explode(" ",$value);
					//$groupByNew = explode("'",$groupByConditon[0]);
					// SalesPlatform.ru begin PHP 5.4 migration
					list($tablename,$colname,$module_field,$fieldname,$single) = explode(":",$key);
					list($module,$field)= explode("_",$module_field);
					//list($tablename,$colname,$module_field,$fieldname,$single) = split(":",$key);
					//list($module,$field)= split("_",$module_field);
					// SalesPlatform.ru end
					$fieldDetails = $key;
					break;
				}
				//$groupByField = $oReportRun->GetFirstSortByField($reportid);
				$queryReports = CustomReportUtils::getCustomReportsQuery($Report_ID,$filtersql);
				$queryResult = $adb->pquery($queryReports,array());
				//ChartUtils::generateChartDataFromReports($queryResult, strtolower($groupByNew[1]));
                if($adb->num_rows($queryResult)){
					$pieChart = ChartUtils::getReportPieChart($queryResult, strtolower($module_field),$fieldDetails,$reportid);
					$barChart = ChartUtils::getReportBarChart($queryResult, strtolower($module_field),$fieldDetails,$reportid);
					$list_report_form->assign("PIECHART",$pieChart);
					$list_report_form->assign("BARCHART",$barChart);
				}
				else{
					$showCharts = false;
				}
			}
			else{
				$showCharts = false;
			}
			$list_report_form->assign("SHOWCHARTS",$showCharts);
		}
		//Monolithic Changes Ends

        // Performance Optimization: Direct output of the report result
        if($_REQUEST['submode'] == 'generateReport' && empty($advft_criteria)) {
			$filtersql = '';
		}

                // SalesPlatform.ru begin
                if($filterlist != '') {
                    if($filtersql != '')
                        $filtersql = $filterlist . ' AND ' . $filtersql;
                    else
                        $filtersql = $filterlist;
                }
                // SalesPlatform.ru end

        $sshtml = array();
		$totalhtml = '';
		$list_report_form->assign("DIRECT_OUTPUT", true);
		$list_report_form->assign_by_ref("__REPORT_RUN_INSTANCE", $oReportRun);
		$list_report_form->assign_by_ref("__REPORT_RUN_FILTER_SQL", $filtersql);
// SalesPlatform.ru begin
		$list_report_form->assign_by_ref("__REPORT_RUN_PARAMS", $reportParams);
// SalesPlatform.ru end
        //Ends

                // SalesPlatform.ru begin: Fixed default time filter for custom reports
		//$ogReport->getSelectedStandardCriteria($reportid);
                // SalesPlatform.ru end
		//commented to omit dashboards for vtiger_reports
		//require_once('modules/Dashboard/ReportsCharts.php');
		//$image = get_graph_by_type('Report','Report',$primarymodule,'',$sshtml[2]);
		//$list_report_form->assign("GRAPH", $image);

		$BLOCK1 = getPrimaryStdFilterHTML($ogReport->primodule,$reporttype,$ogReport->stdselectedcolumn);
		$BLOCK1 .= getSecondaryStdFilterHTML($ogReport->secmodule,$ogReport->stdselectedcolumn);
		// Check if selectedcolumn is found in the filters (Fix for ticket #4866)
		$selectedcolumnvalue = '"'. decode_html($ogReport->stdselectedcolumn) . '"';
                // SalesPlatform.ru begin: Fixed bug with special date field combo in custom reports
		if (!in_array($Report_Type, getCustomReportsListWithDateFilter()) && !$is_admin && isset($ogReport->stdselectedcolumn) && strpos($BLOCK1, $selectedcolumnvalue) === false) {
		//if (!$is_admin && isset($ogReport->stdselectedcolumn) && strpos($BLOCK1, $selectedcolumnvalue) === false) {
                // SalesPlatform.ru end
			$BLOCK1 .= "<option selected value='Not Accessible'>".$app_strings['LBL_NOT_ACCESSIBLE']."</option>";
		}
		$list_report_form->assign("BLOCK1",$BLOCK1);
		$BLOCKJS = $ogReport->getCriteriaJS();
		$list_report_form->assign("BLOCKJS",$BLOCKJS);

		$ogReport->getPriModuleColumnsList($ogReport->primodule);
		$ogReport->getSecModuleColumnsList($ogReport->secmodule);
		$ogReport->getAdvancedFilterList($reportid);

		$COLUMNS_BLOCK = getPrimaryColumns_AdvFilter_HTML($ogReport->primodule, $ogReport);
		$COLUMNS_BLOCK .= getSecondaryColumns_AdvFilter_HTML($ogReport->secmodule, $ogReport);
		$list_report_form->assign("COLUMNS_BLOCK", $COLUMNS_BLOCK);

		$FILTER_OPTION = Reports::getAdvCriteriaHTML();
		$list_report_form->assign("FOPTION",$FILTER_OPTION);

		$rel_fields = $ogReport->adv_rel_fields;
		$list_report_form->assign("REL_FIELDS",Zend_Json::encode($rel_fields));

		$list_report_form->assign("CRITERIA_GROUPS",$ogReport->advft_criteria);

// SalesPlatform.ru begin
		$filterres = $adb->pquery("select * from vtiger_report inner join vtiger_selectquery on vtiger_selectquery.queryid = vtiger_report.queryid".
					  " left join vtiger_relcriteria on vtiger_relcriteria.queryid = vtiger_selectquery.queryid".
					  " where vtiger_report.reportid = ? and vtiger_relcriteria.value = ?", array($reportid, '{$owner}'));
		if($adb->num_rows($filterres) > 0 || in_array($reporttype, getCustomReportsListWithOwnerFilter())) {

		    $ownerssql = "select user_name,concat(last_name,' ',first_name) as full_name from vtiger_users";
                    $ownerparams = array();
                    
                    // Add subordinate users filter for report owners list
                    if(!$is_admin) {
                        $parentRole = fetchUserRole($current_user->id);
                        $ownerssql .= " inner join vtiger_user2role on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid";
                        $ownerssql .= " where vtiger_role.parentrole like ?";
                        $ownerparams[] = "%".$parentRole."%";
                    }

                    $ownersres = $adb->pquery($ownerssql, $ownerparams);
		    $numOfOwners = $adb->num_rows($ownersres);

		    $OWNERCRITERIA .= '<option value="" selected>'.getTranslatedString('LBL_ALL').'</option>';

		    if($numOfOwners > 0)
		    {
			$ownerrow = $adb->fetch_array($ownersres);
			do
			{
			    $OWNERCRITERIA .= '<option value="'.$ownerrow['full_name'].'">'.$ownerrow['full_name'].'</option>';
			}while($ownerrow = $adb->fetch_array($ownersres));

		    }
		    $list_report_form->assign("OWNERCRITERIA",$OWNERCRITERIA);
		}

                if(in_array($reporttype, getCustomReportsListWithAccountFilter())) {
		    $accountsres = $adb->pquery("select distinct vtiger_account.* from vtiger_account
                                         inner join vtiger_salesorder on vtiger_account.accountid=vtiger_salesorder.accountid
                                         inner join vtiger_crmentity as entity1 on vtiger_account.accountid=entity1.crmid
                                         inner join vtiger_crmentity as entity2 on vtiger_salesorder.salesorderid=entity2.crmid
                                         where entity1.deleted = 0 and entity2.deleted = 0", array());
		    $numOfAccounts = $adb->num_rows($accountsres);

		    $ACCOUNTCRITERIA = '';
                    $ACCOUNTCRITERIA .= '<option value="" selected>'.getTranslatedString('LBL_ALL').'</option>';

		    if($numOfAccounts > 0)
		    {
			$accountrow = $adb->fetch_array($accountsres);
			do
			{
			    $ACCOUNTCRITERIA .= '<option value="'.$accountrow['accountid'].'">'.$accountrow['accountname'].'</option>';
			}while($accountrow = $adb->fetch_array($accountsres));

		    }
		    $list_report_form->assign("ACCOUNTCRITERIA",$ACCOUNTCRITERIA);
                }
// SalesPlatform.ru end

		$BLOCKCRITERIA = $ogReport->getSelectedStdFilterCriteria($ogReport->stdselectedfilter);
		$list_report_form->assign("BLOCKCRITERIA",$BLOCKCRITERIA);
		if(isset($ogReport->startdate) && isset($ogReport->enddate))
		{
			$list_report_form->assign("STARTDATE",DateTimeField::convertToUserFormat($ogReport->startdate));
			$list_report_form->assign("ENDDATE",DateTimeField::convertToUserFormat($ogReport->enddate));
		}else
		{
			$list_report_form->assign("STARTDATE",$ogReport->startdate);
			$list_report_form->assign("ENDDATE",$ogReport->enddate);	
		}	
		$list_report_form->assign("MOD", $mod_strings);
		$list_report_form->assign("APP", $app_strings);
		$list_report_form->assign("IMAGE_PATH", $image_path);
		$list_report_form->assign("REPORTID", $reportid);
		$list_report_form->assign("IS_EDITABLE", $ogReport->is_editable);

		$list_report_form->assign("REP_FOLDERS",$ogReport->sgetRptFldr());

		$list_report_form->assign("REPORTNAME", htmlspecialchars($ogReport->reportname,ENT_QUOTES,$default_charset));
		if(is_array($sshtml))$list_report_form->assign("REPORTHTML", $sshtml);
		else $list_report_form->assign("ERROR_MSG", getTranslatedString('LBL_REPORT_GENERATION_FAILED', $currentModule) . "<br>" . $sshtml);
		$list_report_form->assign("REPORTTOTHTML", $totalhtml);
		$list_report_form->assign("FOLDERID", $folderid);
		$list_report_form->assign("DATEFORMAT",$current_user->date_format);
		$list_report_form->assign("JS_DATEFORMAT",parse_calendardate($app_strings['NTC_DATE_FORMAT']));
                // SalesPlatform.ru begin added support datefilter
                if($reporttype == 'tabular' || $reporttype == 'summary' ||  in_array($reporttype, getCustomReportsListWithDateFilter())) {
                    $list_report_form->assign("DATEFILTER", $reporttype);
                } 
                // SalesPlatform.ru end 
		if($modules_export_permitted==true){
			$list_report_form->assign("EXPORT_PERMITTED","YES");
		} else {
			$list_report_form->assign("EXPORT_PERMITTED","NO");
		}
		$rep_in_fldr = $ogReport->sgetRptsforFldr($folderid);
		for($i=0;$i<count($rep_in_fldr);$i++){
			$rep_id = $rep_in_fldr[$i]['reportid'];
			$rep_name = $rep_in_fldr[$i]['reportname'];
			$reports_array[$rep_id]=$rep_name;
		}
		if($_REQUEST['mode'] != 'ajax')
		{
			$list_report_form->assign("REPINFOLDER", $reports_array);
			include('modules/Vtiger/header.php');
			$list_report_form->display('ReportRun.tpl');
		}
		else
		{
			$list_report_form->display('ReportRunContents.tpl');
		}

	} else {
		if($_REQUEST['mode'] != 'ajax') {
			include('modules/Vtiger/header.php');
		}
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_NO_ACCESS']." : ".implode(",",$restrictedmodules)." </span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>								   		     </td>
		</tr>
		</tbody></table>
		</div>";
		echo "</td></tr></table>";
	}

} else {
		echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_REPORT_DELETED']."</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>                                                                                </td>
		</tr>
		</tbody></table>
		</div>";
		echo "</td></tr></table>";
}

/** Function to get the StdfilterHTML strings for the given  primary module
 *  @ param $module : Type String
 *  @ param $selected : Type String(optional)
 *  This Generates the HTML Combo strings for the standard filter for the given reports module
 *  This Returns a HTML sring
 */
function getPrimaryStdFilterHTML($module,$reporttype,$selected="")
{
// SalesPlatform.ru begin
        if(in_array($reporttype, getCustomReportsListWithDateFilter())) {
            return '<option selected value="date:date:date:date">Дата</option>';
        }
// SalesPlatform.ru end

	global $app_list_strings;
	global $ogReport;
	global $current_language;
	$ogReport->oCustomView=new CustomView();
	$result = $ogReport->oCustomView->getStdCriteriaByModule($module);
	$mod_strings = return_module_language($current_language,$module);
	if(isset($result))
	{
		foreach($result as $key=>$value)
		{
			if(isset($mod_strings[$value]))
			{
				if($key == $selected)
				{
					$shtml .= "<option selected value=\"".$key."\">".getTranslatedString($module,$module)." - ".$mod_strings[$value]."</option>";
				}else
				{
					$shtml .= "<option value=\"".$key."\">".getTranslatedString($module,$module)." - ".$mod_strings[$value]."</option>";
				}
			}else
			{
				if($key == $selected)
				{
					$shtml .= "<option selected value=\"".$key."\">".getTranslatedString($module,$module)." - ".$value."</option>";
				}else
				{
					$shtml .= "<option value=\"".$key."\">".getTranslatedString($module,$module)." - ".$value."</option>";
				}
			}
		}
	}

	return $shtml;
}

	/** Function to get the StdfilterHTML strings for the given secondary module
	 *  @ param $module : Type String
	 *  @ param $selected : Type String(optional)
	 *  This Generates the HTML Combo strings for the standard filter for the given reports module
	 *  This Returns a HTML sring
	 */
function getSecondaryStdFilterHTML($module,$selected="")
{
	global $app_list_strings;
	global $ogReport;
	global $current_language;
	$ogReport->oCustomView=new CustomView();

	if($module != "")
        {
        	$secmodule = explode(":",$module);
        	for($i=0;$i < count($secmodule) ;$i++)
        	{
			$result =  $ogReport->oCustomView->getStdCriteriaByModule($secmodule[$i]);
			$mod_strings = return_module_language($current_language,$secmodule[$i]);
        		if(isset($result))
        		{
                		foreach($result as $key=>$value)
                		{
                        		if(isset($mod_strings[$value]))
                                        {
						if($key == $selected)
						{
							$shtml .= "<option selected value=\"".$key."\">".getTranslatedString($secmodule[$i],$secmodule[$i])." - ".$mod_strings[$value]."</option>";
						}else
						{
							$shtml .= "<option value=\"".$key."\">".getTranslatedString($secmodule[$i],$secmodule[$i])." - ".$mod_strings[$value]."</option>";
						}
					}else
					{
						if($key == $selected)
						{
							$shtml .= "<option selected value=\"".$key."\">".getTranslatedString($secmodule[$i],$secmodule[$i])." - ".$value."</option>";
						}else
						{
							$shtml .= "<option value=\"".$key."\">".getTranslatedString($secmodule[$i],$secmodule[$i])." - ".$value."</option>";
						}
					}
                		}
        		}

		}
	}
	return $shtml;
}

function getPrimaryColumns_AdvFilter_HTML($module, $ogReport, $selected='') {
    global $app_list_strings, $current_language;
    // SalesPlatform.ru begin localization of fields outside the module
    global $app_strings;
    // SalesPlatform.ru end
	$mod_strings = return_module_language($current_language,$module);
	$block_listed = array();
    foreach($ogReport->module_list[$module] as $key=>$value)
    {
    	if(isset($ogReport->pri_module_columnslist[$module][$value]) && !$block_listed[$value])
    	{
			$block_listed[$value] = true;
			$shtml .= "<optgroup label=\"".$app_list_strings['moduleList'][$module]." ".getTranslatedString($value)."\" class=\"select\" style=\"border:none\">";
			foreach($ogReport->pri_module_columnslist[$module][$value] as $field=>$fieldlabel)
			{
				if(isset($mod_strings[$fieldlabel]))
				{
					//fix for ticket 5191
					$selected = decode_html($selected);
					$field = decode_html($field);
					//fix ends
					if($selected == $field)
					{
						$shtml .= "<option selected value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
					}else
					{
						$shtml .= "<option value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
					}
				}else
				{
                                        // SalesPlatform.ru begin localization of fields outside the module
                                        if (!empty($app_strings[$fieldlabel])) {
                                            $fieldlabel = $app_strings[$fieldlabel];
                                        }
                                        // SalesPlatform.ru end
					if($selected == $field)
					{
						$shtml .= "<option selected value=\"".$field."\">".$fieldlabel."</option>";
					}else
					{
						$shtml .= "<option value=\"".$field."\">".$fieldlabel."</option>";
					}
				}
			}
       }
    }
    return $shtml;
}


function getSecondaryColumns_AdvFilter_HTML($module, $ogReport, $selected="") {
	global $app_list_strings;
    global $current_language;
    // SalesPlatform.ru begin localization of fields outside the module
    global $app_strings;
    // SalesPlatform.ru end
    if($module != "")
    {
    	$secmodule = explode(":",$module);
    	for($i=0;$i < count($secmodule) ;$i++)
    	{
            $mod_strings = return_module_language($current_language,$secmodule[$i]);
            if(vtlib_isModuleActive($secmodule[$i])){
				$block_listed = array();
				foreach($ogReport->module_list[$secmodule[$i]] as $key=>$value)
                {
					if(isset($ogReport->sec_module_columnslist[$secmodule[$i]][$value]) && !$block_listed[$value])
					{
						$block_listed[$value] = true;
                		  $shtml .= "<optgroup label=\"".$app_list_strings['moduleList'][$secmodule[$i]]." ".getTranslatedString($value)."\" class=\"select\" style=\"border:none\">";
						  foreach($ogReport->sec_module_columnslist[$secmodule[$i]][$value] as $field=>$fieldlabel)
						  {
							if(isset($mod_strings[$fieldlabel]))
							{
								if($selected == $field)
								{
									$shtml .= "<option selected value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
								}else
								{
									$shtml .= "<option value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
								}
							}else
							{
                                                                // SalesPlatform.ru begin localization of fields outside the module
                                                                if (!empty($app_strings[$fieldlabel])) {
                                                                    $fieldlabel = $app_strings[$fieldlabel];
                                                                }
                                                                // SalesPlatform.ru end
								if($selected == $field)
								{
									$shtml .= "<option selected value=\"".$field."\">".$fieldlabel."</option>";
								}else
								{
									$shtml .= "<option value=\"".$field."\">".$fieldlabel."</option>";
								}
							}
						  }
					}
                }
            }
    	}
    }
    return $shtml;
}

function getAdvCriteria_HTML($adv_filter_options, $selected="") {

	 foreach($adv_filter_options as $key=>$value) {
		if($selected == $key) {
			$shtml .= "<option selected value=\"".$key."\">".$value."</option>";
		} else {
			$shtml .= "<option value=\"".$key."\">".$value."</option>";
		}
	 }

    return $shtml;
}
?>
