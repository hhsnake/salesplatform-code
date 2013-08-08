{*<!--

	/*********************************************************************************
	 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
	 * ("License"); You may not use this file except in compliance with the License
	 * The Original Code is:  vtiger CRM Open Source
	 * The Initial Developer of the Original Code is vtiger.
	 * Portions created by vtiger are Copyright (C) vtiger.
	 * All Rights Reserved.
	 *
	 ********************************************************************************/

-->*}
<br>
<script type="text/javascript">
	var rel_fields = {$REL_FIELDS};
</script>
<script language="JavaScript" type="text/javascript" src="modules/Reports/Reports.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-ru-utf8.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script language="JavaScript" type="text/javascript" src="include/calculator/calc.js"></script>
{$BLOCKJS}


<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
    <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	
	
	
<table class="small reportGenHdr mailClient mailClientBg" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<form name="NewReport" action="index.php" method="POST" onsubmit="VtigerJS_DialogBox.block();">
    <input type="hidden" name="booleanoperator" value="5"/>
    <input type="hidden" name="record" value="{$REPORTID}"/>
    <input type="hidden" name="reload" value=""/>    
    <input type="hidden" name="module" value="Reports"/>
    <input type="hidden" name="action" value="SaveAndRun"/>
    <input type="hidden" name="dlgType" value="saveAs"/>
    <input type="hidden" name="reportName"/>
    <input type="hidden" name="folderid" value="{$FOLDERID}"/>
    <input type="hidden" name="reportDesc"/>
    <input type="hidden" name="folder"/>

	<tbody>
	<tr>
	<td style="padding: 10px; text-align: left;" width="70%">
		<span class="moduleName">
		{if $MOD.$REPORTNAME neq ''}
			{$MOD.$REPORTNAME}
		{else}
			{$REPORTNAME}
		{/if}
		</span>&nbsp;&nbsp;
		{if $IS_EDITABLE eq 'true'}
		<input type="button" name="custReport" value="{$MOD.LBL_CUSTOMIZE_REPORT}" class="crmButton small edit" onClick="editReport('{$REPORTID}');">
		{/if}
		<br>
		<a href="index.php?module=Reports&action=ListView" class="reportMnu" style="border-bottom: 0px solid rgb(0, 0, 0);">&lt;{$MOD.LBL_BACK_TO_REPORTS}</a>
	</td>
	<td style="border-left: 2px dotted rgb(109, 109, 109); padding: 10px;" width="30%">
		<b>{$MOD.LBL_SELECT_ANOTHER_REPORT} : </b><br>
		<select name="another_report" class="detailedViewTextBox" onChange="selectReport()">
		{foreach key=report_in_fld_id item=report_in_fld_name from=$REPINFOLDER}
		{if $MOD.$report_in_fld_name neq ''} 
			{if $report_in_fld_id neq $REPORTID}
				<option value={$report_in_fld_id}>{$MOD.$report_in_fld_name}</option>
			{else}	
				<option value={$report_in_fld_id} selected>{$MOD.$report_in_fld_name}</option>
			{/if}
		{else}
			{if $report_in_fld_id neq $REPORTID}
				<option value={$report_in_fld_id}>{$report_in_fld_name}</option>
			{else}	
				<option value={$report_in_fld_id} selected>{$report_in_fld_name}</option>
			{/if}
		{/if}
		{/foreach}
		</select>&nbsp;&nbsp;
	</td>
	</tr>
	</tbody>
</table>

