{*<!--
/*+*******************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *
  *******************************************************************************/
-->*}

<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
</script>
<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
	<tr>
                <td width="10%" class="dvtCellLabel" align=right>
                    <font color="red">*</font> Reminder Text
                </td>
                <td width="90%" align=left class="dvtCellInfo">
                    <input width="10%" type="text" name="remindertext" class='detailedViewTextBox' value="{$task->remindertext}">
                </td>
	</tr>
	<tr>
                <td width="10%" class="dvtCellLabel" align=right>
                    <font color="red">*</font> Check Query (SQL must return single row with field named 'count')
                </td>
                <td width="90%" align=left class="dvtCellInfo">
                    <textarea  style="width:90%;height:200px;" name="checkquery" rows="55" cols="40" class="detailedViewTextBox">{$task->checkquery}</textarea>
                </td>
	</tr>
	<tr>
                <td width="10%" class="dvtCellLabel" align=right>
                    Relation Query (SQL must return single row with two fields named 'crmid' and 'module')
                </td>
                <td width="90%" align=left class="dvtCellInfo">
                    <textarea  style="width:90%;height:200px;" name="getrelatedentity" rows="55" cols="40" class="detailedViewTextBox">{$task->getrelatedentity}</textarea>
                </td>
	</tr>
</table>
