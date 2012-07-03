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

<div style="width: 400px;">

	<form method="POST" action="javascript:void(0);">
	
	<table width="100%" cellpadding="5" cellspacing="0" border="0" class="small mailClient">
	<tr>
                {* SalesPlatform.ru begin: SMSNotifier localization *}
		<td colspan="2" class="mailClientWriteEmailHeader" width="90%" align="left">{'Compose SMS'|getTranslatedString:$MODULE}</td>
		{* <td colspan="2" class="mailClientWriteEmailHeader" width="90%" align="left">Compose SMS</td> *}
                {* SalesPlatform.ru end *}
	</table>
	
	<table width="100%" cellpadding="5" cellspacing="0" border="0" align="center">
	<tr>
		<td>
		
                        {* SalesPlatform.ru begin: SMSNotifier localization *}
                        {'Message'|getTranslatedString:$MODULE}:<br/>
			{* Message:<br/> *}
                        {* SalesPlatform.ru end *}
			<textarea name="message" class="small" rows="12" cols="10" onkeyup="$('__smsnotifer_compose_wordcount__').innerHTML=this.value.length"></textarea>
		</td>
	<tr>
                {* SalesPlatform.ru begin: SMSNotifier localization *}
		<td align="right"><span id="__smsnotifer_compose_wordcount__">0</span> {'characters'|getTranslatedString:$MODULE} </td>
		{* <td align="right"><span id="__smsnotifer_compose_wordcount__">0</span> characters </td> *}
                {* SalesPlatform.ru end *}
	</tr>
	</table>
	
	<table width="100%" cellpadding="5" cellspacing="0" border="0" class="layerPopupTransport">
	<tr>
		<td class="small" align="center">
			<input type="hidden" name="idstring" value="{$IDSTRING}" />
			<input type="hidden" name="phonefields" value="{$PHONEFIELDS}" />
			<input type="hidden" name="sourcemodule" value="{$SOURCEMODULE}" />
			
			<input type="button" class="small crmbutton save" onclick="SMSNotifierCommon.triggerSendSMS(this.form);" value="{$APP.LBL_SEND}"/>
			<input type="button" class="small crmbutton cancel" onclick="SMSNotifierCommon.hideComposeWizard();" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"/>
		</td>
	</tr>
	</table>

	</form>
</div>