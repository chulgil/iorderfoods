<?php

App::uses('Component', 'Controller');

/**
 * Custom component
 */
class HqCommonComponent extends Component {

    /**
     * This component uses the component
     *
     * @var array
     */
    var $components = array('Cookie', 'Session', 'Email');

    /* ------------------------------------------------
      Function name:getStoreTimeHq()
      Description : return time range array for admin
      created:10/2/2016
      ----------------------------------------------------- */

    function getStoreTimeHq($startTime = null, $endTime = null, $storeId = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate', 'Store.delivery_delay', 'Store.pick_up_delay', 'Store.cutoff_time')));
        $tStart = strtotime($startTime);
        $tEnd = strtotime($endTime);
        $tNow = $tStart;
        $timeRange = array();
        $i = 0;
        while ($tNow <= $tEnd) {
            if (!empty($storeInfo['Store']['time_formate'])) {
                $intervalTime = date("h:i a", $tNow);
            } else {
                $intervalTime = date("H:i", $tNow);
            }
            $intervalTimeforValue = date("H:i:s", $tNow);
            if ($intervalTime == "00:00" || $intervalTimeforValue == "00:00:00" || $intervalTime == "00:00:00") {
                $intervalTime = strtotime("23:59");
                if ($storeInfo['Store']['time_formate'] == 1) {
                    $intervalTime = date("h:i a", $intervalTime);
                } else {
                    $intervalTime = date("H:i", $intervalTime);
                }
                $intervalTimeforValue = "23:59:00";
            }
            $timeRange[$intervalTimeforValue] = $intervalTime;
            $tNow = $tNow + 300;
            $i++;
        }
        return $timeRange;
    }

