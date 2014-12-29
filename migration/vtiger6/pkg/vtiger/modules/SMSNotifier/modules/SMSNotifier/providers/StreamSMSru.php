<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 * The Original Code is: SalesPlatform.ru
 ************************************************************************************/
include_once 'vtlib/Vtiger/Net/Client.php';
include_once dirname(__FILE__) . '/streamsms/STREAMSMSOld.Class.php';

class SMSNotifier_StreamSMSru_Provider implements SMSNotifier_ISMSProvider_Model {
	
	private $_username;
	private $_password;
	private $_parameters = array();

        private $_streamsms;
	
	const SENDER_PARAM = 'LBL_SMS_SENDER_StreamSMSru';
	const TIME_PARAM = 'LBL_SMS_TIME_StreamSMSru';
        
	private static $REQUIRED_PARAMETERS = array(self::SENDER_PARAM, self::TIME_PARAM);
	
	function __construct() {
            $this->_streamsms = new STREAMSMSOld();
	}
	
        public function getName() {
		return 'StreamSMSru';
	}
        
	public function setAuthParameters($username, $password) {
		$this->_username = $username;
		$this->_password = $password;
	}
	
	public function setParameter($key, $value) {
		$this->_parameters[$key] = $value;
	}
	
	public function getParameter($key, $defvalue = false)  {
		if(isset($this->_parameters[$key])) {
			return $this->_parameters[$key];
		}
		return $defvalue;
	}
	
	public function getRequiredParams() {
		return self::$REQUIRED_PARAMETERS;
	}
	
	public function getServiceURL($type = false) {		
		return false;
	}	
	
	public function send($message, $tonumbers) {
		if(!is_array($tonumbers)) {
                    $tonumbers = array($tonumbers);
		}
                $status = 1;
                $flash = 0;
                $time = $this->getParameter(self::TIME_PARAM);
                if ($time <= 0) {
                    $time = 10;
                }
                $sender = $this->getParameter(self::SENDER_PARAM);
                $message = htmlspecialchars($message);
		$results = array();
                foreach($tonumbers as $to) {
                    $to = $this->fixRussianPhone($to);
                    $result['to'] = $to;
                    if (strlen($to) == 11) {
                        $streamsms_result = $this->_streamsms->SendTextMessage(
                                $this->_username, $this->_password,
                                $to, $message, $sender, $status, $flash, $time);
                        $result['id'] = $streamsms_result['ID сообщения'];
                        $result['statusmessage'] = $streamsms_result['Ответ сервера'];
                        if (substr_count($streamsms_result['Ответ сервера'], "Ошибка") > 0) {
                            $result['error'] = true;
                            $result['status'] = self::MSG_STATUS_ERROR;
                        } else {
                            $result['error'] = false;
                            $result['status'] = self::MSG_STATUS_PROCESSING;
                        }
                    } else {
                        $result['error'] = true;
                        $result['status'] = self::MSG_STATUS_ERROR;
                        $result['statusmessage'] = 'Ошибочный номер телефона';
                    }
                    $results[] = $result;
                }
		return $results;
	}

        private function fixRussianPhone($to) {
                $to = trim($to);
                $lead_plus = ($to[0] == '+');
                $to = preg_replace('/[^0-9,]/', '', $to);
                if (!$lead_plus && (strlen($to) == 11)) {
                    $to[0] = '7';   // Replace 8 with 7 for Russia
                }
                if (strlen($to) == 10) {
                    $to = "7".$to;  // Russia code
                }
                return $to;
        }
	
	public function query($messageid) {
		if(empty($messageid)){
			$result['error'] = true;
			$result['needlookup'] = 0;
			$result['statusmessage'] = 'Пустой идентификатор сообщения';
			$result['status'] = self::MSG_STATUS_ERROR;
			return($result);
		}
                $streamsms_result = $this->_streamsms->GetMessageState(
                        $this->_username,
                        $this->_password,
                        $messageid);
                $result = array('id' => $messageid, 'error' => false, 'needlookup' => 1, 'statusmessage' => $streamsms_result['Ответ сервера']);
		if (substr_count($streamsms_result['Ответ сервера'], "Ошибка") > 0) {
			$result['error'] = true;
			$result['needlookup'] = 0;
                        $result['status'] = self::MSG_STATUS_ERROR;
		} else {
                        $result['statusmessage'] = $streamsms_result['Статус сообщения'];
                        if (substr_count($streamsms_result['Статус сообщения'], "Ошибка") > 0) {
                                $result['needlookup'] = 0;
                                $result['status'] = self::MSG_STATUS_FAILED;
                                $result['statusmessage'] = $streamsms_result['Статус сообщения'];
                        } else {
                            switch($streamsms_result['Статус сообщения']) {
                                case 'Сообщение ожидает отправки':
                                        $result['status'] = self::MSG_STATUS_PROCESSING;
                                        $result['needlookup'] = 1;
                                        break;
                                case 'Сообщение доставлено на сервер':
                                        $result['status'] = self::MSG_STATUS_DISPATCHED;
                                        $result['needlookup'] = 1;
                                        break;
                                case 'Сообщение передано в мобильную сеть':
                                        $result['status'] = self::MSG_STATUS_PROCESSING;
                                        $result['needlookup'] = 1;
                                        break;
                                case 'Сообщение доставлено получателю':
                                        $result['status'] = self::MSG_STATUS_DELIVERED;
                                        $result['needlookup'] = 0;
                                        break;
                                case 'Статус не распознан':
                                        $result['status'] = self::MSG_STATUS_ERROR;
                                        $result['needlookup'] = 0;
                                        break;
                                default:
                                        $result['status'] = self::MSG_STATUS_ERROR;
                                        $result['needlookup'] = 0;
                                        break;
                            }
                        }
		}
		return $result;
	}
}
?>
