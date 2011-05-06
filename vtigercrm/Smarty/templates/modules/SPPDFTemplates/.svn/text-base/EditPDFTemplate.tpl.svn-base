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
<link rel="stylesheet" href="include/SalesPlatform/CodeMirror/lib/codemirror.css">
<script src="include/SalesPlatform/CodeMirror/lib/codemirror.js"></script>
<link rel="stylesheet" href="include/SalesPlatform/CodeMirror/mode/xml/xml.css">
<script src="include/SalesPlatform/CodeMirror/mode/xml/xml.js"></script>
<link rel="stylesheet" href="include/SalesPlatform/CodeMirror/mode/css/css.css">
<script src="include/SalesPlatform/CodeMirror/mode/css/css.js"></script>
                    
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<br/>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<form name="mainform" action="index.php?module=SPPDFTemplates&action=SavePDFTemplate" method="post" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="return_module" value="SPPDFTemplates">
<input type="hidden" name="parenttab" value="{$PARENTTAB}">
<input type="hidden" name="templateid" value="{$SAVETEMPLATEID}">
<input type="hidden" name="action" value="SavePDFTemplate">
<tr>        
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">

				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">

				<tr>
                    {if $EMODE eq 'edit'}
					    {if $DUPLICATE_FILENAME eq ""}
                            <td class=heading2 valign=bottom><b><a href="index.php?module=SPPDFTemplates&action=ListPDFTemplates&parenttab=Tools">{$MOD.LBL_TEMPLATE_GENERATOR}</a> &gt; {$MOD.LBL_EDIT} &quot;{$NAME}&quot; </b></td>
                        {else}
                            <td class=heading2 valign=bottom><b><a href="index.php?module=SPPDFTemplates&action=ListPDFTemplates&parenttab=Tools">{$MOD.LBL_TEMPLATE_GENERATOR}</a> &gt; {$MOD.LBL_DUPLICATE} &quot;{$DUPLICATE_NAME}&quot; </b></td>
                        {/if}
    				{else}
    					<td class=heading2 valign=bottom><b><a href="index.php?module=SPPDFTemplates&action=ListPDFTemplates&parenttab=Tools">{$MOD.LBL_TEMPLATE_GENERATOR}</a> > {$MOD.LBL_NEW_TEMPLATE} </b></td>
    				{/if}

				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_TEMPLATE_GENERATOR_DESCRIPTION}</td>
				</tr>
				</table>

				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td> 
                    
                    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
          		    <tr><td>
                    
                      <table class="small" width="100%" border="0" cellpadding="3" cellspacing="0"><tr>
                          <!-- <td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td> -->
                          <td style="width: 15%;" class="dvtSelectedCell" id="properties_tab" width="75" align="center" nowrap="nowrap"><b>{$MOD.LBL_PROPERTIES_TAB}</b></td>
                          <td class="dvtTabCache" style="width: 80%;" nowrap="nowrap">&nbsp;</td> 
                      </tr></table>
                    </td></tr>
					
                    <tr><td align="left" valign="top">
                      <div style="diplay:block;" id="properties_div">       
                        <table class="dvtContentSpace" width="100%" border="0" cellpadding="3" cellspacing="0" style="padding:10px;">                        
                        <tr>
    						<td width=20% class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_TEMPLATE_NAME}:</strong></td>
    						<td width=80% class="small cellText"><input name="templatename" id="templatename" type="text" value="{$NAME}" class="detailedViewTextBox" tabindex="1">&nbsp;</td>
    					</tr>
    					<tr>
    						<td valign=top class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_MODULENAMES}:</strong></td>
    						<td class="cellText small" valign="top">
                                <select name="modulename" id="modulename" class="small">
                                	{if $SELECTMODULE neq ""}
                                        {html_options  options=$MODULENAMES selected=$SELECTMODULE}
                                    {else}
                                        {html_options  options=$MODULENAMES}
                                    {/if}
                                </select>
                            </td>      						
    					</tr>    					
                        <tr>
    			    <td width=20% class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_HEADER_SIZE}:</strong></td>
    			    <td width=80% class="small cellText"><input name="header_size" id="header_size" type="text" value="{$HEADER_SIZE}" class="detailedViewTextBox" tabindex="2" style="width: 100px">&nbsp;</td>
    			</tr>
                        <tr>
    			    <td width=20% class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_FOOTER_SIZE}:</strong></td>
    			    <td width=80% class="small cellText"><input name="footer_size" id="footer_size" type="text" value="{$FOOTER_SIZE}" class="detailedViewTextBox" tabindex="3" style="width: 100px">&nbsp;</td>
    			</tr>
    					<tr>
    						<td valign=top class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_PAGE_ORIENTATION}:</strong></td>
    						<td class="cellText small" valign="top">
                                <select name="page_orientation" id="page_orientation" class="small">
                                        {html_options  options=$PAGE_ORIENTATIONS selected=$PAGE_ORIENTATION}
                                </select>
                            </td>      						
    					</tr>    					
                        </table>
                      </div>
                      
                          
                        </td></tr>
                        </table>
                      </div>
                      
                      
                    </td></tr>
                    <tr><td class="small" style="text-align:center;padding:15px 0px 10px 0px;">
					   <input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" onclick="return saveTemplate();" >&nbsp;&nbsp;            			
            		   <input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="window.history.back()" />            			
					</td></tr>
                    </table>

                    <div style="diplay:block;" id="body_div2"> 
                        <style>.CodeMirror {ldelim} border: 1px solid #cccccc; {rdelim}</style>
                        <textarea name="body" id="body" style="width:90%;height:500px" class=small tabindex="5">{$BODY}</textarea>
                    </div>

                    <table width="100%"  border="0" cellspacing="0" cellpadding="5" >
                        <tr><td class="small" style="text-align:center;padding:10px 0px 10px 0px;" colspan="3">
    					   <input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" onclick="return saveTemplate();" >&nbsp;&nbsp;            			
                		   <input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="window.history.back()" />            			
    		   	        </td></tr>
                    </table>

				</td>
				</tr>
				</table>
			</td>
			</tr>
                        </form>
			</table>
			
			
<script>
var editor = CodeMirror.fromTextArea(document.getElementById("body"),
{ldelim}
mode: "text/html", tabMode: "indent"
{rdelim}
);

function check4null(form)
{ldelim}

        var isError = false;
        var errorMessage = "";
        // Here we decide whether to submit the form.
        if (trim(form.templatename.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{$MOD.LBL_TEMPLATE_NAME}";
                form.templatename.focus();
        {rdelim}

        if (trim(form.modulename.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{$MOD.LBL_MODULENAMES}";
                form.templatename.focus();
        {rdelim}

        if (trim(form.header_size.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{$MOD.LBL_HEADER_SIZE}";
                form.templatename.focus();
        {rdelim}

        if (trim(form.footer_size.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{$MOD.LBL_FOOTER_SIZE}";
                form.templatename.focus();
        {rdelim}

        if (trim(form.page_orientation.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{$MOD.LBL_PAGE_ORIENTATION}";
                form.templatename.focus();
        {rdelim}

        // Here we decide whether to submit the form.
        if (isError == true) {ldelim}
                alert("{$MOD.LBL_MISSING_FIELDS}" + errorMessage);
                return false;
        {rdelim}
 return true;

{rdelim}

function saveTemplate()
{ldelim}
    if (!check4null(document.mainform))
       return false;
    else
       return true;
{rdelim}


</script>
