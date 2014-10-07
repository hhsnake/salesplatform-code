<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
require_once 'modules/SPSocialConnector/SPSocialConnectorHelper.php';
require_once 'modules/SPSocialConnector/SPSocialConnector.php';

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
global $current_user;

$popuptype = vtlib_purify($_REQUEST["popuptype"]);

if( $popuptype == 'send_msg') {
    
    $url = vtlib_purify($_REQUEST["URL"]);
    $text = vtlib_purify($_REQUEST["text"]);
    $sourcemodule = vtlib_purify($_REQUEST['source_module']);
    $record_id = vtlib_purify(ZEND_JSON::decode($_REQUEST['record_id']));
    if(!empty($url)) {
        $url = trim($url, ',');
        $urllist = explode(',', $url);
        foreach ($urllist as $key => $value) {
            if ($value == '') {
                unset($urllist[$key]);
            }
        }
        $recordids = array();
        $urllist = array_values($urllist);
        for ($i = 0; $i < count($urllist); $i++) {
            $recordids[$i] = $record_id[0];
            $response[$i] = SPSocialConnectorHelper::parseURL($urllist[$i]);
            $res[$i] = SPSocialConnectorHelper::hybridauthSend($response[$i]->id,$text,$response[$i]->domen);
        } 

        SPSocialConnector::saveMsg($text, $urllist, $response, $res, $current_user->id, $recordids, $sourcemodule);	

    }
        
}

if( $popuptype == 'load_profile') {
    
    $url = vtlib_purify($_REQUEST["URL"]);
    $module = vtlib_purify($_REQUEST["sourcemodule"]);
    $recordid = vtlib_purify($_REQUEST["recordid"]);

    $response = SPSocialConnectorHelper::parseURL($url);
    $user_profile = SPSocialConnectorHelper::hybridauthUserProfile($response->id, $response->domen);
    
    if(!(empty($user_profile->birthDay)) && !(empty($user_profile->birthMonth)) && !(empty($user_profile->birthYear))){
        $date = $user_profile->birthDay.'-'.$user_profile->birthMonth.'-'.$user_profile->birthYear;
        $date = date('Y-m-d', strtotime($date));
    } else {
        $date = NULL;
    }
    
    $region = trim($user_profile->region, ',');
    $regionlist = explode(',', $region);
    
    $user_profile->birthDay = $date;
    $user_profile->city = $regionlist[0];
    $user_profile->country = $regionlist[1];
    
    $res = SPSocialConnectorHelper::addDataByModule( $module, $recordid, $user_profile);
    
}

echo "<script type='text/javascript'>";
echo "window.close();";
echo "</script>";
?>
