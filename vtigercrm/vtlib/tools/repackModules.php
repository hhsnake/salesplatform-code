<?php

chdir(dirname(__FILE__) . '/../..');
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Package.php';
include_once 'includes/main/WebUI.php';

include_once 'include/Webservices/Utils.php';


$repacker = new SPRepackModules();
$repacker->repack();


class SPRepackModules {
    
    private $skipModules = ['Dashboard', 'Events', 'Webmails'];
    private $projectsBundle = ['Project', 'ProjectMilestone', 'ProjectTask'];
    
    public function repack() {
        SPRepackLogger::log("Running repack all modules");
        $package = new Vtiger_Package();
        foreach(Vtiger_Module_Model::getAll() as $module) {
            if($this->isRepackableModule($module->getName())) {
                SPRepackLogger::log("Start repack " . $module->getName() . " module");
                $packagePath = $this->searchPackagePath($module->getName());
                if($packagePath != null) {
                    if(file_exists($packagePath)) {
                        unset($packagePath);
                    }
                    
                    $package->export(
                        $module, 
                        $packagePath,
                        $this->getPackageName($module->getName())
                    );
                } else {
                    SPRepackLogger::log("Not found zip package of module " . $module->getName() . ". No need repack");
                }
                
                SPRepackLogger::log("Repack of " . $module->getName() . " finished");
            }
        }
        
        SPRepackLogger::log("Start repack special modules");
        $this->repackSpecialModules();
        SPRepackLogger::log("End repack special modules");
    }
    
    private function repackSpecialModules() {
        SPRepackLogger::log("Start Projects bundle repack");
        
        $package = new Vtiger_Package();
        $zip = new ZipArchive();
        $status = $zip->open('packages/vtiger/optional/Projects.zip');
        
        if(!$status) {
            SPRepackLogger::log("Cannot open Projects bundle to repack. End Projects repack");
            return;
        }
        

        foreach($this->projectsBundle as $moduleName) {
            SPRepackLogger::log("Repacking " . $moduleName . " in Projects bundle");
            
            $module = Vtiger_Module_Model::getInstance($moduleName);
            $packagePath = $this->searchPackagePath($moduleName);
            if($packagePath == null) {
                $packagePath = 'packages/vtiger/optional/' . $this->getPackageName($moduleName);
            }

            if(file_exists($packagePath)) {
                unlink($packagePath);
            }

            $package->export(
                $module, 
                $packagePath,
                $this->getPackageName($moduleName)
            );

            $zip->addFile($packagePath, $this->getPackageName($moduleName));
            
            unlink($packagePath);
        }
        
        $zip->close();
        
        SPRepackLogger::log("End Projects bundle repack");
    }
    
    private function searchPackagePath($moduleName) {
        foreach($this->getPackagesFolders() as $folderName) {
            $folderPackages = scandir($folderName);
            foreach($folderPackages as $fileName) {
                if($this->isPackageMatch($moduleName, $fileName)) {
                    return $folderName . "/" . $this->getPackageName($moduleName);
                }
            }
        }
        
        return null;
    }
    
    private function getPackageName($moduleName) {
        return $moduleName . '.zip';
    }
    
    private function getPackagesFolders() {
        return [
            'packages/vtiger/mandatory',
            'packages/vtiger/optional',
            'packages/vtiger/marketplace'
        ];
    }
    
    private function isPackageMatch($moduleName, $fileName) {
        return ($this->getPackageName($moduleName) === $fileName);
    }
    
    private function isRepackableModule($moduleName) {
        return !in_array($moduleName, $this->skipModules);
    }
    
}


class SPRepackLogger {
    
    public static function log($message) {
        echo $message . "\n";
    }
}