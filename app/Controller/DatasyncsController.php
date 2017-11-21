<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('StoreAppController', 'Controller');
App::import('Vendor', 'google_data_link', array('file' => 'google_data_link' . DS . 'google_data_link.php'));

class DatasyncsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'NZGateway');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common', 'Dateform');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('oauthClient', 'oauthVendor');
    }

//    function getSettingsForSync() {
//        $this->autoRender = false;
//        $restult = array();
//        $resultSet = $this->find('first', array('fields' => 'CommonSetting.google_client_id,CommonSetting.google_client_secret,CommonSetting.google_redirect_uri'));
//
//        $restult['google_client_id'] = $resultSet['CommonSetting']['google_client_id'];
//        $restult['google_client_secret'] = $resultSet['CommonSetting']['google_client_secret'];
//
//        //added in constant.php and here
//        $protocol = "http://";
//        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
//            $protocol = "https://";
//        }
//        ///tests/oauthVendor
//        $uriredirect = $protocol . $_SERVER['HTTP_HOST'] . $resultSet['CommonSetting']['google_redirect_uri'] . '&client_id=' . $restult['google_client_id'];
//        $restult['google_redirect_uri_withclient'] = $uriredirect;
//        $restult['google_redirect_uri'] = $protocol . $_SERVER['HTTP_HOST'] . $resultSet['CommonSetting']['google_redirect_uri'];
//
//        return $restult;
//    }

    function oauthClient() {
        $this->autoRender = false;
        $protocol = "http://";
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $protocol = "https://";
        }
        $this->loadModel('MainSiteSetting');
        $resultSet = $this->MainSiteSetting->find('first', array('fields' => array('google_client_id', 'google_redirect_uri')));
        if (empty($resultSet)) {
            $this->redirect($this->referer());
        }
        $google_client_id = $resultSet['MainSiteSetting']['google_client_id'];
        $google_redirect_uri = $resultSet['MainSiteSetting']['google_redirect_uri'];
        $uriredirect = $protocol . $_SERVER['HTTP_HOST'] . $google_redirect_uri . '&client_id=' . $google_client_id;
        $this->redirect('https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=' . $uriredirect . '&scope=https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://mail.google.com/ https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/calendar.readonly http://www.google.com/m8/feeds/&access_type=offline&approval_prompt=force');
    }

    function oauthVendor() {
        $this->autoRender = false;
        $this->layout = false;
        if (isset($_GET['error']) && !empty($_GET['error'])) {
            echo "<script type='text/javascript'>";
            echo "window.close();";
            echo "</script>";
        }
        App::import('Vendor', 'google_data_link/google_data_link');
        if (isset($_GET['code']) && !empty($_GET['code'])) {
            $this->loadModel("GoogleToken");
            $userid = AuthComponent::User('id');
            $googel_token = $this->GoogleToken->find('first', array('conditions' => array('GoogleToken.user_id' => $userid)));
            if (empty($googel_token)) {
                $this->loadModel('MainSiteSetting');
                $resultSet = $this->MainSiteSetting->find('first', array('fields' => array('google_client_id', 'google_client_secret', 'google_redirect_uri', 'google_application_name', 'google_api_key')));
                $google_client_id = $resultSet['MainSiteSetting']['google_client_id'];
                $google_client_secret = $resultSet['MainSiteSetting']['google_client_secret'];
                $google_api_key = $resultSet['MainSiteSetting']['google_api_key'];
                $google_application_name = $resultSet['MainSiteSetting']['google_application_name'];
                $protocol = "http://";
                if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
                    $protocol = "https://";
                }
                $google_redirect_uri = $protocol . $_SERVER['HTTP_HOST'] . $resultSet['MainSiteSetting']['google_redirect_uri'];

                $client = new Google_Client();
                $client->setApplicationName($google_application_name);
                $client->setClientId($google_client_id);
                $client->setClientSecret($google_client_secret);
                $client->setRedirectUri($google_redirect_uri);
                $client->setDeveloperKey($google_api_key);
                $client->setAccessType('offline');
                $client->setApprovalPrompt('force');
                $oauth2 = new Google_Oauth2Service($client);

                $client->authenticate($_GET['code']);
                $tokenArr = $client->getAccessToken();

                if ($tokenArr) {
                    $user = $oauth2->userinfo->get();
                }
                $tokenInfo = json_decode($tokenArr, true);
                $tokenInfo['email'] = $user['email'];
                if (!empty($user['email'])) {
                    $token_response = json_encode($tokenInfo);
                    $this->request->data['GoogleToken']['user_id'] = $userid;
                    $this->request->data['GoogleToken']['email'] = $tokenInfo['email'];
                    $this->request->data['GoogleToken']['refresh_token'] = $tokenInfo['refresh_token'];
                    $this->request->data['GoogleToken']['oAuth_token'] = $tokenInfo['access_token'];
                    $this->request->data['GoogleToken']['response_form'] = $token_response;
                    $this->request->data['GoogleToken']['actual_time'] = $tokenInfo['created'];
                    if ($this->GoogleToken->save($this->request->data['GoogleToken'])) {
                        $store_id = $this->Encryption->encode(AuthComponent::User('store_id'));
                        $merchant_id = $this->Encryption->encode(AuthComponent::User('merchant_id'));
                        echo "<script type='text/javascript'>";
                        echo "window.close();";
                        //echo "window.location =" . $protocol . $_SERVER['HTTP_HOST']."/pannels/myBookings/".$store_id."/".$merchant_id;
                        echo "</script>";
                        //$this->Session->setFlash(__('Your allowded to sync google calendar. Thank you!'), 'flash_success');
                        //$this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $this->Encryption->encode(AuthComponent::User('store_id')), $this->Encryption->encode(AuthComponent::User('merchant_id'))));
                    }
                } else {
                    echo "<script type='text/javascript'>";
                    echo "window.close();";
                    echo "</script>";
                }
            } else {
                echo "<script type='text/javascript'>";
                echo "window.close();";
                echo "</script>";
            }
        }
    }

    public function get_events($type = null, $appointment_id = null, $userid = null) {
        //Configure::write('debug',2);
        $this->autoRender = false;
        $this->loadModel("GoogleToken");
        $TOKEN = $this->GoogleToken->find('first', array('conditions' => array('user_id' => $userid)));
        $_SESSION['token'] = $TOKEN['GoogleToken']['response_form'];

        App::import('Vendor', 'google_event', array('file' => 'google_event' . DS . 'google_event.php'));
        //print_r($_SESSION);die;
        if ($appointment_id && $appointment_id != 'null' && $type == 6) {
            $allEventsDetail = available_events($appointment_id, $adjuster_id);
        } elseif ($type == 80) {
            $allEventsDetail = route_events($adjuster_id);
        } elseif ($type == 51) {
            $allEventsDetail = available_delete($adjuster_id);
        } elseif ($type == 45 || $type == 46) {
            $allEventsDetail = deleteAppointment($appointment_id, $adjuster_id);
        } elseif ($appointment_id && $appointment_id != 'null' && $type == 52) {

            $allEventsDetail = add_event($appointment_id, $adjuster_id);
        } elseif ($type == 60) { //admin - active/deactive
            $allEventsDetail = delete_event($adjuster_id);

            $this->loadModel('User');
            //$adjuster_id=$_SESSION['adjusterId'];
            $this->User->id = $adjuster_id;
            $this->User->saveField('token_recevied', 0);
        } elseif ($appointment_id && $appointment_id != 'null' && ($type == 4)) {
            //  echo "hi";die;
            $allEventsDetail = add_event($appointment_id, $adjuster_id);
        } elseif (($appointment_id && $appointment_id != 'null') || $type == 44 || $type == 47 || $type == 48) {

            $allEventsDetail = update_events($appointment_id, $adjuster_id);
        } else {

            $allEventsDetail = get_events($adjuster_id);
            //print_r($allEventsDetail); exit;
        }

        // echo "hi";die;
        $this->loadModel('GoogleAppointment');
        if ($allEventsDetail != 100 || $allEventsDetail != 101) {

            foreach ($allEventsDetail as $row) {

                $this->GoogleAppointment->create();
                $this->GoogleAppointment->save($row);
            }
        }

        if ($this->request->is('ajax')) {
            if ($type == 200) {

                $this->redirect(array('controller' => 'Appointments', 'action' => 'saveChangeStatus', 1));
            } elseif ($type == 1) {

                $this->redirect(array('controller' => 'Appointments', 'action' => 'saveAppointmentEditDetails', 1));
            } elseif ($type == 2) {
                echo 1;
                exit;
            } elseif ($type == 3) {
                $this->redirect(array('controller' => 'Appointments', 'action' => 'saveJobField', 1));
            } elseif ($type == 44) {
                echo 6;
                exit;
            } elseif ($type == 46 || $type == 48) {
                echo 'updated';
                exit; //making snooze
            } elseif ($type == 47) {
                echo 'DragSnooze';
                exit; //snooze keeping back on cal
            }
        } else {


            if ($type == 4) {

                $this->redirect(array('controller' => 'Appointments', 'action' => 'addAppointment', 1, $adjuster_id)); //$adjuster_id for navigation if needed
            } elseif ($type == 6) {
                $this->Session->setFlash("<div class='alert_success'>Availability saved and synchronized Successfully</div>");
                $this->redirect($this->referer());
            } elseif ($type == 60) {
                $this->Session->setFlash("<div class='alert_success'>Adjuster is deactivated from Google Sync</div>");
                $this->redirect($this->referer());
            } elseif ($type == 51) {
                $this->Session->setFlash("<div class='alert_success'>Availability updated and synchronized Successfully</div>");
                $this->redirect($this->referer());
            } elseif ($type == 52) {

                $this->Session->setFlash("<div class='alert_success'>Appointment  is  added and  synchronized Successfully</div>");
                $this->redirect($this->referer());
            } elseif ($type == 45) {
                $this->redirect(array('controller' => 'Appointments', 'action' => 'delete', base64_encode($appointment_id), 'del'));
            } else {
                $this->Session->setFlash("<div class='alert_success'>" . "Events Synchronized Successfully" . "</div>");

                if (AuthComponent::User('role_id') == 4) {
                    $this->redirect(array('controller' => 'schedulings', 'action' => 'routeMap', base64_encode($adjuster_id)));
                } else {
                    $this->redirect(array('controller' => 'schedulings', 'action' => 'adjusterSchedule'));
                }
            }
        }
    }

    /* --------------------------------------
      Function Name:add_event
      Description:To sync  when we add appointment into calendar
      Created By:smartData
      Date:9 October 2014
      ----------------------------------------- */

    public function addEvent($userid) {
        $this->autoRender = false;
        $this->loadModel("GoogleToken");
        $TOKEN = $this->GoogleToken->find('first', array('conditions' => array('user_id' => $userid)));
        $_SESSION['cal_token'] = $TOKEN['GoogleToken']['response_form'];
        App::import('Vendor', 'google_data_link', array('file' => 'google_data_link' . DS . 'google_event.php'));

        $this->loadModel('MainSiteSetting');
        $keyData = $this->MainSiteSetting->find('first', array('fields' => array('google_client_id', 'google_client_secret', 'google_redirect_uri', 'google_application_name')));
        $google_client_id = $keyData['MainSiteSetting']['google_client_id'];
        $google_client_secret = $keyData['MainSiteSetting']['google_client_secret'];
        $google_redirect_uri = $keyData['MainSiteSetting']['google_redirect_uri'];

        $client = new Google_Client();
        $client->setApplicationName($keyData['MainSiteSetting']['google_application_name']);
        $client->setClientId($google_client_id);
        $client->setClientSecret($google_client_secret);
        //$client->setRedirectUri(REDIRECTURI);

        $cal = new Google_CalendarService($client);
        if (isset($_SESSION['cal_token'])) {
            $client->setAccessToken($_SESSION['cal_token']);
        }
        if ($client->getAccessToken()) {
            $calender_id = $TOKEN['GoogleToken']['email'];
            //print_r($result);die;
            $event = new Google_Event();
            if (!empty($calender_id)) {

                $caldata = $cal->calendars->get($calender_id); //add new bp

                $description = "This is description";

                $caleventtitle = "This is Calender title.";

                $event->setSummary($caleventtitle);

                $event->setLocation("Sahastrdhara, Dehradun ,Uttrakhand, 248001");

                $event->setDescription($description);

                $color = "3";

                $stDate = "2016-12-13T09:00:00-07:00";
                $edDate = "2017-01-13T09:00:00-07:00";
                //echo $stDate;die;
                $start = new Google_EventDateTime();
                $start->setDateTime($stDate);
                $event->setStart($start);
                $end = new Google_EventDateTime();
                $end->setDateTime($edDate);
                $event->setEnd($end);
                $event->setColorId($color);
                try {
                    //echo "<pre>"; print_r($event); exit;
                    $createdEvent = $cal->events->insert($calender_id, $event);
                    $id = $createdEvent['id'];
                } catch (Google_ServiceException $e) {
                    prx('ERROR');
                }
                pr($id);
                die('createdEventId');
            } else {
                prx('Calender id error.');
            }
        } else {
            prx('access token error.');
        }
    }

    /* --------------------------------------
      Function Name:update_events
      Description:To sync  events based on particular id of a claim from scheduler side
      Created By:smartData
      Date:9 October 2014
      Modified By:BP
      Modified Date: 01 June 2015
      ----------------------------------------- */

    public function updateEvent($userid = 608) {
        $this->autoRender = false;
        $this->loadModel("GoogleToken");
        $this->loadModel('MainSiteSetting');

        $keyData = $this->MainSiteSetting->find('first', array('fields' => array('google_client_id', 'google_client_secret', 'google_redirect_uri', 'google_application_name')));
        $google_client_id = $keyData['MainSiteSetting']['google_client_id'];
        $google_client_secret = $keyData['MainSiteSetting']['google_client_secret'];
        $google_redirect_uri = $keyData['MainSiteSetting']['google_redirect_uri'];

        $TOKEN = $this->GoogleToken->find('first', array('conditions' => array('user_id' => $userid)));
        if (!empty($TOKEN)) {
            App::import('Vendor', 'google_data_link', array('file' => 'google_data_link' . DS . 'google_event.php'));
            $client = new Google_Client();
            $client->setApplicationName($keyData['MainSiteSetting']['google_application_name']);
            $client->setClientId($google_client_id);
            $client->setClientSecret($google_client_secret);
            $cal = new Google_CalendarService($client);
            $event = new Google_Event();
            $client->setAccessToken($TOKEN['GoogleToken']['response_form']);
            if ($client->getAccessToken()) {
                $event_id = 'omn5lat3lsq33rr4n37t99rgck'; //calendar event id
                $calender_id = $TOKEN['GoogleToken']['email'];

                $event->setSummary('Appointment at Somewhere1');
                //$event->setLocation("Sahastrdhara, Dehradun ,Uttrakhand, 248001");
                $event->setDescription("This Is latest update");



                $stDate = "2016-12-13T09:00:00-07:00";
                $edDate = "2017-01-13T09:00:00-07:00";

                $start = new Google_EventDateTime();
                $start->setDateTime($stDate);
                $event->setStart($start);
                $end = new Google_EventDateTime();
                $end->setDateTime($edDate);
                $event->setEnd($end);


                $updatedEvent = $cal->events->update($calender_id, $event_id, $event);
                pr($event);
                prx($updatedEvent);
                $data = $updatedEvent->getUpdated();
                prx($data);
            }
        }
    }

    function oauthVendor1() {
        $this->autoRender = false;
        $this->layout = false;
        if (isset($_GET['error']) && !empty($_GET['error'])) {
            echo "<script type='text/javascript'>";
            echo "window.close();";
            echo "</script>";
        }
        if (isset($_GET['code']) && !empty($_GET['code'])) {
            $this->loadModel('MainSiteSetting');
            $resultSet = $this->MainSiteSetting->find('first', array('fields' => array('google_client_id', 'google_client_secret', 'google_redirect_uri', 'google_application_name', 'google_api_key')));
            $google_client_id = $resultSet['MainSiteSetting']['google_client_id'];
            $google_client_secret = $resultSet['MainSiteSetting']['google_client_secret'];
            $google_api_key = $resultSet['MainSiteSetting']['google_api_key'];
            $google_application_name = $resultSet['MainSiteSetting']['google_application_name'];
            $protocol = "http://";
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
                $protocol = "https://";
            }
            $google_redirect_uri = $protocol . $_SERVER['HTTP_HOST'] . $resultSet['MainSiteSetting']['google_redirect_uri'];
            App::import('Vendor', 'google_data_link', array('file' => 'google_data_link' . DS . 'google_data_link.php'));
            $client = new Google_Client();
            $client->setApplicationName($google_application_name);
            $client->setClientId($google_client_id);
            $client->setClientSecret($google_client_secret);
            $client->setRedirectUri($google_redirect_uri);
            $client->setDeveloperKey($google_api_key);
            $client->setAccessType('offline');
            $client->setApprovalPrompt('force');
            $oauth2 = new Google_Oauth2Service($client);

            $client->authenticate($_GET['code']);
            $tokenArr = $client->getAccessToken();

            if ($tokenArr) {
                $user = $oauth2->userinfo->get();
            }
            $tokenInfo = json_decode($tokenArr, true);
            $tokenInfo['email'] = $user['email'];

            $this->loadModel("GoogleToken");
            $userid = AuthComponent::User('id');
            $googel_token = $this->GoogleToken->find('first', array('conditions' => array('GoogleToken.user_id' => $userid)));
            if (empty($googel_token)) {
                $token_response = json_encode($tokenInfo);
                $this->request->data['GoogleToken']['user_id'] = $userid;
                $this->request->data['GoogleToken']['email'] = $tokenInfo['email'];
                $this->request->data['GoogleToken']['refresh_token'] = $tokenInfo['refresh_token'];
                $this->request->data['GoogleToken']['oAuth_token'] = $tokenInfo['access_token'];
                $this->request->data['GoogleToken']['response_form'] = $token_response;
                $this->request->data['GoogleToken']['actual_time'] = $tokenInfo['created'];
                if ($this->GoogleToken->save($this->request->data['GoogleToken'])) {
                    $this->redirect(array('controller' => 'datasyncs', 'action' => 'addEvent', $userid));
                }
            } else {
                $timeFirst = $googel_token['GoogleToken']['created'];
                $timeSecond = date('Y-m-d H:i:s');
                $differenceInSeconds = strtotime($timeSecond) - strtotime($timeFirst);
                if ($differenceInSeconds > 3500) {
                    App::import('Vendor', 'google_refresh_token', array('file' => 'google_refresh_token' . DS . 'google_refresh_token.php'));
                    $client->setAccessToken($googel_token['GoogleToken']['response_form']);
                    if ($client->getAccessToken()) {
                        $jsonreq = json_decode($googel_token['GoogleToken']['response_form'], true);
                        try {
                            $resultrefresh = $client->refreshToken($jsonreq['refresh_token']);
                            $requesttoken = $client->getAccessToken();
                            $token_array = json_decode($requesttoken);
                            $this->request->data['GoogleToken']['id'] = $googel_token['GoogleToken']['id'];
                            $this->request->data['GoogleToken']['user_id'] = $userid;
                            $this->request->data['GoogleToken']['email'] = $token_array['email'];
                            $this->request->data['GoogleToken']['refresh_token'] = $token_array['refresh_token'];
                            $this->request->data['GoogleToken']['oAuth_token'] = $token_array['access_token'];
                            $this->request->data['GoogleToken']['response_form'] = $requesttoken;
                            $this->request->data['GoogleToken']['actual_time'] = $token_array['created'];
                            if ($this->GoogleToken->save($this->request->data['GoogleToken'])) {
                                $this->redirect(array('controller' => 'datasyncs', 'action' => 'addEvent', $userid));
                            }
                        } catch (Google_AuthException $e) {
                            return false;
                        }
                        //$tokenInfo['token'] = $client->getAccessToken();
                    } else {
                        return true;
                    }
                }
            }
        } else {

        }
    }

}
