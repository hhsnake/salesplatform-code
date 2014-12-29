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
{* SalesPlatform.ru begin *}
{include file="Header.tpl"|vtemplate_path:'Vtiger'}
{* SalesPlatform.ru end *}
<div>
	<table width="100%" cellpadding="3" cellspacing="1" border="0" class="lvt small">
		<tr>
            {* SalesPlatform.ru begin *}
			<td nowrap="nowrap" title="{$RECORD->get('statusmessage')}" bgcolor="{$RECORD->get('status')}" width="25%">{$RECORD->get('tonumber')}</td>
            {*<td nowrap="nowrap" bgcolor="{$RECORD->get('statuscolor')}" width="25%">{$RECORD->get('tonumber')}</td>*}
            {* SalesPlatform.ru end *}
		</tr>
	</table>
</div>