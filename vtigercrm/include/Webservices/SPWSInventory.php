<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPWSInventory {
  
    public function SPWSInventory() {
        
    }
    
    public function spws_inventoryCreate($elements,$instance) {
        global $adb;
        if (!isset($elements['inventories'])) {
            return array('isSelect' => false,'error' => 'NOT SET');
        } 
        if (!is_array($elements['inventories'])) {
            return array('isSelect' => false,'error' => 'inventories should be an array');
        }
        if (!isset($elements['inventories']['Global'])) {
            return array('isSelect' => false,'error' => 'Global are not set');
        } 
        if (!is_array($elements['inventories']['Global'])) {
            return array('isSelect' => false,'error' => 'Global should be an array');
        }
        if (!isset($elements['inventories']['Products'])) {
            return array('isSelect' => false,'error' => 'Products are not set');
        } 
        if (!is_array($elements['inventories']['Products'])) {
            return array('isSelect' => false,'error' => 'Products should be an array');
        }
        $productElementNum = 1;
        foreach($elements['inventories']['Products'] as $invProductElement) {
            if (!isset($invProductElement)) {
                return array('isSelect' => false,'error' => 'Product Element №'.$productElementNum.' is not set');
            } 
            if (!is_array($invProductElement)) {
                return array('isSelect' => false,'error' => 'Product Element №'.$productElementNum.' should be an array');
            }
            $productElementNum++;
        } 
        
        
        $productCrmid_res = $adb->pquery("select crmid from vtiger_crmentity where setype='Products' or setype='Services'",array());
        $productCrmid_numrows = $adb->num_rows($productCrmid_res);
        $productCrmid_arr = array();
        for($i = 0; $i < $productCrmid_numrows; $i++) {
            array_push($productCrmid_arr,$adb->query_result($productCrmid_res,$i,"crmid"));
        }
        if (count($productCrmid_arr) == 0) {
            return array('isSelect' => false,'error' => 'There are no entities for Products and Services in the database');
        }
        
        $shtax_res = $adb->pquery("select taxid,percentage from vtiger_shippingtaxinfo where deleted='0'",array());
        $shtax_info_arr = array();
        while($shtax_info = $adb->fetch_array($shtax_res)) {
            $shtax_info_arr[$shtax_info['taxid']] = $shtax_info['percentage'];
        }
        
        $productTaxRel_res = $adb->pquery("select * from vtiger_producttaxrel",array());
        $productTaxRel_arr = array();
        while($productTaxRel_rowData = $adb->fetch_array($productTaxRel_res)) {
            $productTaxRel_subarr = array();
            $productTaxRel_subarr['productid'] = $productTaxRel_rowData['productid'];
            $productTaxRel_subarr['taxid'] = $productTaxRel_rowData['taxid'];
            $productTaxRel_subarr['taxpercentage'] = $productTaxRel_rowData['taxpercentage'];
            array_push($productTaxRel_arr,$productTaxRel_subarr);
        }
        
        $allTax_res = $adb->pquery("select * from vtiger_inventorytaxinfo where deleted='0'",array());
        $allTax_arr = array();
        while($allTax_rowData = $adb->fetch_array($allTax_res)) {
            $allTax_subarr = array();
            $allTax_subarr['taxname'] = $allTax_rowData['taxname'];
            $allTax_subarr['taxlabel'] = $allTax_rowData['taxlabel'];
            $allTax_subarr['percentage'] = $allTax_rowData['percentage'];
            $allTax_arr[$allTax_rowData['taxid']] = $allTax_subarr;
        }
        
        
        $invGlobalElementsArr = $elements['inventories']['Global'];
        if (isset($invGlobalElementsArr['inventory_currency'])) {
            $all_currency_id_res = $adb->pquery('SELECT id FROM vtiger_currency_info', array());
	    $all_currency_id = array();
            for($i = 0;$i < $adb->num_rows($all_currency_id_res); $i++) {
                array_push($all_currency_id,$adb->query_result($all_currency_id_res, $i, 'id'));
            }
            if (!in_array($invGlobalElementsArr['inventory_currency'],$all_currency_id)) {
                return array('isSelect' => false,'error' => 'inventory_currency id from global elements dont set in the database');
            } else {
                $_REQUEST['inventory_currency'] = $invGlobalElementsArr['inventory_currency'];
            }
        } else {
            $currency_default_id_res = $adb->pquery('SELECT id FROM vtiger_currency_info WHERE defaultid < 0', array());
	    $inv_currency = 1;
            if($adb->num_rows($currency_default_id_res) > 0) {
	        $inv_currency = $adb->query_result($currency_default_id_res, 0, 'id');
	    }
            
            $_REQUEST['inventory_currency'] = $inv_currency;
        }
        if (isset($invGlobalElementsArr['taxtype'])) {
            if ($invGlobalElementsArr['taxtype'] == "individual" || $invGlobalElementsArr['taxtype'] == "group") {
                $_REQUEST['taxtype'] = $invGlobalElementsArr['taxtype'];                
            } else {
                return array('isSelect' => false,'error' => 'Element taxtype from global elements has a wrong value');
            }
        } else {
            $_REQUEST['taxtype'] = 'group';
        }
        
        
        
        $allProductsTotal = 0;       
        $productElementNum = 0;
        foreach ($elements['inventories']['Products'] as $invProductElementArr) {
            $productElementNum++;
            $productTotal = 0;
            
            if (isset($invProductElementArr['hdnProductId'])) {
                if (!in_array($invProductElementArr['hdnProductId'],$productCrmid_arr)) {
                    return array('isSelect' => false,'error' => 'hdnProductId in product element №'.$productElementNum.' has a wrong value');
                } else {
                    $_REQUEST['hdnProductId'.$productElementNum] = $invProductElementArr['hdnProductId'];
                }
            } else {
                return array("isSelect" => false,'error' => 'The mandatory field hdnProductId in product element №'.$productElementNum.' is not set');
            }
            if (isset($invProductElementArr['qty'])) {
                if (is_numeric($invProductElementArr['qty'])) {
                    if (strpos($invProductElementArr['qty'],'.') || $invProductElementArr['qty'] < 0) {
                        return array('isSelect' => false,'error' => 'qty in product element №'.$productElementNum.' should be an integer and have a positive value');
                    } else {
                        $_REQUEST['qty'.$productElementNum] = $invProductElementArr['qty'];
                    }
                } else {
                    return array('isSelect' => false,'error' => 'qty value in product element №'.$productElementNum.' should be an integer');
                }
            } else {
                return array("isSelect" => false,'error' => 'The mandatory field qty in product element №'.$productElementNum.' is not set');
            }
            if (isset($invProductElementArr['productDescription'])) {
                $_REQUEST['productDescription'.$productElementNum] = $invProductElementArr['productDescription'];
            } else {
                $_REQUEST['productDescription'.$productElementNum] = '';
            }
            if (isset($invProductElementArr['comment'])) {
                $_REQUEST['comment'.$productElementNum] = $invProductElementArr['comment'];
            } else {
                $_REQUEST['comment'.$productElementNum] = '';
            }
            if (isset($invProductElementArr['listPrice'])) {
                if (!is_numeric($invProductElementArr['listPrice']) || $invProductElementArr['listPrice'] < 0) {
                    return array('isSelect' => false,'error' => 'listPrice value in product element №'.$productElementNum.' should be an integer and have a positive value');
                } else {
                    $_REQUEST['listPrice'.$productElementNum] = $invProductElementArr['listPrice'];
                }
            } else {
                $_REQUEST['listPrice'.$productElementNum] = 0;
            }
            $productTotal = $_REQUEST['listPrice'.$productElementNum] * $_REQUEST['qty'.$productElementNum];
            
            $discountProduct = 0;
            if (isset($invProductElementArr['discount_type'])) {
                $discount_type = $invProductElementArr['discount_type'];
                if ($discount_type == 'zero' || $discount_type == 'percentage' || $discount_type == 'amount') {
                    if ($discount_type == 'zero') {
                        $_REQUEST['discount_type'.$productElementNum] = 'zero';
                    }
                    if ($discount_type == 'percentage') {
                        if (isset($invProductElementArr['discount_percentage'])) {
                            if (is_numeric($invProductElementArr['discount_percentage']) && $invProductElementArr['discount_percentage'] >= 0) {
                                $_REQUEST['discount_type'.$productElementNum] = 'percentage';
                                $_REQUEST['discount_percentage'.$productElementNum] = $invProductElementArr['discount_percentage'];
                            } else {
                                return array('isSelect' => false,'error' => 'discount_percentage value in product element №'.$productElementNum.' should be a numeric and have a positive value');
                            }
                        } else {
                            $_REQUEST['discount_type'.$productElementNum] = 'percentage';
                            $_REQUEST['discount_percentage'.$productElementNum] = 0;
                        }
                        
                        $discountProduct = $productTotal * $_REQUEST['discount_percentage'.$productElementNum] / 100;
                    }
                    if ($discount_type == 'amount') {
                        if (isset($invProductElementArr['discount_amount'])) {
                            if (is_numeric($invProductElementArr['discount_amount']) && $invProductElementArr['discount_amount'] >= 0) {
                                $_REQUEST['discount_type'.$productElementNum] = 'amount';
                                $_REQUEST['discount_amount'.$productElementNum] = $invProductElementArr['discount_amount'];
                            } else {
                                return array('isSelect' => false,'error' => 'discount_amount value in product element №'.$productElementNum.' should be a numeric and have a positive value');
                            }
                        } else {
                            $_REQUEST['discount_type'.$productElementNum] = 'amount';
                            $_REQUEST['discount_amount'.$productElementNum] = 0;
                        }
                        
                        $discountProduct = $_REQUEST['discount_amount'.$productElementNum];
                    }
                } else {
                    return array('isSelect' => false,'error' => 'discount_type in product element №'.$productElementNum.'  has a wrong value (this field can take zero, percentage or amount values)');
                }
            }
            $productTotal -= $discountProduct;
            
            $taxProduct = 0;
            if($_REQUEST['taxtype'] == "individual") {
                foreach ($productTaxRel_arr as $productTaxInfo) {
                    if ($productTaxInfo['productid'] == $_REQUEST['hdnProductId'.$productElementNum]) {
                        $prod_tax_fieldname = 'tax'.$productTaxInfo['taxid'].'_percentage';
                        if (isset($invProductElementArr[$prod_tax_fieldname])) {
                            if (is_numeric($invProductElementArr[$prod_tax_fieldname]) && $invProductElementArr[$prod_tax_fieldname] >= 0) {
                                $_REQUEST[$prod_tax_fieldname.$productElementNum] = $invProductElementArr[$prod_tax_fieldname];
                            } else {
                                return array('isSelect' => false,'error' => ''.$prod_tax_fieldname.' value in product element №'.$productElementNum.' should be a numeric and have a positive value');
                            }
                        } else {
                            $_REQUEST[$prod_tax_fieldname.$productElementNum] = $productTaxInfo['taxpercentage'];
                        }
                        
                        $taxProduct += $productTotal * $_REQUEST[$prod_tax_fieldname.$productElementNum] / 100;
                    }
                }
            }
            $productTotal += $taxProduct;
            
            $allProductsTotal += $productTotal;
        }
        

        $grandTotal = $allProductsTotal;
        
        $discountGlobal = 0;
        if (isset($invGlobalElementsArr['discount_type_final'])) {
            $discount_type_final = $invGlobalElementsArr['discount_type_final'];
            if ($discount_type_final == 'zero' || $discount_type_final == 'percentage' || $discount_type_final == 'amount') {
                if ($discount_type_final == 'zero') {
                    $_REQUEST['discount_type_final'] = 'zero';
                }
                if ($discount_type_final == 'percentage') {
                    if (isset($invGlobalElementsArr['discount_percentage_final'])) {
                        if (is_numeric($invGlobalElementsArr['discount_percentage_final']) && $invGlobalElementsArr['discount_percentage_final'] >= 0) {
                            $_REQUEST['discount_type_final'] = 'percentage';
                            $_REQUEST['discount_percentage_final'] = $invGlobalElementsArr['discount_percentage_final'];
                        } else {
                            return array('isSelect' => false,'error' => 'discount_percentage_final value from global elements should be a numeric and have a positive value');
                        }
                    } else {
                        $_REQUEST['discount_type_final'] = 'percentage';
                        $_REQUEST['discount_percentage_final'] = 0;
                    }
                    
                    $discountGlobal = $allProductsTotal * $_REQUEST['discount_percentage_final'] / 100;
                }
                if ($discount_type_final == 'amount') {
                    if (isset($invGlobalElementsArr['discount_amount_final'])) {
                        if (is_numeric($invGlobalElementsArr['discount_amount_final']) && $invGlobalElementsArr['discount_amount_final'] >= 0) {
                                $_REQUEST['discount_type_final'] = 'amount';
                                $_REQUEST['discount_amount_final'] = $invGlobalElementsArr['discount_amount_final'];
                        } else {
                            return array('isSelect' => false,'error' => 'discount_amount value from global elements should be a numeric and have a positive value');
                        }
                    } else {
                            $_REQUEST['discount_type_final'] = 'amount';
                            $_REQUEST['discount_amount_final'] = 0;
                    }
                    
                    $discountGlobal = $_REQUEST['discount_amount_final'];
                }
            } else {
                return array('isSelect' => false,'error' => 'discount_type_final from global elements has a wrong value (this field can take zero, percentage or amount values)');
            }
        }
        $grandTotal -= $discountGlobal;
        
        $taxGlobal = 0;
        if($_REQUEST['taxtype'] == "group") {
            foreach ($allTax_arr  as $taxInfo) {
                $tax_fieldname = $taxInfo['taxname'].'_group_percentage';
                if (isset($invGlobalElementsArr[$tax_fieldname])) {
                    if (is_numeric($invGlobalElementsArr[$tax_fieldname]) && $invGlobalElementsArr[$tax_fieldname] >= 0) {
                        $_REQUEST[$tax_fieldname] = $invGlobalElementsArr[$tax_fieldname];
                    } else {
                        return array('isSelect' => false,'error' => ''.$tax_fieldname.' value from global elements should be a numeric and have a positive value');
                    }
                } else {
                    $_REQUEST[$tax_fieldname] = $taxInfo['percentage'];
                } 
                
                $taxGlobal += $grandTotal * $_REQUEST[$tax_fieldname] / 100;
            }
        }
        $grandTotal += $taxGlobal;
        
        if (isset($invGlobalElementsArr['shipping_handling_charge'])) {
            if (!is_numeric($invGlobalElementsArr['shipping_handling_charge']) || $invGlobalElementsArr['shipping_handling_charge'] < 0) {
                return array('isSelect' => false,'error' => 'shipping_handling_charge value from global elements should be an integer and have a positive value');
            } else {
                $_REQUEST['shipping_handling_charge'] = $invGlobalElementsArr['shipping_handling_charge'];
            }
        } else {
            $_REQUEST['shipping_handling_charge'] = 0;
        }
        $grandTotal += $_REQUEST['shipping_handling_charge'];
        
        $shtaxGlobal = 0;
        foreach ($shtax_info_arr as $shtax_id => $shtax_percentage) {
            $shtax_fieldname = 'shtax'.$shtax_id.'_sh_percent';
            if (isset($invGlobalElementsArr[$shtax_fieldname])) {
                if (is_numeric($invGlobalElementsArr[$shtax_fieldname]) && $invGlobalElementsArr[$shtax_fieldname] > 0) {
                    $_REQUEST[$shtax_fieldname] = $invGlobalElementsArr[$shtax_fieldname];
                } else {
                    return array('isSelect' => false,'error' => ''.$shtax_fieldname.' value from global elements should be a numeric and have a positive value');
                }
            } else {
                $_REQUEST[$shtax_fieldname] = $shtax_percentage;
            } 
            
            $shtaxGlobal += $_REQUEST['shipping_handling_charge'] * $_REQUEST[$shtax_fieldname] / 100;
        }
        $grandTotal += $shtaxGlobal;
        
        if (isset($invGlobalElementsArr['adjustmentType'])) {
            if ($invGlobalElementsArr['adjustmentType'] == "+" || $invGlobalElementsArr['adjustmentType'] == "-") {
                $_REQUEST['adjustmentType'] = $invGlobalElementsArr['adjustmentType'];                
            } else {
                return array('isSelect' => false,'error' => 'Element adjustmentType from global elements has a wrong value');
            }
        } else {
            $_REQUEST['adjustmentType'] = '+';
        }
        if (isset($invGlobalElementsArr['adjustment'])) {
            if (!is_numeric($invGlobalElementsArr['adjustment']) || $invGlobalElementsArr['adjustment'] < 0) {
                return array('isSelect' => false,'error' => 'adjustment value from global elements should be an integer and have a positive value');
            } else {
                $_REQUEST['adjustment'] = $invGlobalElementsArr['adjustment'];
            }
        } else {
            $_REQUEST['adjustment'] = 0;
        }
        

        if($_REQUEST['adjustmentType'] == '+') {
            $grandTotal += $_REQUEST['adjustment'];
        } else {
            $grandTotal -= $_REQUEST['adjustment'];
        }
        
       
        $_REQUEST['subtotal'] = $allProductsTotal;
        $_REQUEST['total'] = $grandTotal;
        $_REQUEST['totalProductCount'] = $productElementNum;
        
        require_once('include/utils/InventoryUtils.php');
        $instance->column_fields['currency_id'] = $_REQUEST['inventory_currency'];
        $cur_sym_rate = getCurrencySymbolandCRate($_REQUEST['inventory_currency']);
        $instance->column_fields['conversion_rate'] = $cur_sym_rate['rate'];
        
        return array('isSelect' => true,'error' => '');
        
    }
    
    public function spws_inventoryRetrieve($crmId,$data) {
        global $adb;
        $inventories = array();
        
        $productsArr = array();
        $productsData_res = $adb->pquery("select * from vtiger_inventoryproductrel where id='$crmId' order by sequence_no ASC",array());
        $productsData_Numrows = $adb->num_rows($productsData_res);
        if ($productsData_Numrows > 0) {
            $tax_query = "select taxinfo.taxname,taxrel.productid from vtiger_producttaxrel as taxrel left join vtiger_inventorytaxinfo as taxinfo on taxrel.taxid=taxinfo.taxid where";
            $prodInfo_query = "select productid,productcode,qtyinstock from vtiger_products where";
            $servInfo_query = "select serviceid,service_no from vtiger_service where";
            $prodIdArr = array();
            for ($i = 0; $i < $productsData_Numrows; $i++) {
                $prodId = $adb->query_result($productsData_res,$i,'productid');
                array_push($prodIdArr,$prodId);
            }
            array_unique($prodIdArr);
            for ($i = 0; $i < count($prodIdArr); $i++) {
                if ($i == 0) {
                    $tax_query .= " taxrel.productid='$prodIdArr[0]'";
                    $prodInfo_query .= " productid='$prodIdArr[0]'";
                    $servInfo_query .= " serviceid='$prodIdArr[0]'";
                } else {
                    $tax_query .= " or taxrel.productid='$prodIdArr[$i]'";
                    $prodInfo_query .= " or productid='$prodIdArr[$i]'";
                    $servInfo_query .= " or serviceid='$prodIdArr[$i]'";
                }
            }
            $prodInfo_res = $adb->pquery($prodInfo_query,array());
            $prodInfo_arr = array();
            while($prodInfo_rowData = $adb->fetch_array($prodInfo_res)) {
                $prodInfo_subarr = array();
                $prodInfo_subarr['productcode'] = $prodInfo_rowData['productcode'];
                $prodInfo_subarr['qtyinstock'] = $prodInfo_rowData['qtyinstock'];
                $prodInfo_arr[$prodInfo_rowData['productid']] = $prodInfo_subarr;
            }
            $servInfo_res = $adb->pquery($servInfo_query,array());
            $servInfo_arr = array();
            while($servInfo_rowData = $adb->fetch_array($servInfo_res)) {
               $servInfo_subarr = array();
                $servInfo_subarr['service_no'] = $servInfo_rowData['service_no'];
                $servInfo_arr[$servInfo_rowData['serviceid']] = $servInfo_subarr;
            }
            $taxInfo_arr = array();
            if ($data['hdnTaxType'] == 'individual') {
                $taxInfo_res = $adb->pquery($tax_query,array());
                while($taxInfo_rowData = $adb->fetch_array($taxInfo_res)) {
                    if (!isset($taxInfo_arr[$taxInfo_rowData['productid']])) {
                        $taxInfo_subarr = array();
                        array_push($taxInfo_subarr,$taxInfo_rowData['taxname']);
                        $taxInfo_arr[$taxInfo_rowData['productid']] = $taxInfo_subarr;
                    } else {
                        array_push($taxInfo_arr[$taxInfo_rowData['productid']],$taxInfo_rowData['taxname']);
                    }
                }
            }
            
            $productsData_res->MoveFirst();
            while($product_rowData = $adb->fetch_array($productsData_res)) {
                
                $productData_subarr = array();
                $productTotal = 0;
                if (isset($prodInfo_arr[$product_rowData['productid']])) {
                    $productData_subarr['productName'] = $prodInfo_arr[$product_rowData['productid']]['productcode'];
                    $productData_subarr['qtyInStock'] = $prodInfo_arr[$product_rowData['productid']]['qtyinstock'];
                } elseif (isset($servInfo_arr[$product_rowData['productid']])) {
                    $productData_subarr['productName'] = $servInfo_arr[$product_rowData['productid']]['service_no'];
                    $productData_subarr['qtyInStock'] = 'NA';
                }
                $productData_subarr['hdnProductId'] = $product_rowData['productid'];
                $productData_subarr['productDescription'] = $product_rowData['description'];                
                $productData_subarr['comment'] = $product_rowData['comment'];
                $productData_subarr['qty'] = $product_rowData['quantity'];
                $productData_subarr['listPrice'] = $product_rowData['listprice'];
                $productTotal = $product_rowData['listprice'] * $product_rowData['quantity'];
                $productData_subarr['productTotal'] = $productTotal;
                if ($product_rowData['discount_percent'] == 0 && $product_rowData['discount_amount'] == 0) {
                    $productData_subarr['discount_type'] = 'zero';
                } elseif ($product_rowData['discount_percent'] != 0) {
                    $productData_subarr['discount_type'] = 'percentage';
                } elseif ($product_rowData['discount_amount'] != 0) {
                    $productData_subarr['discount_type'] = 'amount';
                }
                $productData_subarr['discount_percentage'] = $product_rowData['discount_percent'];
                $productData_subarr['discount_amount'] = $product_rowData['discount_amount'];
                $productData_subarr['discountTotal'] = $productTotal * $product_rowData['discount_percent'] / 100 + $product_rowData['discount_amount'];
                $productData_subarr['totalAfterDiscount'] = $productTotal - $productData_subarr['discountTotal'];
                $productTotal = $productData_subarr['totalAfterDiscount'];
                if ($data['hdnTaxType'] == 'individual') {
                    $prodTaxArr = $taxInfo_arr[$product_rowData['productid']];
                    $taxProduct = 0;
                    for($i = 0; $i < count($prodTaxArr); $i++) {
                        $tax_fieldname = $prodTaxArr[$i].'_percentage';
                        $productData_subarr[$tax_fieldname] = $product_rowData[$prodTaxArr[$i]];
                        $taxProduct += $productData_subarr['totalAfterDiscount'] * $product_rowData[$prodTaxArr[$i]] / 100;
                    }
                    $productData_subarr['taxTotal'] = $taxProduct;
                    $productTotal += $taxProduct;
                }
                $productData_subarr['netPrice'] = $productTotal;
                array_push($productsArr,$productData_subarr);
            }
        }
        
        
        $globalArr = array();
        $globalArr['inventory_currency'] = $data['currency_id'];
        $globalArr['taxtype'] = $data['hdnTaxType'];
        $globalArr['netTotal'] = $data['hdnSubTotal'];
        if ($data['hdnDiscountPercent'] == 0 && $data['hdnDiscountAmount'] == 0) {
            $globalArr['discount_type_final'] = 'zero';
        } elseif ($data['hdnDiscountPercent'] != 0) {
            $globalArr['discount_type_final'] = 'percentage';
        } elseif ($data['hdnDiscountAmount'] != 0) {
            $globalArr['discount_type_final'] = 'amount';
        }
        $globalArr['discount_percentage_final'] = $data['hdnDiscountPercent'];
        $globalArr['discount_amount_final'] = $data['hdnDiscountAmount'];
        $globalArr['discountTotal_final'] = $data['hdnSubTotal'] * $data['hdnDiscountPercent'] / 100 + $data['hdnDiscountAmount'];
           
        if ($data['hdnTaxType'] == 'group') {
            $tax_res = $adb->pquery("select * from vtiger_inventorytaxinfo",array());
            $taxGlobal = 0;
            if ($productsData_Numrows > 0) {
                while($tax_info = $adb->fetch_array($tax_res)) {
                    $globalTaxValue = $adb->query_result($productsData_res,0,$tax_info['taxname']);
                    if ($globalTaxValue != NULL) {
                        $tax_fieldname = $tax_info['taxname'].'_group_percentage';
                        $globalArr[$tax_fieldname] = $globalTaxValue;
                        $taxGlobal += $data['hdnSubTotal'] * $globalTaxValue / 100;
                    }
                }
            } else {
                while($tax_info = $adb->fetch_array($tax_res)) {
                    $tax_fieldname = $tax_info['taxname'].'_group_percentage';
                    $globalArr[$tax_fieldname] = $tax_info['percentage'];
                    $taxGlobal += $data['hdnSubTotal'] * $globalArr[$tax_fieldname] / 100;
                }
            }
            $globalArr['tax_final'] = $taxGlobal;
        }
        
        $globalArr['shipping_handling_charge'] = $data['hdnS_H_Amount'];
           
        $shtaxrel_res = $adb->pquery("select * from vtiger_inventoryshippingrel where id='$crmId'",array());
        $shtaxrel_data = $adb->fetch_array($shtaxrel_res);
        $shtax_res = $adb->pquery("select taxname from vtiger_shippingtaxinfo",array());
        $shtaxGlobal = 0;
        while($shtax_info = $adb->fetch_array($shtax_res)) {
            if ($shtaxrel_data[$shtax_info['taxname']] != NULL) {
                $shtax_fieldname = $shtax_info['taxname'].'_sh_percent';
                $globalArr[$shtax_fieldname] = $shtaxrel_data[$shtax_info['taxname']];
                $shtaxGlobal += $data['hdnS_H_Amount'] * $shtaxrel_data[$shtax_info['taxname']] / 100;
            }
        }
        $globalArr['shipping_handling_tax'] = $shtaxGlobal;

        $globalArr['adjustment'] = $data['txtAdjustment'];
        if ($data['txtAdjustment'] >= 0) {
            $globalArr['adjustmentType'] = '+';
        } else {
            $globalArr['adjustmentType'] = '-';
        }
        $globalArr['grandTotal'] = $data['hdnGrandTotal'];
        
        $inventories['Global'] = $globalArr;
        $inventories['Products'] = $productsArr;
        
        $returnData = $data;
        $returnData['inventories'] = $inventories;
        return $returnData;
    }
    
    public function spws_inventoryDescribe($moduleName) {
        global $adb, $app_strings, $app_currency_strings;
        
        $globalArr = array();
        
        $inv_currency_res = $adb->pquery('SELECT * FROM vtiger_currency_info', array());
	$inv_currency_default = 1;
        $inv_currency_arr = array();
        while($inv_currency_info = $adb->fetch_array($inv_currency_res)) {
            if($inv_currency_info['defaultid'] < 0) {
	        $inv_currency_default = $inv_currency_info['currency_name'];
	    }
            $inv_currency_subarr = array();
            $inv_currency_subarr['label'] = getTranslatedCurrencyString($inv_currency_info['currency_name']);
            $inv_currency_subarr['value'] = $inv_currency_info['currency_name'];
            array_push($inv_currency_arr,$inv_currency_subarr);
        }
        if ($inv_currency_default == 1) {
            $inv_currency_default = $adb->query_result($inv_currency_res, 0, 'currency_name');
        }
        $inventory_currency = array('name' => 'inventory_currency','label' => $app_strings['LBL_CURRENCY'],'mandatory' => '','type' => array('picklistValues' => $inv_currency_arr),'nullable' => '','editable' => '1','default' => $inv_currency_default);
        array_push($globalArr, $inventory_currency);

        $taxtype = array('name' => 'taxtype','label' => $app_strings['LBL_TAX_MODE'],'mandatory' => '','type' => array('picklistValues' => array(array('label' => $app_strings['individual'],'value' => 'individual'),array('label' => $app_strings['group'],'value' => 'Group'))),'nullable' => '','editable' => '1','default' => 'group');
        array_push($globalArr, $taxtype);
        
        $netTotal = array('name' => 'netTotal','label' => $app_strings['LBL_NET_TOTAL'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($globalArr, $netTotal);
        
        $discount_percentage_final = array('name' => 'discount_percentage_final','label' => '% '.$app_strings['LBL_OF_PRICE'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
        array_push($globalArr, $discount_percentage_final);
        
        $discount_amount_final = array('name' => 'discount_amount_final','label' => $app_strings['LBL_DIRECT_PRICE_REDUCTION'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
        array_push($globalArr, $discount_amount_final);
        
        $discountTotal_final = array('name' => 'discountTotal_final','label' => $app_strings['LBL_DISCOUNT'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($globalArr, $discountTotal_final);
        
        $tax_res = $adb->pquery("select taxname,taxlabel from vtiger_inventorytaxinfo",array());
        while($tax_info = $adb->fetch_array($tax_res)) {
            $tax_fieldname = $tax_info['taxname'].'_group_percentage';
            $tax_fieldlabel = $tax_info['taxlabel'];
            $tax_arr = array('name' => $tax_fieldname,'label' => $tax_fieldlabel,'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
            array_push($globalArr, $tax_arr);
        }
        
        $tax_final = array('name' => 'tax_final','label' => $app_strings['LBL_TAX'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($globalArr, $tax_final);
        
        $shipping_handling_charge = array('name' => 'shipping_handling_charge','label' => $app_strings['LBL_DIRECT_PRICE_REDUCTION'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
        array_push($globalArr, $shipping_handling_charge);
        
        $shtax_res = $adb->pquery("select taxname,taxlabel from vtiger_shippingtaxinfo",array());
        while($shtax_info = $adb->fetch_array($shtax_res)) {
            $shtax_fieldname = $shtax_info['taxname'].'_sh_percent';
            $shtax_fieldlabel = $shtax_info['taxlabel'];
            $shtax_arr = array('name' => $shtax_fieldname,'label' => $shtax_fieldlabel,'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
            array_push($globalArr, $shtax_arr);
        }
        
        $shipping_handling_tax = array('name' => 'shipping_handling_tax','label' => $app_strings['LBL_TAX_FOR_SHIPPING_AND_HANDLING'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($globalArr, $shipping_handling_tax);
        
        $adjustmentType = array('name' => 'adjustmentType','label' => $app_strings['LBL_ADJUSTMENT'],'mandatory' => '','type' => array('picklistValues' => array(array('label' => $app_strings['LBL_ADD_ITEM'],'value' => '+'),array('label' => $app_strings['LBL_DEDUCT'],'value' => '-'))),'nullable' => '','editable' => '1','default' => '+');
        array_push($globalArr, $adjustmentType);
        
        $adjustment = array('name' => 'adjustment','label' => $app_strings['LBL_ADJUSTMENT'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
        array_push($globalArr, $adjustment);
        
        $grandTotal = array('name' => 'grandTotal','label' => $app_strings['LBL_GRAND_TOTAL'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($globalArr, $grandTotal);
        
        
        $productArr = array();
        
        $hdnProductcode = array('name' => 'hdnProductcode','label' => $app_strings['LBL_PRODUCT_CODE'],'mandatory' => '','type' => array('name' => 'string'),'nullable' => '1','editable' => '','default' => '');
        array_push($productArr, $hdnProductcode);
        
        $hdnProductId = array('name' => 'hdnProductId','label' => '','mandatory' => '1','type' => array('refersTo' => array('0' => 'Products', '1' => 'Services')),'nullable' => '1','editable' => '1','default' => '');
        array_push($productArr, $hdnProductId);
        
        $productName = array('name' => 'productName','label' => $app_strings['LBL_PRODUCT_NAME'],'mandatory' => '','type' => array('name' => 'string'),'nullable' => '1','editable' => '','default' => '');
        array_push($productArr, $productName);
        
        $productDescription = array('name' => 'productDescription','label' => $app_strings['LBL_PRODUCT_DESCRIPTION'],'mandatory' => '','type' => array('name' => 'text'),'nullable' => '1','editable' => '1','default' => '');
        array_push($productArr, $productDescription);
        
        $comment = array('name' => 'comment','label' => $app_strings['LBL_PRODUCT_COMMENT'],'mandatory' => '','type' => array('name' => 'text'),'nullable' => '1','editable' => '1','default' => '');
        array_push($productArr, $comment);
        
        if ($moduleName == 'PurchaseOrder' || $moduleName == 'Act' || $moduleName == 'Consignment') {
            $qtyInStock = array('name' => 'qtyInStock','label' => $app_strings['LBL_QTY_IN_STOCK'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '1','editable' => '','default' => '');
            array_push($productArr, $qtyInStock);
        }
        
        $qty = array('name' => 'qty','label' => $app_strings['LBL_QTY'],'mandatory' => '1','type' => array('name' => 'integer'),'nullable' => '','editable' => '1','default' => '');
        array_push($productArr, $qty);
        
        $listPrice = array('name' => 'listPrice','label' => $app_strings['LBL_LIST_PRICE'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
        array_push($productArr, $listPrice);
        
        $productTotal = array('name' => 'productTotal','label' => $app_strings['LBL_TOTAL'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($productArr, $productTotal);
        
        $discount_percentage = array('name' => 'discount_percentage','label' => '% '.$app_strings['LBL_OF_PRICE'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
        array_push($productArr, $discount_percentage);
        
        $discount_amount = array('name' => 'discount_amount','label' => $app_strings['LBL_DIRECT_PRICE_REDUCTION'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
        array_push($productArr, $discount_amount);
        
        $discountTotal = array('name' => 'discountTotal','label' => $app_strings['LBL_DISCOUNT'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($productArr, $discountTotal);
        
        $totalAfterDiscount = array('name' => 'totalAfterDiscount','label' => $app_strings['LBL_TOTAL_AFTER_DISCOUNT'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($productArr, $totalAfterDiscount);
        
        $tax_res->MoveFirst();
        while($tax_info = $adb->fetch_array($tax_res)) {
            $tax_fieldname = $tax_info['taxname'].'_percentage';
            $tax_fieldlabel = $tax_info['taxlabel'];
            $tax_arr = array('name' => $tax_fieldname,'label' => $tax_fieldlabel,'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '1','default' => '0');
            array_push($productArr, $tax_arr);
        }
        
        $taxTotal = array('name' => 'taxTotal','label' => $app_strings['LBL_TAX'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($productArr, $taxTotal);
        
        $netPrice = array('name' => 'netPrice','label' => $app_strings['LBL_NET_PRICE'],'mandatory' => '','type' => array('name' => 'double'),'nullable' => '','editable' => '','default' => '0');
        array_push($productArr, $netPrice);
        
        
        $inventories = array('Global' => $globalArr, 'Products' => $productArr);
        return $inventories;
    }
    
    public function spws_inventoryUpdate($elements,$instance) {
        return $this->spws_inventoryCreate($elements,$instance);
    }
    
    public function spws_inventoryDelete($instance) {
        require_once('include/utils/InventoryUtils.php');
        deleteInventoryProductDetails($instance);
    }
    
    public function spws_retrieveProductImages($crmId,$data) {
        global $adb, $site_URL;
        $attachments_query = 'SELECT p.productid, a.attachmentsid, a.name, a.path 
            FROM vtiger_products p, vtiger_seattachmentsrel ar, vtiger_attachments a 
            WHERE p.productid = ar.crmid AND ar.attachmentsid = a.attachmentsid AND p.productid = ?';
        $result = $adb->pquery($attachments_query, array($crmId));
        $it = new SqlResultIterator($adb, $result);
        $attachments = array();
        foreach ($it as $row) {
            $attachments[] = $site_URL."/".$row->path.$row->attachmentsid."_".$row->name;
        }
        $data['images'] = $attachments;
        return $data;
    }
}
?>
