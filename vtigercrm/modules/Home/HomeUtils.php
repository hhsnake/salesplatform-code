<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *******************************************************************************/

/**
 * this file will contain the utility functions for Home module
 */

/**
 * function to get upcoming activities for today
 * @param integer $maxval - the maximum number of records to display
 * @param integer $calCnt - returns the count query if this is set
 * return array    $values   - activities record in array format
 */
function homepage_getUpcomingActivities($maxval,$calCnt){
	require_once("data/Tracker.php");
	require_once('include/utils/utils.php');

	global $adb;
	global $current_user;

	$dbStartDateTime = new DateTimeField(date('Y-m-d H:i:s'));
	$userStartDate = $dbStartDateTime->getDisplayDate();
	$userStartDateTime = new DateTimeField($userStartDate.' 00:00:00');
	$startDateTime = $userStartDateTime->getDBInsertDateTimeValue();

	$userEndDateTime = new DateTimeField($userStartDate.' 23:59:00');
	$endDateTime = $userEndDateTime->getDBInsertDateTimeValue();

	$upcoming_condition = " AND (CAST((CONCAT(date_start,' ',time_start)) AS DATETIME) BETWEEN '$startDateTime' AND '$endDateTime'
									OR CAST((CONCAT(vtiger_recurringevents.recurringdate,' ',time_start)) AS DATETIME) BETWEEN '$startDateTime' AND '$endDateTime')";

	$list_query = " select vtiger_crmentity.crmid,vtiger_crmentity.smownerid,".
		"vtiger_crmentity.setype, vtiger_recurringevents.recurringdate, vtiger_activity.* ".
		"from vtiger_activity inner join vtiger_crmentity on vtiger_crmentity.crmid=".
		"vtiger_activity.activityid LEFT JOIN vtiger_groups ON vtiger_groups.groupid = ".
		"vtiger_crmentity.smownerid left outer join vtiger_recurringevents on ".
		"vtiger_recurringevents.activityid=vtiger_activity.activityid";
	$list_query .= getNonAdminAccessControlQuery('Calendar',$current_user);
	$list_query .= "WHERE vtiger_crmentity.deleted=0 and vtiger_activity.activitytype not in ".
	"('Emails') AND ( vtiger_activity.status is NULL OR vtiger_activity.status not in ".
	"('Completed','Deferred')) and  (  vtiger_activity.eventstatus is NULL OR ".
	"vtiger_activity.eventstatus not in ('Held','Not Held') )".$upcoming_condition;

	$list_query.= " GROUP BY vtiger_activity.activityid";
	$list_query.= " ORDER BY date_start,time_start ASC";
	$list_query.= " limit $maxval";

	$res = $adb->query($list_query);
	$noofrecords = $adb->num_rows($res);
	if($calCnt == 'calculateCnt'){
		return $noofrecords;
	}

	$open_activity_list = array();
	if ($noofrecords>0){
		for($i=0;$i<$noofrecords;$i++){
			$dateValue = $adb->query_result($res,$i,'date_start') . ' ' .
					$adb->query_result($res,$i,'time_start');
			$endDateValue = $adb->query_result($res,$i,'due_date') . ' ' .
					$adb->query_result($res,$i,'time_end');
			$recurringDateValue = $adb->query_result($res,$i,'due_date') . ' ' .
					$adb->query_result($res,$i,'time_start');
			$date = new DateTimeField($dateValue);
			$endDate = new DateTimeField($endDateValue);
			$recurringDate = new DateTimeField($recurringDateValue);

			$open_activity_list[] = array('name' => $adb->query_result($res,$i,'subject'),
										'id' => $adb->query_result($res,$i,'activityid'),
										'type' => $adb->query_result($res,$i,'activitytype'),
										'module' => $adb->query_result($res,$i,'setype'),
										'date_start' => $date->getDisplayDate(),
										'due_date' => $endDate->getDisplayDate(),
										'recurringdate' => $recurringDate->getDisplayDate(),
										'priority' => $adb->query_result($res,$i,'priority'),
									);
		}
	}
	$values = getActivityEntries($open_activity_list);
	$values['ModuleName'] = 'Calendar';
	$values['search_qry'] = "&action=ListView&from_homepage=upcoming_activities";

	return $values;
}

/**
 * this function returns the activity entries in array format
 * it takes in an array containing activity details as a parameter
 * @param array $open_activity_list - the array containing activity details
 * return array $values - activities record in array format
 */
