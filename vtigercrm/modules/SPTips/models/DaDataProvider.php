<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPTips_DaDataProvider_Model extends SPTips_AbstractProvider_Model {
    
    private $APIKey;
    private $secretKey;
    
    public function __construct($name, $APIKey, $secretKey) {
        $this->name = $name;
        $this->APIKey = $APIKey;
        $this->secretKey = $secretKey;
    }
    
    public function getAPIKey() {
        return $this->APIKey;
    }
    
    public function getSecretKey() {
        return $this->secretKey;
    }
    
    public function getSearchAddressURL() {
        return "http://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
    }
    
    public function getHeaders() {
        return array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Token ' . $this->APIKey,
            'X-Secret: ' . $this->secretKey
        );
    }
    
    private function prepareRequest($curl, $data) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    private function executeRequest($url, $data) {
        $result = false;
        if ($curl = curl_init($url)) {
            $this->prepareRequest($curl, $data);
            $json = curl_exec($curl);
            $result = json_decode($json, true);
            curl_close($curl);
        }
        return $result;
    }
    
    public function searchAddress($searchParam) {
        $requestData = array('query' => $searchParam);
        $res = $this->getAllAdresses($requestData);
        return $res;
    }
    
    public function getAllAdresses($requestData) {
        $dadataJson = $this->executeRequest($this->getSearchAddressURL(), $requestData);
        $result = array();
        foreach ($dadataJson['suggestions'] as $value) {
            $description = $value['value'];
            $result[$description] = $value['data'];
        }
        return $result;
    }
    
    public function getProviderFields() {
        return array(
            'postal_code' => 'Postal code',
            'country' => 'Country',
            'region_fias_id' => 'FIAS region code',
            'region_kladr_id' => 'KLADR region code',
            'region_with_type' => 'Region with type',
            'region_type' => 'Region type (short)',
            'region_type_full' => 'Region type (full)',
            'region' => 'Region',
            'area_fias_id' => 'FIAS region code in the region',
            'area_kladr_id' => 'KLADR region code in the region',
            'area_with_type' => 'Area in the region with type',
            'area_type' => 'Area in the region with type (short)',
            'area_type_full' => 'Area in the region with type (full)',
            'area' => 'Area in the region',
            'city_fias_id' => 'FIAS code of the city',
            'city_kladr_id' => 'KLADR code of the city',
            'city_with_type' => 'City with type',
            'city_type' => 'Type of city (short)',
            'city_type_full' => 'Type of city (full)',
            'city' => 'City',
            'city_area' => 'Administrative district (only for Moscow)',
            'city_district_fias_id' => 'FIAS code of the ciry district (only if the district is in FIAS)',
            'city_district_kladr_id' => 'KLADR district code of the city (do not fill out)',
            'city_district_with_type' => 'City district with type',
            'city_district_type' => 'Type of city district (short)',
            'city_district_type_full' => 'Type of city district (long)',
            'city_district' => 'City district',
            'settlement_fias_id' => 'FIAS code of the settlement',
            'settlement_kladr_id' => 'KLADR code of the settlement',
            'settlement_with_type' => 'Settlement with type',
            'settlement_type' => 'Type of settlement (short)',
            'settlement_type_full' => 'Type of settlement (full)',
            'settlement' => 'Settlement',
            'street_fias_id' => 'FIAS street code',
            'street_kladr_id' => 'KLADR street code',
            'street_with_type' => 'Street with type',
            'street_type' => 'Street type (short)',
            'street_type_full' => 'Street type (full)',
            'street' => 'Street',
            'house_fias_id' => 'FIAS house code',
            'house_kladr_id' => 'KLADR house code',
            'house_type' => 'Type of house (short)',
            'house_type_full' => 'Type of house (long)',
            'house' => 'House',
            'block_type' => 'Type of house/block (short)',
            'block_type_full' => 'Type of house/block (long)',
            'block' => 'Block',
            'flat_type' => 'Type of appartment (short)',
            'flat_type_full' => 'Type of appartment (long)',
            'flat' => 'Appartment',
            'flat_area' => 'Appartment area',
            'square_meter_price' => 'Market value m²',
            'flat_price' => 'Market value of an appartment',
            'postal_box' => 'Postal box',
            'fias_id' => 'FIAS code',
            'fias_code' => 'Hierarchical address code in FIAS',
            'fias_level' => 'Detail level which address is found in FIAS',
            'fias_actuality_state' => 'Sign of relevance of the address in FIAS',
            'kladr_id' => 'KLADR code',
            'capital_marker' => 'Sing of the center of a district of region',
            'okato' => 'OKATO code',
            'oktmo' => 'OKTMO code',
            'tax_office' => 'Individual tax code for natural persons',
            'tax_office_legal' => 'Individual tax code for legal personality',
            'timezone' => 'Timezone',
            'geo_lat' => 'Geocode: latitude',
            'geo_lon' => 'Geocode: longitude',
            'beltway_hit' => 'Inside Koltsevaya line?',
            'beltway_distance' => 'Distance from Koltsevaya line in km',
            'qc_geo' => 'Coordinate precision code',
            'qc_complete' => 'Code of eligibility for dispatch',
            'qc_house' => 'Home in FIAS?',
            'qc' => 'Address verification code',
            'unparsed_parts' => 'Unrecognized part of the address',
            'metro' => 'List of nearest metro stations (<= 3)'
        );
    }
}