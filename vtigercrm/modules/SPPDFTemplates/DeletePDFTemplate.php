<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

$idlist = $_REQUEST['idlist'];

$id_array=explode(';', $idlist);

for($i=0; $i < count($id_array)-1; $i++) {
	$sql = "delete from sp_templates where templateid=?";
	$adb->pquery($sql, array($id_array[$i]));
}

header("Location:index.php?module=SPPDFTemplates&action=ListPDFTemplates&parenttab=Tools");

?>
