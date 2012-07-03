<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('data/CRMEntity.php');
require_once('data/SugarBean.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

// Account is used to store vtiger_account information.
class Consignment extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "vtiger_sp_consignment";
	var $table_index= 'consignmentid';
	var $tab_name = Array('vtiger_crmentity','vtiger_sp_consignment','vtiger_sp_consignmentbillads','vtiger_sp_consignmentshipads','vtiger_sp_consignmentcf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_sp_consignment'=>'consignmentid','vtiger_sp_consignmentbillads'=>'consignmentbilladdressid','vtiger_sp_consignmentshipads'=>'consignmentshipaddressid','vtiger_sp_consignmentcf'=>'consignmentid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_sp_consignmentcf', 'consignmentid');
	
	var $column_fields = Array();

	var $update_product_array = Array();	

	var $sortby_fields = Array('consignment_no','sp_consignmentstatus','smownerid','accountname','lastname');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
				'Consignment No'=>Array('sp_consignment'=>'consignment_no'),
				'Sales Order'=>Array('sp_consignment'=>'salesorderid'),
				'Status'=>Array('sp_consignment'=>'sp_consignmentstatus'),
				'Total'=>Array('sp_consignment'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);
	
	var $list_fields_name = Array(
				        'Consignment No'=>'consignment_no',
				        'Sales Order'=>'salesorder_id',
				        'Status'=>'sp_consignmentstatus',
				        'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'consignment_no';

	var $search_fields = Array(
				'Consignment No'=>Array('sp_consignment'=>'consignment_no'),
				'Status'=>Array('sp_consignment'=>'sp_consignmentstatus'),
				'Total'=>Array('sp_consignment'=>'total'),
				);
	
	var $search_fields_name = Array(
				        'Consignment No'=>'consignment_no',
				        'Status'=>'sp_consignmentstatus',
				        'Total'=>'hdnGrandTotal',
				      );

        // For Popup window record selection
	var $popup_fields = Array('consignment_no');

	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'ASC';

	var $mandatory_fields = Array('consignment_no','createdtime' ,'modifiedtime');
	var $_salesorderid;
	var $_recurring_mode;
	
	/**	Constructor which will set the column_fields in this object
	 */
	function Consignment() {
		$this->log =LoggerManager::getLogger('Consignment');
		$this->log->debug("Entering Consignment() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Consignment');
		$this->log->debug("Exiting Consignment method ...");
	}


	/** Function to handle the module specific save operations
	
	*/
	
	function save_module($module)
	{
		//in ajax save we should not call this function, because this will delete all the existing product values
		if(isset($_REQUEST)) {
			if($_REQUEST['action'] != 'ConsignmentAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW'
					&& $_REQUEST['action'] != 'MassEditSave' && $_REQUEST['action'] != 'ProcessDuplicates')
			{
				//Based on the total Number of rows we will save the product relationship with this entity
				saveInventoryProductDetails($this, 'Consignment');
			}
		}
		
		
		// Update the currency id and the conversion rate for the Consignment
		$update_query = "update vtiger_sp_consignment set currency_id=?, conversion_rate=? where consignmentid=?";
		
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id); 
		$this->db->pquery($update_query, $update_params);
	}


	/**	Function used to get the sort order for Consignment listview
	 *	@return string	$sorder	- first check the $_REQUEST['sorder'] if request value is empty then check in the $_SESSION['CONSIGNMENT_SORT_ORDER'] if this session value is empty then default sort order will be returned.
	 */
	function getSortOrder()
	{
		global $log;
                $log->debug("Entering getSortOrder() method ...");	
		if(isset($_REQUEST['sorder'])) 
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		else
			$sorder = (($_SESSION['CONSIGNMENT_SORT_ORDER'] != '')?($_SESSION['CONSIGNMENT_SORT_ORDER']):($this->default_sort_order));
		$log->debug("Exiting getSortOrder() method ...");
		return $sorder;
	}

	/**	Function used to get the order by value for Consignment listview
	 *	@return string	$order_by  - first check the $_REQUEST['order_by'] if request value is empty then check in the $_SESSION['CONSIGNMENT_ORDER_BY'] if this session value is empty then default order by will be returned.
	 */
	function getOrderBy()
	{
		global $log;
                $log->debug("Entering getOrderBy() method ...");
                
		$use_default_order_by = '';		
		if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
			$use_default_order_by = $this->default_order_by;
		}
		
		if (isset($_REQUEST['order_by'])) 
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		else
			$order_by = (($_SESSION['CONSIGNMENT_ORDER_BY'] != '')?($_SESSION['CONSIGNMENT_ORDER_BY']):($use_default_order_by));
		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}	


	/**	function used to get the name of the current object
	 *	@return string $this->name - name of the current object
	 */
	function get_summary_text()
	{
		global $log;
		$log->debug("Entering get_summary_text() method ...");
		$log->debug("Exiting get_summary_text method ...");
		return $this->name;
	}


	/**	function used to get the list of activities which are related to the Consignment
	 *	@param int $id - Consignment id
	 *	@return array - return an array which will be returned from the function GetRelatedList
	 */
	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_activities(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/Activity.php");
		$other = new Activity();
        vtlib_setup_modulevars($related_module, $other);		
		$singular_modname = vtlib_toSingular($related_module);
		
		$parenttab = getParentTab();
		
		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
		
		$button = '';
				
		$button .= '<input type="hidden" name="activity_mode">';
		
		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODOS', $related_module) ."'>&nbsp;";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('f'=>'vtiger_users.first_name', 'l' => 
			'vtiger_users.last_name'));
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid,
				vtiger_activity.*,vtiger_seactivityrel.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
				vtiger_crmentity.modifiedtime
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				where vtiger_seactivityrel.crmid=".$id." and activitytype='Task' and vtiger_crmentity.deleted=0
						and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Completed')
						and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Deferred')";
		
		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset); 
		
		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;
		
		$log->debug("Exiting get_activities method ...");		
		return $return_value;
	}

	/**	function used to get the the activity history related to the quote
	 *	@param int $id - Consignment id
	 *	@return array - return an array which will be returned from the function GetHistory
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('f'=>'vtiger_users.first_name', 'l' => 
			'vtiger_users.last_name'));
		$query = "SELECT vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, 
				vtiger_contactdetails.contactid,vtiger_activity.*,vtiger_seactivityrel.*,
				vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
				vtiger_crmentity.createdtime, vtiger_crmentity.description,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
                left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid	
				where vtiger_activity.activitytype='Task'
					and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred')
					and vtiger_seactivityrel.crmid=".$id."
					and vtiger_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Consignment',$query,$id);
	}



	/**	Function used to get the Status history of the Consignment
	 *	@param $id - Consignment id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_consignmentstatushistory($id)
	{	
		global $log;
		$log->debug("Entering get_consignmentstatushistory(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select vtiger_sp_consignmentstatushistory.*, vtiger_sp_consignment.consignment_no from vtiger_sp_consignmentstatushistory inner join vtiger_sp_consignment on vtiger_sp_consignment.consignmentid = vtiger_sp_consignmentstatushistory.consignmentid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_sp_consignment.consignmentid where vtiger_crmentity.deleted = 0 and vtiger_sp_consignment.consignmentid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['Consignment No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_CONSIGNMENT_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];
		
		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Amount are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$consignmentstatus_access = (getFieldVisibilityPermission('Consignment', $current_user->id, 'sp_consignmentstatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('Consignment');

		$consignmentstatus_array = ($consignmentstatus_access != 1)? $picklistarray['sp_consignmentstatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($consignmentstatus_access != 1)? 'Not Accessible': '-';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			// Module Sequence Numbering
			$entries[] = $row['consignment_no'];
			// END
			$entries[] = $row['accountname'];
			$entries[] = $row['total'];
			$entries[] = (in_array($row['sp_consignmentstatus'], $consignmentstatus_array))? $row['sp_consignmentstatus']: $error_msg;
			$entries[] = DateTimeField::convertToUserFormat($row['lastmodified']);

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_consignmentstatushistory method ...");

		return $return_data;
	}

	// Function to get column name - Overriding function of base class
	function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype) {
		if ($columname == 'salesorderid') {
			if ($fldvalue == '') return null;
		}
		return parent::get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
	}
	
	/*
	 * Function to get the secondary query part of a report 
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule){
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_sp_consignment","consignmentid");
		$query .= " left join vtiger_crmentity as vtiger_crmentityConsignment on vtiger_crmentityConsignment.crmid=vtiger_sp_consignment.consignmentid and vtiger_crmentityConsignment.deleted=0
			left join vtiger_sp_consignmentcf on vtiger_sp_consignment.consignmentid = vtiger_sp_consignmentcf.consignmentid
			left join vtiger_salesorder as vtiger_salesorderConsignment on vtiger_salesorderConsignment.salesorderid=vtiger_sp_consignment.salesorderid
			left join vtiger_sp_consignmentbillads on vtiger_sp_consignment.consignmentid=vtiger_sp_consignmentbillads.consignmentbilladdressid
			left join vtiger_sp_consignmentshipads on vtiger_sp_consignment.consignmentid=vtiger_sp_consignmentshipads.consignmentshipaddressid
			left join vtiger_inventoryproductrel as vtiger_inventoryproductrelConsignment on vtiger_sp_consignment.consignmentid = vtiger_inventoryproductrelConsignment.id
			left join vtiger_service as vtiger_serviceConsignment on vtiger_serviceConsignment.serviceid = vtiger_inventoryproductrelConsignment.productid
			left join vtiger_groups as vtiger_groupsConsignment on vtiger_groupsConsignment.groupid = vtiger_crmentityConsignment.smownerid
			left join vtiger_users as vtiger_usersConsignment on vtiger_usersConsignment.id = vtiger_crmentityConsignment.smownerid
			left join vtiger_contactdetails as vtiger_contactdetailsConsignment on vtiger_sp_consignment.contactid = vtiger_contactdetailsConsignment.contactid
			left join vtiger_account as vtiger_accountConsignment on vtiger_accountConsignment.accountid = vtiger_sp_consignment.accountid ";

		return $query;
	}

	/*
	 * Function to get the relation tables for related modules 
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" =>array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_sp_consignment"=>"consignmentid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_sp_consignment"=>"consignmentid"),
			"Accounts" => array("vtiger_sp_consignment"=>array("consignmentid","accountid")),
		);
		return $rel_tables[$secmodule];
	}
	
	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;
		
		if($return_module == 'Accounts' || $return_module == 'Contacts') {
			$this->trash('Consignment',$id);
		} elseif($return_module=='SalesOrder') {
			$relation_query = 'UPDATE vtiger_sp_consignment set salesorderid=0 where consignmentid=?';
			$this->db->pquery($relation_query, array($id));
		} else {
			$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
	}

        function getListQuery($module, $usewhere='') {
                global $current_user;

                $query = "SELECT vtiger_crmentity.*,
			vtiger_sp_consignment.*,
			vtiger_sp_consignmentbillads.*,
			vtiger_sp_consignmentshipads.*,
			vtiger_salesorder.subject AS salessubject,
			vtiger_account.accountname,
			vtiger_currency_info.currency_name
			FROM vtiger_sp_consignment
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_sp_consignment.consignmentid
			INNER JOIN vtiger_sp_consignmentbillads
				ON vtiger_sp_consignment.consignmentid = vtiger_sp_consignmentbillads.consignmentbilladdressid
			INNER JOIN vtiger_sp_consignmentshipads
				ON vtiger_sp_consignment.consignmentid = vtiger_sp_consignmentshipads.consignmentshipaddressid
			LEFT JOIN vtiger_currency_info
				ON vtiger_sp_consignment.currency_id = vtiger_currency_info.id
			LEFT OUTER JOIN vtiger_salesorder
				ON vtiger_salesorder.salesorderid = vtiger_sp_consignment.salesorderid
			LEFT OUTER JOIN vtiger_account
			        ON vtiger_account.accountid = vtiger_sp_consignment.accountid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_contactdetails.contactid = vtiger_sp_consignment.contactid
			INNER JOIN vtiger_sp_consignmentcf
				ON vtiger_sp_consignment.consignmentid = vtiger_sp_consignmentcf.consignmentid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module,$current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ".$where;

                return $query;
        }

 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		require_once('include/utils/utils.php');
		include_once('vtlib/Vtiger/Module.php');
		global $adb;

 		if($eventType == 'module.postinstall') {
			//Add Assets Module to Customer Portal

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));

			$consignmentInstance = Vtiger_Module::getInstance('Consignment');
			$invoiceInstance = Vtiger_Module::getInstance('Invoice');
			$invoiceInstance->setRelatedlist($consignmentInstance,'Consignment',array(ADD),'get_dependents_list');

                        $filename = "modules/Consignment/pdftemplates/factura.htm";
                        $handle = fopen($filename, "r");
                        $body = fread($handle, filesize($filename));
                        fclose($handle);

                        $templatename = 'Счет-фактура';
                        $header_size = 85;
                        $footer_size = 50;
                        $page_orientation = 'L';
                        $templateid = $adb->getUniqueID('sp_templates');
                        $sql = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,templateid) values (?,?,?,?,?,?,?)";
                        $params = array($templatename, $moduleName, $body, $header_size, $footer_size, $page_orientation, $templateid);
                        $adb->pquery($sql, $params);

                        $filename = "modules/Consignment/pdftemplates/torg12.htm";
                        $handle = fopen($filename, "r");
                        $body = fread($handle, filesize($filename));
                        fclose($handle);

                        $templatename = 'ТОРГ-12';
                        $header_size = 90;
                        $footer_size = 20;
                        $page_orientation = 'P';
                        $templateid = $adb->getUniqueID('sp_templates');
                        $sql = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,templateid) values (?,?,?,?,?,?,?)";
                        $params = array($templatename, $moduleName, $body, $header_size, $footer_size, $page_orientation, $templateid);
                        $adb->pquery($sql, $params);

		} else if($eventType == 'module.disabled') {
			$consignmentInstance = Vtiger_Module::getInstance('Consignment');
			$invoiceInstance = Vtiger_Module::getInstance('Invoice');
			$invoiceInstance->unsetRelatedlist($consignmentInstance,'Consignment','get_dependents_list');
		} else if($eventType == 'module.enabled') {
			$consignmentInstance = Vtiger_Module::getInstance('Consignment');
			$invoiceInstance = Vtiger_Module::getInstance('Invoice');
			$invoiceInstance->setRelatedlist($consignmentInstance,'Consignment',array(ADD),'get_dependents_list');
		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
 	}

}

?>