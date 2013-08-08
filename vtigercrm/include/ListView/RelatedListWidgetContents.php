<?php
/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.                                                              
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

if ($ajaxaction == "LOADRELATEDLISTWIDGET") {
    $crmid = $_REQUEST["recordid"];
    if ($crmid > 0) {
        if ($currentModule == "Documents") {
            $query = "SELECT ce.crmid, ce.setype FROM vtiger_senotesrel nr ".
                    "LEFT JOIN vtiger_crmentity ce ON ce.crmid = nr.crmid ".
                    "WHERE nr.notesid = ? ";
        } else {
            $query = "SELECT ce.crmid, ce.setype FROM vtiger_crmentityrel er ".
                    "LEFT JOIN vtiger_crmentity ce ON ce.crmid = er.crmid ".
                    "WHERE er.relcrmid = ? ";
        }
        $query .= " AND ce.deleted = 0 AND ce.crmid != ? ";
        $list_result = $adb->pquery($query, array($crmid, $crmid));
        $noofrows = $adb->num_rows($list_result);
        for ($i = 0; $i < $noofrows; $i++) {
            $module = $adb->query_result($list_result, $i, "setype");
            $entity_id = $adb->query_result($list_result, $i, "crmid");
            if ($entity_id > 0) {
                $modObj = CRMEntity::getInstance($module);
                $modObj->retrieve_entity_info($entity_id, $module);
                $result = getEntityName($module, array($entity_id));
                if (!empty($result)) {
                    echo getTranslatedString("SINGLE_".$module, $module)." ".
                            '<a href="index.php?module='.$module.'&action=DetailView&record='.$entity_id.'">'.$result[$entity_id].'</a><br />';
                }
            }
        }
    } else {
        echo getTranslatedString(LBL_RECORD_NOT_FOUND);
    }
}
?>