<!-- Generate Report UI Filter -->
<table class="small reportGenerateTable" align="center" cellpadding="5" cellspacing="0" width="95%" border=0>
<tr>
	<td align=center class=small>
	
        {* SalesPlatform.ru begin added support datefilter *}  
        {if $DATEFILTER neq ''}
        {* SalesPlatform.ru end *}
	<table border=0 cellspacing=0 cellpadding=0 width=80%>
		<tr>
			<td align=left class=small><b>{$MOD.LBL_SELECT_COLUMN} </b></td><td class=small>&nbsp;</td>
			<td align=left class=small><b>{$MOD.LBL_SELECT_TIME} </b></td><td class=small>&nbsp;</td>
			<td align=left class=small><b>{$MOD.LBL_SF_STARTDATE} </b></td><td class=small>&nbsp;</td>
			<td align=left class=small><b>{$MOD.LBL_SF_ENDDATE} </b>
		</tr>
		<tr>
			<td align="left" width="30%">
			<select name="stdDateFilterField" class="small" style="width:98%" onchange="standardFilterDisplay();">
			{$BLOCK1}
			</select>
			</td>
			<td class=small>&nbsp;</td>			
			<td align=left width="30%">
			<select name="stdDateFilter" class="small" onchange='showDateRange( this.options[ this.selectedIndex ].value )' style="width:98%">
			{$BLOCKCRITERIA}
			</select>
			</td>
			<td class=small>&nbsp;</td>
			<td align=left width="20%">
				<table border=0 cellspacing=0 cellpadding=2>
				<tr>
					<td align=left><input name="startdate" id="jscal_field_date_start" type="text" size="10" class="importBox" style="width:70px;" value="{$STARTDATE}"></td>
					<td valign=absmiddle align=left><img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_start"><font size="1"><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
						<script type="text/javascript">
							Calendar.setup ({ldelim}
							inputField : "jscal_field_date_start", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
							{rdelim});
						</script>

					</td>
				</tr>
				</table>
			</td>
			<td align=left class=small>&nbsp;</td>
			<td align=left width=20%>
				<table border=0 cellspacing=0 cellpadding=2>
				<tr>
					<td align=left><input name="enddate" id="jscal_field_date_end" type="text" size="10" class="importBox" style="width:70px;" value="{$ENDDATE}"></td>
					<td valign=absmiddle align=left><img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_end"><font size="1"><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
						<script type="text/javascript">
							Calendar.setup ({ldelim}
							inputField : "jscal_field_date_end", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_end", singleClick : true, step : 1
							{rdelim});
						</script>
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	
        {* SalesPlatform.ru begin added support datefilter *}                                         
        {/if}
        {* SalesPlatform.ru end *}
	</td>
</tr>
	<tr>
		<td align="left" style="padding:5px" width="80%">
			{include file='AdvanceFilter.tpl' SOURCE='reports'}
		</td>
	</tr>
{* SalesPlatform.ru begin *}
		{if $OWNERCRITERIA}
		<tr>
			<td align=left class=small style="padding-left: 50px">
			<b>{$MOD.LBL_SELECT_OWNER} </b>
			</td>
		</tr>
		<tr>
			<td align=left class=small style="padding-left: 50px">
			<select name="ownerFilter" class="small" style="width:300px">
			{$OWNERCRITERIA}
			</select>
			</td>
		</tr>
		{/if}
		{if $ACCOUNTCRITERIA}
		<tr>
			<td align=left class=small style="padding-left: 50px">
			<b>{$MOD.LBL_SELECT_ACCOUNT} </b>
			</td>
		</tr>
		<tr>
			<td align=left class=small style="padding-left: 50px">
			<select name="accountFilter" class="small" style="width:300px">
			{$ACCOUNTCRITERIA}
			</select>
			</td>
		</tr>
		{/if}
{* SalesPlatform.ru end *}
	<tr>
		<td align="center">
			<input type="button" class="small create" onclick="generateReport({$REPORTID});" value="{$MOD.LBL_GENERATE_NOW}" title="{$MOD.LBL_GENERATE_NOW}" />
			&nbsp;
			<input type="button" class="small edit" onclick="saveReportAdvFilter({$REPORTID});" value="     {$MOD.LBL_SAVE_REPORT}     " title="{$MOD.LBL_SAVE_REPORT}" />
		</td>
	</tr>
</table>

