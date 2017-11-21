<?php

App::uses('StoreAppController', 'Controller');

//App::import('Vendor', 'google_data_link', array('file' => 'google_data_link' . DS . 'google_event.php'));

class PannelsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common', 'Webservice');
    public $helper = array('Encryption');
    public $uses = array('OrderItem', 'StoreContent', 'Store', 'BookingStatus', 'Booking', 'StoreReview', 'OrderItem', 'Item', 'StoreHoliday', 'StoreReviewImage');

    public function beforeFilter() {
        parent::beforeFilter();

        $roleId = AuthComponent::User('role_id');
        if ($roleId == 4 || $roleId == 5) {
            $storeId = $this->Session->read('store_id');
            $merchantId = $this->Session->read('merchant_id');
            $this->loadModel('StoreAvailability');
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
            $availabilityInfo = $this->StoreAvailability->getStoreAvailabilityDetails($storeId);
            $this->set('availabilityInfo', $availabilityInfo);

            $store_break_data = $this->Store->fetchStoreDetail($storeId, $merchantId);
            $this->set('store_break_data', $store_break_data);

            $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));


            $closedDay = array();
            $storeavaibilityInfo = $this->StoreAvailability->getclosedDay($storeId);
            $daysarray = array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);


            $end_date = date('Y-m-d', date(strtotime("+7 day", strtotime($current_date))));
            $holidayDates = $this->StoreHoliday->getholidaydate($storeId, $current_date, $end_date);
            $HolidayDay = array();
            if (!empty($holidayDates)) {
                foreach ($holidayDates as $key => $date) {
                    if (!empty($date)) {
                        $datetime = DateTime::createFromFormat('Y-m-d', $date);
                        $day = strtolower($datetime->format('l'));
                        if (array_key_exists($day, $daysarray)) {
                            $HolidayDay[$key] = $daysarray[$day];
                        }
                    }
                }
            }

            if (!empty($storeavaibilityInfo)) {
                foreach ($storeavaibilityInfo as $key => $value) {

                    if (!empty($value)) {
                        $day = strtolower($value['StoreAvailability']['day_name']);
                        if (array_key_exists($day, $daysarray)) {
                            $closedDay[$key] = $daysarray[$day];
                        }
                    }
                }
            }
            $closedDay = array_unique(array_merge($HolidayDay, $closedDay));
            $this->set('closedDay', $closedDay);
        }
    }

    public function oauthClient() {
        $userid = AuthComponent::User('id');
        $this->loadModel('GoogleToken');
        $googel_token = $this->GoogleToken->find('count', array('conditions' => array('GoogleToken.user_id' => $userid)));
        if ($googel_token == 0) {
            $protocol = "http://";
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
                $protocol = "https://";
            }
            $this->loadModel('MainSiteSetting');
            $resultSet = $this->MainSiteSetting->find('first', array('fields' => array('google_client_id', 'google_redirect_uri')));
            if (!empty($resultSet)) {
                $google_client_id = $resultSet['MainSiteSetting']['google_client_id'];
                $google_redirect_uri = $resultSet['MainSiteSetting']['google_redirect_uri'];
                $uriredirect = $protocol . $_SERVER['HTTP_HOST'] . $google_redirect_uri . '&client_id=' . $google_client_id;
                $authUrl = 'https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=' . $uriredirect . '&scope=https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://mail.google.com/ https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/calendar.readonly http://www.google.com/m8/feeds/&access_type=offline&approval_prompt=force';
                $this->set('authUrl', $authUrl);
            }
        }
    }

    /* ------------------------------------------------
      Function name:myBookings()
      Description:List of User Bookings
      created:31/8/2015
      ----------------------------------------------------- */

    public function myBookings($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->userLoginCheck();
        $this->layout = $this->store_inner_pages;
        $this->oauthClient();
        $decrypt_userId = AuthComponent::User('id');

        if (isset($encrypted_storeId) && !empty($encrypted_storeId)) {
            $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
            if ($decrypt_storeId == "clear") {
                $decrypt_storeId = $this->Session->read('store_id');
                $encrypted_storeId = $this->Encryption->encode($decrypt_storeId);
            }
        } else {
            $decrypt_storeId = $this->Session->read('store_id');
            $encrypted_storeId = $this->Encryption->encode($decrypt_storeId);
        }
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $this->Booking->bindModel(array('belongsTo' => array('BookingStatus')), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Booking->bindModel(array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name'),
                    'type' => 'INNER',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                ))
                ), false);

        $value = "";
        $fromdate = "";
        $enddate = "";
        $encryptedlock_storeId = "";
        if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
            if ($this->params->pass[0] == 'clear') {
                $this->Session->delete('MyreservationSearchData');
            }
        }


        if ($this->Session->read('MyreservationSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('MyreservationSearchData'), true);
        } else {
            $this->Session->delete('MyreservationSearchData');
        }

        if ($this->Session->read('MyreservationSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('MyreservationSearchData'), true);
            $fromdate = $this->request->data['from_date'];
            $enddate = $this->request->data['to_date'];
            $encryptedlock_storeId = $this->request->data['lock'];
//            $encrypted_storeId = $this->Encryption->encode($encrypted_storeId);
        } else {
            $this->Session->delete('MyreservationSearchData');
        }

        if (!empty($this->params->query)) {
            $conditions1 = array();
            $this->Session->write('MyreservationSearchData', json_encode($this->params->query));
            if (!empty($this->params->query['from_date'])) {
                $fromdate = $this->Dateform->formatDate(trim($this->params->query['from_date']));
                $conditions1['Date(Booking.reservation_date) >='] = $fromdate;
            }

            if (!empty($this->params->query['to_date'])) {
                $enddate = $this->Dateform->formatDate(trim($this->params->query['to_date']));
                $conditions1['Date(Booking.reservation_date) <='] = $enddate;
            }

            if (!empty($this->params->query['lock'])) {
                $merchantId = $this->Session->read('merchant_id');
                $lock_storeId = $this->Encryption->decode($this->params->query['lock']);
                $decrypt_merchantId = $merchantId;
                $encryptedlock_storeId = $this->Encryption->encode($lock_storeId);
            }
            if (!empty($lock_storeId)) {
                $conditions = array('Booking.is_deleted' => 0, 'Booking.is_active' => 1, 'Booking.user_id' => $decrypt_userId, 'Booking.store_id' => $lock_storeId);
                $conditions = array_merge($conditions, $conditions1);
                $this->paginate = array(
                    'conditions' => $conditions,
                    'order' => 'Booking.created DESC',
                    'limit' => 9
                );
            } else {
                $conditions = array('Booking.is_deleted' => 0, 'Booking.is_active' => 1, 'Booking.user_id' => $decrypt_userId);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array(
                    'conditions' => $conditions,
                    'order' => 'Booking.created DESC',
                    'limit' => 9
                );
            }
        } else {
            $this->paginate = array(
                'conditions' => array('Booking.user_id' => $decrypt_userId, 'Booking.is_active' => 1, 'Booking.is_deleted' => 0),
                'order' => 'Booking.created DESC',
                'limit' => 9
            );
        } $myBookings = $this->paginate('Booking');
        $this->set("fromdate", $fromdate);
        if (empty($enddate)) {
            $enddate = $fromdate;
        } $this->set("endDate", $enddate);
        $this->set("encryptedlock_storeId", $encryptedlock_storeId);

        $avalibilty_status = $this->Common->checkStoreAvalibility($decrypt_storeId); // I will check the time avalibility of the store
        if ($avalibilty_status != 1) {
            $setPre = 1;
        } else {
            $setPre = 0;
        }
        $this->loadModel('Store');
        $this->loadModel('StoreAvailability');
        $this->loadModel('StoreHoliday');
        $this->loadModel('Booking');
        $this->loadModel('StorePrintHistory');


        $store = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);

        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));

        if ($store['Store']['dineinblackout_limit']) {
            $current_date = date("Y-m-d", strtotime($current_date . ' +' . $store['Store']['dineinblackout_limit'] . ' day'));
        }

        $today = 1;
        $orderType = 1;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        $time_break = $finaldata['time_break'];
        $store_data = $finaldata['store_data'];
        $storeBreak = $finaldata['storeBreak'];
        $time_range = $finaldata['time_range'];
        $current_date = $finaldata['currentdate'];

        $i = 1;
        $number_person = array();
        for ($i; $i < 30; $i++) {
            $number_person[$i] = $i;
        }

        $explodeVal = explode("-", $current_date);
        $currentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $this->set(compact('storeBreak', 'myBookings', 'setPre', 'number_person', 'time_break', 'time_range', 'store_data', 'store', 'encrypted_storeId', 'encrypted_merchantId', 'encrypted_userId', 'currentDateVar'));

        if ($this->request->is('post')) {
            $this->request->data['Booking']['store_id'] = $this->Session->read('store_id');
            $this->request->data['Booking']['user_id'] = AuthComponent::User('id');
            $reservationDate = $this->Dateform->formatDate($this->request->data['Booking']['start_date']);
            $ResTime = $this->request->data['Store'] ['pickup_hour'] . ':' . $this->request->data['Store']['pickup_minute'] . ':00';
            $reservationDateTime = $reservationDate . " " . $ResTime;

            $this->request->data['Booking']['reservation_date'] = $reservationDateTime;
            $save_result = $this->Booking->saveBookingDetails($this->data); // call on model to save data
            if ($store['Store']['is_dinein_printer'] == 1) {
                $last_id = $this->Booking->getLastInsertId();
                $aPrintData = array();
                $aPrintData['id'] = '';
                $aPrintData['merchant_id'] = $this->Session->read('merchant_id');
                $aPrintData['store_id'] = $this->Session->read('store_id');
                $aPrintData['order_id'] = $last_id;
                $aPrintData['order_number'] = $last_id;
                $aPrintData['type'] = '3'; //DineIn Printer
                $this->StorePrintHistory->saveStorePrintHistory($aPrintData);
            }

            if ($save_result) {
                //$pushBookingId = $this->Booking->getLastInsertId();
                //$this->Webservice->bookingPushNotification($pushBookingId);
                $template_type = 'customer_dine_in_request';
                $this->loadModel('DefaultTemplate');
                $fullName = "Admin";
                $number_person = $this->data['Booking']['number_person']; //no of person
                $start_date = $this->Dateform->formatDate($this->data['Booking']['start_date']);
                $date = new DateTime($start_date);
                $start_date = $date->format('n/j/Y');
                //$start_time = date('H:i a', strtotime($ResTime));
                $start_time = $this->Common->storeTimeFormateUser($ResTime);

                $customer_name = AuthComponent::User('fname') . " " . AuthComponent::User('lname');
                if ($this->data['Booking']['special_request']) {
                    $special_request = $this->data['Booking']['special_request'];
                } else {
                    $special_request = "N/A";
                }
                //$emailSuccess = $this->EmailTemplate->storeTemplates($decrypt_storeId, $decrypt_merchantId, $template_type);
                $emailSuccess = $this->DefaultTemplate->find('first', array('conditions' => array('DefaultTemplate.template_code' => $template_type, 'DefaultTemplate.is_default' => 1)));
                if ($emailSuccess) {
                    if (($store ['Store'] ['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                        $storeEmail = $store['Store']['notification_email'];
                    } else {
                        $storeEmail = $store['Store']['email_id'];
                    }
                    $contactPerson = AuthComponent::User('fname') . " " . AuthComponent::User('lname') . " " . AuthComponent::User('phone');
                    $customerEmail = trim(AuthComponent::User('email'));
                    $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $emailData = str_replace('{BOOKING_DATE}', $start_date, $emailData);
                    $emailData = str_replace('{BOOKING_TIME}', $start_time, $emailData);
                    $emailData = str_replace('{NO_PERSON}', $number_person, $emailData);
                    $emailData = str_replace('{SPECIAL_REQUEST}', $special_request, $emailData);
                    $emailData = str_replace('{CUSTOMER_NAME}', $customer_name, $emailData);
                    $emailData = str_replace('{CONTACT_PERSON}', $contactPerson, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['DefaultTemplate']['template_subject']));
                    $emailData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $emailData);
                    $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];

                    $storePhone = $store['Store']['phone'];
                    $url = "http://" . $store['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);

                    $this->Email->to = $storeEmail;
                    $this->Email->subject = $subject;
                    //$this->Email->from = $customerEmail;
                    $this->Email->from = $this->front_email;
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
                    // $this->Email->delivery = 'smtp';
                    try {
                        $this->Email->send();
                    } catch (Exception $e) {
                        
                    }

                    if (($store ['Store'] ['notification_type'] == 2 || $store['Store']['notification_type'] == 3) && (!empty($store['Store'] ['notification_number']))) {
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['notification_number']);
                    } else {
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['phone']);
                    }
                    $smsData = $emailSuccess['DefaultTemplate']['sms_template'];
                    $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                    $smsData = str_replace('{BOOKING_DATE}', $start_date, $smsData);
                    $smsData = str_replace('{BOOKING_TIME}', $start_time, $smsData);
                    $smsData = str_replace('{NO_PERSON}', $number_person, $smsData);
                    $smsData = str_replace('{SPECIAL_REQUEST}', $special_request, $smsData);
                    $smsData = str_replace('{CONTACT_PERSON}', $contactPerson, $smsData);
                    $smsData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $smsData);
                    $smsData = str_replace('{STORE_PHONE}', $mobnumber, $smsData);
                    $message = $smsData;
                    $this->Common->sendSmsNotificationFront($mobnumber, $message);
                }
                $bookingId = $this->Booking->getLastInsertId();
                if (!empty($bookingId)) {
                    $this->_addEvent($bookingId);
                }
                $this->Session->setFlash(__('Your request has been submitted, you will receive a confirmation email shortly. Thank you!'), 'flash_success');
                $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
            } else {
                $this->Session->setFlash(__('Reservation Request could not be submitted, please try again'), 'flash_error');
                $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
            }
        }
    }

    /* --------------------------------------
      Function Name:add_event
      Description:To sync  when we add appointment into calendar
      Created By:smartData
      Date:9 October 2014
      ----------------------------------------- */

    private function _addEvent($bookingId = null) {
        $userid = AuthComponent::User('id');
        $this->loadModel('GoogleToken');
        $this->loadModel('Booking');
        $this->Booking->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('Store.id', 'store_name', 'address', 'city', 'state', 'zipcode', 'time_zone_id')
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
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $bookingData = $this->Booking->find('first', array('recursive' => 2, 'conditions' => array('Booking.id' => $bookingId)));
        $TOKEN = $this->GoogleToken->find('first', array('conditions' => array('GoogleToken.user_id' => $userid)));
        if (empty($TOKEN)) {
            return false;
        } elseif (empty($bookingData)) {
            return false;
        }

        App::import('Vendor', 'google_data_link/google_event');

        $this->loadModel('MainSiteSetting');
        $keyData = $this->MainSiteSetting->find('first', array('fields' => array('google_client_id', 'google_client_secret', 'google_redirect_uri', 'google_application_name', 'google_api_key')));
        $google_client_id = $keyData['MainSiteSetting']['google_client_id'];
        $google_client_secret = $keyData['MainSiteSetting']['google_client_secret'];
        $google_api_key = $keyData['MainSiteSetting']['google_api_key'];
        $google_redirect_uri = $keyData['MainSiteSetting']['google_redirect_uri'];

        $client = new Google_Client();
        $client->setApplicationName($keyData['MainSiteSetting']['google_application_name']);
        $client->setClientId($google_client_id);
        $client->setClientSecret($google_client_secret);
        $client->setDeveloperKey($google_api_key);
        //$client->setRedirectUri(REDIRECTURI);

        $cal = new Google_CalendarService($client);

        if (isset($TOKEN['GoogleToken']['response_form'])) {
            $client->setAccessToken($TOKEN['GoogleToken']['response_form']);
        }
        if ($client->getAccessToken()) {
            $calender_id = $TOKEN['GoogleToken']['email'];
            $event = new Google_Event();
            if (!empty($calender_id)) {
                $caldata = $cal->calendars->get($calender_id); //add new bp
                $description = "Your reservation status is " . $bookingData['BookingStatuse']['name'];

                $caleventtitle = "Reservation for " . $bookingData['Booking']['number_person'] . " person";

                $event->setSummary($caleventtitle);

                $location = $bookingData['Store']['store_name'] . ',' . $bookingData['Store']['address'] . ', ' . $bookingData['Store']['city'] . ', ' . $bookingData['Store']['state'] . ', ' . $bookingData['Store']['zipcode'];
                $event->setLocation($location);

                $event->setDescription($description);


//                $stDate = "2016-12-16T09:00:00-07:00";
//                $edDate = "2016-12-16T09:00:00-07:00";

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
                //echo $stDate;die;
                $start = new Google_EventDateTime();
                $start->setDateTime($stDate);
                $event->setStart($start);
                $end = new Google_EventDateTime();
                $end->setDateTime($edDate);
                $event->setEnd($end);
                try {
                    $createdEvent = $cal->events->insert($calender_id, $event);
                    if (!empty($createdEvent['id'])) {
                        $id = $createdEvent['id'];
                        $this->Booking->updateAll(array('Booking.cal_event_id' => "'$id'"), array('Booking.id' => $bookingId));
                    }
                } catch (Google_ServiceException $e) {
                    return false;
                }
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

    /* ------------------------------------------------
      Function name:cancelBooking()
      Description:Cancel User bookings
      created:31/8/2015
      ----------------------------------------------------- */

    public function cancelBooking($encrypted_storeId = null, $encrypted_merchantId = null, $encrypted_bookingId = null) {
        $this->userLoginCheck();
        $this->autoRender = false;
        $data['Booking']['id'] = $this->Encryption->decode($encrypted_bookingId);
        //$data['Booking']['is_deleted'] = 1;
        $data['Booking']['booking_status_id'] = 4;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $save_result = $this->Booking->saveBookingDetails($data); // call on model to save data
        if ($save_result) {
            $this->_deleteEvent($data['Booking']['id']);
            $store = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
            $this->loadModel('CountryCode');
            $myBookings = $this->Booking->getBookingDetailsById($data['Booking']['id']);
            $special_request = $myBookings['Booking']['special_request'];
            $countryCodeId = $myBookings['User']['country_code_id'];
            $countryCode = $this->CountryCode->find('first', array('conditions' => array('id' => $countryCodeId), 'fields' => array('code')));


            $pNumber = $myBookings['User']['phone'];
            $phoneNumber = $countryCode['CountryCode']['code'] . $pNumber;
            $template_type = "customer_dine_in_cancel_request";
            $this->loadModel('EmailTemplate');
            $fullName = "Admin";
            $number_person = $myBookings['Booking']['number_person']; //no of person
            $start_time = date('d M Y -  H:i a', strtotime($myBookings['Booking']['reservation_date']));

            $storeTimeAm = "";
            $dateTimeArr = explode("-", $start_time);

            $storeDate = trim($dateTimeArr[0]);
            $storeTime = trim($dateTimeArr[1]);

            if (isset($dateTimeArr[2]) && !empty($dateTimeArr[2])) {
                $storeTimeAm = trim($dateTimeArr[2]);
                $storeTime = $storeTime . $storeTimeAm;
            }
            $customer_name = AuthComponent::User('fname') . " " . AuthComponent::User('lname');
            $emailSuccess = $this->EmailTemplate->storeTemplates($decrypt_storeId, $decrypt_merchantId, $template_type);
            if ($emailSuccess) {
                if (($store ['Store'] ['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                    $storeEmail = trim($store['Store']['notification_email']);
                } else {
                    $storeEmail = trim($store['Store']['email_id']);
                }
                $customerEmail = trim(AuthComponent::User('email'));
                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                $emailData = str_replace('{BOOKING_DATE}', $start_time, $emailData);
                $emailData = str_replace('{NO_PERSON}', $number_person, $emailData);
                $emailData = str_replace('{CUSTOMER_NAME}', $customer_name, $emailData);
                $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
                $emailData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $emailData);
                $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];
                $storePhone = $store['Store']['phone'];
                $url = "http://" . $store['Store']['store_url'];
                $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);

                $this->Email->to = $storeEmail;
                $this->Email->subject = $subject;
                //$this->Email->from = $customerEmail;
                $this->Email->from = $this->front_email;
                $this->set('data', $emailData);
                $this->Email->template = 'template';
                $this->Email->smtpOptions = array(
                    'port' => " $this->smtp_port",
                    'timeout' => '30',
                    'host' => "$this->smtp_host",
                    'username' => "$this->smtp_username",
                    'password' => "$this->smtp_password"
                );
                $this->Email->sendAs = 'html'; // because we like to send pretty mail
                try {
                    $this->Email->send();
                } catch (Exception $e) {
                    
                }


                if (($store ['Store'] ['notification_type'] == 2 || $store['Store']['notification_type'] == 3) && (!empty($store ['Store']['notification_number']))) {
                    $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['notification_number']);
                } else {
                    $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['phone']);
                }
                $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                $smsData = str_replace('{BOOKING_DATE}', $start_time, $smsData);

                $smsData = str_replace('{BOOKING_DATE}', $storeDate, $smsData);
                $smsData = str_replace('{BOOKING_TIME}', $storeTime, $smsData);
                $smsData = str_replace('{SPECIAL_REQUEST}', $special_request, $smsData);
                $smsData = str_replace('{CONTACT_PERSON}', $phoneNumber, $smsData);


                $smsData = str_replace('{NO_PERSON}', $number_person, $smsData);
                $smsData = str_replace('{CUSTOMER_NAME}', $customer_name, $smsData);
                $smsData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $smsData);
                $smsData = str_replace('{STORE_PHONE}', $mobnumber, $smsData);
                $message = $smsData;
                $this->Common->sendSmsNotificationFront($mobnumber, $message);
            }
            $this->Session->setFlash(__('Reservation has been cancelled'), 'flash_success');
            $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
        } else {
            $this->Session->setFlash(__('Reservation could not be cancelled, please try again'), 'flash_error');
            $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
        }
    }

    /* --------------------------------------
      Function Name:update_events
      Description:To sync  events based on particular id of a claim from scheduler side
      Created By:smartData
      Date:14 Dec 2016
      ----------------------------------------- */

    private function _deleteEvent($booking_id = null) {
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
                    'fields' => array('Store.id', 'store_name', 'address', 'city', 'state', 'zipcode', 'time_zone_id')
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
                $cal->events->delete($calender_id, $event_id);
            }
        }
    }

    /* ------------------------------------------------
      Function name:deleteBooking()
      Description:Delete User Booking
      created:31/8/2015
      ----------------------------------------------------- */

    public function deleteBooking($encrypted_storeId = null, $encrypted_merchantId = null, $encrypted_bookingId = null) {
        $this->userLoginCheck();
        $this->autoRender = false;
        $data['Booking']['id'] = $this->Encryption->decode($encrypted_bookingId);
        $data['Booking']['is_deleted'] = 1;
        if ($this->Booking->saveBookingDetails($data)) {
            $this->_deleteEvent($data['Booking']['id']);
            $this->Session->setFlash(__('Reservation has been deleted.'), 'flash_success');
            $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
        } else {
            $this->Session->setFlash(__('Reservation could not be deleted, please try again.'), 'flash_error');
            $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
        }
    }

    /* ------------------------------------------------
      Function name:myReviews()
      Description:List of User Reviews
      created:31/8/2015
      ----------------------------------------------------- */

    public function myReviews($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->userLoginCheck();
        $this->layout = $this->store_inner_pages;
//        $this->Session->delete('Order');
//        $this->Session->delete('Cart');
//        $this->Session->delete('cart');
//        $this->Session->delete('FetchProductData');
//        $this->Session->delete('Coupon');
//        $this->Session->delete('Discount');
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('fields' => array('name')))), false);
        $this->StoreReview->bindModel(array('belongsTo' => array('OrderItem' => array('fields' => array('item_id')))), false);

        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->StoreReview->bindModel(array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name'),
                    'type' => 'INNER',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                ),
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'fields' => array('fname', 'lname'),
                    'type' => 'INNER',
                    'conditions' => array('User.is_deleted' => 0, 'User.is_active' => 1)
                ))
                ), false);
        $decrypt_userId = AuthComponent::User('id');
        $value = "";

        if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
            if ($this->params->pass[0] == 'clear') {
                $this->Session->delete('MyReviewSearchData');
            }
        }

        if ($this->Session->read('MyReviewSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('MyReviewSearchData'), true);
            $value = $this->request->data['User']['keyword'];
            $encrypted_storeId = $this->request->data['Merchant']['store_id'];
            $encrypted_storeId = $this->Encryption->encode($encrypted_storeId);
        } else {
            $this->Session->delete('MyReviewSearchData');
        }

        if (!empty($this->request->data)) {
            $this->Session->write('MyReviewSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $conditions1 = array('OR' => array("StoreReview.review_comment LIKE '%" . $value . "%'", "StoreReview.review_rating LIKE '%" . $value . "%'"));
            } else {
                $conditions1 = array();
            }
            $merchantId = $this->Session->read('merchant_id');
            $decrypt_storeId = $this->request->data['Merchant']['store_id'];
            $decrypt_merchantId = $merchantId;
            $encrypted_storeId = $this->Encryption->encode($decrypt_storeId);

            if (!empty($decrypt_storeId)) {
                $conditions = array('StoreReview.is_deleted' => 0, 'StoreReview.user_id' => $decrypt_userId, 'StoreReview.store_id' => $decrypt_storeId);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array(
                    'conditions' => $conditions,
                    'order' => 'StoreReview.created DESC',
                    'recursive' => 2,
                    'limit' => 9
                );
            } else {
                $conditions = array('StoreReview.is_deleted' => 0, 'StoreReview.user_id' => $decrypt_userId);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array('conditions' => $conditions, 'order' => 'StoreReview.created DESC', 'recursive' => 2, 'limit' => 9);
            }
        } else {
            $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
            $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
            $this->paginate = array(
                'conditions' => array('StoreReview.is_deleted' => 0, 'StoreReview.user_id' => $decrypt_userId),
                'order' => 'StoreReview.created DESC',
                'recursive' => 2,
                'limit' => 9
            );
        }
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId', 'encrypted_userId'));
        $myReviews = $this->paginate('StoreReview');
//        $Reviews = $this->find('all',array('recursive'=>2,'order'=>array('StoreReview.created DESC'),'conditions'=>array('StoreReview.is_deleted'=>0,'StoreReview.user_id'=>$userId)));
//        $myReviews = $this->StoreReview->getReviews($decrypt_storeId, $decrypt_userId);
        //pr($myReviews);die;
        $this->set(compact('myReviews'));
        $this->set("keyword", $value);
    }

    /* ------------------------------------------------
      Function name:deleteReview()
      Description:Delete User Review
      created:31/8/2015
      ----------------------------------------------------- */

    public function deleteReview($encrypted_storeId = null, $encrypted_merchantId = null, $encrypted_reviewId = null) {
        $this->userLoginCheck();
        $this->autoRender = false;
        $data['StoreReview']['id'] = $this->Encryption->decode($encrypted_reviewId);
        $data['StoreReview']['is_deleted'] = 1;
        if ($this->StoreReview->saveReview($data)) {
            $this->StoreReviewImage->updateAll(array('is_deleted' => 1), array('store_review_id' => $data['StoreReview']['id']));
            $this->Session->setFlash(__('Review has been deleted'), 'flash_success');
            $this->redirect(array('controller' => 'pannels', 'action' => 'myReviews', $encrypted_storeId, $encrypted_merchantId)); //
        } else {
            $this->Session->setFlash(__('Review could not be deleted, please try again'), 'flash_error');
            $this->redirect(array('controller' => 'pannels', 'action' => 'myReviews', $encrypted_storeId, $encrypted_merchantId)); //
        }
    }

    /* ------------------------------------------------
      Function name:staticContent()
      Description: Used for static content
      created:8/9/2015
      ----------------------------------------------------- */

    public function staticContent($encrypted_storeId = null, $encrypted_merchantId = null, $encrypted_contentId = null) {
        $this->layout = $this->store_inner_pages;
        $contentId = $this->Encryption->decode($encrypted_contentId);
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $content = $this->StoreContent->getPageDetail($contentId, $decrypt_storeId);
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId', 'content'));
    }

    /* ------------------------------------------------
      Function name:updateBooking()
      Description: Used for update booking request
      created:21/9/2015
      ----------------------------------------------------- */

    public function updateBooking($encrypted_bookId = null, $encrypted_storeId = null, $encrypted_merchantId = null) {   // It will check either the order is pre-order  or Now
        $this->userLoginCheck();
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_bookId = $this->Encryption->decode($encrypted_bookId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $book = $this->Booking->getBookingDetailsById($decrypt_bookId);
        $avalibilty_status = $this->Common->checkStoreAvalibility($decrypt_storeId); // I will check the time avalibility of the store
        if ($avalibilty_status != 1) {
            $setPre = 1;
        } else {
            $setPre = 0;
        }
        $this->loadModel('Store');
        $this->loadModel('StoreAvailability');
        $this->loadModel('StoreHoliday');
        $this->loadModel('Booking');

        //$current_date = date('Y-m-d', strtotime($book['Booking']['reservation_date']));
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));

        $date = new DateTime($current_date);
        $current_day = $date->format('l');
        $store = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
        $store_data = $this->StoreAvailability->getStoreInfoForDay($current_day, $decrypt_storeId); // get store detail
        //$holidayList = $this->StoreHoliday->getStoreHolidaylistDate($decrypt_storeId, $current_date);


        $today = 1;
        $orderType = 1;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        $time_break = $finaldata['time_break'];
        $store_data = $finaldata['store_data'];
        $storeBreak = $finaldata['storeBreak'];
        $time_range = $finaldata['time_range'];
        $current_date = $finaldata['currentdate'];

        $i = 1;
        $number_person = array();
        for ($i; $i < 30; $i++) {
            $number_person[$i] = $i;
        }

        $explodeVal = explode("-", $current_date);
        $currentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $this->set(compact('storeBreak', 'myBookings', 'setPre', 'number_person', 'time_break', 'time_range', 'store_data', 'store', 'encrypted_storeId', 'encrypted_merchantId', 'encrypted_userId', 'currentDateVar'));

