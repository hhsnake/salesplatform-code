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


function getTypeComboList($selectedvalue='')
{
	$temp_array = array("onlymine", "onlybase");

	$status_combo = "<option value=''>".getTranslatedString('LBL_ALL')."</option>";
	foreach($temp_array as $index => $val)
	{
		$select = '';
		if($val == $selectedvalue)
			$select = ' selected';

		$status_combo .= '<option value="'.$val.'"'.$select.'>'.getTranslatedString($val).'</option>';
	}

	return $status_combo;
}


?>
