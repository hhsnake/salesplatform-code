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
		</td>
	    </form>
	</tr>
</table>

<?PHP
global $result;
$list = '';
$closedlist = '';

if($result == '') {
	$list .= '<tr><td>';
	$list .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';
	$list .= '<tr><td class="pageTitle">'.getTranslatedString('LBL_PRICEBOOKS_NONE').'</td></tr></table>';
	$list .= '</td></tr>';
} else {
	$header = $result[0]['head'][0];
	$nooffields = count($header);
	$data = $result[1]['data'];
	$rowcount = count($data);
        
        $list .= '<tr><td colspan="2"><div id="scrollTab">';
        $list .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
        $list .= '<tr>';

        for ($i = 0; $i < $nooffields; $i++) {
                $header_value = $header[$i]['fielddata'];
                $headerlist .= '<td class="detailedViewHeader" align="center">'.getTranslatedString($header_value).'</td>';
        }
        $headerlist .= '</tr>';

        $list .= $headerlist;

        for($i = 0; $i < count($data); $i++) {
                $pricebooklist = '';

                if ($i % 2 == 0)
                        $pricebooklist .= '<tr class="dvtLabel">';
                else
                        $pricebooklist .= '<tr class="dvtInfo">';

                for($j = 0; $j < $nooffields; $j++) {		
                        $pricebooklist .= '<td>'.getTranslatedString($data[$i][$j]['fielddata']).'</td>';
                }
                $pricebooklist .= '</tr>';

                $list .= $pricebooklist;
        }	

        $list .= '</table>';
}
echo $list;

?>
