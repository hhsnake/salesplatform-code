<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'vtlib/Vtiger/PDF/models/Model.php';
include_once 'vtlib/Vtiger/PDF/PDFGenerator.php';
include_once 'data/CRMEntity.php';
include_once 'include/SalesPlatform/PDF/viewers/SPHeaderViewer.php';
include_once 'include/SalesPlatform/PDF/viewers/SPFooterViewer.php';
include_once 'include/SalesPlatform/PDF/viewers/SPContentViewer.php';
include_once 'vtlib/Vtiger/PDF/viewers/PagerViewer.php';

class SalesPlatform_PDF_SPPDFController {

	protected $module;
	protected $focus = null;
	protected $template;
	protected $pageOrientation;
	protected $headerSize;
	protected $footerSize;
	protected $documentModel = null;

	function __construct($module, $templateid) {
		$this->moduleName = $module;
		$this->template = $this->loadTemplate($templateid);
	}
	
	function loadTemplate($templateid) {
	    global $adb;

	    $templates_result = $adb->pquery("select * from sp_templates where templateid=$templateid", array());
	    if($templates_result) {
		$templates_row = $adb->fetch_array($templates_result);
		if($templates_row) {
		    $this->pageOrientation = $templates_row['page_orientation'];
		    $this->headerSize = $templates_row['header_size'];
		    $this->footerSize = $templates_row['footer_size'];
		    return $templates_row['template'];
		}
	    }
	    
	    return '';
	}

	function loadRecord($id) {
		global $current_user;
		$this->focus = $focus = CRMEntity::getInstance($this->moduleName);
		$focus->retrieve_entity_info($id,$this->moduleName);
		$focus->apply_field_security();
		$focus->id = $id;
	}

	function getPDFGenerator() {
		return new Vtiger_PDF_Generator();
	}

	function getContentViewer() {
            $contentViewer = new SalesPlatform_PDF_SPContentViewer($this->template, $this->pageOrientation);
            $contentViewer->setDocumentModel($this->buildDocumentModel());
            $contentViewer->setContentModels(array(new Vtiger_PDF_Model()));
            $contentViewer->setSummaryModel(new Vtiger_PDF_Model());
            $contentViewer->setLabelModel($this->buildContentLabelModel());
            $contentViewer->setWatermarkModel($this->buildWatermarkModel());
            return $contentViewer;
	}

	function getHeaderViewer() {
            $headerViewer = new SalesPlatform_PDF_SPHeaderViewer($this->template, $this->headerSize);
            $headerViewer->setModel($this->buildDocumentModel());
            return $headerViewer;
	}

	function getFooterViewer() {
            $footerViewer = new SalesPlatform_PDF_SPFooterViewer($this->template, $this->footerSize);
            $footerViewer->setModel($this->buildFooterModel());
            $footerViewer->setOnLastPage();
            return $footerViewer;
	}

	function getPagerViewer() {
            $pagerViewer = new Vtiger_PDF_PagerViewer();
            $pagerViewer->setModel($this->buildPagermodel());
            return $pagerViewer;
	}

	function Output($filename, $type) {
		if(is_null($this->focus)) return;

		$pdfgenerator = $this->getPDFGenerator();
		
		$pdfgenerator->setPagerViewer($this->getPagerViewer());
		$pdfgenerator->setHeaderViewer($this->getHeaderViewer());
		$pdfgenerator->setFooterViewer($this->getFooterViewer());
		$pdfgenerator->setContentViewer($this->getContentViewer());
		
                $pdfgenerator->generate($filename, $type);
	}


	// Helper methods
	function buildFooterModel() {
		$footerModel = new Vtiger_PDF_Model();
		return $footerModel;
	}

