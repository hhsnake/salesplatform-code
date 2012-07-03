<?php
/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.                                                              
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPModulePickListRequest {
    protected $valuemap;

    function __construct($values) {
        $this->valuemap = $values;
    }

    function get($key, $defvalue='') {
        $value = $defvalue;
        if (isset($this->valuemap[$key])) {
                $value = $this->valuemap[$key];
        }
        if (!empty($value)) {
                $value = vtlib_purify($value);
        }
        return $value;
    }

    function values() {
        return $this->valuemap;
    }
}
?>