<table class="small reportGenHdr mailClient mailClientBg" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="right" valign="bottom" style="padding:5px">
			<a href="javascript:void(0);" onclick="location.href='index.php?module=Reports&action=SaveAndRun&record={$REPORTID}&folderid={$FOLDERID}'"><img src="{'revert.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_RELOAD_REPORT}" title="{$MOD.LBL_RELOAD_REPORT}" border="0"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="saveReportAs(this,'duplicateReportLayout');"><img src="{'saveas.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_SAVE_REPORT_AS}" title="{$MOD.LBL_SAVE_REPORT_AS}" border="0"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="goToURL(CrearEnlace('CreatePDF',{$REPORTID}));"><img src="{'pdf-file.jpg'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTPDF_BUTTON}" title="{$MOD.LBL_EXPORTPDF_BUTTON}" border="0"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="goToURL(CrearEnlace('CreateXL',{$REPORTID}));"><img src="{'xls-file.jpg'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTXL_BUTTON}" title="{$MOD.LBL_EXPORTXL_BUTTON}" border="0"></a>
			&nbsp;
			<a href="javascript:void(0);" onclick="goToPrintReport({$REPORTID});"><img src="{'fileprint.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_PRINT_REPORT}" title="{$MOD.LBL_PRINT_REPORT}" border="0"></a>
		</td>
	</tr>
</table>


<div style="display: block;" id="Generate" align="center">
	{include file="ReportRunContents.tpl"}
</div>
<br>

</td>
<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
</tr>
</table>


<!-- Save Report As.. UI -->
<div id="duplicateReportLayout" style="display:none;width:350px;" class="layerPopup">
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerHeadingULine">
		<tr>
			<td class="genHeaderSmall" nowrap align="left" width="30%">{$MOD.LBL_SAVE_REPORT_AS}</td>
			<td align="right"><a href="javascript:;" onClick="fninvsh('duplicateReportLayout');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></a></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
		<tr>
			<td class="small">
				<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
					<tr>
						<td width="30%" align="right" style="padding-right:5px;"><b>{$MOD.LBL_REPORT_NAME} : </b></td>
						<td width="70%" align="left" style="padding-left:5px;"><input type="text" name="newreportname" id="newreportname" class="txtBox" value=""></td>
					</tr>
					<tr>
						<td width="30%" align="right" style="padding-right:5px;"><b>{$MOD.LBL_REP_FOLDER} : </b></td>
						<td width="70%" align="left" style="padding-left:5px;">
							<select name="reportfolder" id="reportfolder" class="txtBox">
							{foreach item=folder from=$REP_FOLDERS}
							{if $FOLDERID eq $folder.id}
								<option value="{$folder.id}" selected>{$folder.name}</option>
							{else}
								<option value="{$folder.id}">{$folder.name}</option>
							{/if}
							{/foreach}
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" style="padding-right:5px;" valign="top"><b>{$MOD.LBL_DESCRIPTION}: </b></td>
						<td align="left" style="padding-left:5px;"><textarea name="newreportdescription"  id="newreportdescription" class="txtBox" rows="5">{$REPORTDESC}</textarea></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
		<tr>
			<td class="small" align="center">
			<input name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmbutton small save" onClick="duplicateReport({$REPORTID});" type="button">&nbsp;&nbsp;
			<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="fninvsh('duplicateReportLayout');" type="button">
			</td>
		</tr>
	</table>
</div>

{literal}

<script type="text/javascript">
function CrearEnlace(tipo,id){
        // SalesPlatform.ru begin added support datefilter 
        if (getObj('stdDateFilter')) {
        // SalesPlatform.ru end
	var stdDateFilterFieldvalue = '';
	if(document.NewReport.stdDateFilterField.selectedIndex != -1)
		stdDateFilterFieldvalue = document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].value;

	var stdDateFiltervalue = '';
	if(document.NewReport.stdDateFilter.selectedIndex != -1)
		stdDateFiltervalue = document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].value;
                    
        // SalesPlatform.ru begin added support datefilter        
        }
        // SalesPlatform.ru end            
	if(!checkAdvancedFilter()) return false;
	var advft_criteria = $('advft_criteria').value;
	var advft_criteria_groups = $('advft_criteria_groups').value;

