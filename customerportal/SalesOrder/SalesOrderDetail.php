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

global $result;
global $client;
global $Server_Path;
echo '<!--Get SalesOrder Details Information -->';
$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];
if ($id != '') {

	//Get the Basic Information
	$block = "SalesOrder";
	$params = array('id' => "$id", 'block'=>"$block", 'contactid'=>"$customerid",'sessionid'=>"$sessionid");
	$result = $client->call('get_details', $params, $Server_Path, $Server_Path);
	// Check for Authorization
	if (count($result) == 1 && $result[0] == "#NOT AUTHORIZED#") {
		echo '<tr>
			<td colspan="6" align="center"><b>'.getTranslatedString('LBL_NOT_AUTHORISED').'</b></td>
		</tr></table></td></tr></table></td></tr></table>';
		die();
	}
	$invinfo = $result[0][$block];
        $so_status = get_field_value($invinfo, getTranslatedString('LBL_SALESORDER_STATUS'));
	echo '<table><tr><td><input class="crmbutton small cancel" type="button" value="'.getTranslatedString('LBL_BACK_BUTTON').'" onclick="window.history.back();"/></td></tr></table>';
	echo getblock_fieldlist($invinfo, getTranslatedString('LBL_PRODUCT_LIST_TITLE'));
        echo getblock_header('LNK_PRODUCTS');	
	echo '<tr><td colspan ="4"><table width="100%">';
	echo getblock_fieldlist_managed($invinfo, getTranslatedString('LBL_PRODUCT_LIST_TITLE'), 4, 'productid');
	echo '</table></td></tr>';	
	echo '<!-- --End--  -->';
?>
<input name="id" type="hidden" value="<?=$id?>">
<?php if ($so_status == getTranslatedString('LBL_SALESORDER_STATUS_CREATED')) { ?>
<input class="crmbutton small cancel" name="editsalesorder" type="submit" value="<?php echo getTranslatedString('LBL_EDIT_SALESORDER');?>" onclick="this.form.module.value='SalesOrder';this.form.action.value='index';this.form.fun.value='editsalesorder'">
<?php } ?>
<?php if (($so_status == getTranslatedString('LBL_SALESORDER_STATUS_APPROVED')) || ($so_status == getTranslatedString('LBL_SALESORDER_STATUS_CREATED'))) { ?>
<input class="crmbutton small cancel" name="rejectsalesorder" type="submit" value="<?php echo getTranslatedString('LBL_REJECT_SALESORDER');?>" onclick="this.form.module.value='SalesOrder';this.form.action.value='index';this.form.fun.value='rejectsalesorder'">
<?php } ?>
</form>
<?php
}
?>
