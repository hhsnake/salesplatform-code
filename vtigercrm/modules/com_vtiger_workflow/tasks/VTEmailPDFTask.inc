<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTEmailRecipientsTemplate.inc');
require_once('modules/Emails/mail.php');

include_once 'modules/SalesOrder/SPSalesOrderPDFController.php';
include_once 'modules/Invoice/SPInvoicePDFController.php';
include_once 'modules/Quotes/SPQuotePDFController.php';
include_once 'modules/HelpDesk/SPHelpDeskPDFController.php';

class VTEmailPDFTask extends VTTask{
	// Sending email takes more time, this should be handled via queue all the time.
	public $executeImmediately = true;

	public function getFieldNames(){
		return array("subject", "content", "recepient", 'emailcc', 'emailbcc', 'att_template');
	}
	public function doTask($entity){
		global $adb, $current_user;
		$util = new VTWorkflowUtils();

		$result = $adb->query("select user_name, email1, email2 from vtiger_users where id=1");
		$from_email = $adb->query_result($result,0,'email1');
		$from_name  = $adb->query_result($result,0,'user_name');

		$admin = $util->adminUser();
		$module = $entity->getModuleName();

		$entityCache = new VTEntityCache($admin);

		$et = new VTEmailRecipientsTemplate($this->recepient);
		$to_email = $et->render($entityCache, $entity->getId());
		$ecct = new VTEmailRecipientsTemplate($this->emailcc);
		$cc = $ecct->render($entityCache, $entity->getId());
		$ebcct = new VTEmailRecipientsTemplate($this->emailbcc);
		$bcc = $ebcct->render($entityCache, $entity->getId());
		if(strlen(trim($to_email, " \t\n,")) == 0 && strlen(trim($cc, " \t\n,")) == 0 &&
				strlen(trim($bcc, " \t\n,")) == 0) {
			return ;
		}

		$st = new VTSimpleTemplate($this->subject);
		$subject = $st->render($entityCache, $entity->getId());
		$ct = new VTSimpleTemplate($this->content);
		$content = $ct->render($entityCache, $entity->getId());

                // Attach PDF
                $attachment = '';
                $ws_id = $entity->getId();
                if ($ws_id > 0 &&
                        in_array($module, array('SalesOrder', 'Invoice', 'Quotes', 'HelpDesk'))) {
                    // Get the CRMEntity id
                    $relatedCRMid = substr($ws_id, stripos($ws_id, 'x') + 1);
                    if ($relatedCRMid > 0) {

                        switch($module) {
                            case 'SalesOrder':
                                $controller = new SalesPlatform_SalesOrderPDFController('SalesOrder', $this->att_template);
                                break;
                            case 'Invoice':
                                $controller = new SalesPlatform_InvoicePDFController('Invoice', $this->att_template);
                                break;
                            case 'Quotes':
                                $controller = new SalesPlatform_QuotePDFController('Quotes', $this->att_template);
                                break;
                            case 'HelpDesk':
                                $controller = new SalesPlatform_HelpDeskPDFController('HelpDesk', $this->att_template);
                                break;
                        }

                        $controller->loadRecord($relatedCRMid);

                        $filenameid = $relatedCRMid;
                        if(empty($filenameid)) $filenameid = time();
                        $filepath="test/upload/".$module."_$filenameid.pdf";
                        //added file name to make it work in IE, also forces the download giving the user the option to save
                        $controller->Output($filepath,'F');
                        $attachment = $module."_$filenameid.pdf";
                    }
                }

		send_mail($module,$to_email,$from_name,$from_email,$subject,$content, $cc, $bcc, $attachment);

		$util->revertUser();
	}
}
?>