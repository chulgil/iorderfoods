<?php

App::uses('AppModel', 'Model');

class IntervalPrice extends AppModel {

    var $name = 'IntervalPrice';

    /* ------------------------------------------------
      Function name:saveIntervalPrice()
      Description:To Save Interval days Information
      created:11/02/2016
      ----------------------------------------------------- */

    public function saveIntervalPrice($intervalPrice = null) {
        if ($intervalPrice) {
            if ($this->save($intervalPrice)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:saveSingleSizeIntervalPrice()
      Description:To Save Interval days Information
      Parameter : intervalPrice and size are array
      created:11/02/2016
      ----------------------------------------------------- */

    public function saveSingleSizeIntervalPrice($intervalPriceArray = null, $sizeId = null, $itemId = null, $storeId = null, $temp = null) {
        $flag = false;
        foreach ($intervalPriceArray['Price'] as $priceKey => $priceValue) {
            if (!empty($temp) && $temp == 'All') {
                App::import('model', 'Interval');
                $attr = new Interval();
                $intervalData = $attr->find('first', array('conditions' => array('LOWER(name)' => strtolower(trim($priceKey)), 'store_id' => $storeId), 'fields' => 'id'));
                $priceKey1 = $intervalData['Interval']['id'];
            }
            $priceArray = explode(',', $priceValue);

            //if priceArray is empty
            if (!$priceArray[0]) {
                $priceArray[0] = 0;
            }

            $intervalPrice['store_id'] = $storeId;
            $intervalPrice['size_id'] = $sizeId;
            $intervalPrice['item_id'] = $itemId;
            //$intervalPrice['interval_id'] = $priceKey;
            if (!empty($temp) && $temp == 'All') {
                $intervalPrice['interval_id'] = $priceKey1;
            } else {
                $intervalPrice['interval_id'] = $priceKey;
            }
            $intervalPrice['price'] = trim($priceArray[0]);
            $intervalPrice['is_active'] = $intervalPriceArray['Status'][$priceKey];
            $intervalPrice['size_active'] = 1;
            $this->create();
            $flag = $this->save($intervalPrice);
        }
        return $flag;
    }

    /* ------------------------------------------------
      Function name:updateSingleSizeIntervalPrice()
      Description:To Save Interval days Information
      Parameter : intervalPrice and size are array
      created:11/02/2016
      ----------------------------------------------------- */

    public function updateSingleSizeIntervalPrice($intervalPriceArray = null, $sizeId = null, $itemId = null, $storeId = null) {

        // De-Active all interval Price based on itemId and storeId before updateing
        $flag = $this->deActiveMultipleIntervalPrice($itemId, $storeId);

        if ($flag) {
            foreach ($intervalPriceArray['Price'] as $priceKey => $priceValue) {
                $priceArray = explode(',', $priceValue);

                $intervalPrice = array();
                $intervalPriceId = '';

                //if priceArray is empty
                if (!$priceArray[0]) {
                    $priceArray[0] = 0;
                }

                $intervalPrice['store_id'] = $storeId;
                $intervalPrice['size_id'] = $sizeId;
                $intervalPrice['item_id'] = $itemId;
                $intervalPrice['interval_id'] = $priceKey;
                $intervalPrice['price'] = trim($priceArray[0]);
                $intervalPrice['is_active'] = $intervalPriceArray['Status'][$priceKey];
                $intervalPrice['size_active'] = 1;

                // check record is already exists or not on interval_prices table
                $intervalPriceId = $this->getIntervalPriceId($priceKey, $sizeId, $itemId, $storeId);
                if ($intervalPriceId) {
                    $intervalPrice['id'] = $intervalPriceId;
                    // if record is already exists then updates the record
                    $flag = $this->save($intervalPrice);
                } else {
                    // if record is not exists then insert the value into table
                    $this->create();
                    $flag = $this->save($intervalPrice);
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name:saveMultipleSizeIntervalPrice()
      Description:To Save Interval days Information
      Parameter : intervalPrice and size are array
      created:11/02/2016
      ----------------------------------------------------- */

    public function saveMultipleSizeIntervalPrice($intervalPriceArray = null, $sizeArray = null, $itemId = null, $storeId = null, $temp = null) {
        $flag = false;
        foreach ($intervalPriceArray['Price'] as $priceKey => $priceValue) {
            //if($intervalPriceArray['Status'][$priceKey]==1 && !empty($priceValue)) {
            if (!empty($temp) && $temp == 'All') {
                App::import('model', 'Interval');
                $attr = new Interval();
                $intervalData = $attr->find('first', array('conditions' => array('LOWER(name)' => strtolower(trim($priceKey)), 'store_id' => $storeId), 'fields' => 'id'));
                $priceKey1 = $intervalData['Interval']['id'];
            }
            $priceArray = explode(',', $priceValue);

            foreach ($sizeArray['id'] as $sizeKey => $sizeValue) {

                //if priceArray is empty
                if (!$priceArray[0]) {
                    $priceArray[0] = 0;
                }

                // If price is not set for a particular size, then set price for that size from first index of priceArray
                if (!isset($priceArray[$sizeKey])) {
                    $priceArray[$sizeKey] = $priceArray[0];
                }

                // If price is null for a particular size, then set price for that size from first index of priceArray
                if (empty($priceArray[$sizeKey])) {
                    $priceArray[$sizeKey] = $priceArray[0];
                }

                $intervalPrice['store_id'] = $storeId;
                $intervalPrice['size_id'] = $sizeValue;
                $intervalPrice['item_id'] = $itemId;
                if (!empty($temp) && $temp == 'All') {
                    $intervalPrice['interval_id'] = $priceKey1;
                } else {
                    $intervalPrice['interval_id'] = $priceKey;
                }
                $intervalPrice['price'] = trim($priceArray[$sizeKey]);
                $intervalPrice['is_active'] = $intervalPriceArray['Status'][$priceKey];
                $intervalPrice['size_active'] = 1;
                $this->create();
                $flag = $this->save($intervalPrice);
            }
            //}
        }
        return $flag;
    }

    /* ------------------------------------------------
      Function name:updateMultipleIntervalPrice()
      Description:To Save Interval days Information
      Parameter : intervalPrice and size are array
      created:11/02/2016
      ----------------------------------------------------- */

    public function updateMultipleSizeIntervalPrice($intervalPriceArray = null, $sizeArray = null, $itemId = null, $storeId = null) {

        // De-Active all interval Price based on itemId and storeId before updateing
        $flag = $this->deActiveMultipleIntervalPrice($itemId, $storeId);

        if ($flag) {
            foreach ($intervalPriceArray['Price'] as $priceKey => $priceValue) {
                $priceArray = explode(',', $priceValue);
                foreach ($sizeArray['id'] as $sizeKey => $sizeValue) {
                    $intervalPrice = array();
                    $intervalPriceId = '';

                    //if priceArray is empty
                    if (!$priceArray[0]) {
                        $priceArray[0] = 0;
                    }

                    // If price is not set for a particular size, then set price for that size from first index of priceArray
                    if (!isset($priceArray[$sizeKey])) {
                        $priceArray[$sizeKey] = $priceArray[0];
                    }

                    // If price is null for a particular size, then set price for that size from first index of priceArray
                    if (empty($priceArray[$sizeKey])) {
                        $priceArray[$sizeKey] = $priceArray[0];
                    }

                    $intervalPrice['store_id'] = $storeId;
                    $intervalPrice['size_id'] = $sizeValue;
                    $intervalPrice['item_id'] = $itemId;
                    $intervalPrice['interval_id'] = $priceKey;
                    $intervalPrice['price'] = trim($priceArray[$sizeKey]);
                    $intervalPrice['is_active'] = $intervalPriceArray['Status'][$priceKey];
                    $intervalPrice['size_active'] = 1;

                    // check record is already exists or not on interval_prices table
                    $intervalPriceId = $this->getIntervalPriceId($priceKey, $sizeValue, $itemId, $storeId);
                    if ($intervalPriceId) {
                        $intervalPrice['id'] = $intervalPriceId;
                        // if record is already exists then updates the record
                        $flag = $this->save($intervalPrice);
                    } else {
                        // if record is not exists then insert the value into table
                        $this->create();
                        $flag = $this->save($intervalPrice);
                    }
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name:updateMultipleIntervalPrice()
      Description:De-Active all interval Price based on itemId and storeId
      Parameter : intervalPrice and size are array
      created:11/02/2016
      ----------------------------------------------------- */

    public function deActiveMultipleIntervalPrice($itemId = null, $storeId = null) {
        if (!empty($itemId) && !empty($storeId)) {
            if ($this->updateAll(array('IntervalPrice.size_active' => 0, 'IntervalPrice.is_active' => 0), array('IntervalPrice.item_id' => $itemId, 'IntervalPrice.store_id' => $storeId))) {
                return true;
            }
        }
        return false;
    }

    /* ------------------------------------------------
      Function name:getIntervalPriceId()
      Description: Return itervalPriceId based on intervalId, sizeId, itemId and storeId
      Parameter : intervalPrice and size are array
      created:11/02/2016
      ----------------------------------------------------- */

    public function getIntervalPriceId($intervalId = null, $sizeId = null, $itemId = null, $storeId = null) {
        if (!empty($intervalId) && !empty($itemId) && !empty($storeId)) {
            $intervalPriceId = $this->find('first', array('fields' => array('IntervalPrice.id'), 'conditions' => array('IntervalPrice.item_id' => $itemId, 'IntervalPrice.store_id' => $storeId, 'IntervalPrice.size_id' => $sizeId, 'IntervalPrice.interval_id' => $intervalId), 'recursiv' => -1));
            if (!empty($intervalPriceId)) {
                return $intervalPriceId = $intervalPriceId['IntervalPrice']['id'];
            }
        }
        return false;
    }

    /* ------------------------------------------------
      Function name:getAllInvervalPrices()
      Description: to get all Interval based on storeId and itemId
      created:9/2/2016
      ----------------------------------------------------- */

    public function getAllInvervalPrices($storeId = null, $itemId = null) {
        $intervalPriceList = $this->find('all', array('conditions' => array('IntervalPrice.store_id' => $storeId, 'IntervalPrice.item_id' => $itemId), 'order' => array('IntervalPrice.created ASC')));
        if ($intervalPriceList) {
            return $intervalPriceList;
        } else {
            return false;
        }
    }

}