    function storeToServerTimeZoneHq($dateToconvert = null, $type = null, $storeId = null) {
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $timezone = date_default_timezone_get(); //get server time zone 
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));
        if (!empty($storeInfo)) {
            $storeadmintimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $storeInfo['Store']['time_zone_id']), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));
            date_default_timezone_set("GMT");
            $gmtTime = date("d-m-Y h:i:s A");
            $diff1 = strtotime($gmtTime) + $storeadmintimezone['TimeZone']['difference_in_seconds'];
            $StoreCurrentTime = date("d-m-Y h:i:s A", $diff1);
            $diff1 = (strtotime($gmtTime) - strtotime($StoreCurrentTime));

            date_default_timezone_set($storeadmintimezone['TimeZone']['code']);
            $requiredTime = date("d-m-Y h:i:s A");
            $diff2 = (strtotime($requiredTime) - strtotime($gmtTime));

            date_default_timezone_set($timezone);
            $dateToconvert = str_replace('/', '-', $dateToconvert);
            $dateToconvert = date_format(new DateTime($dateToconvert), "d-m-Y h:i:s A");
            $add = ($diff1) + ($diff2);
            $var = strtotime($dateToconvert) + $add;

            if ($storeInfo['Store']['dst'] == 1) {
                $dateToconvert = date("H:i:s", $var + 3600);
            } else {
                $dateToconvert = date("H:i:s", $var);
            }
        }
        return $dateToconvert;
    }

    /* ------------------------------------------------
      Function name:getStoreTimeAdmin()
      Description: return time range array for admin
      created:29/8/2016
      ----------------------------------------------------- */

    function getStoreTimeAdmin($startTime = null, $endTime = null, $storeId = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate')));
        $tStart = strtotime($startTime);
        $tEnd = strtotime($endTime);
        $tNow = $tStart;
        $timeRange = array();
        while ($tNow <= $tEnd) {
            if (!empty($storeInfo['Store']['time_formate']) && $storeInfo['Store']['time_formate'] == 1) {
                $intervalTime = date("h:i a", $tNow);
            } else {
                $intervalTime = date("H:i", $tNow);
            }
            $intervalTimeforValue = date("H:i:s", $tNow);
            if ($intervalTime == "00:00" || $intervalTimeforValue == "00:00:00" || $intervalTime == "00:00:00") {
                $intervalTime = strtotime("24:00");
                if ($storeInfo['Store']['time_formate'] == 1) {
                    $intervalTime = date("h:i a", $intervalTime);
                } else {
                    $intervalTime = date("H:i", $intervalTime);
                }
                $intervalTimeforValue = "24:00:00";
            }
            $timeRange[$intervalTimeforValue] = $intervalTime;
            $tNow = strtotime('+15 minutes', $tNow);
        }
//        if (!empty($storeInfo['Store']['time_formate']) && $storeInfo['Store']['time_formate'] == 1) {
//            $lastTime = '11:59 pm';
//        } else {
//            $lastTime = '23:59';
//        }
//        $timeRange['23:59:00'] = $lastTime;
        return $timeRange;
    }

    function sendSmsNotificationFront($toNumber = null, $message = null, $storeId = null) {
        if ($toNumber) {
            App::import('Model', 'StoreSetting');
            $this->StoreSetting = new StoreSetting();
            $storeSetting = $this->StoreSetting->findByStoreId($storeId);
            if (!empty($storeSetting['StoreSetting']['twilio_allow'])) {
                App::import('Model', 'Store');
                $this->Store = new Store();
                $settings = $this->Store->fetchStoreDetail($storeId);
                if (!empty($settings['Store']['twilio_api_key']) && !empty($settings['Store']['twilio_api_token']) && !empty($settings['Store']['twilio_number'])) {
                    $tApikey = $settings['Store']['twilio_api_key'];
                    $tApiToken = $settings['Store']['twilio_api_token'];
                    $tApiNumber = $settings['Store']['twilio_number'];
                    App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
                    $client = new Services_Twilio($tApikey, $tApiToken);
                    $client->account->messages->create(array(
                        'To' => $toNumber,
                        'From' => $tApiNumber,
                        'Body' => $message,
                    ));
                }
            }
        }
    }

    public function storeTimeZoneUser($timezoneId = null, $dateToconvert = null, $storeId = null) {
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
            $storefronttimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $storeInfo['Store']['time_zone_id']), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));

            $servertime = date("d-m-Y h:i:s A");
            date_default_timezone_set("GMT");
            $gmtTime = date("d-m-Y h:i:s A");
            $diff1 = (strtotime($gmtTime) - strtotime($servertime));
            date_default_timezone_set($storefronttimezone['TimeZone']['code']);
            $requiredTime = date("d-m-Y h:i:s A");

            $diff2 = (strtotime($requiredTime) - strtotime($gmtTime));
            date_default_timezone_set($timezone);

            $dateToconvert = str_replace('/', '-', $dateToconvert);
            $dateToconvert = date_format(new DateTime($dateToconvert), "Y-m-d h:i:s A");
            $add = ($diff1) + ($diff2);
            $var = strtotime($dateToconvert) + $add;
            $dateToconvert = date("Y-m-d H:i:s", $var);
            if ($storeInfo['Store']['time_formate'] == 1) {
                $dateToconvert = date("Y-m-d h:i:s a", $var);
            }
        }
        return $dateToconvert;
    }

    function getNextDayTimeRange($currentdate = null, $today = 0, $orderType = 1, $withinfunc = false, $decrypt_storeId = null, $decrypt_merchantId = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $date = new DateTime($currentdate);
        $current_day = $date->format('l');
        $this->Store->bindModel(
                array(
                    'hasMany' => array(
                        'StoreAvailability' => array(
                            'className' => 'StoreAvailability',
                            'foreignKey' => 'store_id',
                            'conditions' => array('StoreAvailability.day_name' => $current_day, 'StoreAvailability.is_deleted' => 0, 'StoreAvailability.is_active' => 1, 'is_closed' => 0),
                            'fields' => array('id', 'start_time', 'end_time', 'day_name')
                        )
                    )
                )
        );
        $store_data = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
        $daysdiff = 0;
        if ($withinfunc == false) {
            $currentTime = date("Y-m-d H:i:s", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s'), $decrypt_storeId))));
            $curTimearr = explode(' ', $currentTime);
            $currentDay = $curTimearr[0];
            $strCurTime = strtotime($currentTime);
            $storedelayTime = $this->Store->getDelayTime($decrypt_storeId);
            if ($orderType == 2) {
                $currentdelayTime = date('Y-m-d', strtotime('+' . $storedelayTime['Store']['pick_up_delay'] . 'minutes', $strCurTime));
            } elseif ($orderType == 3) {
                $currentdelayTime = date('Y-m-d', strtotime('+' . $storedelayTime['Store']['delivery_delay'] . 'minutes', $strCurTime));
            } else {
                $currentdelayTime = date('Y-m-d', $strCurTime);
            }
            $date1 = date_create($currentdelayTime);
            $date2 = date_create($currentDay);
            $diff = date_diff($date1, $date2);
            $daysdiff = $diff->days;
        }

        $storeHoliday = ClassRegistry::init('StoreHoliday');
        $holidayList = $storeHoliday->getStoreHolidaylistDate($decrypt_storeId, $currentdate);
        if (!empty($holidayList)) {
            $nextDate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
            $today = 0;
            $finaldata = $this->getNextDayTimeRange($nextDate, $today, $orderType, true);
            return $finaldata;
        }

        if (empty($store_data['StoreAvailability']) || $daysdiff) {
            $nextDate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
            $today = 0;
            $finaldata = $this->getNextDayTimeRange($nextDate, $today, $orderType, true);
            return $finaldata;
        } else {
            $current_array = array();
            $time_break = array();
            $storeBreak = array();
            $time_range = array();
            $finaldata = array();
            $start = $store_data['StoreAvailability'][0]['start_time'];
            $end = $store_data['StoreAvailability'][0]['end_time'];
            $cutTime = '-' . $store_data['Store']['cutoff_time'] . ' minutes';
            $end = date("H:i:s", strtotime("$cutTime", strtotime($end)));
            $start = $this->getStartTime($start, $today, $orderType, 1, $end, $decrypt_storeId);

            App::import('Model', 'StoreBreak');
            $this->StoreBreak = new StoreBreak();
            $store_break = $this->StoreBreak->fetchStoreBreak($store_data['Store']['id'], $store_data['StoreAvailability'][0]['id']);
            $storeBreakTime = $store_break;
            $start = $this->checkbreakTime($start, $store_data, $storeBreakTime, $orderType, $decrypt_storeId);

            if (!$start) {
                $currentdate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
                $today = 0;
                $finaldata = $this->getNextDayTimeRange($currentdate, $today, $orderType, true);
                return $finaldata;
            }


            $time_ranges = $this->getStoreTime($start, $end, $orderType, $store_data, $storeBreakTime, $decrypt_storeId); // calling Common Component

            $current_array = $time_ranges;
            if (empty($current_array) || !$start) {
                $currentdate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
                $today = 0;
                $finaldata = $this->getNextDayTimeRange($currentdate, $today, $orderType, true);
                return $finaldata;
            }
            $finaldata['currentdate'] = $currentdate;
            if ($store_data['Store']['is_break_time'] == 1) {
                $time_break1 = array();
                $time_break2 = array();
                if ($store_data['Store']['is_break1'] == 1) {
                    $break_start_time = $store_break['StoreBreak']['break1_start_time'];
                    $break_end_time = $store_break['StoreBreak']['break1_end_time'];
                    $storeBreak[0]['start'] = $store_break['StoreBreak']['break1_start_time'];
                    $storeBreak[0]['end'] = $store_break['StoreBreak']['break1_end_time'];
                    $time_break1 = $this->getStoreTime($break_start_time, $break_end_time, null, null, null, $decrypt_storeId);
                }
                if ($store_data['Store']['is_break2'] == 1) {
                    $break_start_time = $store_break['StoreBreak']['break2_start_time'];
                    $break_end_time = $store_break['StoreBreak']['break2_end_time'];
                    $storeBreak[1]['start'] = $store_break['StoreBreak']['break2_start_time'];
                    $storeBreak[1]['end'] = $store_break['StoreBreak']['break2_end_time'];
                    $time_break2 = $this->getStoreTime($break_start_time, $break_end_time, null, null, null, $decrypt_storeId);
                }
                $time_break = array_unique(array_merge($time_break1, $time_break2), SORT_REGULAR);
            }
            $time_range = array_diff($current_array, $time_break);
            $time_range = $current_array;
            $current_date = date("Y-m-d H:i:s", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s'), $decrypt_storeId))));
            $CurrentDateTocheck = explode(" ", $current_date);

            $avalibilty_status = $this->checkStoreAvalibility($decrypt_storeId, $CurrentDateTocheck[0], $CurrentDateTocheck[1]);

            if ($avalibilty_status != 1) {
                $setPre = 1;
            } else {
                $setPre = 0;
            }
            $finaldata['setPre'] = $setPre;
            $finaldata['time_break'] = $time_break;
            $finaldata['store_data'] = $store_data;
            $finaldata['storeBreak'] = $storeBreak;
            $finaldata['time_range'] = $time_range;
            return $finaldata;
        }
    }

    /* ---------------------------------------------
      Function name:checcheckStoreAvalibilitykAddress
      Description:To check store timing
      ----------------------------------------------- */

    function checkStoreAvalibility($strore_id = null, $selected_date = null, $booking_time = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        if (isset($strore_id)) {
            $storeHoliday = ClassRegistry::init('StoreHoliday');
            $storeAvailability = ClassRegistry::init('StoreAvailability');
            $store = ClassRegistry::init('Store');
            $storeBreak = ClassRegistry::init('StoreBreak');
            $store_data = $store->fetchStoreBreak($strore_id);
            if (!$selected_date) {
                $currentarr = explode(' ', $this->storeTimeZoneUser('', date('Y-m-d H:i:s'), $strore_id));
                $current_date = $currentarr[0];
            } else {
                $current_date = $selected_date;
            }
            $date = new DateTime($current_date);
            $current_day = $date->format('l');

            if (!$booking_time) {
                $current_time = date('H:i:s', strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s'), $strore_id)));
                $current_time = strtotime($current_time);
            } else {
                $current_time = strtotime($booking_time);
            }
            $start_time = "";
            $end_time = "";
            $description = "";

            $holidayList = $storeHoliday->getStoreHolidaylistDate($strore_id, $current_date);
            if (!empty($holidayList)) {
                $description = $holidayList['StoreHoliday']['description'];
                $store_status['status'] = "Holiday";
                $store_status['description'] = $description;
                return $store_status;
            }

            $storeAvailable = $storeAvailability->getStoreNotAvailableInfoDay($strore_id, $current_day);
            if (!empty($storeAvailable)) {
                $start_time = strtotime($storeAvailable['StoreAvailability']['start_time']);
                $end_time = strtotime($storeAvailable['StoreAvailability']['end_time']);

                $StoreCutOff = $this->Store->fetchStoreCutOff($strore_id);
                $cutTime = '-' . $StoreCutOff['Store']['cutoff_time'] . ' minutes';
                $end_time = strtotime(date("H:i:s", strtotime("$cutTime", $end_time)));

                if (($current_time > $end_time) || ($current_time < $start_time)) {
                    $store_status['status'] = "Timeoff";
                    $store_status['start_time'] = $start_time;
                    $store_status['end_time'] = $end_time;
                    return $store_status;
                } else {
                    if ($store_data['Store']['is_break_time'] == 1) {
                        $store_break = $storeBreak->fetchStoreBreak($strore_id, $storeAvailable['StoreAvailability']['id']);
                        if ($store_data['Store']['is_break1'] == 1) {
                            $start_time = strtotime($store_break['StoreBreak']['break1_start_time']);
                            $end_time = strtotime($store_break['StoreBreak']['break1_end_time']);
                            if (($current_time < $end_time) && ($current_time > $start_time)) {
                                $store_status['status'] = "BreakTime";
                                $store_status['start_time'] = $start_time;
                                $store_status['end_time'] = $end_time;
                                return $store_status;
                            } else {
                                if ($store_data['Store']['is_break2'] == 1) {
                                    $start_time = strtotime($store_break['StoreBreak']['break2_start_time']);
                                    $end_time = strtotime($store_break['StoreBreak']['break2_end_time']);
                                    if (($current_time < $end_time) && ($current_time > $start_time)) {
                                        $store_status['status'] = "BreakTime";
                                        $store_status['start_time'] = $start_time;
                                        $store_status['end_time'] = $end_time;
                                        return $store_status;
                                    } else {
                                        return true;
                                    }
                                } else {
                                    return true;
                                }
                            }
                        } else if ($store_data['Store']['is_break2'] == 1) {
                            $start_time = strtotime($store_break['StoreBreak']['break2_start_time']);
                            $end_time = strtotime($store_break['StoreBreak']['break2_end_time']);
                            if (($current_time < $end_time) && ($current_time > $start_time)) {
                                $store_status['status'] = "BreakTime";
                                $store_status['start_time'] = $start_time;
                                $store_status['end_time'] = $end_time;
                                return $store_status;
                            }
                        } else {
                            return true;
                        }
                    } else {
                        return true;
                    }
                }
            } else {
                $store_status['status'] = "WeekDay";
                return $store_status;
            }
        }
    }

    /* ------------------------------------------------      
     * Function name:getDeliveryInterval()      
     * Description : return start time for store
     * Parameter : $today(1=>Today, 0=>another Day), $orderType(3=>delivery, 2=>Pick-up), $preOrder(1=>preOrder, 0=>Now)
      created:10/2/2016
      ----------------------------------------------------- */

    function getStartTime($startTime = null, $today = null, $orderType = null, $preOrder = null, $endTime = null, $storeId = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $store_data = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.delivery_interval', 'Store.delivery_delay', 'Store.pick_up_delay')));
        $poststartTime = $startTime;
        //Check Order Type (i.e. Delivery/Pick Up)
        $deliveryInterval = 0;
        if ($orderType == 3) {
            $deliveryInterval = !empty($store_data['Store']['delivery_delay']) ? $store_data['Store']['delivery_delay'] : 0;
        } else if ($orderType == 2) {
            $deliveryInterval = !empty($store_data['Store']['pick_up_delay']) ? $store_data['Store']['pick_up_delay'] : 0;
        } else if ($orderType == 1) {
            $deliveryInterval = 60;
        }

        //Check Day (i.e. Today/ another)

        if ($today) {
            $nowDate = date("Y-m-d H:i:s");
            $minute = date("i");
            if ($minute % 5 != 0) {
                $mod = $minute % 5;
                $rem = 5 - $mod;
                $nowDate = date("Y-m-d H:i:s", strtotime((date("Y-m-d H:") . $minute . ":00")) + ($rem * 60));
            }

            $currentStoreactual = date("H:i:s", strtotime($this->storeTimeZoneUser('', $nowDate, $storeId)));
            $currentStoreTime = date("H:i:s", strtotime($currentStoreactual) + ($deliveryInterval * 60));
            $var1 = explode(":", $currentStoreactual);
            $hours = $var1[0];
            $minutes = $var1[1];

            $TotalCurrentminutes = $hours * 60 + $minutes;
            $actualminutes = $TotalCurrentminutes + ($deliveryInterval);

            $var2 = explode(":", $endTime);
            $Ehours = $var2[0];
            $Eminutes = $var2[1];

            $TotalEndminutes = $Ehours * 60 + $Eminutes;
            if (!empty($endTime)) {
                if (strtotime($currentStoreactual) > strtotime($endTime)) {
                    return 0;
                }
                if ($actualminutes >= 1440 || $actualminutes >= $TotalEndminutes) {
                    return 0;
                }
            }

            $currentStoreTime = strtotime($currentStoreTime);
            $startTime = strtotime($startTime);

            //Check Day (i.e. Now/ Predor)
            $currentTime = 0;
            $currentTime = date("H:i:s", ($currentStoreTime));
            $currentTimestr = strtotime($currentTime);
            $hour24 = array('24:00:00', '24:00', '00:00:00', '00:00');
            //store start time is greater than or not to current time
            if ($startTime > $currentTimestr && (!in_array($poststartTime, $hour24))) {
                $currentTime = date("H:i:s", $startTime + ($deliveryInterval * 60));
            }
        } else {
            $currentTime = date("H:i:s", (strtotime($startTime) + ($deliveryInterval * 60)));
        }
        return $currentTime;
    }

    public function checkbreakTime($start, $store_data, $storeBT, $orderType, $storeId = null) {
        //Check Order Type (i.e. Delivery/Pick Up)
        $deliveryInterval = 0;
        if ($orderType == 3) {
            $deliveryInterval = !empty($store_data['Store']['delivery_delay']) ? $store_data['Store']['delivery_delay'] : 0;
        } else if ($orderType == 2) {
            $deliveryInterval = !empty($store_data['Store']['pick_up_delay']) ? $store_data['Store']['pick_up_delay'] : 0;
        } else if ($orderType == 1) {
            $deliveryInterval = 60;
        }

        $currST = date("H:i", strtotime($start) - ($deliveryInterval * 60));

        if ($store_data['Store']['is_break1'] == 1 && strtotime($currST) > strtotime($storeBT['StoreBreak']['break1_start_time']) && strtotime($currST) < strtotime($storeBT['StoreBreak']['break1_end_time'])) {
            return $storeBT['StoreBreak']['break1_end_time'];
        }

        if ($store_data['Store']['is_break2'] == 1 && strtotime($currST) > strtotime($storeBT['StoreBreak']['break2_start_time']) && strtotime($currST) < strtotime($storeBT['StoreBreak']['break2_end_time'])) {
            return $storeBT['StoreBreak']['break2_end_time'];
        }

        return $start;
    }

    /* ------------------------------------------------
      Function name:getStoreTime()
      Description : return time range array for admin
      created:10/2/2016
      ----------------------------------------------------- */

    function getStoreTime($startTime = null, $endTime = null, $ordertype = null, $store_data = null, $storeBT = null, $storeId = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate', 'Store.delivery_delay', 'Store.pick_up_delay', 'Store.cutoff_time')));
        if ($ordertype) {
            if ($ordertype == 2) {
                $interval = ($storeInfo['Store']['pick_up_delay'] * 60);
            } elseif ($ordertype == 3) {
                $interval = ($storeInfo['Store']['delivery_delay'] * 60);
            } else {
                $interval = 900;
            }
        } else {
            $interval = 900;
        }

        $tStart = strtotime($startTime);

        $tEnd = strtotime($endTime);
        if (!empty($store_data) && !empty($storeBT)) {
            $BStart1 = strtotime($storeBT['StoreBreak']['break1_start_time']);
            $BEnd1 = strtotime($storeBT['StoreBreak']['break1_end_time']);
            $BStart2 = strtotime($storeBT['StoreBreak']['break2_start_time']);
            $BEnd2 = strtotime($storeBT['StoreBreak']['break2_end_time']);
        }
        $tNow = $tStart;
        $timeRange = array();
        $i = 0;
        while ($tNow <= $tEnd) {
            if (!empty($store_data) && !empty($storeBT)) {
                if ($store_data['Store']['is_break1'] == 1 && $tNow > $BStart1 && $tNow < $BEnd1) {
                    $tNow = $BEnd1;
                }
                if ($store_data['Store']['is_break2'] == 1 && $tNow > $BStart2 && $tNow < $BEnd2) {
                    $tNow = $BEnd2;
                }
            }

            if (!empty($storeInfo['Store']['time_formate'])) {
                $intervalTime = date("h:i a", $tNow);
            } else {
                $intervalTime = date("H:i", $tNow);
            }
            $intervalTimeforValue = date("H:i:s", $tNow);
            if ($intervalTime == "00:00" || $intervalTimeforValue == "00:00:00" || $intervalTime == "00:00:00") {
                $intervalTime = strtotime("23:59");
                if ($storeInfo['Store']['time_formate'] == 1) {
                    $intervalTime = date("h:i a", $intervalTime);
                } else {
                    $intervalTime = date("H:i", $intervalTime);
                }
                $intervalTimeforValue = "23:59:00";
            }

            $timeRange[$intervalTimeforValue] = $intervalTime;
            $tNow = $tNow + 300;
            $i++;
        }
        return $timeRange;
    }

}
