<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

abstract class SPTips_AbstractProvider_Model {
    
    protected $name;
    
    /**
     * Returns name of the provider
     * @return String
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * returns array with adresses info
     * @return Array
     */
    public abstract function searchAddress($searchParam);
    
}