function getActivityEntries($open_activity_list){
	global $current_language, $app_strings;
	$current_module_strings = return_module_language($current_language, 'Calendar');
	if(!empty($open_activity_list)){
		$header=array();
		$header[] =$current_module_strings['LBL_LIST_SUBJECT'];
		$header[] =$current_module_strings['Type'];

		$entries = array();
		foreach($open_activity_list as $event){
			$recur_date=preg_replace('/--/','',$event['recurringdate']);
			if($recur_date!=""){
				$event['date_start']=$event['recurringdate'];
			}
			$font_color_high = "color:#00DD00;";
			$font_color_medium = "color:#DD00DD;";

			switch ($event['priority']){
				case 'High':
					$font_color=$font_color_high;
					break;
				case 'Medium':
					$font_color=$font_color_medium;
					break;
				default:
					$font_color='';
			}

			if($event['type'] != 'Task' && $event['type'] != 'Emails' && $event['type'] != ''){
				$activity_type = 'Events';
			}else{
				$activity_type = 'Task';
			}

			$entries[$event['id']] = array(
					'0' => '<a href="index.php?action=DetailView&module='.$event["module"].'&activity_mode='.$activity_type.'&record='.$event["id"].'" style="'.$font_color.';">'.$event["name"].'</a>',
					'1' => $event["type"],
					);
		}
		$values = array('noofactivities'=>count($open_activity_list),'Header'=>$header,'Entries'=>$entries);
	}else{
		$values = array('noofactivities'=>count($open_activity_list), 'Entries'=>
			'<div class="componentName">'.$app_strings['LBL_NO_DATA'].'</div>');
	}
	return $values;
}


/**
 * function to get pending activities for today
 * @param integer $maxval - the maximum number of records to display
 * @param integer $calCnt - returns the count query if this is set
 * return array    $values   - activities record in array format
 */
function homepage_getPendingActivities($maxval,$calCnt){
	require_once("data/Tracker.php");
	require_once("include/utils/utils.php");
	require_once('include/utils/CommonUtils.php');

	global $adb;
	global $current_user;

	$dbStartDateTime = new DateTimeField(date('Y-m-d H:i:s'));
	$userStartDate = $dbStartDateTime->getDisplayDate();
	$userStartDateTime = new DateTimeField($userStartDate.' 00:00:00');
	$startDateTime = $userStartDateTime->getDBInsertDateTimeValue();

	$userEndDateTime = new DateTimeField($userStartDate.' 23:59:00');
	$endDateTime = $userEndDateTime->getDBInsertDateTimeValue();

	$pending_condition = " AND (CAST((CONCAT(date_start,' ',time_start)) AS DATETIME) BETWEEN '$startDateTime' AND '$endDateTime'
									OR CAST((CONCAT(vtiger_recurringevents.recurringdate,' ',time_start)) AS DATETIME) BETWEEN '$startDateTime' AND '$endDateTime')";

	$list_query = "select vtiger_crmentity.crmid,vtiger_crmentity.smownerid,vtiger_crmentity.".
	"setype, vtiger_recurringevents.recurringdate, vtiger_activity.* from vtiger_activity ".
	"inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid LEFT ".
	"JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid left outer join ".
	"vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid".
	$list_query .= getNonAdminAccessControlQuery('Calendar',$current_user);
	$list_query .= "WHERE vtiger_crmentity.deleted=0 and (vtiger_activity.activitytype not in ".
	"('Emails')) AND (vtiger_activity.status is NULL OR vtiger_activity.status not in ".
	"('Completed','Deferred')) and (vtiger_activity.eventstatus is NULL OR  vtiger_activity.".
	"eventstatus not in ('Held','Not Held')) ".$pending_condition;

	$list_query.= " GROUP BY vtiger_activity.activityid";
	$list_query.= " ORDER BY date_start,time_start ASC";
	$list_query.= " limit $maxval";

	$res = $adb->query($list_query);
	$noofrecords = $adb->num_rows($res);
	if($calCnt == 'calculateCnt'){
		return $noofrecords;
	}

	$open_activity_list = array();
	$noofrows = $adb->num_rows($res);
	if (count($res)>0){
		for($i=0;$i<$noofrows;$i++){
			$dateValue = $adb->query_result($res,$i,'date_start') . ' ' .
					$adb->query_result($res,$i,'time_start');
			$endDateValue = $adb->query_result($res,$i,'due_date') . ' ' .
					$adb->query_result($res,$i,'time_end');
			$recurringDateValue = $adb->query_result($res,$i,'due_date') . ' ' .
					$adb->query_result($res,$i,'time_start');
			$date = new DateTimeField($dateValue);
			$endDate = new DateTimeField($endDateValue);
			$recurringDate = new DateTimeField($recurringDateValue);

			$open_activity_list[] = array('name' => $adb->query_result($res,$i,'subject'),
										'id' => $adb->query_result($res,$i,'activityid'),
										'type' => $adb->query_result($res,$i,'activitytype'),
										'module' => $adb->query_result($res,$i,'setype'),
										'date_start' => $date->getDisplayDate(),
										'due_date' => $endDate->getDisplayDate(),
										'recurringdate' => $recurringDate->getDisplayDate(),
										'priority' => $adb->query_result($res,$i,'priority'),
									);
			}
		}

	$values = getActivityEntries($open_activity_list);
	$values['ModuleName'] = 'Calendar';
	$values['search_qry'] = "&action=ListView&from_homepage=pending_activities";

	return $values;
}


