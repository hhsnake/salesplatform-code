<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPTips_GoogleProvider_Model extends SPTips_AbstractProvider_Model {
    
    private $APIKey;
    
    public function __construct($name, $APIKey) {
        $this->name = $name;
        $this->APIKey = $APIKey;
        $this->curLanguage = Vtiger_Language_Handler::getShortLanguageName();
    }
    
    public function getHeaders() {
        return array(
            'Content-Type: application/json',
            'Accept: application/json',
        );
    }
    
    private function prepareRequest($curl) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curl, CURLOPT_GET, 1);
    }
    
    public function searchAddress($searchParam) {
        $result = false;
        $resultsArr = array();

        $url = $this->getAPIAutocompleteUrl($searchParam);
        if ($curl = curl_init($url)) {
            $this->prepareRequest($curl);
            $json = curl_exec($curl);
            $result = json_decode($json, true);
            curl_close($curl);
        }
        
        foreach ($result['predictions'] as $prediction) {
            $description = $prediction['description'];
            $placeId = $prediction['place_id'];
            $detailInfo = $this->searchDetailAddress($placeId);
            $detailAddressArray = $this->makeArrayForCurrentAddress($detailInfo);
            $resultsArr[$description] = $detailAddressArray;
        }
        return $resultsArr;
    }
    
    public function searchDetailAddress($placeId) {
        $url = $this->getAPIDetailsUrl($placeId);
        if ($curl = curl_init($url)) {
            $this->prepareRequest($curl);
            $json = curl_exec($curl);
            $result = json_decode($json, true);
            curl_close($curl);
        }
        return $result;
    }
    
    public function makeArrayForCurrentAddress($detailInfo) {
        $searchingKeys = array_keys($this->getProviderFields());
        $result = array();
        foreach ($detailInfo["result"]["address_components"] as $value) {
            $answerLocationTypes = $value['types'];
            $intersectedKeys = array_intersect($answerLocationTypes, $searchingKeys);
            $currentKey = $intersectedKeys[0];
            
            if (empty($currentKey)) {
                continue;
            }
            $result[$currentKey] = $value['long_name'];
        }
        return $result;
    }
    
    /*
     * this type request returns json with name and place_id.
     * if we need details info, use getAPIDetailsUrl with that place_id
     */
    public function getAPIAutocompleteUrl($searchParam, $types = "address", $format = "json") {
        $url = "https://maps.googleapis.com/maps/api/place/autocomplete/";
        $url .= $format . "?input=" . urlencode($searchParam) . "&types=" . $types . "&language=" . $this->curLanguage . "&key=" . $this->APIKey;
        return $url;
    }
    
    public function getAPIDetailsUrl($placeId, $format = "json") {
        $url = "https://maps.googleapis.com/maps/api/place/details/";
        $url .= $format . "?placeid=" . $placeId . "&language=" . $this->curLanguage . "&key=" . $this->APIKey;
        return $url;
    }
    
    public function getProviderFields() {
        return array(
            'street_number' => 'Street number',
            'route' => 'Street',
            'locality' => 'City',
            'country' => 'Country',
            'postal_code' => 'Postal code'
        );
    }
}

