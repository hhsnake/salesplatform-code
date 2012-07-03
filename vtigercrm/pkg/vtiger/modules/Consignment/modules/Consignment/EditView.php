<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  TODO To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once ('Smarty_setup.php');
require_once ('data/Tracker.php');
require_once ('modules/Invoice/Invoice.php');
require_once ('include/CustomFieldUtil.php');
require_once ('include/utils/utils.php');

global $app_strings, $mod_strings, $currentModule, $log, $current_user;

$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty();
//added to fix the issue4600
$searchurl = getBasic_Advance_SearchURL();
$smarty->assign("SEARCH", $searchurl);
//4600 ends

$currencyid = fetchCurrency($current_user->id);
$rate_symbol = getCurrencySymbolandCRate($currencyid);
$rate = $rate_symbol['rate'];
if (isset ($_REQUEST['record']) && $_REQUEST['record'] != '') {
	if (isset ($_REQUEST['convertmode']) && $_REQUEST['convertmode'] == 'invoicetoconsignment') {
		$invoiceid = $_REQUEST['record'];
		$invoice_focus = new Invoice();
		$invoice_focus->id = $invoiceid;
		$invoice_focus->retrieve_entity_info($invoiceid, "Invoice");
		$focus = getConvertInvoiceToConsignment($focus, $invoice_focus, $invoiceid);

		// Reset the value w.r.t Invoice Selected
		$currencyid = $invoice_focus->column_fields['currency_id'];
		$rate = $invoice_focus->column_fields['conversion_rate'];

		//Added to display the Invoice's associated vtiger_products -- when we create vtiger_sp_consignment from Invoice DetailView
		$associated_prod = getAssociatedProducts("Invoice", $invoice_focus);
		$txtTax = (($invoice_focus->column_fields['txtTax'] != '') ? $invoice_focus->column_fields['txtTax'] : '0.000');
		$txtAdj = (($invoice_focus->column_fields['txtAdjustment'] != '') ? $invoice_focus->column_fields['txtAdjustment'] : '0.000');

		$smarty->assign("CONVERT_MODE", vtlib_purify($_REQUEST['convertmode']));
		$smarty->assign("ASSOCIATEDPRODUCTS", $associated_prod);
		$smarty->assign("MODE", $invoice_focus->mode);
		$smarty->assign("AVAILABLE_PRODUCTS", 'true');
	} else {
		$focus->id = $_REQUEST['record'];
		$focus->mode = 'edit';
		$focus->retrieve_entity_info($_REQUEST['record'], "Consignment");
		$focus->name = $focus->column_fields['subject'];
	}
}
if (isset ($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$smarty->assign("DUPLICATE_FROM", $focus->id);
	$Consignment_associated_prod = getAssociatedProducts($currentModule, $focus);
    $inventory_cur_info = getInventoryCurrencyInfo($currentModule, $focus->id);
	$currencyid = $inventory_cur_info['currency_id'];
	$focus->id = "";
	$focus->mode = '';
}
if(empty($_REQUEST['record']) && $focus->mode != 'edit'){
	setObjectValuesFromRequest($focus);
}
if (isset ($_REQUEST['opportunity_id']) && $_REQUEST['opportunity_id'] != '') {
	$potfocus = new Potentials();
	$potfocus->column_fields['potential_id'] = $_REQUEST['opportunity_id'];
	$associated_prod = getAssociatedProducts("Potentials", $potfocus, $potfocus->column_fields['potential_id']);

}
if (!empty ($_REQUEST['parent_id']) && !empty ($_REQUEST['return_module'])) {
	if ($_REQUEST['return_module'] == 'Services') {
		$focus->column_fields['product_id'] = vtlib_purify($_REQUEST['parent_id']);
		$log->debug("Service Id from the request is " . vtlib_purify($_REQUEST['parent_id']));
		$associated_prod = getAssociatedProducts("Services", $focus, $focus->column_fields['product_id']);
	for ($i=1; $i<=count($associated_prod);$i++) {
		$associated_prod_id = $associated_prod[$i]['hdnProductId'.$i];
		$associated_prod_prices = getPricesForProducts($currencyid,array($associated_prod_id),'Services');
		$associated_prod[$i]['listPrice'.$i] = $associated_prod_prices[$associated_prod_id];
	}
		$smarty->assign("ASSOCIATEDPRODUCTS", $associated_prod);
		$smarty->assign("AVAILABLE_PRODUCTS", 'true');
	}
}

if (isset ($_REQUEST['account_id']) && $_REQUEST['account_id'] != '' && $_REQUEST['record'] == '') {
	require_once ('modules/Accounts/Accounts.php');
	$acct_focus = new Accounts();
	$acct_focus->retrieve_entity_info($_REQUEST['account_id'], "Accounts");
	$focus->column_fields['bill_city'] = $acct_focus->column_fields['bill_city'];
	$focus->column_fields['ship_city'] = $acct_focus->column_fields['ship_city'];
	$focus->column_fields['bill_street'] = $acct_focus->column_fields['bill_street'];
	$focus->column_fields['ship_street'] = $acct_focus->column_fields['ship_street'];
	$focus->column_fields['bill_state'] = $acct_focus->column_fields['bill_state'];
	$focus->column_fields['ship_state'] = $acct_focus->column_fields['ship_state'];
	$focus->column_fields['bill_code'] = $acct_focus->column_fields['bill_code'];
	$focus->column_fields['ship_code'] = $acct_focus->column_fields['ship_code'];
	$focus->column_fields['bill_country'] = $acct_focus->column_fields['bill_country'];
	$focus->column_fields['ship_country'] = $acct_focus->column_fields['ship_country'];
	$focus->column_fields['bill_pobox'] = $acct_focus->column_fields['bill_pobox'];
	$focus->column_fields['ship_pobox'] = $acct_focus->column_fields['ship_pobox'];

}

global $theme;
$theme_path = "themes/" . $theme . "/";
$image_path = $theme_path . "images/";

$disp_view = getView($focus->mode);
$mode = $focus->mode;
if ($disp_view == 'edit_view')
	$smarty->assign("BLOCKS", getBlocks($currentModule, $disp_view, $mode, $focus->column_fields));
else {
	$bas_block = getBlocks($currentModule, $disp_view, $mode, $focus->column_fields, 'BAS');
	$adv_block = getBlocks($currentModule, $disp_view, $mode, $focus->column_fields, 'ADV');

	$blocks['basicTab'] = $bas_block;
	if (is_array($adv_block))
		$blocks['moreTab'] = $adv_block;

	$smarty->assign("BLOCKS", $blocks);
	$smarty->assign("BLOCKS_COUNT", count($blocks));
}

$smarty->assign("OP_MODE", $disp_view);

$smarty->assign("MODULE", $currentModule);
$smarty->assign("SINGLE_MOD", 'Consignment');

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

$log->info("Consignment view");

if (isset ($focus->name))
	$smarty->assign("NAME", $focus->name);
else
	$smarty->assign("NAME", "");

if (isset ($_REQUEST['convertmode']) && $_REQUEST['convertmode'] == 'invoicetoconsignment') {
	$smarty->assign("MODE", $invoice_focus->mode);
	$se_array = getProductDetailsBlockInfo($invoice_focus->mode, "Invoice", $invoice_focus);
}
elseif ($focus->mode == 'edit') {
	$smarty->assign("UPDATEINFO", updateInfo($focus->id));
	$associated_prod = getAssociatedProducts("Consignment", $focus);
	$smarty->assign("ASSOCIATEDPRODUCTS", $associated_prod);
	$smarty->assign("MODE", $focus->mode);
}
elseif (isset ($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$associated_prod = $Consignment_associated_prod;
	$smarty->assign("AVAILABLE_PRODUCTS", 'true');
	$smarty->assign("MODE", $focus->mode);
}
elseif (isset ($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') {
	$smarty->assign("ASSOCIATEDPRODUCTS", $associated_prod);
	$InvTotal = getInventoryTotal($_REQUEST['return_module'], $_REQUEST['return_id']);
	$smarty->assign("MODE", $focus->mode);
}

if (isset ($cust_fld)) {
	$smarty->assign("CUSTOMFIELD", $cust_fld);
}

$smarty->assign("ASSOCIATEDPRODUCTS", $associated_prod);

if (isset ($_REQUEST['return_module']))
	$smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
else
	$smarty->assign("RETURN_MODULE", "Consignment");
if (isset ($_REQUEST['return_action']))
	$smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
else
	$smarty->assign("RETURN_ACTION", "index");
if (isset ($_REQUEST['return_id']))
	$smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
if (isset ($_REQUEST['return_viewname']))
	$smarty->assign("RETURN_VIEWNAME", vtlib_purify($_REQUEST['return_viewname']));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=" . session_id() . $GLOBALS['request_string']);
$smarty->assign("ID", $focus->id);

$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign("CALENDAR_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));

//in create new Consignment, get all available product taxes and shipping & Handling taxes

if ($focus->mode != 'edit') {
	$tax_details = getAllTaxes('available');
	$sh_tax_details = getAllTaxes('available', 'sh');
} else {
	$tax_details = getAllTaxes('available', '', $focus->mode, $focus->id);
	$sh_tax_details = getAllTaxes('available', 'sh', 'edit', $focus->id);
}
$smarty->assign("GROUP_TAXES", $tax_details);
$smarty->assign("SH_TAXES", $sh_tax_details);

$tabid = getTabid("Consignment");
$validationData = getDBValidationData($focus->tab_name, $tabid);
$data = split_validationdataArray($validationData);
$category = getParentTab();
$smarty->assign("CATEGORY", $category);

$smarty->assign("VALIDATION_DATA_FIELDNAME", $data['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE", $data['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL", $data['fieldlabel']);

global $adb;
// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if ($focus->mode != 'edit' && $mod_seq_field != null) {
	$autostr = getTranslatedString('MSG_AUTO_GEN_ON_SAVE');
	$mod_seq_string = $adb->pquery("SELECT prefix, cur_id from vtiger_modentity_num where semodule = ? and active=1", array (
		$currentModule
	));
	$mod_seq_prefix = $adb->query_result($mod_seq_string, 0, 'prefix');
	$mod_seq_no = $adb->query_result($mod_seq_string, 0, 'cur_id');
	if ($adb->num_rows($mod_seq_string) == 0 || $focus->checkModuleSeqNumber($focus->table_name, $mod_seq_field['column'], $mod_seq_prefix . $mod_seq_no))
		echo '<br><font color="#FF0000"><b>' . getTranslatedString('LBL_DUPLICATE') . ' ' . getTranslatedString($mod_seq_field['label']) .
		' - ' . getTranslatedString('LBL_CLICK') . ' <a href="index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings&selmodule=' . $currentModule . '">' . getTranslatedString('LBL_HERE') . '</a> ' . getTranslatedString('LBL_TO_CONFIGURE') . ' ' . getTranslatedString($mod_seq_field['label']) . '</b></font>';
	else
		$smarty->assign("MOD_SEQ_ID", $autostr);
} else {
	$smarty->assign("MOD_SEQ_ID", $focus->column_fields[$mod_seq_field['name']]);
}
// END

$smarty->assign("CURRENCIES_LIST", getAllCurrencies());
if ($focus->mode == 'edit') {
	$inventory_cur_info = getInventoryCurrencyInfo('Consignment', $focus->id);
	$smarty->assign("INV_CURRENCY_ID", $inventory_cur_info['currency_id']);
} else {
	$smarty->assign("INV_CURRENCY_ID", $currencyid);
}

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);
$smarty->assign("DUPLICATE",vtlib_purify($_REQUEST['isDuplicate']));

$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($currentModule);
$smarty->assign("PICKIST_DEPENDENCY_DATASOURCE", Zend_Json::encode($picklistDependencyDatasource));

// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));
// END

if ($focus->mode == 'edit')
	$smarty->display("Inventory/InventoryEditView.tpl");
else
	$smarty->display('Inventory/InventoryCreateView.tpl');

?>