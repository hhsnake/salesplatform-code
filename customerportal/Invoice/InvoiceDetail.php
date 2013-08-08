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
echo '<!--Get Invoice Details Information -->';
$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];
$customeremail = $_SESSION['customer_name'];
if($id != '')
{

	//Get the Basic Information
	$block = "Invoice";
	$params = array('id' => "$id", 'block'=>"$block", 'contactid'=>"$customerid",'sessionid'=>"$sessionid");
	$result = $client->call('get_invoice_detail', $params, $Server_Path, $Server_Path);
	// Check for Authorization
	if (count($result) == 1 && $result[0] == "#NOT AUTHORIZED#") {
		echo '<tr>
			<td colspan="6" align="center"><b>'.getTranslatedString('LBL_NOT_AUTHORISED').'</b></td>
		</tr></table></td></tr></table></td></tr></table>';
		die();
	}
	$invinfo = $result[0][$block];
	echo '<table><tr><td><input class="crmbutton small cancel" type="button" value="'.getTranslatedString('LBL_BACK_BUTTON').'" onclick="window.history.back();"/></td></tr></table>';
	echo getblock_fieldlist($invinfo);
	echo '<tr><td colspan ="4"><table width="100%">';
        // SalesPlatform.ru begin
        if (count($online_payments) > 0) {
            $summ = trim(get_field_value($invinfo, getTranslatedString('Всего')));
            // Remove brackets/contents from a string
            $summ = trim(preg_replace('/\s*\([^)]*\)/', '', $summ));
            // Remove ","
            $summ = str_replace(",", "", $summ);
            // Round
            $inv_summ = htmlspecialchars(round($summ));
            $inv_addr = htmlspecialchars(trim(get_field_value($invinfo, getTranslatedString('Фактический адрес'))));
            $inv_date = htmlspecialchars(trim(get_field_value($invinfo, getTranslatedString('Дата'))));
            $inv_number = htmlspecialchars(trim(get_field_value($invinfo, getTranslatedString('Счет №'))));
            $inv_title = htmlspecialchars(trim(strip_tags(get_field_value($invinfo, getTranslatedString('Тема')))));
            $inv_desc = htmlspecialchars(trim(get_field_value($invinfo, getTranslatedString('Описание'))));
            $inv_id = htmlspecialchars(trim($id));
        
            $order_details = "Оплата счета №".$inv_number." от ".$inv_date.": ".$inv_title;
            if (!empty($inv_desc)) {
                $order_details .= " ".$inv_desc;
            }
?>
<!-- Place here payment form -->
<?php
        }
        // SalesPlatform.ru end
	echo '</table></td></tr>';	
	echo '</table></td></tr></table></td></tr></table>';
	echo '<!-- --End--  -->';

}
?>
