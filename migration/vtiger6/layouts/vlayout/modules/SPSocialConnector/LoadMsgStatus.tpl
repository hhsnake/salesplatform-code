{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
-->*}
{strip}
    <div class="modelContainer">
        <div class="modal-header">
            {vtranslate('Import result', $MODULE)} <h3>{$MSG_COUNT}</h3> {vtranslate('Messages', $MODULE)}<br>
        </div>

        <div class="modal-footer">
            <div class=" pull-left cancelLinkContainer">
                <button class="btn btn-success" onClick="self.close()"><strong>{vtranslate('LBL_CLOSE', $MODULE)}</strong></button>
            </div>
        </div>
    </div>
{/strip}