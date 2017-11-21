<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('StoreAppController', 'Controller');

class OrderOverviewsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Session');
    public $uses = array('StoreAvailability', 'OrderItem', 'StoreContent', 'Store', 'BookingStatus', 'Booking', 'StoreReview', 'OrderItem', 'Item', 'StoreHoliday');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('getOrderDetail', 'getDateTime', 'ajaxOrderDetailSave', 'checkDeliverType', 'editGuestOrderDetail', 'deleteDeliveryAddress', 'chkMinPmtAmt');
        $closedDay = array();
        $storeId = $this->Session->read('store_id');
        $storeavaibilityInfo = $this->StoreAvailability->getclosedDay($storeId);
        $daysarray = array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
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
        
        $this->loadModel('StoreSetting');
        $storeSetting = $this->StoreSetting->findByStoreId($storeId);
        $this->set('storeSetting', $storeSetting);
        
        
    }

    function getDateTime() {
        $this->layout = false;
        $this->autoRender = false;
        $today = 1;
        $storeId = $this->Session->read('store_id');
        $merchantId = $this->Session->read('merchant_id');
        $order_type = $this->request->data['orderType'];
        if ($order_type == 2) {
            $this->Session->delete('Zone');
        }
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $finaldata = $this->_getdate($storeId, $merchantId, $order_type, $today, $current_date);
        $time_break = $finaldata['time_break'];
        $store_data = $finaldata['store_data'];
        $storeBreak = $finaldata['storeBreak'];
        $time_range = $finaldata['time_range'];
        $current_date = $finaldata['currentdate'];
        $setPre = $finaldata['setPre'];
        $explodeVal = explode("-", $current_date);
        $currentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $nowAvail = $this->Store->getNowAvailability($order_type, $storeId);
        $encrypted_storeId = $this->Encryption->encode($storeId);
        $encrypted_merchantId = $this->Encryption->encode($merchantId);
        $this->set(compact('nowAvail', 'storeBreak', 'setPre', 'time_break', 'time_range', 'store_data', 'currentDateVar', 'encrypted_storeId', 'encrypted_merchantId'));
        if ($order_type == 2) {
            $this->render('/Elements/orderoverview/pickup');
        } elseif ($order_type == 3) {
            $this->render('/Elements/orderoverview/delivery');
        }
    }

    function _getdate($storeId, $merchantId, $orderType, $today, $current_date) {
        $this->loadModel('Store');
        $this->loadModel('StoreHoliday');
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        return $finaldata;
    }

    /* ------------------------------------------------
      Function name:getDeliveryAddress()
      Description:get delivery address
      created:29/09/2016
      ----------------------------------------------------- */

    public function getDeliveryAddress($id = null, $returnHTML = false) {
        $this->layout = false;
        $this->loadModel('DeliveryAddress');
        if (!empty($id)) {
            $this->request->data['deliveryId'] = $id;
        }
        if ($this->request->is(array('ajax', 'put', 'post'))) {
            $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
            if (empty($this->request->data['deliveryId'])) {
                $delivery = $this->DeliveryAddress->fetchfirstAddress(AuthComponent::User('id'));
                if (!empty($delivery)) {
                    $deliveryID = $delivery['DeliveryAddress']['id'];
                } else {
                    $deliveryID = '';
                }
            } else {
                $deliveryID = $this->request->data['deliveryId'];
            }
            $resultAddress = $this->DeliveryAddress->fetchAddress($deliveryID);
            $this->set(compact('resultAddress'));
        }

        if ($returnHTML) {
            $this->autoRender = false;
            $viewObject = new View($this, false);
            $HTML = $viewObject->render("get_delivery_address");
            return $HTML;
        }
    }

    public function getAddressDetail() {
        $this->layout = false;
        if ($this->request->is('ajax') && $this->request->data['addressId']) {
            $addressId = $this->Encryption->decode($this->request->data['addressId']);
            $this->loadModel('CountryCode');
            $countryCode = $this->CountryCode->fetchAllCountryCode();
            $this->loadModel('DeliveryAddress');
            $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
            $resultAddress = $this->DeliveryAddress->fetchAddress($addressId);
            $this->request->data = $resultAddress;
            $this->set(compact('addressId', 'countryCode'));
        }
    }

    public function updateDeliveryAddress() {
        $this->layout = false;
        //$this->autoRender = false;
        if ($this->request->is(array('ajax', 'put', 'post'))) {
            parse_str($this->request->data['formData'], $data);
            $this->request->data = $data['data'];
            $storeId = $this->Session->read('store_id');
            $merchantId = $this->Session->read('merchant_id');
            $zipCode = trim($this->request->data['DeliveryAddress']['zipcode'], " ");
            $stateName = trim($this->data['DeliveryAddress']['state'], " ");
            $cityName = strtolower($this->request->data['DeliveryAddress']['city']);
            $cityName = trim(ucwords($cityName));
            $address = trim(ucwords($this->request->data['DeliveryAddress']['address']));
            $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
            $adjuster_address2 = str_replace(' ', '+', $dlocation);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
            $output = json_decode($geocode);
            $this->request->data['DeliveryAddress']['id'] = $this->Encryption->decode($this->request->data['DeliveryAddress']['id']);
            $this->request->data['DeliveryAddress']['user_id'] = AuthComponent::User('id');
            $this->request->data['DeliveryAddress']['store_id'] = $storeId;
            $this->request->data['DeliveryAddress']['merchant_id'] = $merchantId;
            if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                
            } else {
                $latitude = @$output->results[0]->geometry->location->lat;
                $longitude = @$output->results[0]->geometry->location->lng;
                $this->request->data['DeliveryAddress']['latitude'] = $latitude;
                $this->request->data['DeliveryAddress']['longitude'] = $longitude;
            }

            $this->loadModel('DeliveryAddress');
            if ($this->request->data['DeliveryAddress']['default'] == 1) {
                $this->DeliveryAddress->updateAll(array('DeliveryAddress.default' => 0), array('DeliveryAddress.user_id' => $this->request->data['DeliveryAddress']['user_id']));
            }
            $zoneData = $this->Common->addressInZone($this->request->data);
            if (empty($zoneData)) {
                $this->autoRender = false;
                $response['msg'] = "Order cannot be delivered to this address.";
                $response['status'] = "Error";
                return json_encode($response);
            } else {
                $result_sucess = $this->DeliveryAddress->saveAddress($this->request->data);
                if ($result_sucess) {
                    $html = $this->getDeliveryAddress($this->request->data['DeliveryAddress']['id'], true);
                    return $html;
                } else {
                    return false;
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name:deleteDeliveryAddress()
      Description: Delete delivery address of user
      created:29/09/2016
      ----------------------------------------------------- */

    public function deleteDeliveryAddress() {
        $this->autoRender = false;
        $this->layout = false;
        if ($this->request->is(array('ajax', 'put', 'post')) && !empty($this->request->data['addressId'])) {
            $this->loadModel('DeliveryAddress');
            $data['DeliveryAddress']['id'] = $this->Encryption->decode($this->request->data['addressId']);
            $data['DeliveryAddress']['is_deleted'] = 1;
            if ($this->DeliveryAddress->saveAddress($data)) {
                $userId = AuthComponent::User('id'); // Customer Id
                $roleId = AuthComponent::User('role_id');
                $storeId = $this->Session->read('store_id');
                $merchantId = $this->Session->read('merchant_id');
                $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
                $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $storeId, $merchantId); // It will call the function in the model to check the address either exist or not
                $this->set(compact('checkaddress', 'countryCode'));
                $this->render('/OrderOverviews/delivery_address');
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:addAddress()
      Description:This section will add the delivery address portion
      created:29/09/2016
      ----------------------------------------------------- */

    public function addAddress() {
        $this->layout = false;
        $this->loadModel('DeliveryAddress');
        $userId = AuthComponent::User('id'); // Customer Id
        $roleId = AuthComponent::User('role_id');
        $storeId = $this->Session->read('store_id');
        $merchantId = $this->Session->read('merchant_id');
        $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $storeId, $merchantId); // It will call the function in the model to check the address either exist or not
        $label1 = 0;
        $label2 = 0;
        $label3 = 0;
        $label4 = 0;
        $label5 = 0;
        if (!empty($checkaddress)) {
            foreach ($checkaddress as $address) {
                if ($address['DeliveryAddress']['label'] == 1) {
                    $label1 = 1;
                } elseif ($address['DeliveryAddress']['label'] == 2) {
                    $label2 = 1;
                } elseif ($address['DeliveryAddress']['label'] == 3) {
                    $label3 = 1;
                } elseif ($address['DeliveryAddress']['label'] == 4) {
                    $label4 = 1;
                } elseif ($address['DeliveryAddress']['label'] == 5) {
                    $label5 = 1;
                }
            }
        }

        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('label1', 'label2', 'label3', 'label4', 'label5', 'countryCode'));
    }

    public function ajaxAddressAdd() {
        $this->layout = false;
        $result = false;
        if ($this->request->is(array('ajax', 'post'))) {
            $userId = AuthComponent::User('id'); // Customer Id
            $roleId = AuthComponent::User('role_id');
            $storeId = $this->Session->read('store_id');
            $merchantId = $this->Session->read('merchant_id');
            parse_str($this->request->data['formData'], $data);
            $tmp = $data['data'];
            if (!empty($tmp['DeliveryAddress']) || !empty($tmp['DeliveryAddress1']) || !empty($tmp['DeliveryAddress2']) || !empty($tmp['DeliveryAddress3']) || !empty($tmp['DeliveryAddress4'])) {
                $this->loadModel('DeliveryAddress');
                $zoneError = '';
                if (isset($tmp['DeliveryAddress'])) {
                    $zipCode = trim($tmp['DeliveryAddress']['zipcode'], " ");
                    $stateName = trim($tmp['DeliveryAddress']['state'], " ");
                    $cityName = strtolower($tmp['DeliveryAddress']['city']);
                    $cityName = trim(ucwords($cityName));
                    $address = trim(ucwords($tmp['DeliveryAddress']['address']));
                    $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                    $adjuster_address2 = str_replace(' ', '+', $dlocation);
                    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
                    $output = json_decode($geocode);
                    $tmp['DeliveryAddress']['user_id'] = $userId;
                    $tmp['DeliveryAddress']['store_id'] = $storeId;
                    $tmp['DeliveryAddress']['merchant_id'] = $merchantId;
                    if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                        
                    } else {
                        $latitude = @$output->results[0]->geometry->location->lat;
                        $longitude = @$output->results[0]->geometry->location->lng;
                        $tmp['DeliveryAddress']['latitude'] = $latitude;
                        $tmp['DeliveryAddress']['longitude'] = $longitude;
                    }
                    $tmp['DeliveryAddress']['label'] = 1;
                    $data['DeliveryAddress'] = $tmp['DeliveryAddress'];
                    $zoneData = $this->Common->addressInZone($data);
                    if (empty($zoneData)) {
                        $zoneError.= "Order cannot be delivered to home address.<br />";
                    } else {
                        $response['label1'] = 'Error';
                        $this->DeliveryAddress->create();
                        $result = $this->DeliveryAddress->saveAddress($data);
                    }
                }
                if (isset($tmp['DeliveryAddress1'])) {
                    $zipCode = trim($tmp['DeliveryAddress1']['zipcode'], " ");
                    $stateName = trim($tmp['DeliveryAddress1']['state'], " ");
                    $cityName = strtolower($tmp['DeliveryAddress1']['city']);
                    $cityName = trim(ucwords($cityName));
                    $address = trim(ucwords($tmp['DeliveryAddress1']['address']));
                    $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                    $adjuster_address2 = str_replace(' ', '+', $dlocation);
                    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
                    $output = json_decode($geocode);
                    $tmp['DeliveryAddress1']['user_id'] = $userId;
                    $tmp['DeliveryAddress1']['store_id'] = $storeId;
                    $tmp['DeliveryAddress1']['merchant_id'] = $merchantId;
                    if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                        
                    } else {
                        $latitude = @$output->results[0]->geometry->location->lat;
                        $longitude = @$output->results[0]->geometry->location->lng;
                        $tmp['DeliveryAddress1']['latitude'] = $latitude;
                        $tmp['DeliveryAddress1']['longitude'] = $longitude;
                    }
                    $tmp['DeliveryAddress1']['label'] = 2;
                    $data1['DeliveryAddress'] = $tmp['DeliveryAddress1'];
                    $zoneData = $this->Common->addressInZone($data1);
                    if (empty($zoneData)) {
                        $zoneError.= "Order cannot be delivered to work address.<br />";
                    } else {
                        $response['label2'] = 'Error';
                        $this->DeliveryAddress->create();
                        $result = $this->DeliveryAddress->saveAddress($data1);
                    }
                }
                if (isset($tmp['DeliveryAddress2'])) {
                    $zipCode = trim($tmp['DeliveryAddress2']['zipcode'], " ");
                    $stateName = trim($tmp['DeliveryAddress2']['state'], " ");
                    $cityName = strtolower($tmp['DeliveryAddress2']['city']);
                    $cityName = trim(ucwords($cityName));
                    $address = trim(ucwords($tmp['DeliveryAddress2']['address']));
                    $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                    $adjuster_address2 = str_replace(' ', '+', $dlocation);
                    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
                    $output = json_decode($geocode);
                    $tmp['DeliveryAddress2']['user_id'] = $userId;
                    $tmp['DeliveryAddress2']['store_id'] = $storeId;
                    $tmp['DeliveryAddress2']['merchant_id'] = $merchantId;
                    if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                        
                    } else {
                        $latitude = @$output->results[0]->geometry->location->lat;
                        $longitude = @$output->results[0]->geometry->location->lng;
                        $tmp['DeliveryAddress2']['latitude'] = $latitude;
                        $tmp['DeliveryAddress2']['longitude'] = $longitude;
                    }
                    $tmp['DeliveryAddress2']['label'] = 3;
                    $data2['DeliveryAddress'] = $tmp['DeliveryAddress2'];
                    $zoneData = $this->Common->addressInZone($data2);
                    if (empty($zoneData)) {
                        $zoneError.= "Order cannot be delivered to other address.<br />";
                    } else {
                        $response['label3'] = 'Error';
                        $this->DeliveryAddress->create();
                        $result = $this->DeliveryAddress->saveAddress($data2);
                    }
                }
                if (isset($tmp['DeliveryAddress3'])) {
                    $zipCode = trim($tmp['DeliveryAddress3']['zipcode'], " ");
                    $stateName = trim($tmp['DeliveryAddress3']['state'], " ");
                    $cityName = strtolower($tmp['DeliveryAddress3']['city']);
                    $cityName = trim(ucwords($cityName));
                    $address = trim(ucwords($tmp['DeliveryAddress3']['address']));
                    $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                    $adjuster_address3 = str_replace(' ', '+', $dlocation);
                    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address3 . '&sensor=false');
                    $output = json_decode($geocode);
                    $tmp['DeliveryAddress3']['user_id'] = $userId;
                    $tmp['DeliveryAddress3']['store_id'] = $storeId;
                    $tmp['DeliveryAddress3']['merchant_id'] = $merchantId;
                    if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                        
                    } else {
                        $latitude = @$output->results[0]->geometry->location->lat;
                        $longitude = @$output->results[0]->geometry->location->lng;
                        $tmp['DeliveryAddress3']['latitude'] = $latitude;
                        $tmp['DeliveryAddress3']['longitude'] = $longitude;
                    }
                    $tmp['DeliveryAddress3']['label'] = 4;
                    $data3['DeliveryAddress'] = $tmp['DeliveryAddress3'];
                    $zoneData = $this->Common->addressInZone($data3);
                    if (empty($zoneData)) {
                        $zoneError.= "Order cannot be delivered to address 4.<br />";
                    } else {
                        $response['label4'] = 'Error';
                        $this->DeliveryAddress->create();
                        $result = $this->DeliveryAddress->saveAddress($data3);
                    }
                }

                if (isset($tmp['DeliveryAddress4'])) {
                    $zipCode = trim($tmp['DeliveryAddress4']['zipcode'], " ");
                    $stateName = trim($tmp['DeliveryAddress4']['state'], " ");
                    $cityName = strtolower($tmp['DeliveryAddress4']['city']);
                    $cityName = trim(ucwords($cityName));
                    $address = trim(ucwords($tmp['DeliveryAddress4']['address']));
                    $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                    $adjuster_address4 = str_replace(' ', '+', $dlocation);
                    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address4 . '&sensor=false');
                    $output = json_decode($geocode);
                    $tmp['DeliveryAddress4']['user_id'] = $userId;
                    $tmp['DeliveryAddress4']['store_id'] = $storeId;
                    $tmp['DeliveryAddress4']['merchant_id'] = $merchantId;
                    if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                        
                    } else {
                        $latitude = @$output->results[0]->geometry->location->lat;
                        $longitude = @$output->results[0]->geometry->location->lng;
                        $tmp['DeliveryAddress4']['latitude'] = $latitude;
                        $tmp['DeliveryAddress4']['longitude'] = $longitude;
                    }
                    $tmp['DeliveryAddress4']['label'] = 5;
                    $data4['DeliveryAddress'] = $tmp['DeliveryAddress4'];
                    $zoneData = $this->Common->addressInZone($data4);
                    if (empty($zoneData)) {
                        $zoneError.= "Order cannot be delivered to address 5.<br />";
                    } else {
                        $response['label5'] = 'Error';
                        $this->DeliveryAddress->create();
                        $result = $this->DeliveryAddress->saveAddress($data4);
                    }
                }
                if (!empty($zoneError)) {
                    $this->autoRender = false;
                    $response['msg'] = $zoneError;
                    return json_encode($response);
                }
            }
        }
        if (!empty($result)) {
            $addressId = $this->DeliveryAddress->getLastInsertID();
            $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
            $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $storeId, $merchantId); // It will call the function in the model to check the address either exist or not
            $this->set(compact('checkaddress', 'countryCode', 'addressId'));
            $this->render('/OrderOverviews/delivery_address');
        } else {
            $this->autoRender = false;
            return false;
        }
    }

    public function ajaxOrderDetailSave() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'post', 'put'))) {
            parse_str($this->request->data['formData'], $data);
            if (empty($data['data']['Order']['type'])) {
                $response['status'] = 'Error';
                $response['msg'] = "Order type is not selected, please refresh page and try again.";
                return json_encode($response);
            }
            //prx($data);
            unset($data['_Token']);
            $oDetail = array();
            $userId = AuthComponent::User('id'); // Customer Id
            if (!empty($data['data']['Order']['type']) && $data['data']['Order']['type'] == 3) {
                if (!empty($_SESSION['cart'])) {
                    $in = false;
                    $response['msg'] = 'The items below are non-deliverable:<br><br>';
                    foreach ($_SESSION['cart'] as $cItem) {
                        if ($cItem['Item']['is_deliverable'] == 0) {
                            $in = true;
                            $response['status'] = 'Error';
                            $response['msg'] .= $cItem['Item']['name'] . "<br>";
                        }
                    }
                    if ($in) {
                        $response['msg'] .= "<br>Please either delete or change your order type.";
                        return json_encode($response);
                    }
                }
            }
            if (empty($this->_checkStoreStatus($data['data']['Order']['type']))) {
                $response['status'] = 'Error';
                $response['msg'] = "Currently store is not taking orders, please visit after some time.";
                return json_encode($response);
            };
            if (!empty($data['data']['Store']['pickup_date'])) {
                $data['data']['pickup']['type'] = 1;
            } else {
                $data['data']['pickup']['type'] = 0;
                $nowData = $this->_checkNowTime($data['data']['Order']['type']);
                $explodedData = explode(":", $nowData['pickup_time']);
                if (strpos($nowData['pickup_time'], 'pm') !== false || strpos($nowData['pickup_time'], 'am') !== false) {
                    $explodedData[0] = date("H", strtotime($nowData['pickup_time']));
                    $explodedData[1] = date("i", strtotime($nowData['pickup_time']));
                }
                $data['data']['Store']['pickup_date'] = $nowData['pickup_date'];
                $data['data']['Store']['pickup_hour'] = $explodedData[0];
                $data['data']['Store']['pickup_minute'] = $explodedData[1];
            }
            if (!empty($userId)) {//For login user
                if (!empty($data['data'])) {
                    $oDetail['order_type'] = (!empty($data['data']['Order']['type'])) ? $data['data']['Order']['type'] : '';
                    $oDetail['preorder_type'] = (!empty($data['data']['pickup']['type'])) ? 1 : 0;
                    $oDetail['pickup_date'] = (!empty($data['data']['Store']['pickup_date'])) ? $data['data']['Store']['pickup_date'] : '';
                    $oDetail['pickup_hour'] = (!empty($data['data']['Store']['pickup_hour'])) ? $data['data']['Store']['pickup_hour'] : '';
                    $oDetail['pickup_minute'] = (!empty($data['data']['Store']['pickup_minute'])) ? $data['data']['Store']['pickup_minute'] : '';
                    $oDetail['delivery_address_id'] = (!empty($data['data']['DeliveryAddress']['id'])) ? $data['data']['DeliveryAddress']['id'] : '';
                    if (!empty($oDetail['order_type']) && $oDetail['order_type'] == 2) {
                        $oDetail['delivery_address_id'] = '';
                        $this->Session->delete('Zone');
                    }
                    if (!empty($oDetail['order_type']) && $oDetail['order_type'] == '3' && !empty($oDetail['delivery_address_id'])) {
                        $this->loadModel('DeliveryAddress');
                        $DelAddress = $this->DeliveryAddress->fetchAddress($oDetail['delivery_address_id']);
                        $this->Common->setZonefee($DelAddress);
                        $zoneData = $this->Session->read('Zone.id');
                        if (empty($zoneData)) {
                            $response['status'] = 'Error';
                            $response['msg'] = "Order cannot be delivered to this address, Please update address or choose another address.";
                            return json_encode($response);
                        }
                    }
                    $this->Session->write('ordersummary', $oDetail);
                    $this->render('/Elements/orderoverview/login_user_order_detail');
                }
            } else {//For guest user
                $addressId = $this->Session->read("ordersummary.delivery_address_id");
                if (!empty($addressId)) {
                    $data['data']['DeliveryAddress']['id'] = $addressId;
                }
                if (!empty($data['data']) && (!empty($data['data']['Order']['type']))) {
                    $oDetail['order_type'] = (!empty($data['data']['Order']['type'])) ? $data['data']['Order']['type'] : '';
                    $oDetail['preorder_type'] = (!empty($data['data']['pickup']['type'])) ? 1 : 0;
                    $oDetail['pickup_date'] = (!empty($data['data']['Store']['pickup_date'])) ? $data['data']['Store']['pickup_date'] : '';
                    $oDetail['pickup_hour'] = (!empty($data['data']['Store']['pickup_hour'])) ? $data['data']['Store']['pickup_hour'] : '';
                    $oDetail['pickup_minute'] = (!empty($data['data']['Store']['pickup_minute'])) ? $data['data']['Store']['pickup_minute'] : '';
                    $oDetail['delivery_address_id'] = (!empty($data['data']['DeliveryAddress']['id'])) ? $data['data']['DeliveryAddress']['id'] : '';
                    $this->Session->write('ordersummary', $oDetail);
                }
                if (!empty($data['data']['DeliveryAddress']) && ($data['data']['Order']['type'] == 3 || $data['data']['Order']['type'] == 2)) {

                    $zipCode = trim($data['data']['DeliveryAddress']['zipcode'], " ");
                    $stateName = trim($data['data']['DeliveryAddress']['state'], " ");
                    $cityName = strtolower($data['data']['DeliveryAddress']['city']);
                    $cityName = trim(ucwords($cityName));
                    $address = trim(ucwords($data['data']['DeliveryAddress']['address']));
                    $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                    $adjuster_address = str_replace(' ', '+', $dlocation);
                    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=' . GOOGLE_GEOMAP_API_KEY . '&address=' . $adjuster_address . '&sensor=false');
                    $output = json_decode($geocode);
                    if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                        
                    } else {
                        $latitude = @$output->results[0]->geometry->location->lat;
                        $longitude = @$output->results[0]->geometry->location->lng;
                        $data['data']['DeliveryAddress']['latitude'] = $latitude;
                        $data['data']['DeliveryAddress']['longitude'] = $longitude;
                    }
                    $this->request->data['DeliveryAddress'] = $data['data']['DeliveryAddress'];
                    $this->request->data['DeliveryAddress']['store_id'] = $this->Session->read('store_id');
                    $this->request->data['DeliveryAddress']['merchant_id'] = $this->Session->read('merchant_id');
                    $this->request->data['DeliveryAddress']['user_id'] = 0;
                    $this->request->data['DeliveryAddress']['email'] = $this->Session->read('GuestUser.email');
                    $this->request->data['DeliveryAddress']['name_on_bell'] = $this->request->data['DeliveryAddress']['name'];
                    $this->loadModel('DeliveryAddress');
                    if (empty($addressId)) {
                        $this->DeliveryAddress->create();
                    } else {
                        $this->request->data['DeliveryAddress']['id'] = $addressId;
                    }
                    if ($this->DeliveryAddress->saveAddress($this->request->data)) {
                        if (empty($addressId)) {
                            $delivery_address_id = $this->DeliveryAddress->getLastInsertID();
                            $this->Session->write("ordersummary.delivery_address_id", $delivery_address_id);
                        }
                        $this->loadModel('CountryCode');
                        $country_code = $this->CountryCode->fetchCountryCodeId($this->request->data['DeliveryAddress']['country_code_id']);
                        $this->Session->write("ordersummary.address", $this->request->data['DeliveryAddress']['address']);
                        $this->Session->write("ordersummary.city", $this->request->data['DeliveryAddress']['city']);
                        $this->Session->write("ordersummary.state", $this->request->data['DeliveryAddress']['state']);
                        $this->Session->write("ordersummary.zipcode", $this->request->data['DeliveryAddress']['zipcode']);
                        $this->Session->write('GuestUser.name', $this->request->data['DeliveryAddress']['name']);
                        $this->Session->write('GuestUser.country_code_id', $this->request->data['DeliveryAddress']['country_code_id']);
                        $this->Session->write('GuestUser.countryCode', $country_code['CountryCode']['code']);
                        $this->Session->write('GuestUser.userPhone', trim($this->request->data['DeliveryAddress']['phone']));
                    }
                    if (!empty($data['data']['Order']['type']) && $data['data']['Order']['type'] == '3') {
                        $this->loadModel('DeliveryAddress');
                        $delivery_address_id = $this->Session->read("ordersummary.delivery_address_id");
                        $DelAddress = $this->DeliveryAddress->fetchAddress($delivery_address_id);
                        $this->Common->setZonefee($DelAddress);
                        $zoneData = $this->Session->read('Zone.id');
                        if (empty($zoneData)) {
                            $response['status'] = 'Error';
                            $response['msg'] = "Order cannot be delivered to this address, Please update or change address.";
                            return json_encode($response);
                        }
                    }
                }
                $this->render('/Elements/orderoverview/guest_order_detail');
            }
        }
    }

    public function editGuestOrderDetail() {
        $this->layout = false;
        if ($this->request->is(array('ajax', 'post', 'put'))) {
            $this->loadModel('CountryCode');
            $countryCode = $this->CountryCode->fetchAllCountryCode();
            $this->set(compact('countryCode'));
            $this->render('/Elements/orderoverview/edit_guest_order_detail');
        }
    }

    public function editLoginUserOrderDetail() {
        $this->layout = false;
        if ($this->request->is(array('ajax', 'post', 'put'))) {
            $this->loadModel('DeliveryAddress');
            $userId = AuthComponent::User('id'); // Customer Id
            $roleId = AuthComponent::User('role_id');
            $storeId = $this->Session->read('store_id');
            $merchantId = $this->Session->read('merchant_id');
            $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
            $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $storeId, $merchantId); // It will call the function in the model to check the address either exist or not
            $this->set(compact('checkaddress', 'countryCode'));
            $this->render('/Elements/orderoverview/edit_login_user_order_detail');
        }
    }

    public function chkMinPmtAmt() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'post'))) {
            $response = array();
            $responses = $this->checkMendatoryItem('private');
            $t = json_decode($responses, true);
            if ($t["status"] == 'Error') {
                return $responses;
            }
            //pr($_SESSION);
            if (empty($_SESSION['ordersummary']['order_type'])) {
                $response['status'] = 'Error';
                $response['msg'] = "Please select order type.";
                return json_encode($response);
            }
            if ($_SESSION['ordersummary']['order_type'] == 3 && empty($_SESSION['ordersummary']['delivery_address_id'])) {
                $response['status'] = 'Error';
                $response['msg'] = "Please select delivery address.";
                return json_encode($response);
            }
            if ($_SESSION['ordersummary']['order_type'] == 2) {
                if (empty($_SESSION['ordersummary']['pickup_date']) || empty($_SESSION['ordersummary']['pickup_hour'])) {
                    $response['status'] = 'Error';
                    $response['msg'] = "Please fill order details.";
                    return json_encode($response);
                }
            }
            if ($_SESSION['ordersummary']['order_type'] == '3') {
                if (!empty($_SESSION['cart'])) {
                    $in = false;
                    $response['msg'] = 'The items below are non-deliverable:<br><br>';
                    foreach ($_SESSION['cart'] as $cItem) {
                        if ($cItem['Item']['is_deliverable'] == 0) {
                            $in = true;
                            $response['status'] = 'Error';
                            $response['msg'] .= $cItem['Item']['name'] . "<br>";
                        }
                    }
                    if ($in) {
                        $response['msg'] .= "<br>Please either delete or change your order type.";
                        return json_encode($response);
                    }
                }
                $this->loadModel('DeliveryAddress');
                $DelAddress = $this->DeliveryAddress->fetchAddress($_SESSION['ordersummary']['delivery_address_id']);
                $this->Session->delete('Zone');
                $this->Common->setZonefee($DelAddress);
                $zoneData = $this->Session->read('Zone.id');
                if (empty($zoneData)) {
                    $response['status'] = 'Error';
                    $response['msg'] = "Order cannot be delivered to this address, Please update address.";
                    return json_encode($response);
                }
            }
            $responsCA = $this->checkAmount();
            $responsCAD = json_decode($responsCA, true);
            if ($responsCAD["status"] == 'Error') {
                return $responsCA;
            } else {
                $response['status'] = 'Success';
                $response['msg'] = 'Success';
                return json_encode($response);
            }
        }
    }

    public function checkDeliverType() {
        $this->layout = false;
        if ($this->request->is(array('ajax', 'post'))) {
            $finalItem = $this->Session->read('cart');
            if (!empty($finalItem)) {
                $this->loadModel('Store');
                $store_result = $this->Store->fetchStoreDetail($this->Session->read('store_id'));
                if (isset($store_result['Store']['service_fee'])) {
                    $this->Session->write('service_fee', $store_result['Store']['service_fee']);
                }
//                if (isset($store_result['Store']['delivery_fee'])) {
//                    $this->Session->write('delivery_fee', $store_result['Store']['delivery_fee']);
//                }
                if (isset($store_result['Store']['tip'])) {
                    $this->Session->write('tip', $store_result['Store']['tip']);
                }
            }
            $this->set(compact('finalItem'));
            $this->render('/Elements/orderoverview/check_delivery_type');
        }
    }

    public function isItemDeliverable() {
        $this->layout = false;
        $this->autoRender = false;
        if (!empty($_SESSION['cart']) && !empty($_SESSION['Order']['order_type']) && $_SESSION['Order']['order_type'] == 3) {
            $in = false;
            $response['msg'] = 'The items below are non-deliverable:<br><br>';
            foreach ($_SESSION['cart'] as $cItem) {
                if ($cItem['Item']['is_deliverable'] == 0) {
                    $in = true;
                    $response['msg'] .= $cItem['Item']['name'] . "<br>";
                }
            }
            if ($in) {
                $response['status'] = 'Error';
                $response['msg'] .= "<br>Please either delete or change your order type.";
                return json_encode($response);
            }
        }
        if (DESIGN == 4) {//old layout
            $responsCA = $this->checkAmount();
            $responsCAD = json_decode($responsCA, true);
            if ($responsCAD["status"] == 'Error') {
                return $responsCA;
            }
        }
        if ($this->request->is(array('post')) && !empty($this->request->data)) {
            if (!empty($this->request->data['item_id'])) {
                $this->loadModel('Item');
                $iData = $this->Item->findById($this->request->data['item_id'], array('name', 'is_deliverable'));
                if (!empty($iData) && $iData['Item']['is_deliverable'] == 0) {
                    $msg = $iData['Item']['name'] . " is non-deliverable.<br>Please change your order type or select other item.";
                    $response['status'] = 'Error';
                    $response['msg'] = $msg;
                    return json_encode($response);
                }
            }
        }
    }

    public function checkMendatoryItem($param = null) {
        if (empty($param)) {
            $this->layout = "ajax";
            $this->autoRender = false;
        }
        $responsOAC = $this->orderAllowedCheck();
        $responsOACD = json_decode($responsOAC, true);
        if ($responsOACD["status"] == 'Error') {
            $this->autoRender = false;
            return $responsOAC;
        }
        $cartData = $this->Session->read('cart');
        //pr($cartData);
        $response['status'] = 'Success';
        if (!empty($cartData)) {
            $userId = AuthComponent::User('id');
            $guestUser = false;
            $newDesignGuestUser = $this->Session->read('GuestUser');
            $oldDesignGuestUser = $this->Session->read('Order.delivery_address_id');
            if ($newDesignGuestUser || $oldDesignGuestUser) {
                $guestUser = true;
            }
            if (empty($userId) && empty($guestUser)) {
                $response['status'] = 'Error';
                $response['msg'] = 'Please login.';
                return json_encode($response);
            }
            $i = 0;
            $storeId = $this->Session->read('store_id');
            $this->loadModel('Category');
            $categoryData = $this->Category->find('all', array('conditions' => array('Category.store_id' => $storeId, 'Category.is_deleted' => 0, 'Category.is_active' => 1, 'Category.is_mandatory' => 1, 'Category.max_value >' => 0), 'order' => array('Category.position' => 'asc')));
            $nowDate = date("Y-m-d H:i:s");
            $currentServerDateTime = $this->Common->storeTimeZoneUser('', $nowDate);
            $currentTime = date("H:i:s", (strtotime($currentServerDateTime)));
            $current_date = date("Y-m-d", (strtotime($currentServerDateTime)));
            foreach ($categoryData as $ckey => $cvalue) {
                if (!empty($cvalue['Category']['is_meal'])) {
                    $starTime = strtotime($cvalue['Category']['start_time']);
                    $endTime = strtotime($cvalue['Category']['end_time']);
                    $currentTime = strtotime($currentTime);
                    if (!($currentTime >= $starTime && $currentTime <= $endTime)) {
                        unset($categoryData[$ckey]);
                        continue;
                    }
                }
                if (!empty($cvalue['Category']['days']) && !empty($cvalue['Category']['is_meal'])) {
                    $days = explode(',', $cvalue['Category']['days']);
                    $day_number = date('N', strtotime($current_date));
                    if (!in_array($day_number, $days)) {
                        unset($categoryData[$ckey]);
                    }
                }
            }
            if (!empty($categoryData)) {
                foreach ($categoryData as $cData) {
                    $categoryList[] = $cData['Category']['id'];
                    $itemCount = 0;
                    foreach ($cartData as $key => $ctData) {
                        if ($cData['Category']['id'] == $ctData['Item']['categoryid']) {
                            $itemCount += $ctData['Item']['quantity'];
                            //$itemCount++;
                        }
                    }
                    $minItem = $maxItem = ' item';
                    if ($cData['Category']['min_value'] > 1) {
                        $minItem = ' items';
                    }
                    if ($cData['Category']['max_value'] > 1) {
                        $maxItem = ' items';
                    }
                    if ($itemCount == 0) {
                        if ($cData['Category']['min_value'] >= 1) {
                            $response['status'] = 'Error';
                            $response['msg'] = 'Please select minimum of ' . $cData['Category']['min_value'] . ' to maximum of ' . $cData['Category']['max_value'] . $maxItem . ' from ' . $cData['Category']['name'] . ' category.';
                            return json_encode($response);
                        }
                    }
                    if (!empty($itemCount) && ($cData['Category']['min_value'] > 0 || $cData['Category']['max_value'] > 0)) {
                        if ($cData['Category']['min_value'] > $itemCount) {
                            $response['status'] = 'Error';
                            $response['msg'] = 'Please select minimum ' . $cData['Category']['min_value'] . $minItem . ' from ' . $cData['Category']['name'] . ' category.';
                            return json_encode($response);
                        }
                        if ($cData['Category']['max_value'] < $itemCount) {
                            $response['status'] = 'Error';
                            $response['msg'] = 'Please select maximum ' . $cData['Category']['max_value'] . $maxItem . ' from ' . $cData['Category']['name'] . ' category.';
                            return json_encode($response);
                        }
                    }
                }
            }
            $in = false;
            $response = array();
            $response['msg'] = '';
            if (!empty($categoryList)) {
                $cartItemIds = $cartDataNew = $matchedCartItemIds = array();
                foreach ($cartData as $key => $data) {
                    if (!isset($cartDataNew[$data['Item']['id']])) {
                        $cartDataNew[$data['Item']['id']]['Item']['id'] = $data['Item']['id'];
                        $cartDataNew[$data['Item']['id']]['Item']['quantity'] = $data['Item']['quantity'];
                    } else {
                        $cartDataNew[$data['Item']['id']]['Item']['id'] = $data['Item']['id'];
                        $cartDataNew[$data['Item']['id']]['Item']['quantity'] += $data['Item']['quantity'];
                    }
                }

                if (!empty($cartDataNew)) {
                    //Category Mandatory Items list
                    $mandatoryItemList = $this->Item->find('list', array('fields' => array('Item.id'), 'conditions' => array('Item.mandatory_item_units >' => 1, 'Item.store_id' => $storeId, 'Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.category_id' => $categoryList)));
                    //Cart Items
                    foreach ($cartDataNew as $nData) {
                        $this->Item->bindModel(
                                array(
                            'belongsTo' => array(
                                'Category' => array(
                                    'className' => 'Category',
                                    'foreignKey' => 'category_id',
                                    'type' => 'INNER',
                                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1, 'Category.is_mandatory' => 1),
                                    'fields' => array('id', 'name', 'is_mandatory')
                                )
                            )
                                ), false
                        );
                        $itemData = $this->Item->findById($nData['Item']['id'], array('mandatory_item_units', 'name', 'Category.is_mandatory'));
                        if (!empty($itemData) && ($nData['Item']['quantity'] < $itemData['Item']['mandatory_item_units'])) {
                            $in = true;
                            if ($itemData['Item']['mandatory_item_units'] > 1) {
                                $cMsg = $itemData['Item']['mandatory_item_units'] . ' Units of ' . $itemData['Item']['name'] . ' to check out.';
                            } else {
                                $cMsg = $itemData['Item']['name'] . ' to check out.';
                            }
                            $response['msg'] .= 'Please select ' . $cMsg;
                        }

                        if (in_array($nData['Item']['id'], $mandatoryItemList)) {
                            unset($mandatoryItemList[$nData['Item']['id']]);
                        }
                    }
                    if (!empty($mandatoryItemList)) {
                        $mItemData = $this->Item->find('all', array('fields' => array('mandatory_item_units', 'name', 'Category.is_mandatory', 'Category.name'), 'conditions' => array('Item.id' => $mandatoryItemList)));
                        if (!empty($mItemData)) {
                            $in = true;
                            $response['msg'] = 'Please select mandatory item :';
                            foreach ($mItemData as $mItem) {
                                $response['msg'].='<br>' . $mItem['Item']['mandatory_item_units'] . ' units of ' . $mItem['Item']['name'];
                            }
                        }
                    }
                }
            }
            if ($in) {
                $response['status'] = 'Error';
            } else {
                $responsCA = $this->checkAmount();
                $responsCAD = json_decode($responsCA, true);
                if ($responsCAD["status"] == 'Error') {
                    return $responsCA;
                } else {
                    $response['status'] = 'Success';
                    $response['msg'] = 'Success';
                }
            }
            return json_encode($response);
        } else {
            $response['status'] = 'Error';
            $response['msg'] = 'Your cart is empty.';
            return json_encode($response);
        }
    }

    public function checkAddressInZone() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is('post')) {
            if (!empty($this->request->data['deliveryAddressId'])) {
                $this->loadModel('DeliveryAddress');
                $DelAddress = $this->DeliveryAddress->fetchAddress($this->request->data['deliveryAddressId']);
                $this->Common->setZonefee($DelAddress);
                $zoneData = $this->Session->read('Zone.id');
                if (empty($zoneData)) {
                    unset($_SESSION['Zone']);
                    $this->Session->delete('Zone');
                    $this->Session->delete("ordersummary.delivery_address_id");
                    $response['status'] = 'Error';
                    $response['msg'] = "Order cannot be delivered to this address, Please update address or choose another address.";
                    return json_encode($response);
                }
            }
        }
    }

    public function checkAmount() {
        $service_fee = 0;
        if (empty($_SESSION['cart'])) {
            $response['status'] = 'Error';
            $response['msg'] = "Cart is empty. Please add item.";
            return json_encode($response);
        } else {
            $total_price = 0;
            $finalItem = $this->Session->read('cart');
            foreach ($finalItem as $total) {
                $total_price = $total_price + $total['Item']['final_price'];
            }
            //$service_fee = $this->Session->read('service_fee');
            $service_fee = $this->Session->read('final_service_fee');
            $total_price = $total_price + $service_fee;
            // It will give the final totoal with all taxes
        }
        $order_type = $totaltaxPrice = $minimum_price = $total = 0;
        if (isset($_SESSION['Order']['order_type'])) {
            $order_type = $_SESSION['Order']['order_type'];
        }
        if (isset($_SESSION['ordersummary']['order_type'])) {
            $order_type = $_SESSION['ordersummary']['order_type'];
        }
        if ($order_type == 3) {
            if ($this->Session->check('Zone.fee')) {
                $total_price = $total_price + $this->Session->read('Zone.fee');
            }
        }
        if (isset($_SESSION['taxPrice'])) {
            $total_price = $total_price + $_SESSION['taxPrice'];
        }
        if (isset($_SESSION['Cart']['tip'])) {
            $total_price = $total_price + $_SESSION['Cart']['tip'];
        }
        $this->Session->write('Cart.grand_total_final', $this->Common->amount_format($total_price, true));
        $this->loadModel('Store');
        $storeInfo = $this->Store->fetchStoreDetail($this->Session->read('store_id'));
        $gross_amount = $_SESSION['Cart']['grand_total_final'];
        $totalwithoutTax = $gross_amount;
        $response = array();
        $response['status'] = 'Success';
        $response['msg'] = 'Success';
        if (isset($_SESSION['taxPrice'])) {
            $totaltaxPrice = $_SESSION['taxPrice'];
        }
        if ($order_type == 2) {
            if ($storeInfo['Store']['is_pick_beftax']) {
                $totalwithoutTax = $gross_amount - $totaltaxPrice;
            }
            $minimum_price = $storeInfo['Store']['minimum_takeaway_price'];
        } else {
            if ($storeInfo['Store']['is_delivery_beftax']) {
                $totalwithoutTax = $gross_amount - $totaltaxPrice;
            }
            $minimum_price = $storeInfo['Store']['minimum_order_price'];
        }
        $total = $totalwithoutTax - $service_fee;
        if (!empty($gross_amount) && !empty($_SESSION['cart'])) {

            if ($order_type == 3) {
                if ($this->Session->check('Zone.fee')) {
                    $total = $total - $this->Session->read('Zone.fee');
                }
            }
            if ($total < 0) {
                $response['status'] = 'Error';
                $response['msg'] = "Order amount is not sufficient";
            }
        }
	if (isset($_SESSION['Coupon'])) {
            $Couponcode = $_SESSION['Coupon']['Coupon']['coupon_code'];
            $discount_amount = 0;
            if (isset($_SESSION['Coupon'])) {
                if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) { // Price
                    $discount_amount = $_SESSION['Coupon']['Coupon']['discount'];
                } else { // Percentage
                    $discount_amount = $total * $_SESSION['Coupon']['Coupon']['discount'] / 100;
                }
            }
            if ($total < $discount_amount) {
                $discount_amount = $total;
            }
            $total = $total - $discount_amount;
        }
        if ($minimum_price > 0) {
            if (($total) >= ($minimum_price)) {
                $response['status'] = 'Success';
                $response['msg'] = 'Success';
            } else {
                $minimum_price = number_format($minimum_price, 2);
                $message = "Order total should be equal or more than $" . $minimum_price . "(minimum order price).";
                $response['status'] = 'Error';
                $response['msg'] = $message;
            }
        } else {
            if ($total < 0) {
                $message = "Order total should be equal or more than $0.00";
                $response['status'] = 'Error';
                $response['msg'] = $message;
            } else {
                $response['status'] = 'Success';
                $response['msg'] = 'Success';
            }
        }
        if (empty($_SESSION['cart'])) {
            $response['status'] = 'Error';
            $response['msg'] = "Cart is empty. Please add item.";
        }
        return json_encode($response);
    }

    public function saveOrderTime() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $data = $this->request->data;
        $oDetail = array();
        if (!empty($data['ordertype'])) {
            //$oDetail['order_type'] = $data['ordertype'];
            $this->Session->write('ordersummary.order_type', $data['ordertype']);
        }
        if (!empty($data['preOrder'])) {
            //$oDetail['preorder_type'] = $data['preOrder'];
            $this->Session->write('ordersummary.preorder_type', $data['preOrder']);
        }
        if (!empty($data['orderdate'])) {
            //$oDetail['pickup_date'] = $data['orderdate'];
            $this->Session->write('ordersummary.pickup_date', $data['orderdate']);
        }
        if (!empty($data['orderhour'])) {
            //$oDetail['pickup_hour'] = $data['orderhour'];
            $this->Session->write('ordersummary.pickup_hour', $data['orderhour']);
        }
        if (!empty($data['ordermin'])) {
            //$oDetail['pickup_minute'] = $data['ordermin'];
            $this->Session->write('ordersummary.pickup_minute', $data['ordermin']);
        }
        //$this->Session->write('ordersummary', $oDetail);
        if (!empty($data['ordertype']) && $data['ordertype'] == 2) {
            $this->Session->delete('Zone'); //user goes on back page from orderoverview page
            $this->Session->delete('ordersummary.delivery_address_id'); //user goes on back page from orderoverview page
        }
        if (!empty($_SESSION['cart']) && !empty($_SESSION['ordersummary']['order_type']) && $_SESSION['ordersummary']['order_type'] == 3) {
            $in = false;
            $response['msg'] = 'The items below are non-deliverable:<br><br>';
            foreach ($_SESSION['cart'] as $cItem) {
                if ($cItem['Item']['is_deliverable'] == 0) {
                    $in = true;
                    $response['msg'] .= $cItem['Item']['name'] . "<br>";
                }
            }
            if ($in) {
                $response['status'] = 'Error';
                $response['msg'] .= "<br>Please either delete or change your order type.";
                return json_encode($response);
            }
        }
        $response['status'] = 'Success';
        $response['msg'] = 'Success';
        return json_encode($response);
    }

}
