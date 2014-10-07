<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
header('Content-Type: text/html; charset= utf-8');
require_once( "hybridauth/Hybrid/Auth.php" );
include_once dirname(__FILE__) . '/SPSocialConnector.php';

class SPSocialConnectorHelper {
    
    static function parseURL( $URL ) {

        if(strpos($URL, "http://")===FALSE && strpos($URL, "https://")===FALSE) {
                $URL = "http://".$URL;
        }

        $parseURL = parse_url($URL);

        if(strpos($parseURL['host'], "www.")!==FALSE) {
            $domen = substr($parseURL['host'], 4);
        } else {
            $domen = $parseURL['host'];
        }

        if(empty($parseURL['query'])) {
            if(strpos($parseURL['path'], "/id")!==FALSE) {
                $id = substr($parseURL['path'], 3);
            } else {
                $id = substr($parseURL['path'], 1);
            }
        } else {
            $id = substr($parseURL['query'], 3);
        }

        $response->domen = $domen;
        $response->id = $id;

        return $response;
    }
        
    static function hybridauthSend( $hybridID, $hybridText, $hybridDomen ) {        

        global $adb;
        
        $qresult = $adb->pquery("SELECT * FROM vtiger_sp_socialconnector_providers where provider_domen like '%$hybridDomen%'", array());
        while($resultrow = $adb->fetch_array($qresult)) {
            $social_network = $resultrow['provider_name'];
        }

        // start a new session (required for Hybridauth)
        session_start(); 

        // change the following paths if necessary 
        $config = dirname(__FILE__) . '/hybridauth/config.php';


        try {
            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new Hybrid_Auth( $config );

            // try to authenticate the user, user will be redirected for authentication, 
            // if he already did, then Hybridauth will ignore this step and return an instance of the adapter
            $req = $hybridauth->authenticate( "$social_network" );  

            // send private message to user  
            $id_and_text =  $hybridID."?".$hybridText;
            $send_msg = $req->sendPrivateMessage( $id_and_text );


            if($send_msg==1){
                $response = 1;
            }
            else{                        
                $response = 0;
            }

            $req->logout(); 

        } catch( Exception $e ) {  
            // Display the recived error, 
            // to know more please refer to Exceptions handling section on the userguide
            switch( $e->getCode() ) { 
                case 0 : 
                    return $error_msg = "Unspecified error."; 
                    break;
                case 1 : 
                    return $error_msg = "Hybriauth configuration error."; 
                    break;
                case 2 : 
                    return $error_msg = "Provider not properly configured."; 
                    break;
                case 3 : 
                    return $error_msg = "Unknown or disabled provider."; 
                    break;
                case 4 : 
                    return $error_msg = "Missing provider application credentials."; 
                    break;
                case 5 : 
                    return $error_msg = "Authentification failed. " 
                          . "The user has canceled the authentication or the provider refused the connection."; 
                    break;
                case 6 : 
                    return $error_msg = "User profile request failed. Most likely the user is not connected "
                          . "to the provider and he should authenticate again."; 
                    $req->logout(); 
                    break;
                case 7 : 
                    return $error_msg = "User not connected to the provider."; 
                    $req->logout(); 
                    break;
                case 8 : 
                    return $error_msg = "Provider does not support this feature."; 
                    break;
            } 

        }    
     
        return $response;
    }
        
    static function hybridauthUserProfile( $hybridID, $hybridDomen ) {

        global $adb;
        
        $qresult = $adb->pquery("SELECT * FROM vtiger_sp_socialconnector_providers where provider_domen like '%$hybridDomen%'", array());
        while($resultrow = $adb->fetch_array($qresult)) {
            $social_network = $resultrow['provider_name'];
        }

        // start a new session (required for Hybridauth)
        session_start(); 

        // change the following paths if necessary 
        $config   = dirname(__FILE__) . '/hybridauth/config.php';

        try {
            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new Hybrid_Auth( $config );

            // try to authenticate the user, user will be redirected for authentication, 
            // if he already did, then Hybridauth will ignore this step and return an instance of the adapter
            $req = $hybridauth->authenticate( "$social_network" );  

            // get the user profile 
            $user_profile = $req->getUserProfileByID( $hybridID );
            $user_profile->provider = $social_network;

            $req->logout(); 
        } catch( Exception $e ) {  
            // Display the recived error, 
            // to know more please refer to Exceptions handling section on the userguide
            switch( $e->getCode() ) { 
                case 0 : 
                    return $error_msg = "Unspecified error."; 
                    break;
                case 1 : 
                    return $error_msg = "Hybriauth configuration error."; 
                    break;
                case 2 : 
                    return $error_msg = "Provider not properly configured."; 
                    break;
                case 3 : 
                    return $error_msg = "Unknown or disabled provider."; 
                    break;
                case 4 : 
                    return $error_msg = "Missing provider application credentials."; 
                    break;
                case 5 : 
                    return $error_msg = "Authentification failed. " 
                          . "The user has canceled the authentication or the provider refused the connection."; 
                    break;
                case 6 : 
                    return $error_msg = "User profile request failed. Most likely the user is not connected "
                          . "to the provider and he should authenticate again."; 
                    $req->logout(); 
                    break;
                case 7 : 
                    return $error_msg = "User not connected to the provider."; 
                    $req->logout(); 
                    break;
                case 8 : 
                    return $error_msg = "Provider does not support this feature."; 
                    break;
            } 

        }    

        return $user_profile;
    }
        
