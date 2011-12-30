{*<!--
/*+********************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *********************************************************************************/
-->*}
{include file="LoginHeader.tpl}

<table class="loginWrapper" width="100%" height="100%" cellpadding="20" cellspacing="0" border="0">
	<tr valign="top">
		<td valign="top" align="left" width="50%">
			<img align="absmiddle" src="test/logo/{$COMPANY_DETAILS.logo}" alt="logo"/>
			<br />
			<a target="_blank" href="http://{$COMPANY_DETAILS.website}">{$COMPANY_DETAILS.name}</a>
		</td>

		<td rowspan="2">
			<div class="loginForm">
				<div class="poweredBy">SalesPlatform vtiger CRM - {$VTIGER_VERSION}</div>
				<form action="index.php" method="post" name="DetailView" id="form">
					<input type="hidden" name="module" value="Users" />
					<input type="hidden" name="action" value="Authenticate" />
					<input type="hidden" name="return_module" value="Users" />
					<input type="hidden" name="return_action" value="Login" />
					<div class="inputs">
{* SalesPlatform.ru begin *}
						<div class="label">{$APP.LBL_USER_NAME}</div>
{*						<div class="label">User Name</div> *}
{* SalesPlatform.ru end *}
						<div class="input"><input type="text" name="user_name"/></div>
						<br />
{* SalesPlatform.ru begin *}
						<div class="label">{$APP.LBL_PASSWORD}</div>
{*						<div class="label">Password</div> *}
{* SalesPlatform.ru end *}
						<div class="input"><input type="password" name="user_password"/></div>
						{if $LOGIN_ERROR neq ''}
						<div class="errorMessage">
							{$LOGIN_ERROR}
						</div>
						{/if}
						<br />
						<div class="button">
{* SalesPlatform.ru begin *}
							<input type="submit" id="submitButton" value="{$APP.LBL_LOGIN}" />
{*							<input type="submit" id="submitButton" value="Login" /> *}
{* SalesPlatform.ru end *}
						</div>
					</div>
				</form>
			</div>
			<div class="importantLinks">
			<a href='javascript:mypopup()'>{$APP.LNK_READ_LICENSE}</a>
			|
{* SalesPlatform.ru begin *}
                        <a href='http://community.salesplatform.ru/vtiger_links.php/privacy_policy' target='_blank'>{$APP.LNK_PRIVACY_POLICY}</a>
{*                        <a href='http://www.vtiger.com/products/crm/privacy_policy.html' target='_blank'>{$APP.LNK_PRIVACY_POLICY}</a> *}
{* SalesPlatform.ru end *}
			|
			&copy; 2004- {php} echo date('Y'); {/php}
			</div>
		</td>
	</tr>
	<tr valign="bottom">
		<td class="communityLinks">
{* SalesPlatform.ru begin *}
                        {$APP.LBL_CONNECT_WITH_US}
{*			Connect with us *}
{* SalesPlatform.ru end *}
			<br />
{* SalesPlatform.ru begin *}
			<a target="_blank" href="http://twitter.com/#!/salesplatformru">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Twitter.png" alt="Twitter">
			</a>
			<a target="_blank" href="http://salesplatform.ru/wiki/">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Manuals.png" alt="Manuals">
			</a>
			<a target="_blank" href="http://community.salesplatform.ru/forums/">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Forums.png" alt="Forums">
			</a>
			<a target="_blank" href="http://community.salesplatform.ru/blog/">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Blogs.png" alt="Blogs">
			</a>
{*
			<a target="_blank" href="http://www.facebook.com/pages/vtiger/226866697333578?sk=app_143539149057867">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Facebook.png" alt="Facebook">
			</a>
			<a target="_blank" href="http://twitter.com/#!/vtigercrm">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Twitter.png" alt="Twitter">
			</a>
			<a target="_blank" href="http://www.linkedin.com/company/1270573?trk=tyah">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Linkedin.png" alt="Linkedin">
			</a>
			<a target="_blank" href="http://www.youtube.com/user/vtigercrm">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Youtube.png" alt="Videos">
			</a>
			<a target="_blank" href="http://wiki.vtiger.com/">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Manuals.png" alt="Manuals">
			</a>
			<a target="_blank" href="http://forums.vtiger.com/">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Forums.png" alt="Forums">
			</a>
			<a target="_blank" href="http://blogs.vtiger.com/">
				<img align="absmiddle" border="0" src="{$IMAGE_PATH}/Blogs.png" alt="Blogs">
			</a>
{**}
{* SalesPlatform.ru end *}
		</td>
	</tr>
</table>

{include file="LoginFooter.tpl}