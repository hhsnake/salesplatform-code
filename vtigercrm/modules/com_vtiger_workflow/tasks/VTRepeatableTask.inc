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

require_once('modules/com_vtiger_workflow/tasks/VTCreateEventTask.inc');

abstract class VTRepeatableTask extends VTTask {
	public $executeImmediately = false;
        
	public $executeRepeatedly = true;

        public $nextTime;

        /**
         * Call this method in first line of your doTask() method
         *
         * public function doTask($wfEntity) {
         *     parent::doTask($wfEntity);
         *
         *     // TODO your code here
         * }
         * 
         */
	public function doTask($wfEntity) {
                $trigger = $this->trigger;
                if ($this->active && ($trigger['period'] != null) && ($trigger['time'] != null)) {
                    global $adb;
                    $taskQueue = new VTTaskQueue($adb);
                    $this->nextTime = VTRepeatableTask::calcNextTime($trigger['time'], $trigger['period']);
                    $taskQueue->queueTask($this->id, $wfEntity->getId(), $this->nextTime);
                }
        }

        /**
         * Calculate the next execution Task time
         *
         * @param <string> $startTime - start time in 2:45pm format
         * @param <int> $period - period in mins
         * @return <int> - next execution timestamp time
         */
        public static function calcNextTime($startTime, $period) {
                $currentTime = time();
                $secPeriod = $period * 60;
                $startTime = strtotime(VTCreateEventTask::conv12to24hour($startTime));
                $wholePeriodsCount = floor(($currentTime - $startTime) / $secPeriod);
                $nextTime = ($startTime + ($secPeriod * ($wholePeriodsCount + 1)));
                return $nextTime;
        }
}
?>
