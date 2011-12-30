<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$new_tables = 0;

require_once('config.php');
require_once('include/logging.php');
require_once('modules/Leads/Leads.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Accounts/Accounts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('modules/Users/Users.php');
require_once('modules/Users/LoginHistory.php');
require_once('data/Tracker.php');
require_once('include/utils/utils.php');
require_once('modules/Users/DefaultDataPopulator.php');
require_once('modules/Users/CreateUserPrivilegeFile.php');

// load the config_override.php file to provide default user settings
if (is_file("config_override.php")) {
	require_once("config_override.php");
}

$adb = PearDatabase::getInstance();
$log =& LoggerManager::getLogger('INSTALL');

function create_default_users_access() {
      	global $log, $adb;
        global $admin_email;
        global $admin_password;

        $role1_id = $adb->getUniqueID("vtiger_role");
		$role2_id = $adb->getUniqueID("vtiger_role");
		$role3_id = $adb->getUniqueID("vtiger_role");
		$role4_id = $adb->getUniqueID("vtiger_role");
		$role5_id = $adb->getUniqueID("vtiger_role");
		$role6_id = $adb->getUniqueID("vtiger_role");
		
		$profile1_id = $adb->getUniqueID("vtiger_profile");
		$profile2_id = $adb->getUniqueID("vtiger_profile");
		$profile3_id = $adb->getUniqueID("vtiger_profile");
		$profile4_id = $adb->getUniqueID("vtiger_profile");

			/*Old records
		$adb->query("insert into vtiger_role values('H".$role1_id."','Organisation','H".$role1_id."',0)");
        $adb->query("insert into vtiger_role values('H".$role2_id."','CEO','H".$role1_id."::H".$role2_id."',1)");
        $adb->query("insert into vtiger_role values('H".$role3_id."','Vice President','H".$role1_id."::H".$role2_id."::H".$role3_id."',2)");
        $adb->query("insert into vtiger_role values('H".$role4_id."','Sales Manager','H".$role1_id."::H".$role2_id."::H".$role3_id."::H".$role4_id."',3)");
        $adb->query("insert into vtiger_role values('H".$role5_id."','Sales Man','H".$role1_id."::H".$role2_id."::H".$role3_id."::H".$role4_id."::H".$role5_id."',4)");
		*/

		//vtiger-ru-fork 28.10.2010 Eugene Babiy. Добавлен Администратор
		$adb->query("insert into vtiger_role values('H".$role1_id."','Организация','H".$role1_id."',0)");
        $adb->query("insert into vtiger_role values('H".$role2_id."','Директор','H".$role1_id."::H".$role2_id."',1)");
        $adb->query("insert into vtiger_role values('H".$role3_id."','Администратор','H".$role1_id."::H".$role2_id."::H".$role3_id."',2)");
        $adb->query("insert into vtiger_role values('H".$role4_id."','Заместитель Директора','H".$role1_id."::H".$role2_id."::H".$role3_id."::H".$role4_id."',3)");
        $adb->query("insert into vtiger_role values('H".$role5_id."','Менеджер по Продажам','H".$role1_id."::H".$role2_id."::H".$role3_id."::H".$role4_id."::H".$role5_id."',4)");
        $adb->query("insert into vtiger_role values('H".$role6_id."','Продавец','H".$role1_id."::H".$role2_id."::H".$role3_id."::H".$role4_id."::H".$role5_id."::H".$role6_id."',5)");
        
		//Insert into vtiger_role2profile
		$adb->query("insert into vtiger_role2profile values ('H".$role2_id."',".$profile1_id.")");
		$adb->query("insert into vtiger_role2profile values ('H".$role3_id."',".$profile1_id.")");
	  	$adb->query("insert into vtiger_role2profile values ('H".$role4_id."',".$profile2_id.")");
		$adb->query("insert into vtiger_role2profile values ('H".$role5_id."',".$profile2_id.")");
		$adb->query("insert into vtiger_role2profile values ('H".$role6_id."',".$profile2_id.")");  
	   
		//New Security Start
		//Inserting into vtiger_profile vtiger_table
		$adb->query("insert into vtiger_profile values ('".$profile1_id."','Администратор','Профиль Администратора Системы')");	
		$adb->query("insert into vtiger_profile values ('".$profile2_id."','Продажи','Профиль относящийся к Продажам')");
		$adb->query("insert into vtiger_profile values ('".$profile3_id."','Поддержка','Профиль относящийся к Поддержке')");
		$adb->query("insert into vtiger_profile values ('".$profile4_id."','Гости','Гостевой профиль для Тестирования')");
		
		//Inserting into vtiger_profile2gloabal permissions
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile1_id."',1,0)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile1_id."',2,0)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile2_id."',1,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile2_id."',2,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile3_id."',1,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile3_id."',2,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile4_id."',1,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile4_id."',2,1)");

		//Inserting into vtiger_profile2tab
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",1,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",2,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",3,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",4,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",6,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",7,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",8,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",9,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",10,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",13,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",14,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",15,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",16,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",18,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",19,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",20,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",21,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",22,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",23,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",24,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",25,0)");
       	$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",26,0)");
       	$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",27,0)");

		//Inserting into vtiger_profile2tab
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",1,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",2,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",3,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",4,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",6,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",7,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",8,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",9,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",10,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",13,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",14,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",15,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",16,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",18,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",19,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",20,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",21,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",22,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",23,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",24,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",25,0)");
        $adb->query("insert into vtiger_profile2tab values (".$profile2_id.",26,0)");
       	$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",27,0)");

		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",1,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",2,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",3,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",4,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",6,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",7,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",8,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",9,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",10,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",13,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",14,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",15,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",16,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",18,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",19,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",20,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",21,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",22,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",23,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",24,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",25,0)");
        $adb->query("insert into vtiger_profile2tab values (".$profile3_id.",26,0)");
       	$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",27,0)");

		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",1,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",2,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",3,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",4,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",6,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",7,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",8,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",9,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",10,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",13,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",14,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",15,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",16,0)");	
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",18,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",19,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",20,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",21,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",22,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",23,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",24,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",25,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",26,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",27,0)");
		//Inserting into vtiger_profile2standardpermissions  Adminsitrator
		
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,4,0)");

        $adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,4,0)");

		//Insert into Profile 2 std permissions for Sales User  
		//Help Desk Create/Delete not allowed. Read-Only	
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,4,0)");
		
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,4,0)");


        	$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,4,0)");

		//Inserting into vtiger_profile2std for Support Profile
		// Potential is read-only
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,4,0)");


        $adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,4,0)");
        
		//Inserting into vtiger_profile2stdper for Profile Guest Profile
		//All Read-Only
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,4,0)");	
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,4,0)");	
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,4,0)");	


        $adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,4,0)");

		//Inserting into vtiger_profile 2 utility Admin
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",2,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",2,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",4,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",4,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",6,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",6,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",8,6,0)");
       	$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",6,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",4,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",13,5,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",13,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",13,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",14,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",14,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,9,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",18,5,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",18,6,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",6,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",4,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",2,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",13,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",14,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",18,10,0)");

		//Inserting into vtiger_profile2utility Sales Profile
		//Import Export Not Allowed.	
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",2,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",2,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",4,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",4,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",6,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",6,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",8,6,1)");
       	$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",6,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",4,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",13,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",13,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",13,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",14,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",14,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,9,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",18,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",18,6,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",6,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",4,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",2,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",13,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",14,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",18,10,0)");

		//Inserting into vtiger_profile2utility Support Profile
		//Import Export Not Allowed.	
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",2,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",2,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",4,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",4,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",6,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",6,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",8,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",6,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",4,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",13,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",13,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",13,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",14,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",14,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,9,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",18,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",18,6,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",6,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",4,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",2,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",13,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",14,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",18,10,0)");

		//Inserting into vtiger_profile2utility Guest Profile Read-Only
		//Import Export BusinessCar Not Allowed.	
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",2,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",2,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",4,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",4,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",6,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",6,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",8,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,8,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",6,8,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",4,8,1)");	
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",13,5,1)");
    	$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",13,6,1)");	 
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",13,8,1)");		
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",14,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",14,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,9,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",18,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",18,6,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",6,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",4,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",2,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",13,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",14,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",18,10,0)");
	
		 // Invalidate any cached information
    	VTCacheUtils::clearRoleSubordinates();

        // create default admin user
        //vtiger-ru-fork 28.10.2010 Eugene Babiy
    	$user = new Users();
        $user->column_fields["last_name"] = 'Администратор';
        $user->column_fields["user_name"] = 'admin';
        $user->column_fields["status"] = 'Active';
        $user->column_fields["is_admin"] = 'on';
        $user->column_fields["user_password"] = $admin_password;
        $user->column_fields["tz"] = 'Europe/Moscow';
        $user->column_fields["holidays"] = 'ru,';
        $user->column_fields["workdays"] = '1,2,3,4,5,6,0,';
        $user->column_fields["weekstart"] = '1';
        $user->column_fields["namedays"] = '';
        $user->column_fields["currency_id"] = 1;
        $user->column_fields["reminder_interval"] = '1 Minute';
        $user->column_fields["reminder_next_time"] = date('Y-m-d H:i');
		$user->column_fields["date_format"] = 'yyyy-mm-dd';
		$user->column_fields["hour_format"] = '24';
		$user->column_fields["start_hour"] = '08:00';
		$user->column_fields["end_hour"] = '23:00';
		$user->column_fields["imagename"] = '';
		$user->column_fields["internal_mailer"] = '1';
		$user->column_fields["activity_view"] = 'This Week';
		$user->column_fields["lead_view"] = 'Today';
        //added by philip for default admin emailid
		if($admin_email == '')
			$admin_email ="sales@salesplatform.ru";
        $user->column_fields["email1"] = $admin_email;
		$role_query = "select roleid from vtiger_role where rolename='Администратор'";
		$adb->checkConnection();
		$adb->database->SetFetchMode(ADODB_FETCH_ASSOC);
		$role_result = $adb->query($role_query);
		$role_id = $adb->query_result($role_result,0,"roleid");
		$user->column_fields["roleid"] = $role_id;

            // SalesPlatform.ru begin: Add default currency formatting
            $user->column_fields["currency_grouping_pattern"] = '123,456,789';
            $user->column_fields["currency_decimal_separator"] = ',';
            $user->column_fields["currency_grouping_separator"] = ' ';
            $user->column_fields["currency_symbol_placement"] = '1.0$';
            // SalesPlatform.ru end

        $user->save("Users");
        $admin_user_id = $user->id;

		//Inserting into vtiger_groups table
		$group1_id = $adb->getUniqueID("vtiger_users");
		$group2_id = $adb->getUniqueID("vtiger_users");
		$group3_id = $adb->getUniqueID("vtiger_users");


		//vtiger-ru-fork 28.10.2010 Eugene Babiy
		$adb->query("insert into vtiger_groups values ('".$group1_id."','Отдел Продаж','Группа менеджеров по Продажам')");
		$adb->query("insert into vtiger_group2role values ('".$group1_id."','H".$role4_id."')");
		$adb->query("insert into vtiger_group2rs values ('".$group1_id."','H".$role5_id."')");

		$adb->query("insert into vtiger_groups values ('".$group2_id."','Отдел Маркетинга','Группа менеджеров по Маркетингу')");
		$adb->query("insert into vtiger_group2role values ('".$group2_id."','H".$role2_id."')");
		$adb->query("insert into vtiger_group2rs values ('".$group2_id."','H".$role3_id."')");

		$adb->query("insert into vtiger_groups values ('".$group3_id."','Отдел Поддержки','Группа сервисной поддержки Клиентов')");
		$adb->query("insert into vtiger_group2role values ('".$group3_id."','H".$role3_id."')");
		$adb->query("insert into vtiger_group2rs values ('".$group3_id."','H".$role3_id."')");
		
		// Setting user group relation for admin user
	 	$adb->pquery("insert into vtiger_users2group values (?,?)", array($group2_id, $admin_user_id));

		//Creating the flat files for admin user
		createUserPrivilegesfile($admin_user_id);
		createUserSharingPrivilegesfile($admin_user_id);
		
		//Insert into vtiger_profile2field
		insertProfile2field($profile1_id);
        insertProfile2field($profile2_id);	
        insertProfile2field($profile3_id);	
        insertProfile2field($profile4_id);

	insert_def_org_field();
	
}

