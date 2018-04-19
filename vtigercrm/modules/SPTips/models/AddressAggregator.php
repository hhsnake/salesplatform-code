<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPTips_AddressAggregator_Model {
    
    private $provider;
    private $searchParam;
    private $vtigerFields;
    private $providerFields; // fields, that are in rule
    
    public function __construct($providerObj, $searchParam, $vtigerFields, $providerFields) {
        $this->provider = $providerObj;
        $this->searchParam = $searchParam;
        $this->vtigerFields = $vtigerFields;
        $this->providerFields = $providerFields;
    }
    
    public function getIntersectionBetweenFields($providerAnswer) {
        if (count($this->vtigerFields) !== count($this->providerFields)) {
            return null;
        }
        
        $resultArr = array();
        
        foreach ($providerAnswer as $key => $addressInfo) {
            foreach ($addressInfo as $providerKey => $item) {
                if (in_array($providerKey, $this->providerFields)) {
                    $vtigerField = array_keys($this->providerFields, $providerKey);
                    foreach ($vtigerField as $foundField) {
                        $resultArr[$key][$foundField] = $item;
                    }
                }
            }
        }
        return $resultArr;
    }
    
    public function searchAddress() {
        $provider = $this->provider;
        $providerAnswer = $provider->searchAddress($this->searchParam);
        
        $resultArray = $this->getIntersectionBetweenFields($providerAnswer);
        return $resultArray;
    }
}

