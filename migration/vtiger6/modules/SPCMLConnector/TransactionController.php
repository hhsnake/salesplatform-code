<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'modules/SPCMLConnector/SalesOrderController.php';
require_once 'modules/SPCMLConnector/CatalogController.php';
require_once 'modules/SPCMLConnector/TranzactionHistory.php';
require_once 'modules/SPCMLConnector/ImportFileManager.php';
require_once 'modules/SPCMLConnector/CmlParser.php';

/**
 * Controls tranzactions of products nad orders exchange.
 * @author alexd
 */
class TransactionController {
    /**
     *
     * @var TranzactionHistory 
     */
    private $transactionHistory;
    /**
     *
     * @var ImportFileManager 
     */
    private $importFileManager;
    private $userName;
    private $userPassword;
      
    /**
     * Initilizate history controller.
     */
    public function __construct($userName, $userPassword) {
        $this->transactionHistory = new TranzactionHistory();
        $this->importFileManager = new ImportFileManager();
        $this->userName = $userName;
        $this->userPassword = $userPassword;
    }
    
    /**
     * Check user name and it acces key. If success - return true.
     * @return boolean
     */
    private function checkLogin() {
        global $adb;     
        $queryResult = $adb->pquery("select id from vtiger_users where user_name=? and accesskey=?;",
                array($this->userName, $this->userPassword));
        $resultRow = $adb->fetchByAssoc($queryResult);
        
        if($resultRow == null) {
            return false;
        }
        return true;
    }
    
    /**
     * Execute transaction step and return execution status.
     * @param Array $request
     * @return String
     */
    private function executeStep($request) {
        $stepType = vtlib_purify($request['type']);
        
        /* Check type and call method */
        if($stepType == 'catalog') {
            return $this->catalogExchange($request);
        } elseif($stepType == 'sale') {
            return  $this->salesOrderExchange($request);
        } 

        return  'Failure. Unknow transaction type!';
    }
    
       /**
     * Controls SalesOrder exchange.
     * @param array $request
     * @return string
     */
    private function salesOrderExchange($request) {
        $mode = vtlib_purify($request['mode']);
        $status = 'success';
        
        /* Begin execute transaction steps */
        switch($mode) {
            case 'checkauth':
                break;
            case 'init':
                $status = "zip=yes
                10240000000";
                break;
            case 'query':
                $status = $this->salesOrderQuery();
                break;
            case 'success':
                $status = 'success';
                break;            
            case 'file':       
                $orderFileName = vtlib_purify($request['filename']);
                $status = $this->executeSalesOrderImport($orderFileName);
                break;
            default:
                $status = 'Failure. Unknow mode!';
                break;
        }
      
        return $status;
    }
    
    /**
     * Controls catalog import process.
     * @param array $request
     * @return String
     */
    private function catalogExchange($request) {
        $mode = vtlib_purify($request['mode']);
        $status = 'success';
        
        /* Begin execute transaction steps */
        switch($mode) {
            case 'checkauth':
                break;
            case 'init':
                $status = "zip=yes
                1024000";
                break;
            case 'file':       
                $fileName = vtlib_purify($request['filename']);
                if(!$this->importFileManager->saveRequestFile($fileName)) {
                    $status = 'Failure. File not saved';
                }
                break;
            case 'import':
                $fileName = vtlib_purify($request['filename']);
                $status = $this->executeCatalogImport($fileName); 
                break;
            default:
                $status = 'Fail. Unknow mode!';
                break;
        }
      
        return $status;
    }
    
    /**
     * Save order file and import it. Return import status.
     * @param String $ordersFileName
     * @return String
     */
    private function executeSalesOrderImport($ordersFileName) {
        $saveStatus = $this->importFileManager->saveRequestFile($ordersFileName);
        if(!$saveStatus) {
            return 'Failure. File not saved!';
        }
        
        $ordersFileContent = $this->importFileManager->getOrdersFileContent($ordersFileName);
        return $this->startSalesOrderUpdate($ordersFileContent);
    }
    
    /**
     * Import orders from one es and return import status.
     * @param String $ordersFileContent
     * @return String
     */
    private function startSalesOrderUpdate($ordersFileContent) {
        $status = 'success';
        try {
            $parser = new CmlParser();
            $cmlSalesOrders = $parser->parseOrders($ordersFileContent);

            $salesOrderController = new SalesOrderController($this->userName);
            foreach($cmlSalesOrders as $order) {
                $salesOrderController->saveOrder($order);
            }
            
            $this->transactionHistory->fixSuccessTranzaction('SalesOrder', 'from_1c');
        } catch (Exception $ex) {
            $status = 'Failure. '.$ex->getMessage();
            $this->transactionHistory->fixTranzactionError('SalesOrder', 'from_1c', $status);
        }
        
        return $status;
    }
    
    /**
     * Get SalesOrder in xml  and prepare string before put it to one es.
     * @return String
     */
    private function salesOrderQuery() {
        $salesOrderController = new SalesOrderController($this->userName);
        $beginTime = $this->transactionHistory->getLastSalesOneEsTranzaction();
        $xmlSalesOrders = $salesOrderController->getXmlOrders($beginTime);

        /* One es system don't know utf-8 encoding */
        $xmlSalesOrders = str_replace("UTF-8", "windows-1251", $xmlSalesOrders);                
        return iconv("utf-8","windows-1251", $xmlSalesOrders);
    }
       
    /**
     * Imports catalog and return import status.
     * @param String $importFileName
     * @return String
     */
    private function executeCatalogImport($importFileName) {
        $status = 'success';
        
        /* Ignore file name contains offer - because we can import only two files together or will be error  */
        if(strpos($importFileName, 'import') !== false) {
            try {
                $this->startCatalogsUpdate($importFileName);
                $this->transactionHistory->fixSuccessTranzaction('Products', 'from_1c');
            } catch (Exception $ex) {
                $status = 'Failure. '.$ex->getMessage();
                $this->transactionHistory->fixTranzactionError('Products', 'from_1c', $status);
            }
        }

        return $status;
    }
      
    /**
     * Execute catalog update step.
     * @param type $name Description
     * @return String
     */
    private function startCatalogsUpdate($importFileName) {
        $importFileContent = $this->importFileManager->getImportFileContent($importFileName);
        $offersFileContent = $this->importFileManager->getOffersFileContent($importFileName);

        $parser = new CmlParser();
        $cmlCatalog = $parser->parseCatalog($importFileContent, $offersFileContent);

        $catalogController = new CatalogController($this->userName);
        $catalogController->save($cmlCatalog);
    }  
    
    /**
     * Controls transactions from one es system and send answers to inner system.
     * Start to execute step. Step commands get from $request.
     * @param Array $request 
     */
    public function startTransactionStep($request) {
        if( !($this->checkLogin()) ) {
            $status = 'Incorrect user name or access key!';
        } else {
            $status = $this->executeStep($request);
        }

        return $status;
    } 
    
}