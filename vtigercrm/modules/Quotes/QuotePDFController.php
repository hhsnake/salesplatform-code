<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
	
include_once 'include/InventoryPDFController.php';

class Vtiger_QuotePDFController extends Vtiger_InventoryPDFController{
	function buildHeaderModelTitle() {
// SkyAdmin begin	
		return sprintf("%s: %s", getTranslatedString('Quote #', $this->moduleName), $this->focusColumnValue('quote_no'));
//		return sprintf("%s: %s", rtrim($this->moduleName, 's'), $this->focusColumnValue('quote_no'));
// SkyAdmin end
	}

	function getWatermarkContent() {
		return $this->focusColumnValue('quotestatus');
	}

	function buildHeaderModelColumnRight() {
		$issueDateLabel = getTranslatedString('Issued Date', $this->moduleName);
		$validDateLabel = getTranslatedString('Valid Date', $this->moduleName);
		$billingAddressLabel = getTranslatedString('Billing Address', $this->moduleName);
		$shippingAddressLabel = getTranslatedString('Shipping Address', $this->moduleName);

		$modelColumn2 = array(
				'dates' => array(
					$issueDateLabel  => $this->formatDate(date("Y-m-d")),
					$validDateLabel => $this->formatDate($this->focusColumnValue('validtill')),
				),
				$billingAddressLabel  => $this->buildHeaderBillingAddress(),
				$shippingAddressLabel => $this->buildHeaderShippingAddress()
			);
		return $modelColumn2;
	}
}

?>
