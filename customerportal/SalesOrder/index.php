<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once("include/utils/utils.php");

@include("../PortalConfig.php");
if (!isset($_SESSION['customer_id']) || $_SESSION['customer_id'] == '') {
	@header("Location: $Authenticate_Path/login.php");
	exit;
}

include("index.html");
global $result;
$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];
$id = $_REQUEST['id'];

if ($_REQUEST['fun'] == 'newsalesorder') {
	include("NewSalesOrder.php");
        
} else if($_REQUEST['fun'] == 'editsalesorder') {
	include("NewSalesOrder.php");
        
} else if($_REQUEST['fun'] == 'rejectsalesorder') {
        if ($id != '') {
                $module = "SalesOrder";
                $params = array(array('id'=>$customerid, 'sessionid'=>$sessionid,
                    'module'=>$module, 'entityid'=>$id, 
                    'fieldname'=>'sostatus', 'fieldvalue'=>'Rejected'));
                $result = $client->call('change_entity_field', $params, $Server_Path, $Server_Path);
                if (!$result[0]) {
                        echo getTranslatedString('LBL_CANNOT_CHANGE_SALESORDER_STATE');
                } else {
                        include("SalesOrderList.php");
                }
        }
} else if($_REQUEST['fun'] == 'savesalesorder') {
	include("SaveSalesOrder.php");
        
} else if ($id != '') {
        include("SalesOrderDetail.php");
        
} else {
        include("SalesOrderList.php");
        
}
	
?>
</table> </td></tr></table></td></tr></table>


	