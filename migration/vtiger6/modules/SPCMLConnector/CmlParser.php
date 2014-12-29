<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'modules/SPCMLConnector/ParseException.php';
require_once 'modules/SPCMLConnector/CmlCatalog.php';
require_once 'modules/SPCMLConnector/CmlProduct.php';
require_once 'modules/SPCMLConnector/CmlService.php';
require_once 'modules/SPCMLConnector/CmlSalesOrder.php';
require_once 'modules/SPCMLConnector/CmlAccount.php';


/**
 * Provides API to parse two types of documents - Catalog/Package, and Order Document
 */
class CmlParser {
    
    private function filter($value) {
        return str_replace("'", "", $value);
    }


    /**
     * Parse import file. Return CmlCatalog representation of import document.
     * @param String $import
     * @return CmlCatalog
     * @throws ParseException
     */
    private function parseImport($import) {
        $commerceData = $this->getCommerceData($import);
        
        /* Start initilizate catalog */
        $this->checkMandatoryChildElement($commerceData, "Каталог", "Not catalog in import.xml!");
        return $this->initilizateCatalog($commerceData->Каталог);
    }
    
    /**
     * Parse offers file. Return CmlCatalog representation of import document.
     * @param String $offer
     * @return CmlCatalog
     */
    private function parseOffer($offer) {
        $commerceData = $this->getCommerceData($offer);
        
        /* Start initilizate package */
        $this->checkMandatoryChildElement($commerceData, "ПакетПредложений", "Not package in offers.xml!");
        return $this->initilizatePackage($commerceData->ПакетПредложений);
    }
    
    /**
     * Create SimpleXMLElement describes commerce data. 
     * If document not valid - throw Exception.
     * @param String $xmlData
     * @return \SimpleXMLElement
     * @throws ParseException
     */
    private function getCommerceData($xmlData) {
        $commerceData = NULL;
        
        try {
            $commerceData = new SimpleXMLElement($xmlData);
        } catch (Exception $ex) {
            throw new ParseException("Invalid files encoding or import files not correct!");
        }
        return $commerceData;
    }
    
    /**
     * Check field as mandatory. If field not exists or it content empty - throw exception.
     * @param SimpleXMLElement $rootXmlElement
     * @param String $childName
     * @param String $errorMessage
     * @throws ParseException
     */
    private function checkMandatoryChildElement($rootXmlElement, $childName, $errorMessage) {

        /*  If tag exists but empty - condition will be true*/
        if($this->isChildExists($rootXmlElement, $childName)) {         
                return;
        }   
        
        /* If no field or it empty */
        throw new ParseException($errorMessage);
    }
    
    /**
     * Get first child contenct without tags. If no child or empty content - 
     * throw exception.
     * @param SimpleXMLElement $rootXmlElement
     * @param String $childName
     * @param String $errorMessage
     * @return String
     * @throws ParseException
     */
    private function getMandatoryChildContent($rootXmlElement, $childName, $errorMessage) {
        
        /*  If tag exists but empty - condition will be true*/
        if($this->isChildExists($rootXmlElement, $childName)) {         
                return strip_tags($rootXmlElement->$childName->asXML());
        }   
        
        /* If no field or it empty */
        throw new ParseException($errorMessage);
    }
    
    /**
     * Return child entity content. if no entity - return null.
     * @param SimpleXMLElement $rootXmlElement
     * @param String $childName
     * @return null|String
     */
    private function getChildContent($rootXmlElement, $childName) {
        if($this->isChildExists($rootXmlElement, $childName)) {         
                return strip_tags($rootXmlElement->$childName->asXML());
        }
        return NULL;
    }
    
    /**
     * Initilizate new CmlCatalog by getted $catalog.
     * @param SimpleXMLElement $catalog
     * @return CmlCatalog
     */
    private function initilizateCatalog($catalog) {
        
        /* Get catalog mandatory fields */
        $catalogName = $this->getMandatoryChildContent($catalog, 'Наименование', 'Not catalog name in import.xml');
        $catalogName = $this->filter($catalogName);
        $oneEsIdentifier =  $this->getMandatoryChildContent($catalog, 'Ид', 'Not catalog identificator in import.xml');
        $cmlCatalog = new CmlCatalog($catalogName, $oneEsIdentifier);
        
        $this->checkMandatoryChildElement($catalog, 'Товары', 'Not products in import.xml');
        
        $cmlCatalog = $this->initilizateCatalogInventories($cmlCatalog, $catalog->Товары);
        
        return $cmlCatalog;
    }
    