    static function addDataByModule($module, $recordid, $profileFromSocialNet) {
        
        global $adb;

        $i = 0;     // Iterator of changed fields

        if($module == 'Contacts') {
            
            $query = "SELECT t1.mailingcity, t1.mailingcountry, t2.firstname, t2.lastname, t2.email, t2.mobile, t3.homephone, t3.birthday 
                FROM vtiger_contactaddress t1, vtiger_contactdetails t2, vtiger_contactsubdetails t3
                where t1.contactaddressid = '$recordid' and t2.contactid = '$recordid' and t3.contactsubscriptionid = '$recordid'" ;

            $res = $adb->pquery($query, array());

            while($row = $adb->fetch_array($res)) {
                $profileFromDB->firstname = $row['firstname'];
                $profileFromDB->lastname = $row['lastname'];
                $profileFromDB->birthday = $row['birthday'];
                $profileFromDB->email = $row['email'];
                $profileFromDB->mobile = $row['mobile'];
                $profileFromDB->homephone = $row['homephone'];
                $profileFromDB->mailingcity = $row['mailingcity'];
                $profileFromDB->mailingcountry = $row['mailingcountry'];
            }
            
            $profile->firstname = $profileFromSocialNet->firstName;
            $profile->lastname = $profileFromSocialNet->lastName;
            $profile->birthday = $profileFromSocialNet->birthDay;
            $profile->email = $profileFromSocialNet->email;
            $profile->mobile = $profileFromSocialNet->mobilePhone;
            $profile->homephone = $profileFromSocialNet->homePhone;
            $profile->mailingcity = $profileFromSocialNet->city;
            $profile->mailingcountry = $profileFromSocialNet->country;

            // Checking which fields not in the table and which fields have the profile at social net
            // $index - field name, $value - value of the field in social net
            foreach($profile as $index => $value) {
                if( !(empty($value)) && empty($profileFromDB->$index) ){

                    // Check whether there is a column $index in table, if there is then UPDATE
                    $result = $adb->pquery("SHOW COLUMNS FROM vtiger_contactaddress LIKE '$index'", array());
                    $exists = ($adb->num_rows($result))?TRUE:FALSE;
                    if($exists) {
                        $query = "UPDATE vtiger_contactaddress SET $index = '$value' WHERE contactaddressid = '$recordid'";
                        $res = $adb->pquery($query, array());
                    }

                    $result1 = $adb->pquery("SHOW COLUMNS FROM vtiger_contactdetails LIKE '$index'", array());
                    $exists1 = ($adb->num_rows($result1))?TRUE:FALSE;
                    if($exists1) {
                        $query = "UPDATE vtiger_contactdetails SET $index = '$value' WHERE contactid = '$recordid'";
                        $res = $adb->pquery($query, array());
                    }

                    $result2 = $adb->pquery("SHOW COLUMNS FROM vtiger_contactsubdetails LIKE '$index'", array());
                    $exists2 = ($adb->num_rows($result2))?TRUE:FALSE;
                    if($exists2) {
                        $query = "UPDATE vtiger_contactsubdetails SET $index = '$value' WHERE contactsubscriptionid = '$recordid'";
                        $res = $adb->pquery($query, array());
                    }

                    $response->response[$i]->index = $index;
                    $response->response[$i]->value = $value;
                    $i++;
                }
            }
        }

        if($module == 'Leads') {
            
            $query = "SELECT t1.city, t1.country, t2.firstname, t2.lastname, t2.email, t1.mobile, t1.phone, t3.website 
                FROM vtiger_leadaddress t1, vtiger_leaddetails t2, vtiger_leadsubdetails t3 
                WHERE t1.leadaddressid = '$recordid' and t2.leadid = '$recordid' and t3.leadsubscriptionid = '$recordid'";

            $res = $adb->pquery($query, array());

            while($row = $adb->fetch_array($res)) {
                $profileFromDB->firstname = $row['firstname'];
                $profileFromDB->lastname = $row['lastname'];
                $profileFromDB->email = $row['email'];
                $profileFromDB->mobile = $row['mobile'];
                $profileFromDB->homephone = $row['phone'];
                $profileFromDB->city = $row['city'];
                $profileFromDB->country = $row['country'];
                $profileFromDB->website = $row['website'];
            }

            $profile->firstname = $profileFromSocialNet->firstName;
            $profile->lastname = $profileFromSocialNet->lastName;
            $profile->email = $profileFromSocialNet->email;
            $profile->mobile = $profileFromSocialNet->mobilePhone;
            $profile->phone = $profileFromSocialNet->homePhone;
            $profile->city = $profileFromSocialNet->city;
            $profile->country = $profileFromSocialNet->country;
            $profile->website = $profileFromSocialNet->webSite;

            // Checking which fields not in the table and which fields have the profile at social net
            // $index - field name, $value - value of the field in social net
            foreach($profile as $index => $value) {
                if( !(empty($value)) && empty($profileFromDB->$index) ) {

                    // Check whether there is a column $index in table, if there is then UPDATE
                    $result = $adb->pquery("SHOW COLUMNS FROM vtiger_leadaddress LIKE '$index'", array());
                    $exists = ($adb->num_rows($result))?TRUE:FALSE;
                    if($exists) {
                        $query = "UPDATE vtiger_leadaddress SET $index = '$value' WHERE leadaddressid = '$recordid'";
                        $res = $adb->pquery($query, array());
                    }

                    $result1 = $adb->pquery("SHOW COLUMNS FROM vtiger_leaddetails LIKE '$index'", array());
                    $exists1 = ($adb->num_rows($result1))?TRUE:FALSE;
                    if($exists1) {
                        $query = "UPDATE vtiger_leaddetails SET $index = '$value' WHERE leadid = '$recordid'";
                        $res = $adb->pquery($query, array());
                    }

                    $result2 = $adb->pquery("SHOW COLUMNS FROM vtiger_leaddetails LIKE '$index'", array());
                    $exists2 = ($adb->num_rows($result2))?TRUE:FALSE;
                    if($exists2) {
                        $query = "UPDATE vtiger_leadsubdetails SET $index = '$value' WHERE leadsubscriptionid = '$recordid'";
                        $res = $adb->pquery($query, array());
                    }

                    $response->response[$i]->index = $index;
                    $response->response[$i]->value = $value;
                    $i++;
                }
            }
        }

        if($module == 'Accounts') {
            
            $query = "SELECT t1.ship_city, t1.ship_country, t2.email1, t2.otherphone, t2.phone, t2.website 
                FROM vtiger_accountshipads t1, vtiger_account t2 
                WHERE t1.accountaddressid = '$recordid' and t2.accountid = '$recordid'";

            $res = $adb->pquery($query, array());

            while($row = $adb->fetch_array($res)) {
                $profileFromDB->email1 = $row['email1'];
                $profileFromDB->phone = $row['phone'];
                $profileFromDB->otherphone = $row['otherphone'];
                $profileFromDB->ship_city = $row['ship_city'];
                $profileFromDB->ship_country = $row['ship_country'];
                $profileFromDB->website = $row['website'];
            }

            $profile->email1 = $profileFromSocialNet->email;
            $profile->phone = $profileFromSocialNet->mobilePhone;
            $profile->otherphone = $profileFromSocialNet->homePhone;
            $profile->ship_city = $profileFromSocialNet->city;
            $profile->ship_country = $profileFromSocialNet->country;
            $profile->website = $profileFromSocialNet->webSite;

            // Checking which fields not in the table and which fields have the profile at social net
            // $index - field name, $value - value of the field in social net
            foreach($profile as $index => $value) {
                if( !(empty($value)) && empty($profileFromDB->$index) ) {

                    // Check whether there is a column $index in table, if there is then UPDATE
                    $result =  $adb->pquery("SHOW COLUMNS FROM vtiger_accountshipads LIKE '$index'", array());
                    $exists = ($adb->num_rows($result))?TRUE:FALSE;
                    if($exists) {
                        $query = "UPDATE vtiger_accountshipads SET $index = '$value' WHERE accountaddressid = '$recordid'";
                        $res = $adb->pquery($query, array());
                    }

                    $result1 =  $adb->pquery("SHOW COLUMNS FROM vtiger_account LIKE '$index'", array());
                    $exists1 = ($adb->num_rows($result1))?TRUE:FALSE;
                    if($exists1) {
                        $query = "UPDATE vtiger_account SET $index = '$value' WHERE accountid = '$recordid'";
                        $res = $adb->pquery($query, array());
                    }                                     

                    $response->response[$i]->index = $index;
                    $response->response[$i]->value = $value;
                    $i++;
                }
            }
        }
        
        return $response;
    }
                           
}

?>