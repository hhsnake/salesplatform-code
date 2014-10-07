<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
include_once dirname(__FILE__) . '/STREAMSMS.Class.v2.1.php';

Class SPSTREAMSMS2 extends STREAMSMS2 {

    /**
    * SendMessage - передача простого текстового SMS-сообщения
    *
    * @param $SessionID string Идентификатор сессии
    * @param $Data string Текст сообщения
    * @param $DestinationAddresses string Мобильный телефонный номер получателя сообщения
    * @param $SourceAddress string Имя отправителя
    * @param $ReceiptRequested boolean Отчет о статусе сообщения

    *
    * @return array("Ответ сервера" => (string), "ID сообщения" => (string))
    */
    function SendMessage($SessionID, $Data, $DestinationAddresses,$SourceAddress,$ReceiptRequested, $CountDA){

        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
            <SendMessage xmlns="http://ws1.streamsms.ru">
            <sessionID>'.$SessionID.'</sessionID>
	      <message>
                <Data>'.$Data.'</Data>
                <DestinationAddresses>'.$DestinationAddresses.'</DestinationAddresses>
                <SourceAddress>'.$SourceAddress.'</SourceAddress>
                <ReceiptRequested>'.$ReceiptRequested.'</ReceiptRequested>
              </message>
            </SendMessage>
          </soap:Body>
        </soap:Envelope>';

        $headers = array(
            "POST /SmsService.php HTTP/1.1",
            "Host: ws1.streamsms.ru",
            "Content-Type: text/xml; charset=utf-8",
            "Content-length: ".strlen($xml_data),
            "SOAPAction: http://ws1.streamsms.ru/SendMessage"
        );

	$data = $this->SendToServer($xml_data,$headers);
        // Show me the result
        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);
        return array(
            "Ответ сервера" => $this->GetCommandStatus($results[3]['value']),
            "ID сообщения" => $results[4]['value']
            );
    }

    /**
    * GetMessageState – запрос на получение статуса отправленного SMS-сообщения
    *
    * @param $SessionID string Идентификатор сессии
    * @param $MessageID string Идентификатор сообщения
    *
    * @return array("Ответ сервера" => (string), "Статус сообщения"" => (string))
    */
    function GetMessageState($SessionID,$MessageID){

        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://
            www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <GetMessageState xmlns="http://ws1.streamsms.ru">
              <sessionID>'.$SessionID.'</sessionID>
              <messageID>'.$MessageID.'</messageID>
            </GetMessageState>
          </soap:Body>
        </soap:Envelope>';

	$headers = array(
            "POST /SmsService.php HTTP/1.1",
            "Host: ws1.streamsms.ru",
            "Content-Type: text/xml; charset=utf-8",
            "Content-length: ".strlen($xml_data),
            "SOAPAction: http://ws1.streamsms.ru/GetMessageState"
        );

        $data = $this->SendToServer($xml_data,$headers);

        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);
        return array(
           "Ответ сервера" => $this->GetCommandStatus($results[3]['value']),
           "Статус сообщения" => $this->GetMessageStatus($results[4]['value']), 
        );
    }
}

?>