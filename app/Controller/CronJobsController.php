<?php

class CronJobsController extends AppController {

    var $helpers = array('Html', 'Form', 'Cache');
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Webservice', 'Encryption', 'Common');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('orderReminder', 'bookingReminder', 'expiresDateCron', 'birthDayCron', 'anniversaryCron', 'featuredSection', 'specialTemplates', 'storeTemplates', 'voiceCall', 'notification');
    }

    public function sendNewLetter() {
        $this->layout = "";
        $this->autoRender = false;
        $this->loadModel('Store');
        $this->loadModel('User');
        $this->loadModel('Merchant');
        $this->loadModel('DefaultTemplate');
        $this->loadModel('Newsletter');
        $this->loadModel('NewsletterManagement');
        if ($this->CronJob->checkCronCurrentStatus('send_newsletter') == 0) {
            # Cron Status is set to 1 for active
            $this->CronJob->activateCron('send_newsletter');

            $newsLetterData2 = $this->Newsletter->find('all', array('fields' => array('Newsletter.id', 'Newsletter.store_id', 'Newsletter.merchant_id', 'Newsletter.added_from'), 'conditions' => array('Newsletter.is_deleted' => 0, 'Newsletter.is_active' => 1, 'OR' => array('type' => array(1, 3)))));
            if (!empty($newsLetterData2)) {
                foreach ($newsLetterData2 as $news) {
                    $timeInterval = array();
                    if ($news['Newsletter']['added_from'] == 1) {//Store
                        $currentDateTime = $this->Webservice->getcurrentTime($news['Newsletter']['store_id'], 1);
                    } else {//Hq
                        $currentDateTime = date("Y-m-d H:i:s");
                    }
                    $currentDateTimeArr = explode(' ', $currentDateTime);
                    $cDate = $currentDateTimeArr[0]; //Y-m-d
                    $currentDateArr = explode('-', $currentDateTimeArr[0]);
                    $currentDate = intval($currentDateArr[2]);
                    $currentDay = intval($currentDateArr[1]);
                    $timeInterval1 = strtotime(substr($currentDateTimeArr[1], 0, 5));
                    //$timeInterval = substr($currentDateTimeArr[1], 0,5);
                    $timeInterval[] = date("H:i", strtotime('-1 minutes', $timeInterval1)) . ":00";
                    $timeInterval[] = date("H:i", strtotime('+1 minutes', $timeInterval1)) . ":00";
                    //echo "Current Date -   >" . $currentDate . "<br>";
                    //echo "Current Day -   >" . $currentDay . "<br>";
                    $this->NewsletterManagement->bindModel(
                            array(
                        'belongsTo' => array(
                            'Newsletter' => array(
                                'className' => 'Newsletter',
                                'foreignKey' => 'newsletter_id',
                                'conditions' => array('Newsletter.is_deleted' => 0, 'Newsletter.is_active' => 1),
                                'type' => 'INNER'
                            ),
                            'Store' => array(
                                'className' => 'Store',
                                'foreignKey' => 'store_id',
                                'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                                'type' => 'INNER'
                            )
                        )
                            ), false
                    );
                    $conditions1 = array('send_type' => 1, 'send_date' => $currentDate, 'timezone_send_time BETWEEN ? and ?' => $timeInterval, 'NewsletterManagement.newsletter_id' => $news['Newsletter']['id']);
                    $conditions2 = array('send_type' => 2, 'send_day' => $currentDay, 'timezone_send_time BETWEEN ? and ?' => $timeInterval, 'NewsletterManagement.newsletter_id' => $news['Newsletter']['id']);
                    $conditions3 = array('send_type' => 3, 'timezone_send_time BETWEEN ? and ?' => $timeInterval, 'NewsletterManagement.newsletter_id' => $news['Newsletter']['id']);
                    $conditions4 = array('send_type' => 4, 'timezone_send_time BETWEEN ? and ?' => $timeInterval, 'NewsletterManagement.newsletter_id' => $news['Newsletter']['id'], 'DATE(NewsletterManagement.specific_date)' => $cDate);
                    if ($news['Newsletter']['added_from'] == 2) {
                        $fields = array('NewsletterManagement.newsletter_id', 'Newsletter.name', 'Newsletter.content', 'NewsletterManagement.specific_date', 'Newsletter.merchant_id');
                        $this->NewsletterManagement->unbindModel(array('belongsTo' => array('Store')), false);
                    } else {
                        $fields = array('NewsletterManagement.store_id', 'NewsletterManagement.newsletter_id', 'Newsletter.name', 'Newsletter.content', 'NewsletterManagement.specific_date', 'Store.store_name', 'Store.email_id', 'Store.id', 'Store.address', 'Store.city', 'Store.state', 'Store.zipcode', 'Store.phone', 'Store.store_url');
                    }
                    $newsLetterData1 = $this->NewsletterManagement->find('all', array('fields' => $fields, 'conditions' => $conditions1));
                    $newsLetterData2 = $this->NewsletterManagement->find('all', array('fields' => $fields, 'conditions' => $conditions2));
                    $newsLetterData3 = $this->NewsletterManagement->find('all', array('fields' => $fields, 'conditions' => $conditions3));
                    $newsLetterData4 = $this->NewsletterManagement->find('all', array('fields' => $fields, 'conditions' => $conditions4));
                    $newsLetterData = array_merge($newsLetterData1, $newsLetterData2, $newsLetterData3, $newsLetterData4);
                    //prx($newsLetterData);
                    $emailSuccess = $this->DefaultTemplate->find('first', array('conditions' => array('DefaultTemplate.template_code' => 'newsletter', 'DefaultTemplate.is_default' => 1)));
                    if (!empty($newsLetterData) && !empty($emailSuccess)) {
                        foreach ($newsLetterData as $data) {
                            if (!empty($data['Store']['id'])) {
                                $userData = $this->User->find('all', array('fields' => array('User.email', 'User.fname', 'User.lname'), 'conditions' => array('User.store_id' => $data['Store']['id'], 'User.is_active' => 1, 'User.role_id' => array(4, 5), 'User.is_deleted' => 0, 'User.is_newsletter' => 1)));
                                if (!empty($userData)) {
                                    //pr($userData);
                                    foreach ($userData as $usr) {
                                        $fullName = $usr['User']['fname'] . " " . @$usr['User']['lname'];
                                        $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                                        $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                        $emailData = str_replace('{CONTENT}', $data['Newsletter']['content'], $emailData);
                                        $emailData = str_replace('{SUBJECT}', $data['Newsletter']['name'], $emailData);
                                        $emailData = str_replace('{STORE_NAME}', $data['Store']['store_name'], $emailData);
                                        $url = "http://" . $data['Store']['store_url'];
                                        $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $data['Store']['store_url'] . "</a>";
                                        $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                                        $storeAddress = $data['Store']['address'] . "<br>" . $data['Store']['city'] . ", " . $data['Store']['state'] . " " . $data['Store']['zipcode'];
                                        $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                        $storePhone = $data['Store']['phone'];
                                        $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                                        $this->Email->to = $usr['User']['email'];
                                        $this->Email->from = $data['Store']['email_id'];
                                        $this->Email->subject = $data['Newsletter']['name'];
                                        $this->set('data', $emailData);
                                        $this->Email->template = 'template';
                                        $this->Email->smtpOptions = array(
                                            'port' => "$this->smtp_port",
                                            'timeout' => '100',
                                            'host' => "$this->smtp_host",
                                            'username' => "$this->smtp_username",
                                            'password' => "$this->smtp_password"
                                        );
                                        $this->Email->sendAs = 'html';
                                        try {
                                            $this->Email->send();
                                        } catch (Exception $e) {

                                        }
                                    }
                                }
                            } elseif (!empty($data['Newsletter']['merchant_id'])) {
                                $storeEmail = $this->Merchant->getMerchantDetail($data['Newsletter']['merchant_id']);
                                $userData = $this->User->find('all', array('fields' => array('User.email', 'User.fname', 'User.lname'), 'conditions' => array('User.merchant_id' => $data['Newsletter']['merchant_id'], 'User.is_active' => 1, 'User.role_id' => array(4, 5), 'User.is_deleted' => 0, 'User.is_newsletter' => 1)));
                                if (!empty($userData) && !empty($storeEmail)) {
                                    foreach ($userData as $usr) {
                                        $fullName = $usr['User']['fname'] . " " . @$usr['User']['lname'];
                                        $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                                        $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                        $emailData = str_replace('{CONTENT}', $data['Newsletter']['content'], $emailData);
                                        $emailData = str_replace('{SUBJECT}', $data['Newsletter']['name'], $emailData);
                                        $emailData = str_replace('{STORE_NAME}', $storeEmail['Merchant']['company_name'], $emailData);
                                        $url = "http://" . $storeEmail['Merchant']['domain_name'];
                                        $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Merchant']['domain_name'] . "</a>";
                                        $emailData = str_replace('{STORE_URL}', $merchantUrl, $emailData);
                                        $storeAddress = $storeEmail['Merchant']['address'] . "<br>" . $storeEmail['Merchant']['city'] . ", " . $storeEmail['Merchant']['state'] . " " . $storeEmail['Merchant']['zipcode'];
                                        $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                        $emailData = str_replace('{STORE_PHONE}', $storeEmail['Merchant']['phone'], $emailData);
                                        $this->Email->to = $usr['User']['email'];
                                        $this->Email->from = $storeEmail['Merchant']['email'];
                                        $this->Email->subject = $data['Newsletter']['name'];
                                        $this->set('data', $emailData);
                                        $this->Email->template = 'template';
                                        $this->Email->smtpOptions = array(
                                            'port' => "$this->smtp_port",
                                            'timeout' => '100',
                                            'host' => "$this->smtp_host",
                                            'username' => "$this->smtp_username",
                                            'password' => "$this->smtp_password"
                                        );
                                        $this->Email->sendAs = 'html';
                                        try {
                                            $this->Email->send();
                                        } catch (Exception $e) {

                                        }
                                    }
                                }
                            }
                        }
                    }


                    foreach ($newsLetterData4 as $nkey => $newsdata) {
                        $this->Newsletter->updateAll(array('is_active' => 0), array('id' => $newsdata['NewsletterManagement']['newsletter_id']));
                    }
                }
            }

            # Cron Status is set to 0 for deActive
            $this->CronJob->deActivateCron('send_newsletter');
        }
    }

    public function expiresDateCron() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('Store');
        $this->loadModel('TimeZone');
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
        CakeLog::info('expiresDateCron Start', 'Cronjob');
        $this->TimeZone->unbindModel(array('hasMany' => array('Store')));
        $storeList = $this->Store->find('all', array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('id', 'time_zone_id'), 'recursive' => 2));
        //prx($storeList);
        foreach ($storeList as $list) {
            if (!empty($list['TimeZone']['code']) && !empty($list['Store']['id'])) {
                date_default_timezone_set($list['TimeZone']['code']);
                $date = date('Y-m-d');
                $store_id = $list['Store']['id'];
                $this->loadModel('ItemOffer');
                $itemOfferResults = $this->ItemOffer->find('list', array('fields' => array('id'), 'conditions' => array('ItemOffer.store_id' => $store_id, 'ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'DATE(ItemOffer.end_date) <' => $date)));
                if (!empty($itemOfferResults)) {
                    $this->ItemOffer->updateAll(array('is_active' => 0), array('id' => $itemOfferResults));
                }
                $this->loadModel('Offer');
                $offerResults = $this->Offer->find('list', array('fields' => array('id'), 'conditions' => array('Offer.store_id' => $store_id, 'Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'DATE(Offer.offer_end_date) <' => $date)));
                if (!empty($offerResults)) {
                    $this->Offer->updateAll(array('is_active' => 0), array('id' => $offerResults));
                }
                $this->loadModel('Coupon');
                $couponResults = $this->Coupon->find('list', array('fields' => array('id'), 'conditions' => array('Coupon.store_id' => $store_id, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0, 'DATE(Coupon.end_date) <' => $date)));
                if (!empty($couponResults)) {
                    $this->Coupon->updateAll(array('is_active' => 0), array('id' => $couponResults));
                }
                $this->loadModel('Item');
                $ItemResults = $this->Item->find('list', array('fields' => array('id'), 'conditions' => array('Item.store_id' => $store_id, 'Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.is_seasonal_item' => 1, 'DATE(Item.end_date) <' => $date)));
                if (!empty($ItemResults)) {
                    $this->Item->updateAll(array('is_active' => 0), array('id' => $ItemResults));
                }
            }
        }
        $this->menuAdd();
        $this->storeTemplates();
        CakeLog::info('expiresDateCron END', 'Cronjob');
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
                                //Start
                                //StoreSetting and ModulePermission first time flag entry
                                $this->loadModel('StoreSetting');
                                $this->loadModel('ModulePermission');
                                $settingData = $this->StoreSetting->findByStoreId($store_id, array('StoreSetting.id'));
                                if (empty($settingData)) {
                                    $data['store_id'] = $store_id;
                                    $this->StoreSetting->create();
                                    $this->StoreSetting->save($data);
                                    $this->ModulePermission->create();
                                    $this->ModulePermission->save($data);
                                }
                                //End
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
        }
    }

    public function birthDayCron() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('Store');
        $this->loadModel('User');
        $this->loadModel('SpecialDay');
        $this->loadModel('SpecialDayTime');
        $this->SpecialDay->bindModel(
                array(
            'belongsTo' => array(
                'SpecialDayTime' => array(
                    'className' => 'SpecialDayTime',
                    'foreignKey' => 'special_day_time_id',
                    'conditions' => array('SpecialDayTime.is_active' => 1, 'SpecialDayTime.is_deleted' => 0),
                    'fields' => array('time_in_minutes', 'id', 'special_day_time'),
                )
            ),
                )
                , false
        );
        $this->Store->bindModel(
                array(
            'hasOne' => array(
                'SpecialDay' => array(
                    'className' => 'SpecialDay',
                    'foreignKey' => 'store_id',
                    'conditions' => array('SpecialDay.is_active' => 1, 'SpecialDay.is_deleted' => 0, 'SpecialDay.default_special_day_id' => 1),
                    'fields' => array('id', 'store_id', 'default_special_day_id', 'template_message', 'sms_template', 'special_day_time_id'),
                )
            ),
                )
                , false
        );
        $storeList = $this->Store->find('all', array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('id', 'store_name', 'address', 'city', 'state', 'zipcode', 'phone', 'email_id', 'store_url'), 'recursive' => 2));
        foreach ($storeList as $list) {
            //pr($list);
            if (!empty($list['Store']['id'])) {
                if (!empty($list['SpecialDay'])) {
                    $storeDateArr = $this->Webservice->getcurrentTime($list['Store']['id'], 2);
                    if ($list['SpecialDay']['special_day_time_id'] == 3) {
                        $storeDate = date('Y-m-d', strtotime('-1 week', strtotime($storeDateArr)));
                    } elseif ($list['SpecialDay']['special_day_time_id'] == 1) {
                        $storeDate = date('Y-m-d', strtotime('-1 day', strtotime($storeDateArr)));
                    } elseif ($list['SpecialDay']['special_day_time_id'] == 2) {
                        $storeDate = date('Y-m-d', strtotime('-2 day', strtotime($storeDateArr)));
                    } elseif ($list['SpecialDay']['special_day_time_id'] == 4) {
                        $storeDate = date('Y-m-d', strtotime('-2 week', strtotime($storeDateArr)));
                    } else {
                        $storeDate = $storeDateArr;
                    }
                    $dateArr = explode('-', $storeDate);
                    $date = $dateArr[2];
                    $month = $dateArr[1];
                    $store_id = $list['Store']['id'];
                    $birthday = $this->User->find('all', array('fields' => array('id', 'email', 'fname', 'lname'), 'conditions' => array('User.store_id' => $store_id, 'User.is_active' => 1, 'User.is_emailnotification1' => 1, 'User.is_deleted' => 0, 'MONTH(User.dateOfBirth)' => $month, 'DAYOFMONTH(User.dateOfBirth)' => $date, 'User.role_id' => array(4))));
                    $subject = 'Happy Birthday To You!';
                    if (!empty($birthday)) {
                        foreach ($birthday as $usr) {
                            $fullName = $usr['User']['fname'] . " " . $usr['User']['lname'];
                            $emailData = $list['SpecialDay']['template_message'];
                            $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                            $emailData = str_replace('{STORE_NAME}', $list['Store']['store_name'], $emailData);
                            $storeAddress = $list['Store']['address'] . "<br>" . $list['Store']['city'] . ", " . $list['Store']['state'] . " " . $list['Store']['zipcode'];
                            $storePhone = $list['Store']['phone'];
                            $url = "http://" . $list['Store']['store_url'];
                            $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $list['Store']['store_url'] . "</a>";
                            $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                            $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                            $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                            $this->Email->to = $usr['User']['email'];
                            $this->Email->from = $list['Store']['email_id'];
                            $this->Email->subject = $subject;
                            $this->set('data', $emailData);
                            $this->Email->template = 'template';
                            $this->Email->smtpOptions = array(
                                'port' => "$this->smtp_port",
                                'timeout' => '100',
                                'host' => "$this->smtp_host",
                                'username' => "$this->smtp_username",
                                'password' => "$this->smtp_password"
                            );
                            $this->Email->sendAs = 'html';
                            try {
                                $this->Email->send();
                            } catch (Exception $e) {

                            }
                        }
                    }
                }
            }
        }
    }

    public function anniversaryCron() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('Store');
        $this->loadModel('User');
        $this->loadModel('SpecialDay');
        $this->loadModel('SpecialDayTime');
        $this->SpecialDay->bindModel(
                array(
            'belongsTo' => array(
                'SpecialDayTime' => array(
                    'className' => 'SpecialDayTime',
                    'foreignKey' => 'special_day_time_id',
                    'conditions' => array('SpecialDayTime.is_active' => 1, 'SpecialDayTime.is_deleted' => 0),
                    'fields' => array('time_in_minutes', 'id', 'special_day_time'),
                )
            ),
                )
                , false
        );
        $this->Store->bindModel(
                array(
            'hasOne' => array(
                'SpecialDay' => array(
                    'className' => 'SpecialDay',
                    'foreignKey' => 'store_id',
                    'conditions' => array('SpecialDay.is_active' => 1, 'SpecialDay.is_deleted' => 0, 'SpecialDay.default_special_day_id' => 2),
                    'fields' => array('id', 'store_id', 'default_special_day_id', 'template_message', 'sms_template', 'special_day_time_id'),
                )
            ),
                )
                , false
        );
        $storeList = $this->Store->find('all', array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('id', 'store_name', 'address', 'city', 'state', 'zipcode', 'phone', 'email_id', 'store_url'), 'recursive' => 2));
        foreach ($storeList as $list) {
            if (!empty($list['Store']['id'])) {
                if (!empty($list['SpecialDay'])) {
                    $storeDateArr = $this->Webservice->getcurrentTime($list['Store']['id'], 2);
                    if ($list['SpecialDay']['special_day_time_id'] == 3) {
                        $storeDate = date('Y-m-d', strtotime('-1 week', strtotime($storeDateArr)));
                    } elseif ($list['SpecialDay']['special_day_time_id'] == 1) {
                        $storeDate = date('Y-m-d', strtotime('-1 day', strtotime($storeDateArr)));
                    } elseif ($list['SpecialDay']['special_day_time_id'] == 2) {
                        $storeDate = date('Y-m-d', strtotime('-2 day', strtotime($storeDateArr)));
                    } elseif ($list['SpecialDay']['special_day_time_id'] == 4) {
                        $storeDate = date('Y-m-d', strtotime('-2 week', strtotime($storeDateArr)));
                    } else {
                        $storeDate = $storeDateArr;
                    }
                    $dateArr = explode('-', $storeDate);
                    $date = $dateArr[2];
                    $month = $dateArr[1];
                    $store_id = $list['Store']['id'];
                    $anniversary = $this->User->find('all', array('fields' => array('id', 'email', 'fname', 'lname', 'created'), 'conditions' => array('User.store_id' => $store_id, 'User.is_active' => 1, 'User.is_emailnotification' => 1, 'User.is_deleted' => 0, 'MONTH(User.created)' => $month, 'DAYOFMONTH(User.created)' => $date, 'User.role_id' => array(4, 5))));
                    $subject = 'Happy Anniversary To You!';
                    if (!empty($anniversary)) {
                        //pr($anniversary);
                        foreach ($anniversary as $usr) {
                            $anniversaryArr = explode(" ", $usr['User']['created']);
                            $anniversaryDate = $anniversaryArr[0];
                            $interval = $storeDateArr - $anniversaryDate;
                            $anniversaryYear = $this->addNumberSuffix($interval);
                            if ($interval > 0) {
                                $fullName = $usr['User']['fname'] . " " . $usr['User']['lname'];
                                $emailData = $list['SpecialDay']['template_message'];
                                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                $emailData = str_replace('{STORE_NAME}', $list['Store']['store_name'], $emailData);
                                $storeAddress = $list['Store']['address'] . "<br>" . $list['Store']['city'] . ", " . $list['Store']['state'] . " " . $list['Store']['zipcode'];
                                $storePhone = $list['Store']['phone'];
                                $url = "http://" . $list['Store']['store_url'];
                                $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $list['Store']['store_url'] . "</a>";
                                $emailData = str_replace('{YEAR_NUMBER}', $anniversaryYear, $emailData);
                                $emailData = str_replace('{YEAR}', $interval, $emailData);

                                if ($interval == 1) {
                                    $emailData = str_replace('{YEARS}', "year", $emailData);
                                } else {
                                    $emailData = str_replace('{YEARS}', "years", $emailData);
                                }


                                $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                                $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                                $this->Email->to = $usr['User']['email'];
                                $this->Email->from = $list['Store']['email_id'];
                                $this->Email->subject = $subject;
                                $this->set('data', $emailData);
                                $this->Email->template = 'template';
                                $this->Email->smtpOptions = array(
                                    'port' => "$this->smtp_port",
                                    'timeout' => '100',
                                    'host' => "$this->smtp_host",
                                    'username' => "$this->smtp_username",
                                    'password' => "$this->smtp_password"
                                );
                                $this->Email->sendAs = 'html';
                                try {
                                    $this->Email->send();
                                } catch (Exception $e) {

                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function specialTemplates() {
        $this->autoRender = false;
        $this->layout = false;
        $this->loadModel('Store');
        $storeListArr = $this->Store->find('all', array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('id', 'merchant_id')));
        $this->loadModel('DefaultSpecialDay');
        $this->loadModel('SpecialDay');
        $defaultSpecialDayData = $this->DefaultSpecialDay->find('all', array('conditions' => array('DefaultSpecialDay.is_active' => 1, 'DefaultSpecialDay.is_deleted' => 0)));
        foreach ($storeListArr as $storeList) {//echo "<br>===========<br>";
            foreach ($defaultSpecialDayData as $eData) {
                if (!empty($storeList['Store']['merchant_id'])) {
                    $emailTemp['SpecialDay']['store_id'] = $storeList['Store']['id'];
                    $emailTemp['SpecialDay']['merchant_id'] = $storeList['Store']['merchant_id'];
                    $emailTemp['SpecialDay']['default_special_day_id'] = $eData['DefaultSpecialDay']['id'];
                    $emailTemp['SpecialDay']['template_message'] = $eData['DefaultSpecialDay']['template_message'];
                    $emailTemp['SpecialDay']['sms_template'] = $eData['DefaultSpecialDay']['sms_template'];
                    $emailTemp['SpecialDay']['template_code'] = $eData['DefaultSpecialDay']['template_code'];
                    $emailTemp['SpecialDay']['special_day_time_id'] = 1;
                }

//                $templateNotExists = $this->EmailTemplate->checkTemplate($eData['DefaultTemplate']['template_code'], $merchantID);
//                if ($templateNotExists) {
                //pr($emailTemp);
                $this->SpecialDay->create();
                $this->SpecialDay->save($emailTemp);
                // }
            }
        }
    }

    /*     * ******************************************************************************************
      @Function Name : orderReminder
      @Description   : this function is used for send Order Reminder to Store Admin user based on set alarm Time
      @Author        : SmartData
      created:02/01/2017
     * ****************************************************************************************** */

    public function orderReminder() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('Order');
        $this->loadModel('DeliveryAddress');
        $this->loadModel('User');
        $this->loadModel('Item');
        $this->loadModel('OrderItem');
        $this->loadModel('CronJob');
        $this->loadModel('Store');
        if ($this->CronJob->checkCronCurrentStatus('order_notification') == 0) {
            # Cron Status is set to 1 for active
            $this->CronJob->activateCron('order_notification');
            $storeArr = $this->Store->find('all', array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('id')));
            if (!empty($storeArr)) {
                foreach ($storeArr as $storeDetail) {
                    $storeDate = $this->Webservice->getcurrentTime($storeDetail['Store']['id'], 1);
                    //echo "Store id: " . $storeDetail['Store']['id'] . "--->" . $storeDate . "<br>";
                    $this->Order->bindModel(array('belongsTo' => array('DeliveryAddress' => array('foreignKey' => 'delivery_address_id', 'fields' => array('id', 'name_on_bell')))), false);
                    $this->Order->bindModel(array(
                        'belongsTo' => array(
                            'User' => array(
                                'className' => 'User',
                                'foreignKey' => 'user_id',
                                'fields' => array('id', 'fname', 'lname'),
                                'conditions' => array('User.is_deleted' => 0, 'User.is_active' => 1)
                            ))
                            ), false);
                    $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')))), false);
                    $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'item_id')))), false);

                    $orderList = $this->Order->find('all', array('recursive' => 2, 'conditions' => array('Order.pickup_time >=' => $storeDate, 'Order.is_pre_order' => 1, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.store_id' => $storeDetail['Store']['id']), 'fields' => array('id', 'order_number', 'seqment_id', 'order_status_id', 'pickup_time', 'is_pre_order', 'user_id', 'store_id', 'merchant_id', 'delivery_address_id')));
                    //pr($orderList);

                    if (!empty($orderList)) {
                        $orderArr = array();
                        foreach ($orderList as $orderListArr) {
                            $orderArr = array();
                            $orderArr['order_id'] = $orderListArr['Order']['id'];
                            $store_id = $orderListArr['Order']['store_id'];
                            if ($orderListArr['Order']['seqment_id'] == 3) {
                                $orderArr['order_type'] = "Home Delivery";
                            } elseif ($orderListArr['Order']['seqment_id'] == 2) {
                                $orderArr['order_type'] = "Pickup";
                            }
                            if (!empty($orderListArr['Order']['pickup_time'])) {
                                $orderArr['Order_timing'] = $orderListArr['Order']['pickup_time'];
                            } else {
                                $orderArr['Order_timing'] = "";
                            }

                            if (!empty($orderListArr['DeliveryAddress']['name_on_bell'])) {
                                $orderArr['customer_name'] = $orderListArr['DeliveryAddress']['name_on_bell'];
                            } elseif (!empty($orderListArr['User']['fname'])) {
                                $orderArr['customer_name'] = $orderListArr['User']['fname'] . " " . $orderListArr['User']['lname'];
                            } else {
                                $orderArr['customer_name'] = "";
                            }
                            $orderArr['store_id'] = $orderListArr['Order']['store_id'];
                            $orderArr['notification_type'] = "3";
                            $orderNotification_type = "3";
                            $item_message = '';
                            if (!empty($orderListArr['OrderItem'])) {
                                $j = 2;
                                foreach ($orderListArr['OrderItem'] as $items) {
                                    if (!empty($items['Item']))
                                        $item_message .= $items['Item']['name'] . " " . $j . ". ";
                                    $j++;
                                }
                            }

                            $orderArr['order_message'] = $item_message;
                            $store_date = strtotime($storeDate);
                            $order_date = strtotime($orderListArr['Order']['pickup_time']);
                            $diff = abs($order_date - $store_date);
                            $minutes = round($diff / 60);
                            $message = "New Order";
                            if (!empty($orderArr)) {
                                $message = json_encode($orderArr);
                            }

                            $userDetArr = $this->AdminUser($orderListArr['Order']['store_id'], $orderListArr['Order']['merchant_id'], $message, $orderNotification_type, $minutes, $orderListArr['Order']['id']);
                        }
                    }
                }
            }
            $this->CronJob->deActivateCron('order_notification');
        }
    }

    /*     * ******************************************************************************************
      @Function Name : bookingReminder
      @Description   : this function is used for send Booking Reminder to Store Admin user based on set alarm Time
      @Author        : SmartData
      created:02/01/2017
     * ****************************************************************************************** */

    public function bookingReminder() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('Booking');
        $this->loadModel('User');
        $this->loadModel('CronJob');
        $this->loadModel('Store');
        if ($this->CronJob->checkCronCurrentStatus('booking_notification') == 0) {
            # Cron Status is set to 1 for active
            $this->CronJob->activateCron('booking_notification');
            $storeArr = $this->Store->find('all', array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('id')));
            // pr($storeArr);

            if (!empty($storeArr)) {
                foreach ($storeArr as $storeDetail) {
                    $storeDate = $this->Webservice->getcurrentTime($storeDetail['Store']['id'], 1);
                    $this->Booking->bindModel(array(
                        'belongsTo' => array(
                            'User' => array(
                                'className' => 'User',
                                'foreignKey' => 'user_id',
                                'fields' => array('id', 'fname', 'lname', 'email'),
                                'conditions' => array('User.is_deleted' => 0, 'User.is_active' => 1)
                            ))
                            ), false);
                    //echo "Store id: " . $storeDetail['Store']['id'] . "--->" . $storeDate . "<br>";
                    $bookingArr = $this->Booking->find('all', array('recursive' => 1, 'conditions' => array('Booking.reservation_date >=' => $storeDate, 'Booking.is_active' => 1, 'Booking.is_deleted' => 0, 'Booking.store_id' => $storeDetail['Store']['id'])));
                    //{"booking_id":"335","number_person":"1","special_request":"Please make my table clean.","customer_name":"Rishabh","booking_timing":"MM-dd-yyyy hh:mm a","notification_type":"2"}
                    $orderArr = array();
                    if (!empty($bookingArr)) {
                        foreach ($bookingArr as $bookingDetail) {
                            $orderArr['booking_id'] = $bookingDetail['Booking']['id'];
                            $store_id = $bookingDetail['Booking']['store_id'];

                            $orderArr['number_person'] = "";
                            if (!empty($bookingDetail['Booking']['number_person'])) {
                                $orderArr['number_person'] = $bookingDetail['Booking']['number_person'];
                            }

                            $orderArr['special_request'] = "";
                            if (!empty($bookingDetail['Booking']['special_request'])) {
                                $orderArr['special_request'] = $bookingDetail['Booking']['special_request'];
                            }


                            $orderArr['name'] = "";
                            if (!empty($bookingDetail['User']['fname'])) {
                                $orderArr['name'] = $bookingDetail['User']['fname'] . " " . $bookingDetail['User']['lname'];
                            }

                            $orderArr['email'] = "";
                            if (!empty($bookingDetail['User']['email'])) {
                                $orderArr['email'] = $bookingDetail['User']['email'];
                            }

                            $orderArr['is_replied'] = FALSE;
                            if ($bookingDetail['Booking']['is_replied'] == 1) {
                                $orderArr['is_replied'] = TRUE;
                            }


                            $orderArr['admin_comment'] = "";
                            if (!empty($bookingDetail['Booking']['admin_comment'])) {
                                $orderArr['admin_comment'] = $bookingDetail['Booking']['admin_comment'];
                            }


                            $orderArr['date'] = "";
                            $orderArr['time'] = "";
                            if (!empty($bookingDetail['Booking']['reservation_date'])) {
                                $dateTime = explode(" ", $bookingDetail['Booking']['reservation_date']);
                                $dateResBooking = explode("-", $dateTime[0]);
                                $finalResdate = $dateResBooking[2] . '/' . $dateResBooking[1] . '/' . $dateResBooking[0];
                                $orderArr['date'] = $finalResdate;
                                $orderArr['time'] = $dateTime[1];
                            }


                            $orderArr['placed_date'] = "";
                            $orderArr['placed_date'] = "";
                            if (!empty($bookingDetail['Booking']['created'])) {
                                $placedDateTime = explode(" ", $bookingDetail['Booking']['created']);
                                $dateBooking = explode("-", $placedDateTime[0]);
                                $finaldate = $dateBooking[2] . '/' . $dateBooking[1] . '/' . $dateBooking[0];
                                $orderArr['placed_date'] = $finaldate;
                                $orderArr['placed_time'] = $placedDateTime[1];
                            }

                            $orderArr['booking_status'] = "";
                            if (!empty($bookingDetail['Booking']['booking_status_id'])) {
                                if ($bookingDetail['Booking']['booking_status_id'] == 1) {
                                    $orderArr['booking_status'] = "Pending";
                                } elseif ($bookingDetail['Booking']['booking_status_id'] == 4) {
                                    $orderArr['booking_status'] = "Cancel";
                                } elseif ($bookingDetail['Booking']['booking_status_id'] == 5) {
                                    $orderArr['booking_status'] = "Booked";
                                }
                            }

                            $orderArr['store_id'] = $bookingDetail['Booking']['store_id'];
                            $orderArr['notification_type'] = "4";

                            $orderNotification_type = "4";
                            $store_date = strtotime($storeDate);
                            $order_date = strtotime($bookingDetail['Booking']['reservation_date']);
                            $diff = abs($order_date - $store_date);
                            $minutes = round($diff / 60);
                            $message = "New Order";
                            if (!empty($orderArr)) {
                                $message = json_encode($orderArr);
                            }

                            $userDetArr = $this->AdminUser($storeDetail['Store']['id'], null, $message, $orderNotification_type, $minutes, $bookingDetail['Booking']['id']);
                        }
                    }
                }
            }
            # Cron Status is set to 0 for deActive
            $this->CronJob->deActivateCron('booking_notification');
        }
    }

    public function AdminUser($storeId = null, $merchantId = null, $message = null, $orderNotification_type = null, $minutes = null, $orderReserId = null) {

        $this->loadModel('User');
        $this->loadModel('NotificationConfiguration');
        $this->loadModel('UserDevice');
        $this->loadModel('OrderCronJob');
        $this->loadModel('Booking');
        $this->loadModel('Order');
        $this->UserDevice->bindModel(array(
            'belongsTo' => array(
                'NotificationConfiguration' => array(
                    'className' => 'NotificationConfiguration',
                    'foreignKey' => 'notification_configuration_id',
                    'type' => 'LEFT',
                    'conditions' => array('NotificationConfiguration.is_active' => 1, 'NotificationConfiguration.is_deleted' => 0),
                    'fields' => array('id', 'order_notification', 'show_in_notification', 'sound', 'badge_app', 'add_alarm', 'alarm_time_id')
                ),
            )
                ), FALSE);
        $this->UserDevice->bindModel(array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => 3),
                    'fields' => array('id')
                ),
            )
                ), FALSE);
        $this->NotificationConfiguration->bindModel(array(
            'belongsTo' => array(
                'AlarmTime' => array(
                    'className' => 'AlarmTime',
                    'foreignKey' => 'alarm_time_id',
                    'conditions' => array('AlarmTime.is_active' => 1, 'AlarmTime.is_deleted' => 0),
                    'fields' => array('id', 'alarm_time', 'alarm_in_minutes')
                ),
            )
                ), FALSE);
        $userDet = $this->UserDevice->find('all', array('recursive' => 2, 'conditions' => array('UserDevice.is_active' => 1, 'UserDevice.is_deleted' => 0, 'UserDevice.store_id' => $storeId), 'fields' => array('UserDevice.id', 'UserDevice.notification_configuration_id', 'UserDevice.user_id', 'UserDevice.device_type', 'UserDevice.device_token')));

        //pr($userDet);
        $this->Webservice = $this->Components->load('Webservice');
        $date = $this->Webservice->getcurrentTime($storeId, 2);
        $todaysPendingBookings = $this->Booking->find('count', array('fields' => array('id'), 'conditions' => array('Booking.store_id' => $storeId, 'Booking.is_active' => 1, 'DATE(Booking.reservation_date)' => $date, 'Booking.booking_status_id' => 1, 'Booking.is_deleted' => 0)));
        $todaysPendingOrder = $this->Order->find('count', array('fields' => array('id'), 'conditions' => array('Order.is_future_order' => 0, 'Order.store_id' => $storeId, 'Order.is_active' => 1, 'DATE(Order.pickup_time)' => $date, 'Order.order_status_id' => 1)));
        $totalBookingOrder = (int) $todaysPendingBookings + $todaysPendingOrder;
        //echo $todaysPendingBookings."<br>";
        //echo $todaysPendingOrder."<br>";
        //echo $totalBookingOrder."<br>";
        //die;
        $deviceTokenAndroidIds = array();
        $deviceTokenIosIds = array();

        $deviceNotificationArr = array();
        if (!empty($userDet)) {
            foreach ($userDet as $deviceNotification) {
                //echo "---------------------------------------------<br>";
                //echo "User Id : ".$deviceNotification['User']['id']."<br>";
                //echo "UserDevice Token  : ".$deviceNotification['UserDevice']['device_token']."<br>";
                //echo $orderReserId;
                $orderCronDetArr = $this->orderCron($orderReserId, $storeId, $merchantId, $deviceNotification['User']['id'], $deviceNotification['UserDevice']['device_token']);
                //pr($orderCronDetArr);
                $a = 0;
                $i = 0;
                if (empty($orderCronDetArr)) {
                    if (!empty($deviceNotification['User'])) {
                        if (!empty($deviceNotification['NotificationConfiguration'])) {
                            if ($deviceNotification['NotificationConfiguration']['add_alarm'] == 1) {
                                if (!empty($deviceNotification['NotificationConfiguration']['AlarmTime'])) {
                                    $userSelectedMinute = $deviceNotification['NotificationConfiguration']['AlarmTime']['alarm_in_minutes'];
                                    if ($userSelectedMinute > 0) {
                                        $minRange = $userSelectedMinute - 3;
                                        $maxRange = $userSelectedMinute + 3;
                                        if ($minutes >= $minRange && $minutes <= $maxRange) {  //$minutes - >differnce between store time and order time
                                            if ($deviceNotification['UserDevice']['device_type'] == 'android') {
                                                $deviceTokenAndroidIds[$a] = $deviceNotification['UserDevice']['device_token'];
                                                $this->hitCurl($deviceTokenAndroidIds, $message, $orderNotification_type); //Android
                                                $a++;
                                            }
                                            if ($deviceNotification['UserDevice']['device_type'] == 'ios') {
                                                $deviceTokenIosIds[$i]['token'] = $deviceNotification['UserDevice']['device_token'];
                                                $deviceTokenIosIds[$i]['notification'] = $deviceNotification['NotificationConfiguration'];
                                                $this->hitSocket($deviceTokenIosIds, $message, $orderNotification_type, $totalBookingOrder); //IOS
                                                $i++;
                                            }
                                            if (!empty($orderReserId)) {
                                                $data['order_id'] = $orderReserId;
                                                $data['store_id'] = $storeId;
                                                $data['user_id'] = $deviceNotification['User']['id'];
                                                $data['device_token'] = trim($deviceNotification['UserDevice']['device_token']);
                                                $data['merchant_id'] = $merchantId;
                                                $this->OrderCronJob->create();
                                                $this->OrderCronJob->save($data);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function orderCron($orderResId = null, $storeId = null, $merchantId = null, $user_id = null, $deviceToken = null) {
        $this->loadModel('OrderCronJob');
        $orderCronDet = $this->OrderCronJob->find('first', array('conditions' => array('OrderCronJob.store_id' => $storeId, 'OrderCronJob.merchant_id' => $merchantId, 'OrderCronJob.is_active' => 1, 'OrderCronJob.is_deleted' => 0, 'OrderCronJob.order_id' => $orderResId, 'OrderCronJob.user_id' => $user_id, 'OrderCronJob.device_token' => trim($deviceToken)), 'fields' => array('OrderCronJob.id', 'OrderCronJob.order_id', 'OrderCronJob.user_id')));
        return $orderCronDet;
    }

    function hitCurl($gcmDeciceTokenId, $message, $orderNotification_type) {
        $tAlert = (array) json_decode($message);
        if ($orderNotification_type == 4) {
            $message = array('Reservation' => $tAlert, 'status' => 1, 'message' => 'This is reminder for booking.', 'notification_type' => $orderNotification_type);
            $fields = array(
                'registration_ids' => $gcmDeciceTokenId,
                'data' => $message,
            );
        } else {
            $message = array('Order' => $tAlert, 'status' => 1, 'message' => 'This is reminder for order.', 'notification_type' => $orderNotification_type);
            $fields = array(
                'registration_ids' => $gcmDeciceTokenId,
                'data' => $message,
            );
        }
        $json = json_encode($fields);
        //pr($json);
        //die;

        $headers = array(
            'Authorization:key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_ENCODING, '');
        //curl_setopt($ch, CURLOPT_NOBODY, true);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_URL, GCM_URL_ANDROID);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        //curl_setopt($ch, CURLOPT_HEADER, true); // header will be at output
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD'); // HTTP request is 'HEAD'
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // ADD THIS
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $result = curl_exec($ch);
        //pr($result);
        curl_close($ch);
        return $result;
    }

    function hitSocket($tToken = null, $message = null, $deviceNotificationArr = array(), $totalBookingOrder = null) {

        $tResult = array();
        $i = 0;
        $tAlert = array();
        if (!empty($message)) {
            $tAlert = (array) json_decode($message);
        } else {
            $tAlert['notification_type'] = '';
        }
        $tHost = IOS_PUSH_HOST;
        $tPort = IOS_PUSH_PORT;

        $tCert = IOS_PUSH_CERTIFICATE;
        $tPassphrase = IOS_PASSPHASE;
        //echo $tHost."<br>";
        //echo $tPort."<br>";
        //echo $tCert."<br>";
        if (!empty($tToken)) {
            foreach ($tToken as $deviceToken) {
                // Create the message content that is to be sent to the device.
                // $tBadge = 8;
                $tSound = "default";
                $tBody = array();
                if (!empty($tAlert)) {
                    if ($tAlert['notification_type'] == 4) {
                        $tBody['Reservation'] = $tAlert;
                        $msg = 'booking';
                    } else {
                        $tBody['Order'] = $tAlert;
                        $msg = 'order';
                    }
                }
                if (!empty($totalBookingOrder)) {
                    $tBadge = $totalBookingOrder;
                } else {
                    $tBadge = 1;
                }
                $tBody['aps'] = array('alert' => "This is reminder for " . $msg, 'badge' => $tBadge, 'sound' => $tSound);
                $tBody = json_encode($tBody);
                //pr($tBody);
                $tContext = stream_context_create();
                stream_context_set_option($tContext, 'ssl', 'local_cert', $tCert);
                // Remove this line if you would like to enter the Private Key Passphrase manually.
                stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);

                // Open the Connection to the APNS Server.
                // $tSocket = stream_socket_client ('ssl://'.$tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
                $tSocket = stream_socket_client('ssl://' . $tHost . ':' . $tPort, $error, $errorString, 30, STREAM_CLIENT_CONNECT, $tContext);

                // Check if we were able to open a socket.
                if (!$tSocket) {
                    continue;
                }
                //exit ("APNS Connection Failed: $error $errorString" . PHP_EOL);
                // Build the Binary Notification.
                //$tMsg = chr (0) . chr (0) . chr (32) . pack ('H*', trim($deviceToken['token'])) . pack ('n', strlen ($tBody)) . $tBody;
                if (empty($deviceToken['token'])) {
                    continue;
                }
                if ($deviceToken['token'] == 'SimulatorToken-668678778') {
                    continue;
                }
                $tMsg = chr(0) . chr(0) . chr(32) . pack('H*', trim($deviceToken['token'])) . pack('n', strlen($tBody)) . $tBody;
                $tResult[$i] = fwrite($tSocket, $tMsg, strlen($tMsg));
                //pr($tResult[$i]);
                $i++;
            }
            // Send the Notification to the Server.
            fclose($tSocket);
            //pr($tResult);
            return $tResult;
        }
    }

    function addNumberSuffix($num) {
        if (!in_array(($num % 100), array(11, 12, 13))) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1: return $num . '<sup>st</sup>';
                case 2: return $num . '<sup>nd</sup>';
                case 3: return $num . '<sup>rd</sup>';
            }
        }
        return $num . '<sup>th</sup>';
    }

    public function storeTemplates() {
        $this->loadModel('Store');
        $storeList = $this->Store->getStoreList();
        $this->loadModel('EmailTemplate');
        $this->loadModel('DefaultTemplate');
        //$template_code = array('reply_admin_to_customer');
        //$template_code = array('contact_to_store_admin');
        //$template_code = array('extended_offer','promotional_offer');
        $is_default = 0;
        $emailData = $this->DefaultTemplate->find('all', array('conditions' => array('is_default' => 0)));
        foreach ($storeList as $store) {//echo "<br>===========<br>";
            foreach ($emailData as $eData) {
                unset($eData['DefaultTemplate']['id'], $eData['DefaultTemplate']['is_active'], $eData['DefaultTemplate']['is_deleted'], $eData['DefaultTemplate']['created'], $eData['DefaultTemplate']['modified'], $eData['DefaultTemplate']['is_default']);
                $emailTemp['EmailTemplate'] = $eData['DefaultTemplate'];
                $emailTemp['EmailTemplate']['store_id'] = $store['Store']['id'];
                $emailTemp['EmailTemplate']['merchant_id'] = $store['Store']['merchant_id'];
                $templateNotExists = $this->EmailTemplate->checkStoreTemplate($eData['DefaultTemplate']['template_code'], $emailTemp['EmailTemplate']['store_id'], $emailTemp['EmailTemplate']['merchant_id']);
                if ($templateNotExists) {
                    //pr($merchantID);
                    $this->EmailTemplate->create();
                    $this->EmailTemplate->saveTemplate($emailTemp);
                }
            }
        }
    }

    public function callToMe() {
        configure::Write('debug', 2);
        $this->layout = false;
        $this->autoRender = false;
        $storeId = 149;
        $this->loadModel('Store');
        $settings = $this->Store->fetchStoreDetail($storeId);

        if (!empty($settings['Store']['twilio_api_key']) && !empty($settings['Store']['twilio_api_token']) && !empty($settings['Store']['twilio_number'])) {
            $tApikey = $settings['Store']['twilio_api_key'];
            $tApiToken = $settings['Store']['twilio_api_token'];
            $tApiNumber = $settings['Store']['twilio_number'];
            //App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
            App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'autoload.php'));
            //App::import('Vendor', 'Twilio/Rest/Client.php');

            App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Rest' . DS . 'Client.php'));
            //use Twilio\Rest\Client;
            // Step 3: Instantiate a new Twilio Rest Client
            $client = new Client($tApikey, $tApiToken);
            try {
                // Initiate a new outbound call
                $call = $client->account->calls->create(
                        // Step 4: Change the 'To' number below to whatever number you'd like
                        // to call.
                        "+919808117322",
                        // Step 5: Change the 'From' number below to be a valid Twilio number
                        // that you've purchased or verified with Twilio.
                        $tApiNumber,
                        // Step 6: Set the URL Twilio will request when the call is answered.
                        array("url" => "http://foodlovela.iorderfoods.com/hello-monkey.php")
                );
                echo "Started call: " . $call->sid;
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            die('END');
        }
    }

    public function voiceCall($toNumber = null) {
        configure::Write('debug', 2);
        $this->layout = false;
        $this->autoRender = false;
        App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
        //App::import('Vendor', array('file' =>DS.'autoload.php'));
        //header('Content-Type: text/xml');
        $from = "+18552271882";
        $callee = "+12133995508";
        if ($toNumber) {
            $callee = $toNumber;
        }
        $tApikey = "AC954e5b4acdc29986f96b44dd371eaf48";
        $tApiToken = "1407cf1cd0273cbbfd097392228d93c9";
        $client = new Services_Twilio($tApikey, $tApiToken);

        $call = $client->account->calls->create($from, $callee, "http://foodlovela.iorderfoods.com/hello-monkey.xml", array());
        echo $call->sid;

        file_put_contents('myfile.xml', '');
        $file = fopen("myfile.xml", "w");
        $text = '<?xml version="1.0" encoding="UTF-8"?><Response><Say voice="alice">This is final test for IorderFood</Say></Response>';
        fwrite($file, $text);
        fclose($file);
    }

    public function callToMe1() {
        configure::Write('debug', 2);
        $this->layout = false;
        $this->autoRender = false;
        $from = "+18552271882";
        $callee = "+919808113883";
        $tApikey = "AC954e5b4acdc29986f96b44dd371eaf48";
        $tApiToken = "1407cf1cd0273cbbfd097392228d93c9";
        App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
        $client = new Services_Twilio($tApikey, $tApiToken);
        //$from = "+91" . "" . "9808117322";
        $call = $client->account->calls->create($from, $callee, "http://foodlovela.iorderfoods.com/hello-monkey.xml", array());

        echo $call->sid;
    }

    public function notification() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('Item');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->loadModel('EmailTemplate');
        $this->loadModel('CountryCode');
        if ($this->CronJob->checkCronCurrentStatus('group_notification') == 0) {
            # Cron Status is set to 1 for active
            $this->CronJob->activateCron('send_newsletter');
            $orderDetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array('Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.group_notification_flag' => 0), 'order' => array('Order.created' => 'DESC')));
            //prx($orderDetail);
            if (!empty($orderDetail)) {
                foreach ($orderDetail as $orderData) {
                    $store_id = $orderData['Order']['store_id'];
                    $merchant_id = $orderData['Order']['merchant_id'];
                    $segment_type = $orderData['Order']['seqment_id'];
                    $orderId = $orderData['Order']['id'];
                    $this->Item->bindModel(array('belongsTo' => array('Category' => array('foreignKey' => 'category_id', 'fields' => array('name')))));
                    $this->Order->bindModel(array('belongsTo' => array('Segment' => array('foreignKey' => 'seqment_id', 'fields' => array('name')))));
                    $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))));
                    $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name', 'category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))));
                    $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount')))));
                    $result_order = $this->Order->getfirstOrder($merchant_id, $store_id, $orderId);
                    //prx($result_order);
                    if (!empty($result_order)) {
                        $this->loadModel('Store');
                        $storeEmail = $this->Store->fetchStoreDetail($store_id);
                        $order_type = "";
                        if (!empty($result_order['Segment']['name'])) {
                            $order_type = $result_order['Segment']['name'];
                        }
                        $emailSend = 1;
                        $smsNotification = 1;
                        if (!empty($result_order['Order']['user_id'])) {//registered user
                            $this->loadModel('User');
                            $this->User->bindModel(array(
                                'belongsTo' => array(
                                    'CountryCode' => array(
                                        'className' => 'CountryCode',
                                        'foreignKey' => 'country_code_id',
                                        'type' => 'INNER',
                                    ))
                                    ), false);
                            $userDetail = $this->User->find("first", array('recursive' => 2, 'conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0, 'User.merchant_id' => $merchant_id, 'User.role_id' => array(4, 5), 'User.id' => $result_order['Order']['user_id']), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname', 'User.phone', 'User.country_code_id', 'User.is_smsnotification', 'User.is_emailnotification')));
                            //pr($userDetail);
                            $country_code['CountryCode']['code'] = $userDetail['CountryCode']['code'];
                            $fullName = $userDetail['User']['fname'] . ' ' . $userDetail['User']['lname'];
                            $user_email = $userDetail['User']['email'];
                            $phone = $userDetail['User']['phone'];
                            if (empty($userDetail['User']['is_smsnotification'])) {
                                $smsNotification = 0;
                            }
                            if (empty($userDetail['User']['is_emailnotification'])) {
                                $emailSend = 0;
                            }
                        } else {//guest user
                            $userid = '';
                            $this->loadModel('DeliveryAddress');
                            $delivery_address_id = $result_order['Order']['delivery_address_id'];
                            $this->DeliveryAddress->bindModel(array(
                                'belongsTo' => array(
                                    'CountryCode' => array(
                                        'className' => 'CountryCode',
                                        'foreignKey' => 'country_code_id',
                                        'type' => 'INNER',
                                    ))
                                    ), false);
                            $delivery_address = $this->DeliveryAddress->fetchAddress($delivery_address_id, $userid, $store_id);
                            //prx($country_code);
                            $country_code['CountryCode']['code'] = $delivery_address['CountryCode']['code'];
                            $fullName = $delivery_address['DeliveryAddress']['name_on_bell'];
                            $user_email = $delivery_address['DeliveryAddress']['email'];
                            $phone = $delivery_address['DeliveryAddress']['phone'];
                        }
                        if ($result_order['Order']['is_pre_order'] == 1) {
                            $template_type = 'pre_order_receipt';
                        } else {
                            if ($result_order['Order']['seqment_id'] == 3) {
                                //$template_type = 'order_receipt';
                                $template_type = 'pre_order_receipt';
                            } else {
                                //$template_type = 'pickup_order_receipt';
                                $template_type = 'pre_order_receipt';
                            }
                        }
                        $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, $template_type);
                        if (!empty($emailSuccess)) {
			    $printdata = $this->Common->getOrderFaxFormat($orderId, $store_id, $merchant_id);	
                            if ($emailSend == 1) {//email to user for order placed detail
                                //prx($emailSuccess);
                                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                $preorderDateTime = $this->Common->storeTimeFormateUser($result_order['Order']['pickup_time'], true, $store_id);
                                if (isset($preorderDateTime) && !empty($preorderDateTime)) {
                                    $orderDateTime = explode(" ", $preorderDateTime);
                                    $date = $orderDateTime[0];
                                    $time = $orderDateTime[1];
                                    if (isset($orderDateTime[2]) && !empty($orderDateTime[2])) {
                                        $storeTimeAm = trim($orderDateTime[2]);
                                        $time = $time . $storeTimeAm;
                                    }
                                }
                                //echo $result_order['Order']['pickup_time']."<br>";
                                //echo $preorderDateTime;die;
				$emailData = str_replace('{PRE_ORDER_DATE_TIME}', $preorderDateTime, $emailData);
                                $emailData = str_replace('{ORDER_DETAIL}', $printdata, $emailData);
                                $emailData = str_replace('Order Id:', '', $emailData);
                                $emailData = str_replace('{ORDER_ID}', '', $emailData);
                                $emailData = str_replace('Total Amount:', '', $emailData);
                                $emailData = str_replace('{TOTAL}', '', $emailData);
                                $emailData = str_replace('Transaction Id :', '', $emailData);
                                $emailData = str_replace('{TRANSACTION_ID}', '', $emailData);
                                $url = "http://" . $storeEmail['Store']['store_url'];
                                $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                                $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                                $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);

                                $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                                $storePhone = $storeEmail['Store']['phone'];
                                $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                                $orderType = ($segment_type == 2) ? "Pick-up" : "Delivery";
                                $newSubject = "Your " . $storeEmail['Store']['store_name'] . " Online Order Confirmation #" . $result_order['Order']['order_number'] . "/" . $orderType;
                                $this->Email->to = $user_email;
                                $this->Email->subject = $newSubject;
                                $this->Email->from = $storeEmail['Store']['email_id'];
                                //pr($emailData);
                                $this->set('data', $emailData);
                                $this->Email->template = 'template';
                                $this->Email->smtpOptions = array(
                                    'port' => "$this->smtp_port",
                                    'timeout' => '100',
                                    'host' => "$this->smtp_host",
                                    'username' => "$this->smtp_username",
                                    'password' => "$this->smtp_password"
                                );
                                $this->Email->sendAs = 'html'; // because we like to send pretty mail
                                // $this->Email->delivery ='smtp';
                                try {
                                    $this->Email->send();
                                } catch (Exception $e) {
                                    
                                }
                                if ($smsNotification == 1) {//msg to user
                                    $mobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'), '', $phone);
                                    $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                                    $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                                    $smsData = str_replace('{ORDER_STATUS}', 'Pending', $smsData);
                                    $smsData = str_replace('{ORDER_NUMBER}', $result_order['Order']['order_number'], $smsData);
                                    $smsData = str_replace('{ORDER_DATE}', $date, $smsData);
                                    $smsData = str_replace('{ORDER_TIME}', $time, $smsData);
                                    $smsData = str_replace('{ORDER_TYPE}', $order_type, $smsData);
                                    $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                                    $smsData = str_replace('{PRE_ORDER_DATE_TIME}', $preorderDateTime, $smsData);
                                    $smsData = str_replace('{STORE_PHONE}', $storeEmail['Store']['notification_number'], $smsData);
                                    $message = $smsData;
                                    //pr($smsData);
                                    $this->Common->sendSmsNotificationFront($mobnumber, $message, $store_id);
                                }
                            }
                            try {//email to admin for new order
                                // Store ORder Email Notification
                                $this->loadModel('DefaultTemplate');
                                $template_type = 'order_notification';
                                $emailTemplate = $this->DefaultTemplate->adminTemplates($template_type);
                                $storeEmailData = $emailTemplate['DefaultTemplate']['template_message'];
                                $storesmsData = $emailTemplate['DefaultTemplate']['sms_template'];
                                //Store ORder Email Notification
                                if (($storeEmail['Store']['notification_type'] == 1 || $storeEmail['Store']['notification_type'] == 3) && (!empty($storeEmail['Store']['notification_email']))) {
                                    $EncorderID = $this->Encryption->encode($orderId);
                                    $url = "http://" . $storeEmail['Store']['store_url'];
                                    $surl = $url . '/orders/confirmOrder/' . $EncorderID;
                                    $orderconHtml = '<table style="width: 550px; height: 100px; margin :0 auto;" border="0" cellpadding="10" cellspacing="0"><tbody><tr><td style="text-align:center;">';
                                    $orderconHtml .= '<a href="' . $surl . '" style="padding:15px 15px;background-color:#F1592A;color:#FFFFFF;font-weight:bold;text-decoration: none;border:1px solid #000000;">CONFIRM ORDER</a></td></tr></tbody></table> ';
                                    $storeEmailData = ''
                                        . ''
                                        . '<table style="width: 100%; border: none; font-size: 14px;">'
                                            . '<tr>'
                                                . '<td style="width: 100%;">'
                                                    . '<table style="border:2px solid #000; margin :0 auto;">'
                                                        . '<tr>'
                                                            . '<td>'
                                                                . $orderconHtml . $printdata
                                                            . '</td>'
                                                        . '</tr>'
                                                    . '</table>'
                                                . '</td>'
                                            . '</tr>'
                                        . '</table>';
                                    $subject = ucwords(str_replace('_', ' ', $emailTemplate['DefaultTemplate']['template_subject']));
                                    $this->Email->to = $storeEmail['Store']['notification_email'];
                                    $this->Email->subject = $subject;
                                    $this->Email->from = $storeEmail['Store']['email_id'];
                                    //pr($storeEmailData);
                                    $this->set('data', $storeEmailData);
                                    $this->Email->template = 'template';
                                    $this->Email->smtpOptions = array(
                                        'port' => "$this->smtp_port",
                                        'timeout' => '100',
                                        'host' => "$this->smtp_host",
                                        'username' => "$this->smtp_username",
                                        'password' => "$this->smtp_password"
                                    );
                                    $this->Email->sendAs = 'html';
                                    $this->Email->send();
                                }

                                // STore Order Notification via SMS
                                if (($storeEmail['Store']['notification_type'] == 2 || $storeEmail['Store']['notification_type'] == 3) && (!empty($storeEmail['Store']['notification_number']))) {
                                    $storemobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'), '', $storeEmail['Store']['notification_number']);
                                    if ($storesmsData) {
                                        $storesmsData = str_replace('{ORDER_NUMBER}', $result_order['Order']['order_number'], $storesmsData);
                                        $storesmsData = str_replace('{ORDER_DATE}', $date, $storesmsData);
                                        $storesmsData = str_replace('{ORDER_TIME}', $time, $storesmsData);
                                        $storesmsData = str_replace('{ORDER_TYPE}', $order_type, $storesmsData);
                                        $storesmsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $storesmsData);
                                        $storesmsData = str_replace('{STORE_PHONE}', $storeEmail['Store']['notification_number'], $storesmsData);
                                        //prx($storesmsData);
                                        $this->Common->sendSmsNotificationFront($storemobnumber, $storesmsData, $store_id);
                                    }
                                }
                                
                                
                                
                                //sms notification to admin using call
                                $this->loadModel('StoreSetting');
                                $storeSetting = $this->StoreSetting->findByStoreId($store_id);
                                if (!empty($storeSetting['StoreSetting']['twilio_voice_allow'])) {
                                    $this->loadModel('MainSiteSetting');
                                    $configInfo = $this->MainSiteSetting->getSiteSettings();
                                    if (!empty($configInfo['MainSiteSetting']['twilio_api_key']) && !empty($configInfo['MainSiteSetting']['twilio_api_token']) && !empty($configInfo['MainSiteSetting']['twilio_number']) && !empty($storeEmail['Store']['notification_voice'])) {
                                        //file_put_contents('order_notification.xml', '');
                                        $date = date("MdY");
                                        $target_dir = WWW_ROOT . '/Notification/' . $date;
                                        if (!file_exists($target_dir)) {
                                            (new Folder($target_dir, true, 0777));
                                        }
                                        $target_file = $target_dir . '/' . $orderData['Order']['order_number'] . '.xml';
                                        $file = fopen($target_file, "w");
                                        $totalAmountS = ($orderData['Order']['amount'] - $orderData['Order']['coupon_discount']);
                                        $text = '<?xml version="1.0" encoding="UTF-8"?><Response><Say voice="alice">Dear store owner,</Say> <Pause length="1"></Pause> <Say voice="alice">you have received an order</Say> <Pause length="1"></Pause> <Say voice="alice">order id ' . implode(',', str_split($orderData['Order']['order_number'])) . '</Say> <Pause length="1"></Pause> <Say voice="alice">order total $' . $totalAmountS . '. Thank you!</Say></Response>';
                                        fwrite($file, $text);
                                        fclose($file);
                                        App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
                                        $to = $callee = str_replace(array('(', ')', ' ', '-'), '', $storeEmail['Store']['notification_voice']);
                                        $tApikey = $configInfo['MainSiteSetting']['twilio_api_key'];
                                        $tApiToken = $configInfo['MainSiteSetting']['twilio_api_token'];
                                        $from = $tApiNumber = $configInfo['MainSiteSetting']['twilio_number'];
                                        $client = new Services_Twilio($tApikey, $tApiToken);
                                        $folderName = $date;
                                        $fileName = $orderData['Order']['order_number'] . '.xml';
                                        $call = $client->account->calls->create($from, $to, HTTP_ROOT . "order_notification.php?folderName=" . $folderName . "&fileName=" . $fileName, array());
                                    }
                                }
                                
                            } catch (Exception $e) {
                                
                            }
                            try {
                                $this->Webservice->orderPushNotification($orderId);
                            } catch (Exception $e) {
                                
                            }
                            $this->Order->UpdateAll(array('Order.group_notification_flag' => 1), array('Order.id' => $orderId));
                        }
                    }//End order data
                    $this->loadModel('StoreSetting');
                    $storeSetting = $this->StoreSetting->findByStoreId($store_id);
                    if (!empty($storeSetting) && !empty($storeSetting['StoreSetting']['fax_allow'])) {
                        try {
                            $this->orderFaxrelay($orderId, $store_id, $merchant_id);
                        } catch (Exception $e) {
                            
                        }
                    }
                }
            }

            # Cron Status is set to 0 for deActive
            $this->CronJob->deActivateCron('group_notification');
        }
        /*         * ***************************************************************** */
    }

}
