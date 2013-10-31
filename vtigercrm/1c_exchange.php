<?
/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

    require_once 'modules/SPCMLConnector/TranzactionController.php';
    require_once 'include/utils/VtlibUtils.php';
    $auth = new WebserviceExchange();               //need to auth
    if(vtlib_isModuleActive("SPCMLConnector")) {
        if($auth->isLogin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
            $tranzaction = new TranzactionController($_REQUEST);
            $tranzaction->runTranzaction();
        } else {
            echo "Auth required!";
        }
        
    }
?>
