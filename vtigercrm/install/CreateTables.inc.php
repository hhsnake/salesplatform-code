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
                // SalesPlatform.ru end Added perms to PDF templates in installer
                $adb->query("insert into vtiger_profile2tab values (".$profile1_id.",30,1)");
                // SalesPlatform.ru end

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
                // SalesPlatform.ru end Added perms to PDF templates in installer
                $adb->query("insert into vtiger_profile2tab values (".$profile2_id.",30,1)");
                // SalesPlatform.ru end

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
                // SalesPlatform.ru end Added perms to PDF templates in installer
                $adb->query("insert into vtiger_profile2tab values (".$profile3_id.",30,1)");
                // SalesPlatform.ru end

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
                // SalesPlatform.ru end Added perms to PDF templates in installer
                $adb->query("insert into vtiger_profile2tab values (".$profile4_id.",30,1)");
                // SalesPlatform.ru end

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
        // SalesPlatform.ru begin Added Potentials merge perms
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",2,8,0)");
        // SalesPlatform.ru end
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
        // SalesPlatform.ru begin Added Potentials merge perms
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",2,8,0)");
        // SalesPlatform.ru end
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
        // SalesPlatform.ru begin Added Potentials merge perms
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",2,8,0)");
        // SalesPlatform.ru end
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
        // SalesPlatform.ru begin Added Potentials merge perms
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",2,8,1)");
        // SalesPlatform.ru end
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
			$admin_email ="noreply@salesplatform.ru";
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
$adb->getUniqueID("sp_templates");
$adb->getUniqueID("sp_templates");
$adb->getUniqueID("sp_templates");
$adb->getUniqueID("sp_templates");
$adb->pquery('INSERT INTO `sp_templates` VALUES (1,\'Счет\',\'Invoice\',\'{header}\n\n<p style="font-weight: bold; text-decoration: underline">{$orgName}</p>\n\n<p style="font-weight: bold">Адрес: {$orgBillingAddress}, тел.: {$orgPhone}</p>\n\n<div style="font-weight: bold; text-align: center">Образец заполнения платежного поручения</div>\n\n<table border="1" cellpadding="2">\n<tr>\n  <td width="140">ИНН {$orgInn}</td><td width="140">КПП {$orgKpp}</td><td rowspan="2" width="50"><br/><br/>Сч. №</td><td rowspan="2" width="200"><br/><br/>{$orgBankAccount}</td>\n</tr>\n<tr>\n<td colspan="2" width="280"><span style="font-size: 8pt">Получатель</span><br/>{$orgName}</td>\n</tr>\n<tr>\n<td colspan="2" rowspan="2" width="280"><span style="font-size: 8pt">Банк получателя</span><br/>{$orgBankName}</td>\n<td width="50">БИК</td>\n<td rowspan="2" width="200">{$orgBankId}<br/>{$orgCorrAccount}</td>\n</tr>\n<tr>\n<td width="50">Сч. №</td>\n</tr>\n</table>\n<br/>\n<h1 style="text-align: center">СЧЕТ № {$invoice_no} от {$invoice_invoicedate}</h1>\n<br/><br/>\n<table border="0">\n<tr>\n<td width="100">Плательщик:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>\n</tr>\n<tr>\n<td width="100">Грузополучатель:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>\n</tr>\n</table>\n\n{/header}\n\n{table_head}\n<table border="1" style="font-size: 8pt" cellpadding="2">\n    <tr style="text-align: center; font-weight: bold">\n	<td width="30">№</td>\n      <td width="260">Наименование<br/>товара</td>\n      <td width="65">Единица<br/>изме-<br/>рения</td>\n      <td width="35">Коли-<br/>чество</td>\n	<td width="70">Цена</td>\n	<td width="70">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width="30">{$productNumber}</td>\n      <td width="260">{$productName} {$productComment}</td>\n	<td width="65" style="text-align: center">{$productUnits}</td>\n	<td width="35" style="text-align: right">{$productQuantityInt}</td>\n	<td width="70" style="text-align: right">{$productPrice}</td>\n	<td width="70" style="text-align: right">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<table border="0" style="font-size: 8pt;font-weight: bold">\n    <tr>\n      <td width="460">\n        <table border="0" cellpadding="2">\n          <tr><td width="460" style="text-align: right">Итого:</td></tr>\n          <tr><td width="460" style="text-align: right">Сумма НДС:</td></tr>\n          <tr><td width="460" style="text-align: right">Всего к оплате:</td></tr>\n        </table>\n      </td>\n      <td width="70">\n        <table border="1" cellpadding="2">\n          <tr><td width="70" style="text-align: right">{$summaryNetTotal}</td></tr>\n          <tr><td width="70" style="text-align: right">{$summaryTax}</td></tr>\n          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>\n        </table>\n      </td>\n  </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n<br/>\n    <p>Руководитель предприятия  __________________ ( {$orgDirector} ) <br/>\n    <br/>\n    Главный бухгалтер  __________________ ( {$orgBookkeeper} )\n    </p>\n{/ending}\',110,50,\'P\'),(2,\'Накладная\',\'SalesOrder\',\'{header}\n<h1 style=\"font-size: 14pt\">Расходная накладная № {$salesorder_no}</h1>\n<hr>\n<table border=\"0\" style=\"font-size: 9pt\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$account_accountname}</span></td>\n</tr>\n</table>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\" rowspan=\"2\">№</td>\n	<td width=\"200\" rowspan=\"2\">Товар</td>\n	<td width=\"50\" rowspan=\"2\" colspan=\"2\">Мест</td>\n	<td width=\"60\" rowspan=\"2\" colspan=\"2\">Количество</td>\n	<td width=\"60\" rowspan=\"2\">Цена</td>\n	<td width=\"60\" rowspan=\"2\">Сумма</td>\n	<td width=\"70\">Номер ГТД</td>\n    </tr>\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"70\">Страна<br/>происхождения</td>\n    </tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\" rowspan=\"2\">{$productNumber}</td>\n	<td width=\"200\" rowspan=\"2\">{$productName}</td>\n	<td width=\"25\" rowspan=\"2\"></td>\n	<td width=\"25\" rowspan=\"2\">шт.</td>\n	<td width=\"30\" rowspan=\"2\" style=\"text-align: right\">{$productQuantityInt}</td>\n	<td width=\"30\" rowspan=\"2\">{$productUnits}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productNetTotal}</td>\n	<td width=\"70\">{$customsId}</td>\n    </tr>\n    <tr>\n	<td width=\"70\">{$manufCountry}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Итого:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <table border=\"0\">\n    <tr>\n	<td>Отпустил  __________ </td><td>Получил  __________ </td>\n    </tr>\n    </table>\n{/ending}\n\',50,0,\'P\'),(3,\'Предложение\',\'Quotes\',\'\n{header}\n\n<p align="right">{$orgLogo}</p>\n<p style=\"font-weight: bold\">\n{$orgName}<br/>\nИНН {$orgInn}<br/>\nКПП {$orgKpp}<br/>\n{$orgBillingAddress}<br/>\nТел.: {$orgPhone}<br/>\nФакс: {$orgFax}<br/>\n{$orgWebsite}\n</p>\n\n<h1>Коммерческое предложение № {$quote_no}</h1>\n<p>Действительно до: {$quote_validtill}</p>\n<hr size=\"2\">\n\n<p style=\"font-weight: bold\">\n{$account_accountname}<br/>\n{$billingAddress}\n</p>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\">№</td>\n	<td width=\"260\">Товары (работы, услуги)</td>\n	<td width=\"70\">Ед.</td>\n	<td width=\"30\">Кол-во</td>\n	<td width=\"70\">Цена</td>\n	<td width=\"70\">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\">{$productNumber}</td>\n	<td width=\"260\">{$productName}</td>\n	<td width=\"70\">{$productUnits}</td>\n	<td width=\"30\" style=\"text-align: right\">{$productQuantity}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\">\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Итого:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p style=\"font-weight: bold\">\nВсего: {$summaryGrandTotal} руб. ( {$summaryGrandTotalLiteral} )\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <p>Руководитель предприятия  __________ ( {$orgDirector} ) <br/>\n    </p>\n{/ending}\n\',100,0,\'P\'),(4,\'Заказ на закупку\',\'PurchaseOrder\',\'{header}\n<h1 style=\"font-size: 14pt\">Заказ на закупку № {$purchaseorder_no}</h1>\n<hr>\n<table border=\"0\" style=\"font-size: 9pt\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$vendor_vendorname}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}</span></td>\n</tr>\n</table>\n{/header}\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n<tr style=\"text-align: center; font-weight: bold\">\n<td width=\"30\">№</td>\n<td width=\"200\">Товар</td>\n<td width=\"60\" colspan=\"2\">Количество</td>\n<td width=\"60\">Цена</td>\n<td width=\"60\">Сумма</td>\n</tr>\n{/table_head}\n{table_row}\n<tr>\n<td width=\"30\">{$productNumber}</td>\n<td width=\"200\">{$productName}</td>\n<td width=\"30\" style=\"text-align: right\">{$productQuantityInt}</td>\n<td width=\"30\">{$productUnits}</td>\n<td width=\"60\" style=\"text-align: right\">{$productPrice}</td>\n<td width=\"60\" style=\"text-align: right\">{$productNetTotal}</td>\n</tr>\n{/table_row}\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n<tr>\n<td width=\"350\" style=\"text-align: right\">Итого:</td>\n<td width=\"60\" style=\"text-align: right\">{$summaryNetTotal}</td>\n</tr>\n<tr>\n<td width=\"350\" style=\"text-align: right\">Сумма НДС:</td>\n<td width=\"60\" style=\"text-align: right\">{$summaryTax}</td>\n</tr>\n</table>\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n{/summary}\n{ending}\n{/ending}\',50,0,\'P\')', array());
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

