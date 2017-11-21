<?php

App::uses('AppModel', 'Model');

class StoreHoliday extends AppModel {

    var $name = 'StoreHoliday';

    /* ------------------------------------------------
      Function name:saveStoreHolidayInfo()
      Description:save Store holiday Information
      created:29/7/2015
      ----------------------------------------------------- */

    public function saveStoreHolidayInfo($holidayData = null) {
        if ($holidayData) {
            if ($this->save($holidayData)) {
                return true; //Success
            } else {
                return false; // Failure
            }
        }
    }

    /* ------------------------------------------------
      Function name:emailExists()
      Description:To check if email already exists.
      created:27/7/2015
      ----------------------------------------------------- */

    public function getStoreHolidayInfo($storeId = null) {
        if ($storeId) {
            $storeHolidayInfo = $this->find('all', array('conditions' => array('StoreHoliday.store_id' => $storeId, 'StoreHoliday.is_deleted' => 0)));
            return $storeHolidayInfo;
        }
    }

    /* ------------------------------------------------
      Function name:emailExists()
      Description:To find holiday list
      created:27/7/2015
      ----------------------------------------------------- */

    public function getStoreHolidaylist($storeId = null) {
        if ($storeId) {
            $storeHolidayInfo = $this->find('all', array('conditions' => array('StoreHoliday.store_id' => $storeId, 'StoreHoliday.is_deleted' => 0), 'fields' => array('holiday_date', 'description')));
            return $storeHolidayInfo;
        }
    }

    public function getStoreHolidaylistDate($storeId = null, $current_date = null) {
        $storeHolidayInfo = $this->find('first', array('conditions' => array('StoreHoliday.store_id' => $storeId, 'StoreHoliday.holiday_date' => $current_date, 'StoreHoliday.is_deleted' => 0), 'fields' => array('holiday_date', 'description')));
        return $storeHolidayInfo;
    }

    /* ------------------------------------------------
      Function name:emailExists()
      Description:To check if email already exists.
      created:27/7/2015
      ----------------------------------------------------- */

    public function storeHolidayNotExists($holidayDate = null, $storeId = null) {
        if ($holidayDate) {
            $dateExists = $this->find('first', array('conditions' => array('StoreHoliday.store_id' => $storeId, 'StoreHoliday.holiday_date' => $holidayDate, 'StoreHoliday.is_deleted' => 0)));
            if ($dateExists) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function getholidaydate($storeId = null, $currentdate = null, $end_date = null) {
        if ($storeId) {
            $condition = 'StoreHoliday.store_id = ' . $storeId . ' AND StoreHoliday.is_deleted = 0 AND StoreHoliday.is_active = 1 AND date(StoreHoliday.holiday_date)>="' . $currentdate . '" AND date(StoreHoliday.holiday_date)<="' . $end_date . '"';

            $storeHolidayInfo = $this->find('list', array('fields' => array('holiday_date'), 'conditions' => array($condition)));
            return $storeHolidayInfo;
        }
    }

}
