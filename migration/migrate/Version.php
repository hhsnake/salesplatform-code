<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/


include_once 'MigrationVersion.php';

class Version {
    const BEGINNING_OF_TIME = "201002";

    protected $number;
    protected $date;

    public function __construct($versionString=null) {
        $isCorrect = false;
        if ($versionString) {
            $spVersion = explode('-', $versionString);
            if (isset($spVersion[0])) {
                $isCorrect = true;
                $this->number = $spVersion[0];
                $this->date = isset($spVersion[1]) ? $spVersion[1] : self::BEGINNING_OF_TIME;
            }
        }

        if (!$isCorrect) {
            $this->initAsLast();
        }
    }

    private function initAsLast() {
        $this->number = MigrationVersion::NUMBER;
        $this->date = MigrationVersion::DATE;
    }

    public function compare($version) {
        if (version_compare($this->number, $version->number) == 0) {
            if ($this->date > $version->date) {
                return 1;
            } else if ($this->date < $version->date) {
                return -1;
            } else {
                return 0;
            }
        } else {
            return version_compare($this->number, $version->number);
        }
    }

    public function asString() {
        return ($this->date == self::BEGINNING_OF_TIME) ?
            $this->number :
            implode("-", array($this->number, $this->date));
    }

    public function isLastVersion() {
        return ($this->number == MigrationVersion::NUMBER && $this->date == MigrationVersion::DATE);
    }
}