        function buildDocumentModel() {
	
		global $adb;
		
		$model = new Vtiger_PDF_Model();

		// Company information
		$result = $adb->pquery("SELECT * FROM vtiger_organizationdetails", array());
		$num_rows = $adb->num_rows($result);
		if($num_rows) {
			$resultrow = $adb->fetch_array($result);

			$model->set('orgAddress', $adb->query_result($result,0,"address"));
			$model->set('orgCity', $adb->query_result($result,0,"city"));
			$model->set('orgState', $adb->query_result($result,0,"state"));
			$model->set('orgCountry', $adb->query_result($result,0,"country"));
			$model->set('orgCode', $adb->query_result($result,0,"code"));
			
			$model->set('orgBillingAddress', implode(', ', 
			    array($adb->query_result($result,0,"code"), 
				  $adb->query_result($result,0,"city"),
				  $adb->query_result($result,0,"address"))));
			
			$model->set('orgPhone', $adb->query_result($result,0,"phone"));
			$model->set('orgFax', $adb->query_result($result,0,"fax"));
			$model->set('orgWebsite', $adb->query_result($result,0,"website"));
			$model->set('orgInn', $adb->query_result($result,0,"inn"));
			$model->set('orgKpp', $adb->query_result($result,0,"kpp"));
			$model->set('orgBankAccount', $adb->query_result($result,0,"bankaccount"));
			$model->set('orgBankName', $adb->query_result($result,0,'bankname'));
			$model->set('orgBankId', $adb->query_result($result,0,'bankid'));
			$model->set('orgCorrAccount', $adb->query_result($result,0,'corraccount'));

			if($adb->query_result($result,0,'director')) {
			    $model->set('orgDirector', $adb->query_result($result,0,'director'));
			} else {
			    $model->set('orgDirector', str_repeat('_', 15));
			}
			if($adb->query_result($result,0,'bookkeeper')) {
			    $model->set('orgBookkeeper', $adb->query_result($result,0,'bookkeeper'));
			} else {
			    $model->set('orgBookkeeper', str_repeat('_', 15));
			}
			if($adb->query_result($result,0,'entrepreneur')) {
			    $model->set('orgEntrepreneur', $adb->query_result($result,0,'entrepreneur'));
			} else {
			    $model->set('orgEntrepreneur', str_repeat('_', 15));
			}
			if($adb->query_result($result,0,'entrepreneurreg')) {
			    $model->set('orgEntrepreneurreg', $adb->query_result($result,0,'entrepreneurreg'));
			} else {
			    $model->set('orgEntrepreneurreg', str_repeat('_', 50));
			}

			$model->set('orgLogo', '<img src="test/logo/'.$resultrow['logoname'].'">');
			$model->set('orgLogoPath', 'test/logo/'.$resultrow['logoname']);
			$model->set('orgName', decode_html($resultrow['organizationname']));
		}

		$model->set('billingAddress', $this->buildHeaderBillingAddress());
		$model->set('shippingAddress', $this->buildHeaderShippingAddress());

		return $model;
	}

	function focusColumnValues($names, $delimeter="\n") {
		if(!is_array($names)) {
			$names = array($names);
		}
		$values = array();
		foreach($names as $name) {
			$value = $this->focusColumnValue($name, false);
			if($value !== false) {
				$values[] = $value;
			}
		}
		return $this->joinValues($values, $delimeter);
	}

        function focusColumnValue($key, $defvalue='') {
		$focus = $this->focus;
		if(isset($focus->column_fields[$key])) {
			return $focus->column_fields[$key];
		}
		return $defvalue;
	}

        function joinValues($values, $delimeter= "\n") {
		$valueString = '';
		foreach($values as $value) {
			if(empty($value)) continue;
			$valueString .= $value . $delimeter;
		}
		return rtrim($valueString, $delimeter);
	}

	function formatNumber($value, $decimal=3) {
		return number_format($value, $decimal, ',', ' ');
	}

	function formatPrice($value, $decimal=2) {
		return number_format($value, $decimal, ',', ' ');
	}

	function formatDate($value) {
		return getDisplayDate($value);
	}