// SalesPlatform.ru begin
	if(document.NewReport.ownerFilter && document.NewReport.ownerFilter.selectedIndex != -1)
		ownerFiltervalue = document.NewReport.ownerFilter.options[document.NewReport.ownerFilter.selectedIndex].value;
        else
            ownerFiltervalue = '';
	
        if(document.NewReport.accountFilter && document.NewReport.accountFilter.selectedIndex != -1)
            accountFiltervalue = document.NewReport.accountFilter.options[document.NewReport.accountFilter.selectedIndex].value;
        else
            accountFiltervalue = '';

	return "index.php?module=Reports&action="+tipo+"&record="+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups+'&ownerFilter='+ownerFiltervalue+'&accountFilter='+accountFiltervalue+"&stdDateFilterField="+stdDateFilterFieldvalue+"&stdDateFilter="+stdDateFiltervalue+"&startdate="+document.NewReport.startdate.value+"&enddate="+document.NewReport.enddate.value;
//	return "index.php?module=Reports&action="+tipo+"&record="+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups;
// SalesPlatform.ru end

}

function goToURL(url) {
	document.location.href = url;
}

// SalesPlatform.ru begin added support datefilter 
if (getObj('stdDateFilter')) {
// SalesPlatform.ru end
var filter = getObj('stdDateFilter').options[document.NewReport.stdDateFilter.selectedIndex].value
    if( filter != "custom" )
    {
        showDateRange( filter );
    }
// SalesPlatform.ru begin added support datefilter 
}
// SalesPlatform.ru end    

// If current user has no access to date fields, we should disable selection
// Fix for: #4670
standardFilterDisplay();


function saveReportAs(oLoc,divid) {
	$('newreportname').value = '';
	$('newreportdescription').value = '';
	fnvshobj(oLoc,divid);
}

function duplicateReport(id) {

	VtigerJS_DialogBox.block();
	
	var newreportname = $('newreportname').value;
	if (trim(newreportname) == "") {
		VtigerJS_DialogBox.unblock();
		alert(alert_arr.MISSING_REPORT_NAME);
		return false;
	} else {
		new Ajax.Request(
				'index.php',
				{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody: 'action=ReportsAjax&mode=ajax&file=CheckReport&module=Reports&check=reportCheck&reportName='+encodeURIComponent(newreportname),
				onComplete: function(response) {
					if(response.responseText != 0) {
						VtigerJS_DialogBox.unblock();
						alert(alert_arr.REPORT_NAME_EXISTS);
						return false;
					} else {
						createDuplicateReport(id);
					}
				}
				}
		);
	}
}

function createDuplicateReport(id) {
	
	var newreportname = $('newreportname').value;
	var newreportdescription = $('newreportdescription').value;
	var newreportfolder = $('reportfolder').value;
	
	if(!checkAdvancedFilter()) return false;

	var advft_criteria = $('advft_criteria').value;
	var advft_criteria_groups = $('advft_criteria_groups').value;
	
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'action=ReportsAjax&file=DuplicateReport&mode=ajax&module=Reports&record='+id+'&newreportname='+encodeURIComponent(newreportname)+'&newreportdescription='+encodeURIComponent(newreportdescription)+'&newreportfolder='+newreportfolder+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups,
                        onComplete: function(response) {
							var responseArray = JSON.parse(response.responseText);
							if(trim(responseArray['errormessage']) != '') {
								VtigerJS_DialogBox.unblock();
								alert(resonseArray['errormessage']);								
							}
							var reportid = responseArray['reportid'];
							var folderid = responseArray['folderid'];
							var url ='index.php?action=SaveAndRun&module=Reports&record='+reportid+'&folderid='+folderid;
							goToURL(url);
                        }
                }
        );
}