    /**
     * Initilizate prices, count and currencies of all inventories.
     * @param SimpleXMLElement $package
     * @return \CmlCatalog
     */
    private function initilizatePackage($package) {
        $oneEsIdentifier = $this->getMandatoryChildContent($package, 'Ид', 'Not id in offers!');
        $name = $this->getMandatoryChildContent($package, 'Наименование', 'Not name in offers!');
        $name = $this->filter($name);
        $cmlCatalog = new CmlCatalog($name, $oneEsIdentifier);
        
        /* Get package currency */
        $packageCurrency = $this->getPackageCurrency($package);
        $cmlCatalog->setCurrency($packageCurrency);
        
        /* Initilizate products and services */
        $this->checkMandatoryChildElement($package, "Предложения", "Not offers in offers.xml!");
        $cmlCatalog = $this->initilizatePackageInventories($package->Предложения, $cmlCatalog);
        
        return $cmlCatalog;
    }
    
    /**
     * Return CmlCatalog with only products - because in package no data about
     * inventory type.
     * @param SimpleXMLElement $package
     * @param CmlCatalog $cmlCatalog
     * @return \CmlCatalog
     */
    private function initilizatePackageInventories($packageOffers, $cmlCatalog) {
        foreach($packageOffers->Предложение as $offer) {   
            $inventory = $this->initilizatePackageInventory($offer);
            if($inventory->getCurrency() == NULL) {
                $inventory->setCurrency($cmlCatalog->getCurrency());
            }
            $cmlCatalog->addProduct($inventory);
        }     
        
        return $cmlCatalog;
    }
    
    /**
     * Initilizate inventory cost parameters by getted SimpleXMLElement.
     * @param SimpleXMLElement $offer
     * @return \CmlProduct
     */
    private function initilizatePackageInventory($offer) {
        $priceXmlElement = $this->getMandatoryPriceElement($offer);
        
        $price = $this->getMandatoryChildContent($priceXmlElement, 'ЦенаЗаЕдиницу',
                'Not product price in offers.xml');
        $currency = $this->getChildContent($priceXmlElement, 'Валюта');
        $conversionRate = $this->getChildContent($priceXmlElement, 'Коэффициент');
        
        $name = $this->getMandatoryChildContent($offer, 'Наименование',
                'Not product name in offers.xml');
        $name = $this->filter($name);
        
        $oneEsIdentifier = $this->getMandatoryChildContent($offer, 'Ид',
                'Not product identificator in offers.xml');
        $count = $this->getChildContent($offer, 'Количество');
        
        $inventory = new CmlProduct();
        $inventory->offersInitilizate($oneEsIdentifier, $name, $price, $currency, $conversionRate, $count);
        
        return $inventory;
    }
    
    /**
     * Return SimpleXmlElement describes price of product.
     * @param SimpleXmlElement $offer
     * @return SimpleXmlElement
     */
    private function getMandatoryPriceElement($offer) {
        $this->checkMandatoryChildElement($offer, "Цены", "Not product price in offers!");
        $prices = $offer->Цены;
        $this->checkMandatoryChildElement($prices,"Цена","Not product price in offers!");

        return $prices->Цена; 
    }
    
    /**
     * Return currency from first price type.
     * @param SimpleXMLElement $package
     * @return String
     */
    private function getPackageCurrency($package) {
        $this->checkMandatoryChildElement($package, "ТипыЦен", "Not price type in offers.xml!");
        $priceTypes = $package->ТипыЦен;
        
        /* Get first price type */
        $this->checkMandatoryChildElement($priceTypes, "ТипЦены", "Not price type in offers.xml!");
        $priceType = $priceTypes->ТипЦены;
        
        $currency = $this->getMandatoryChildContent($priceType, "Валюта", "Not price type in offers.xml!");
        return $currency;
    }
    
    /**
     * Initilizate catalog products and services and return CmlCatalog.
     * @param CmlCatalog $cmlCatalog
     * @param SimpleXMLElement $catalogInventories
     * return CmlCatalog
     */
    private function initilizateCatalogInventories($cmlCatalog, $catalogInventories) {
        foreach($catalogInventories->Товар as $importInventory) {
            if($this->isProduct($importInventory)) {
                $inventory = new CmlProduct();
                $inventory = $this->initilizateCatalogInventory($importInventory, $inventory);
                $cmlCatalog->addProduct($inventory);
            } else {
                $inventory = new CmlService();
                $inventory = $this->initilizateCatalogInventory($importInventory, $inventory);
                $cmlCatalog->addService($inventory);
            }
        }
        return $cmlCatalog;
    }
    
