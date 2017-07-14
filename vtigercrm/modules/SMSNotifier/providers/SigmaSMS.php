<?php
/***********************************************************************************
 * PINstudio #Binizik
 ************************************************************************************/
include_once 'vtlib/Vtiger/Net/Client.php';

class SMSNotifier_SigmaSMS_Provider implements SMSNotifier_ISMSProvider_Model {

    private $_username;
    private $_password;
    private $_parameters = array();
    private $_errorsMsg = array(
            1 => 'ошибка авторизации',
            2 => 'недостаточно денежных средств',
            3 => 'запрос отвергнут провайдером',
            4 => 'неверный запрос',
            5 => 'неверный тип запроса',
            6 => 'неверные параметры сообщения',
            7 => 'запрос с неизвестного IP адреса',
            8 => 'сообщение не найдено в БД',
            9 => 'неверный адрес отправителя',
            10 => 'неверный текст сообщения',
            11 => 'неверный параметр validity_period',
            13 => 'превышено максимальное количество номеров за один запрос',
            14 => 'неверный тип группы',
            15 => 'ошибка сохранения в БД',
            16 => 'неверный формат даты',
            17 => 'неверный формат даты и времени',
            99 => 'внутренняя ошибка системы'
        );

    const SENDER_PARAM = 'Отправитель';

    private static $REQUIRED_PARAMETERS = array(self::SENDER_PARAM);

    function __construct() {
    }

    public function getName() {
        return 'SigmaSMS';
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
        if (!is_array($tonumbers)) {
            $tonumbers = array($tonumbers);
        }
        $message = htmlspecialchars($message);
        $results = array();
        $fields = array();

        foreach ($tonumbers as $to) {
            $fields['target'] .= $to.',';
        }
        
        $fields['target'] = urlencode( rtrim($fields['target'], ',') );
        $fields['user'] = urlencode( $this->_username );
        $fields['pass'] = urlencode( md5($this->_password) );
        $fields['type'] = urlencode('SMS');
        $fields['action'] = urlencode('post');
        $fields['message'] = urlencode( htmlspecialchars($message) );
        $fields['sender'] = urlencode( $this->getParameter(self::SENDER_PARAM) );


        $responseXml = $this->curl_send($fields, 'http://gates.sigmasms.ru/http/gate.cgi');
        $response = simplexml_load_string($responseXml);

        if (empty($response->result->error)) {
            foreach ($response->result->message as $k => $v) {
                $result['id'] = $v->attributes()['id'];
                $result['to'] = $v->attributes()['target'];
                $result['status'] = self::MSG_STATUS_PROCESSING;
                $result['statusmessage'] = "Передано в MSGC на отправку - в очереди отправки MSGC";

                $results[] = $result;
            }

            if (!empty($response->errors)) {
                foreach ($response->errors->error as $k => $v) {
                    $result['id'] = time()-rand(0, 2244); // Random id
                    $result['to'] = $v->attributes()['target'];
                    $result['error'] = true;
                    $result['status'] = self::MSG_STATUS_ERROR;
                    $result['statusmessage'] = $v;

                    $results[] = $result;
                }
            } 
        } else {
            foreach (explode(',', $fields['target']) as $k => $v) {
                $result['id'] = time()-rand(0, 2244); // Random id
                $result['to'] = $v;
                $result['error'] = true;
                $result['status'] = self::MSG_STATUS_ERROR;
                $result['statusmessage'] = $this->_errorsMsg[ (int)$response->result->error ];

                $results[] = $result;
            }
        }

        return $results;
    }

    public function query($messageId) {
        if (empty($messageId)){
            $result['error'] = true;
            $result['needlookup'] = 0;
            $result['statusmessage'] = 'Пустой идентификатор сообщения';
            $result['status'] = self::MSG_STATUS_ERROR;

            return $result;
        }

        $fields['user'] = urlencode( $this->_username );
        $fields['pass'] = urlencode( md5($this->_password) );
        $fields['message_id'] = urlencode($messageId);
        $fields['action'] = urlencode('status');

        $responseXml = $this->curl_send($fields, 'http://gates.sigmasms.ru/http/gate.cgi');
        $response = simplexml_load_string($responseXml);

        if (!empty($response->result->error)) {
            $result['error'] = true;
            $result['needlookup'] = 0;
            $result['status'] = self::MSG_STATUS_ERROR;
            $result['statusmessage'] = $this->_errorsMsg[ (int)$response->result->error ];
        } else {
            switch($response->MESSAGES->MESSAGE->MSGSTC_CODE) {
                case 'delivered':
                    // Сообщение было доставлено. Конечный статус (не меняется со временем)
                    $result['statusmessage'] = "Сообщение было доставлено";
                    $result['status'] = self::MSG_STATUS_DELIVERED;
                    $result['needlookup'] = 0;
                    $result['error'] = false;

                    break;
                case 'wait':
                    // Не конечный статус (меняется со временем).
                    $result['statusmessage'] = "Передано в MSGC на отправку - в очереди отправки MSGC";
                    $result['status'] = self::MSG_STATUS_PROCESSING;
                    $result['needlookup'] = 1;
                    $result['error'] = false;

                    break;
                case 'failed':
                    // Сообщение не было доставлено. Конечный статус (не меняется со временем)
                    $result['statusmessage'] = "Сообщение не было доставлено";
                    $result['status'] = self::MSG_STATUS_FAILED;
                    $result['needlookup'] = 0;
                    $result['error'] = true;

                    break;
                default:
                    $result['statusmessage'] = "Неизвестный статус: ".$response->MESSAGES->MESSAGE->MSGSTC_CODE;
                    $result['status'] = self::MSG_STATUS_ERROR;
                    $result['needlookup'] = 0;
                    $result['error'] = true;

                    break;
            }
        }

        return $result;
    }

    private function curl_send($fields, $url) {
        foreach ($fields as $k => $v) {
            $data .= $k.'='.$v.'&';
        }
        rtrim($data,'&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
