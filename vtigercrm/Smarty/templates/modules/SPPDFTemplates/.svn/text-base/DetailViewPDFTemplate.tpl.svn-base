{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/
-->*}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        {*<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>*}
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">

				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
		    	<form method="post" action="index.php" name="etemplatedetailview" onsubmit="VtigerJS_DialogBox.block();">  
				<input type="hidden" name="action" value="">
				<input type="hidden" name="module" value="SPPDFTemplates">
				<input type="hidden" name="templateid" value="{$TEMPLATEID}">
				<input type="hidden" name="parenttab" value="{$PARENTTAB}">
				<input type="hidden" name="isDuplicate" value="false">
				<tr>
					<td class=heading2 valign=bottom><b><a href="index.php?module=SPPDFTemplates&action=ListPDFTemplates&parenttab=Tools">{$MOD.LBL_TEMPLATE_GENERATOR}</a> &gt; {$MOD.LBL_VIEWING} &quot;{$NAME}&quot; </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_TEMPLATE_GENERATOR_DESCRIPTION}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_PROPERTIES} &quot;{$NAME}&quot; </strong></td>
						<td class="small" align=right>&nbsp;&nbsp;
						  <input class="crmButton edit small" type="submit" name="Button" value="{$APP.LBL_EDIT_BUTTON_LABEL}" onclick="this.form.action.value='EditPDFTemplate'; this.form.parenttab.value='Tools'">&nbsp;
						  <input class="crmbutton small create" type="submit" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON}" title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="U"  onclick="this.form.isDuplicate.value='true'; this.form.action.value='EditPDFTemplate';">&nbsp;&nbsp;
                        </td>
					</tr>
					</table>
					
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
						<td width=20% class="small cellLabel"><strong>{$MOD.LBL_TEMPLATE_NAME}:</strong></td>
						<td width=80% class="small cellText"><strong>{$NAME}</strong></td>
				    </tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_MODULENAMES}:</strong></td>
						<td class="cellText small" valign=top>{$MODULENAME}</td>
					</tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_HEADER_SIZE}:</strong></td>
						<td class="cellText small" valign=top>{$HEADER_SIZE}</td>
					</tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_FOOTER_SIZE}:</strong></td>
						<td class="cellText small" valign=top>{$FOOTER_SIZE}</td>
					</tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_PAGE_ORIENTATION}:</strong></td>
						{if $PAGE_ORIENTATION eq 'L'}
						<td class="cellText small" valign=top>{$MOD.Landscape}</td>
						{else}
						<td class="cellText small" valign=top>{$MOD.Portrait}</td>
						{/if}
					</tr>
					<tr>
					  <td colspan="2" valign=top class="cellText small">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="thickBorder">
                        <tr>
                          <td valign=top>
                          <table width="100%"  border="0" cellspacing="0" cellpadding="5" >
                              <tr>
                                <td colspan="2" valign="top" class="small" style="background-color:#cccccc"><strong>{$MOD.LBL_PDF_TEMPLATE}</strong></td>
                              </tr>
                             
                              <tr>
                                <td valign="top" class="cellLabel small">{$MOD.LBL_BODY}</td>
                                <td class="cellText small">{$BODY}</td>
                              </tr>
                              
                          </table>
                          </td>                          
                        </tr>                        
                      </table>
                      </td>
					  </tr>
					  
					  
					</table> 					
				</td>
				</table>

			</td>
			</tr>
			</table>
		</td>
	</tr>
	</form>
	</table>
		


</td>
   </tr>   
</tbody>
</table>
