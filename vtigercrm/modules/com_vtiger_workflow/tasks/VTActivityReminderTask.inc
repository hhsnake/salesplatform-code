<?php
/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.                                                              
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');
require_once('modules/com_vtiger_workflow/tasks/VTRepeatableTask.inc');

class VTActivityReminderTask extends VTRepeatableTask {
    public $executeImmediately = false;

    public function getFieldNames() {
        return array('remindertext', 'checkquery', 'getrelatedentity');
    }

    public function doTask($wfEntity) {
        if (!$this->isSafeQuery($this->checkquery)) {
            error_log("VTActivityReminderTask error: SQL must contains only SELECT query");
            return;
        }

        parent::doTask($wfEntity);
        
        $util = new VTWorkflowUtils();
        // Get the WorkflowEntity id
        $ws_id = $wfEntity->getId();
        if ($ws_id > 0) {
            // Get the CRMEntity id
            $relatedCRMid = vtws_getCRMEntityId($ws_id);
            if ($relatedCRMid > 0) {
                global $adb;
                require_once 'modules/Calendar/Activity.php';
                $focus = new Activity();
                if (!empty($this->getrelatedentity)) {
                    if (!$this->isSafeQuery($this->getrelatedentity)) {
                        error_log("VTActivityReminderTask error: SQL must contains only SELECT query");
                        return;
                    }
                    $result = $adb->pquery($this->getrelatedentity, array($relatedCRMid));
                    if (!$result) {
                        error_log("VTActivityReminderTask error: bad SQL=" . $this->getrelatedentity . " or bad crmId=".$relatedCRMid);
                    } else {
                        $parent_crmid = $adb->query_result($result, 0, "crmid");
                        $focus->id = $parent_crmid;
                        $module = $adb->query_result($result, 0, "module");
                    }
                } else {
                    $focus->id = $relatedCRMid;
                    $module = $wfEntity->getModuleName();
                }
                if ($focus->id > 0) {
                    $check_result = $this->checkEntityActivities($focus->id);
                    if ($check_result !== false) {
                        if ($check_result > 0) {
                            $sql = "UPDATE vtiger_activity_reminder_popup SET status=1 WHERE recordid=? and title=?";
                            $adb->pquery($sql, array($focus->id, $this->remindertext));
                        } else {
                            $focus->column_fields['date_start'] = date('Y-m-d');
                            $focus->column_fields['time_start'] = date('H:i');
                            $focus->insertIntoActivityReminderPopup($module);

                            // Set special type
                            $query = "UPDATE vtiger_activity_reminder_popup SET rtype = 1, title=? WHERE semodule = ? AND recordid = ?"; 
                            $adb->pquery($query, array($this->remindertext, $module, $focus->id));
                            
                            // Reset next reminder time
                            $_SESSION['reset_reminder_time'] = 1;
                        }
                    } else {
                        error_log("VTActivityReminderTask error: bad SQL=" . $this->checkquery . " or bad crmId=".$focus->id);
                    }
                }
            }
        }
    }
    
    private function checkEntityActivities($crmid) {
        global $adb;
        $sql = str_replace('?', $crmid, $this->checkquery);
        $result = $adb->pquery($sql, array());
        return $result ? $adb->query_result($result, 0, "count") : false;
    }

    /**
     * Check query for the modification statements
     * 
     * @param string $query SQL query
     * @return bool 
     */
    private function isSafeQuery($query) {
        $query = strtoupper($query);
        $needle = "SELECT";
        $startWithSelect = (substr($query, 0, strlen($needle)) === $needle);
        $notContains = substr_count($query, "DELETE ") == 0 && 
                substr_count($query, "CREATE ") == 0 && 
                substr_count($query, "INSERT ") == 0 && 
                substr_count($query, "UPDATE ") == 0 && 
                substr_count($query, "ALTER ") == 0 
                && substr_count($query, "DROP ") == 0;
        return $startWithSelect && $notContains;
    }
}
?>
