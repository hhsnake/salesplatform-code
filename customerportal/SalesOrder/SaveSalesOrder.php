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
global $result;

$title = $_REQUEST['title'];
$module = $_REQUEST['module'];
$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];
$all_ids_str = $_REQUEST['ids'];
$onlymine=$_REQUEST['onlymine'];

// Get checked products
$all_ids = explode('_', $all_ids_str);
$product_ids_str = "";
foreach ($all_ids as $ix => $current_id) {
        if ($current_id > 0 && !empty($_REQUEST['productid'.$current_id])) {
                $product_ids_str .= ($current_id."#".$_REQUEST['productid'.$current_id]."_");
        }
}

$params = Array(Array(
		'id'=>"$customerid",
		'sessionid'=>"$sessionid",
		'title'=>"$title",
		'module'=>"$module",
		'product_ids'=>"$product_ids_str",
		'salesorderid'=>"$id",
	));

$record_result = $client->call('create_salesorder', $params);
if (isset($record_result[0]['new_salesorder']) && $record_result[0]['new_salesorder']['salesorderid'] != '') {
	$new_record = 1;
	$salesorderid = $record_result[0]['new_salesorder']['salesorderid'];
}

if ($new_record == 1) {
	?>
	<script>
		var salesorderid = <?php echo $salesorderid; ?>;
		window.location.href = "index.php?module=SalesOrder&action=index&fun=detail&id="+salesorderid+"&onlymine=<?=$onlymine?>"
	</script>
	<?php
} else {
	echo getTranslatedString('LBL_PROBLEM_IN_SALESORDER_SAVING');
	include("NewSalesOrder.php");
}
?>
