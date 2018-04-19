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
<div class="container-fluid">
    <div class="col-sm-12 col-xs-12">
        <div class=" vt-default-callout vt-info-callout">
            <h4 class="vt-callout-header"><span class="fa fa-info-circle">&nbsp;</span>{vtranslate('LBL_INFORMATION', $QUALIFIED_MODULE)}</h4>
            <p>{vtranslate('LBL_DIFFERENT_RULES_FOR_PROVIDERS', $QUALIFIED_MODULE)}</p>
            <p>{vtranslate('LBL_CURRENT_PROVIDER', $QUALIFIED_MODULE)}&nbsp;<strong>{$PROVIDER_NAME}</strong></p>
        </div>
        <br>
        <div class="editViewContainer">
            <br>
            <form id="rulesForm" class="form-horizontal" method="POST">
                <div class="editViewBody">
                    <div class="editViewContents">
                        {* select with modules *}
                        <div class="form-group">
                            <label class="muted control-label col-sm-2 col-xs-2">{vtranslate('LBL_SELECT_MODULE', $QUALIFIED_MODULE)}</label>
                            <div class="controls col-sm-3 col-xs-3">
                                <select name="sourceModule" class="select2 form-control marginLeftZero">
                                    {foreach item=MODULE_MODEL from=$AVAILABLE_MODULES}
                                        {assign var=MODULE_NAME value=$MODULE_MODEL->get('name')}
                                        <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $SELECTED_MODULE} selected {/if}>
                                            {vtranslate($MODULE_NAME, $MODULE_NAME)}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        {* select with mandatory source fields *}
                        <div class="form-group">
                            <label class="muted control-label col-sm-2 col-xs-2">{vtranslate('LBL_DEPENDEND_FIELD', $QUALIFIED_MODULE)}</label>
                            <div class="controls col-sm-3 col-xs-3">
                                <select name="sourceField" class="select2 form-control" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}" data-rule-required="true">
                                    <option value=''></option>
                                {foreach key=FIELD_NAME item=FIELD_LABEL from=$PICKLIST_FIELDS}
                                    <option value="{$FIELD_NAME}" {if $RECORD_MODEL->get('sourceField') eq $FIELD_NAME} selected {/if}>{vtranslate($FIELD_LABEL, $SELECTED_MODULE)}</option>
                                {/foreach}
                                </select>
                            </div>
                            <div class="controls col-sm-3 col-xs-3">
                                <select name="sourceProviderField" class="select2 form-control select2-offscreen" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}">
                                    <option value=''></option>
                                    {foreach key=FIELD_NAME item=FIELD_LABEL from=$PROVIDER_PICKLIST_FIELDS}
                                        <option value="{$FIELD_NAME}" {if $RECORD_MODEL->get('sourceProviderField') eq $FIELD_NAME} selected {/if}>{vtranslate($FIELD_NAME, $QUALIFIED_MODULE)}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        {if $RECORD_MODEL->get('targetFields') && $RECORD_MODEL->get('targetProviderFields')}
                            {assign var=COUNTER value=0}
                            {assign var=TARGET_PROVIDER_FIELDS value=$RECORD_MODEL->get('targetProviderFields')}
                            {foreach key=KEY item=ITEM from=$RECORD_MODEL->get('targetFields')}
                                <div class="form-group">
                                    <label class="muted control-label col-sm-2 col-xs-2">{vtranslate('LBL_FILL_IN_FIELD', $QUALIFIED_MODULE)}</label>
                                    <div class="controls col-sm-3 col-xs-3">
                                        <select name="dependentFields[]" class="select2 form-control select2-offscreen" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}">
                                            {foreach key=FIELD_NAME item=FIELD_LABEL from=$PICKLIST_FIELDS}
                                                <option value="{$FIELD_NAME}" {if $KEY eq $FIELD_NAME} selected {/if}>{vtranslate($FIELD_LABEL, $SELECTED_MODULE)}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="controls col-sm-3 col-xs-3">
                                        {assign var=TEST value=$RECORD_MODEL->get('targetProviderFields')}
                                        <select name="providerFields[]" class="select2 form-control select2-offscreen" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}">
                                            <option value=''></option>
                                            {foreach key=FIELD_NAME item=FIELD_LABEL from=$PROVIDER_PICKLIST_FIELDS}
                                                <option value="{$FIELD_NAME}" {if $TARGET_PROVIDER_FIELDS[$KEY] eq $FIELD_NAME} selected {/if}>{vtranslate($FIELD_NAME, $QUALIFIED_MODULE)}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <a role="javascript:void(0)" class="deleteFieldsItemLine" data-rule-id="{$RULE_ID}">
                                        <strong><i class="fa fa-trash" style="vertical-align: middle"></i></strong>
                                    </a>
                                </div>
                            {assign var=COUNTER value=COUNTER+1}
                            {/foreach}
                        {/if}
                        {* template for adding new fields on page *}
                        <div class="form-group hide lineItemCopy">
                            <label class="muted control-label col-sm-2 col-xs-2">{vtranslate('LBL_FILL_IN_FIELD', $QUALIFIED_MODULE)}</label>
                            <div class="controls col-sm-3 col-xs-3">
                                <select name="dependentFields[]" class="select2 form-control select2-offscreen" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}">
                                    {foreach key=FIELD_NAME item=FIELD_LABEL from=$PICKLIST_FIELDS}
                                        <option value="{$FIELD_NAME}" {if $RECORD_MODEL->get('targetfield') eq $FIELD_NAME} selected {/if}>{vtranslate($FIELD_LABEL, $SELECTED_MODULE)}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="controls col-sm-3 col-xs-3">
                                <select name="providerFields[]" class="select2 form-control select2-offscreen" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}">
                                    <option value=''></option>
                                    {foreach key=FIELD_NAME item=FIELD_LABEL from=$PROVIDER_PICKLIST_FIELDS}
                                        <option value="{$FIELD_NAME}" {if $RECORD_MODEL->get('sourcefield') eq $FIELD_NAME} selected {/if}>{vtranslate($FIELD_NAME, $QUALIFIED_MODULE)}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <a role="javascript:void(0)" class="deleteFieldsItemLine" data-rule-id="{$RULE_ID}">
                                <strong><i class="fa fa-trash" style="vertical-align: middle"></i></strong>
                            </a>
                        </div>
                    </div>

                    <div class="btn-toolbar">
                        <span class="btn-group">
                            <button type="button" class="btn btn-default" id="addDependendField" data-module-name="SPTips">
                                <i class="fa fa-plus"></i>
                                &nbsp;&nbsp;
                                <strong>{vtranslate('LBL_ADD_FIELD', $QUALIFIED_MODULE)}</strong>
                            </button> 
                        </span>
                    </div>
                    <input type="hidden" name="module" value="SPTips"/>
                    <input type="hidden" name="parent" value="Settings"/>
                    <input type="hidden" name="action" value="SaveRule"/>
                    <input type="hidden" name="record" value="{$RULE_ID}"/>
                    <input type="hidden" name="providerId" value="{$CURRENT_PROVIDER_ID}"/>
                    <div class='modal-overlay-footer clearfix'>
                        <div class="row clearfix">
                            <div class=' textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                                <button type='submit' class='btn btn-success saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                                <a class='cancelLink'  href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
{/strip}