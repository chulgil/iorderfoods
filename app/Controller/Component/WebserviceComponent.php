<?php

App::uses('Component', 'Controller');

class WebserviceComponent extends Component {

    
   var $components = array('Cookie', 'Session', 'Email', 'Upload', 'Categories.Easyphpthumbnail');
   
    public function webserviceLog($string=null,$file_name=null,$header=null){      
      $logfile="./webserviceLog/".date("Y-m-d")."_".$file_name;      
      ob_start();
      echo "\n=========================".date("H:i:s")."===========================\n";
      print_r($header)."\n";
      print_r($string);
      echo "\n=====================================================================\n";
      $out=ob_get_contents();
      $file = fopen($logfile,"a+");
      fwrite($file,$out);
      fclose($file);//write $out to file
      ob_end_clean();
      
    }
     public function webserviceAdminLog($string=null,$file_name=null,$header=null){      
      $logfile="./webserviceAdminLog/".date("Y-m-d")."_".$file_name;      
      ob_start();
      echo "\n=========================".date("H:i:s")."===========================\n";
      print_r($header)."\n";
      print_r($string);
      echo "\n=====================================================================\n";
      $out=ob_get_contents();
      $file = fopen($logfile,"a+");
      fwrite($file,$out);
      fclose($file);//write $out to file
      ob_end_clean();
      
    }
   function reformatDate($date, $from_format = 'm-d-Y', $to_format = 'Y-m-d')
    {
        $date_aux = date_create_from_format($from_format, $date);
        return date_format($date_aux, $to_format);
    }
    