      /**
       * Сумма прописью
       * @author runcore
       */
      function num2str($inn, $stripkop=false) {
        $nol = 'ноль';
        $str[100]= array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот', 'восемьсот','девятьсот');
        $str[11] = array('','десять','одиннадцать','двенадцать','тринадцать', 'четырнадцать','пятнадцать','шестнадцать','семнадцать', 'восемнадцать','девятнадцать','двадцать');
        $str[10] = array('','десять','двадцать','тридцать','сорок','пятьдесят', 'шестьдесят','семьдесят','восемьдесят','девяносто');
        $sex = array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),// m
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять') // f
        );
        $forms = array(
           array('копейка', 'копейки', 'копеек', 1), // 10^-2
           array('рубль', 'рубля', 'рублей',  0), // 10^ 0
           array('тысяча', 'тысячи', 'тысяч', 1), // 10^ 3
           array('миллион', 'миллиона', 'миллионов',  0), // 10^ 6
           array('миллиард', 'миллиарда', 'миллиардов',  0), // 10^ 9
           array('триллион', 'триллиона', 'триллионов',  0), // 10^12
       );
       $out = $tmp = array();
       // Поехали!
       $tmp = explode('.', str_replace(',','.', $inn));
       $rub = number_format($tmp[ 0], 0,'','-');
       if ($rub== 0) $out[] = $nol;
       // нормализация копеек
       $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0,2) : '00';
       $segments = explode('-', $rub);
       $offset = sizeof($segments);
       if ((int)$rub== 0) { // если 0 рублей
           $o[] = $nol;
           $o[] = $this->morph( 0, $forms[1][ 0],$forms[1][1],$forms[1][2]);
       }
       else {
           foreach ($segments as $k=>$lev) {
               $sexi= (int) $forms[$offset][3]; // определяем род
               $ri = (int) $lev; // текущий сегмент
               if ($ri== 0 && $offset>1) {// если сегмент==0 & не последний уровень(там Units)
                   $offset--;
                   continue;
               }
               // нормализация
               $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
               // получаем циферки для анализа
               $r1 = (int)substr($ri, 0,1); //первая цифра
               $r2 = (int)substr($ri,1,1); //вторая
               $r3 = (int)substr($ri,2,1); //третья
               $r22= (int)$r2.$r3; //вторая и третья
               // разгребаем порядки
               if ($ri>99) $o[] = $str[100][$r1]; // Сотни
               if ($r22>20) {// >20
                   $o[] = $str[10][$r2];
                   $o[] = $sex[ $sexi ][$r3];
               }
               else { // <=20
                   if ($r22>9) $o[] = $str[11][$r22-9]; // 10-20
                   elseif($r22> 0) $o[] = $sex[ $sexi ][$r3]; // 1-9
               }
               // Рубли
               $o[] = $this->morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
               $offset--;
           }
       }
       // Копейки
       if (!$stripkop) {
           $o[] = $kop;
           $o[] = $this->morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
       }
       return $this->rus_ucfirst(preg_replace("/\s{2,}/",' ',implode(' ',$o)));
       
   }
   
   function rus_ucfirst($string) {
   
    $tbl = array('а' => 'А',
    		 'б' => 'Б',
    		 'в' => 'В',
    		 'г' => 'Г',
    		 'д' => 'Д',
    		 'е' => 'Е',
    		 'ё' => 'Ё',
    		 'ж' => 'Ж',
    		 'з' => 'З',
    		 'и' => 'И',
    		 'й' => 'Й',
    		 'к' => 'К',
    		 'л' => 'Л',
    		 'м' => 'М',
    		 'н' => 'Н',
    		 'о' => 'О',
    		 'п' => 'П',
    		 'р' => 'Р',
    		 'с' => 'С',
    		 'т' => 'Т',
    		 'у' => 'У',
    		 'ф' => 'Ф',
    		 'х' => 'Х',
    		 'ц' => 'Ц',
    		 'ч' => 'Ч',
    		 'ш' => 'Ш',
    		 'щ' => 'Щ',
    		 'ъ' => 'Ъ',
    		 'ы' => 'Ы',
    		 'ь' => 'Ь',
    		 'э' => 'Э',
    		 'ю' => 'Ю',
    		 'я' => 'Я');
    		 
	return substr_replace($string, $tbl[substr($string,0,2)], 0, 2);
   }
    
   /**
       * Склоняем словоформу
       */
   function morph($n, $f1, $f2, $f5) {
       $n = abs($n) % 100;
       $n1= $n % 10;
       if ($n>10 && $n<20) return $f5;
       if ($n1>1 && $n1<5) return $f2;
       if ($n1==1) return $f1;
       return $f5;
   }

    function russianDate($date){
	$date=explode("-", $date);
	switch ($date[1]){
	    case 1: $m='Января'; break;
	    case 2: $m='Февраля'; break;
	    case 3: $m='Марта'; break;
	    case 4: $m='Апреля'; break;
	    case 5: $m='Мая'; break;
	    case 6: $m='Июня'; break;
	    case 7: $m='Июля'; break;
	    case 8: $m='Августа'; break;
	    case 9: $m='Сентября'; break;
	    case 10: $m='Октября'; break;
	    case 11: $m='Ноября'; break;
	    case 12: $m='Декабря'; break;
	}
	
	return $date[2].' '.$m.' '.$date[0].' г.';
    }

    function shortDate($date){
	$date=explode("-", $date);
	return $date[2].'.'.$date[1].'.'.$date[0];
    }

    protected function generateEntityModel($entity, $module, $prefix, $model) {
	// Get only active field information
        $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
    
	if($cachedModuleFields) {
	    foreach($cachedModuleFields as $fieldname=>$fieldinfo) {
		$fieldname = $fieldinfo['fieldname'];
		$type = explode('~', $fieldinfo['typeofdata']);
		switch($type[0]) {
		case 'N':
		case 'NN': $model->set($prefix.$fieldname, $this->formatPrice($entity->column_fields[$fieldname]));
			   break;
		case 'D': $model->set($prefix.$fieldname, $this->russianDate($entity->column_fields[$fieldname]));
			  $model->set($prefix.$fieldname.'_short', $this->shortDate($entity->column_fields[$fieldname]));
			  break;
		case 'C': if($entity->column_fields[$fieldname] == 0) {
			    $model->set($prefix.$fieldname, 'Нет');
			  } else {
			    $model->set($prefix.$fieldname, 'Да');
			  }
			  break;
		case 'V':  $model->set($prefix.$fieldname, nl2br($entity->column_fields[$fieldname]));
                           $model->set($prefix.$fieldname.'_translated', nl2br(getTranslatedString($entity->column_fields[$fieldname], $module)));
			   break;
		default: $model->set($prefix.$fieldname, $entity->column_fields[$fieldname]);
			   break;
		}
	    }
	}
    }

    function buildContentLabelModel() {
            $labelModel = new Vtiger_PDF_Model();
            return $labelModel;
}

    function buildFooterLabelModel() {
            $labelModel = new Vtiger_PDF_Model();
            return $labelModel;
    }

    function buildPagerModel() {
            $footerModel = new Vtiger_PDF_Model();
            $footerModel->set('format', '-%s-');
            return $footerModel;
    }

    function getWatermarkContent() {
            return '';
    }

    function buildWatermarkModel() {
            $watermarkModel = new Vtiger_PDF_Model();
            $watermarkModel->set('content', $this->getWatermarkContent());
            return $watermarkModel;
    }

    function buildHeaderBillingAddress() {
            return $this->focusColumnValues(array('bill_code','bill_country','bill_city','bill_street','bill_pobox'), ', ');
    }

    function buildHeaderShippingAddress() {
            return $this->focusColumnValues(array('bill_code','bill_country','bill_city','bill_street','bill_pobox'), ', ');
    }

    function buildCurrencySymbol() {
            global $adb;
            $currencyId = $this->focus->column_fields['currency_id'];
            if(!empty($currencyId)) {
                    $result = $adb->pquery("SELECT currency_symbol FROM vtiger_currency_info WHERE id=?", array($currencyId));
                    return decode_html($adb->query_result($result,0,'currency_symbol'));
            }
            return false;
    }
}
?>