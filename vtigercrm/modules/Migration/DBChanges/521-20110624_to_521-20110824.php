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

$migrationlog->debug("\n\nDB Changes from 5.2.1-20110624 to 5.2.1-20110824 -------- Starts \n\n");

require_once 'include/utils/CommonUtils.php';
//global $adb;

changeDBCollation('utf8_general_ci');

ExecuteQuery("alter table `vtiger_systems` add `server_tls` varchar(20) default NULL");
ExecuteQuery("alter table `vtiger_systems` add `from_name` varchar(200) DEFAULT ''");
ExecuteQuery("alter table `vtiger_systems` add `use_sendmail` varchar(5) DEFAULT 'false'");
ExecuteQuery("update vtiger_systems set use_sendmail='false'");

$migrationlog->debug("\n\nDB Changes from 5.2.1-20110624 to 5.2.1-20110824 -------- Ends \n\n");
?>