$modules = array("DefaultDataPopulator");
$focus=0;
$success = $adb->createTables("schema/DatabaseSchema.xml");

//Postgres8 fix - create sequences. 
//   This should be a part of "createTables" however ...
 if( $adb->dbType == "pgsql" ) {
     $sequences = array(
 	"vtiger_leadsource_seq",
 	"vtiger_accounttype_seq",
 	"vtiger_industry_seq",
 	"vtiger_leadstatus_seq",
 	"vtiger_rating_seq",
 	"vtiger_opportunity_type_seq",
 	"vtiger_salutationtype_seq",
 	"vtiger_sales_stage_seq",
 	"vtiger_ticketstatus_seq",
 	"vtiger_ticketpriorities_seq",
 	"vtiger_ticketseverities_seq",
 	"vtiger_ticketcategories_seq",
 	"vtiger_duration_minutes_seq",
 	"vtiger_eventstatus_seq",
 	"vtiger_taskstatus_seq",
 	"vtiger_taskpriority_seq",
 	"vtiger_manufacturer_seq",
 	"vtiger_productcategory_seq",
 	"vtiger_activitytype_seq",
 	"vtiger_currency_seq",
 	"vtiger_faqcategories_seq",
 	"vtiger_usageunit_seq",
 	"vtiger_glacct_seq",
 	"vtiger_quotestage_seq",
 	"vtiger_carrier_seq",
 	"vtiger_taxclass_seq",
 	"vtiger_recurringtype_seq",
 	"vtiger_faqstatus_seq",
 	"vtiger_invoicestatus_seq",
 	"vtiger_postatus_seq",
 	"vtiger_sostatus_seq",
 	"vtiger_visibility_seq",
 	"vtiger_campaigntype_seq",
 	"vtiger_campaignstatus_seq",
 	"vtiger_expectedresponse_seq",
 	"vtiger_status_seq",
 	"vtiger_activity_view_seq",
 	"vtiger_lead_view_seq",
 	"vtiger_date_format_seq",
 	"vtiger_users_seq",
 	"vtiger_role_seq",
 	"vtiger_profile_seq",
 	"vtiger_field_seq",
 	"vtiger_def_org_share_seq",
 	"vtiger_datashare_relatedmodules_seq",
 	"vtiger_relatedlists_seq",
 	"vtiger_notificationscheduler_seq",
 	"vtiger_inventorynotification_seq",
 	"vtiger_currency_info_seq",
 	"vtiger_emailtemplates_seq",
 	"vtiger_inventory_tandc_seq",
 	"vtiger_selectquery_seq",
 	"vtiger_customview_seq",
 	"vtiger_crmentity_seq",
 	"vtiger_seactivityrel_seq",
 	"vtiger_freetags_seq",
 	"vtiger_shippingtaxinfo_seq",
 	"vtiger_inventorytaxinfo_seq"
 	);
 
     foreach ($sequences as $sequence ) {
 	$log->info( "Creating sequence ".$sequence);
 	$adb->query( "CREATE SEQUENCE ".$sequence." INCREMENT BY 1 NO MAXVALUE NO MINVALUE CACHE 1;");
     }
 }


