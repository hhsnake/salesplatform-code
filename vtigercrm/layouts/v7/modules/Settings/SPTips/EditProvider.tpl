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
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class=" vt-default-callout vt-info-callout">
            <h4 class="vt-callout-header"><span class="fa fa-info-circle">&nbsp;&nbsp;</span>{vtranslate('LBL_INFORMATION', $QUALIFIED_MODULE)}</h4>
            <p>{vtranslate('LBL_PROVIDERS_AVAILABLE_FOR_SELECTION', $QUALIFIED_MODULE)}</p>
            <ul class="list-group">
            {foreach item=AVAILABLE_PROVIDER from=$AVAILABLE_PROVIDERS}
                <li class="list-group-item">{$AVAILABLE_PROVIDER}</li>
            {/foreach}  
            </ul>
        </div>
        
        <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
            <input type="hidden" name="module" value="{$MODULE}">
            <input type="hidden" name="parent" value="Settings" />
            <input type="hidden" name="action" value="SaveProvider" />
            <input type="hidden" name="record" value="{$RECORD_STRUCTURE->get('provider_id')}" />
            {assign var=RECORD_ID value=$RECORD_STRUCTURE->get('provider_id')}
            
            <div class="editViewHeader">
                <div class='row'>
                    <div class="col-lg-12 col-md-12 col-sm-12 ">
                        <h4 class="editHeader">{vtranslate('LBL_EDIT_PROVIDER', $QUALIFIED_MODULE)}</h4>
                    </div>
                </div>
            </div>
            <hr>
            <div class="editViewBody">
                <div class="editViewContents" >
                    
                    <table class="table table-borderless">
                        <tr>
                            <td>
                                <label class="control-label fieldLabel col-sm-4">{vtranslate('LBL_PROVIDER_NAME', $QUALIFIED_MODULE)}</label>
                                <input class="fieldValue inputElement" type="text" disabled="disabled" name="provider_name" value="{$RECORD_STRUCTURE->get('provider_name')}"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label fieldLabel col-sm-4">{vtranslate('LBL_API_KEY', $QUALIFIED_MODULE)}</label>
                                <input class="fieldValue inputElement" type="text" name="api_key" value="{$RECORD_STRUCTURE->get('api_key')}"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label fieldLabel col-sm-4">{vtranslate('LBL_TOKEN', $QUALIFIED_MODULE)}</label>
                                <input class="fieldValue inputElement" type="text" name="token" value="{$RECORD_STRUCTURE->get('token')}"/>
                            </td>
                        </tr>
                    </table>
                    
                </div>
            </div>
                <div class="modal-overlay-footer">
                        <div class="row clearfix">
                            <div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                                {*SalesPlatform.ru begin
                                <button class="btn btn-success saveButton" type="submit">{vtranslate('LBL_SAVE', $MODULE)}</button>
                                <a class="cancelLink" href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                                {*<button class="btn btn-success saveButton" type="submit">Save</button>
                                <a class="cancelLink" href="javascript:history.back()" type="reset">Cancel</a>*}
                                {*SalesPaltform.ru end*}
                            </div>
                        </div>
                    </div>
            <div class='modal-overlay-footer clearfix'>
                <div class="row clearfix">
                    <div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                        <button type='submit' class='btn btn-success saveButton'  >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                        <a class='cancelLink'  href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
{/strip}