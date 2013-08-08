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

$onlymine = $_REQUEST['onlymine'];
if ($onlymine == 'true') {
    $mine_selected = 'selected';
    $all_selected = '';
} else {
    $mine_selected = '';
    $all_selected = 'selected';
}
$params = array();
echo '<tr><td><span class="lvtHeaderText">'.getTranslatedString("LBL_SALESORDER_INFORMATION").'</span</td>';    
        $allow_all = $client->call('show_all',array('module'=>'SalesOrder'),$Server_Path, $Server_Path);
        if($allow_all == 'true') {
                echo '<td align="right" style="padding-right:50px;"> <b>'.getTranslatedString('SHOW').'</b>&nbsp; <select name="list_type" onchange="getList(this, \'SalesOrder\');">
                        <option value="mine" '. $mine_selected .'>'.getTranslatedString('MINE').'</option>
                        <option value="all"'. $all_selected .'>'.getTranslatedString('ALL').'</option>
                        </select></td></tr>';
        }
?>
    <tr><td></td>
    <td align="right">
        <input type="hidden" name="onlymine" value="<?=$onlymine?>">
        <input class="crmbutton small cancel" name="newsalesorder" type="submit" value="<?php echo getTranslatedString('LBL_NEW_SALESORDER');?>" onclick="this.form.module.value='SalesOrder';this.form.action.value='index';this.form.fun.value='newsalesorder'">
        </form>
    </td>
    </tr>
    <tr><td colspan="2">
            <hr noshade="noshade" size="1" width="100%" align="left"><table width="95%"  border="0" cellspacing="0" cellpadding="5" align="center">
<?php
if ($customerid != '' ) {
	$block = "SalesOrder";
	$params = array('id' => "$customerid", 'block'=>"$block",'sessionid'=>$sessionid,'onlymine'=>$onlymine);
	$result = $client->call('get_list_values', $params, $Server_Path, $Server_Path);
	echo getblock_fieldlistview($result,$block);
}


?>
