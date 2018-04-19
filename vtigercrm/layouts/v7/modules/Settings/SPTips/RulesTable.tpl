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
<table id="listview-table" class="table listview-table rulesTable">
    <thead>
        <tr class="listViewContentHeader">
            <th style="width:25%">
                {vtranslate('LBL_EXISTING_RULES', $QUALIFIED_MODULE)}
            </th>
        </tr>
    </thead>
    <tbody class="overflow-y">
        <tr class="listViewEntries">
            <td class="listViewEntryValue"></td>
            <td class="listViewEntryValue">{vtranslate('LBL_MODULE', $QUALIFIED_MODULE)}</td>
            <td class="listViewEntryValue">{vtranslate('LBL_SELECTED_FIELD', $QUALIFIED_MODULE)}</td>
            <td class="listViewEntryValue">{vtranslate('LBL_FILL_IN_FIELDS', $QUALIFIED_MODULE)}</td>
        </tr>

        {foreach key=key item=ITEM from=$EXISTING_RULES}
            {assign var=RULE_ID value=$ITEM->get('ruleId')}
            {assign var=DEPENDENT_FIELDS value=$ITEM->get('targetFields')}
            {assign var=PROVIDER_FIELDS value=$ITEM->get('targetProviderFields')}
            {assign var=MODULE_NAME value=$ITEM->get('sourceModule')}
            {assign var=SOURCE_FIELD value=$ITEM->get('sourceField')}
            {assign var=SOURCE_PROVIDER_FIELD value=$ITEM->get('sourceProviderField')}

            <tr class="listViewEntries">
                <td width="10%">
                    <div class="table-actions" style = "width:60px">
                        <a href="index.php?module=SPTips&view=EditRules&parent=Settings&record={$RULE_ID}">
                            <i class="fa fa-pencil"></i>
                        </a>
                        &nbsp;&nbsp;
                        <a role="javascript:void(0)" class="deleteRule" data-rule-id="{$RULE_ID}">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </td>
                <td class="listViewEntryValue">{vtranslate($MODULE_NAME, $MODULE_NAME)}</td>
                <td class="listViewEntryValue">{vtranslate($SOURCE_FIELD, $MODULE_NAME)}&nbsp;&nbsp;<i class="fa fa-arrow-left">&nbsp;&nbsp;</i>{vtranslate($SOURCE_PROVIDER_FIELD, $QUALIFIED_MODULE)}</td>
                <td class="listViewEntryValue">
                    <ul class="lists-menu">
                        {foreach item=LABEL key=FIELD from=$DEPENDENT_FIELDS}
                            <li style="font-size:12px;" class="listViewFilter" >
                                {vtranslate($LABEL, $MODULE_NAME)}&nbsp;&nbsp;<i class="fa fa-arrow-left">&nbsp;&nbsp;</i> {vtranslate($PROVIDER_FIELDS[$FIELD], $QUALIFIED_MODULE)}
                            </li>
                        {/foreach}
                    </ul>
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
{/strip}