// Register Cron Jobs
registerCronTasks();

// Register all the Cron Tasks
function registerCronTasks() {
	include_once 'vtlib/Vtiger/Cron.php';
        
        // SalesPlatform.ru begin localization for 5.4.0
        Vtiger_Cron::register( 'Workflow', 'cron/modules/com_vtiger_workflow/com_vtiger_workflow.service', 900, 'com_vtiger_workflow', 1, 1, 'Рекомендуемая частота обновления для Обработчиков - 15 минут.');
	Vtiger_Cron::register( 'RecurringInvoice', 'cron/modules/SalesOrder/RecurringInvoice.service', 43200, 'SalesOrder', 1, 2, 'Рекомендуемая частота обновления для RecurringInvoice - 12 часов.');
	Vtiger_Cron::register( 'SendReminder', 'cron/SendReminder.service', 900, 'Calendar', 1, 3, 'Рекомендуемая частота обновления для SendReminder - 15 минут.');
	Vtiger_Cron::register( 'ScheduleReports', 'cron/modules/Reports/ScheduleReports.service', 900, 'Reports', 1, 4, 'Рекомендуемая частота обновления для ScheduleReports - 15 минут.');
	Vtiger_Cron::register( 'MailScanner', 'cron/MailScanner.service', 900, 'Settings', 1, 5, 'Рекомендуемая частота обновления для MailScanner - 15 минут.');
	// Vtiger_Cron::register( 'Workflow', 'cron/modules/com_vtiger_workflow/com_vtiger_workflow.service', 900, 'com_vtiger_workflow', 1, 1, 'Recommended frequency for Workflow is 15 mins');
	// Vtiger_Cron::register( 'RecurringInvoice', 'cron/modules/SalesOrder/RecurringInvoice.service', 43200, 'SalesOrder', 1, 2, 'Recommended frequency for RecurringInvoice is 12 hours');
	// Vtiger_Cron::register( 'SendReminder', 'cron/SendReminder.service', 900, 'Calendar', 1, 3, 'Recommended frequency for SendReminder is 15 mins');
	// Vtiger_Cron::register( 'ScheduleReports', 'cron/modules/Reports/ScheduleReports.service', 900, 'Reports', 1, 4, 'Recommended frequency for ScheduleReports is 15 mins');
	// Vtiger_Cron::register( 'MailScanner', 'cron/MailScanner.service', 900, 'Settings', 1, 5, 'Recommended frequency for MailScanner is 15 mins');
        // SalesPlatform.ru end
        
}

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

	// Registering event for HelpDesk - To reset from_portal value
	$em->registerHandler('vtiger.entity.aftersave.final', 'modules/HelpDesk/HelpDeskHandler.php', 'HelpDeskHandler');
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
        // SalesPlatform.ru begin: Added PurchaseOrder entity method
	$emm->addEntityMethod("PurchaseOrder","UpdateInventoryPurchase","include/InventoryHandler.php","handleInventoryPurchase");
        // SalesPlatform.ru end

	// Register Entity Method for Customer Portal Login details email notification task
	$emm->addEntityMethod("Contacts","SendPortalLoginDetails","modules/Contacts/ContactsHandler.php","Contacts_sendCustomerPortalLoginDetails");

	// Register Entity Method for Email notification on ticket creation from Customer portal
	$emm->addEntityMethod("HelpDesk","NotifyOnPortalTicketCreation","modules/HelpDesk/HelpDeskHandler.php","HelpDesk_nofifyOnPortalTicketCreation");

	// Register Entity Method for Email notification on ticket comment from Customer portal
	$emm->addEntityMethod("HelpDesk","NotifyOnPortalTicketComment","modules/HelpDesk/HelpDeskHandler.php","HelpDesk_notifyOnPortalTicketComment");

	// Register Entity Method for Email notification to Record Owner on ticket change, which is not from Customer portal
	$emm->addEntityMethod("HelpDesk","NotifyOwnerOnTicketChange","modules/HelpDesk/HelpDeskHandler.php","HelpDesk_notifyOwnerOnTicketChange");

	// Register Entity Method for Email notification to Related Customer on ticket change, which is not from Customer portal
	$emm->addEntityMethod("HelpDesk","NotifyParentOnTicketChange","modules/HelpDesk/HelpDeskHandler.php","HelpDesk_notifyParentOnTicketChange");
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
	$accWorkFlow->defaultworkflow = 1;
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
	$conWorkFlow->defaultworkflow = 1;
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
	$conpuWorkFlow->defaultworkflow = 1;
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
	$potentialWorkFlow->defaultworkflow = 1;
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

	$workflowManager = new VTWorkflowManager($adb);
	$taskManager = new VTTaskManager($adb);

	// Contact workflow on creation/modification
	$contactWorkFlow = $workflowManager->newWorkFlow("Contacts");
	$contactWorkFlow->test = '';
	//SalesPlatform.ru begin
        $contactWorkFlow->description = "Автоматические обработчики для создания и модификации контактов";
        //$contactWorkFlow->description = "Workflow for Contact Creation or Modification";
	//SalesPlatform.ru end
        $contactWorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
	$contactWorkFlow->defaultworkflow = 1;
	$workflowManager->save($contactWorkFlow);

	$task = $taskManager->createTask('VTEntityMethodTask', $contactWorkFlow->id);
	$task->active = true;
        //SalesPlatform.ru begin
        $task->summary = 'Сообщение с регистрационными данными клиентского портала';
	//$task->summary = 'Email Customer Portal Login Details';
        //SalesPlatform.ru end
	$task->methodName = "SendPortalLoginDetails";
	$taskManager->saveTask($task);

	// Trouble Tickets workflow on creation from Customer Portal
	$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
	$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"true:boolean"}]';
	//SalesPlatform.ru begin
        $helpDeskWorkflow->description = "Автоматический обработчик для создаваемой заявки из портала";
        //$helpDeskWorkflow->description = "Workflow for Ticket Created from Portal";
	//SalesPlatform.ru end
        $helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_FIRST_SAVE;
	$helpDeskWorkflow->defaultworkflow = 1;
	$workflowManager->save($helpDeskWorkflow);

	$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
	$task->active = true;
	//SalesPlatform.ru begin
        $task->summary = 'Уведомляет ответственного за заявку и связанный контакт при создании заявки из портала';
        //$task->summary = 'Notify Record Owner and the Related Contact when Ticket is created from Portal';
	//SalesPlatform.ru end
        $task->methodName = "NotifyOnPortalTicketCreation";
	$taskManager->saveTask($task);

	// Trouble Tickets workflow on ticket update from Customer Portal
	$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
	$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"true:boolean"}]';
	//SalesPlatform.ru begin
        $helpDeskWorkflow->description = "Автоматические обработчики для обновляемых заявок из портала";
        //$helpDeskWorkflow->description = "Workflow for Ticket Updated from Portal";
	//SalesPlatform.ru end
        $helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_MODIFY;
	$helpDeskWorkflow->defaultworkflow = 1;
	$workflowManager->save($helpDeskWorkflow);

	$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
	$task->active = true;
	//SalesPlatform.ru begin
        $task->summary = 'Уведомляет ответственного за заявку при добавлении комментариев к заявке через клиентский портал';
        //$task->summary = 'Notify Record Owner when Comment is added to a Ticket from Customer Portal';
        //SalesPlatform.ru end
        $task->methodName = "NotifyOnPortalTicketComment";
	$taskManager->saveTask($task);

	// Trouble Tickets workflow on ticket change, which is not from Customer Portal - Both Record Owner and Related Customer
	$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
	$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"false:boolean"}]';
	//SalesPlatform.ru begin
        $helpDeskWorkflow->description = "Обработчик для изменяемых заявок, не из портала";
        //$helpDeskWorkflow->description = "Workflow for Ticket Change, not from the Portal";
	//SalesPlatform.ru end
        $helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
	$helpDeskWorkflow->defaultworkflow = 1;
	$workflowManager->save($helpDeskWorkflow);

	$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
	$task->active = true;
        //SalesPlatform.ru begin
        $task->summary = 'Уведомляет ответственного за заявку, созданную не из портала, при ее изменении';
	//$task->summary = 'Notify Record Owner on Ticket Change, which is not done from Portal';
	//SalesPlatform.ru end
        $task->methodName = "NotifyOwnerOnTicketChange";
	$taskManager->saveTask($task);

	$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
	$task->active = true;
	//SalesPlatform.ru begin
        $task->summary = 'Уведомляет связанного клиента при изменении заявки, которая создана не из портала';
        //$task->summary = 'Notify Related Customer on Ticket Change, which is not done from Portal';
	//SalesPlatform.ru end
        $task->methodName = "NotifyParentOnTicketChange";
	$taskManager->saveTask($task);

	// Events workflow when Send Notification is checked
	$eventsWorkflow = $workflowManager->newWorkFlow("Events");
	$eventsWorkflow->test = '[{"fieldname":"sendnotification","operation":"is","value":"true:boolean"}]';
	//SalesPlatform.ru begin
        $eventsWorkflow->description = "Автоматические обработчики для событий при выбранной опции Отправить уведомление";
        //$eventsWorkflow->description = "Workflow for Events when Send Notification is True";
	//SalesPlatform.ru end
        $eventsWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
	$eventsWorkflow->defaultworkflow = 1;
	$workflowManager->save($eventsWorkflow);

	$task = $taskManager->createTask('VTEmailTask', $eventsWorkflow->id);
	$task->active = true;
	//SalesPlatform.ru begin
        $task->summary = 'Отправляет письмо с уведомлением приглашенным пользователям';
        //$task->summary = 'Send Notification Email to Record Owner';
        //SalesPlatform.ru end
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	//SalesPlatform.ru begin
        $task->subject = "Событие :  \$subject";
	$task->content = '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
					.'<b>Детали события:</b><br/>'
					.'Название события       : $subject<br/>'
					.'Дата и Время Начала    : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'Дата и Время Окончания : $due_date  $time_end ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'Статус                 : $eventstatus <br/>'
					.'Приоритет              : $taskpriority <br/>'
					.'Относится к            : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
					.'$(parent_id            : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
					.'Контакты               : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
					.'Место проведения       : $location <br/>'
					.'Описание               : $description';
        //vtiger commented code 
        /*
        $task->subject = "Event :  \$subject";
	$task->content = '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
					.'<b>Activity Notification Details:</b><br/>'
					.'Subject             : $subject<br/>'
					.'Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'End date and time   : $due_date  $time_end ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'Status              : $eventstatus <br/>'
					.'Priority            : $taskpriority <br/>'
					.'Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
											.'$(parent_id : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
					.'Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
					.'Location            : $location <br/>'
					.'Description         : $description';
         */
	//SalesPlatform.ru end
        $taskManager->saveTask($task);

	// Calendar workflow when Send Notification is checked
	$calendarWorkflow = $workflowManager->newWorkFlow("Calendar");
	$calendarWorkflow->test = '[{"fieldname":"sendnotification","operation":"is","value":"true:boolean"}]';
        //SalesPlatform.ru begin
        $calendarWorkflow->description = "Автоматические обработчики для задач Календаря при выбранной опции Отправить уведомление";
        //$calendarWorkflow->description = "Workflow for Calendar Todos when Send Notification is True";
	//SalesPlatform.ru end
        $calendarWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
	$calendarWorkflow->defaultworkflow = 1;
	$workflowManager->save($calendarWorkflow);

	$task = $taskManager->createTask('VTEmailTask', $calendarWorkflow->id);
	$task->active = true;
        //SalesPlatform.ru begin
        $task->summary = 'Отправляет письмо с уведомлением ответственному за задачу';
        //$task->summary = 'Send Notification Email to Record Owner';
        //SalesPlatform.ru end
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	//SalesPlatform.ru begin
        $task->subject = "Задача :  \$subject";
	$task->content = '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
					.'<b>Детали задачи:</b><br/>'
					.'Название задачи        : $subject<br/>'
					.'Дата и Время Начала    : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'Дата и Время Окончания : $due_date ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'Статус                 : $taskstatus <br/>'
					.'Приоритет              : $taskpriority <br/>'
					.'Относится к            : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
					.'$(parent_id : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
					.'Контакты               : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
					.'Место проведения       : $location <br/>'
					.'Описание               : $description';
        //vtiger commented code
        /*
        $task->subject = "Task :  \$subject";
	$task->content = '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
					.'<b>Task Notification Details:</b><br/>'
					.'Subject : $subject<br/>'
					.'Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'End date and time   : $due_date ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'Status              : $taskstatus <br/>'
					.'Priority            : $taskpriority <br/>'
					.'Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
					.'$(parent_id         : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
					.'Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
					.'Location            : $location <br/>'
					.'Description         : $description';
         */
	//SalesPlatform.ru end
        $taskManager->saveTask($task);
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
        // SalesPlatform.ru begin: Add detail view action icons
        $accountInstance->addLink('DETAILVIEWBASIC', 'LBL_SHOW_ACCOUNT_HIERARCHY', 'index.php?module=Accounts&action=AccountHierarchy&accountid=$RECORD$', 'themes/images/products.gif');
        //$accountInstance->addLink('DETAILVIEWBASIC', 'LBL_SHOW_ACCOUNT_HIERARCHY', 'index.php?module=Accounts&action=AccountHierarchy&accountid=$RECORD$');
        // SalesPlatform.ru end

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

        // SalesPlatform.ru begin: Add related records link to Documents
        $documentInstance = Vtiger_Module::getInstance('Documents');
	$documentInstance->addLink(
		'DETAILVIEWWIDGET', 'LBL_RELATED_TO',
		'module=Documents&action=DocumentsAjax&file=DetailViewAjax&recordid=$RECORD$&ajxaction=LOADRELATEDLISTWIDGET',
		''
	);
        // SalesPlatform.ru end

        // SalesPlatform.ru begin 5.4.0-201308
	$vendorsInstance = Vtiger_Module::getInstance('Vendors');
	$vendorsInstance->addLink('DETAILVIEWBASIC', 'LBL_ADD_NOTE', 'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$','themes/images/bookMark.gif');
	$vendorsInstance->addLink('DETAILVIEWBASIC', 'LBL_VENDORS_ADD_EVENTS', 'index.php?module=Calendar&action=EditView&return_module=Vendors&return_action=DetailView&activity_mode=Events&return_id=$RECORD$&parent_id=$RECORD$&parenttab=Sales','themes/images/AddEvent.gif');
	$vendorsInstance->addLink('DETAILVIEWBASIC', 'LBL_VENDORS_ADD_TASK', 'index.php?module=Calendar&action=EditView&return_module=Vendors&return_action=DetailView&activity_mode=Task&return_id=$RECORD$&parent_id=$RECORD$&parenttab=Sales','themes/images/AddToDo.gif');

	$helpdeskInstance = Vtiger_Module::getInstance('HelpDesk');
	$helpdeskInstance->addLink('DETAILVIEWWIDGET', 'LBL_RELATED_TO', 'module=HelpDesk&action=HelpDeskAjax&file=DetailViewAjax&recordid=$RECORD$&ajxaction=LOADRELATEDLISTWIDGET');
        // SalesPlatform.ru end

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
