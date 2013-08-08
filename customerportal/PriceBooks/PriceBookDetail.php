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
?>
			<input align="left" class="crmbutton small cancel"type="button" value="<?PHP echo getTranslatedString('LBL_BACK_BUTTON');?>" onclick="window.history.back();"/>	
			</td>
		</form>
		
<?PHP

global $result;
global $client;
global $Server_Path;
$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];
if ($pricebookid != '') {
	
	$params = array('id' => "$pricebookid", 'block' => "$block", 'contactid' => $customerid, 'sessionid' => "$sessionid");
	$result = $client->call('get_details', $params, $Server_Path, $Server_Path);	
	// Check for Authorization
	if (count($result) == 1 && $result[0] == "#NOT AUTHORIZED#") {
		echo '<tr>
			<td colspan="6" align="center"><b>'.getTranslatedString('LBL_NOT_AUTHORISED').'</b></td>
		</tr></table></td></tr></table></td></tr></table>';
		include("footer.html");
		die();
	}
	$pricebookinfo = $result[0][$block];
        
	echo getblock_fieldlist($pricebookinfo, getTranslatedString('LBL_PRODUCT_LIST_TITLE'));
        echo getblock_header('LNK_PRODUCTS');	
	echo '<tr><td colspan="4"><table>';
	echo getblock_fieldlist_managed($pricebookinfo, getTranslatedString('LBL_PRODUCT_LIST_TITLE'), 4);
	echo '</table></td></tr>';

	$list .= '
		</table>
	 </td>
   </tr>
</table>
</td></tr>';
	
	
	echo $list;
	echo '</table></td></tr>';	
	echo '</table></td></tr></table></td></tr></table>';
	echo '<!-- --End--  -->';

}
else
	echo getTranslatedString('LBL_NONE_SUBMITTED');
?>