/**
 * this function returns the number of columns in the home page for the current user.
 * if nothing is found in the database it returns 4 by default
 * return integer $data - the number of columns
 */
function getNumberOfColumns(){
	global $current_user, $adb;

	$sql = "select * from vtiger_home_layout where userid=?";
	$result = $adb->pquery($sql, array($current_user->id));

	if($adb->num_rows($result)>0){
		$data = $adb->query_result($result,0,"layout");
	}else{
		$data = 4;	//default is 4 column layout for now
	}
	return $data;
}

// SalesPlatform.ru begin Widgets added
function homepage_getSP_EVENTS_UpcomingActivities($maxval,$calCnt){
        global $log;
        $log->debug("Entering homepage_getSP_EVENTS_UpcomingActivities() method ...");
	require_once("data/Tracker.php");
	require_once("include/utils/utils.php");
	global $adb;
	global $current_language;
	global $current_user;
	$current_module_strings = return_module_language($current_language, "Calendar");
 	
	$list_query = 'select vtiger_activity.subject, vtiger_activity.activitytype, vtiger_activity.activityid
		from vtiger_activity inner join vtiger_crmentity on vtiger_activity.activityid = vtiger_crmentity.crmid 
		where vtiger_activity.activitytype not in ("Emails") AND vtiger_crmentity.deleted =0 AND vtiger_crmentity.smownerid = ?';
	
        $list_query .=" ORDER BY vtiger_activity.activityid DESC";
       
	$list_query .= " LIMIT 0," . $adb->sql_escape_string($maxval);
	
        $list_result = $adb->pquery($list_query, array($current_user->id));
	$noofrows = $adb->num_rows($list_result);
	
	$open_events_list =array();
	if ($noofrows > 0) {
		for($i=0;$i<$noofrows && $i<$maxval;$i++) 
		{
			$open_events_list[] = Array('subject' => $adb->query_result($list_result,$i,'subject'),  
                                        'type' => $adb->query_result($list_result,$i,'activitytype'), 
					'id' => $adb->query_result($list_result,$i,'activityid')
					);
		}
	}
	
	$header=array();
	$header[] =$current_module_strings['LBL_ACTIVITY'];
	$header[] =$current_module_strings['Type'];
	
    $entries=array();
    foreach($open_events_list as $event)
	{
		$value=array();
		$event_fields = array(
				'SUBJECT' => $event['subject'],    
				'TYPE' => $event['type'],
				'ID' => $event['id'],
				);

                // SalesPlatform.ru begin UTF-8 support
		$Top_firstname = (mb_strlen($event['subject'], 'UTF-8') > 40) ? (mb_substr($event['subject'],0,40,'UTF-8').'...') : $event['subject'];
		//$Top_firstname = (mb_strlen($event['subject'], 'UTF-8') > 40) ? (mb_substr($event['subject'],0,20).'...') : $event['subject'];
                // SalesPlatform.ru end
		$value[]= '<a href="index.php?action=DetailView&module=Calendar&record='.$event_fields['ID'].'">'.$Top_firstname.'</a>';
                  
                $value[] = getTranslatedString($event['type'], 'Calendar');
           
		$entries[$event_fields['ID']]=$value;
	}
	
	$search_qry = "&action=ListView&from_homepage=pending_activities";

	$values=Array('ModuleName'=>'Calendar','Header'=>$header,'Entries'=>$entries, 'search_qry'=>$search_qry);
	$log->debug("Exiting getNewLeads method ...");
	if (($display_empty_home_blocks && count($entries) == 0 ) || (count($entries)>0))
		return $values;     
}
// SalesPlatform.ru end 

