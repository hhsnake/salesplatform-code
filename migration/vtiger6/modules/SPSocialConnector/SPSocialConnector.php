<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
include_once 'include/Zend/Json.php';
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class SPSocialConnector extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_sp_socialconnector';
    var $table_index= 'socialserviceconnectorid';

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array('vtiger_sp_socialconnectorcf', 'socialserviceconnectorid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_crmentity', 'vtiger_sp_socialconnector', 'vtiger_sp_socialconnectorcf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
            'vtiger_crmentity' => 'crmid',
            'vtiger_sp_socialconnector' => 'socialserviceconnectorid',
            'vtiger_sp_socialconnectorcf'=>'socialserviceconnectorid');

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
            /* Format: Field Label => Array(tablename, columnname) */
            // tablename should not have prefix 'vtiger_'
            'message' => Array('sp_socialconnector', 'message'),
            'Assigned To' => Array('crmentity','smownerid')
    );
    var $list_fields_name = Array (
            /* Format: Field Label => fieldname */
            'message' => 'message',
            'Assigned To' => 'assigned_user_id'
    );

    // Make the field link to detail view 
    var $list_link_field = 'message';

    // For Popup listview and UI type support
    var $search_fields = Array(
            /* Format: Field Label => Array(tablename, columnname) */
            // tablename should not have prefix 'vtiger_'
            'message' => Array('sp_socialconnector', 'message')
    );
    var $search_fields_name = Array (
            /* Format: Field Label => fieldname */
            'message' => 'message'
    );

    // For Popup window record selection
    var $popup_fields = Array ('message');

    // Allow sorting on the following (field column names)
    var $sortby_fields = Array ('message');

    // Should contain field labels
    //var $detailview_links = Array ('Message');

    // For Alphabetical search
    var $def_basicsearch_col = 'message';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'message';

    // Required Information for enabling Import feature
    var $required_fields = Array ('assigned_user_id'=>1);

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'crmid';
    var $default_sort_order='DESC';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('createdtime', 'modifiedtime', 'message');
	
    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields($currentModule);
        $this->db = new PearDatabase();
        $this->log = $log;
    }

    function getSortOrder() {
        global $currentModule;

        $sortorder = $this->default_sort_order;
        if($_REQUEST['sorder']) $sortorder = $_REQUEST['sorder'];
        else if($_SESSION[$currentModule.'_Sort_Order']) 
            $sortorder = $_SESSION[$currentModule.'_Sort_Order'];

        return $sortorder;
    }

    function getOrderBy() {
        $orderby = $this->default_order_by;
        if($_REQUEST['order_by']) $orderby = $_REQUEST['order_by'];
        else if($_SESSION[$currentModule.'_Order_By'])
            $orderby = $_SESSION[$currentModule.'_Order_By'];
        return $orderby;
    }

    function save_module($module) {
    }

    /**
     * Return query to use based on given modulename, fieldname
     * Useful to handle specific case handling for Popup
     */
    function getQueryByModuleField($module, $fieldname, $srcrecord) {
        // $srcrecord could be empty
    }

    /**
     * Get list view query (send more WHERE clause condition if required)
     */
    function getListQuery($module, $usewhere=false) {
        $query = "SELECT vtiger_crmentity.*, $this->table_name.*";

        // Select Custom Field Table Columns if present
        if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

        $query .= " FROM $this->table_name";

        $query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

        // Consider custom table join as well.
        if(!empty($this->customFieldTable)) {
            $query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                " = $this->table_name.$this->table_index"; 
        }
        $query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
        $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
                        " INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
                        " WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
        $linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

        for($i=0; $i<$linkedFieldsCount; $i++) {
            $related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
            $fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
            $columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

            checkFileAccessForInclusion("modules/$related_module/$related_module.php");
            require_once("modules/$related_module/$related_module.php");
            $other = new $related_module();
            vtlib_setup_modulevars($related_module, $other);

            $query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
        }

        $query .= "	WHERE vtiger_crmentity.deleted = 0 ";
        if($usewhere) {
            $query .= $usewhere;
        }
        $query .= $this->getListViewSecurityParameter($module);
        return $query;
    }

    /**
     * Apply security restriction (sharing privilege) query part for List view.
     */
    function getListViewSecurityParameter($module) {
        global $current_user;
        require('user_privileges/user_privileges_'.$current_user->id.'.php');
        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

        $sec_query = '';
        $tabid = getTabid($module);

        if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 
            && $defaultOrgSharingPermission[$tabid] == 3) {

                $sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN 
                        (
                                SELECT vtiger_user2role.userid FROM vtiger_user2role 
                                INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid 
                                INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid 
                                WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
                        ) 
                        OR vtiger_crmentity.smownerid IN 
                        (
                                SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per 
                                WHERE userid=".$current_user->id." AND tabid=".$tabid."
                        ) 
                        OR 
                                (";

                        // Build the query based on the group association of current user.
                        if(sizeof($current_user_groups) > 0) {
                            $sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
                        }
                        $sec_query .= " vtiger_groups.groupid IN 
                                (
                                        SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid 
                                        FROM vtiger_tmp_read_group_sharing_per
                                        WHERE userid=".$current_user->id." and tabid=".$tabid."
                                )";
                $sec_query .= ")
                )";
        }
        return $sec_query;
    }

    /**
     * Create query to export the records.
     */
    function create_export_query($where) {
        global $current_user;
        $thismodule = $_REQUEST['module'];

        include("include/utils/ExportUtils.php");

        //To get the Permitted fields query and the permitted fields list
        $sql = getPermittedFieldsQuery($thismodule, "detail_view");

        $fields_list = getFieldsListFromQuery($sql);

        $query = "SELECT $fields_list, vtiger_users.user_name AS user_name 
                        FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

        if(!empty($this->customFieldTable)) {
            $query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                          " = $this->table_name.$this->table_index"; 
        }

        $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
        $query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";

        $linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
                        " INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
                        " WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
        $linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

        for($i=0; $i<$linkedFieldsCount; $i++) {
            $related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
            $fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
            $columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

            checkFileAccessForInclusion("modules/$related_module/$related_module.php");
            require_once("modules/$related_module/$related_module.php");
            $other = new $related_module();
            vtlib_setup_modulevars($related_module, $other);

            $query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
        }

        $where_auto = " vtiger_crmentity.deleted=0";

        if($where != '') $query .= " WHERE ($where) AND $where_auto";
        else $query .= " WHERE $where_auto";

        require('user_privileges/user_privileges_'.$current_user->id.'.php');
        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

        // Security Check for Field Access
        if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[7] == 3) {
            //Added security check to get the permitted records only
            $query = $query." ".getListViewSecurityParameter($thismodule);
        }
        return $query;
    }
	
    /**
     * Transform the value while exporting (if required)
     */
    function transform_export_value($key, $value) {
            return parent::transform_export_value($key, $value);
    }

    /** 
     * Function which will give the basic query to find duplicates
     */
    function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
        $select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

        // Select Custom Field Table Columns if present
        if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

        $from_clause = " FROM $this->table_name";

        $from_clause .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

        // Consider custom table join as well.
        if(isset($this->customFieldTable)) {
            $from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                          " = $this->table_name.$this->table_index"; 
        }
        $from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                                        LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $where_clause = "	WHERE vtiger_crmentity.deleted = 0";
        $where_clause .= $this->getListViewSecurityParameter($module);

        if (isset($select_cols) && trim($select_cols) != '') {
            $sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
                    " INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
            // Consider custom table join as well.
            if(isset($this->customFieldTable)) {
                $sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
            }
            $sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";	
        } else {
            $sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
        }	

        $query = $select_clause . $from_clause .
                " LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
                " INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
                $where_clause .
                " ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

        return $query;		
    }

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {

        //adds sharing accsess 
        $SPSocialConnectorModule  = Vtiger_Module::getInstance('SPSocialConnector'); 
        Vtiger_Access::setDefaultSharing($SPSocialConnectorModule);

        $registerLinks = false;
        $unregisterLinks = false;

        if($event_type == 'module.postinstall') {
            global $adb;
            $unregisterLinks = true;
            $registerLinks = true;

            // Mark the module as Additional module
            $adb->pquery('UPDATE vtiger_tab SET customized=1 WHERE name=?', array($modulename));

            // Insert into vtiger_sp_socialconnector_providers social nets
            $sql = "insert into vtiger_sp_socialconnector_providers (id,provider_name,provider_domen) values(?, ?, ?)";
            $params1 = array("1","Facebook","facebook.com");
            $params2 = array("2","Vkontakte","vk.com");
            $params3 = array("3","Twitter","twitter.com");
            $adb->pquery($sql, $params1);
            $adb->pquery($sql, $params2);
            $adb->pquery($sql, $params3);

            // Adding new fields to Contacts
            $contactInstance = Vtiger_Module::getInstance('Contacts');
            $blockInstance = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION', $contactInstance);

            $fieldInstance1 = new Vtiger_Field();
            $fieldInstance1->label = 'Facebook URL';
            $fieldInstance1->name = 'fb_url';
            $fieldInstance1->table = 'vtiger_contactscf';
            $fieldInstance1->column = 'fb_url';
            $fieldInstance1->columntype = 'varchar';
            $fieldInstance1->uitype = 17;
            $fieldInstance1->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance1);
            $fieldInstance1->setRelatedModules(Array('SPSocialConnector'));

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->label = 'Vkontakte URL';
            $fieldInstance->name = 'vk_url';
            $fieldInstance->table = 'vtiger_contactscf';
            $fieldInstance->column = 'vk_url';
            $fieldInstance->columntype = 'varchar';
            $fieldInstance->uitype = 17;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
            $fieldInstance->setRelatedModules(Array('SPSocialConnector'));

            $fieldInstance2 = new Vtiger_Field();
            $fieldInstance2->label = 'Twitter URL';
            $fieldInstance2->name = 'tw_url';
            $fieldInstance2->table = 'vtiger_contactscf';
            $fieldInstance2->column = 'tw_url';
            $fieldInstance2->columntype = 'varchar';
            $fieldInstance2->uitype = 17;
            $fieldInstance2->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance2);
            $fieldInstance2->setRelatedModules(Array('SPSocialConnector'));

            $sql_vk = "ALTER TABLE `vtiger_contactscf` ADD COLUMN `vk_url` VARCHAR(155) NULL ;";
            $sql_fb = "ALTER TABLE `vtiger_contactscf` ADD COLUMN `fb_url` VARCHAR(155) NULL ;";
            $sql_tw = "ALTER TABLE `vtiger_contactscf` ADD COLUMN `tw_url` VARCHAR(155) NULL ;";
            $params = array();
            $adb->pquery($sql_fb, $params);
            $adb->pquery($sql_vk, $params);
            $adb->pquery($sql_tw, $params); 

            // Adding new fields to Leads
            $leadInstance = Vtiger_Module::getInstance('Leads');
            $blockInstance = Vtiger_Block::getInstance('LBL_LEAD_INFORMATION', $leadInstance); 

            $fieldInstance1 = new Vtiger_Field();
            $fieldInstance1->label = 'Facebook URL';
            $fieldInstance1->name = 'fb_url';
            $fieldInstance1->table = 'vtiger_leadscf';
            $fieldInstance1->column = 'fb_url';
            $fieldInstance1->columntype = 'varchar';
            $fieldInstance1->uitype = 17;
            $fieldInstance1->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance1);
            $fieldInstance1->setRelatedModules(Array('SPSocialConnector'));

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->label = 'Vkontakte URL';
            $fieldInstance->name = 'vk_url';
            $fieldInstance->table = 'vtiger_leadscf';
            $fieldInstance->column = 'vk_url';
            $fieldInstance->columntype = 'varchar';
            $fieldInstance->uitype = 17;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
            $fieldInstance->setRelatedModules(Array('SPSocialConnector'));

            $fieldInstance2 = new Vtiger_Field();
            $fieldInstance2->label = 'Twitter URL';
            $fieldInstance2->name = 'tw_url';
            $fieldInstance2->table = 'vtiger_leadscf';
            $fieldInstance2->column = 'tw_url';
            $fieldInstance2->columntype = 'varchar';
            $fieldInstance2->uitype = 17;
            $fieldInstance2->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance2);
            $fieldInstance2->setRelatedModules(Array('SPSocialConnector'));

            $sql_vk = "ALTER TABLE `vtiger_leadscf` ADD COLUMN `vk_url` VARCHAR(155) NULL ;";
            $sql_fb = "ALTER TABLE `vtiger_leadscf` ADD COLUMN `fb_url` VARCHAR(155) NULL ;";
            $sql_tw = "ALTER TABLE `vtiger_leadscf` ADD COLUMN `tw_url` VARCHAR(155) NULL ;";
            $params = array();
            $adb->pquery($sql_fb, $params);
            $adb->pquery($sql_vk, $params);
            $adb->pquery($sql_tw, $params);

            // Adding new fields to Accounts
            $accountInstance = Vtiger_Module::getInstance('Accounts');
            $blockInstance = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $accountInstance);               

            $fieldInstance1 = new Vtiger_Field();
            $fieldInstance1->label = 'Facebook URL';
            $fieldInstance1->name = 'fb_url';
            $fieldInstance1->table = 'vtiger_accountscf';
            $fieldInstance1->column = 'fb_url';
            $fieldInstance1->columntype = 'varchar';
            $fieldInstance1->uitype = 17;
            $fieldInstance1->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance1);
            $fieldInstance1->setRelatedModules(Array('SPSocialConnector'));

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->label = 'Vkontakte URL';
            $fieldInstance->name = 'vk_url';
            $fieldInstance->table = 'vtiger_accountscf';
            $fieldInstance->column = 'vk_url';
            $fieldInstance->columntype = 'varchar';
            $fieldInstance->uitype = 17;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
            $fieldInstance->setRelatedModules(Array('SPSocialConnector'));

            $fieldInstance2 = new Vtiger_Field();
            $fieldInstance2->label = 'Twitter URL';
            $fieldInstance2->name = 'tw_url';
            $fieldInstance2->table = 'vtiger_accountscf';
            $fieldInstance2->column = 'tw_url';
            $fieldInstance2->columntype = 'varchar';
            $fieldInstance2->uitype = 17;
            $fieldInstance2->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance2);
            $fieldInstance2->setRelatedModules(Array('SPSocialConnector'));

            $sql_vk = "ALTER TABLE `vtiger_accountscf` ADD COLUMN `vk_url` VARCHAR(155) NULL ;";
            $sql_fb = "ALTER TABLE `vtiger_accountscf` ADD COLUMN `fb_url` VARCHAR(155) NULL ;";
            $sql_tw = "ALTER TABLE `vtiger_accountscf` ADD COLUMN `tw_url` VARCHAR(155) NULL ;";
            $params = array();
            $adb->pquery($sql_fb, $params);
            $adb->pquery($sql_vk, $params);
            $adb->pquery($sql_tw, $params);               

        } else if($event_type == 'module.disabled') {
            $unregisterLinks = true;

            // Disabled additional fields for Contacts
            $contactInstance = Vtiger_Module::getInstance('Contacts');

            $fieldInstance = Vtiger_Field::getInstance('fb_url', $contactInstance);
            $fieldInstance->setPresence(1);
            $fieldInstance->save();

            $fieldInstance1 = Vtiger_Field::getInstance('vk_url', $contactInstance);
            $fieldInstance1->setPresence(1);
            $fieldInstance1->save();

            $fieldInstance2 = Vtiger_Field::getInstance('tw_url', $contactInstance);
            $fieldInstance2->setPresence(1);
            $fieldInstance2->save();

            // Disabled additional fields for Leads
            $leadInstance = Vtiger_Module::getInstance('Leads');

            $fieldInstance = Vtiger_Field::getInstance('fb_url', $leadInstance);
            $fieldInstance->setPresence(1);
            $fieldInstance->save();

            $fieldInstance1 = Vtiger_Field::getInstance('vk_url', $leadInstance);
            $fieldInstance1->setPresence(1);
            $fieldInstance1->save();

            $fieldInstance2 = Vtiger_Field::getInstance('tw_url', $leadInstance);
            $fieldInstance2->setPresence(1);
            $fieldInstance2->save();

            // Disabled additional fields for Accounts
            $accountsInstance = Vtiger_Module::getInstance('Accounts');

            $fieldInstance = Vtiger_Field::getInstance('fb_url', $accountsInstance);
            $fieldInstance->setPresence(1);
            $fieldInstance->save();

            $fieldInstance1 = Vtiger_Field::getInstance('vk_url', $accountsInstance);
            $fieldInstance1->setPresence(1);
            $fieldInstance1->save();

            $fieldInstance2 = Vtiger_Field::getInstance('tw_url', $accountsInstance);
            $fieldInstance2->setPresence(1);
            $fieldInstance2->save();

        } else if($event_type == 'module.enabled') {
            $registerLinks = true;
            
            // Enabled additional fields for Contacts
            $contactInstance = Vtiger_Module::getInstance('Contacts');

            $fieldInstance = Vtiger_Field::getInstance('fb_url', $contactInstance);
            $fieldInstance->setPresence(2);
            $fieldInstance->save();

            $fieldInstance1 = Vtiger_Field::getInstance('vk_url', $contactInstance);
            $fieldInstance1->setPresence(2);
            $fieldInstance1->save();

            $fieldInstance2 = Vtiger_Field::getInstance('tw_url', $contactInstance);
            $fieldInstance2->setPresence(2);
            $fieldInstance2->save();

            // Enabled additional fields for Leads
            $leadInstance = Vtiger_Module::getInstance('Leads');

            $fieldInstance = Vtiger_Field::getInstance('fb_url', $leadInstance);
            $fieldInstance->setPresence(2);
            $fieldInstance->save();

            $fieldInstance1 = Vtiger_Field::getInstance('vk_url', $leadInstance);
            $fieldInstance1->setPresence(2);
            $fieldInstance1->save();

            $fieldInstance2 = Vtiger_Field::getInstance('tw_url', $leadInstance);
            $fieldInstance2->setPresence(2);
            $fieldInstance2->save();

            // Enabled additional fields for Accounts
            $accountsInstance = Vtiger_Module::getInstance('Accounts');

            $fieldInstance = Vtiger_Field::getInstance('fb_url', $accountsInstance);
            $fieldInstance->setPresence(2);
            $fieldInstance->save();

            $fieldInstance1 = Vtiger_Field::getInstance('vk_url', $accountsInstance);
            $fieldInstance1->setPresence(2);
            $fieldInstance1->save();

            $fieldInstance2 = Vtiger_Field::getInstance('tw_url', $accountsInstance);
            $fieldInstance2->setPresence(2);
            $fieldInstance2->save();

        } else if($event_type == 'module.preuninstall') {
                // TODO Handle actions when this module is about to be deleted.
        } else if($event_type == 'module.preupdate') {
                // TODO Handle actions before this module is updated.
        } else if($event_type == 'module.postupdate') {
                // TODO Handle actions after this module is updated.
        }

        if($unregisterLinks) {

            $socialserviceconnectorModuleInstance = Vtiger_Module::getInstance('SPSocialConnector');
            //$socialserviceconnectorModuleInstance->deleteLink('HEADERSCRIPT', 'SPSocialConnectorDetail');
            //$socialserviceconnectorModuleInstance->deleteLink('HEADERSCRIPT', 'SPSocialConnectorEdit');
                        
            $leadsModuleInstance = Vtiger_Module::getInstance('Leads');
            $leadsModuleInstance->deleteLink('DETAILVIEWBASIC', 'Send to social nets');

            $contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
            $contactsModuleInstance->deleteLink('DETAILVIEWBASIC', 'Send to social nets');

            $accountsModuleInstance = Vtiger_Module::getInstance('Accounts');
            $accountsModuleInstance->deleteLink('DETAILVIEWBASIC', 'Send to social nets');

            // SalesPlatform.ru begin Unset module to CRM entity related list
            $leadsModuleInstance->unsetRelatedlist($socialserviceconnectorModuleInstance,'SPSocialConnector','get_related_list');
            $contactsModuleInstance->unsetRelatedlist($socialserviceconnectorModuleInstance,'SPSocialConnector','get_related_list');
            $accountsModuleInstance->unsetRelatedlist($socialserviceconnectorModuleInstance,'SPSocialConnector','get_related_list');
            // SalesPlatform.ru end
        }

        if($registerLinks) {

            $socialserviceconnectorModuleInstance = Vtiger_Module::getInstance('SPSocialConnector');
            //$socialserviceconnectorModuleInstance->addLink("HEADERSCRIPT", "SPSocialConnectorDetail", "layouts/vlayout/modules/SPSocialConnector/resources/Detail.js");
            //$socialserviceconnectorModuleInstance->addLink("HEADERSCRIPT", "SPSocialConnectorEdit", "layouts/vlayout/modules/SPSocialConnector/resources/Edit.js");

            $leadsModuleInstance = Vtiger_Module::getInstance('Leads');
            $leadsModuleInstance->addLink("DETAILVIEWBASIC", "Send to social nets", "javascript:SPSocialConnector_Detail_Js.triggerSendMessage('index.php?module=\$MODULE\$&view=MassActionAjax&mode=showSendMessageForm');");

            $contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
            $contactsModuleInstance->addLink("DETAILVIEWBASIC", "Send to social nets", "javascript:SPSocialConnector_Detail_Js.triggerSendMessage('index.php?module=\$MODULE\$&view=MassActionAjax&mode=showSendMessageForm');");

            $accountsModuleInstance = Vtiger_Module::getInstance('Accounts');
            $accountsModuleInstance->addLink("DETAILVIEWBASIC", "Send to social nets", "javascript:SPSocialConnector_Detail_Js.triggerSendMessage('index.php?module=\$MODULE\$&view=MassActionAjax&mode=showSendMessageForm');");

            // SalesPlatform.ru begin Set module to CRM entity related list
            $leadsModuleInstance->setRelatedlist($socialserviceconnectorModuleInstance,'SPSocialConnector',array(),'get_related_list');
            $contactsModuleInstance->setRelatedlist($socialserviceconnectorModuleInstance,'SPSocialConnector',array(),'get_related_list');
            $accountsModuleInstance->setRelatedlist($socialserviceconnectorModuleInstance,'SPSocialConnector',array(),'get_related_list');
            // SalesPlatform.ru end
        }

    }

    function getListButtons($app_strings) {
        $list_buttons = Array();

        if(isPermitted('SPSocialConnector','Delete','') == 'yes') $list_buttons['del'] = $app_strings[LBL_MASS_DELETE];

        return $list_buttons;
    }

    // Save message info in database and linking with other modules (Leads, Contacts, Accounts)
    static function saveMsg($message, $urlfieldList, $provider, $status, $ownerid = false, $linktoids = false, $linktoModule = false) {
        global $current_user;

        if($ownerid === false) {
            if(isset($current_user) && !empty($current_user)) {
                    $ownerid = $current_user->id;
            } else {
                    $ownerid = 1;
            }
        }

        $moduleName = 'SPSocialConnector';
        $focus = CRMEntity::getInstance($moduleName);

        $focus->column_fields['message'] = $message;
        $focus->column_fields['assigned_user_id'] = $ownerid;
        
        for($i = 0; $i < count($urlfieldList); $i++){
            if($status[$i] == 1) {
                switch ($provider[$i]->domen) {
                    case 'facebook.com':
                        $focus->column_fields['fb_status'] = 'Sent';
                        break;
                    case 'twitter.com':
                        $focus->column_fields['tw_status'] = 'Sent';
                        break;
                    case 'vk.com':
                        $focus->column_fields['vk_status'] = 'Sent';
                        break;
                    default:
                        break;
                }
            } else {
                switch ($provider[$i]->domen) {
                    case 'facebook.com':
                        $focus->column_fields['fb_status'] = 'Not sent';
                        break;
                    case 'twitter.com':
                        $focus->column_fields['tw_status'] = 'Not sent';
                        break;
                    case 'vk.com':
                        $focus->column_fields['vk_status'] = 'Not sent';
                        break;
                    default:
                        break;
                }             
            }
        }
        $focus->save($moduleName);

        relateEntities($focus, $moduleName, $focus->id, $linktoModule, $linktoids);

    }
}

?>
