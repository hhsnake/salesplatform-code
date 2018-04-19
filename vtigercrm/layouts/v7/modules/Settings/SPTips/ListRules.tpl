{*
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
*}
{strip}
    <div class="col-sm-12 col-xs-12 ">
        <div class=" vt-default-callout vt-info-callout">
            <h4 class="vt-callout-header"><span class="fa fa-info-circle">&nbsp;</span>{vtranslate('LBL_INFORMATION', $QUALIFIED_MODULE)}</h4>
            <ul>
                <li>{vtranslate('LBL_DIFFERENT_RULES_FOR_PROVIDERS', $QUALIFIED_MODULE)}</li>
                <li>{vtranslate('LBL_AUTOCOMPLETE_FIELDS', $QUALIFIED_MODULE)}</li>
            </ul>
        </div>
        
        <div id="listview-actions" class="listview-actions-container">
            <label class="muted control-label">{vtranslate('LBL_PROVIDER', $QUALIFIED_MODULE)}&nbsp;&nbsp;</label>
            <select name="existingProviders" class="select2" style="min-width: 250px;">
                {foreach item=CURRENT_PROVIDER key=KEY from=$EXISTING_PROVIDERS}
                    {if empty($CURRENT_PROVIDER_ID)}
                        <option value=''></option>
                    {/if}
                    <option value="{$CURRENT_PROVIDER}" data-viewmodule="{$CURRENT_PROVIDER}" data-provider_id="{$KEY}" 
                        {if $KEY eq $CURRENT_PROVIDER_ID}selected{/if}>{vtranslate($CURRENT_PROVIDER, $MODULE_NAME)}</option>
                {/foreach}
            </select>
            <button id="addRule" class="btn btn-default pull-right">
                <strong>{vtranslate('LBL_CREATE_RULE', $QUALIFIED_MODULE)}</strong>
            </button>
            <br><br>
            <button id="editProvider" type="button" class="btn btn-default pull-right">
                <strong>{vtranslate('LBL_EDIT_PROVIDER', $QUALIFIED_MODULE)}</strong>
            </button>
            <div class="list-content row">
                <div class="col-sm-12 col-xs-12 ">
                    <div id="table-content" class="table-container" style="padding-top:0px !important;">
                        {include file="RulesTable.tpl"|@vtemplate_path:$QUALIFIED_MODULE}
                    </div>
                    <div id="scroller_wrapper" class="bottom-fixed-scroll">
                        <div id="scroller" class="scroller-div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
{/strip}