// TODO HTML
if($success==0)
	die("Error: Tables not created.  Table creation failed.\n");
elseif ($success==1)
	die("Error: Tables partially created.  Table creation failed.\n");

foreach ($modules as $module ) {
	$focus = new $module();
	$focus->create_tables();
}
			
create_default_users_access();

// create and populate combo tables
require_once('include/PopulateComboValues.php');
$combo = new PopulateComboValues();
$combo->create_tables();
$combo->create_nonpicklist_tables();
//Writing tab data in flat file
create_tab_data_file();
create_parenttab_data_file();

// default report population
require_once('modules/Reports/PopulateReports.php');

// default customview population
require_once('modules/CustomView/PopulateCustomView.php');

// ensure required sequences are created (adodb creates them as needed, but if
// creation occurs within a transaction we get problems
$adb->getUniqueID("vtiger_crmentity");
$adb->getUniqueID("vtiger_seactivityrel");
$adb->getUniqueID("vtiger_freetags");

//Master currency population
//Insert into vtiger_currency vtiger_table
$adb->pquery("insert into vtiger_currency_info values(?,?,?,?,?,?,?,?)", array($adb->getUniqueID("vtiger_currency_info"),$currency_name,$currency_code,$currency_symbol,1,'Active','-11','0'));

// SalesPlatform.ru begin
// Insert default PDF templates
$adb->pquery('INSERT INTO `sp_templates` VALUES (19,\'Счет-фактура\',\'SalesOrder\',\'{header}\n\n<p style=\"font-size: 6pt; text-align: right; margin: 0; padding: 0\">Приложение №1<br/>\nк Правилам ведения журналов учета полученных и выставленных счетов-фактур,<br/>\nкниг покупок и книг продаж при расчетах по налогу на добавленную стоимость,<br/>\nутвержденным постановлением Правительства Российской Федерации от 2 декабря 2000 г. N 914<br/>\n(в редакции постановлений Правительства Российской Федерации от 15 марта 2001 г. N 189,<br/>\nот 27 июля 2002 г. N 575, от 16 февраля 2004 г. N 84, от 11 мая 2006 г. N 283, от 26 мая 2009 г. N 451)<br/>\n</p>\n<h1 style=\"font-size: 12pt; margin: 0; padding: 0\">Счет-фактура № {$salesorder_factura_no} от {$salesorder_factura_date} </h1>\n<p style=\"font-size: 8pt; margin: 0; padding: 0\">Продавец: {$orgName} <br/> \nАдрес: {$orgBillingAddress} <br/> \nИНН/КПП продавца: {$orgInn}/{$orgKpp} <br/> \nГрузоотправитель и его адрес: он же <br/> \nГрузополучатель и его адрес: {$account_accountname}, {$billingAddress} <br/> \nК платежно-расчетному документу № {$salesorder_payment_no} от {$salesorder_payment_date} <br/> \nПокупатель: {$account_accountname} <br/> \nАдрес:	{$billingAddress} <br/> \nИНН / КПП покупателя: {$account_inn}/{$account_kpp}<br/> </p>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center\">\n	<td width=\"200\" valign=\"middle\">Наименование товара (описание<br/>выполненных работ, оказанных услуг),<br/>имущественного права</td>\n	<td width=\"37\" valign=\"middle\">Единица<br/>изме-<br/>рения</td>\n	<td width=\"55\" valign=\"middle\">Коли-<br/>чество</td>\n	<td width=\"55\" valign=\"middle\">Цена (тариф)<br/>за единицу<br/>измерения</td>\n	<td width=\"80\" valign=\"middle\">Стоимость товаров (работ,<br/>услуг),<br/>имущественных<br/>прав, всего без<br/>налога</td>\n	<td width=\"35\" valign=\"middle\">В том<br/>числе<br/>акциз</td>\n	<td width=\"55\" valign=\"middle\">Налоговая<br/>ставка</td>\n	<td width=\"55\" valign=\"middle\">Сумма<br/>налога</td>\n	<td width=\"80\" valign=\"middle\">Стоимость товаров (работ,<br/>услуг),<br/>имущественных<br/>прав, всего с<br/>учетом налога</td>\n	<td width=\"55\" valign=\"middle\">Страна<br/>происхож-<br/>дения</td>\n	<td width=\"75\" valign=\"middle\">Номер<br/>таможенной<br/>декларации</td>\n	</tr>\n    <tr style=\"text-align: center\">\n	<td width=\"200\">1</td>\n	<td width=\"37\">2</td>\n	<td width=\"55\">3</td>\n	<td width=\"55\">4</td>\n	<td width=\"80\">5</td>\n	<td width=\"35\">6</td>\n	<td width=\"55\">7</td>\n	<td width=\"55\">8</td>\n	<td width=\"80\">9</td>\n	<td width=\"55\">10</td>\n	<td width=\"75\">11</td>\n    </tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"200\" style=\"padding: 3px\">{$productName}</td>\n	<td width=\"37\" style=\"text-align: center;padding: 3px\">{$productUnits}</td>\n	<td width=\"55\" style=\"text-align: right;padding: 3px\">{$productQuantity}</td>\n	<td width=\"55\" style=\"text-align: right;padding: 3px\">{$productPrice}</td>\n	<td width=\"80\" style=\"text-align: right;padding: 3px\">{$productNetTotal}</td>\n	<td width=\"35\" style=\"text-align: right;padding: 3px\">--</td>\n	<td width=\"55\" style=\"text-align: center;padding: 3px\">{$productTaxPercent}%</td>\n	<td width=\"55\" style=\"text-align: right;padding: 3px\">{$productTax}</td>\n	<td width=\"80\" style=\"text-align: right;padding: 3px\">{$productTotal}</td>\n	<td width=\"55\" style=\"padding-left: 3px\">{$manufCountry}</td>\n	<td width=\"75\" style=\"padding: 3px\">{$customsId}</td>\n    </tr>\n{/table_row}\n\n{summary}\n    <tr>\n	<td width=\"517\" colspan=7><span style=\"font-weight: bold\">Всего к оплате</span></td>\n	<td width=\"55\" style=\"text-align: right\">{$summaryTax}</td>\n	<td width=\"80\" style=\"text-align: right\">{$summaryGrandTotal}</td>\n    </tr>\n</table>\n{/summary}\n\n{ending}\n<p></p>\n<table border=\"0\" style=\"font-size: 8pt\">\n<tr>\n    <td width=\"200\" style=\"text-align: right\">Руководитель организации </td>\n    <td width=\"80\" style=\"text-align: center\"> __________ </td>\n    <td width=\"80\" style=\"text-align: center\"> <span style=\"text-decoration: underline\">{$orgDirector}</span></td>\n    <td style=\"text-align: right\">Главный бухгалтер </td>\n    <td width=\"80\" style=\"text-align: center\">  __________ </td>\n    <td width=\"80\" style=\"text-align: center\"> <span style=\"text-decoration: underline\">{$orgBookkeeper}</span></td>\n</tr>\n<tr style=\"font-size: 6pt; text-align: center\">\n    <td width=\"200\"></td><td width=\"80\"> (подпись) </td><td width=\"80\"> (ФИО)</td><td></td><td width=\"80\"> (подпись)</td><td width=\"80\"> (ФИО)</td> \n</tr>\n<tr>\n<td colspan=\"6\"><p></p></td>\n</tr>\n<tr>\n    <td width=\"200\" style=\"text-align: right\">Индивидуальный предприниматель </td>\n    <td width=\"80\" style=\"text-align: center\"> __________ </td>\n    <td width=\"80\" style=\"text-align: center\"> <span style=\"text-decoration: underline\">{$orgEntrepreneur}</span> </td>\n    <td colspan=\"3\" style=\"text-align: center\"> <span style=\"text-decoration: underline\">{$orgEntrepreneurreg}</span> </td>\n</tr>\n<tr style=\"font-size: 6pt; text-align: center\">\n    <td width=\"200\"></td><td width=\"80\"> (подпись) </td><td width=\"80\"> (ФИО) </td><td colspan=\"3\"> (реквизиты свидетельства о государственной <br/>регистрации индивидуального предпринимателя)</td> \n</tr>\n</table>\n<p></p>\n<p style=\"font-size: 6pt\">Примечание. Первый  экземпляр  -  покупателю,  второй   экземпляр - продавцу</p>\n{/ending}\',85,50,\'L\'),(20,\'Счет\',\'Invoice\',\'{header}\n\n<table border=\"1\" cellpadding=\"2\">\n<tr>\n<td colspan=\"2\" rowspan=\"2\" width=\"280\">{$orgBankName}<br/><span style=\"font-size: 8pt\">Банк получателя</span></td>\n<td width=\"50\">БИК</td>\n<td rowspan=\"2\" width=\"200\">{$orgBankId}<br/>{$orgCorrAccount}</td>\n</tr>\n<tr>\n<td width=\"50\">Сч. №</td>\n</tr>\n<tr>\n<td width=\"140\">ИНН {$orgInn}</td><td width=\"140\">КПП {$orgKpp}</td><td rowspan=\"2\" width=\"50\">Сч. №</td><td rowspan=\"2\" width=\"200\">{$orgBankAccount}</td>\n</tr>\n<tr>\n<td colspan=\"2\" width=\"280\">{$orgName}<br/><span style=\"font-size: 8pt\">Получатель</span></td>\n</tr>\n</table>\n\n<h1>Счет на оплату № {$invoice_no} от {$invoice_invoicedate}</h1>\n<hr size=\"2\">\n<table border=\"0\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}, ИНН {$orgInn}, КПП {$orgKpp},<br/>{$orgBillingAddress}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$account_accountname}, {$billingAddress}</span></td>\n</tr>\n</table>\n\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\">№</td>\n	<td width=\"260\">Товары (работы, услуги)</td>\n	<td width=\"70\">Кол-во</td>\n	<td width=\"30\">Ед.</td>\n	<td width=\"70\">Цена</td>\n	<td width=\"70\">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\">{$productNumber}</td>\n	<td width=\"260\">{$productName}</td>\n	<td width=\"70\">{$productUnits}</td>\n	<td width=\"30\" style=\"text-align: right\">{$productQuantity}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Итого:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Всего к оплате:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryGrandTotal}</td>\n    </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <p>Руководитель предприятия  __________ ( {$orgDirector} ) <br/>\n    <br/>\n    Главный бухгалтер  __________ ( {$orgBookkeeper} )\n    </p>\n{/ending}\',85,50,\'P\'),(21,\'Накладная\',\'SalesOrder\',\'{header}\n<h1 style=\"font-size: 14pt\">Расходная накладная № {$salesorder_no} от {$salesorder_factura_date} </h1>\n<hr>\n<table border=\"0\" style=\"font-size: 9pt\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$account_accountname}</span></td>\n</tr>\n</table>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\" rowspan=\"2\">№</td>\n	<td width=\"200\" rowspan=\"2\">Товар</td>\n	<td width=\"50\" rowspan=\"2\" colspan=\"2\">Мест</td>\n	<td width=\"60\" rowspan=\"2\" colspan=\"2\">Количество</td>\n	<td width=\"60\" rowspan=\"2\">Цена</td>\n	<td width=\"60\" rowspan=\"2\">Сумма</td>\n	<td width=\"70\">Номер ГТД</td>\n    </tr>\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"70\">Страна<br/>происхождения</td>\n    </tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\" rowspan=\"2\">{$productNumber}</td>\n	<td width=\"200\" rowspan=\"2\">{$productName}</td>\n	<td width=\"25\" rowspan=\"2\"></td>\n	<td width=\"25\" rowspan=\"2\">шт.</td>\n	<td width=\"30\" rowspan=\"2\" style=\"text-align: right\">{$productQuantityInt}</td>\n	<td width=\"30\" rowspan=\"2\">{$productUnits}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productNetTotal}</td>\n	<td width=\"70\">{$customsId}</td>\n    </tr>\n    <tr>\n	<td width=\"70\">{$manufCountry}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Итого:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <table border=\"0\">\n    <tr>\n	<td>Отпустил  __________ </td><td>Получил  __________ </td>\n    </tr>\n    </table>\n{/ending}\n\',50,0,\'P\'),(22,\'Предложение\',\'Quotes\',\'\n{header}\n\n<p style=\"font-weight: bold\">\n{$orgName}<br/>\nИНН {$orgInn}<br/>\nКПП {$orgKpp}<br/>\n{$orgBillingAddress}<br/>\nТел.: {$orgPhone}<br/>\nФакс: {$orgFax}<br/>\n{$orgWebsite}\n</p>\n\n<h1>Коммерческое предложение № {$quote_no}</h1>\n<p>Действительно до: {$quote_validtill}</p>\n<hr size=\"2\">\n\n<p style=\"font-weight: bold\">\n{$account_accountname}<br/>\n{$billingAddress}\n</p>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\">№</td>\n	<td width=\"260\">Товары (работы, услуги)</td>\n	<td width=\"70\">Кол-во</td>\n	<td width=\"30\">Ед.</td>\n	<td width=\"70\">Цена</td>\n	<td width=\"70\">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\">{$productNumber}</td>\n	<td width=\"260\">{$productName}</td>\n	<td width=\"70\">{$productUnits}</td>\n	<td width=\"30\" style=\"text-align: right\">{$productQuantity}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\">\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Итого:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p style=\"font-weight: bold\">\nВсего: {$summaryGrandTotal} руб. ( {$summaryGrandTotalLiteral} )\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <p>Руководитель предприятия  __________ ( {$orgDirector} ) <br/>\n    </p>\n{/ending}\n\',85,0,\'P\')', array());
// SalesPlatform.ru end

