<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * @author igor.struchkov@salesplatform.ru
 */

require_once 'include/utils/utils.php';

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.4.0-201208 to 5.4.0-201211 -------- Starts \n\n");

require_once 'include/utils/CommonUtils.php';

changeDBCollation('utf8_general_ci');

function addUserHomeWidgets($adb, $userid) {
        $s102 = $adb->getUniqueID("vtiger_homestuff");
        $sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
        ExecutePQuery($sql, array($s102, 102, 'Default', $userid, 0, 'LBL_SP_ACC'));
        $sql="insert into vtiger_homedefault values(".$s102.", 'SP_ACC', 5, 'Accounts')";
        ExecuteQuery($sql);

        $s103 = $adb->getUniqueID("vtiger_homestuff");
        $sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
        ExecutePQuery($sql, array($s103, 103, 'Default', $userid, 0, 'LBL_SP_POT'));
        $sql="insert into vtiger_homedefault values(".$s103.", 'SP_POT', 5, 'Potentials')";
        ExecuteQuery($sql);

        $s104 = $adb->getUniqueID("vtiger_homestuff");
        $sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
        ExecutePQuery($sql, array($s104, 104, 'Default', $userid, 0, 'LBL_SP_EVENTS'));
        $sql="insert into vtiger_homedefault values(".$s104.", 'SP_EVENTS', 5, 'Calendar')";
        ExecuteQuery($sql);

        $s105 = $adb->getUniqueID("vtiger_homestuff");
        $sql = "insert into vtiger_homestuff values(?,?,?,?,?,?)";
        ExecutePQuery($sql, array($s105, 105, 'Default', $userid, 0, 'LBL_SP_EXT_EVENTS'));
        $sql="insert into vtiger_homedefault values(".$s105.", 'SP_EXT_EVENTS', 5, 'Calendar')";
        ExecuteQuery($sql);
}

$res = $adb->query('select id from vtiger_users');
for($i = 0; $i < $adb->num_rows($res); $i++) {
    addUserHomeWidgets($adb, $adb->query_result($res, $i, 'id'));
}

$tabid = getTabid('SPPDFTemplates');
if($tabid) {
    ExecutePQuery('insert into vtiger_profile2tab select profileid, ?, 1 from vtiger_profile',
            array($tabid));
}

$adb->query("CREATE TABLE IF NOT EXISTS `vtiger_webforms_field` (
  `id` int(19) NOT NULL AUTO_INCREMENT,
  `webformid` int(19) NOT NULL,
  `fieldname` varchar(50) NOT NULL,
  `neutralizedfield` varchar(50) NOT NULL,
  `defaultvalue` varchar(200) DEFAULT NULL,
  `required` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `webforms_webforms_field_idx` (`id`),
  KEY `fk_1_vtiger_webforms_field` (`webformid`),
  KEY `fk_2_vtiger_webforms_field` (`fieldname`),
  CONSTRAINT `fk_1_vtiger_webforms_field` FOREIGN KEY (`webformid`) REFERENCES `vtiger_webforms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_2_vtiger_webforms_field` FOREIGN KEY (`fieldname`) REFERENCES `vtiger_field` (`fieldname`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

ExecuteQuery("CREATE TABLE `sp_smartfilter` (
  `filterid` int(11) NOT NULL,
  `name` varchar(255),
  `tabid` int(11) NOT NULL,
  PRIMARY KEY  (`filterid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

ExecuteQuery("CREATE TABLE `sp_smartfilterfield` (
  `filterid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  KEY  (`filterid`),
  KEY  (`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

ExecuteQuery("CREATE TABLE `sp_role2smartfilter` (
  `roleid` varchar(255),
  `filterid` int(11) NOT NULL,
  KEY  (`roleid`),
  KEY  (`filterid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$actModuleInstance = Vtiger_Module::getInstance('Act');
if($actModuleInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_RELATED_PRODUCTS';
    $actModuleInstance->addBlock($blockInstance);

    ExecutePQuery("update vtiger_field set typeofdata='V~M' where tabid=? and fieldname='assigned_user_id'", 
            array($actModuleInstance->id));
}

$consModuleInstance = Vtiger_Module::getInstance('Consignment');
if($consModuleInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_RELATED_PRODUCTS';
    $consModuleInstance->addBlock($blockInstance);

    ExecutePQuery("update vtiger_field set typeofdata='V~M' where tabid=? and fieldname='assigned_user_id'",
            array($consModuleInstance->id));
}

ExecuteQuery("alter table `vtiger_reportsharing` modify `shareid` varchar(200) not null");

ExecuteQuery("insert into vtiger_relatedlists values(" . $adb->getUniqueID('vtiger_relatedlists') . "," . getTabid("Potentials") . "," . getTabid("Invoice") . ",'get_related_list',9,'Invoice',0,'add,select')");

ExecutePQuery("insert into vtiger_profile2utility select profileid, ?, 8, 0 from vtiger_profile",
        array(getTabid('Potentials')));

$query=$adb->pquery("select * from vtiger_def_org_share where tabid=?",array(getTabid('Emails')));
$numOfRows=$adb->num_rows($query);
if($numOfRows == 0){
    ExecuteQuery("insert into vtiger_def_org_share values (".$adb->getUniqueID('vtiger_def_org_share').",".getTabid('Emails').",3,0)");
}

$query=$adb->pquery("select * from vtiger_links where linklabel = 'LBL_CHECK_STATUS'",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows > 0){
    ExecuteQuery("update vtiger_links set linkurl = 'javascript:SMSNotifier.checkstatus(\\'tbl".'$WRAPPER_NAME$'."\\', ".'$RECORD$'.")' where linklabel = 'LBL_CHECK_STATUS'");
}

ExecuteQuery("CREATE TABLE `sp_html_widget_contents` (
  `userid` int(19) NOT NULL,
  `widgetid` int(19) NOT NULL,
  `contents` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$adb->query('alter table `vtiger_sp_consignment` drop foreign key `fk_2_vtiger_sp_consignmentid`');
$adb->query('alter table `vtiger_sp_consignment` drop key `fk_2_vtiger_sp_consignment`');
$adb->query('alter table `vtiger_sp_act` drop foreign key `fk_2_vtiger_sp_act`');
$adb->query('alter table `vtiger_sp_act` drop key `fk_2_vtiger_sp_act`');

$migrationlog->debug("\n\nDB Changes from 5.4.0-201208 to 5.4.0-201211 -------- Ends \n\n");
?>