//        $current_array = array();
//        $time_break = array();
//        if (empty($holidayList)) {
//            if (!empty($store_data)) {
//                $start = $store_data['StoreAvailability']['start_time'];
//                $end = $store_data['StoreAvailability']['end_time'];
//                $StoreCutOff = $this->Store->fetchStoreCutOff($decrypt_storeId);
//                $cutTime = '-' . $StoreCutOff ['Store']['cutoff_time'] . ' minutes';
//                $end = date("H:i:s", strtotime("$cutTime", strtotime($end)));
//                $time_ranges = $this->Common->getStoreTime($start, $end); // calling Common Component
//                foreach ($time_ranges as $time_key => $time_val) {
//                    $current_datee = $this->Common->gettodayDate();
//                    $current_time = strtotime($this->Common->gettodayDate(2));
//                    $time_key_str = strtotime($time_key);
//                    if (strtotime($current_datee) < strtotime($current_date)) {
//                        $current_array[$time_key] = $time_val;
//                    } else {
//                        if ($time_key_str > $current_time) {
//                            $current_array[$time_key] = $time_val;
//                        }
//                    }
//                }
//
//                if ($store['Store']['is_break_time'] == 1) {
//                    $this->loadModel('StoreBreak');
//                    $store_break = $this->StoreBreak->fetchStoreBreak($store['Store']['id'], $store_data['StoreAvailability']['id']);
//                    $time_break1 = array();
//                    $time_break2 = array();
//                    if ($store['Store']['is_break1'] == 1) {
//                        $break_start_time = $store_break['StoreBreak']['break1_start_time'];
//                        $break_end_time = $store_break['StoreBreak']['break1_end_time'];
//                        $time_break1 = $this->Common->getStoreTime($break_start_time, $break_end_time);
//                    }
//                    if ($store['Store']['is_break2'] == 1) {
//                        $break_start_time = $store_break['StoreBreak']['break2_start_time'];
//                        $break_end_time = $store_break['StoreBreak']['break2_end_time'];
//                        $time_break2 = $this->Common->getStoreTime($break_start_time, $break_end_time);
//                    } $time_break = array_unique(array_merge($time_break1, $time_break2), SORT_REGULAR);
//                }
//            }
//        }
//        $time_range = $current_array;

        $book = $this->Booking->getBookingDetailsById($decrypt_bookId);
        $this->set(compact('book', 'setPre', 'number_person', 'time_break', 'time_range', 'store_data', 'store', 'encrypted_storeId', 'encrypted_merchantId', 'encrypted_bookId'));

        if ($this->request->is('post')) {
            $this->request->data['Booking']['store_id'] = $decrypt_storeId;
            $this->request->data['Booking']['user_id'] = AuthComponent::User('id');
            $reservationDate = $this->Dateform->formatDate($this->request->data['Booking'] ['start_date']);
            $ResTime = $this->request->data['Store'] ['pickup_hour'] . ':' . $this->request->data['Store']['pickup_minute'] . ':00';
            $reservationDateTime = $reservationDate . " " . $ResTime;
            $this->request->data['Booking']['reservation_date'] = $reservationDateTime;
            $this->request->data['Booking']['booking_status_id'] = 1;
            $save_result = $this->Booking->saveBookingDetails($this->data); // call on model to save data
            if ($save_result) {
                $this->_updateEvent($this->request->data['Booking']['id']);
                $template_type = 'customer_dine_in_request';
                $this->loadModel('DefaultTemplate');
                $fullName = "Admin";
                $number_person = $this->data['Booking']['number_person']; //no of person
                $start_date = $this->data['Booking']['start_date'];
                $start_time = date('H:i a', strtotime($ResTime));
                $customer_name = AuthComponent::User('fname') . " " . AuthComponent::User('lname');
                if (!empty($this->data['Booking']['special_request'])) {
                    $special_request = $this->data['Booking']['special_request'];
                } else {
                    $special_request = "N/A";
                }
                //$emailSuccess = $this->EmailTemplate->storeTemplates($decrypt_storeId, $decrypt_merchantId, $template_type);
                $emailSuccess = $this->DefaultTemplate->find('first', array('conditions' => array('DefaultTemplate.template_code' => $template_type, 'DefaultTemplate.is_default' => 1)));

                if ($emailSuccess) {
                    if (($store ['Store'] ['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                        $storeEmail = trim($store['Store']['notification_email']);
                    } else {
                        $storeEmail = trim($store['Store']['email_id']);
                    }
                    $customerEmail = trim(AuthComponent::User('email'));
                    $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $emailData = str_replace('{BOOKING_DATE}', $start_date, $emailData);
                    $emailData = str_replace('{BOOKING_TIME}', $start_time, $emailData);
                    $emailData = str_replace('{NO_PERSON}', $number_person, $emailData);
                    $emailData = str_replace('{SPECIAL_REQUEST}', $special_request, $emailData);
                    $emailData = str_replace('{CUSTOMER_NAME}', $customer_name, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['DefaultTemplate']['template_subject']));
                    $emailData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $emailData);
                    $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];
                    $storePhone = $store['Store']['phone'];
                    $url = "http://" . $store['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);

                    $this->Email->to = $storeEmail;
                    $this->Email->subject = $subject;
                    //$this->Email->from = $customerEmail;
                    $this->Email->from = $this->front_email;
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
                    // $this->Email->delivery = 'smtp';
                    try {
                        $this->Email->send();
                    } catch (Exception $e) {
                        
                    }

                    if (($store ['Store'] ['notification_type'] == 2 || $store['Store']['notification_type'] == 3) && (!empty($store['Store'] ['notification_number']))) {
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['notification_number']);
                    } else {
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['phone']);
                    }
                    $contactPerson = AuthComponent::User('fname') . " " . AuthComponent::User('lname') . " " . AuthComponent::User('phone');
                    $smsData = $emailSuccess['DefaultTemplate']['sms_template'];
                    $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                    $smsData = str_replace('{BOOKING_DATE}', $start_date, $smsData);
                    $smsData = str_replace('{BOOKING_TIME}', $start_time, $smsData);
                    $smsData = str_replace('{NO_PERSON}', $number_person, $smsData);
                    $smsData = str_replace('{SPECIAL_REQUEST}', $special_request, $smsData);
                    $smsData = str_replace('{CONTACT_PERSON}', $contactPerson, $smsData);
                    $smsData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $smsData);
                    $smsData = str_replace('{STORE_PHONE}', $mobnumber, $smsData);
                    $message = $smsData;
                    $this->Common->sendSmsNotificationFront($mobnumber, $message);
                }
                $this->Session->setFlash(__('Your request has been updated, you will receive a confirmation email shortly. Thank you!'), 'flash_success');
                $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
            } else {
                $this->Session->setFlash(__('Reservation request could not be updated, please try again'), 'flash_error');
                $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
            }
        }
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
                    'fields' => array('store_name', 'address', 'city', 'state', 'zipcode', 'time_zone_id')
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
                $description = "Your reservation status is " . $bookingData['BookingStatuse']['name'];
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

    /* ------------------------------------------------
      Function name:rating()
      Description: Display latest 20 reviews
      created: 24/09/2015
      ----------------------------------------------------- */

    public function allReviews() {

        $this->layout = $this->store_inner_pages;
        $storeId = $this->Session->read('store_id');
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')))), false);
        $this->StoreReview->bindModel(array('belongsTo' => array('User' => array('fields' => array('salutation', 'fname', 'lname')), 'OrderItem' => array('foreignKey' => 'order_item_id', 'fields' => array('item_id')))), false);
        $this->StoreReview->bindModel(
                array(
                    'hasMany' => array(
                        'StoreReviewImages' => array(
                            'fields' => array('image', 'is_deleted'),
                            'conditions' => array('StoreReviewImages.is_deleted' => 0, 'StoreReviewImages.is_active' => 1)
        ))));
        //$allReviews = $this->StoreReview->getAllReview($decrypt_storeId);
        $this->paginate = array('conditions' => array('StoreReview.store_id' => $storeId, 'StoreReview.is_active' => 1, 'StoreReview.is_deleted' => 0, 'StoreReview.is_approved' => 1), 'order' => array('StoreReview.review_rating' => 'DESC', 'StoreReview.created' => 'DESC'), 'recursive' => 2);
        $allReviews = $this->paginate('StoreReview');
        $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
        $encrypted_merchantId = $this->Encryption->encode($this->Session->read('merchant_id'));
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId', 'allReviews'));
    }

    public function fax() {
        $this->autoRender = false;
        $username = 'ecomm2015'; // Enter your Interfax username here
        $password = 'ecomm2015'; // Enter your Interfax password here
        $faxnumber = '18558579962'; // Enter your designated fax number here in the format +[country code][area code][fax number], for example: +12125554874
        $texttofax = '<html><head></head><body><h3>Test Message From Smartdata India</h3><div style="color:#FF0000;">Hi this is Ekansh</div><div style="color:#FCCCC0;">Test from application</div></body></html>'; // Enter your fax contents here
        $filetype = 'TXT'; // If $texttofax is regular text, enter TXT here. If $texttofax is HTML enter HTML here

        /*         * ************** Settings end *************** */

        $client = new SoapClient("http://ws.interfax.net/dfs.asmx?wsdl");
        $params->Username = $username;
        $params->Password = $password;
        $params->FaxNumber = $faxnumber;
        $params->Data = $texttofax;
        $params->FileType = $filetype;
//        pr($params);die;
        $faxResult = $client->SendCharFax($params);
//        print_r($faxResult);
    }

    function PrintReceipt($encryorderId = null, $fromView = null) {
        $this->autoRender = false;
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderItem');
        $this->loadModel('OrderPayment');
        $this->loadModel('Order');
        $this->loadModel('Store');
        $this->loadModel('OrderPreference');
        $this->loadModel('OrderTopping');

        $orderId = '250';
        $storeID = $this->Session->read('store_id');
        $merchantId = $this->Session->read('merchant_id');
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);

        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'total_item_price', 'tax_price'))), 'belongsTo' => array('Store' => array('fields' => array('id', 'service_fee', 'delivery_fee', 'store_name', 'store_url', 'address')), 'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id', 'fields' => array('name')), 'DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address', 'phone')), 'OrderStatus' => array('fields' => array('name')), 'User' => array('foreignKey' => 'user_id', 'fields' => array('fname', 'lname', 'phone'))),
            'hasOne' => array('OrderPayment' => array('fields' => array('payment_gateway')))), false);
        $orderDetails = $this->Order->getfirstOrder($merchantId, $storeID, $orderId);
        $amount = 0;
        foreach ($orderDetails['OrderItem'] as $order) {
            if (empty($order['OrderOffer'])) {
                $tempitem = "<tr><td>" . $order ['quantity'] . 'X' . @$order['Size']['size'] . ' ' . $order ['Item'] ['name'] . '</td><td> $' . number_format($order['total_item_price'], 2) . "</td></tr>";
                $toppingstr = "";
                foreach ($order['OrderTopping'] as $key => $toppingarr) {
                    if (!empty($toppingarr['Topping']['name'])) {
                        $toppingstr.=$toppingarr['Topping']['name'] . ",";
                    }
                }


                $preferencetr = "";
                foreach ($order['OrderPreference'] as $key => $prearr) {
                    if (!empty($prearr['SubPreference']['name'])) {
                        $preferencetr.=$prearr['SubPreference']['name'] . ",";
                    }
                }
                $toppingstr = rtrim($toppingstr, ",");
                $preferencetr = rtrim($preferencetr, ",");
                if (!empty($toppingstr)) {
                    $topping = "<tr><td><small><strong>Toppings: </strong>" . $toppingstr . "</small></td></tr>";
                }
                if (!empty($preferencetr)) {
                    $preference = "<tr><td><small><strong>Preference: </strong>" . $preferencetr . "</small></td></tr>";
                }
                $itemss[] = $tempitem . $topping . $preference;
            } else {


                $offerItemName = '';
                foreach ($order['OrderOffer'] as $off) {
                    $offerItemName .= '<tr><td><small><strong>Offer Item: </strong>' . $off ['quantity'] . 'X' . @$off['Size']['size'] . ' ' . $off['Item']['name'] . "</td></tr>";
                }

                $toppingstr = "";
                foreach ($order['OrderTopping'] as $key => $toppingarr) {
                    if (!empty($toppingarr['Topping']['name'])) {
                        $toppingstr.=$toppingarr['Topping']['name'] . ",";
                    }
                }

                $preferencetr = "";
                foreach ($order['OrderPreference'] as $key => $prearr) {
                    if (!empty($prearr['SubPreference']['name'])) {
                        $preferencetr.=$prearr['SubPreference']['name'] . ",";
                    }
                }
                $toppingstr = rtrim($toppingstr, ",");
                $preferencetr = rtrim($preferencetr, ",");
                if (!empty($toppingstr)) {
                    $topping = "<tr><td><small><strong>Toppings: </strong>" . $toppingstr . "</td></tr>";
                }
                if (!empty($preferencetr)) {
                    $preference = "<tr><td><small><strong>Preference: </strong>" . $preferencetr . "</td></tr>";
                }
                $tempitem = "<tr><td>" . $order['quantity'] . 'X' . @$order['Type'] ['name'] . ' ' . @$order['Size']['size'] . ' ' . $order ['Item'] ['name'] . '</td><td> $' . number_format($order['total_item_price'], 2) . "</td></tr>";
                $itemss[] = $tempitem . $topping . $preference . $offerItemName;
            }
            $amount = $amount + $order['total_item_price'];
        }
        $printdata = "<table>";


        //pr($orderDetails);
        $date = date('m/d/Y', strtotime($orderDetails['Order']['pickup_time']));
        $time = date('h:i:s', strtotime($orderDetails['Order']['pickup_time']));
        $am = date('A', strtotime($orderDetails ['Order'] ['pickup_time']));
        $printdata .="<tr><td>" . $date . "</td><td>" . $time . "</td><td>" . $am . "</td></tr>";
        $printdata.="<tr><td></br>" . $orderDetails ['Segment']['name'] . "</td></tr>";
        $printdata .= "<tr><td>" . $orderDetails['OrderPayment']['payment_gateway'] . "</td></tr>";
        $printdata .= "<tr><td>Order#</td><td>" . $orderDetails['Order']['order_number'] . "</td></tr>";


        if ($orderDetails['Order']['seqment_id'] != 2) {
            $printdata.="<tr><td>Customer Name:</td><td>" . $orderDetails['DeliveryAddress']['name_on_bell'] . "</td></tr>";
            $printdata .= "<tr><td>Phone#</td><td>" . $orderDetails['DeliveryAddress']['phone'] . "</td></tr>";
            $address = $orderDetails['DeliveryAddress'] ['name_on_bell'] . " " . $orderDetails['DeliveryAddress']['address'] . " " . $orderDetails['DeliveryAddress']['city'];
        } else {
            $printdata .= "<tr><td>Customer Name:</td><td>" . $orderDetails['User']['fname'] . ' ' . $orderDetails['User'] ['lname'] . "</td></tr>";
            $printdata.="<tr><td>Phone#</td><td>" . $orderDetails['User']['phone'] . "</td></tr>";
            $address = "Pick up";
        }
        $printdata.="<tr><td>Address:</td><td>" . $address . "</td></tr>";
        $printdata .="<tr><td>< /br><strong>Order Detail</strong></td></tr>";

        foreach ($itemss as $dataItem) {
            $printdata .=$dataItem;
            $printdata .="<tr><td></br></td></tr>";
        }
        $printdata .="<tr><td></br></td></tr>";
        $printdata .="<tr><td>Sub-Total:</td><td>$" . number_format($amount, 2) . "</td></tr>";
        $printdata .="<tr><td>Tax:</td><td>$" . $orderDetails['Order']['tax_price'] . "</td></tr>";
        $printdata .="<tr><td>Coupon Discount:</td><td>$" . $orderDetails['Order']['coupon_discount'] . "</td></tr>";
        $printdata .="<tr><td>Service Fee:</td><td>$" . $orderDetails['Order']['service_amount'] . "</td></tr>";
        $printdata .="<tr><td>Delivery Fee:</td><td>$" . $orderDetails['Order']['delivery_amount'] . "</td></tr>";
        $printdata .="<tr><td>Total:</td><td>$" . $orderDetails['Order']['amount'] . "</td></tr>";
        $printdata .="<tr><td><br/>*Special Instructions Section*</td></tr>";
        $printdata .="<tr><td>" . $orderDetails['Order']['order_comments'] . "</td></tr>";
        $printdata .="<tr><td><br/>iOrderFoods.com</td></tr></table>";
        echo $printdata;
        die;
    }

    public function orderImages($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = $this->store_layout;
        $this->set('title', 'Store Review Images');
        $this->loadModel('StoreReviewImage');
        $storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        //$allReviewImages = $this->StoreReviewImage->getAllStoreReviewImages($decrypt_storeId);
        $this->paginate = array('limit' => '9', 'order' => array('StoreReviewImage.created' => 'DESC'), 'conditions' => array('StoreReviewImage.store_id' => $storeId, 'StoreReviewImage.is_active' => 1, 'StoreReviewImage.is_deleted' => 0), 'fields' => array('StoreReviewImage.image'));
        $allReviewImages = $this->paginate('StoreReviewImage');
        //prx($allReviewImages);
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId', 'allReviewImages'));
    }

    public function orderImagesAjax() {
        $this->layout = false;
        $this->loadModel('StoreReviewImage');
        $storeId = $this->Session->read('store_id');
        $limit = 9;
        if (!empty($this->request->data['pageNo'])) {
            $pageNo = $this->request->data['pageNo'];
        } else {
            $pageNo = 1;
        }
        $offset = ($pageNo - 1) * $limit; //O = (P - 1) * L
        $allReviewImages = $this->StoreReviewImage->find('all', array('offset' => $offset, 'limit' => $limit, 'order' => array('StoreReviewImage.created' => 'DESC'), 'conditions' => array('StoreReviewImage.store_id' => $storeId, 'StoreReviewImage.is_active' => 1, 'StoreReviewImage.is_deleted' => 0), 'fields' => array('StoreReviewImage.image')));
        if (empty($allReviewImages)) {
            echo 1;

            exit;
        }
        $this->set(compact('allReviewImages'));
    }

    function testing() {
        $this->loadModel('Zipcode');
        $this->loadModel('Zip');
        $data = $this->Zipcode->find('all', array('fields' => array('id', 'City', 'zipcode'), 'group' => '`Zipcode`.`zipcode`'));
        foreach ($data as $row) {
            $updateData = array();
            $updateData['Zip']['zipcode'] = $row['Zipcode']['zipcode'];
            $updateData['Zip']['city_id'] = "(select cities.id from cities where cities.name='{$row['Zipcode']['City']}')"; //;
            if ($this->Zip->updateAll(
                            array('Zip.city_id' => $updateData['Zip']['city_id']), array('Zip.zipcode' => $updateData['Zip']['zipcode'])
                    ))
                echo $row['Zipcode']['id'] . "====" . $updateData['Zip']['zipcode'] . "<br/>";
            else
                echo $row['Zipcode']['id'] . "====No <br/>";
        }
        echo "done";
        die;
    }

    /* ------------------------------------------------
      Function name: reviewImages()
      Description: Display the list of store reviews images in admin panel
      created:26/07/2016
      ----------------------------------------------------- */

    public function reviewImageDet() {
        $this->layout = false;

        if ($this->request->is('ajax')) {
            $id = $this->Encryption->decode($this->request->data['storeReviewId']);
            $result = $this->StoreReviewImage->find('all', array('conditions' => array('StoreReviewImage.store_review_id' => $id, 'StoreReviewImage.is_deleted' => 0), 'fields' => array('StoreReviewImage.id', 'StoreReviewImage.image')));
            $imgArr = array();
            foreach ($result as $k => $result1) {
                $imgArr[$k] = $result1['StoreReviewImage']['image'];
            }
            $this->set('imgArr', $imgArr);
        }
    }

    public function cropImage() {
        $this->layout = false;
        $this->autoRender = false;
        // Condition checking thumb File exist or not
        $newWidth = 300;
        $newHeight = 190;
        $imagename = "1482147018316242073_2.jpeg";
        $path = WWW_ROOT . "storeReviewImage/" . $imagename;
        pr($path);
        if (file_exists($path)) {
            // full path for thumb image
            $full_thumb_url = WWW_ROOT . "/storeReviewImage/thumb/" . $imagename;
            list($width, $height, $type, $attr) = getimagesize($path);
            if (empty($newHeight) && empty($newWidth)) {
                $newHeight = 150;
                $newWidth = 150;
            }
            $responseT = $this->Common->getResize($height, $width, $newHeight, $newWidth, $path, $full_thumb_url);
            if ($responseT) {
                if ($responseT) {
                    $response['status'] = true;
                } else {
                    $response['status'] = false;
                    $response['errmsg'] = "Unable to upload image";
                }
            } else {
                $response['status'] = false;
                $response['errmsg'] = "Unable to upload image";
            }
            prx($response);
        } else {
            pr("file not exist");
        }
    }
    
    public function menuAdd() {
        $this->loadModel('Merchant');
        $merchantList = $this->Merchant->getListTotalMerchant();
        if (!empty($merchantList)) {
            $this->loadModel('Store');
            foreach ($merchantList as $merchant_id => $mList) {
                if (!empty($merchant_id)) {
                    $storeList = $this->Store->getMerchantStores($merchant_id);
                    if (!empty($storeList)) {
                        foreach ($storeList as $store_id => $sList) {
                            if (!empty($store_id) && !empty($merchant_id)) {
                                $this->loadModel('StoreContent');
                                $menus = array('Home', 'Place Order', 'Reservations', 'Store Info', 'Photo', 'Reviews', 'Menu', 'Deals', 'Gallery');
                                foreach ($menus as $key => $menu) {
                                    $key = $key + 1;
                                    $pagedata['name'] = strtoupper($menu);
                                    $pagedata['content_key'] = 'default_' . strtolower(str_replace(' ', '', $menu));
                                    $conditions = array('UPPER(StoreContent.name)' => strtoupper($pagedata['name']), 'StoreContent.merchant_id' => $merchant_id, 'StoreContent.store_id' => $store_id, 'StoreContent.is_deleted' => 0);
                                    $count = $this->StoreContent->find('first', array('fields' => array('id'), 'conditions' => $conditions));
                                    if (empty($count)) {
                                        $pagedata['page_position'] = 1;
                                        $pagedata['position'] = $key;
                                        $pagedata['is_active'] = 1;
                                        $pagedata['store_id'] = $store_id;
                                        $pagedata['merchant_id'] = $merchant_id;
                                        $this->StoreContent->create();
                                        $this->StoreContent->savePage($pagedata);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }die('done');
    }

}