// Register All the Events
registerEvents($adb);

// Register All the Entity Methods
registerEntityMethods($adb);

// Populate Default Workflows
populateDefaultWorkflows($adb);

// Populate Links
populateLinks();

// Set Help Information for Fields
setFieldHelpInfo();

// Register all the events here
function registerEvents($adb) {
	require_once('include/events/include.inc');
	$em = new VTEventsManager($adb);

	// Registering event for Recurring Invoices
	$em->registerHandler('vtiger.entity.aftersave', 'modules/SalesOrder/RecurringInvoiceHandler.php', 'RecurringInvoiceHandler');

	//Registering Entity Delta handler for before save and after save events of the record to track the field value changes
	$em->registerHandler('vtiger.entity.beforesave', 'data/VTEntityDelta.php', 'VTEntityDelta');
	$em->registerHandler('vtiger.entity.aftersave', 'data/VTEntityDelta.php', 'VTEntityDelta');
	
	// Workflow manager
	$dependentEventHandlers = array('VTEntityDelta');
	$dependentEventHandlersJson = Zend_Json::encode($dependentEventHandlers);
	$em->registerHandler('vtiger.entity.aftersave', 'modules/com_vtiger_workflow/VTEventHandler.inc', 'VTWorkflowEventHandler',
								'',$dependentEventHandlersJson);
	
	//Registering events for On modify
	$em->registerHandler('vtiger.entity.afterrestore', 'modules/com_vtiger_workflow/VTEventHandler.inc', 'VTWorkflowEventHandler');
}

