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

global $client;

$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];
$onlymine=$_REQUEST['onlymine'];
$invinfo = array();
if ($id > 0) {
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
}
$title = get_field_value($invinfo, getTranslatedString('SALESORDER_TITLE'));
$title = empty($title) ? "Заказ ".date("ymdHis") : $title;
?>
<tr><td colspan="2">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
   <tr>
	<td align="left">
	   <span class="lvtHeaderText">&nbsp;&nbsp;<?php echo getTranslatedString('LBL_NEW_SALESORDER');?></span>
	   <hr noshade="noshade" size="1" width="90%" align="left"><br><br>
		<table width="80%"  border="0" cellspacing="0" cellpadding="5" align="center">
		   <form name="Save" method="post" action="index.php">
		   <input type="hidden" name="module" value="SalesOrder">
		   <input type="hidden" name="action" value="index">
		   <input type="hidden" name="fun" value="savesalesorder">
		   <input type="hidden" name="id" value="<?=$id?>">
		   <input type="hidden" name="onlymine" value="<?=$onlymine?>">
		   <tr>
			<td colspan="6" class="detailedViewHeader"><b><?PHP echo getTranslatedString('LBL_NEW_SALESORDER');?></b></td></tr>  
		   <tr>
			<td class="dvtCellLabel" align="right"><font color="red">*</font><?PHP echo getTranslatedString('SALESORDER_TITLE');?></td>
			<td colspan="5" class="dvtCellInfo">
				<input type="text" name="title" value="<?=$title?>" class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
			</td>
		   </tr>
		   <tr>
			<td colspan="6">&nbsp;</td>
		   </tr>
<?php
	$block = "Products";
	$params = array('id' => "$customerid", 'block'=>"$block",'sessionid'=>$sessionid,'onlymine'=>$onlymine);
	$result = $client->call('get_list_values', $params, $Server_Path, $Server_Path);
	echo getblock_fieldlistedit($result,$block,"productid",$invinfo,getTranslatedString('Quantity'));
?>
                   <tr>
			<td colspan="6">
			   <div align="center">
				<input class="crmbutton small cancel" title="<?PHP echo getTranslatedString('LBL_SAVE_ALT');?>" accesskey="S" class="small"  name="button" value="<?PHP echo getTranslatedString('LBL_SAVE');?>" style="width: 70px;" type="submit" onclick="return formvalidate(this.form)">
				<input class="crmbutton small cancel" title="<?PHP echo getTranslatedString('LBL_CANCEL_ALT');?>" accesskey="X" class="small" name="button" value="<?PHP echo getTranslatedString('LBL_CANCEL');?>" style="width: 70px;" type="button" onclick="window.history.back()";>
			   </div>
			</td>
		   </tr>
                   <tr><td colspan="6">&nbsp;</td></tr>
		   </form>
		</table>
	 </td>
   </tr>
</table>
<script>
function formvalidate(form)
{
	if(trim(form.title.value) == '')
	{
		alert("SalesOrder Title is empty");
		return false;
	}
	return true;
}
function trim(s) 
{
	while (s.substring(0,1) == " ")
	{
		s = s.substring(1, s.length);
	}
	while (s.substring(s.length-1, s.length) == ' ')
	{
		s = s.substring(0,s.length-1);
	}

	return s;
}
</script>
<?php

?>