function generateReport(id) {

	if(!checkAdvancedFilter()) return false;
	
	VtigerJS_DialogBox.block();
	
	var advft_criteria = $('advft_criteria').value;
	var advft_criteria_groups = $('advft_criteria_groups').value;

        // SalesPlatform.ru begin added support datefilter 
        if (getObj('stdDateFilter')) {
        // SalesPlatform.ru end
	var stdDateFilterFieldvalue = '';
	if(document.NewReport.stdDateFilterField.selectedIndex != -1)
		stdDateFilterFieldvalue = document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].value;

	var stdDateFiltervalue = '';
	if(document.NewReport.stdDateFilter.selectedIndex != -1)
		stdDateFiltervalue = document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].value;
	var startdatevalue = document.NewReport.startdate.value;
	var enddatevalue = document.NewReport.enddate.value;
        // SalesPlatform.ru begin added support datefilter 
        }
        // SalesPlatform.ru end

// SalesPlatform.ru begin
	if(document.NewReport.ownerFilter && document.NewReport.ownerFilter.selectedIndex != -1)
		ownerFiltervalue = document.NewReport.ownerFilter.options[document.NewReport.ownerFilter.selectedIndex].value;
        else
                ownerFiltervalue = '';
	if(document.NewReport.accountFilter && document.NewReport.accountFilter.selectedIndex != -1)
		accountFiltervalue = document.NewReport.accountFilter.options[document.NewReport.accountFilter.selectedIndex].value;
        else
                accountFiltervalue = '';
// SalesPlatform.ru end

// SalesPlatform.ru begin added support datefilter 
if (getObj('stdDateFilter')) {
// SalesPlatform.ru end
	var date1=getObj("startdate")
        var date2=getObj("enddate")
	
if ((date1.value != '') || (date2.value != ''))
{

        if(!dateValidate("startdate","Start Date","D"))
                return false

        if(!dateValidate("enddate","End Date","D"))
                return false

	if(!dateComparison("startdate",'Start Date',"enddate",'End Date','LE'))
                return false;
}
    
// SalesPlatform.ru begin added support datefilter 
}
// SalesPlatform.ru end

	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
// SalesPlatform.ru begin
                        postBody: 'action=ReportsAjax&file=SaveAndRun&mode=ajax&module=Reports&submode=saveCriteria&record='+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups+'&ownerFilter='+ownerFiltervalue+'&accountFilter='+accountFiltervalue+'&stdDateFilterField='+stdDateFilterFieldvalue+'&stdDateFilter='+stdDateFiltervalue+'&startdate='+startdatevalue+'&enddate='+enddatevalue,
//                        postBody: 'action=ReportsAjax&file=SaveAndRun&mode=ajax&module=Reports&submode=saveCriteria&record='+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups,
// SalesPlatform.ru end
                        onComplete: function(response) {
							getObj('Generate').innerHTML = response.responseText;
							// Performance Optimization: To update record count of the report result 
							var __reportrun_directoutput_recordcount_scriptnode = $('__reportrun_directoutput_recordcount_script');
							if(__reportrun_directoutput_recordcount_scriptnode) { eval(__reportrun_directoutput_recordcount_scriptnode.innerHTML); }
							// END
							setTimeout("ReportInfor()",1);
							VtigerJS_DialogBox.unblock();
                        }
                }
        );
}


function saveReportAdvFilter(id) {

	if(!checkAdvancedFilter()) return false;
	
	VtigerJS_DialogBox.block();
	
	var advft_criteria = $('advft_criteria').value;
	var advft_criteria_groups = $('advft_criteria_groups').value;

	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'action=ReportsAjax&file=SaveAndRun&mode=ajax&module=Reports&submode=saveCriteria&record='+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups,
                        onComplete: function(response) {
							getObj('Generate').innerHTML = response.responseText;
							// Performance Optimization: To update record count of the report result 
							var __reportrun_directoutput_recordcount_scriptnode = $('__reportrun_directoutput_recordcount_script');
							if(__reportrun_directoutput_recordcount_scriptnode) { eval(__reportrun_directoutput_recordcount_scriptnode.innerHTML); }
							// END
							VtigerJS_DialogBox.unblock();
                        }
                }
        );
}