// Register all the entity methods here
function registerEntityMethods($adb) {
	require_once("modules/com_vtiger_workflow/include.inc");
	require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
	require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");
	$emm = new VTEntityMethodManager($adb);
	
	// Registering method for Updating Inventory Stock
	$emm->addEntityMethod("SalesOrder","UpdateInventory","include/InventoryHandler.php","handleInventoryProductRel");//Adding EntityMethod for Updating Products data after creating SalesOrder
	$emm->addEntityMethod("Invoice","UpdateInventory","include/InventoryHandler.php","handleInventoryProductRel");//Adding EntityMethod for Updating Products data after creating Invoice
}

function populateDefaultWorkflows($adb) {
	require_once("modules/com_vtiger_workflow/include.inc");
	require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
	require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");

	// Creating Workflow for Updating Inventory Stock for Invoice
	$vtWorkFlow = new VTWorkflowManager($adb);
	$invWorkFlow = $vtWorkFlow->newWorkFlow("SalesOrder");
	$invWorkFlow->test = '[{"fieldname":"sostatus","operation":"is","value":"Delivered"}]';
	$invWorkFlow->description = "Обновление склада при доставке заказа";
	$invWorkFlow->executionCondition=2;	
	$vtWorkFlow->save($invWorkFlow);

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEntityMethodTask', $invWorkFlow->id);
	$task->active=true;
	$task->methodName = "UpdateInventory";
	$tm->saveTask($task);
	
	
	// Creating Workflow for Accounts when Notifyowner is true
	
	$vtaWorkFlow = new VTWorkflowManager($adb);
	$accWorkFlow = $vtaWorkFlow->newWorkFlow("Accounts");
	$accWorkFlow->test = '[{"fieldname":"notify_owner","operation":"is","value":"true:boolean"}]';
	$accWorkFlow->description = "Отправить Email пользователю, если указано Уведомлять ответственного";
	$accWorkFlow->executionCondition=2;	
	$vtaWorkFlow->save($accWorkFlow);
	$id1=$accWorkFlow->id;
	
	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$accWorkFlow->id);
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Уведомление о назначении Контрагента";
	$task->content = "В системе vtigerCRM Вам был назначен контрагент<br>Информация о контрагенте :<br><br>".
			"Контрагент №:".'<b>$account_no</b><br>'."Контрагент:".'<b>$accountname</b><br>'."Рейтинг:".'<b>$rating</b><br>'.
			"Отрасль:".'<b>$industry</b><br>'."Тип:".'<b>$accounttype</b><br>'.
			"Описание:".'<b>$description</b><br><br><br>'."Спасибо,<br>Admin";
	$task->summary="Создан Контрагент ";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));
	
	// Creating Workflow for Contacts when Notifyowner is true
	
	$vtcWorkFlow = new VTWorkflowManager($adb);
	$conWorkFlow = 	$vtcWorkFlow->newWorkFlow("Contacts");
	$conWorkFlow->summary="Создан Контакт ";
	$conWorkFlow->executionCondition=2;
	$conWorkFlow->test = '[{"fieldname":"notify_owner","operation":"is","value":"true:boolean"}]';
	$conWorkFlow->description = "Отправить Email пользователю, если указано Уведомлять ответственного";
	
	$vtcWorkFlow->save($conWorkFlow);
	$id1=$conWorkFlow->id;
	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$conWorkFlow->id);
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Уведомление о назначении Контакта";
	$task->content = "В системе vtigerCRM Вам был назначен контакт<br>Информация о контакте :<br><br>".
			"Контакт №:".'<b>$contact_no</b><br>'."Фамилия:".'<b>$lastname</b><br>'."Имя:".'<b>$firstname</b><br>'.
			"Источник:".'<b>$leadsource</b><br>'.
			"Отдел:".'<b>$department</b><br>'.
			"Описание:".'<b>$description</b><br><br><br>'."Спасибо,<br>Admin";
	$task->summary="Создан Контакт ";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));
	
	
	// Creating Workflow for Contacts when PortalUser is true
	
	$vtcWorkFlow = new VTWorkflowManager($adb);
	$conpuWorkFlow = $vtcWorkFlow->newWorkFlow("Contacts");
	$conpuWorkFlow->test = '[{"fieldname":"portal","operation":"is","value":"true:boolean"}]';
	$conpuWorkFlow->description = "Отправить Email пользователю, если контакт стал пользователем портала";
	$conpuWorkFlow->executionCondition=2;
	$vtcWorkFlow->save($conpuWorkFlow);
	$id1=$conpuWorkFlow->id;
	
	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$conpuWorkFlow->id);
	
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Уведомление о назначении Контакта";
	$task->content = "В системе vtigerCRM Вам был назначен контакт<br>Информация о контакте :<br><br>".
			"Контакт №:".'<b>$contact_no</b><br>'."Фамилия:".'<b>$lastname</b><br>'."Имя:".'<b>$firstname</b><br>'.
			"Источник:".'<b>$leadsource</b><br>'.
			"Отдел:".'<b>$department</b><br>'.
			"Описание:".'<b>$description</b><br><br><br>'."А также <b>детали логина на CustomerPortal</b> были отправлены " .
			"Email :-".'$email<br>'."<br>Спасибо,<br>Admin";
		
	$task->summary="Создан Контакт ";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));

	// Creating Workflow for Potentials

	$vtcWorkFlow = new VTWorkflowManager($adb);
	$potentialWorkFlow = $vtcWorkFlow->newWorkFlow("Potentials");
	$potentialWorkFlow->description = "Отправить Email пользователю при создании Сделки";
	$potentialWorkFlow->executionCondition=1;
	$vtcWorkFlow->save($potentialWorkFlow);
	$id1=$potentialWorkFlow->id;

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$potentialWorkFlow->id);

	$task->active=true;
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Уведомление о назначении Сделки";
	$task->content = "В системе vtigerCRM Вам была назначена сделка<br>Информация о сделке :<br><br>".
			"Сделка №:".'<b>$potential_no</b><br>'."Название Сделки:".'<b>$potentialname</b><br>'.
			"Сумма (руб):".'<b>$amount</b><br>'.
			"Ожидаемая Дата Закрытия:".'<b>$closingdate</b><br>'.
			"Тип:".'<b>$opportunity_type</b><br><br><br>'.
			"Описание:".'$description<br>'."<br>Спасибо,<br>Admin";

	$task->summary="Создана сделка ";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));
}

