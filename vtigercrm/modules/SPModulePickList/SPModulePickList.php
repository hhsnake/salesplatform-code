<?php
/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.                                                              
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPModulePickList {

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {
        $modulename = 'SPModulePickList';
        
        $register = false; 

        if($event_type == 'module.postinstall') {
            $register = true;
        } else if($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
            $register = false;
        } else if($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled
            $register = true;
        } else if($event_type == 'module.preuninstall') {
            return;
        } else if($event_type == 'module.preupdate') {
            return;
        } else if($event_type == 'module.postupdate') {
            return;
        }

        global $adb;
        if ($register) {
            // Create main component table
            $adb->pquery(
                    "CREATE TABLE IF NOT EXISTS `sp_module_picklist_fields_rel` (
                        `fieldid` int(19) NOT NULL,
                        `srcfieldid` int(19) NOT NULL,
                        KEY `fieldid_idx` (`fieldid`),
                        KEY `srcfieldid_idx` (`srcfieldid`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;", array());
                
            $tabid = getTabid($modulename);
            
            // Add JQuery CSS
            $jquery_css = array('include/SalesPlatform/jQueryUI/themes/base/jquery.ui.all.css');
            foreach ($jquery_css as $css) {
                $linkid = $adb->getUniqueId('vtiger_links');
                $adb->pquery("INSERT INTO vtiger_links (linkid, tabid, linktype, linklabel, linkurl)
                        VALUES (?,?,?,?,?)", array($linkid, $tabid, 'HEADERCSS', $modulename, $css));
            }
            // Add JQuery JS
            $jquery_js = array('include/SalesPlatform/jQueryUI/jquery-1.7.2.min.js',
                'include/SalesPlatform/jQueryUI/jquery.ui.core.min.js',
                'include/SalesPlatform/jQueryUI/jquery.ui.widget.min.js',
                'include/SalesPlatform/jQueryUI/jquery.ui.position.min.js',
                'include/SalesPlatform/jQueryUI/jquery.ui.autocomplete.min.js',
                'include/SalesPlatform/jQueryUI/jquery.noconflict.js');
            foreach ($jquery_js as $js) {
                $linkid = $adb->getUniqueId('vtiger_links');
                $adb->pquery("INSERT INTO vtiger_links (linkid, tabid, linktype, linklabel, linkurl)
                        VALUES (?,?,?,?,?)", array($linkid, $tabid, 'HEADERSCRIPT', $modulename, $js));
            }
        } else {
            $adb->pquery("DELETE FROM vtiger_links WHERE linklabel=?", array($modulename));
        }
        
	global $__cache_module_activeinfo;
	unset($__cache_module_activeinfo[$modulename]);
    }
}

?>