    /**
     * Return AbstractProduct by $importProduct
     * @param SimpleXMLElement $xmlInventoryDescription
     * @param CmlProduct|CmlService $inventory
     * @return AbstractProduct
     */
    private function initilizateCatalogInventory($xmlInventoryDescription, $inventory) {
        $name = $this->getMandatoryChildContent($xmlInventoryDescription, 'Наименование',
                'Not product name in import.xml');
        $oneEsIdentifier = $this->getMandatoryChildContent($xmlInventoryDescription, 'Ид',
                'Not product identificator in import.xml');
        $unitName = $this->getChildContent($xmlInventoryDescription, 'БазоваяЕдиница');
        $article = $this->getChildContent($xmlInventoryDescription, 'Артикул');
        $NDS = $this->getTaxRate($xmlInventoryDescription);
        
        /* REST API broke on special symbols */
        $name = $this->filter($name);
        $article = $this->filter($article);
        
        $inventory->catalogInitilizate($oneEsIdentifier, $name, $article, $unitName, $NDS);
        
        return $inventory;
    }
  
    /**
     * Check by props of xmlElement product type of current product.
     * If no props or - product will be service.
     * @param SimpleXMLElement $product
     * @return boolean
     */
    private function isProduct($product) {
        if($this->isChildExists($product,'ЗначенияРеквизитов')) {
            $props = $product->ЗначенияРеквизитов;
            foreach($props->ЗначениеРеквизита as $prop) {
               $propName = $this->getChildContent($prop, 'Наименование');
               $propValue = $this->getChildContent($prop, 'Значение');
               if($propName == 'ТипНоменклатуры' && $propValue == 'Товар' ) {
                   return true;
               }
            }
        }
        
        return false;
    }
    
    /**
     * Return tax rate. If no tax - return 0.
     * @param SimpleXMLElement $importProduct
     * @return int
     */
    private function getTaxRate($importProduct) {
        if($this->isChildExists($importProduct, 'СтавкиНалогов')) {
            $rates = $importProduct->СтавкиНалогов;
            
            /* Search rate named NDS  */
            foreach($rates->СтавкаНалога as $rate) {
                $rateName = $this->getChildContent($rate, 'Наименование');
                $rateValue = $this->getChildContent($rate, 'Ставка');
                
                /* If tax rate not NULL */
                if($rateName == 'НДС' && $rateValue != 'БезНалога' && $rateValue != NULL) {
                    return $rateValue;
                }
            }
        }
        return 0;
    }
    
    /**
     * If $rootXmlElement not have child with name $childName or it empty - return false.
     * @param SimpleXmlElement $rootXmlElement
     * @param String $childName
     * @return boolean
     */
    private function isChildExists($rootXmlElement, $childName) {
        $element = $rootXmlElement->$childName;

        /*  If tag exists but empty - condition will be true*/
        if($element != "") {     
            return true;
        }
        return false;
    }
    
    /**
     * Join prices of products from offers package with catalog services and
     * products. Joining inventories by it's one es identifier.
     * @param CmlCatalog $catalog
     * @param CmlCatalog $package
     */
    private function joinImportWithOffer($catalog, $package) {
        $catalog->setCurrency($package->getCurrency());
        $catalog->setInventoriesCurrencyCode($package->getCurrency());
        $catalog = $this->joinImportWithOfferProducts($catalog, $package);
        $catalog = $this->joinImportWithOfferServices($catalog, $package);

        return $catalog;
    }
    
    /**
     * Add to services in $catalog prices, count and currency from $package.
     * Return updated $catalog.
     * @param CmlCatalog $catalog
     * @param CmlCatalog $package
     * @return CmlCatalog
     */
    private function joinImportWithOfferServices($catalog, $package) {
        $catalogServices = $catalog->getServices();
        foreach($catalogServices as $number => $catalogService) {     //key need to change product in array
            foreach($package->getProducts() as $packageInventory) {     //in package all inventories as products
                if($catalogService->compare($packageInventory)) {
                    
                    /* Add price, currency and count of service - and reinitilizate array of services */
                    $catalogService->mergeImportWithOffer($packageInventory);
                    $catalogServices[$number] = $catalogService;
                    break;      //no need search more products
                }
            }
        }
        
        /* Reinit catalog services */
        $catalog->setServices($catalogServices);
        return $catalog;
    }
    
