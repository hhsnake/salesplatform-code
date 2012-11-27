<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

$sp_numeric_forms = array (
    'ru_ru' => array(
        '0' => 'ноль',
        '10^3' => array('тысяча', 'тысячи', 'тысяч', 1),
        '10^6' => array('миллион', 'миллиона', 'миллионов',  0),
        '10^9' => array('миллиард', 'миллиарда', 'миллиардов',  0),
        '10^12' => array('триллион', 'триллиона', 'триллионов',  0),
        '100' => array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот', 'восемьсот','девятьсот'),
        '11' => array('','десять','одиннадцать','двенадцать','тринадцать', 'четырнадцать','пятнадцать','шестнадцать','семнадцать', 'восемнадцать','девятнадцать','двадцать'),
        '10' => array('','десять','двадцать','тридцать','сорок','пятьдесят', 'шестьдесят','семьдесят','восемьдесят','девяносто'),
        '1' => array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),// m
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять') // f
        ),
    ),

    'en_us' => array(
        '0' => 'zero',
        '10^3' =>  array('thousand', 'thounsands', 'thousands', 0),
        '10^6' => array('million', 'millions', 'millions',  0),
        '10^9' => array('billion', 'billions', 'billions',  0),
        '10^12' => array('trillion', 'trillions', 'trillions',  0),
        '100' => array('','one hundred','two hundreds','three hundreds','four hundreds','five hundreds','six hundreds', 'seven hundreds', 'eight hundreds','nine hundreds'),
        '11' => array('','ten','eleven','twelve','thirteen', 'fourteen','fifteen','sixteen','seventeen', 'eighteen','nineteen','twenty'),
        '10' => array('','ten','twenty','thirty','fourty','fifty', 'sixty','seventy','eighty','ninety'),
        '1' => array(
            array('','one','two','three','four','five','six','seven', 'eight','nine'),// m
            array('','one','two','three','four','five','six','seven', 'eight','nine') // f
        )
    ),
    
);


$sp_currency_forms = array (

    'ru_ru' => array(
        'RUB' => array(array('рубль', 'рубля', 'рублей',  0),
                       array('копейка', 'копейки', 'копеек', 1)),
        'USD' => array(array('доллар', 'доллара', 'долларов',  0),
                       array('цент', 'цента', 'центов', 0)),
        'EUR' => array(array('евро', 'евро', 'евро',  0),
                       array('цент', 'цента', 'центов', 0)),
    ),

    'en_us' => array(
        'RUB' => array(array('ruble', 'rubles', 'rubles',  0),
                       array('copeck', 'copecks', 'copecks', 0)),
        'USD' => array(array('dollar', 'dollars', 'dollars',  0),
                       array('cent', 'cents', 'cents', 0)),
        'EUR' => array(array('euro', 'euro', 'euro',  0),
                       array('cent', 'cents', 'cents', 0)),
    ),

);

?>
