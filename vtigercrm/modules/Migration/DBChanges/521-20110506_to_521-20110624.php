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

$migrationlog->debug("\n\nDB Changes from 5.2.1-20110506 to 5.2.1-20110624 -------- Starts \n\n");

require_once 'include/utils/CommonUtils.php';
//global $adb;

changeDBCollation('utf8_general_ci');

$query=$adb->pquery("select * from vtiger_links where linklabel = 'LBL_CHECK_STATUS'",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows > 0){
    ExecuteQuery("update vtiger_links set linkurl = 'javascript:SMSNotifier.checkstatus(\\'tbl".'$WRAPPER_NAME$'."\\', ".'$RECORD$'.")' where linklabel = 'LBL_CHECK_STATUS'");
}

$migrationlog->debug("\n\nDB Changes from 5.2.1-20110506 to 5.2.1-20110624 -------- Ends \n\n");
?>