    /**
     * Add to products in $catalog prices, count and currency from $package.
     * Return updated $catalog.
     * @param CmlCatalog $catalog
     * @param CmlCatalog $package
     * @return CmlCatalog
     */
    private function joinImportWithOfferProducts($catalog, $package) {
        $catalogProducts = $catalog->getProducts();
        foreach($catalogProducts as $number => $catalogProduct) {     //key need to change product in array
            foreach($package->getProducts() as $packageProduct) {     //in package all inventories as products
                if($catalogProduct->compare($packageProduct)) {
                    
                    /* Add price, currency and count of product - and reinitilizate array of products */
                    $catalogProduct->mergeImportWithOffer($packageProduct);
                    $catalogProducts[$number] = $catalogProduct;
                    break;      //no need search more products
                }
            }
        }
        
        /* Reinit catalog products */
        $catalog->setProducts($catalogProducts);
        return $catalog;
    }
    
    /**
     * Initilizate orders from SimpleXmlElement $orders.
     * @param SimpleXmlElement $xmlOrders
     * @return array<CmlSalesOrder>
     */
    private function initilizateOrders($xmlOrders) {
        $cmlSalesOrders = array();
        foreach($xmlOrders->Документ as $xmlOrder) {
            $number = $this->getMandatoryChildContent($xmlOrder, 'Номер', 'Not document number in orders.xml');
            $oneEsidentifier = $this->getMandatoryChildContent($xmlOrder, 'Ид', 'Not document id in orders.xml! Document number - ' . $number);
            $currency = $this->getMandatoryChildContent($xmlOrder, 'Валюта', 'Not document currency in orders.xml! Document number - ' . $number);
            
            $salesOrder = new CmlSalesOrder($number, $oneEsidentifier, $currency);
            $salesOrder = $this->getDocumentAccount($salesOrder,$xmlOrder);
            $salesOrder = $this->initilizateOrderInventories($salesOrder, $xmlOrder);
            $salesOrder = $this->initilizateOrderProps($salesOrder, $xmlOrder);
            
            $cmlSalesOrders[] = $salesOrder;
        }
        
        return $cmlSalesOrders;
    }
    
    /**
     * Initilizate account from document. If no account - throw exception.
     * @param CmlSalesOrder $salesOrder
     * @param SimpleXMLElement $order
     * @return CmlSalesOrder
     */
    private function getDocumentAccount($salesOrder, $xmlOrder) {
        $xmlAccount = $xmlOrder;
        if($this->isChildExists($xmlOrder, 'Контрагенты')) {
            $xmlAccount = $xmlOrder->Контрагенты;
        }
        
        $this->checkMandatoryChildElement($xmlAccount, 'Контрагент', 'Not account in order! Order number - ' . $salesOrder->getNumber());
        $account = $xmlAccount->Контрагент;
                
        $oneEsIdentifier = $this->getMandatoryChildContent($account, 'Ид', 'Not account id in order! Order number - ' . $salesOrder->getNumber());
        
        /* Phisical or legal face */
        $accountName = $this->getChildContent($account, 'ПолноеНаименование');
        if($accountName == null) {
            $accountName = $this->getMandatoryChildContent($account, 'ОфициальноеНаименование', 'Not account name in order! Order number - ' . $salesOrder->getNumber());
        }
        $accountName = $this->filter($accountName);
        
        $inn = $this->getChildContent($account, 'ИНН');
        $kpp = $this->getChildContent($account, 'КПП');
        
        $cmlAccount = new CmlAccount($accountName, $oneEsIdentifier);
        $cmlAccount->initAccountTaxInfo($inn, $kpp);
        $this->parseAccountAddress($cmlAccount, $account);
        
        $salesOrder->addAccount($cmlAccount);
        return $salesOrder;
    }
    
    /**
     * Initlizate account address if it exists.
     * @param CmlAccount $cmlAccount
     * @param SimpleXmlElement $xmlAccount
     */
    private function parseAccountAddress($cmlAccount, $xmlAccount) {
        //TODO: In next version diff phis and legal addreses
        
        $address = null;

        /* Phisical or legal face */
        if($this->isChildExists($xmlAccount, 'АдресРегистрации')) {
            $xmlAddress = $xmlAccount->АдресРегистрации;
        } elseif($this->isChildExists($xmlAccount, 'Адрес')) {
            $xmlAddress = $xmlAccount->Адрес;
        } else {
            return;
        }

        /* Parse addres fields */
        foreach($xmlAddress->АдресноеПоле as $xmlAddressPart) {

            if($this->isChildExists($xmlAddressPart, 'Значение')) {
                
                $address = $address.$this->getChildContent($xmlAddressPart, 'Значение')." ";
            } 
        }
        
        if($address != null) {
            $cmlAccount->initAccountAddress($address, $address);        //shipping and billing equals in this version
        }
    }
    
