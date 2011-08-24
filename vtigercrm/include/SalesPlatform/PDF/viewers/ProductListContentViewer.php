<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'include/SalesPlatform/PDF/viewers/SPContentViewer.php';

class SalesPlatform_PDF_ProductListDocumentContentViewer extends SalesPlatform_PDF_SPContentViewer {

	function display($parent) {

		$models = $this->contentModels;

		$totalModels = count($models);
		$pdf = $parent->getPDF();
		$pdf->setPageOrientation($this->orientation);
		$pdf->SetAutoPageBreak(true, 10);
		
		$parent->createPage();
		$contentFrame = $parent->getContentFrame();
		
		try {
		    $template = new Aste_Template($this->template);

		    $table_head = $template->getBlock('table_head');
		    $content = $table_head->fetch();

		    for ($index = 0; $index < $totalModels; ++$index) {
			$model = $models[$index];
			
			$contentHeight = 1;

			$table_row = $template->getBlock('table_row', true);
			foreach($model->keys() as $key) {
    			    $table_row->setVar($key, $model->get($key));
    			}
			$content .= $table_row->fetch();
			
		    }
		
		
		    $summary = $template->getBlock('summary');
		    foreach($this->contentSummaryModel->keys() as $key) {
    		        $summary->setVar($key, $this->contentSummaryModel->get($key));
    		    }
    		    $summary->setVar('summaryTotalItems', $totalModels);
		    $content .= $summary->fetch();

		    $ending = $template->getBlock('ending');
		    foreach($this->documentModel->keys() as $key) {
    		        $ending->setVar($key, $this->documentModel->get($key));
    		    }
		    $content .= $ending->fetch();

		    $pdf->writeHTMLCell(0, 0, $contentFrame->x, $contentFrame->y, $content);
		} catch(Aste_Exception $e) {
		}

	}

}