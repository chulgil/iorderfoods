<?php

App::uses('AppModel', 'Model');

class StoreAvailability extends AppModel {

    var $name = 'StoreAvailability';

    /* ------------------------------------------------
      Function name:saveStoreHolidayInfo()
      Description:save Store holiday Information
      created:29/7/2015
      ----------------------------------------------------- */

    public function saveStoreAvailabilityInfo($availabilityData = null) {
        if ($availabilityData) {
            if ($this->save($availabilityData)) {
                return true; //Success
            } else {
                return false; // Failure
            }
        }
    }

    /* ------------------------------------------------
      Function name:getStoreAvailabilityInfo()
      Description:To get list of Atore Availability id's
      created:27/7/2015
      ----------------------------------------------------- */

    public function getStoreAvailabilityInfo($storeId = null) {
        if ($storeId) {
            $storeavailabilityInfo = $this->find('list', array('conditions' => array('StoreAvailability.store_id' => $storeId, 'StoreAvailability.is_deleted' => 0)));
            return $storeavailabilityInfo;
        }
    }

    /* ------------------------------------------------
      Function name:getStoreAvailabilityDetails()
      Description:To get details of Store Availability
      created:27/7/2015
      ----------------------------------------------------- */

    public function getStoreAvailabilityDetails($storeId = null) {
        if ($storeId) {
            $storeavailabilityDetails = $this->find('all', array('conditions' => array('StoreAvailability.store_id' => $storeId, 'StoreAvailability.is_deleted' => 0)));
            return $storeavailabilityDetails;
        }
    }

    /* ------------------------------------------------
      Function name:getStoreNotAvailableInfo()
      Description:To check if email already exists.
      created:27/7/2015
      ----------------------------------------------------- */

    public function getStoreNotAvailableInfo($storeId = null) {
        if ($storeId) {
            $store_closedDay = $this->find('all', array('fields' => array('day_name', 'start_time', 'end_time'), 'conditions' => array('StoreAvailability.store_id' => $storeId, 'StoreAvailability.is_deleted' => 0, 'StoreAvailability.is_closed' => 0)));
            return $store_closedDay;
        }
    }

    public function getStoreNotAvailableInfoDay($storeId = null, $currentDay = null) {
        $store_closedDay = $this->find('first', array('fields' => array('id', 'day_name', 'start_time', 'end_time'), 'conditions' => array('StoreAvailability.store_id' => $storeId, 'StoreAvailability.day_name' => $currentDay, 'StoreAvailability.is_deleted' => 0, 'StoreAvailability.is_closed' => 0)));
        return $store_closedDay;
    }

    /* ------------------------------------------------
      Function name:getStoreNotAvailableInfo()
      Description:To check if email already exists.
      created:27/7/2015
      ----------------------------------------------------- */

    public function getStoreInfoForDay($selected_day = null, $storeId = null) {
        if ($storeId) {
            $store_closedDay = $this->find('first', array('fields' => array('id', 'day_name', 'start_time', 'end_time'), 'conditions' => array('StoreAvailability.store_id' => $storeId, 'StoreAvailability.day_name' => $selected_day, 'StoreAvailability.is_deleted' => 0, 'StoreAvailability.is_active' => 1, 'StoreAvailability.is_closed' => 0)));
            return $store_closedDay;
        }
    }

    public function getclosedDay($storeId = null) {
        if ($storeId) {
            $store_closedDay = $this->find('all', array('fields' => array('day_name', 'start_time', 'end_time'), 'conditions' => array('StoreAvailability.store_id' => $storeId, 'StoreAvailability.is_deleted' => 0, 'StoreAvailability.is_closed' => 1)));
            return $store_closedDay;
        }
    }

}