// Function to populate Links
function populateLinks() {
	include_once('vtlib/Vtiger/Module.php');
	
	// Links for Accounts module
	$accountInstance = Vtiger_Module::getInstance('Accounts');
	// Detail View Custom link
	$accountInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE', 
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'themes/images/bookMark.gif'
	);
	$accountInstance->addLink('DETAILVIEWBASIC', 'LBL_SHOW_ACCOUNT_HIERARCHY', 'index.php?module=Accounts&action=AccountHierarchy&accountid=$RECORD$');
	
	$leadInstance = Vtiger_Module::getInstance('Leads');
	$leadInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE', 
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'themes/images/bookMark.gif'
	);
	
	$contactInstance = Vtiger_Module::getInstance('Contacts');
	$contactInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE', 
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'themes/images/bookMark.gif'
	);
}
	
function setFieldHelpInfo() {
	// Added Help Info for Hours and Days fields of HelpDesk module.
	require_once('vtlib/Vtiger/Module.php');
	$tt_module = Vtiger_Module::getInstance('HelpDesk');
	$field1 = Vtiger_Field::getInstance('hours',$tt_module);
	$field2 = Vtiger_Field::getInstance('days',$tt_module);
	
	$field1->setHelpInfo('Это оценка трудоемкости заявки в часах.'.
				'<br>При добавлении заявки к Сервисному Контракту '. 
				'число использованных единиц обслуживания автоматически увеличивается при закрытии заявки '.
				'на величину, указанную в заявке. '.
				'При этом выбирается оценка в зависимости от значения поля Отслеживаемая Единица.');

	$field2->setHelpInfo('Это оценка трудоемкости заявки в днях.'.
				'<br>При добавлении заявки к Сервисному Контракту '. 
				'число использованных единиц обслуживания автоматически увеличивается при закрытии заявки '.
				'на величину, указанную в заявке. '.
				'При этом выбирается оценка в зависимости от значения поля Отслеживаемая Единица.');

	$usersModuleInstance = Vtiger_Module::getInstance('Users');
	$field1 = Vtiger_Field::getInstance('currency_grouping_pattern', $usersModuleInstance);
	$field2 = Vtiger_Field::getInstance('currency_decimal_separator', $usersModuleInstance);
	$field3 = Vtiger_Field::getInstance('currency_grouping_separator', $usersModuleInstance);
	$field4 = Vtiger_Field::getInstance('currency_symbol_placement', $usersModuleInstance);

	$field1->setHelpInfo("<b>Currency - Digit Grouping Pattern</b> <br/><br/>".
								"This pattern specifies the format in which the currency separator will be placed.");
	$field2->setHelpInfo("<b>Currency - Decimal Separator</b> <br/><br/>".
										"Decimal separator specifies the separator to be used to separate ".
										"the fractional values from the whole number part. <br/>".
										"<b>Eg:</b> <br/>".
										". => 123.45 <br/>".
										", => 123,45 <br/>".
										"' => 123'45 <br/>".
										"  => 123 45 <br/>".
										"$ => 123$45 <br/>");
	$field3->setHelpInfo("<b>Currency - Grouping Separator</b> <br/><br/>".
										"Grouping separator specifies the separator to be used to group ".
										"the whole number part into hundreds, thousands etc. <br/>".
										"<b>Eg:</b> <br/>".
										". => 123.456.789 <br/>".
										", => 123,456,789 <br/>".
										"' => 123'456'789 <br/>".
										"  => 123 456 789 <br/>".
										"$ => 123$456$789 <br/>");
	$field4->setHelpInfo("<b>Currency - Symbol Placement</b> <br/><br/>".
										"Symbol Placement allows you to configure the position of the ".
										"currency symbol with respect to the currency value.<br/>".
										"<b>Eg:</b> <br/>".
										"$1.0 => $123,456,789.50 <br/>".
										"1.0$ => 123,456,789.50$ <br/>");
}

?>
