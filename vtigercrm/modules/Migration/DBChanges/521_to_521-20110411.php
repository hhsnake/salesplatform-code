<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

/**
 * @author igor.struchkov@salesplatform.ru
 */

require_once 'include/utils/utils.php';

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.2.1 to 5.2.1-20110411 -------- Starts \n\n");

require_once 'include/utils/CommonUtils.php';
//global $adb;

changeDBCollation('utf8_general_ci');

$query=$adb->pquery("select * from vtiger_language where prefix='ru_ru'",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows == 0){
    ExecuteQuery("insert into vtiger_language(name,prefix,label,lastupdated,sequence,isdefault,active) 
		  values('Русский','ru_ru','RU Русский',Now(),NULL,1,1)");
    ExecuteQuery("update vtiger_language_seq set id=LAST_INSERT_ID(id+1)");

}

ExecuteQuery("alter table `vtiger_organizationdetails` add `inn` varchar(30) default ''");
ExecuteQuery("alter table `vtiger_organizationdetails` add `kpp` varchar(30) default ''");

$migrationlog->debug("\n\nDB Changes from 5.2.1 to 5.2.1-20110411 -------- Ends \n\n");
?>