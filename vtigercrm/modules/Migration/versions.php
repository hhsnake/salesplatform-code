<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
********************************************************************************/

//This array contains all the versions to which are all we are providing migration
//For each and every release we have to add the previous release version in this array

$versions = Array(
	"510"=>"5.1.0",
	'520rc'=>'5.2.0 RC',
	'520'=>'5.2.0',
	'521'=>'5.2.1',
    '521-20110411'=>'5.2.1-20110411',
    '521-20110506'=>'5.2.1-20110506',
    '521-20110624'=>'5.2.1-20110624',
    '521-20110824'=>'5.2.1-20110824',
    '530-201112'=>'5.3.0-201112',
    '530-201207'=>'5.3.0-201207',
    '540-201208'=>'5.4.0-201208',
    '540-201211'=>'5.4.0-201211',
    '540-201302'=>'5.4.0-201302',
    '540-201308'=>'5.4.0-201308',
);

$versions_branch2 = Array(
                        '530rc'=>'5.3.0 RC',
                        '530'=>'5.3.0',
                        '530-201112'=>'5.3.0-201112',
                        '530-201207'=>'5.3.0-201207',
                        '540-201208'=>'5.4.0-201208',
                        '540-201211'=>'5.4.0-201211',
                        '540-201302'=>'5.4.0-201302',
                        '540-201308'=>'5.4.0-201308',
		 );

$versions_branch3 = Array(
                        '540rc'=>'5.4.0 RC',
                        '540'=>'5.4.0',
                        '540-201208'=>'5.4.0-201208',
                        '540-201211'=>'5.4.0-201211',
                        '540-201302'=>'5.4.0-201302',
                        '540-201308'=>'5.4.0-201308',
		 );

$current_version = '540-201310';

?>
