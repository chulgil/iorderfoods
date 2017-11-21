<?php

App::uses('StoreAppController', 'Controller');

class BookingsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common', 'Paypal', 'Webservice');
    public $helper = array('Encryption', 'Dateform', 'Common');
    public $uses = array('Order', 'Item', 'ItemPrice', 'ItemType', 'Size', 'OrderItem', 'StoreReview', 'Favorite', 'Topping', 'OrderTopping', 'Booking', 'StorePrintHistory', 'Store');

    public function beforeFilter() {
        parent::beforeFilter();
        $adminfunctions = array('index', 'manageBooking');
        if (in_array($this->params['action'], $adminfunctions) && !$this->Common->checkPermissionByaction($this->params['controller'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List of Dine In booking order
      created:17/8/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $value = "";
        $criteria = "Booking.store_id =$storeID AND Booking.is_active=1 AND Booking.is_deleted=0 AND User.role_id IN ('4','5')";
        $todaydate = $this->Common->sa_gettodayDate();
        if ($this->Session->read('BookingSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('BookingSearchData'), true);
        } else {
            $this->Session->delete('BookingSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('BookingSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Order']['keyword'])) {
                $value = trim($this->request->data['Order']['keyword']);
                $criteria .= " AND (User.fname LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR Booking.id LIKE '%" . $value . "%')";
            }
            if (!empty($this->request->data['Order']['todayBookingRq'])) {
                $criteria .= ' AND Booking.is_active=1 AND DATE(convert_tz(Booking.reservation_date,"' . $this->server_offset . '","' . $this->store_offset . '"))="' . $todaydate . '"';
            }
            if (!empty($this->request->data['Order']['pendingBookingRq'])) {
                $criteria .= ' AND Booking.booking_status_id=1 AND DATE(convert_tz(Booking.reservation_date,"' . $this->server_offset . '","' . $this->store_offset . '"))="' . $todaydate . '"';
            }
            if (!empty($this->request->data['Booking']['is_replied'])) {
                if ($this->request->data['Booking']['is_replied'] == 1) {
                    $repliedID = trim($this->request->data['Booking']['is_replied']);
                }
                if ($this->request->data['Booking']['is_replied'] == 2) {
                    $repliedID = 0;
                }
                $criteria .= " AND (Booking.is_replied =$repliedID)";
            }
            if (!empty($this->request->data['OrderStatus']['id'])) {
                $status = trim($this->request->data['OrderStatus']['id']);
                $criteria .= " AND (Booking.booking_status_id =$status)";
            }
        }
        $this->Booking->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ),
                'BookingStatuse' => array(
                    'className' => 'BookingStatuse',
                    'foreignKey' => 'booking_status_id'
                ),
                'StorePrintHistory' => array(
                    'className' => 'StorePrintHistory',
                    'foreignKey' => false,
                    'conditions' => array('Booking.id=StorePrintHistory.order_id')
                )
            ),
                )
                , false
        );
        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Booking.reservation_date' => 'DESC'));
        $orderdetail = $this->paginate('Booking');
        $store = $this->Store->fetchStoreDetail($storeID, $merchantId);
        $this->set('store', $store['Store']);
        $this->set('list', $orderdetail);
        $this->loadModel('BookingStatus');
        $status = $this->BookingStatus->statusList($storeID);
        $arr = array_diff($status, array('Available'));
        $this->set('statusList', $arr);
        $this->set('keyword', $value);
        $this->Booking->unbindModel(array('belongsTo' => array('BookingStatuse', 'StorePrintHistory')), true);
        $cData = $this->Booking->find('all', array('conditions' => $criteria));
        $earray = array();
        foreach ($cData as $data) {
            $resdate = strtotime($data['Booking']['reservation_date']);
            $temp['id'] = $data['Booking']['id'];
            $temp['title'] = date('h:i A', $resdate) . ', ' . $data['Booking']['number_person'] . ' Person';
            $temp['start'] = date('Y-m-d', $resdate);
            $temp['url'] = '/bookings/manageBooking/' . $this->Encryption->encode($data['Booking']['id']);
            array_push($earray, $temp);
        }
        $cJson = json_encode($earray);
        $this->set('cJson', $cJson);
    }

    /* ------------------------------------------------
      Function name:manageBooking()
      Description:Send the notification to customer of Dine In booking status
      created:17/8/2015
      ----------------------------------------------------- */

    public function manageBooking($EncryptBookingID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $bookingId = $this->Encryption->decode($EncryptBookingID);
        if(empty($bookingId)){
            $bookingId = $this->request->data['Data']['id'];
        }
        $this->loadModel('Store');
        $storeEmail = $this->Store->fetchStoreDetail($storeId);
        $criteria = "Booking.store_id =$storeId AND Booking.is_deleted=0 AND Booking.id= $bookingId";
        $this->loadModel('User');
        $this->User->bindModel(array('belongsTo' => array('CountryCode' => array('className' => 'CountryCode', 'foreignKey' => 'country_code_id', 'fields' => array('code')))), false);
        $this->Booking->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ), 'BookingStatus' => array(
                    'className' => 'BookingStatus',
                    'foreignKey' => 'booking_status_id'
                )
            ),
                )
                , false
        );
        if ($this->request->is(array('post', 'put'))) {

            $this->request->data = $this->Common->trimValue($this->request->data);
            $template_type = 'booking_status';
            $fullName = trim($this->request->data['Data']['name']);
            $order = $this->request->data['Data']['ordercode'];
            $number = $this->request->data['Data']['number'];
            $countryCode = $this->request->data['Data']['code'];
            $pNumber = $this->request->data['Data']['phone'];
            $special_request = $this->request->data['Data']['special_request'];
            $datetime = $this->request->data['Data']['datetime'];
            $bookformatedDate = $this->Common->storeTimeFormate($datetime, 1);
            $st = $this->request->data['BookingStatus']['name'];
            $phoneNumber=$countryCode.$pNumber;
            //After date change with store time zone
            $storeTimeAm="";
            $dateTimeArr= explode(" ", $bookformatedDate);
            $storeDate=$dateTimeArr[0];
            $storeTime=$dateTimeArr[1];
            
            if(isset($dateTimeArr[2]) && !empty($dateTimeArr[2])){
                $storeTimeAm=$dateTimeArr[2];
                $storeTime=$storeTime.' '.$storeTimeAm;
            }
            
            $comment = "";
            switch ($st) {
                case "1":
                    $status = 'Pending';
                    if (!empty($this->request->data['Data']['comment'])) {
                        $comment.= trim($this->request->data['Data']['comment']);
                    }
                    break;
                case "2":
                    $status = 'Available';
                    break;
                case "3":
                    $status = 'Not Available';
                    break;
                case "4":
                    $status = 'Cancel';
                    $template_type = 'cancel_booking';
                    if (!empty($this->request->data['Data']['comment'])) {
                        $comment = "Admin comment: ";
                        $comment.= trim($this->request->data['Data']['comment']);
                    }
                    break;
                default:
                    $status = 'Booked';
                    $template_type = 'confirm_booking';
                    if (!empty($this->request->data['Data']['comment'])) {
                        $comment = "Admin comment: ";
                        $comment.= trim($this->request->data['Data']['comment']);
                    }
            }
            $this->loadModel('EmailTemplate');
            $emailSuccess = $this->EmailTemplate->storeTemplates($storeId, $merchantId, $template_type);
            if ($emailSuccess) {
                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                $subject = $emailSuccess['EmailTemplate']['template_subject'];
            }
            if ($this->request->data['Data']['emailnotify'] == 1) {
                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                $emailData = str_replace('{STATUS}', $status, $emailData);
                $emailData = str_replace('{BOOKING_PEOPLE}', $number, $emailData);
                $bookformatedDate = $this->Common->storeTimeFormate($datetime, 1);
                $emailData = str_replace('{BOOKING_DATE_TIME}', $bookformatedDate, $emailData);
                $url = "http://" . $storeEmail['Store']['store_url'];
                $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                $emailData = str_replace('{STORE_PHONE}', $storeEmail['Store']['phone'], $emailData);
                $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                $storePhone = $storeEmail['Store']['phone'];
                $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                $emailData = str_replace('{COMMENT}', $comment, $emailData);
                $subject = ucwords(str_replace('_', ' ', $subject));
                $this->Email->to = $this->request->data['Data']['to'];
                $this->Email->subject = $subject;
                $this->Email->from = $storeEmail['Store']['email_id'];
                $this->set('data', $emailData);
                $this->Email->template = 'template';
                $this->Email->smtpOptions = array(
                    'port' => "$this->smtp_port",
                    'timeout' => '30',
                    'host' => "$this->smtp_host",
                    'username' => "$this->smtp_username",
                    'password' => "$this->smtp_password"
                );
                $this->Email->sendAs = 'html'; // because we like to send pretty mail
                try {
                    $this->Email->send();
                    $this->Session->setFlash(__("Message send successfully."), 'alert_success');
                } catch (Exception $e) {
                    
                }
            }
            if ($this->request->data['Data']['smsnotify'] == 1) {
                /*                 * ************sms gateway data************* */
                $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                $smsData = str_replace('{REQUEST_ID}', $order, $smsData);
                $smsData = str_replace('{BOOKING_STATUS}', $status, $smsData);
                
                $smsData = str_replace('{BOOKING_DATE}', $storeDate, $smsData);
                $smsData = str_replace('{BOOKING_TIME}', $storeTime, $smsData);
                $smsData = str_replace('{NO_PERSON}', $number, $smsData);
                $smsData = str_replace('{SPECIAL_REQUEST}', $special_request, $smsData);
                $smsData = str_replace('{CONTACT_PERSON}', $phoneNumber, $smsData);
                
                $smsData = str_replace('{COMMENT}', $comment, $smsData);
                $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                /*                 * ***********end sms gateway data********** */
                $message = $smsData;
                $this->request->data['Data']['code'];
                $mob = $this->request->data['Data']['code'] . "" . str_replace(array('(', ')', ' ', '-'), '', $this->request->data['Data']['phone']);
                $this->Common->sendSmsNotification($mob, $message);
                $this->Session->setFlash(__("SMS send successfully."), 'alert_success');
            }

            $this->Booking->id = $this->request->data['Data']['id'];
            $bookingStatus = trim($this->request->data['BookingStatus']['name']);
            $this->Booking->saveField("booking_status_id", $bookingStatus);
            $this->Booking->saveField("admin_comment", $this->request->data['Data']['comment']);
            $this->Booking->id = $this->request->data['Data']['id'];
            $replied = 1;
            $this->Booking->saveField("is_replied", $replied);
            $this->_updateEvent($this->request->data['Data']['id']);
            $this->redirect(array('action' => 'index', 'controller' => 'bookings'));
        }
        $this->paginate = array('recursive' => 3, 'conditions' => array($criteria), 'order' => array('Booking.created' => 'DESC'));
        $orderdetail = $this->paginate('Booking');
        $this->set('list', $orderdetail);
        $this->loadModel('BookingStatus');
        $status = $this->BookingStatus->statusList($storeId);
        $arr = array_diff($status, array('Available'));
        $this->set('statusList', $arr);
    }

    /* --------------------------------------
      Function Name:update_events
      Description:To sync  events based on particular id of a claim from scheduler side
      Created By:smartData
      Date:14 Dec 2016
      ----------------------------------------- */

    private function _updateEvent($booking_id = null) {
        if (empty($booking_id)) {
            return false;
        }
        $this->autoRender = false;
        $this->loadModel("GoogleToken");
        $this->loadModel('Booking');
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Booking->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id','store_name', 'address', 'city', 'state', 'zipcode', 'time_zone_id')
                ),
                'BookingStatuse' => array(
                    'className' => 'BookingStatuse',
                    'foreignKey' => 'booking_status_id'
                )
            ),
                )
                , false
        );
        $this->Store->bindModel(
                array(
            'belongsTo' => array(
                'TimeZone' => array(
                    'className' => 'TimeZone',
                    'foreignKey' => 'time_zone_id',
                )
            ),
                )
                , false
        );
        $bookingData = $this->Booking->find('first', array('recursive' => 2, 'conditions' => array('Booking.id' => $booking_id)));
        $TOKEN = $this->GoogleToken->find('first', array('conditions' => array('user_id' => $bookingData['Booking']['user_id'])));
        if (!empty($TOKEN) && !empty($bookingData['Booking']['cal_event_id'])) {
            $this->loadModel('MainSiteSetting');
            $keyData = $this->MainSiteSetting->find('first', array('fields' => array('google_client_id', 'google_client_secret', 'google_redirect_uri', 'google_application_name', 'google_api_key')));
            $google_client_id = $keyData['MainSiteSetting']['google_client_id'];
            $google_client_secret = $keyData['MainSiteSetting']['google_client_secret'];
            $google_api_key = $keyData['MainSiteSetting']['google_api_key'];
            $google_redirect_uri = $keyData['MainSiteSetting']['google_redirect_uri'];
            App::import('Vendor', 'google_data_link/google_event');
            $client = new Google_Client();
            $client->setApplicationName($keyData['MainSiteSetting']['google_application_name']);
            $client->setClientId($google_client_id);
            $client->setClientSecret($google_client_secret);
            $client->setDeveloperKey($google_api_key);
            $cal = new Google_CalendarService($client);
            $event = new Google_Event();
            $client->setAccessToken($TOKEN['GoogleToken']['response_form']);
            if ($client->getAccessToken()) {
                $event_id = $bookingData['Booking']['cal_event_id']; //calendar event id
                $calender_id = $TOKEN['GoogleToken']['email'];
                $caleventtitle = "Reservation for " . $bookingData['Booking']['number_person'] . " person";
                $event->setSummary($caleventtitle);
                $location = $bookingData['Store']['store_name'] . ',' . $bookingData['Store']['address'] . ', ' . $bookingData['Store']['city'] . ', ' . $bookingData['Store']['state'] . ', ' . $bookingData['Store']['zipcode'];
                $event->setLocation($location);
                $description = "Your reservation status is " . $bookingData['BookingStatuse']['name'] . ". " . $bookingData['Booking']['admin_comment'];
                $event->setDescription($description);

                $rdate = date_create($bookingData['Booking']['reservation_date']);
                $date = date_format($rdate, "Y-m-d");
                $sttime = date_format($rdate, "H:i:s");
                $timestamp = strtotime(date_format($rdate, "H:i:s")) + 60 * 60;
                $edtime = date('H:i:s', $timestamp);

                if (empty($bookingData['Store']['TimeZone']['code'])) {
                    $bookingData['Store']['TimeZone']['code'] = "US/Pacific";
                }
                $offset = (new DateTime('now', new DateTimeZone($bookingData['Store']['TimeZone']['code'])))->format('P');
                $stDate = $date . 'T' . $sttime . $offset;
                $edDate = $date . 'T' . $edtime . $offset;
                $caldata = $cal->calendars->get($calender_id); //add new bp
                if ($caldata) {
                    if ($caldata['timeZone']) {
                        $usercalander_time_zone = $caldata['timeZone'];
                        $startdt = $bookingData['Booking']['reservation_date'];
                        $returnoffset = $this->timezonecheckDST($usercalander_time_zone, $startdt);
                        if ($returnoffset) {
                            $stDate = $date . 'T' . $sttime . ".000" . $returnoffset;
                            $edDate = $date . 'T' . $edtime . ".000" . $returnoffset;
                        }
                    }
                }

                $start = new Google_EventDateTime();
                $start->setDateTime($stDate);
                $event->setStart($start);
                $end = new Google_EventDateTime();
                $end->setDateTime($edDate);
                $event->setEnd($end);
                $cal->events->update($calender_id, $event_id, $event);
            }
        }
    }

    /* --------------------------------------
      Function Name:timezonecheckDST
      Description:To get the DST & Offset of given timezone & date
      Date: 09 April 2015
      ----------------------------------------- */

    function timezonecheckDST($tzId, $specific_date = null) {

        $dtz = new DateTimeZone($tzId);
        $time_in_country = new DateTime($specific_date, $dtz);
        if ($time_in_country) {
            $offset = $dtz->getOffset($time_in_country);
            $offsetString = sprintf('%+03d:%02u', $offset / 3600, abs($offset) % 3600 / 60);
            return $offsetString;
        } else {
            return '-05:00'; //eastern
        }
    }

    function PrintForKitchen() {
        $this->Common->printdemo();
    }

    function checkPrinterConnection() {
        
    }

    function testPrinting() {
        
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Booking');
            $this->Booking->bindModel(
                    array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id'
                    )
                ),
                    )
                    , false
            );
            $storeID = $this->Session->read('admin_store_id');
            $criteria = "Booking.store_id =$storeID AND Booking.is_active=1 AND Booking.is_deleted=0 AND User.role_id IN ('4','5')";
            $searchData = $this->Booking->find('all', array('fields' => array('Booking.id', 'User.fname', 'User.lname'), 'conditions' => array('OR' => array('User.fname LIKE' => '%' . $_GET['term'] . '%', 'User.lname LIKE' => '%' . $_GET['term'] . '%'), $criteria), 'group' => 'User.fname'));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['User']['fname'], 'value' => $val['User']['fname'], 'desc' => $val['User']['fname'] . " " . $val['User']['lname']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

}
