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
                        <font color="red">*</font> {'LBL_MODULE'|@getTranslatedString:''}
                </td>
                <td width="90%" align=left class="dvtCellInfo">
                    <input width="10%" type="text" name="modulename" class='detailedViewTextBox' value="{$task->modulename}">
                </td>
	</tr>
	<tr>
                <td width="10%" class="dvtCellLabel" align=right>
                        <font color="red">*</font> {'LBL_RELATION_FIELD_NAME'|@getTranslatedString:''}
                </td>
                <td width="90%" align=left class="dvtCellInfo">
                    <input width="10%" type="text" name="relfieldname" class='detailedViewTextBox' value="{$task->relfieldname}">
                </td>
	</tr>
	<tr>
                <td width="10%" class="dvtCellLabel" align=right>
                        <font color="red">*</font> {'LBL_UPDATED_FIELD_NAME'|@getTranslatedString:''}
                </td>
                <td width="90%" align=left class="dvtCellInfo">
                    <input width="10%" type="text" name="fieldname" class='detailedViewTextBox' value="{$task->fieldname}">
                </td>
	</tr>
	<tr>
                <td width="10%" class="dvtCellLabel" align=right>
                        {'LBL_NEW_FIELD_VALUE'|@getTranslatedString:''}
                </td>
                <td width="90%" align=left class="dvtCellInfo">
                    <input width="10%" type="text" name="fieldvalue" class='detailedViewTextBox' value="{$task->fieldvalue}">
                </td>
	</tr>
</table>