// SalesPlatform.ru begin Widgets added
function homepage_getSP_EXT_EVENTS_UpcomingActivities($maxval,$calCnt){
        global $log;
        $log->debug("Entering homepage_getSP_EXT_EVENTS_UpcomingActivities() method ...");
	require_once("data/Tracker.php");
	require_once("include/utils/utils.php");
	global $adb;
	global $current_language;
	global $current_user;
	$current_module_strings = return_module_language($current_language, "Calendar");

	$list_query = 'select vtiger_activity.subject, vtiger_activity.activitytype, vtiger_activity.date_start, vtiger_users.first_name, vtiger_users.last_name, vtiger_activity.activityid
                        from vtiger_activity inner join vtiger_crmentity 
                        on vtiger_activity.activityid = vtiger_crmentity.crmid 
                        inner join vtiger_users 
                        on vtiger_crmentity.smownerid = vtiger_users.id
                        where vtiger_activity.activitytype not in ("Emails") AND vtiger_crmentity.deleted =0 AND vtiger_users.reports_to_id=?';
	
        $list_query .=" ORDER BY vtiger_activity.activityid DESC";
	 
	$list_query .= " LIMIT 0," . $adb->sql_escape_string($maxval);
        	
	$list_result = $adb->pquery($list_query, array($current_user->id));
	$noofrows = $adb->num_rows($list_result);
	$open_events_list =array();
	if ($noofrows > 0) {
		for($i=0;$i<$noofrows && $i<$maxval;$i++) 
		{
			$open_events_list[] = Array('subject' => $adb->query_result($list_result,$i,'subject'), 
                                        'date' => $adb->query_result($list_result,$i,'date_start'),
                                        'type' => $adb->query_result($list_result,$i,'activitytype'), 
                                        'firstname' => $adb->query_result($list_result,$i,'first_name'), 
                                        'lastname' => $adb->query_result($list_result,$i,'last_name'),
					'id' => $adb->query_result($list_result,$i,'activityid')
					);
		}
	}
	
	$header=array();
	$header[] =$current_module_strings['LBL_ACTIVITY'];
        $header[] =$current_module_strings['Start Date'];
	$header[] =$current_module_strings['Type'];
        $header[] =$current_module_strings['Assigned To'];
	
    $entries=array();
    foreach($open_events_list as $event)
	{
		$value=array();
		$event_fields = array(
				'SUBJECT' => $event['subject'],
                                'DATE' => $event['date'],
				'TYPE' => $event['type'],
                                'FIRSTNAME' => $event['firstname'],
                                'LASTNAME' => $event['lastname'],
				'ID' => $event['id'],
				);
	
                // SalesPlatform.ru begin UTF-8 support
		$Top_subject = (mb_strlen($event['subject'], 'UTF-8') > 40) ? (mb_substr($event['subject'],0,40,'UTF-8').'...') : $event['subject'];
		//$Top_subject = (mb_strlen($event['subject'], 'UTF-8') > 40) ? (mb_substr($event['subject'],0,20).'...') : $event['subject'];
                // SalesPlatform.ru end
                $value[]= '<a href="index.php?action=DetailView&module=Calendar&record='.$event_fields['ID'].'">'.$Top_subject.'</a>';
                $value[] = getTranslatedString($event['date'], 'Calendar'); 
                $value[] = getTranslatedString($event['type'], 'Calendar'); 
                $Top_FL = $event['firstname'].' '.$event['lastname'];
                $value[] = $Top_FL;
		$entries[$event_fields['ID']]=$value;
	}
	
	$search_qry = "&action=ListView&from_homepage=upcoming_activities";

	$values=Array('ModuleName'=>'Calendar','Header'=>$header,'Entries'=>$entries,'search_qry'=>$search_qry);
	$log->debug("Exiting getNewLeads method ...");
	if (($display_empty_home_blocks && count($entries) == 0 ) || (count($entries)>0))
		return $values;   
}
// SalesPlatform.ru end
?>