    /**
     * Parse all order products and services. If no one servicew or product - throw exception.
     * @param CmlSalesOrder $salesOrder
     * @param SimpleXMLElement $order
     * @return CmlSalesOrder 
     */
    private function initilizateOrderInventories($salesOrder, $order) {
        $this->checkMandatoryChildElement($order, 'Товары', 'Not products in order!' . $salesOrder->getNumber());
        $orderInventories = $order->Товары;
        
        $this->checkMandatoryChildElement($orderInventories, 'Товар', 'No one product or service in order. Order number - ' . $salesOrder->getNumber());
        
        foreach($orderInventories->Товар as $xmlInventory) {
            if($this->isProduct($xmlInventory)) {
                $inventory = new CmlProduct();
                $inventory = $this->initilizateOrderInventory($xmlInventory, $inventory, $salesOrder);
                $salesOrder->addProduct($inventory);
            } else {
                $inventory = new CmlService();
                $inventory = $this->initilizateOrderInventory($xmlInventory, $inventory, $salesOrder);
                $salesOrder->addService($inventory);
            } 
        }
        return $salesOrder;
    }
    
    /**
     * Initilizate AbstractProduct by order xml.
     * @param SimpleXmlElement $orderProduct
     * @param AbstractProduct $inventory 
     * @param CmlSalesOrder $salesOrder
     * @return AbstractProduct
     */
    private function initilizateOrderInventory($orderProduct, $inventory, $salesOrder) {
        $name = $this->getMandatoryChildContent($orderProduct, 'Наименование', 'Not product name in order! Order number - ' . $salesOrder->getNumber());
        $oneEsIdentifier = $this->getMandatoryChildContent($orderProduct, 'Ид', 'Not product identificator in order. Order number - ' . $salesOrder->getNumber());
        $price = $this->getMandatoryChildContent($orderProduct, 'ЦенаЗаЕдиницу', 'Not product price in order. Order number - ' . $salesOrder->getNumber());
        $count = $this->getMandatoryChildContent($orderProduct, 'Количество', 'Not product count in order. Order number - ' . $salesOrder->getNumber());
        $unitName = $this->getChildContent($orderProduct, 'БазоваяЕдиница');
        $article = $this->getChildContent($orderProduct, 'Артикул');
        $NDS = $this->getTaxRate($orderProduct);
        
        $name = $this->filter($name);
        $article = $this->filter($article);
        
        $inventory->orderInitilizate($oneEsIdentifier, $name, $article, $unitName, $price, $count, $NDS);
        
        return $inventory;
    }
    
    /**
     * Initilizate order props
     * @param CmlSalesOrder $salesOrder
     * @param SimpleXmlElement $xmlOrder
     * @return CmlSalesOrder
     */
    private function initilizateOrderProps($salesOrder, $xmlOrder) {
        if($this->isChildExists($xmlOrder, 'ЗначенияРеквизитов')) {
            $xmlProps = $xmlOrder->ЗначенияРеквизитов;
            foreach($xmlProps->ЗначениеРеквизита as $prop) {
                $propName = $this->getChildContent($prop, 'Наименование');
                $propValue = $this->getChildContent($prop, 'Значение');
                $salesOrder->addProp($propName, $propValue);
            }
        }
        
        return $salesOrder;
    }
    
    /**
     * Parse import and offer of catalog described in xml and return CmlCatalog
     * representation of parsed documents. If error on parse - throw Exception.
     * @param String $import
     * @param String $offer
     * @return CmlCatalog
     */
    public function parseCatalog($import, $offer) {
        $catalog = $this->parseImport($import);
        $package = $this->parseOffer($offer);
        
        return $this->joinImportWithOffer($catalog, $package);
    }
    
    /**
     * Parse orders described in xml and return array of CmlSalesOrder
     * @param array<CmlSalesOrder> $order
     */
    public function parseOrders($order) {
        $commerceData = $this->getCommerceData($order);
        $orders = $this->initilizateOrders($commerceData);
        
        return $orders;
    }
}
