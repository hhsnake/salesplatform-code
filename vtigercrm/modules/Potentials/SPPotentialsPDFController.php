<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'include/SalesPlatform/PDF/SPPDFController.php';

class SalesPlatform_PotentialsPDFController extends SalesPlatform_PDF_SPPDFController {

	function buildDocumentModel() {
	
		$model = parent::buildDocumentModel();
	
		$this->generateEntityModel($this->focus, 'Potentials', 'potential_', $model);

                if($this->focusColumnValue('related_to'))
                    $setype = getSalesEntityType($this->focusColumnValue('related_to'));

                $account = new Accounts();
                $contact = new Contacts();

                if($setype == 'Accounts')
           	    $account->retrieve_entity_info($this->focusColumnValue('related_to'), $setype);
                elseif($setype == 'Contacts')
           	    $contact->retrieve_entity_info($this->focusColumnValue('related_to'), $setype);

                $this->generateEntityModel($account, 'Accounts', 'account_', $model);
                $this->generateEntityModel($contact, 'Contacts', 'contact_', $model);

                $this->generateUi10Models($model);
                $this->generateRelatedListModels($model);

		$model->set('potential_no', $this->focusColumnValue('potential_no'));
                $model->set('potential_owner', getUserFullName($this->focusColumnValue('assigned_user_id')));

                return $model;
	}

}
?>
