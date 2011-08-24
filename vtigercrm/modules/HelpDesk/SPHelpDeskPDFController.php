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

class SalesPlatform_HelpDeskPDFController extends SalesPlatform_PDF_SPPDFController {

	function buildDocumentModel() {
	
		$model = parent::buildDocumentModel();
	
		$this->generateEntityModel($this->focus, 'HelpDesk', 'helpdesk_', $model);

           	$entity = new Products();
                if($this->focusColumnValue('product_id'))
            	    $entity->retrieve_entity_info($this->focusColumnValue('product_id'), 'Products');
                $this->generateEntityModel($entity, 'Products', 'product_', $model);

                if($this->focusColumnValue('parent_id'))
                    $setype = getSalesEntityType($this->focusColumnValue('parent_id'));

                $account = new Accounts();
                $contact = new Contacts();

                if($setype == 'Accounts')
           	    $account->retrieve_entity_info($this->focusColumnValue('parent_id'), $setype);
                elseif($setype == 'Contacts')
           	    $contact->retrieve_entity_info($this->focusColumnValue('parent_id'), $setype);

                $this->generateEntityModel($account, 'Accounts', 'account_', $model);
                $this->generateEntityModel($contact, 'Contacts', 'contact_', $model);

                $model->set('helpdesk_owner', getUserFullName($this->focusColumnValue('assigned_user_id')));

                return $model;
	}

}
?>