    public function getcurrentTime($storeId=null,$returnType=1){
        /*
         * 1- "Y-m-d h:i:s"
         * 2- "Y-m-d"
         * 3- "h:i:s"
         */
        $returnTime=null;
        $storeTimezoneInfo=array();
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));
        if (!empty($storeInfo['Store']['time_zone_id'])){
            $storeTimezoneInfo = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $storeInfo['Store']['time_zone_id']), 'fields' => array('TimeZone.code'), 'recursive' => -1));
            date_default_timezone_set($storeTimezoneInfo['TimeZone']['code']);
            if($returnType==1){
                $returnTime = date("Y-m-d H:i:s");
            }elseif($returnType==2){
                $returnTime = date("Y-m-d");
            }else{
                $returnTime = date("H:i:s");
            }
        } 
        
        return $returnTime;
        
    }
    
    function storeTimezone($storeId=null, $dateToconvert = null, $type = null) {
        
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $timezone = date_default_timezone_get(); //get server time zone            

        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));

        if ($storeInfo['Store']['time_zone_id'] != 0 || $storeInfo['Store']['time_zone_id'] != '') {
            $storeadmintimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" =>$storeInfo['Store']['time_zone_id']), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));

            $servertime = date("d-m-Y h:i:s A");
            date_default_timezone_set("GMT");
            $gmtTime = date("d-m-Y h:i:s A");
            $diff1 = (strtotime($gmtTime) - strtotime($servertime));
            date_default_timezone_set($storeadmintimezone['TimeZone']['code']);
            $requiredTime = date("d-m-Y h:i:s A");
            $diff2 = (strtotime($requiredTime) - strtotime($gmtTime));
            date_default_timezone_set($timezone);
            $dateToconvert = str_replace('/', '-', $dateToconvert);
            $dateToconvert = date_format(new DateTime($dateToconvert), "d-m-Y h:i:s A");
            $add = ($diff1) + ($diff2);
            $var = strtotime($dateToconvert) + $add;
            $dateToconvert = date("Y-m-d h:i:s a", $var);
        }
        return $dateToconvert;
    }
    
    function sendSmsNotificationFront($toNumber = null, $message = null,$storeId=null) {
        if ($toNumber) {
            App::import('Model', 'Store');
            $this->Store = new Store();
            $settings = $this->Store->find('first', array('fields'=>array('id','twilio_api_key','twilio_api_token','twilio_number'),'conditions' => array('Store.id' => $storeId)));
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
    function gettodayDate($format = 1) {
        if ($format == 1) {
            $currTimevar = date("Y-m-d", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        } elseif ($format == 2) {
            $currTimevar = date("H:i:s", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        } elseif ($format == 3) {
            $currTimevar = date("Y-m-d H:i:s", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        }

        return $currTimevar;
    }
    /* ------------------------------------------------
      Function name:storeCheck()
      Description:This function is used to check is store has Holiday or not on booking Date
      created:14/10/2016
      ----------------------------------------------------- */
    function storePaymentCheck($storeId=null,$order_type=null) {
        
        App::import('Model', 'Store');
        $this->Store = new Store();
        $result=FALSE;
//            $storeCheck=$this->Store->find('first',array('conditions'=>array('Store.id'=>$storeId,'Store.is_deleted'=>0,'Store.is_active'=>1)));
            $storePaymentCheck=$this->Store->find('first',array('conditions'=>array('Store.id'=>$storeId,'Store.is_deleted'=>0,'Store.is_active'=>1), "fields" => array("Store.id","Store.is_pay_by_credit_card", "Store.is_express_check_out", "Store.delivery_zone_type", "Store.cash_on_delivery")));
            if(empty($storePaymentCheck)){
                $responsedata['message'] = "The Store is not active.";
                $responsedata['response'] = 0;
                $responsedata['discount'] = "";
                return $responsedata;
            }
            if($order_type=="Cash"){
                if($storePaymentCheck['Store']['cash_on_delivery']==0){
                    $responsedata['message'] = "Cash on delivery is not allowed by the store.";
                    $responsedata['response'] = 0;
                    $responsedata['discount'] = "";
                    return $responsedata;
                }
            }
            if($order_type=="CreditCard"){
                if($storePaymentCheck['Store']['is_pay_by_credit_card']==0){
                    $responsedata['message'] = "Credit card payment is not allowed by the store.";
                    $responsedata['response'] = 0;
                    $responsedata['discount'] = "";
                    return $responsedata;
                }
            }
            
            if($order_type=="Paypal"){
                if($storePaymentCheck['Store']['is_express_check_out']==0){
                    $responsedata['message'] = "Paypal express payment is not allowed by the store.";
                    $responsedata['response'] = 0;
                    $responsedata['discount'] = "";
                    return $responsedata;
                }
            }
            
        }
       /* ------------------------------------------------
      Function name:storeOrderCheck()
      Description:This function is used to check is Wheater store accepting order on that day and Time.
      created:26/10/2016
      ----------------------------------------------------- */  
        
    function storeOrderCheck($storeId = null, $orderType = null, $time = null, $date = null) {

        App::import('Model', 'Store');
        $this->Store = new Store();
        $dateformat = explode("-", $date);
        $today = $this->getcurrentTime($storeId, 2);
        $todayunix=strtotime($today);
        $bookingDate = $dateformat[2] . "-" . $dateformat[1] . "-" . $dateformat[0]; // date foramate Y-M-D
        /*$bookingDate=$bookingDate." ".$time*/;
        $bookingDateunix=strtotime($bookingDate);
        $result = FALSE;
        $storeOrderCheck = $this->Store->find('first', array('conditions' => array('Store.id' => $storeId, 'Store.is_deleted' => 0, 'Store.is_active' => 1), "fields" => array("Store.id", "Store.deliveryblackout_limit", "Store.pickblackout_limit", "Store.deliverycalendar_limit", "Store.pickblackout_limit", "Store.delivery_delay", "Store.pick_up_delay","Store.pickcalendar_limit")));
        
        $deliveryInterval = 0;
        $orderType=strtolower($orderType);
        if ($orderType == "delivery") {
            $deliveryInterval = !empty($storeOrderCheck['Store']['delivery_delay']) ? $storeOrderCheck['Store']['delivery_delay'] : 0;
            //$currST = date("Y-m-d h:i:s", strtotime($today) + ($deliveryInterval * 60));
            if (!empty($storeOrderCheck['Store']['deliverycalendar_limit'])) {
                $DeliveryadvanceDay = $storeOrderCheck['Store']['deliverycalendar_limit'] - 1;
                $dayDate = date('Y-m-d H:i:s', strtotime($today . ' +' . $DeliveryadvanceDay . ' day'));
                $dayDateunix=strtotime($dayDate);
                //if (($bookingDateunix < $dayDateunix) && ($bookingDateunix >= $todayunix)) {
               if ($bookingDateunix >= $todayunix) {
                  $responsedata['message'] = "Order Placed";
                  $responsedata['response'] = 1;
                  return $responsedata;
                    
                } else {
                    $responsedata['message'] = "Store is not accepting order on selected date, please select other date.";
                    $responsedata['response'] = 0;
                    return $responsedata;
                }
            } else {
                $responsedata['message'] = "Order Placed";
                $responsedata['response'] = 1;
                return $responsedata;
            }
        } else {
            $deliveryInterval = !empty($storeOrderCheck['Store']['pick_up_delay']) ? $storeOrderCheck['Store']['pick_up_delay'] : 0;
           // $currST = date('Y-m-d h:i:s', strtotime($today) + ($deliveryInterval * 60));
            if ($storeOrderCheck['Store']['pickcalendar_limit'] > 0) {
                $pickupadvanceDay = $storeOrderCheck['Store']['pickcalendar_limit'] - 1;
                $dayDate = date('Y-m-d H:i:s', strtotime($today . ' +' . $pickupadvanceDay . ' day'));
                $dayDateunix=strtotime($dayDate);
                //echo $bookingDate."<br>";
                //echo $today."<br>";
                //echo $dayDate."<br>";
                //if (($bookingDateunix < $dayDateunix) && ($bookingDateunix >= $todayunix)) {
                  if ($bookingDateunix >= $todayunix) {
                     $responsedata['message'] = "Order Placed";
                     $responsedata['response'] = 1;
                     return $responsedata;
                } else {
                    $responsedata['message'] = "Store is not opened or not accepting order this time.";
                    $responsedata['response'] = 0;
                    return $responsedata;
                }
            } else {
                $responsedata['message'] = "Order Placed";
                $responsedata['response'] = 1;
                return $responsedata;
            }
        }
    }
    
    /* ------------------------------------------------
      Function name:storeCheck()
      Description:This function is used to check is store has Holiday or not on booking Date
      created:14/10/2016
      ----------------------------------------------------- */
    function storeCheck($storeId=null,$order_type=null) {
        
        App::import('Model', 'Store');
        $this->Store = new Store();
        $result=FALSE;
//            $storeCheck=$this->Store->find('first',array('conditions'=>array('Store.id'=>$storeId,'Store.is_deleted'=>0,'Store.is_active'=>1)));
            $storeCheck=$this->Store->find('first',array('conditions'=>array('Store.id'=>$storeId,'Store.is_deleted'=>0,'Store.is_active'=>1), "fields" => array("Store.id", "Store.is_delivery", "Store.is_take_away", "Store.is_booking_open", "Store.cash_on_delivery", "Store.is_pay_by_credit_card", "Store.is_express_check_out", "Store.delivery_zone_type")));
            if(empty($storeCheck)){
                $responsedata['message'] = "The Store is not active.";
                $responsedata['response'] = 0;
                $responsedata['discount'] = "";
                return $responsedata;
            }
            if($order_type=="Delivery"){
                if($storeCheck['Store']['is_delivery']==0){
                    $responsedata['message'] = "Store doesn't provide delivery order.";
                    $responsedata['response'] = 0;
                    $responsedata['discount'] = "";
                    return $responsedata;
                }
            }
            
            if($order_type=="Carry-Out"){
                if($storeCheck['Store']['is_take_away']==0){
                    $responsedata['message'] = "Store doesn't provide pickup order.";
                    $responsedata['response'] = 0;
                    $responsedata['discount'] = "";
                    return $responsedata;
                }
            }
            
        }
    /* ------------------------------------------------
      Function name:storeHolidayCheck()
      Description:This function is used to check is store has Holiday or not on booking Date
      created:14/10/2016
      ----------------------------------------------------- */
    function storeHolidayCheck($storeId=null,$time=null,$date=null) {
        
        App::import('Model', 'StoreHoliday');
        $this->StoreHoliday = new StoreHoliday();
        $dateformat=  explode("-", $date);
        $result=FALSE;
        $dateInt=$dateformat[2]."-".$dateformat[1]."-".$dateformat[0]; // date foramate Y-M-D
            $StoreHolidayTime=$this->StoreHoliday->find('all',array('conditions'=>array('StoreHoliday.store_id'=>$storeId,'StoreHoliday.is_deleted'=>0,'StoreHoliday.holiday_date'=>$dateInt,'StoreHoliday.is_active'=>1)));
            if(empty($StoreHolidayTime)){
                $result=TRUE;
            }else{
                $result=FALSE;
            }
            return $result;
        }
        
    /* ------------------------------------------------
      Function name:storeStoreBlackOutCheck()
      Description:This function is used to check is store has Store has Black Out Day or not on booking Date
      created:14/10/2016
      ----------------------------------------------------- */
    function storeBlackOutCheck($storeId = null, $time = null, $date = null, $orderType = null) {

        App::import('Model', 'Store');
        $this->Store = new Store();
        $dateformat = explode("-", $date);
        $bookingDate = $dateformat[2] . "-" . $dateformat[1] . "-" . $dateformat[0]; // date foramate Y-M-D
        $bookingDateunix = strtotime($bookingDate);
        $storeStoreBlackOutCheck = $this->Store->find('first', array('conditions' => array('Store.id' => $storeId, 'Store.is_deleted' => 0, 'Store.is_active' => 1), "fields" => array("Store.id", "Store.deliveryblackout_limit", "Store.pickblackout_limit", "Store.deliverycalendar_limit", "Store.pickcalendar_limit")));
        $today = $this->getcurrentTime($storeId, 2);
        $todayunix = strtotime($today);
        $orderType = strtolower($orderType);
        if ($orderType == "delivery") {
            if (!empty($storeStoreBlackOutCheck['Store']['deliverycalendar_limit'])) {
            $Deliverymaxdatenumber = $storeStoreBlackOutCheck['Store']['deliverycalendar_limit'] - 1 + $storeStoreBlackOutCheck['Store']['deliveryblackout_limit'];

                $Deliverymaxdate = date('Y-m-d', strtotime($today . ' +' . $Deliverymaxdatenumber . ' day'));
                $Deliverymaxdateunix = strtotime($Deliverymaxdate);
                if (!empty($storeStoreBlackOutCheck['Store']['deliveryblackout_limit'])) {
                    $maxblackOutdate = date('Y-m-d', strtotime($today . ' +' . $storeStoreBlackOutCheck['Store']['deliveryblackout_limit'] . ' day'));
                    $maxblackOutdateunix = strtotime($maxblackOutdate);
                    $todayunix = $maxblackOutdateunix;
                    if ($bookingDateunix >= $maxblackOutdateunix) {
                        $responsedata['message'] = "Success";
                        $responsedata['response'] = 1;
                    } else {
                        $responsedata['message'] = "Store has blackout day, please select another date.";
                        $responsedata['response'] = 0;
                        return $responsedata;
                    }
                }
                // Check Order date is between Current date & Max advance limit
                if (($bookingDateunix >= $todayunix) && ($bookingDateunix <= $Deliverymaxdateunix)) {
                    $responsedata['message'] = "Success";
                    $responsedata['response'] = 1;
                    return $responsedata;
                } else {
                    $responsedata['message'] = "Store is not accepting order for selected date, please select another date.";
                    $responsedata['response'] = 0;
                    return $responsedata;
                }
                   
            } else {
                if ($bookingDateunix == $todayunix) {
                    $responsedata['message'] = "Success";
                    $responsedata['response'] = 1;
                } else {
                    $responsedata['message'] = "Store is not accepting order for selected date, please select another date.";
                    $responsedata['response'] = 0;
                    return $responsedata;
                }
            }
             return $responsedata;
        } else {
            if (!empty($storeStoreBlackOutCheck['Store']['pickcalendar_limit'])) {
                $Deliverymaxdatenumber = $storeStoreBlackOutCheck['Store']['pickcalendar_limit'] - 1 + $storeStoreBlackOutCheck['Store']['pickblackout_limit'];
                $Deliverymaxdate = date('Y-m-d', strtotime($today . ' +' . $Deliverymaxdatenumber . ' day'));
                $Deliverymaxdateunix = strtotime($Deliverymaxdate);
                if (!empty($storeStoreBlackOutCheck['Store']['deliveryblackout_limit'])) {
                    $maxblackOutdate = date('Y-m-d', strtotime($today . ' +' . $storeStoreBlackOutCheck['Store']['pickblackout_limit'] . ' day'));
                    $maxblackOutdateunix = strtotime($maxblackOutdate);
                    $todayunix = $maxblackOutdateunix;
                    if ($bookingDateunix >= $maxblackOutdateunix) {
                        $responsedata['message'] = "Success";
                        $responsedata['response'] = 1;
                    } else {
                        $responsedata['message'] = "Store has blackout day, please select another date.";
                        $responsedata['response'] = 0;
                        return $responsedata;
                    }
                }

                // Check Order date is between Current date & Max advance limit
                if (($bookingDateunix >= $todayunix) && ($bookingDateunix <= $Deliverymaxdateunix)) {
                    $responsedata['message'] = "Success";
                    $responsedata['response'] = 1;
                } else {
                    $responsedata['message'] = "Store is not accepting order for selected date, please select another date.";
                    $responsedata['response'] = 0;
                    return $responsedata;
                }
            } else {
                if ($bookingDateunix == $todayunix) {
                    $responsedata['message'] = "Success";
                    $responsedata['response'] = 1;
                } else {
                    $responsedata['message'] = "Store is not accepting order for selected date, please select another date.";
                    $responsedata['response'] = 0;
                    return $responsedata;
                }
            }
             return $responsedata;
        }
    }    
  /* ------------------------------------------------
      Function name:storeAvailabilityCheck()
      Description:This function is used to check is store has Opend on booking Date and time
      created:14/10/2016
      ----------------------------------------------------- */      
        
        function storeAvailabilityCheck($storeId = null, $time = null, $date = null) {
        App::import('Model', 'StoreAvailability');
        $this->StoreAvailability = new StoreAvailability();
        $this->StoreAvailability->bindModel(
                array(
            'hasOne' => array(
                'StoreBreak' => array(
                    'className' => 'StoreBreak',
                    'foreignKey' => 'store_availablity_id',
                    'conditions' => array('StoreBreak.is_deleted' => 0, 'StoreBreak.is_active' => 1, 'StoreBreak.store_id' => $storeId),
                )
            )
                ), false
        );
        $dateformat = explode("-", $date);
        $dateInt = $dateformat[2] . "-" . $dateformat[1] . "-" . $dateformat[0]; // date foramate Y-M-D
        $dateDay = date("l", strtotime($dateInt)) . "\n"; // Find day of that date for compaision with StoreAvailability  day
//        echo $dateDay;
       $conditions = array('StoreAvailability.store_id' => $storeId, 'StoreAvailability.is_deleted' => 0, 'StoreAvailability.day_name' => trim(strtolower($dateDay)), 'StoreAvailability.is_closed' => 0, 'StoreAvailability.is_active' => 1);
        $StoreAvailabilityCheck = $this->StoreAvailability->find('first', array('conditions' => $conditions, "fields" => array("StoreAvailability.id", "StoreAvailability.start_time", "StoreAvailability.end_time","StoreAvailability.day_name", "StoreAvailability.is_closed"), 'recursive' => 2));
        //pr($StoreAvailabilityCheck);
        
        $result = FALSE;
        if(!empty($StoreAvailabilityCheck)){
        if ($StoreAvailabilityCheck['StoreAvailability']['is_closed'] == 0) {
            if ($StoreAvailabilityCheck['StoreAvailability']['start_time'] == '24:00:00') {
                $StoreAvailabilityCheck['StoreAvailability']['start_time'] = "00:00:00";
            }
            if ($StoreAvailabilityCheck['StoreAvailability']['end_time'] == '24:00:00') {
                $StoreAvailabilityCheck['StoreAvailability']['end_time'] = "00:00:00";
            }
            $compairTime = strtotime($time);
            $StoreAvailabilityStartTime = strtotime($StoreAvailabilityCheck['StoreAvailability']['start_time']);
            $StoreAvailabilityEndTime = strtotime($StoreAvailabilityCheck['StoreAvailability']['end_time']);
            
            if (($compairTime >= $StoreAvailabilityStartTime) && ($compairTime <= $StoreAvailabilityEndTime)) {// Check is Order time is ordered at store time 
                $storeBreakCheck= $this->storeBreakCheck($StoreAvailabilityCheck,$time,$storeId);
                if($storeBreakCheck){
                     $result = TRUE;
                }  else {
                    $result = FALSE;
                    }
            } else {
                $result = FALSE;
            }
        } else {
            $result = FALSE;
        }
        } else {
            $result = FALSE;
        }
        return $result;
    }
    /* ------------------------------------------------
      Function name:storeBreakCheck()
      Description:This function is used to check is store has break or not on booking time
      created:14/10/2016
      ----------------------------------------------------- */
    function storeBreakCheck($StoreAvailabilityCheck = array(), $time = null, $storeId = null) {
//        pr($StoreAvailabilityCheck);
        App::import('Model', 'Store');
        $this->Store = new Store();
        $result = FALSE;
        $storeStoreBlackOutCheck = $this->Store->find('first', array('conditions' => array('Store.id' => $storeId, 'Store.is_deleted' => 0, 'Store.is_active' => 1), "fields" => array("Store.id", "Store.is_break_time", "Store.is_break1", "Store.is_break2")));
if(!empty($StoreAvailabilityCheck['StoreBreak'])){
        if ($StoreAvailabilityCheck['StoreBreak']['break1_start_time'] == '24:00:00') {
            $StoreAvailabilityCheck['StoreBreak']['break1_start_time'] = "00:00:00";
        }
        if ($StoreAvailabilityCheck['StoreBreak']['break1_end_time'] == '24:00:00') {
            $StoreAvailabilityCheck['StoreBreak']['break1_end_time'] = "00:00:00";
        }
        if ($StoreAvailabilityCheck['StoreBreak']['break2_start_time'] == '24:00:00') {
            $StoreAvailabilityCheck['StoreBreak']['break2_start_time'] = "00:00:00";
        }
        if ($StoreAvailabilityCheck['StoreBreak']['break2_end_time'] == '24:00:00') {
            $StoreAvailabilityCheck['StoreBreak']['break2_end_time'] = "00:00:00";
        }
        
        
         // strtotime Store break time conversion for break 1
        $StoreBreak1StartTime = strtotime($StoreAvailabilityCheck['StoreBreak']['break1_start_time']);
        $StoreBreak1EndTime = strtotime($StoreAvailabilityCheck['StoreBreak']['break1_end_time']);
        // strtotime Store break time conversion for break 2
        $StoreBreak2StartTime = strtotime($StoreAvailabilityCheck['StoreBreak']['break2_start_time']);
        $StoreBreak2EndTime = strtotime($StoreAvailabilityCheck['StoreBreak']['break2_end_time']);
        }
        $compairTime = strtotime($time);


//        echo "Booking Time -- : " . $time . "<br>";
//        echo "StoreBreak1StartTime Dates -- : " . $StoreAvailabilityCheck['StoreBreak']['break1_start_time'] . "<br>";
//        echo "StoreBreak1EndTime Advance Day -- : " . $StoreAvailabilityCheck['StoreBreak']['break1_end_time'] . "<br>";
//        echo "StoreBreak2StartTime Dates -- : " . $StoreAvailabilityCheck['StoreBreak']['break2_start_time'] . "<br>";
//        echo "StoreBreak2EndTime Advance Day -- : " . $StoreAvailabilityCheck['StoreBreak']['break2_end_time'] . "<br>";
//        echo "--------------------------------" . "<br>";
//        echo "Booking Time -- : " . $compairTime . "<br>";
//        echo "StoreBreak1StartTime Dates -- : " . $StoreBreak1StartTime . "<br>";
//        echo "StoreBreak1EndTime Advance Day -- : " . $StoreBreak1EndTime . "<br>";
//        echo "StoreBreak2StartTime Dates -- : " .  $StoreBreak2StartTime. "<br>";
//        echo "StoreBreak2EndTime Advance Day -- : " .$StoreBreak2EndTime . "<br>";
        
        
        if ($storeStoreBlackOutCheck['Store']['is_break_time'] == 1) {
            if ($storeStoreBlackOutCheck['Store']['is_break1'] == 1) {
                if ($storeStoreBlackOutCheck['Store']['is_break2'] == 1) {
                    if (($compairTime <= $StoreBreak1StartTime) && ($compairTime >= $StoreBreak1EndTime) ||($compairTime >= $StoreBreak1StartTime) && ($compairTime >= $StoreBreak1EndTime)||($compairTime <= $StoreBreak1StartTime) && ($compairTime <= $StoreBreak1EndTime)) { // compaire First Break time
                         if (($compairTime <= $StoreBreak2StartTime) && ($compairTime >= $StoreBreak2EndTime) ||($compairTime >= $StoreBreak2StartTime) && ($compairTime >= $StoreBreak2EndTime)||($compairTime <= $StoreBreak2StartTime) && ($compairTime <= $StoreBreak2EndTime)) {// compaire Second Break time
                            $result = TRUE;
                        } else {
                            $result = FALSE;
                        }
                    } else {
                        $result = FALSE;
                    }
                } else {
                    $result = TRUE;
                }
            } else {
                $result = TRUE;
            }
        } else {
            $result = TRUE;
        }
        return $result;
    }
    /* ------------------------------------------------
      Function name:storeAddressCheck()
      Description:This function is used to check is Delivery Address is valid or not
      created:14/10/2016
      ----------------------------------------------------- */
    function deliveryAddressCheck($storeId=null,$merchant_id=null,$address_id=null) {
        
        App::import('Model', 'DeliveryAddress');
        $this->DeliveryAddress = new DeliveryAddress();
        $result=FALSE;
        $deliveryAddressCheck=$this->DeliveryAddress->find('first',array('conditions'=>array('DeliveryAddress.merchant_id'=>$merchant_id,'DeliveryAddress.is_deleted'=>0,'DeliveryAddress.id'=>$address_id,'DeliveryAddress.is_active'=>1),'fields'=>array('id')));
        //pr($deliveryAddressCheck);
            if(!empty($deliveryAddressCheck)){
                $result=TRUE;
            }else{
                $result=FALSE;
            }
            return $result;
        }
    /* ------------------------------------------------
      Function name:checkCategory()
      Description:This function is used to check Category is active or not
      created:17/10/2016
      ----------------------------------------------------- */
    function checkCouponCode($storeId = null, $merchant_id = null, $couponCode = null, $user_id = null) {

        App::import('Model', 'Coupon');
        $this->Coupon = new Coupon();
        App::import('Model', 'UserCoupon');
        $this->UserCoupon = new UserCoupon();
//        echo $user_id;
        $coupon = $this->Coupon->find('first', array('conditions' => array('Coupon.store_id' => $storeId, 'Coupon.coupon_code' => $couponCode, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0), 'fields' => array('id', 'number_can_use', 'used_count', 'discount', 'discount_type')));
        if (empty($coupon)) {
            $responsedata['message'] = "This coupon code is invalid or has expired.";
            $responsedata['response'] = 0;
            $responsedata['discount'] = "";
            return $responsedata;
        }
        //$UserCoupon = $this->UserCoupon->find('first', array('conditions' => array('UserCoupon.store_id' => $storeId,'UserCoupon.coupon_id' => $coupon['Coupon']['id'],'UserCoupon.user_id' => $user_id, 'UserCoupon.is_active' => 1, 'UserCoupon.is_deleted' => 0), 'fields' => array('id')));
        //if(empty($UserCoupon)){
        //    $responsedata['message'] = "Coupon is not valid or Expired";
        //    $responsedata['response'] = 0;
        //    $responsedata['discount'] = "";
        //    return $responsedata;
        //}

        if ($coupon['Coupon']['number_can_use'] > $coupon['Coupon']['used_count']) {
            
            $responsedata['response'] = 1;
            $responsedata['message'] = "Success";
            $responsedata['discount'] = $coupon['Coupon']['discount'];
            $responsedata['discount_type'] = $coupon['Coupon']['discount_type'];
            return $responsedata;
        } else {
            $responsedata['message'] = "This coupon code is invalid or has expired.";
            $responsedata['response'] = 0;
            $responsedata['discount'] = "";
            return $responsedata;
        }
    }

    /* ------------------------------------------------
     /* ------------------------------------------------
      Function name:checkCategory()
      Description:This function is used to check Category is active or not
      created:17/10/2016
      ----------------------------------------------------- */
    function checkCategory($storeId=null,$merchant_id=null,$catId=null) {
        
        App::import('Model', 'Category');
        $this->Category = new Category();
        $result=FALSE;
            $checkCategory=$this->Category->find('first',array('conditions'=>array('Category.store_id'=>$storeId,'Category.merchant_id'=>$merchant_id,'Category.is_deleted'=>0,'Category.id'=>$catId,'Category.is_active'=>1),'fields'=>array('id')));
            if(!empty($checkCategory)){
                $result=TRUE;
            }else{
                $result=FALSE;
            }
            return $result;
        }
         /* ------------------------------------------------
      Function name:checkItem()
      Description:This function is used to check Item is active or not
      created:17/10/2016
      ----------------------------------------------------- */
    function checkItem($storeId=null,$merchant_id=null,$catId=null,$itemId=null) {
        
        App::import('Model', 'Item');
        $this->Item = new Item();
        $result=FALSE;
            $checkItem=$this->Item->find('first',array('conditions'=>array('Item.store_id'=>$storeId,'Item.merchant_id'=>$merchant_id,'Item.is_deleted'=>0,'Item.id'=>$itemId,'Item.category_id'=>$catId,'Item.is_active'=>1),'fields'=>array('id','name','description','units','is_seasonal_item','start_date','end_date','is_deliverable','preference_mandatory','default_subs_price')));
            if(!empty($checkItem)){
                $result=$checkItem;
            }else{
                $result=FALSE;
            }
            return $result;
        }
        
            /* ------------------------------------------------
      Function name:checkItemPrice()
      Description:This function is used to check is store price
      created:17/10/2016
      ----------------------------------------------------- */
    function checkItemPrice($storeId=null,$merchant_id=null,$itemId=null,$size_id=null) {
        
        App::import('Model', 'ItemPrice');
        $this->ItemPrice = new ItemPrice();
        $result=FALSE;
        $checkItemPrice=$this->ItemPrice->find('first',array('conditions'=>array('ItemPrice.store_id'=>$storeId,'ItemPrice.merchant_id'=>$merchant_id,'ItemPrice.is_deleted'=>0,'ItemPrice.item_id'=>$itemId,'ItemPrice.is_active'=>1,'ItemPrice.size_id'=>$size_id),'fields'=>array('id')));
            //pr($checkItemPrice);
            if(!empty($checkItemPrice)){
                $result=TRUE;
            }else{
                $result=FALSE;
            }
            return $result;
        }
        
/* ------------------------------------------------------------------------------------------------------------------------
      Function name:checkSubAddon()
      Description:This function is used to check is store has addon SubAddon or not on booking Date
      created:17/10/2016
      -------------------------------------------------------------------------------------------------------------------- */
    function checkSubAddon($storeId = null, $merchant_id = null, $itemId = null, $sub_addonId = null,$addonsize_id=null) {

        App::import('Model', 'Topping');
        $this->Topping = new Topping();
        App::import('Model', 'AddonSize');
        $this->AddonSize = new AddonSize();
        $result = FALSE;
        if (!empty($addonsize_id)) {
            $AddonSizes = $this->AddonSize->find('first', array('fields' => array('id', 'size', 'price_percentage'), 'conditions' => array('AddonSize.store_id' => $storeId, 'AddonSize.is_active' => 1, 'AddonSize.is_deleted' => 0, 'AddonSize.merchant_id' => $merchant_id, 'AddonSize.id' => $addonsize_id),'fields'=>array('id')));
            if (empty($AddonSizes)) {
                return FALSE;
            }
        }

        $checkSubTopping = $this->Topping->find('first', array('conditions' => array('Topping.store_id' => $storeId, 'Topping.merchant_id' => $merchant_id, 'Topping.is_deleted' => 0, 'Topping.item_id' => $itemId, 'Topping.is_active' => 1, 'Topping.id' => $sub_addonId, 'Topping.is_addon_category' => 0),'fields'=>array('id','addon_id')));
        if (empty($checkSubTopping)) {
            return FALSE;
        }
        $checkTopping = $this->Topping->find('first', array('conditions' => array('Topping.store_id' => $storeId, 'Topping.merchant_id' => $merchant_id, 'Topping.is_deleted' => 0, 'Topping.item_id' => $itemId, 'Topping.is_active' => 1,'Topping.id' => $checkSubTopping['Topping']['addon_id'],'Topping.is_addon_category' => 1)));
        return !empty($checkTopping) ? TRUE : FALSE;
    }

/* ------------------------------------------------------------------------------------------------------------------------
      Function name:checkSubpreference()
      Description:This function is used to check is store has Sub preference or not on booking Date
      created:17/10/2016
      -------------------------------------------------------------------------------------------------------------------- */   

    function checkSubpreference($storeId=null,$merchant_id=null,$subprefernce_id=null) {
        
        App::import('Model', 'SubPreference');
        $this->SubPreference = new SubPreference();
        App::import('Model', 'Type');
        $this->Type = new Type();
        $result=FALSE;
        
         $checkSubPreference=$this->SubPreference->find('first',array('conditions'=>array('SubPreference.store_id'=>$storeId,'SubPreference.is_deleted'=>0,'SubPreference.is_active'=>1,'SubPreference.id'=>$subprefernce_id),'fields'=>array('id','type_id')));
//         pr($checkSubPreference);
          if (empty($checkSubPreference)) {
            return FALSE;
        }
        $this->Type->unBindModel(array('hasMany' => array('ItemType'))); 
        $checkPreference=$this->Type->find('first',array('conditions'=>array('Type.store_id'=>$storeId,'Type.merchant_id'=>$merchant_id,'Type.is_deleted'=>0,'Type.is_active'=>1,'Type.id'=>$checkSubPreference['SubPreference']['type_id']),'fields'=>array('id')));
//        pr($checkPreference);
         return !empty($checkPreference) ? TRUE : FALSE;
        }
/* ------------------------------------------------------------------------------------------------------------------------
      Function name:checkOffer()
      Description:This function is used to check offer is active or not active also check is offer is available on that date and time
      created:17/10/2016
      -------------------------------------------------------------------------------------------------------------------- */           
    function checkOffer($storeId = null, $merchant_id = null, $offerItem_id = null, $offer_id = null, $offerSize_id = null, $date = null, $time = null) {
        App::import('Model', 'Offer');
        $this->Offer = new Offer();
        $dateformat = explode("-", $date);
        //$bookingDate = $dateformat[2] . "-" . $dateformat[1] . "-" . $dateformat[0]; // date foramate Y-M-D
        $bookingDate = $this->getcurrentTime($storeId, 2);
        $checkOffer = $this->Offer->find('first', array('conditions' => array('Offer.store_id' => $storeId, 'Offer.merchant_id' => $merchant_id, 'Offer.is_deleted' => 0, 'Offer.is_active' => 1, 'Offer.id' => $offer_id, 'Offer.item_id' => $offerItem_id, 'Offer.size_id' => $offerSize_id),'fields'=>array('id','is_time','offer_start_date','offer_end_date','offer_start_time','offer_end_time')));
        //echo $bookingDate."<br>";
        //echo $time."<br>";
        //pr($checkOffer);
        if (empty($checkOffer)) {
            return FALSE;
        }
        
        if(!empty($checkOffer['Offer']['offer_start_date']) && !empty($checkOffer['Offer']['offer_end_date'])){
         //echo "here"."<br>";
            if ($checkOffer['Offer']['offer_start_date'] > $bookingDate) {
               //echo "here ---1"."<br>";
             return FALSE;
            }
            if ($checkOffer['Offer']['offer_end_date'] < $bookingDate) {
               //echo "here ---1"."<br>";
             return FALSE;
            }
        }
        
        // Check offer Data and time is offer is vallid on that data or time
        if ($checkOffer['Offer']['is_time'] == 1) {
         //echo "here is in IS Time"."<br>";
                if ($checkOffer['Offer']['offer_start_time'] == '24:00:00') {
                    $checkOffer['Offer']['offer_start_time'] = "00:00:00";
                }
                if ($checkOffer['Offer']['offer_end_time'] == '24:00:00') {
                    $checkOffer['Offer']['offer_end_time'] = "00:00:00";
                }
                $bookingTime = strtotime($time);
                $startTime = strtotime($checkOffer['Offer']['offer_start_time']);
                $endTime = strtotime($checkOffer['Offer']['offer_end_time']);
                //echo "Booking Time--".$bookingTime."<br>";
                //echo "Offer Start Time--".$startTime."<br>";
                //echo "Offer End Time--".$endTime."<br>";

                if (($bookingTime >= $startTime) && ($bookingTime <= $endTime)) {
                  //echo "I am here in True"."<br>";
                    return TRUE;
                } else {
                  //echo "I am here in False"."<br>";
                    return FALSE;
                }
            
        } else {
            return TRUE;
        }
    }
    
    /* ------------------------------------------------------------------------------------------------------------------------
      Function name:checkOfferDetail()
      Description:This function is used to check checkOffer Detail is active or not active also check is offer is available on that date and time
      created:17/10/2016
      -------------------------------------------------------------------------------------------------------------------- */  
    function checkOfferDetail($storeId = null, $merchant_id = null, $offeredItem_id = null, $offered_id = null, $offer_id = null, $offeredSize_id = null) {
        App::import('Model', 'OfferDetail');
        $this->OfferDetail = new OfferDetail();
        if(empty($offeredSize_id)){
            $offeredSize_id=0;
        }
        $checkOfferDetail = $this->OfferDetail->find('first', array('conditions' => array('OfferDetail.store_id' => $storeId, 'OfferDetail.merchant_id' => $merchant_id, 'OfferDetail.is_deleted' => 0, 'OfferDetail.is_active' => 1, 'OfferDetail.id' => $offered_id, 'OfferDetail.offer_id' => $offer_id, 'OfferDetail.offerItemID' => $offeredItem_id, 'OfferDetail.offerSize' => $offeredSize_id)));
      if (empty($checkOfferDetail)) {
            return FALSE;
        }
        if(!empty($checkOfferDetail)){
            $checkItem=$this->Item->find('first',array('conditions'=>array('Item.store_id'=>$storeId,'Item.merchant_id'=>$merchant_id,'Item.is_deleted'=>0,'Item.id'=>$offeredItem_id,'Item.is_active'=>1),'fields'=>array('id')));
            if(!empty($checkItem)){
                 return TRUE;
            }else{
                return FALSE;
            }
        }
    }
    
     /* ------------------------------------------------------------------------------------------------------------------------
      Function name:checkOfferDetail()
      Description:This function is used to check checkOffer Detail is active or not active also check is offer is available on that date and time
      created:17/10/2016
      -------------------------------------------------------------------------------------------------------------------- */  
    function extendedOfferCheck($storeId = null, $merchant_id = null, $extendedOfferItem_id = null, $extendedOffer_id = null, $extendedOfferunit_id = null,$date=null) {
        App::import('Model', 'ItemOffer');
        $this->ItemOffer = new ItemOffer();
        $dateformat = explode("-", $date);
        //$bookingDate = $dateformat[2] . "-" . $dateformat[1] . "-" . $dateformat[0]; // date foramate Y-M-D
        $bookingDate = $this->getcurrentTime($storeId, 2);
        $extendedOfferCheck = $this->ItemOffer->find('first', array('conditions' => array('ItemOffer.store_id' => $storeId, 'ItemOffer.merchant_id' => $merchant_id, 'ItemOffer.is_deleted' => 0, 'ItemOffer.is_active' => 1, 'ItemOffer.id' => $extendedOffer_id, 'ItemOffer.item_id' => $extendedOfferItem_id, 'ItemOffer.unit_counter' => $extendedOfferunit_id)));
        if (empty($extendedOfferCheck)) {
            return FALSE;
        }
        if (($extendedOfferCheck['ItemOffer']['start_date'] <= $bookingDate) && ($extendedOfferCheck['ItemOffer']['end_date'] >= $bookingDate)) {
            return TRUE;
        }else{
             return FALSE;
        }
        
    }
    
      /* ------------------------------------------------------------------------------------------------------------------------
      Function name:getItemPrice()
      Description:This function is used to find Item Price based on Size Id
      created:18/10/2016
      -------------------------------------------------------------------------------------------------------------------- */  
      function getItemPrice($storeId = null, $merchant_id = null, $catId = null, $itemId = null, $sizeId = null) {
        App::import('Model', 'Item');
        $this->Item = new Item();
        $this->ItemPrice->bindModel(
                array('belongsTo' => array(
                        'Size' => array(
                            'className' => 'Size',
                            'foreignKey' => 'size_id',
                            'conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $storeId),
                            'order' => array('Size.id ASC')
                        ),
                        'StoreTax' => array(
                            'className' => 'StoreTax',
                            'foreignKey' => 'store_tax_id',
                            'conditions' => array('StoreTax.is_active' => 1, 'StoreTax.is_deleted' => 0, 'StoreTax.store_id' => $storeId)
                        )
        )));
        $this->Item->bindModel(
                array('hasOne' => array(
                        'ItemPrice' => array(
                            'className' => 'ItemPrice',
                            'foreignKey' => 'item_id',
                            'type' => 'INNER',
                            'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $storeId, 'ItemPrice.size_id' => $sizeId),
                            'order' => array('ItemPrice.position ASC')
                        ),
                    )
        ));
        $checkItem = $this->Item->find('first', array('conditions' => array('Item.store_id' => $storeId, 'Item.merchant_id' => $merchant_id, 'Item.is_deleted' => 0, 'Item.id' => $itemId, 'Item.category_id' => $catId, 'Item.is_active' => 1), 'fields' => array('id', 'name', 'description', 'units', 'is_seasonal_item', 'start_date', 'end_date', 'is_deliverable', 'preference_mandatory', 'default_subs_price'), 'recursive' => 3));
        
        if (!empty($checkItem['ItemPrice']['Size'])) {
                $default_price = $checkItem['ItemPrice']['price'];
                $intervalPrice = 0;
                $intervalPrice = $this->getTimeIntervalPrice($itemId, $sizeId, $storeId);
                if (!empty($intervalPrice['IntervalPrice'])) {
                    $default_price = $intervalPrice['IntervalPrice']['price'];
                }
            }else{
               $default_price= $checkItem['ItemPrice']['price'];
            }
            return $default_price;
        }

public function getTimeIntervalPrice($itemId = null, $sizeId = null, $storeId = null) {

        App::import('Model', 'IntervalPrice');
        $this->IntervalPrice = new IntervalPrice();
        App::import('Model', 'Interval');
        $this->Interval = new Interval();
        $currentDateTime = $this->getcurrentTime($storeId, 1);
        $currentTime = date("H:i:s", strtotime($currentDateTime));
        $currentDay = date("N", strtotime($currentDateTime));
        $this->Interval->unbindModel(
                array('hasMany' => array('IntervalDay'))
        );
        
        $this->Interval->bindModel(
                array(
                    'hasOne' => array(                        
                        'IntervalDay' => array(
                            'className' => 'IntervalDay',
                            'foreignKey' => 'interval_id',
                            'conditions' => array('IntervalDay.week_day_id' => $currentDay,'IntervalDay.day_status' => 1, 'IntervalDay.store_id' => $storeId),
                            'fields'=>array('IntervalDay.id','IntervalDay.week_day_id','IntervalDay.interval_id'),
                            'type'=>'INNER',
                            
                        )
                    )
                )
        ); 
        
        $this->IntervalPrice->bindModel(
                array(
                    'belongsTo' => array(
                        'Interval' => array(
                            'className' => 'Interval',
                            'foreignKey' => 'interval_id',
                            'conditions' => array('Interval.is_active'=>1,'Interval.is_deleted' => 0, 'Interval.store_id' => $storeId,'Interval.start <=' => $currentTime,'Interval.end >=' => $currentTime),
                            'fields'=>array('Interval.id','Interval.name'),
                            'type' => 'INNER'                            
                        )
                    )
                )
        );
        $intervalPriceDetail=array();
        $intervalPriceDetail = $this->IntervalPrice->find('all', array('recursive'=>2,'conditions' => array('IntervalPrice.item_id' => $itemId,'IntervalPrice.size_id' => $sizeId,'IntervalPrice.store_id' => $storeId, 'IntervalPrice.is_active' => 1, 'IntervalPrice.is_deleted' => 0, 'IntervalPrice.size_active' => 1),'fields'=>array('IntervalPrice.id','IntervalPrice.interval_id','IntervalPrice.price')));
        foreach($intervalPriceDetail as $key => $value){
            if(!empty($value['IntervalPrice']) && !empty($value['Interval']) && !empty($value['Interval']['IntervalDay'])){ 
                return $intervalPriceDetail[$key];break;
            }
    }
}
    
public function getSubAddonPrice($storeId = null, $merchant_id = null, $itemId = null, $item_Size_Id = null, $sub_addonId = null, $addon_size_id = null) {

        App::import('Model', 'Item');
        $this->Item = new Item();
        App::import('Model', 'AddonSize');
        $this->AddonSize = new AddonSize();
        $this->Item->bindModel(
                array(
            'hasOne' => array(
                'ItemPrice' => array(
                    'className' => 'ItemPrice',
                    'foreignKey' => 'item_id',
                    'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $storeId, 'ItemPrice.size_id' => $item_Size_Id),
                    'order' => array('ItemPrice.position ASC'),
                    'fields' => array('id', 'item_id', 'price', 'store_tax_id', 'size_id', 'position')
                ), 'Topping' => array(
                    'className' => 'Topping',
                    'foreignKey' => 'item_id',
                    'order' => array('Topping.position ASC'),
                    'conditions' => array('Topping.id' => $sub_addonId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $storeId),
                    'fields' => array('id', 'price', 'item_id', 'category_id', 'name', 'is_addon_category', 'addon_id', 'no_size', 'size_id', 'price', 'position')
                ), 'ItemDefaultTopping' => array(
                    'className' => 'ItemDefaultTopping',
                    'foreignKey' => 'item_id',
                    'conditions' => array('ItemDefaultTopping.store_id' => $storeId, 'ItemDefaultTopping.topping_id' => $sub_addonId, 'ItemDefaultTopping.is_active' => 1, 'ItemDefaultTopping.is_deleted' => 0)
                )
            )
                ), false);

        $checkItem = $this->Item->find('first', array('conditions' => array('Item.store_id' => $storeId, 'Item.merchant_id' => $merchant_id, 'Item.is_deleted' => 0, 'Item.id' => $itemId, 'Item.is_active' => 1), 'fields' => array('id', 'name', 'description', 'units', 'is_seasonal_item', 'start_date', 'end_date', 'is_deliverable', 'preference_mandatory', 'default_subs_price'), 'recursive' => 3));
        $arrAddon=array();
        //pr($checkItem);
        $addonPrice = 0;
        if ($checkItem['Topping']['no_size'] == 0) {
            
            if (!empty($addon_size_id)) {
                $toppingSizes = $this->AddonSize->find('first', array('fields' => array('id', 'size', 'price_percentage'), 'conditions' => array('AddonSize.store_id' => $storeId, 'AddonSize.is_active' => 1, 'AddonSize.is_deleted' => 0, 'AddonSize.merchant_id' => $merchant_id, 'AddonSize.id' => $addon_size_id)));
                //pr($toppingSizes);
                if ($checkItem['Item']['default_subs_price'] == 1) {
                        $addonPrice = $addonPrice + $checkItem['Topping']['price'];
                        
                } else {
                    $addonPrice = $addonPrice + $this->getToppingPrice($checkItem['ItemPrice']['size_id'], $checkItem['ItemPrice']['item_id'], $storeId, $sub_addonId);
                 
                }
                //echo $addonPrice."<br>";
                //echo $checkItem['Topping']['price']."<br>";
                //echo $toppingSizes['AddonSize']['price_percentage']."<br>";
                $addonPrice = number_format($addonPrice * ($toppingSizes['AddonSize']['price_percentage'] / 100), 2);
            }
        } else {
            if ($checkItem['Item']['default_subs_price'] == 1) {
                if (empty($checkItem['ItemDefaultTopping'])) {
                    $addonPrice = $addonPrice + $checkItem['Topping']['price'];
                } else {
                    $addonPrice = 0;
                }
            } else {
                $addonPrice = $addonPrice + $this->getToppingPrice($checkItem['ItemPrice']['size_id'], $checkItem['ItemPrice']['item_id'], $storeId, $sub_addonId);
            }
        }
        //echo $addonPrice;
            $arrAddon['subadon_id']=$sub_addonId;
            $arrAddon['message']="Success";
            $arrAddon['price']=$addonPrice;
            return $arrAddon;
    }

    public function getToppingPrice($sizeID = null, $ItemId = null, $storeId = null,$sub_addonId=null) {
        App::import('Model', 'ToppingPrice');
        $this->ToppingPrice = new ToppingPrice();
        
        $Toppingprice = $this->ToppingPrice->find('first', array('conditions' => array('ToppingPrice.item_id' => $ItemId, 'ToppingPrice.store_id' => $storeId,'ToppingPrice.topping_id' => $sub_addonId, 'ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0)));
        //pr($Toppingprice);
        $toppingPrice=0;
        if($Toppingprice){
         $toppingPrice= $Toppingprice['ToppingPrice']['price'];
        }
       
        return $toppingPrice;
    }
    
    public function getSubPrePrice($storeId = null, $merchant_id = null, $itemId = null, $sub_preferenceId = null, $sizeId = null) {
        App::import('Model', 'Item');
        $this->Item = new Item();
        App::import('Model', 'ItemType');
        $this->ItemType = new ItemType();
        App::import('Model', 'SubPreference');
        $this->SubPreference = new SubPreference();
        
        $checkSubPreference=$this->SubPreference->find('first',array('conditions'=>array('SubPreference.store_id'=>$storeId,'SubPreference.is_deleted'=>0,'SubPreference.is_active'=>1,'SubPreference.id'=>$sub_preferenceId),'fields'=>array('id','type_id')));
        
        $this->Item->bindModel(
                array(
            'hasOne' => array(
                'ItemType' => array(
                    'className' => 'ItemType',
                    'foreignKey' => 'item_id',
                    'order' => array('ItemType.position ASC'),
                    'conditions' => array('ItemType.is_active' => 1,'ItemType.item_id' => $itemId,'ItemType.type_id' =>$checkSubPreference['SubPreference']['type_id'], 'ItemType.is_deleted' => 0, 'ItemType.store_id' => $storeId),
                    'fields' => array('id', 'item_id', 'type_id', 'position', 'position')
                ),
            )
                ), false);
        $checkItem = $this->Item->find('first', array('conditions' => array('Item.store_id' => $storeId, 'Item.merchant_id' => $merchant_id, 'Item.is_deleted' => 0, 'Item.id' => $itemId, 'Item.is_active' => 1), 'fields' => array('id', 'name', 'description', 'units', 'is_seasonal_item', 'start_date', 'end_date', 'is_deliverable', 'preference_mandatory', 'default_subs_price'), 'recursive' => 4));
//        pr($checkItem);
        $arrPre=array();
        $preferencePrice=0;
        if($checkItem['Item']['preference_mandatory']){
            if(empty($sub_preferenceId)){
                $msg="Please select preference";
                $arrPre['message']=$msg;
                $arrPre['price']="";
                return $arrPre;
            }
        }    
        if(empty($checkItem['ItemType'])){
            $msg="Preference is not available for this item.";
            $arrPre['subpreference_id']=$sub_preferenceId;
            $arrPre['message']=$msg;
            $arrPre['price']="";
            return $arrPre;
        }    
        
         if(!empty($checkItem['ItemType'])){
              $preferencePrice=$this->getTypePrice($storeId, $merchant_id, $itemId, $checkSubPreference['SubPreference']['type_id'], $sub_preferenceId, $sizeId,$checkItem['Item']['default_subs_price']);
              $arrPre['subpreference_id']=$sub_preferenceId;
              $arrPre['message']="Success";
              $arrPre['price']=$preferencePrice;
            }    
            return $arrPre;
    
    }
 public function getTypePrice($storeId = null, $merchant_id = null, $itemId = null, $preferenceId = null, $sub_preferenceId = null, $sizeId = null,$itemDefaultPrice=null) {
     
        App::import('Model', 'Type');
        $this->Item = new Item();
        App::import('Model', 'ItemType');
        $this->ItemType = new ItemType();
        App::import('Model', 'SubPreference');
        $this->SubPreference = new SubPreference();
        

        $this->Type->bindModel(
                array('hasOne' => array(
                        'SubPreference' => array(
                            'className' => 'SubPreference',
                            'foreignKey' => 'type_id',
                            'order' => array('SubPreference.position ASC'),
                            'conditions' => array('SubPreference.is_active' => 1,'SubPreference.id' => $sub_preferenceId, 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $storeId),
                            'fields' => array('SubPreference.id', 'SubPreference.name', 'SubPreference.price', 'SubPreference.position'),
                            'type' => 'INNER'
                        )
        ))); 
         $this->Type->SubPreference->bindModel(
                array('hasOne' => array(
                        'SubPreferencePrice' => array(
                            'className' => 'SubPreferencePrice',
                            'foreignKey' => 'sub_preference_id',
                            'conditions' => array('SubPreferencePrice.is_active' => 1, 'SubPreferencePrice.is_deleted' => 0, 'SubPreferencePrice.store_id' => $storeId, 'SubPreferencePrice.size_id' => $sizeId, 'SubPreferencePrice.item_id' => $itemId),
                            'fields' => array('SubPreferencePrice.id', 'SubPreferencePrice.item_id', 'SubPreferencePrice.size_id', 'SubPreferencePrice.price', 'SubPreferencePrice.sub_preference_id'),
                            'type' => 'INNER'
                        )
                    )
        ));
        $this->Type->unBindModel(array('hasMany' => array('ItemType')));
        $preferenceData = $this->Type->find('first',array('conditions'=>array('Type.id'=>$preferenceId),'recursive'=>2));
        $pricePreference=0;
        if(!empty($preferenceData)){
            if($itemDefaultPrice){
               if(!empty($preferenceData['SubPreference']['price'])){
                $pricePreference=$preferenceData['SubPreference']['price'];
               }
            }else{
               if(!empty($preferenceData['SubPreference']['SubPreferencePrice']['price'])){
                $pricePreference=$preferenceData['SubPreference']['SubPreferencePrice']['price'];
               }
            }
        }
        return $pricePreference;
 }
 
 function getOffersPrice($storeId = null, $merchant_id = null, $offer = array(),$itemPrice=null,$itemQuantity=null) {
        App::import('Model', 'Offer');
        $this->Offer = new Offer();
        App::import('Model', 'OfferDetail');
        $this->OfferDetail = new OfferDetail();
        App::import('Model', 'Item');
        $this->Item = new Item();
//        echo $itemPrice."<br>";
//        pr($offer);
        $arrOffer=array();
        $checkOffer = $this->Offer->find('first', array('conditions' => array('Offer.store_id' => $storeId, 'Offer.merchant_id' => $merchant_id, 'Offer.is_deleted' => 0, 'Offer.is_active' => 1, 'Offer.id' => $offer['offer_id'], 'Offer.item_id' => $offer['item_id'], 'Offer.size_id' => $offer['size_id'])));
//        pr($checkOffer);
        if($checkOffer['Offer']['unit']!=$itemQuantity){
            $arrOffer['message']="Offer is Valid on ".$checkOffer['Offer']['unit']." Quantity";
            $arrOffer['price']="";
            return $arrOffer;
        }
        if (empty($checkOffer)) {
            $arrOffer['message']="Offer is not applicable for this Item.";
            $arrOffer['price']="";
            return $arrOffer;
        }
        if($offer['is_fixed_price']!=$checkOffer['Offer']['is_fixed_price']){
            $arrOffer['message']="Offer is ".$checkOffer['Offer']['is_fixed_price'];
            $arrOffer['price']="";
            return $arrOffer;
        }
        
        if($checkOffer['Offer']['is_fixed_price']==1){
            if($offer['offerprice']!=$checkOffer['Offer']['offerprice']){
                $arrOffer['message']="Offer price not matched.";
                $arrOffer['price']="";
                return $arrOffer;
            }else{
                $arrOffer['message']="Success fixed offer price.";
                $arrOffer['price']=$checkOffer['Offer']['offerprice'];
                return $arrOffer;
            }
        }
        
        if($offer['is_fixed_price']==0){
            if(empty($offer['offered_items'])){
                $arrOffer['message']="Success";
                $arrOffer['price']=$itemPrice;
                return $arrOffer;
            }
            
            if(!empty($offer['offered_items'])){
                foreach ($offer['offered_items'] as $ot=>$offeredItem){
                    //pr($offeredItem);
                    if(!(empty($offeredItem))){
                    $checkOfferDetail = $this->OfferDetail->find('first', array('conditions' => array('OfferDetail.store_id' => $storeId, 'OfferDetail.merchant_id' => $merchant_id, 'OfferDetail.is_deleted' => 0, 'OfferDetail.is_active' => 1, 'OfferDetail.id' => $offeredItem['Offered_id'], 'OfferDetail.offer_id' =>$offer['offer_id'], 'OfferDetail.offerSize' => $offeredItem['size_id']),'fields'=>array('OfferDetail.id','OfferDetail.quantity','OfferDetail.discountAmt')));
                    $checkItem = $this->Item->find('first', array('conditions' => array('Item.store_id' => $storeId, 'Item.merchant_id' => $merchant_id, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Item.id' => $offeredItem['offered_item_id']),'fields'=>array('Item.id')));
                    if(empty($checkItem)){
                        $arrOffer['message']="Offer details item is not active.";
                        $arrOffer['price']="";
                        return $arrOffer;
                    }
                   if($checkOfferDetail['OfferDetail']['discountAmt']!=$offeredItem['price']){
                        $arrOffer['message']="Offer details price not matched.";
                        $arrOffer['price']="";
                        return $arrOffer;
                   }
                    if($checkOfferDetail['OfferDetail']['discountAmt']==$offeredItem['price']){
                        $arrOffer['message']="Success";
                        $arrOff['offeredPrice'][$ot]=$offeredItem['price'];
                    }
                }
            }
                if(!empty($arrOff['offeredPrice'])){
                    $arrOffer['price']=array_sum($arrOff['offeredPrice']);
                }else{
                    $arrOffer['price']="";
                }
                
                return $arrOffer;
            }
            
        }
        
    }
    
    /* ------------------------------------------------------------------------------------------------------------------------
      Function name:checkOfferDetail()
      Description:This function is used to check checkOffer Detail is active or not active also check is offer is available on that date and time
      created:17/10/2016
      -------------------------------------------------------------------------------------------------------------------- */  
    function getExtendedOffersPrice($storeId = null, $merchant_id = null, $extendedOffersPrice = array(),$user_id=null,$ItemQuantity=null,$itemPrice=null) {
        App::import('Model', 'ItemOffer');
        $this->ItemOffer = new ItemOffer();
        $extendedOfferCheck = $this->ItemOffer->find('first', array('conditions' => array('ItemOffer.store_id' => $storeId, 'ItemOffer.merchant_id' => $merchant_id, 'ItemOffer.is_deleted' => 0, 'ItemOffer.is_active' => 1, 'ItemOffer.id' => $extendedOffersPrice['promo_id'], 'ItemOffer.item_id' => $extendedOffersPrice['item_id'], 'ItemOffer.unit_counter' => $extendedOffersPrice['unit'])));
        $arrExtendedOffer=array();
         if (empty($extendedOfferCheck)) {
            $arrExtendedOffer['message']="Extended Offer is not applicable for this Item.";
                        $arrExtendedOffer['Price']="";
                        return $arrExtendedOffer;
        }
        if(!empty($extendedOfferCheck)){
            $itemisFree=$this->checkItemOffer($extendedOffersPrice['item_id'],$user_id,$ItemQuantity,$storeId);
            if($itemisFree==0){
                $arrExtendedOffer['message']="No item free.";
                $arrExtendedOffer['price']="";
                return $arrExtendedOffer;
            }
            if($itemisFree>0){
                $arrExtendedOffer['message']="Item free.";
                $arrExtendedOffer['price']=$itemPrice;
                return $arrExtendedOffer;
            }
        }
       
        
    }
    
    public function checkItemOffer($itemId=null,$userid=0,$ItemQuantity=1,$storeId=null){
        $FreeItemQuantity=0;
        if(!empty($itemId)){
            
            $todayDate=$this->getcurrentTime($storeId,1);
            $offerExists=$this->OfferExists($itemId,$todayDate);
            //pr($offerExists);
            if($offerExists){ 
               $startdate=$offerExists['ItemOffer']['start_date'];
               $endDate=$offerExists['ItemOffer']['end_date'];
               $orderItemCount = 0;
               if (!empty($userid)) {
                    $orderItemCount=$this->getItemInfo($itemId,$userid,$startdate,$endDate);
               }
               //pr($orderItemCount);
               $totalItem=0;
               if(!empty($orderItemCount[0][0]['total'])){
                   $totalItem=$orderItemCount[0][0]['total'];
               }
               $offerExists['ItemOffer']['unit_counter'] = $offerExists['ItemOffer']['unit_counter'] - 1;
               
               if($ItemQuantity){
                   $FreeItemQuantity=0;
                   
                  $applicableItemQunatity=fmod($totalItem,$offerExists['ItemOffer']['unit_counter']);
                  $totalItem=$applicableItemQunatity+$ItemQuantity;
                  if($totalItem>=$offerExists['ItemOffer']['unit_counter']){
                      //$Mod=fmod($totalItem,$offerExists['ItemOffer']['unit_counter']);
                      $FreeItemQuantity = (int)($totalItem/$offerExists['ItemOffer']['unit_counter']);
                      return $FreeItemQuantity;                      
                  }else{
                      return $FreeItemQuantity;
                  }
               }else{
                   $applicableItemQunatity=fmod($totalItem,$offerExists['ItemOffer']['unit_counter']);
                   $totalItem=$applicableItemQunatity+1;
                   
                   
                    if($totalItem>=$offerExists['ItemOffer']['unit_counter']){
                        $FreeItemQuantity=(int)($totalItem/$offerExists['ItemOffer']['unit_counter']);
                        return $FreeItemQuantity;
                    }
               }
            }
        }
        return $FreeItemQuantity;
    }
    /*------------------------------------------------
    Function name:OfferExists()
    Description:To find Detail of coupon from coupon table 
    created:08/8/2015
   -----------------------------------------------------*/
    public function OfferExists($itemID=null,$todayDate=null){    
        App::import('Model', 'ItemOffer');
        $this->ItemOffer = new ItemOffer();
        $todayDate=date('Y-m-d',strtotime($todayDate));
        if($todayDate){            
            $condition['start_date <=']= $todayDate; 
            $condition['end_date >=']=$todayDate;
        }
        $condition['ItemOffer.item_id']=$itemID;
        $condition['ItemOffer.is_deleted']=0;
        $condition['ItemOffer.is_active']=1;
        $offerDetail =$this->ItemOffer->find('first',array('conditions'=>$condition));     
        if($offerDetail){
            return $offerDetail;         
        }else{
            return 0;    
        }
     }
    public function getItemInfo($itemId=null,$userid=null,$startDate=null,$endDate=null){
        App::import('Model', 'OrderItem');
        $this->OrderItem = new OrderItem();
        $date[]=$startDate;
        $date[]=$endDate;
        $offer = $this->OrderItem->find('all',array('fields'=>array('SUM(quantity) as total'),'conditions'=>array('OrderItem.is_deleted'=>0,'OrderItem.item_id'=>$itemId,'OrderItem.user_id'=>$userid,'OrderItem.created BETWEEN ? and ? '=>$date)));
        //pr($offer);
        return $offer;
        
    }
    public function getStoreTx($storeId=null,$merchant_id=null,$itemId=null,$size_id=null){
        App::import('Model', 'StoreTax');
        $this->StoreTax = new StoreTax();
        App::import('Model', 'ItemPrice');
        $this->ItemPrice = new ItemPrice();
        $checkItemPrice=$this->ItemPrice->find('first',array('conditions'=>array('ItemPrice.store_id'=>$storeId,'ItemPrice.merchant_id'=>$merchant_id,'ItemPrice.is_deleted'=>0,'ItemPrice.item_id'=>$itemId,'ItemPrice.is_active'=>1,'ItemPrice.size_id'=>$size_id)));
//        pr($checkItemPrice);
        if(!empty($checkItemPrice)){
        $StoreTax = $this->StoreTax->find('first',array('conditions'=>array('StoreTax.is_deleted'=>0,'StoreTax.id'=>$checkItemPrice['ItemPrice']['store_tax_id'],'StoreTax.store_id'=>$storeId,'StoreTax.is_active'=>1)));
        }
        
        if(!empty($StoreTax)){
        $tax_value=$StoreTax['StoreTax']['tax_value'];
        return $tax_value;
        }
        
    }
    
    function getIntervalId($storeId = null, $merchant_id = null, $catId = null, $itemId = null, $sizeId = null) {
         App::import('Model', 'IntervalPrice');
        $this->IntervalPrice = new IntervalPrice();
        App::import('Model', 'Interval');
        $this->Interval = new Interval();
        $currentDateTime = $this->getcurrentTime($storeId, 1);
        $currentTime = date("H:i:s", strtotime($currentDateTime));
        $currentDay = date("N", strtotime($currentDateTime));
        $this->Interval->unbindModel(
                array('hasMany' => array('IntervalDay'))
        );
        
        $this->Interval->bindModel(
                array(
                    'hasOne' => array(                        
                        'IntervalDay' => array(
                            'className' => 'IntervalDay',
                            'foreignKey' => 'interval_id',
                            'conditions' => array('IntervalDay.week_day_id' => $currentDay,'IntervalDay.day_status' => 1, 'IntervalDay.store_id' => $storeId),
                            'fields'=>array('IntervalDay.id','IntervalDay.week_day_id','IntervalDay.interval_id'),
                            'type'=>'INNER',
                            
                        )
                    )
                )
        ); 
        
        $this->IntervalPrice->bindModel(
                array(
                    'belongsTo' => array(
                        'Interval' => array(
                            'className' => 'Interval',
                            'foreignKey' => 'interval_id',
                            'conditions' => array('Interval.is_active'=>1,'Interval.is_deleted' => 0, 'Interval.store_id' => $storeId,'Interval.start <=' => $currentTime,'Interval.end >=' => $currentTime),
                            'fields'=>array('Interval.id','Interval.name'),
                            'type' => 'INNER'                            
                        )
                    )
                )
        );
        $intervalPriceDetail=array();
        $intervalPriceDetail = $this->IntervalPrice->find('all', array('recursive'=>2,'conditions' => array('IntervalPrice.item_id' => $itemId,'IntervalPrice.size_id' => $sizeId,'IntervalPrice.store_id' => $storeId, 'IntervalPrice.is_active' => 1, 'IntervalPrice.is_deleted' => 0, 'IntervalPrice.size_active' => 1),'fields'=>array('IntervalPrice.id','IntervalPrice.interval_id','IntervalPrice.price')));
        foreach($intervalPriceDetail as $key => $value){
            if(!empty($value['IntervalPrice']) && !empty($value['Interval']) && !empty($value['Interval']['IntervalDay'])){ 
                return $intervalPriceDetail[$key];break;
            }
    }
}


function storeTimeFormateUser($timeToconvert = null, $withDate = null, $storeId=null) {
        if (!$this->Session->check('store_id')) {
            return $timeToconvert;
        }
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

  /*
      To get order Fax format
     */

    public function getOrderFaxFormat($orderId = null, $store_id = null, $merchant_id = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        if (isset($store_id) && !empty($store_id)) {
            $storeID = $store_id;
        }
        if (isset($merchant_id) && !empty($merchant_id)) {
            $merchantId = $merchant_id;
        }
        $storeInfo = $this->Store->fetchStoreDetail($storeID);
        if ($orderId) {
            App::import('Model', 'OrderOffer');
            $this->OrderOffer = new OrderOffer();
            App::import('Model', 'OrderItem');
            $this->OrderItem = new OrderItem();
            App::import('Model', 'OrderPayment');
            $this->OrderPayment = new OrderPayment();
            App::import('Model', 'Order');
            $this->Order = new Order();
            App::import('Model', 'OrderPreference');
            $this->OrderPreference = new OrderPreference();
            App::import('Model', 'OrderTopping');
            $this->OrderTopping = new OrderTopping();
            App::import('Model', 'OrderItemFree');
            $this->OrderItemFree = new OrderItemFree();
            App::import('Model', 'Item');
            $this->Item = new Item();

            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent')));
            $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
            $this->OrderItem->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
            $this->OrderItem->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
            $this->OrderItem->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
            $this->Order->bindModel(array('hasMany' => array('OrderItemFree' => array('fields' => array('Order_id', 'item_id','free_quantity','price')))));
            $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'total_item_price', 'tax_price', 'interval_id'))), 'belongsTo' => array('Store' => array('fields' => array('id', 'service_fee', 'delivery_fee', 'store_name', 'store_url', 'address')), 'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id', 'fields' => array('name')), 'DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address', 'phone', 'email')), 'OrderStatus' => array('fields' => array('name')), 'User' => array('foreignKey' => 'user_id', 'fields' => array('email', 'fname', 'lname', 'phone')), 'OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount', 'payment_gateway')))), false);

            $this->Order->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id'), 'order' => array('OrderTopping.id' => 'asc')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'), 'order' => array('OrderPreference.id' => 'asc'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
            $orderDetails = $this->Order->getfirstOrder($merchantId, $storeID, $orderId);
            //pr($orderDetails);
            $amount = 0;
            $itemPriceWidth = "";
            $flag = true;
            foreach ($orderDetails['OrderItem'] as $order) {
                if ($flag) {
                    if (!empty($order['OrderTopping'])) {
                        foreach ($order['OrderTopping'] as $key => $toppingarr) {
                            if (!empty($toppingarr['Topping']['name'])) {
                                $itemPriceWidth = "";
                            }
                        }
                        $flag = false;
                    } else {
                        if (!empty($order['OrderPreference'])) {
                            foreach ($order['OrderPreference'] as $key => $prearr) {
                                if (!empty($prearr['SubPreference']['name'])) {
                                    $itemPriceWidth = "";
                                }
                            }
                        }
                        $flag = false;
                    }
                }
            }

            foreach ($orderDetails['OrderItem'] as $order) {
                if (empty($order['OrderOffer'])) {
                    if (!empty($order['Size']['size'])) {
                        $sizestring = $order['Size']['size'] . "&nbsp;";
                    } else {
                        $sizestring = "";
                    }
                    $Interval = "";
                    if (isset($order['interval_id'])) {
                        $intervalId = $order['interval_id'];
                        $Interval = ($this->getIntervalName($intervalId)) ? "(" . $this->getIntervalName($intervalId) . ")" : '';
                    }

                    $tempitem = "<tr><td style='width:82%;'>" . $order['quantity'] . '&nbsp;&nbsp;&nbsp;' . $sizestring . $order['Item']['name'] . '&nbsp;' . $Interval . '</td><td ' . $itemPriceWidth . '> $' . number_format($order['total_item_price'], 2) . "</td></tr>";
                    $toppingstr = "";
                    if (!empty($order['OrderTopping'])) {
                        foreach ($order['OrderTopping'] as $key => $toppingarr) {
                            if (!empty($toppingarr['Topping']['name'])) {
                                $addonsize = 1;
                                $addOnSizedetails = $this->getaddonSize($toppingarr['addon_size_id'],$storeID);
                                if ($addOnSizedetails) {
                                    $addonsize = $addOnSizedetails['AddonSize']['size'];
                                }
                                $toppingstr.=$addonsize . ' ' . $toppingarr['Topping']['name'] . ", ";
                            }
                        }
                    }
                    $preferencetr = "";
                    if (!empty($order['OrderPreference'])) {
                        foreach ($order['OrderPreference'] as $key => $prearr) {
                            if (!empty($prearr['SubPreference']['name'])) {
                                $preferencetr.=$prearr['SubPreference']['name'] . ", ";
                            }
                        }
                    }
                    $toppingstr = rtrim($toppingstr);
                    $preferencetr = rtrim($preferencetr);
                    $toppingstr = rtrim($toppingstr, ",");
                    $preferencetr = rtrim($preferencetr, ",");
                    $topping = "";
                    if (!empty($toppingstr)) {
                        $topping = "<tr><td width='60%'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small style='float:left;margin-left:30px;width:80%;'><strong >Add-on: </strong>" . wordwrap($toppingstr, 65, "<br>", true) . "</small></td></tr>";
                    }
                    $preference = "";
                    if (!empty($preferencetr)) {

                        $preference = "<tr><td width='60%'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small style='float:left;margin-left:30px;width:80%;'>" . wordwrap($preferencetr, 65, "<br>", true) . "</small></td></tr>";
                    }

                    $itemss[] = $tempitem . $preference . $topping;
                } else {

                    $offerItemName = '';
                    foreach ($order['OrderOffer'] as $off) {
                        if (!empty($off['Size']['size'])) {
                            $offsizestring = $off['Size']['size'] . "&nbsp;";
                        } else {
                            $offsizestring = "";
                        }
                        $offerItemName .= '<tr><td>&nbsp;&nbsp;&nbsp;<small><strong>Promo : </strong>' . $off['quantity'] . '&nbsp;&nbsp;&nbsp;' . $offsizestring . $off['Item']['name'] . "</small></td></tr>";
                    }

                    $toppingstr = "";
                    if (!empty($order['OrderTopping'])) {
                        foreach ($order['OrderTopping'] as $key => $toppingarr) {
                            if (!empty($toppingarr['Topping']['name'])) {
                                $toppingstr.=$toppingarr['Topping']['name'] . ", ";
                            }
                        }
                    }

                    $preferencetr = "";
                    if (!empty($order['OrderPreference'])) {
                        foreach ($order['OrderPreference'] as $key => $prearr) {
                            if (!empty($prearr['SubPreference']['name'])) {
                                $preferencetr.=$prearr['SubPreference']['name'] . ", ";
                            }
                        }
                    }
                    $toppingstr = rtrim($toppingstr);
                    $preferencetr = rtrim($preferencetr);
                    $toppingstr = rtrim($toppingstr, ",");
                    $preferencetr = rtrim($preferencetr, ",");
                    $topping = "";
                    if (!empty($toppingstr)) {
                        $topping = "<tr><td width='60%;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small style='float:left;margin-left:30px;width:80%;'><strong>Add-on: </strong>" . wordwrap($toppingstr, 65, "<br>", true) . "</small></td></tr>";
                    }
                    $preference = "";
                    if (!empty($preferencetr)) {
                        $preference = "<tr><td width='60%;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small style='float:left;margin-left:30px;width:width:80%;'>" . wordwrap($preferencetr, 65, "<br>", true) . "</small></td></tr>";
                    }
                    if (!empty($order['Size']['size'])) {
                        $osizestring = $order['Size']['size'] . "&nbsp;";
                    } else {
                        $osizestring = "";
                    }

                    $Interval = "";
                    if (isset($order['interval_id'])) {
                        $intervalId = $order['interval_id'];
                        $Interval = ($this->getIntervalName($intervalId)) ? "(" . $this->getIntervalName($intervalId) . ")" : '';
                    }


                    $tempitem = "<tr><td style='width:82%;'>" . $order['quantity'] . "&nbsp;&nbsp;&nbsp;" . $osizestring . $order['Item']['name'] . '&nbsp;' . $Interval . "</td><td> $" . number_format($order['total_item_price'], 2) . "</td></tr>";
                    $itemss[] = $tempitem . $preference . $topping . $offerItemName;
                }
                $amount = $amount + $order['total_item_price'];
            }
            $printdata = "<table style='width:100%'>";
            $printdata.="<tr><td><table style='border:2px solid black;width:100%;'>";
            $createdTime = $this->storeTimeZoneUser($storeInfo['Store']['time_zone_id'], $orderDetails['Order']['created']);
            $date = date('m/d/Y', strtotime($createdTime));
            $time = date('h:i:s', strtotime($createdTime));
            $am = date('A', strtotime($createdTime));
            $printdata .="<tr><td>" . $date . "</td><td style='width:16.9%'>" . $time . ' ' . $am . "</td><td></td></tr>";

            //---- Start End-User info ----------------------------------------------------------------
            if ($orderDetails['Order']['user_id'] == 0) {
                $enduser_name = $orderDetails['DeliveryAddress']['name_on_bell'];
                $enduser_phone = $orderDetails['DeliveryAddress']['phone'];
                $email = $orderDetails['DeliveryAddress']['email'];
            } else {
                $enduser_name = $orderDetails['User']['fname'] . ' ' . $orderDetails['User']['lname'];
                $enduser_phone = $orderDetails['User']['phone'];
                $email = $orderDetails['User']['email'];
            }
            $printdata.="<tr><td>" . $enduser_name . "</td><td></td></tr>";
            $printdata.="<tr><td>" . $email . "</td><td></td></tr>";
            $printdata.="<tr><td>" . $enduser_phone . "</td><td></td></tr>";
            //---- End End-User info ------------------------------------------------------------------
            // 09/30/2016 Request by Gina
            switch ($storeInfo['Store']['store_url']) {
                case "littletokyo.curryhouseusa.com" :
                case "cupertino.curryhouseusa.com" :
                case "cypress.curryhouseusa.com" :
                case "gardena.curryhouseusa.com" :
                case "irvine.curryhouseusa.com" :
                case "koreatown.curryhouseusa.com" :
                case "montreypark.curryhouseusa.com" :
                case "puentehills.curryhouseusa.com" :
                case "santaana.curryhouseusa.com" :
                case "torrance.curryhouseusa.com" :
                case "westla.curryhouseusa.com" :
                    $printdata.="<tr><td>" . $email . "</td><td></td></tr>";
                    break;
            }

            $printdata.="<tr><td></br>Online " . $orderDetails['Segment']['name'] . "</td><td></td></tr>";
            if ($orderDetails['OrderPayment']['payment_gateway'] == 'COD') {
                if ($orderDetails['Order']['seqment_id'] == 3) {
                    $printdata.="<tr><td>Cash on Delivery - UNPAID</td></tr>";
                } else {
                    $printdata.="<tr><td>Cash on Pickup - UNPAID</td></tr>";
                }
            } else {
                $printdata.="<tr><td>PAID by credit card (" . $orderDetails['OrderPayment']['payment_gateway'] . ")</td></tr>";
            }
            $printdata.="<tr><td>Order#: " . $orderDetails['Order']['order_number'] . "</td><td></td></tr>";
            if ($orderDetails['Order']['seqment_id'] != 2) {
                $address = $orderDetails['DeliveryAddress']['address'] . " " . $orderDetails['DeliveryAddress']['city'];
                $pickuptime = '';
                $printdata .="<tr><td>Delivery Date/Time:</td><td>" . date('m/d/Y h:i A', strtotime($orderDetails['Order']['pickup_time'])) . "</td></tr>";
            } else {
                $address = "Pick up Date/Time:";
                $pickuptime = date('m/d/Y h:i A', strtotime($orderDetails['Order']['pickup_time']));
            }
            $printdata .="<tr><td>" . $address . "</td><td>" . $pickuptime . "</td></tr>";

            $printdata .="</table></td></tr>";

            $printdata .="<tr><td><table style='border:2px solid black;width:100%;'><tr><td><strong>Order Detail</strong></td></tr>";

            foreach ($itemss as $dataItem) {
                $printdata .=$dataItem;
                $printdata .="<tr><td style='border-top:1px solid black;'></td><td style='border-top:1px solid black;'></td></tr>";
            }
            $printdata .="</table></td></tr>";
            $printdata .="<tr><td></br></td></tr>";
            $printdata .="<tr><td><table style='border:2px solid black;width:100%;'>";
            $printdata .="<tr><td style='width:82%;'>Sub-Total:</td><td style='width:17.8%;'>$" . number_format($amount, 2) . "</td></tr>";
            if($orderDetails['Order']['coupon_discount']>0){
               $printdata .="<tr><td style='width:82%;'>Coupon Discount: " . $orderDetails['Order']['coupon_code'] . "</td><td>-$" . $orderDetails['Order']['coupon_discount'] . "</td></tr>";
            }
            if(!empty($orderDetails['OrderItemFree'])){
               $freeInt=0;
               $freeItemArr=array();
               foreach($orderDetails['OrderItemFree'] as $freeItem){
                  $itemName= $this->Item->find('first',array('conditions' => array('Item.id' =>$freeItem['item_id']), 'fields' => array('name')));
                  if(!empty($itemName['Item']['name'])){
                     $printdata .="<tr><td style='width:82%;'>Item offers: ".$freeItem['free_quantity']." ".$itemName['Item']['name'] . "</td><td>-$" . $freeItem['price']. "</td></tr>";
                  }
               }
            }
            if($orderDetails['Order']['service_amount']>0){
               $printdata .="<tr><td style='width:82%;'>Service Fee:</td><td>$" . $orderDetails['Order']['service_amount'] . "</td></tr>";
            }
            if($orderDetails['Order']['delivery_amount']>0){
               $printdata .="<tr><td style='width:82%;'>Delivery Fee:</td><td>$" . $orderDetails['Order']['delivery_amount'] . "</td></tr>";
            }
            if($orderDetails['Order']['tax_price']>0){
               $printdata .="<tr><td style='width:82%;'>Tax:</td><td>$" . $orderDetails['Order']['tax_price'] . "<br/></td></tr>";
            }
            if($orderDetails['Order']['tip']>0){
               $printdata .="<tr><td style='width:82%;'>Tip:</td><td>$" . $orderDetails['Order']['tip'] . "<br/></td></tr>";
            }
            $printdata .="<tr><td style='width:82%;'>Total:</td><td>$" . $orderDetails['Order']['amount'] . "</td></tr>";
            $printdata .="<tr><td><br/>*Special Instructions Section*</td></tr>";
            $printdata .="<tr><td>" . $orderDetails['Order']['order_comments'] . "</td></tr>";
            //$printdata .="<tr><td><br/>iOrderFoods.com</td></tr>";
            $printdata .="<tr><td><br/>We kindly ask you not to reply to this e-mail but instead contact us via phone call.</td></tr>";
            $printdata .="</table></td></tr>";
            $printdata .="</table></td></tr>";
            $printdata .="</table>";
            return $printdata;
        }
    }
    
    function getIntervalName($intervalid = null) {
        App::import('Model', 'Interval');
        $this->Interval = new Interval();
        $intervalName = $this->Interval->getIntervalName($intervalid);
        return $intervalName;
    }
    
     /* ------------------------------------------------
      Function name:getStoreTime()
      Description : return time range array for admin
      created:10/2/2016
      ----------------------------------------------------- */

    function getStoreTime($startTime = null, $endTime = null, $ordertype = null, $storeId = null) {
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
        $reachEndTime = false;
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
                $reachEndTime = "23:59:00";
                $reachEndTimeInterval = $intervalTime;
            } else {
                $timeRange[$intervalTimeforValue] = $intervalTime;
            }
            $tNow = $tNow + 300;
            $i++;
        }
        if ($reachEndTime){
            $timeRange[$reachEndTime] = $reachEndTimeInterval;
        }
        return $timeRange;
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
            //$deliveryInterval = 60;
            $deliveryInterval = 0;
        }
        //Check Day (i.e. Today/ another)
        if ($today) {
            //$nowDate = date("Y-m-d H:i:s");
            $nowDate=$this->getcurrentTime($storeId,1);
            $minute = date("i");
            if ($minute % 5 != 0) {
                $mod = $minute % 5;
                $rem = 5 - $mod;
                $nowDate = date("Y-m-d H:i:s", strtotime((date("Y-m-d H:") . $minute . ":00")) + ($rem * 60));
            }  
            $currentStoreactual = date("H:i:s", strtotime($this->storeTimeZoneUser('', $nowDate)));
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
            //echo "CurrentTime3".$currentTime."<br>";
        } else {
            $currentTime = date("H:i:s", (strtotime($startTime) + ($deliveryInterval * 60)));
        }
        return $currentTime;
    }
    
    function storeTimeZoneUser($timezoneId = null, $dateToconvert = null,$storeId=null) {
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $timezone = date_default_timezone_get(); //get server time zone
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));
        if (!empty($storeInfo['Store']['time_zone_id'])) {
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
     //Get Todays Order     
    function getTodaysOrder($storeId = null) {
        if ($storeId) {
            App::import('Model', 'Order');
            $this->Order = new Order();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone($storeId,'', date('Y-m-d H:i:s')))));
            $totalorders = $this->Order->getTodaysOrder($storeId, $current_date);
            return $totalorders;
        }
    }
     //Get pre-Order
    function getPreOrder($storeId = null) {
        if ($storeId) {
            App::import('Model', 'Order');
            $this->Order = new Order();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone($storeId,'', date('Y-m-d H:i:s')))));
            $totalPreOrders = $this->Order->getPreOrder($storeId, $current_date);
            return $totalPreOrders;
        }
    }

    //get todays Pending Order
    function getTodaysPendingOrder($storeId = null) {
        if ($storeId) {
            App::import('Model', 'Order');
            $this->Order = new Order();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone($storeId,'', date('Y-m-d H:i:s')))));
            $totalorders = $this->Order->getTodaysPendingOrder($storeId, $current_date);
            return $totalorders;
        }
    }

    //get todays Bookings Request
    function getTodaysBookingRequest($storeId = null) {
        if ($storeId) {
            App::import('Model', 'Booking');
            $this->Booking = new Booking();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone($storeId,'', date('Y-m-d H:i:s')))));
            $totalorders = $this->Booking->getTodaysBookingRequest($storeId, $current_date);
            return $totalorders;
        }
    }

    //get todays pending Bookings Request
    function getTodaysPendingBookings($storeId = null) {
        if ($storeId) {
            App::import('Model', 'Booking');
            $this->Booking = new Booking();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone($storeId,'', date('Y-m-d H:i:s')))));
            $pendingbookings = $this->Booking->getTodaysPendingBookings($storeId, $current_date);
            return $pendingbookings;
        }
    }
    
    //For Push Notification
   function orderPushNotification($pushOrderId=null){
      try{
            App::import('Controller', 'AdminServices');
            $orderNoti = new AdminServicesController;
            if(!empty($pushOrderId)){
            $orderNoti->orderNotification($pushOrderId);          
            }
        }catch (Exception $e) {
            
         }
    }
    function bookingPushNotification($pushBookingId=null){
      try{
            App::import('Controller', 'AdminServices');
            $bookingNoti = new AdminServicesController;
            if(!empty($pushBookingId)){
            $bookingNoti->bookingNotification($pushBookingId);          
            }
        }catch (Exception $e) {
            
         }
    }
    public function cropImage($path=null, $imgFolder=null,$imagename=null,$newWidth=null,$newHeight=null,$imageType=null) {
        if (file_exists($path)) {
            // full path for thumb image
            $full_thumb_url = WWW_ROOT . $imgFolder."/thumb/" . $imagename;;
            list($width, $height, $type, $attr) = getimagesize($path);
            if (empty($newHeight) && empty($newWidth)) {
                $newHeight = 150;
                $newWidth = 150;
            }
           
            $responseT = $this->getResize($height, $width, $newHeight, $newWidth, $path, $full_thumb_url,$imageType);
            return $responseT;
        }
    }
    
    public function getResize($height, $width, $newHeight, $newWidth, $filenamePass, $uploadPath,$imageType=null) {

	//$imageInfo=getimagesize($filenamePass);
        //$extension = $this->getExtension($uploadPath);
        //$extension = strtolower($extension);
	//$extension=$imageInfo['mime'];
        $imagePas = imagecreatetruecolor($newWidth, $newHeight);
       
        if ($imageType=="jpg") {			
            $image = imagecreatefromjpeg($filenamePass);
            imagecopyresampled($imagePas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagejpeg($imagePas, $uploadPath, 100);
            return 1;
        } else if ($imageType=="png") {	
            $image = imagecreatefrompng($filenamePass);
            //imagecopyresampled($imagePas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            //imagepng($imagePas,$uploadPath ,9);

            imagealphablending($imagePas, false);
            imagesavealpha($imagePas, true);
            $transparent = imagecolorallocatealpha($imagePas, 255, 255, 255, 127);
            imagefilledrectangle($imagePas, 0, 0, $newWidth, $newHeight, $transparent);
            imagecopyresampled($imagePas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagepng($imagePas, $uploadPath);
            imagedestroy($imagePas);
            imagedestroy($image);
            return 1;
        } else if($imageType=="gif"){		
            $image = imagecreatefromgif($filenamePass);
            imagecopyresampled($imagePas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagegif($imagePas, $uploadPath);
            return 1;
        }
        return 0;
    }
    
    function getExtension($strPass) {
        $ioata = strrpos($strPass, ".");
        if (!$ioata) {
            return "";
        }
        $lens = strlen($strPass) - $ioata;
        $exten = substr($strPass, $ioata + 1, $lens);
        return $exten;
    }
    
   public function getaddonSize($sizeid = null,$storeId=null) {
        App::import('Model', 'AddonSize');
        $this->AddonSize = new AddonSize();
        $Sizedetail = $this->AddonSize->getAddonSizeDetail($sizeid, $storeId);
        return $Sizedetail;
    }
    
   function addOrdinalNumberSuffix($num) {
        if (!in_array(($num % 100),array(11,12,13))){
          switch ($num % 10) {
            // Handle 1st, 2nd, 3rd
            case 1:  return $num.'st';
            case 2:  return $num.'nd';
            case 3:  return $num.'rd';
          }
        }
        return $num.'th';
    }
}
