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

class VTActivityReminderTask extends VTTask {
    public $executeImmediately = false;

    public function getFieldNames() {
        return array('remindertext', 'checkquery', 'getrelatedentity');
    }

    public function doTask($wfEntity) {
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
                        $result = $adb->pquery($this->getrelatedentity, array($relatedCRMid));
                        $parent_crmid = $adb->query_result($result, 0, "crmid");
                        $focus->id = $parent_crmid;
                        $module = $adb->query_result($result, 0, "module");
                    } else {
                        $focus->id = $relatedCRMid;
                        $module = $wfEntity->getModuleName();
                    }
                    if ($focus->id > 0) {
                        if ($this->checkEntityActivities($focus->id)) {
                            $sql = "update vtiger_activity_reminder_popup set status=1 where recordid=? and title=?";
                            $adb->pquery($sql, array($focus->id, $this->remindertext));
                        } else {
                            $focus->column_fields['date_start'] = date('Y-m-d');
                            $focus->column_fields['time_start'] = date('H:i');
                            $focus->insertIntoActivityReminderPopup($module);
                            
                            // Set special type
                            $query = "UPDATE vtiger_activity_reminder_popup SET rtype = 1, title=? WHERE semodule = ? and recordid = ?"; 
                            $adb->pquery($query, array($this->remindertext, $module, $focus->id));
                        }
                    }
                }
        }
    }
    
    private function checkEntityActivities($crmid) {
        global $adb;
        $sql = $this->checkquery;
        $result = $adb->pquery($sql, array($crmid, $crmid));
        return $adb->query_result($result, 0, "count") > 0;
    }
}
/*
    // Select current activities

    // Check query
    SELECT (
        (SELECT count(*) FROM vtiger_activity 
           INNER JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid 
           INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid 
           LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid 
           LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid 
           LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid 
           LEFT OUTER JOIN vtiger_recurringevents ON vtiger_recurringevents.activityid = vtiger_activity.activityid 
           LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid 
           WHERE vtiger_seactivityrel.crmid = ? AND 
                vtiger_crmentity.deleted = 0 AND 
                ((vtiger_activity.activitytype = 'Task' AND 
                   vtiger_activity.status NOT IN ('Completed', 'Deferred')) OR 
                   (vtiger_activity.activitytype NOT IN ('Emails','Task') AND  
                      vtiger_activity.eventstatus NOT IN ('','Held')
                   )
                )
        ) +
        (SELECT count(*) 
            FROM vtiger_leaddetails, vtiger_crmentity 
            WHERE vtiger_crmentity.crmid = vtiger_leaddetails.leadid AND 
               vtiger_crmentity.deleted = 0 AND vtiger_leaddetails.leadid = ? AND 
               vtiger_leaddetails.leadstatus IN ('Lost Lead', 'Junk Lead')
        )
    ) 
    AS count

    // Get related entity query
    SELECT vtiger_crmentity.crmid as crmid, setype as module 
        FROM vtiger_seactivityrel, vtiger_crmentity 
        WHERE vtiger_crmentity.crmid = vtiger_seactivityrel.crmid AND activityid=?
 */
?>