<?php
/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.                                                              
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

include_once dirname(__FILE__) . '/SPModulePickListRequest.php';

/**
 * Main controller of actions
 */
class SPModulePickListController {	

    /**
     * Core processing method
     */
    function process(SPModulePickListRequest $request) {
        $type = $request->get('type');		
        if ($type == 'GetValues') {
            $this->processGetValues($request);
        } else {
            $this->processDefault($request);
        }
    }

    /**
     * Default action
     */
    protected function processDefault($request) {
    }

    /**
     * Save action
     */
    protected function processGetValues($request) {
        global $adb;
        $output = "";
        $fieldname = $request->get('fieldname');
        $term = $request->get('term');
        $query = "SELECT vf.columnname as columnname, vf.tablename as tablename, ".
                        "srcvf.columnname as srccolumnname, srcvf.tablename as srctablename ".
                "FROM sp_module_picklist_fields_rel spm ".
                "INNER JOIN vtiger_field vf ON vf.fieldid = spm.fieldid ".
                "INNER JOIN vtiger_field srcvf ON srcvf.fieldid = spm.srcfieldid ".
                "WHERE vf.fieldname = ?";
        $result = $adb->pquery($query, array($fieldname)); 
        $count = $adb->num_rows($result);
        if ($count > 0) {
            $tablename = $adb->query_result($result, 0, 'srctablename');
            $columnname = $adb->query_result($result, 0, 'srccolumnname');
            
            $value_query = "SELECT $columnname FROM $tablename WHERE $columnname LIKE ? ";
            $value_result = $adb->pquery($value_query, array("%$term%")); 
            if ($value_result) {
                while($value_record = $adb->fetchByAssoc($value_result)) {
                    if (!empty($output)) {
                        $output .= ", ";
                    }
                    $output .= '"'.$value_record[$columnname].'"';
                }
            }
            $output = "[".$output."]";
        }
        echo $output;
    }
}

$controller = new SPModulePickListController();
$controller->process(new SPModulePickListRequest($_REQUEST));

?>
