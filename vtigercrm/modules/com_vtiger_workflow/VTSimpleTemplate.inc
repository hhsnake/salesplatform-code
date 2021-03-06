<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class VTSimpleTemplate{
	
	function __construct($templateString){
		$this->template = $templateString;
	}
	
	function render($entityCache, $entityId){
		$this->cache = $entityCache;
		$this->parent = $this->cache->forId($entityId);
		return $this->parseTemplate();
	}
	
	private function matchHandler($match){
		preg_match('/\((\w+) : \(([_\w]+)\) (\w+)\)/', $match[1], $matches);
		if(count($matches)==0){
			$fieldname = $match[1];
			$data = $this->parent->getData();
			if($this->useValue($data, $fieldname)){
				// SalesPlatform.ru begin
				//$result = $data[$fieldname];
				$result = getTranslatedString( $data[$fieldname], $this->parent->getModuleName());
				// SalesPlatform.ru end
			}else{
				$result ='';
                                // SalesPlatform.ru begin
                                if ($fieldname == "crmid") {
                                    $result = vtws_getCRMEntityId($this->parent->getId());
                                }
                                // SalesPlatform.ru end
			}
		}else{
			list($full, $referenceField, $referenceModule, $fieldname) = $matches;
			if($referenceModule === '__VtigerMeta__'){
				$result = $this->getMetaValue($fieldname);
			}else{
				$referenceId = $this->parent->get($referenceField);
				if($referenceId==null){
					$result="";
				}else{
					$entity = $this->cache->forId($referenceId);
					if($referenceModule==="Users" && $entity->getModuleName()=="Groups"){
						list($groupEntityId, $groupId) = vtws_getIdComponents($referenceId);
						
						require_once('include/utils/GetGroupUsers.php');
						$ggu = new GetGroupUsers();
						$ggu->getAllUsersInGroup($groupId);
						
						$users = $ggu->group_users;
						$parts = Array();
						foreach($users as $userId){
							$refId = vtws_getWebserviceEntityId("Users", $userId);
							$entity = $this->cache->forId($refId);
							$data = $entity->getData();
							if($this->useValue($data, $fieldname)){
								$parts[] = $data[$fieldname];
							}
						}
						$result = implode(",", $parts);
					}if($entity->getModuleName()===$referenceModule){
						$data = $entity->getData();
						if($this->useValue($data, $fieldname)){
							$result = $data[$fieldname];
						}else{
							$result = '';
						}
					}else{
						$result = '';
					}
				}
			}
		}
		return $result;						
		
	}

	protected function useValue($data, $fieldname) {
		return !empty($data[$fieldname]);
	}

	function parseTemplate(){
// SalesPlatform.ru begin
            return preg_replace_callback('/\\$(\w+|\((\w+) : \(([_\w]+)\) (\w+)\))/', array($this,"matchHandler"), $this->template);
//            return preg_replace_callback('/\\$(\w+|\((\w+) : \(([_\w]+)\) (\w+)\))\s*,?/', array($this,"matchHandler"), $this->template);
// SalesPlatform.ru end
	}
	
	function getMetaValue($fieldname){
		switch($fieldname){
			case 'date': return getNewDisplayDate();
			case 'time': return date('h-i-s');
			default: '';
		}
	}
}
?>
