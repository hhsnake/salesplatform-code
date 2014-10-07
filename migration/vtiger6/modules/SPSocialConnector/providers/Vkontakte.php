<?php
/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.                                                              
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
include_once(dirname(__DIR__).'/hybridauth/Hybrid/Providers/Vkontakte.php');

class Hybrid_Providers_Extended_Vkontakte extends Hybrid_Providers_Vkontakte {
    // set permissions 
    public $scope = "offline";

    // new function: view user profile by id
    function getUserProfileByID( $id ) {
        // refresh tokens if needed 
        $this->refreshToken();
        
        // Vkontakte requires user id, not just token for api access
        $params['uids'] = "$id";
        $params['fields'] = 'nickname,sex,bdate,timezone,photo_big,city,country,contacts,personal';
        
        // ask vkontakte api for user info
        $response = $this->api->api( "https://api.vk.com/method/users.get" , 'GET', $params);
        
        if ( !isset( $response->response[0] ) || !isset( $response->response[0]->uid ) || isset( $response->error ) ) {
            throw new Exception( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
        }

        $response = $response->response[0];
        $this->user->profile->identifier    = (property_exists($response,'uid'))?$response->uid:"";
        $this->user->profile->firstName     = (property_exists($response,'first_name'))?$response->first_name:"";
        $this->user->profile->lastName      = (property_exists($response,'last_name'))?$response->last_name:"";
        $this->user->profile->displayName   = (property_exists($response,'nickname'))?$response->nickname:"";
        $this->user->profile->photoURL      = (property_exists($response,'photo_big'))?$response->photo_big:"";
        $this->user->profile->profileURL    = (property_exists($response,'screen_name'))?"http://vk.com/" . $response->screen_name:"";    
        $this->user->profile->mobilePhone   = (property_exists($response,'mobile_phone'))?$response->mobile_phone:"";
        $this->user->profile->homePhone     = (property_exists($response,'home_phone'))?$response->home_phone:"";

        if(property_exists($response,'sex')) {
            switch ($response->sex) {
                case 1:
                    $this->user->profile->gender = 'female'; 
                    break;
                case 2: 
                    $this->user->profile->gender = 'male'; 
                    break;
                default: 
                    $this->user->profile->gender = ''; 
                    break;
            }
        }

        if( property_exists($response,'bdate') ) { 
            if( substr_count($response->bdate, ".") == 2 ) {
                list($birthday_day, $birthday_month, $birthday_year) = explode( '.', $response->bdate );                         
                $this->user->profile->birthDay   = (int) $birthday_day;
                $this->user->profile->birthMonth = (int) $birthday_month;
                $this->user->profile->birthYear  = (int) $birthday_year;
            } else {
                list($birthday_day, $birthday_month) = explode( '.', $response->bdate );                         
                $this->user->profile->birthDay   = (int) $birthday_day;
                $this->user->profile->birthMonth = (int) $birthday_month;
            }
        }

        if( property_exists($response,'country') && $response->country != 0 ) {
            $country_id['cids'] = "$response->country";
            $country_name = $this->api->api( "https://api.vk.com/method/places.getCountryById" , 'GET', $country_id );
            $country_name = $country_name->response[0];
            $this->user->profile->country = (property_exists($country_name,'name'))?$country_name->name:"";
        }

        if( property_exists($response,'city') && $response->city != 0 ) {
            $city_id['cids'] = "$response->city";
            $city_name = $this->api->api( "https://api.vk.com/method/places.getCityById" , 'GET', $city_id );
            $city_name = $city_name->response[0];
            $this->user->profile->city = (property_exists($city_name,'name'))?$city_name->name:"";
        }
        
        if($this->user->profile->city || $this->user->profile->country) {
            $this->user->profile->region = $this->user->profile->city.", ".$this->user->profile->country;
        }

        return $this->user->profile;
    }

    // new function: send private message to user by URL
    function sendPrivateMessage( $id_and_text ) {
        /** 
         * This access_token is unlimited
         * If your password or account were changed you must generate new access_token
         * Go to:
         *
         *   https://oauth.vk.com/authorize? 
         *   client_id=YOUR_APP_ID& 
         *   scope=offline, messages& 
         *   redirect_uri=https://oauth.vk.com/blank.html& 
         *   display=wap& 
         *   response_type=token 
         *
         *  Then copy access token from url
         */
        $access_token = "";

        list($id, $text) = explode( '?', $id_and_text );

        // refresh tokens if needed 
        $this->refreshToken();

        // Get user ID
        $params['uids'] = "$id";
        $params['fields'] = 'sex,bdate';
        $response = $this->api->api( "https://api.vk.com/method/users.get" , 'GET', $params);
        $response = $response->response[0];
        $new_id = (property_exists($response,'uid'))?$response->uid:"";
        $parameters = array( 'uids' => "$new_id", 'message' => "$text", 'access_token' => "$access_token" ); 
        $response = $this->api->get( 'https://api.vk.com/method/messages.send', $parameters );

        if($response->error->error_code == 9) { 
            return 1;
        } else if ( isset( $response->response ) ){ 
            return 1;
        } else {
            return 0;
        }
    }
}


