{strip}
    <div class="container-fluid">
        <div class="contents tabbable">
            <ul class="nav nav-tabs massEditTabs" style="margin-bottom: 0;">
                <li class="active"><a href="#providersTab" data-toggle="tab"><strong>{vtranslate('LBL_AVAILABLE_PROVIDERS',$QUALIFIED_MODULE)}</strong></a></li>
                <li id="assignedToRoleTab"><a href="#rulesTab" data-toggle="tab"><strong>{vtranslate('LBL_RULES',$QUALIFIED_MODULE)}</strong></a></li>
            </ul>
        </div>

        <div class="tab-content layoutContent padding20 themeTableColor overflowVisible">
            <div class="tab-pane active" id="providersTab">	
                <div id="pickListValuesTable">
                <div class=" vt-default-callout vt-info-callout">
                    <h4 class="vt-callout-header"><span class="fa fa-info-circle">&nbsp;&nbsp;</span>{vtranslate('LBL_INFORMATION', $QUALIFIED_MODULE)}</h4>
                    <p>{vtranslate('LBL_PROVIDERS_INFO', $QUALIFIED_MODULE)}</p>
                </div>
                
                <div class="controls fieldValue col-sm-6">
                    {if empty($CURRENT_PROVIDER_ID)}
                        <h5>{vtranslate('LBL_SELECT_PROVIDER', $QUALIFIED_MODULE)}</h5>
                    {/if}
                    <select id="existingProviders" class="select2" name="modulesList" style="min-width: 250px;">
                        {if empty($CURRENT_PROVIDER_ID)}
                            <option value=''></option>
                        {/if}
                        {foreach item=CURRENT_PROVIDER key=KEY from=$EXISTING_PROVIDERS}
                            <option value="{$CURRENT_PROVIDER}" data-viewmodule="{$CURRENT_PROVIDER}" data-provider_id="{$KEY}" 
                                {if $KEY eq $CURRENT_PROVIDER_ID}selected{/if}>{vtranslate($CURRENT_PROVIDER, $MODULE_NAME)}</option>
                        {/foreach}   
                    </select>
                        <button id="selectProvider" class="btn btn-default"><strong>{vtranslate('LBL_SELECT', $QUALIFIED_MODULE)}</strong></button>
                </div>
                </div>
            </div>
            <div class="tab-pane form-horizontal row" id="rulesTab">
                <div id="rulesContainer">
                    {include file="ListRules.tpl"|@vtemplate_path:$QUALIFIED_MODULE}
                </div>	
            </div>
        </div>
    </div>
{/strip}	
