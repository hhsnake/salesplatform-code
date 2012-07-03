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
include_once 'vtlib/Vtiger/PDF/models/Model.php';
include_once 'include/SalesPlatform/PDF/viewers/ProductListHeaderViewer.php';
include_once 'include/SalesPlatform/PDF/viewers/ProductListFooterViewer.php';
include_once 'include/SalesPlatform/PDF/viewers/ProductListContentViewer.php';
include_once 'vtlib/Vtiger/PDF/PDFGenerator.php';
include_once 'data/CRMEntity.php';

class SalesPlatform_PDF_ProductListDocumentPDFController extends
        SalesPlatform_PDF_SPPDFController {

        function loadRecord($id) {
                parent::loadRecord($id);
		$this->associated_products = getAssociatedProducts($this->moduleName,$this->focus);
	}
        
	function getContentViewer() {
		$contentViewer = new SalesPlatform_PDF_ProductListDocumentContentViewer($this->template, $this->pageOrientation);
		$contentViewer->setDocumentModel($this->buildDocumentModel());
		$contentViewer->setContentModels($this->buildContentModels());
		$contentViewer->setSummaryModel($this->buildSummaryModel());
		$contentViewer->setLabelModel($this->buildContentLabelModel());
		$contentViewer->setWatermarkModel($this->buildWatermarkModel());
		return $contentViewer;
	}

	function getHeaderViewer() {
		$headerViewer = new SalesPlatform_PDF_ProductListDocumentHeaderViewer($this->template, $this->headerSize);
		$headerViewer->setModel($this->buildDocumentModel());
		return $headerViewer;
	}

	function getFooterViewer() {
		$footerViewer = new SalesPlatform_PDF_ProductListDocumentFooterViewer($this->template, $this->footerSize);
		$footerViewer->setModel($this->buildFooterModel());
		$footerViewer->setLabelModel($this->buildFooterLabelModel());
		$footerViewer->setOnLastPage();
		return $footerViewer;
	}

	function Output($filename, $type) {
            parent::Output($filename, $type);
	}


	// Helper methods
	
	function buildContentModels() {
		$associated_products = $this->associated_products;
                $final_details = $associated_products[1]['final_details'];
                $contentModels = array();
		$productLineItemIndex = 0;
		$totaltaxes = 0;
		foreach($associated_products as $productLineItem) {
			++$productLineItemIndex;

			$contentModel = new Vtiger_PDF_Model();

			$discountPercentage  = 0.00;
			$total_tax_percent = 0.00;
			$producttotal_taxes = 0.00;
			$quantity = ''; $listPrice = ''; $discount = ''; $taxable_total = '';
			$tax_amount = ''; $producttotal = '';


			$quantity	= $productLineItem["qty{$productLineItemIndex}"];
			$usageunit	= $productLineItem["usageunit{$productLineItemIndex}"];
			$listPrice	= $productLineItem["listPrice{$productLineItemIndex}"];
			$discount	= $productLineItem["discountTotal{$productLineItemIndex}"];
			$taxable_total = $quantity * $listPrice - $discount;
			if($discount > 0 && $quantity > 0) {
			    $priceWithDiscount = $listPrice - $discount / $quantity;
			} else {
			    $priceWithDiscount = $listPrice;
			}
			$producttotal = $taxable_total;
			$priceWithTax = $priceWithDiscount;
			if($this->focus->column_fields["hdnTaxType"] == "individual") {
				for($tax_count=0;$tax_count<count($productLineItem['taxes']);$tax_count++) {
					$tax_percent = $productLineItem['taxes'][$tax_count]['percentage'];
					$total_tax_percent += $tax_percent;
					$tax_amount = (($taxable_total*$tax_percent)/100);
					$producttotal_taxes += $tax_amount;
					$priceWithTax += (($priceWithDiscount * $tax_percent)/100);
				}
                        } else {
                            // Recalculate tax when group mode is enabled
                            $group_tax_details = $final_details['taxes'];
                            $group_total_tax_percent = '0.00';
                            for($i=0;$i<count($group_tax_details);$i++) {
                                    $group_total_tax_percent += $group_tax_details[$i]['percentage'];
                            }
                            $total_tax_percent += $group_total_tax_percent;
                            $tax_amount = (($taxable_total*$group_total_tax_percent)/100);
                            $producttotal_taxes += $tax_amount;
                            $priceWithTax += (($priceWithDiscount * $group_total_tax_percent)/100);
                        }
			$producttotal = $taxable_total+$producttotal_taxes;
			$tax = $producttotal_taxes;
			$totaltaxes += $tax;
			$discountPercentage = $productLineItem["discount_percent{$productLineItemIndex}"];
			$productName = $productLineItem["productName{$productLineItemIndex}"];
			//get the sub product
            $subProducts = $productLineItem["subProductArray{$productLineItemIndex}"];
            if($subProducts != ''){
				foreach($subProducts as $subProduct) {
					$productName .="\n"." - ".decode_html($subProduct);
                    }
			}
        		$contentModel->set('productNumber', $productLineItemIndex);
            		$contentModel->set('productName', $productName);
			$contentModel->set('productCode', $productLineItem["hdnProductcode{$productLineItemIndex}"]);
			$contentModel->set('productQuantity', $this->formatNumber($quantity, 3));
			$contentModel->set('productQuantityInt', $this->formatNumber($quantity, 0));
			$contentModel->set('productUnits', getTranslatedString($usageunit, 'Products'));
			$contentModel->set('productUnitsCode', $productLineItem["unitCode{$productLineItemIndex}"]);
			$contentModel->set('productPrice',     $this->formatPrice($priceWithDiscount));
			$contentModel->set('productPriceWithTax', $this->formatPrice($priceWithTax));
			$contentModel->set('productDiscount',  $this->formatPrice($discount)."\n ($discountPercentage%)");
			$contentModel->set('productNetTotal',  $this->formatPrice($taxable_total));
			$contentModel->set('productTax',       $this->formatPrice($tax));
			$contentModel->set('productTaxPercent', $total_tax_percent);
			$contentModel->set('productTotal',     $this->formatPrice($producttotal));
			$contentModel->set('productDescription',   nl2br($productLineItem["productDescription{$productLineItemIndex}"]));
			$contentModel->set('productComment',   nl2br($productLineItem["comment{$productLineItemIndex}"]));
			$contentModel->set('manufCountry', $productLineItem["manufCountry{$productLineItemIndex}"]);
			$contentModel->set('manufCountryCode', $productLineItem["manufCountryCode{$productLineItemIndex}"]);
			$contentModel->set('customsId', $productLineItem["customsId{$productLineItemIndex}"]);

			$contentModels[] = $contentModel;
		}
		$this->totaltaxes = $totaltaxes; //will be used to add it to the net total
		
		return $contentModels;
	}


	function buildSummaryModel() {
		$associated_products = $this->associated_products;
		$final_details = $associated_products[1]['final_details'];

		$summaryModel = new Vtiger_PDF_Model();

		$netTotal = $discount = $handlingCharges =  $handlingTaxes = 0;
		$adjustment = $grandTotal = 0;

		$productLineItemIndex = 0;
		$sh_tax_percent = 0;
		foreach($associated_products as $productLineItem) {
			++$productLineItemIndex;
			$netTotal += $productLineItem["netPrice{$productLineItemIndex}"];
		}

		$summaryModel->set("summaryNetTotal", $this->formatPrice($netTotal));
		
		$discount_amount = $final_details["discount_amount_final"];
		$discount_percent = $final_details["discount_percentage_final"];

		$discount = 0.0;
		if(!empty($discount_amount)) {
			$discount = $discount_amount;
		} else if(!empty($discount_percent)) {
			$discount = (($discount_percent*$final_details["hdnSubTotal"])/100);
		}
		$summaryModel->set("summaryDiscount", $this->formatPrice($discount));
		
		$group_total_tax_percent = '0.00';
                $overall_tax = 0;
		//To calculate the group tax amount
		if($final_details['taxtype'] == 'group') {
			$group_tax_details = $final_details['taxes'];
			for($i=0;$i<count($group_tax_details);$i++) {
				$group_total_tax_percent += $group_tax_details[$i]['percentage'];
			}
			$summaryModel->set("summaryTax", $this->formatPrice($final_details['tax_totalamount']));
			$summaryModel->set("summaryTaxLiteral", $this->num2str($final_details['tax_totalamount']));
			$summaryModel->set("summaryTaxPercent", $group_total_tax_percent);
                        $overall_tax += $final_details['tax_totalamount'];
		}
		else {
		    $summaryModel->set("summaryTax", $this->formatPrice($this->totaltaxes));
    		    $summaryModel->set("summaryTaxLiteral", $this->num2str($this->totaltaxes));
		    if($netTotal > 0) {
			$summaryModel->set("summaryTaxPercent", $this->totaltaxes / $netTotal * 100);
		    }
		    else {
			$summaryModel->set("summaryTaxPercent", 0);
		    }

                    $overall_tax += $this->totaltaxes;
		}
		//Shipping & Handling taxes
		$sh_tax_details = $final_details['sh_taxes'];
		for($i=0;$i<count($sh_tax_details);$i++) {
			$sh_tax_percent = $sh_tax_percent + $sh_tax_details[$i]['percentage'];
		}
		//obtain the Currency Symbol
		$currencySymbol = $this->buildCurrencySymbol();
		
		$summaryModel->set("summaryShipping", $this->formatPrice($final_details['shipping_handling_charge']));
		$summaryModel->set("summaryShippingTax", $this->formatPrice($final_details['shtax_totalamount']));
		$summaryModel->set("summaryShippingTaxPercent", $sh_tax_percent);
		$summaryModel->set("summaryAdjustment", $this->formatPrice($final_details['adjustment']));
		$summaryModel->set("summaryGrandTotal", $this->formatPrice($final_details['grandTotal'])); // TODO add currency string

		$summaryModel->set("summaryGrandTotalLiteral", $this->num2str($final_details['grandTotal']));

                $overall_tax += $final_details['shtax_totalamount'];
		$summaryModel->set("summaryOverallTax", $this->formatPrice(round($overall_tax)));
		$summaryModel->set("summaryOverallTaxLiteral", $this->num2str(round($overall_tax)));
		
		return $summaryModel;
	}



}
?>