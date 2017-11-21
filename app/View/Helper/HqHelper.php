<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppHelper', 'View/Helper');
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class HqHelper extends Helper {

    public $helpers = array('Session');

    function storeTimeZoneUserMerchant($timezoneId = null, $dateToconvert = null, $store_id = null) {
        if (!$this->Session->check('Auth.User.role_id')) {
            //return $dateToconvert;
        }

        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $storeId = $store_id;
        $timezone = date_default_timezone_get(); //get server time zone            
        $dtz = new DateTimeZone($timezone);
        if (empty($timezoneId)) {
            $time = new DateTime('now', $dtz);
            $diffInSeconds = $dtz->getOffset($time);
            $timezonedetail = $this->TimeZone->getTimezoneId($diffInSeconds); // get server time zone id
            if (!empty($timezonedetail)) {
                $timezoneId = $timezonedetail['TimeZone']['id'];
            } else {
                $timezoneId = 7;
            }
        }

        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));
        if ($storeInfo['Store']['time_zone_id'] != 0 || $storeInfo['Store']['time_zone_id'] != '') {
            $storeTimezoneInfo = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $timezoneId), 'fields' => array('TimeZone.difference_in_seconds'), 'recursive' => -1));
            $storefronttimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $storeInfo['Store']['time_zone_id']), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));
            $servertime = date("d-m-Y h:i:s A");
            date_default_timezone_set("GMT");
            $gmtTime = date("d-m-Y h:i:s A");
            $diff1 = (strtotime($gmtTime) - strtotime($servertime));
            date_default_timezone_set($storefronttimezone['TimeZone']['code']);
            $requiredTime = date("d-m-Y h:i:s A");
            //$requiredTime=date("d-m-Y h:i:s A",strtotime($gmtTime)+$storefronttimezone['TimeZone']['difference_in_seconds']);
            $diff2 = (strtotime($requiredTime) - strtotime($gmtTime));
            date_default_timezone_set($timezone);
            $dateToconvert = str_replace('/', '-', $dateToconvert);
            $dateToconvert = date_format(new DateTime($dateToconvert), "Y-m-d h:i:s A");
            $add = ($diff1) + ($diff2);
            $var = strtotime($dateToconvert) + $add;


            if ($storeInfo['Store']['dst'] == 1) {
                $dateToconvert = date("Y-m-d H:i:s", $var);
            } else {
                $dateToconvert = date("Y-m-d H:i:s", $var);
            }

            if ($storeInfo['Store']['time_formate'] == 1) {
                $dateToconvert = date("Y-m-d h:i:s a", $var);
            }
        }
        return $dateToconvert;
    }

    public function getStoreDetails($store_id) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $store_data = $this->Store->find('first', array('fields' => array('Store.store_name'), 'conditions' => array('Store.id' => $store_id)));
        return $store_data;
    }

    function storeTimezone($timezoneId = null, $dateToconvert = null, $type = null, $storeId = null) {
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $timezone = date_default_timezone_get(); //get server time zone            
        $dtz = new DateTimeZone($timezone);

        if (empty($timezoneId)) {
            $time = new DateTime('now', $dtz);
            $diffInSeconds = $dtz->getOffset($time);
            $timezonedetail = $this->TimeZone->getTimezoneId($diffInSeconds); // get server time zone id
            if (!empty($timezonedetail)) {
                $timezoneId = $timezonedetail['TimeZone']['id'];
            } else {
                $timezoneId = 7;
            }
        }

        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));

        if ($storeInfo['Store']['time_zone_id'] != 0 || $storeInfo['Store']['time_zone_id'] != '') {
            $storeTimezoneInfo = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $timezoneId), 'fields' => array('TimeZone.difference_in_seconds'), 'recursive' => -1));
            $storeadmintimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $storeInfo['Store']['time_zone_id']), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));

            $servertime = date("d-m-Y h:i:s A");
            date_default_timezone_set("GMT");
            $gmtTime = date("d-m-Y h:i:s A");
            $diff1 = (strtotime($gmtTime) - strtotime($servertime));
            //$requiredTime=date("d-m-Y h:i:s A",strtotime($gmtTime)+$storeadmintimezone['TimeZone']['difference_in_seconds']);
            date_default_timezone_set($storeadmintimezone['TimeZone']['code']);
            $requiredTime = date("d-m-Y h:i:s A");
            $diff2 = (strtotime($requiredTime) - strtotime($gmtTime));
            date_default_timezone_set($timezone);
            $dateToconvert = str_replace('/', '-', $dateToconvert);
            $dateToconvert = date_format(new DateTime($dateToconvert), "d-m-Y h:i:s A");
            $add = ($diff1) + ($diff2);
            $var = strtotime($dateToconvert) + $add;

            //$gmtDiff=(strtotime(gmdate("H:i:s"))-strtotime(date("H:i:s")));
            if (!empty($type)) {
                if ($storeInfo['Store']['dst'] == 1) {
                    $dateToconvert = date("H:i:s", $var);
                } else {
                    $dateToconvert = date("H:i:s", $var);
                }
            } else {
                if ($storeInfo['Store']['dst'] == 1) {
                    $dateToconvert = date("Y-m-d H:i:s", $var);
                } else {
                    $dateToconvert = date("Y-m-d H:i:s", $var);
                }
            }


            if ($storeInfo['Store']['time_formate'] == 1 && $type != '') {
                $dateToconvert = date("h:i:s a", $var);
            } else if ($storeInfo['Store']['time_formate'] == 0 && $type != '') {
                $dateToconvert = date("H:i:s", $var);
            } else if ($storeInfo['Store']['time_formate'] == 1) {
                $dateToconvert = date("Y-m-d h:i:s a", $var);
            }
        }
        return $dateToconvert;
    }

    function storeTimeFormate($timeToconvert = null, $withDate = null, $storeId = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate')));
        if ($withDate) {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("m-d-Y h:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("m-d-Y H:i", (strtotime($timeToconvert)));
            }
        } else {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("h:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("H:i", (strtotime($timeToconvert)));
            }
        }
        return $timeToconvert;
    }

    function getIntervalName($intervalid = null) {
        App::import('Model', 'Interval');
        $this->Interval = new Interval();
        $intervalName = $this->Interval->getIntervalName($intervalid);
        return $intervalName;
    }

    public function getaddonSize($sizeid = null, $storeId = null) {
        App::import('Model', 'AddonSize');
        $this->AddonSize = new AddonSize();
        $Sizedetail = $this->AddonSize->getAddonSize($sizeid, $storeId);
        return $Sizedetail;
    }

    //get HQ stores

    function getHQStores($merchantId = null) {
        if ($merchantId) {
            App::import('Model', 'Store');
            $this->Store = new Store();
            $merchantList = $this->Store->getMerchantStores($merchantId);
            return $merchantList;
        }
    }

}
