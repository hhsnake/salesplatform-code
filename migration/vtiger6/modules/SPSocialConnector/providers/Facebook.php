<?php
/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.                                                              
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
include_once(dirname(__DIR__).'/hybridauth/Hybrid/Providers/Facebook.php');

class Hybrid_Providers_Extended_Facebook extends Hybrid_Providers_Facebook {
    public $scope = "email, user_about_me, user_birthday, user_hometown, user_website, read_stream, 
                        offline_access, publish_stream, read_friendlists";

    // new function: view user profile by id
    function getUserProfileByID( $id ) {
        // request user profile from fb api
        try { 
            $data = $this->api->api("/$id"); 
        } catch( FacebookApiException $e ) {
            throw new Exception( "User profile request failed! {$this->providerId} returned an error: $e", 6 );
        } 
     
        // if the provider identifier is not recived, we assume the auth has failed
        if ( ! isset( $data["id"] ) ) { 
            throw new Exception( "User profile request failed! {$this->providerId} api returned an invalid response.", 6 );
        }

        // store the user profile.
        $this->user->profile->identifier    = (array_key_exists('id',$data))?$data['id']:"";
        $this->user->profile->displayName   = (array_key_exists('name',$data))?$data['name']:"";
        $this->user->profile->firstName     = (array_key_exists('first_name',$data))?$data['first_name']:"";
        $this->user->profile->lastName      = (array_key_exists('last_name',$data))?$data['last_name']:"";
        $this->user->profile->photoURL      = "https://graph.facebook.com/" . $this->user->profile->identifier . "/picture?width=150&height=200";
        $this->user->profile->profileURL    = (array_key_exists('link',$data))?$data['link']:""; 
        $this->user->profile->webSiteURL    = (array_key_exists('website',$data))?$data['website']:""; 
        $this->user->profile->gender        = (array_key_exists('gender',$data))?$data['gender']:"";
        $this->user->profile->description   = (array_key_exists('bio',$data))?$data['bio']:"";
        $this->user->profile->email         = (array_key_exists('email',$data))?$data['email']:"";
        $this->user->profile->emailVerified = (array_key_exists('email',$data))?$data['email']:"";
        $this->user->profile->region        = (array_key_exists("location",$data)&&array_key_exists("name",$data['location']))?$data['location']["name"]:"";

        if( array_key_exists('birthday',$data) ) {
            list($birthday_month, $birthday_day, $birthday_year) = explode( "/", $data['birthday'] );
            $this->user->profile->birthDay   = (int) $birthday_day;
            $this->user->profile->birthMonth = (int) $birthday_month;
            $this->user->profile->birthYear  = (int) $birthday_year;
        }

        return $this->user->profile;
    }

     // new function: send private message to user by URL
    function sendPrivateMessage( $id_and_text ) {
        list($id, $text) = explode( '?!?', $id_and_text );
        
?>
        <html>
            <body>
                <script>
                    var isLoaded = false;
                    window.fbAsyncInit = function() {
                        FB.init({
                            appId      : '',
                            status     : true,
                            xfbml      : true,
                            version    : 'v2.1'
                        });
                        
                        FB.ui({
                            method: 'send',
                            link: 'https://www.facebook.com/',
                            to: '<?php echo $id; ?>'
                        });
                      };                     
                      
                      (function(d, s, id){
                         var js, fjs = d.getElementsByTagName(s)[0];
                         if (d.getElementById(id)) {return;}
                         js = d.createElement(s); js.id = id;
                         js.src = "//connect.facebook.net/ru_RU/sdk.js";
                         fjs.parentNode.insertBefore(js, fjs);
                       }(document, 'script', 'facebook-jssdk'));
                </script>
            </body>
        </html>
<?php          
        return 1;
    }
}