function selectReport() {
	var id = document.NewReport.another_report.options  [document.NewReport.another_report.selectedIndex].value;
	var folderid = getObj('folderid').value;
	url ='index.php?action=SaveAndRun&module=Reports&record='+id+'&folderid='+folderid;
	goToURL(url);
}
function ReportInfor()
{
        // SalesPlatform.ru begin added support datefilter 
        if (getObj('stdDateFilter')) {
        // SalesPlatform.ru end
	var stdDateFilterFieldvalue = '';
	if(document.NewReport.stdDateFilterField.selectedIndex != -1)
		stdDateFilterFieldvalue = document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].text;

	var stdDateFiltervalue = '';
	if(document.NewReport.stdDateFilter.selectedIndex != -1)
		stdDateFiltervalue = document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].text;

	var startdatevalue = document.NewReport.startdate.value;
	var enddatevalue = document.NewReport.enddate.value;

	if(startdatevalue != '' && enddatevalue=='')
	{
		{/literal}
		var reportinfr = '{'Reporting'|@getTranslatedString}  "'+stdDateFilterFieldvalue+'"   ({'from'|@getTranslatedString}  '+startdatevalue+' )';
                {literal}
	}else if(startdatevalue == '' && enddatevalue !='')
	{
		{/literal}
		var reportinfr = 'Reporting  "'+stdDateFilterFieldvalue+'"   (  {'till'|@getTranslatedString}  '+enddatevalue+')';
                {literal}
	}else if(startdatevalue == '' && enddatevalue =='')
	{
		{/literal}
                var reportinfr = "{$MOD.NO_FILTER_SELECTED}";
                {literal}
	}else if(startdatevalue != '' && enddatevalue !='')
	{
		{/literal}
	var reportinfr = '{'Reporting'|@getTranslatedString} "'+stdDateFilterFieldvalue+'"  {'of'|@getTranslatedString}  "'+stdDateFiltervalue+'"  ( '+startdatevalue+'  {'to'|@getTranslatedString}  '+enddatevalue+' )';
                {literal}
	}
	getObj('report_info').innerHTML = reportinfr;
// SalesPlatform.ru begin added support datefilter 
}
// SalesPlatform.ru end
}
ReportInfor();

{/literal}

function goToPrintReport(id) {ldelim}
	var stdDateFilterFieldvalue = '';
	if(document.NewReport.stdDateFilterField.selectedIndex != -1)
	stdDateFilterFieldvalue = document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].value;

	var stdDateFiltervalue = '';
if(document.NewReport.stdDateFilter.selectedIndex != -1)
	stdDateFiltervalue = document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].value;

	if(!checkAdvancedFilter()) return false;
	var advft_criteria = $('advft_criteria').value;
	var advft_criteria_groups = $('advft_criteria_groups').value;

// SalesPlatform.ru begin
	if(document.NewReport.ownerFilter && document.NewReport.ownerFilter.selectedIndex != -1)
		ownerFiltervalue = document.NewReport.ownerFilter.options[document.NewReport.ownerFilter.selectedIndex].value;
        else
                ownerFiltervalue = '';
	if(document.NewReport.accountFilter && document.NewReport.accountFilter.selectedIndex != -1)
		accountFiltervalue = document.NewReport.accountFilter.options[document.NewReport.accountFilter.selectedIndex].value;
        else
                accountFiltervalue = '';
	window.open("index.php?module=Reports&action=ReportsAjax&file=PrintReport&record="+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups+"&ownerFilter="+ownerFiltervalue+"&accountFilter="+accountFiltervalue+"&stdDateFilterField="+stdDateFilterFieldvalue+"&stdDateFilter="+stdDateFiltervalue+"&startdate="+document.NewReport.startdate.value+"&enddate="+document.NewReport.enddate.value,"{$MOD.LBL_Print_REPORT}","width=800,height=650,resizable=1,scrollbars=1,left=100");
//	window.open("index.php?module=Reports&action=ReportsAjax&file=PrintReport&record="+id+'&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups,"{$MOD.LBL_Print_REPORT}","width=800,height=650,resizable=1,scrollbars=1,left=100");
// SalesPlatform.ru end

{rdelim}

</script>