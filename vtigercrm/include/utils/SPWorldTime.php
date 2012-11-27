<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

//Clock data forming and localization
function sp_worldTimeData() {
    $countrysTime_arr = array(
                                 0 => array('0','Local time'),
                                 1 => array('9.30','Australia - Adelaide'),
                                 2 => array('8','Australia - Perth'),
                                 3 => array('10','Australia - Sydney'),
                                 4 => array('1','Austria'),
                                 5 => array('1','Algeria'),
                                 6 => array('-3','Argentina'),
                                 7 => array('4.30','Afghanistan'),
                                 8 => array('6','Bangladesh'),
                                 9 => array('3','Bahrain'),
                                 10 => array('1','Belgium'),
                                 11 => array('6.30','Burma (Myanmar)'),
                                 12 => array('2','Bulgaria'),
                                 13 => array('-4','Bolivia'),
                                 14 => array('-5','Brazil - Andes'),
                                 15 => array('-3','Brazil - East'),
                                 16 => array('-4','Brazil - West'),
                                 17 => array('0','UK'),
                                 18 => array('1','Hungary'),
                                 19 => array('7','Vietnam'),
                                 20 => array('0','Ghana'),
                                 21 => array('1','Germany'),
                                 22 => array('-3','Greenland'),
                                 23 => array('2','Greece'),
                                 24 => array('1','Denmark'),
                                 25 => array('2','Egypt'),
                                 26 => array('2','Zambia'),
                                 27 => array('2','Zimbabwe'),
                                 28 => array('2','Israel'),
                                 29 => array('5.30','India'),
                                 30 => array('8','Indonesia - Bali, Borneo'),
                                 31 => array('9','Indonesia - Irian Jaya'),
                                 32 => array('7','Indonesia - Sumatra, Java'),
                                 33 => array('3','Iraq'),
                                 34 => array('3.30','Iran'),
                                 35 => array('1','Spain'), 
                                 36 => array('1','Italy'),
                                 37 => array('3','Yemen'),
                                 38 => array('-8','Canada - Vancouver'),
                                 39 => array('-6','Canada - Winnipeg'),
                                 40 => array('-7','Canada - Calgary'),
                                 41 => array('-4','Canada - Nova Scotia'),    
                                 42 => array('-3.30','Canada - Newfoundland'),
                                 43 => array('-5','Canada - Toronto'),
                                 44 => array('3','Qatar'), 
                                 45 => array('3','Kenya'),
                                 46 => array('8','China - Mainland'),
                                 47 => array('8','China - Taiwan'), 
                                 48 => array('-5','Colombia'),
                                 49 => array('9','Korea (North &amp; South)'),
                                 50 => array('-5','Cuba'),
                                 51 => array('3','Kuwait'), 
                                 52 => array('1','Libya'),
                                 53 => array('4','Mauritius'),
                                 54 => array('8','Malaysia'),
                                 55 => array('1','Mali'),
                                 56 => array('5','Maldives'),
                                 57 => array('-6','Mexico'),
                                 58 => array('0','Morocco'),
                                 59 => array('5.45','Nepal'),
                                 60 => array('1','Nigeria'),
                                 61 => array('1','Netherlands'),
                                 62 => array('12','New Zealand'),
                                 63 => array('1','Norway'),
                                 64 => array('4','UAE'),
                                 65 => array('4','Oman'),   
                                 66 => array('5','Pakistan'),
                                 67 => array('-5','Peru'),
                                 68 => array('1','Poland'),
                                 69 => array('1','Portugal'),
                                 70 => array('9','Russia - Vladivostok'),
                                 71 => array('3','Russia - Moscow'),
                                 72 => array('11','Russia - Kamchatka'),
                                 73 => array('2','Romania'),
                                 74 => array('3','Saudi Arabia'),
                                 75 => array('4','Seychelles'),
                                 76 => array('8','Singapore'),
                                 77 => array('3','Syria'),
                                 78 => array('-9','USA - Alaska'),
                                 79 => array('-9','USA - Arizona'),
                                 80 => array('-10','USA - Hawaii'),
                                 81 => array('-7','USA - Mountain'),
                                 82 => array('-5','USA - Indiana East'),
                                 83 => array('-8','USA - Pacific'),
                                 84 => array('-6','USA - Central'),
                                 85 => array('7','Thailand'),
                                 86 => array('12','Tonga'),
                                 87 => array('2','Turkey'),
                                 88 => array('5','Uzbekistan'),
                                 89 => array('3','Ukraine'),
                                 90 => array('12','Fiji'),
                                 91 => array('8','Philippines'),
                                 92 => array('2','Finland'),
                                 93 => array('1','France'),
                                 94 => array('-5','Chile'),
                                 95 => array('1','Sweden'),
                                 96 => array('1','Switzerland'),  
                                 97 => array('5.30','Sri Lanka'),    
                                 98 => array('-5','Ecuador'),
                                 99 => array('2','South Africa'), 
                                 100 => array('1','Yugoslavia'),   
                                 101 => array('-5','Jamaica'),
                                 102 => array('9','Japan')
                         );
    
    $months_arr = array('January','February','March','April','May','June','July','August','September','October','November','December');
    
    $countrysTime_transl = array();
    for($i=0; $i < count($countrysTime_arr); $i++) {
        $arr_elem = $countrysTime_arr[$i];
        $countrysTime_transl[$i] = array($arr_elem[0],getTranslatedString($arr_elem[1]));
    }
    
    $months_transl = '';
    for($i=0; $i < count($months_arr); $i++) {
        if ($i == 0) {
            $months_transl = getTranslatedString($months_arr[$i]);
        }
        else {
            $months_transl .= ';'.getTranslatedString($months_arr[$i]);
        }
    }
    
    $ret_arr = array();
    $ret_arr[0] = $countrysTime_transl;
    $ret_arr[1] = $months_transl;
    
    return $ret_arr;
}
?>
