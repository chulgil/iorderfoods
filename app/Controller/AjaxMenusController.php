<?php

App::uses('StoreAppController', 'Controller');

class AjaxMenusController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common', 'NZGateway');
    public $helper = array('Encryption', 'Common', 'Session');
    public $uses = array('User', 'Store');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('user_login', 'user_guest', 'setDefaultStoreTime', 'delivery', 'guestdelivery', 'getDeliveryAddress', 'deleteaddress', 'addAddress', 'checklogin','checkAddressInZone');
        $roleId = AuthComponent::User('role_id');
        if (!empty($roleId) && $roleId != 4) {
            $this->InvalidLogin($roleId);
        }
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
        $closedDay = array();
        $storeavaibilityInfo = $this->StoreAvailability->getclosedDay($storeId);
        $daysarray = array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
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
        $this->set('closedDay', $closedDay);
        $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
        $encrypted_merchantId = $this->Encryption->encode($this->Session->read('merchant_id'));
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
    }

    public function user_guest() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        if ($this->request->data) {
            $this->loadModel('DeliveryAddress');
            $data['DeliveryAddress']['user_id'] = 0;
            $data['DeliveryAddress']['store_id'] = $this->Session->read('store_id');
            $data['DeliveryAddress']['merchant_id'] = $this->Session->read('merchant_id');
            $data['DeliveryAddress']['name_on_bell'] = $this->request->data['name'];
            $data['DeliveryAddress']['phone'] = $this->request->data['phone'];
            $data['DeliveryAddress']['email'] = $this->request->data['email'];
            $data['DeliveryAddress']['country_code_id'] = $this->request->data['country_code_id'];
            if ($this->DeliveryAddress->saveAddress($data)) {
                $address_id = $this->DeliveryAddress->getLastInsertId();
                $this->Session->write('Order.delivery_address_id', $address_id);
                $this->Session->write('Order.pickup_store_id', $this->Session->read('store_id'));
                $response['status'] = 1;
                $response['msg'] = 'User login as guest';
            } else {
                $response['status'] = 1;
                $response['msg'] = 'User not login as guest';
            }
        } else {
            $response['status'] = 0;
            $response['msg'] = 'Invalid request';
        }
        return json_encode($response);
    }

    public function setDefaultStoreTime() {
        $this->layout = null;
        $ReqorderType = 2;
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $storeId = $this->Session->read('store_id');
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent', 'StoreAvailability')));
        $storeData = $this->Store->findById($storeId, array('Store.is_delivery', 'Store.is_take_away'));
        if (!empty($storeData) && !empty($storeData['Store']['is_take_away'])) {
            $ReqorderType = 2;
        } else {
            $ReqorderType = 3;
        }
        if (isset($this->request->data['ordertype'])) {
            $ReqorderType = $this->request->data['ordertype'];
        }
	$this->Session->write('Order.order_type',$ReqorderType);
	$nowAvail = $this->blackOutDays();
        //get current pick-up time
        $finaldata = array();
        $today = 1;
        $orderType = 2;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        $pickcurrent_date = $finaldata['currentdate'];
        $explodeVal = explode("-", $pickcurrent_date);
        $pickcurrentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $pickupadvanceDay = $finaldata['store_data']['Store']['pickcalendar_limit'] - 1 + $finaldata['store_data']['Store']['pickblackout_limit'];

        $datetoConvert = explode('-', $pickcurrentDateVar);
        $datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
        $pickupmaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $pickupadvanceDay . ' day'));
        $pickcurrentDateVar = date('m-d-Y', strtotime($datetoConvert . ' +' . $finaldata['store_data']['Store']['pickblackout_limit'] . ' day'));

        //get current delivery time
        $finaldata = array();
        $today = 1;
        $orderType = 3;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        $delcurrent_date = $finaldata['currentdate'];
        $explodeVal = explode("-", $delcurrent_date);
        $delcurrentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $deliveryadvanceDay = $finaldata['store_data']['Store']['deliverycalendar_limit'] - 1 + $finaldata['store_data']['Store']['deliveryblackout_limit'];
        $datetoConvert = explode('-', $delcurrentDateVar);
        $datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
        $deliverymaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $deliveryadvanceDay . ' day'));
        $delcurrentDateVar = date('m-d-Y', strtotime($datetoConvert . ' +' . $finaldata['store_data']['Store']['deliveryblackout_limit'] . ' day'));

        $finaldata['delcurrentDateVar'] = $delcurrentDateVar;
        $finaldata['deliverymaxdate'] = $deliverymaxdate;
        $finaldata['pickcurrentDateVar'] = $pickcurrentDateVar;
        $finaldata['pickupmaxdate'] = $pickupmaxdate;
        $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
        $encrypted_merchantId = $this->Encryption->encode($this->Session->read('merchant_id'));
        $this->set(compact('finaldata', 'encrypted_storeId', 'encrypted_merchantId', 'ReqorderType','nowAvail'));
    }

    public function delivery() {
        $this->layout = null;
        if ($this->Session->check('Auth.User.Order')) {
            $this->Session->delete('Auth.User.Order');
        }
        if ($this->request->is('post') && $this->request->data) {
            $order_type = $this->request->data['orderType']['type'];
            $this->Session->write('Order.order_type', $order_type);
            $this->Session->write('Cart.segment_type', $order_type);
            $preOrderallowed = $this->Store->checkPreorder($this->Session->read('store_id'), $this->Session->read('merchant_id'));
            if (empty($preOrderallowed)) {
                $nowData = $this->_checkNowTime($order_type);
                $pickupTime = $nowData['pickup_time'];
                $pickupDate = $nowData['pickup_date'];
                $this->Session->write('Order.is_preorder', 0);
            } else {
                $this->request->data['Store']['pickup_time'] = $this->request->data['Store']['pickup_hour'] . ':' . $this->request->data['Store']['pickup_minute'] . ':00';
                $pickupTime = $this->Common->storeTimeFormateUser($this->request->data['Store']['pickup_time']);
                if ($order_type == 2) {
                    $pickupDate = $this->request->data['orderType']['pick_up_date'];
                } else {
                    $pickupDate = $this->request->data['orderType']['delivery_date'];
                }
                $this->Session->write('Order.is_preorder', 1);
            }
            $this->Session->write('Order.store_pickup_time', $pickupTime);
            $this->Session->write('Order.store_pickup_date', $pickupDate);
        }
        $delivery_address_id = $this->Session->read('Order.delivery_address_id');
        if (!empty($delivery_address_id)) {
            $this->loadModel('DeliveryAddress');
            $DelAddress = $this->DeliveryAddress->fetchAddress($delivery_address_id);
            $this->request->data = $DelAddress;
        }
        $this->render('/Elements/orderLogin/delivery');
    }

    public function deliveryaddress($orderId = null) {
        $this->layout = null;
        $this->autoRender = false;
        //$this->Session->write('Order.delivery_address_id', $this->data['DeliveryAddress']['id']);
        $this->loadModel('DeliveryAddress');
        $DelAddress = $this->DeliveryAddress->fetchAddress($this->data['DeliveryAddress']['id']);
        $this->Common->setZonefee($DelAddress);
        $zoneData = $this->Session->read('Zone.id');
        //$zoneData = $this->Common->addressInZone($DelAddress);
        if (empty($zoneData)) {
            $response['msg'] = "Orders cannot be delivered to this address. Please choose another address.";
            $response['status'] = "Error";
        } else {
            $this->Session->write('Order.delivery_address_id', $this->data['DeliveryAddress']['id']);
            $response['status'] = "Success";
        }
        return json_encode($response);
    }

    public function guestdelivery() {
        $this->layout = null;
        $this->autoRender = false;
        $this->loadModel('DeliveryAddress');
        $deliveryaddressID = '';
        if ($this->Session->check('Order.delivery_address_id')) {
            $deliveryaddressID = $this->Session->read('Order.delivery_address_id');
        }
        $data['DeliveryAddress']['id'] = $deliveryaddressID;
        $data['DeliveryAddress']['user_id'] = 0;
        $data['DeliveryAddress']['store_id'] = $this->Session->read('store_id');
        $data['DeliveryAddress']['merchant_id'] = $this->Session->read('merchant_id');
        $data['DeliveryAddress']['address'] = $this->request->data['DeliveryAddress']['address'];
        $data['DeliveryAddress']['city'] = $this->request->data['DeliveryAddress']['city'];
        $data['DeliveryAddress']['state'] = $this->request->data['DeliveryAddress']['state'];
        $data['DeliveryAddress']['zipcode'] = $this->request->data['DeliveryAddress']['zipcode'];
        if (isset($this->request->data['DeliveryAddress']['name'])) {
            $data['DeliveryAddress']['name_on_bell'] = $this->request->data['DeliveryAddress']['name'];
        }
        if (isset($this->request->data['DeliveryAddress']['email'])) {
            $data['DeliveryAddress']['email'] = $this->request->data['DeliveryAddress']['email'];
        }
        if (isset($this->request->data['DeliveryAddress']['phone'])) {
            $data['DeliveryAddress']['phone'] = $this->request->data['DeliveryAddress']['phone'];
        }


        $dlocation = $data['DeliveryAddress']['address'] . " " . $data['DeliveryAddress']['city'] . " " . $data['DeliveryAddress']['state'] . " " . $data['DeliveryAddress']['zipcode'];
        $adjuster_address2 = str_replace(' ', '+', $dlocation);
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
        $output = json_decode($geocode);
        if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
            
        } else {
            $latitude = @$output->results[0]->geometry->location->lat;
            $longitude = @$output->results[0]->geometry->location->lng;
            $data['DeliveryAddress']['latitude'] = $latitude;
            $data['DeliveryAddress']['longitude'] = $longitude;
        }
        $this->Common->setZonefee($data);
        $zoneData = $this->Session->read('Zone.id');
        if (empty($zoneData)) {
            $response['status'] = 'Error';
            $response['msg'] = "Orders cannot be delivered to this address. Please choose another address.";
        } else {
            $this->loadModel('DeliveryAddress');
            if ($this->DeliveryAddress->saveAddress($data)) {
                if (empty($data['DeliveryAddress']['id'])) {
                    $lastID = $this->DeliveryAddress->getLastInsertId();
                    $this->Session->write('Order.delivery_address_id', $lastID);
                }
                $response['status'] = 'Success';
            } else {
                $response['status'] = 'Error';
                $response['msg'] = "Somthing went wrong please try after some time.";
            }
        }
        return json_encode($response);
    }

    public function updateAddress($encrypt_deliveryAddressId = null) {
        $this->layout = null;
        $this->autoRender = false;
        $decrypt_storeId = $this->Session->read('store_id');
        $decrypt_merchantId = $this->Session->read('merchant_id');
        if (isset($this->request->data['address'])) {
            $encrypt_deliveryAddressId = $this->request->data['address'];
        }
        $decrypt_deliveryAddressId = $this->Encryption->decode($encrypt_deliveryAddressId);
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('countryCode'));
        $this->loadModel('DeliveryAddress');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        $resultAddress = $this->DeliveryAddress->fetchAddress($decrypt_deliveryAddressId);
        if (isset($this->request->data['DeliveryAddress'])) {
            $zipCode = trim($this->request->data['DeliveryAddress']['zipcode'], " ");
            $stateName = trim($this->data['DeliveryAddress']['state'], " ");
            $cityName = strtolower($this->request->data['DeliveryAddress']['city']);
            $cityName = trim(ucwords($cityName));
            $address = trim(ucwords($this->request->data['DeliveryAddress']['address']));
            $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
            $adjuster_address2 = str_replace(' ', '+', $dlocation);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
            $output = json_decode($geocode);
            $this->request->data['DeliveryAddress']['user_id'] = AuthComponent::User('id');
            $this->request->data['DeliveryAddress']['store_id'] = $decrypt_storeId;
            $this->request->data['DeliveryAddress']['merchant_id'] = $decrypt_merchantId;
            if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                
            } else {
                $latitude = @$output->results[0]->geometry->location->lat;
                $longitude = @$output->results[0]->geometry->location->lng;
                $this->request->data['DeliveryAddress']['latitude'] = $latitude;
                $this->request->data['DeliveryAddress']['longitude'] = $longitude;
            }
            if ($this->request->data['DeliveryAddress']['default'] == 1) {
                $this->DeliveryAddress->updateAll(array('DeliveryAddress.default' => 0), array('DeliveryAddress.user_id' => $this->request->data['DeliveryAddress']['user_id']));
            }
            $zoneData = $this->Common->addressInZone($this->request->data);
            if (empty($zoneData)) {
                $response['msg'] = "Orders cannot be delivered to this address. Please choose another address.";
                $response['status'] = "Error";
                return json_encode($response);
            } else {
                $this->DeliveryAddress->saveAddress($this->request->data);
            }
        }
        if ($resultAddress) {
            $this->request->data = $resultAddress;
        }
        $addressId = $resultAddress['DeliveryAddress']['id'];
        $this->set(compact('addressId', 'encrypted_storeId', 'encrypted_merchantId'));
        $this->render('/Elements/orderLogin/update_address');
    }

    public function getDeliveryAddress() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('DeliveryAddress');
        $encrypted_storeId = $_POST['storeId'];
        $encrypted_merchantId = $_POST['merchantId'];
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        if (empty($_POST['deliveryId'])) {
            $delivery = $this->DeliveryAddress->fetchfirstAddress(AuthComponent::User('id'));
            $deliveryID = $delivery['DeliveryAddress']['id'];
        } else {
            $deliveryID = $_POST['deliveryId'];
        }
        $resultAddress = $this->DeliveryAddress->fetchAddress($deliveryID);
        $this->set(compact('resultAddress', 'encrypted_storeId', 'encrypted_merchantId'));
        $this->render('/Elements/orderLogin/get_delivery_address');
    }

    public function deleteaddress() {
        $this->autoRender = false;
        $this->loadModel('DeliveryAddress');
        $data['DeliveryAddress']['id'] = $this->Encryption->decode($_POST['address']);
        $data['DeliveryAddress']['is_deleted'] = 1;
        $this->DeliveryAddress->saveAddress($data);
    }

    public function addAddress() {
        $this->layout = false;
        $this->autoRender = false;
        $decrypt_storeId = $this->Session->read('store_id');
        $decrypt_merchantId = $this->Session->read('merchant_id');
        $this->loadModel('DeliveryAddress');
        $userId = AuthComponent::User('id'); // Customer Id
        $roleId = AuthComponent::User('role_id');
        $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $decrypt_storeId, $decrypt_merchantId); // It will call the function in the model to check the address either exist or not
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
        $tmp = $this->request->data;
        $zoneError = '';
        if ($this->request->is('post') && !empty($tmp)) {
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
                $tmp['DeliveryAddress']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress']['merchant_id'] = $decrypt_merchantId;
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
                    $zoneError.= "Orders cannot be delivered to Home address.<br />";
                } else {
                    $response['label1'] = 'Error';
                    $this->DeliveryAddress->create();
                    $this->DeliveryAddress->saveAddress($data);
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
                $tmp['DeliveryAddress1']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress1']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress1']['merchant_id'] = $decrypt_merchantId;
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
                    $zoneError.= "Order cannot be delivered to Work address.<br />";
                } else {
                    $response['label2'] = 'Error';
                    $this->DeliveryAddress->create();
                    $this->DeliveryAddress->saveAddress($data1);
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
                $tmp['DeliveryAddress2']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress2']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress2']['merchant_id'] = $decrypt_merchantId;
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
                    $zoneError.= "Order cannot be delivered to Other address.<br />";
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
                $tmp['DeliveryAddress3']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress3']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress3']['merchant_id'] = $decrypt_merchantId;
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
                    $zoneError.= "Order cannot be delivered to Address 4.<br />";
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
                $tmp['DeliveryAddress4']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress4']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress4']['merchant_id'] = $decrypt_merchantId;
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
                    $zoneError.= "Order cannot be delivered to Address 5.<br />";
                } else {
                    $response['label5'] = 'Error';
                    $this->DeliveryAddress->create();
                    $result = $this->DeliveryAddress->saveAddress($data4);
                }
            }
            if (!empty($zoneError)) {
                $response['msg'] = $zoneError;
                return json_encode($response);
            }
        }
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('label1', 'label2', 'label3', 'label4', 'label5', 'countryCode', 'encrypted_storeId', 'encrypted_merchantId'));
        $this->render('/Elements/orderLogin/add_address');
    }

    public function checklogin() {
        $this->layout = false;
        $this->autoRender = false;
        if (AuthComponent::User('id')) {
            return json_encode(1);
        } elseif ($this->Session->check('Order.delivery_address_id')) {
            return json_encode(1);
        } else {
            return json_encode(0);
        }
    }

    public function checkAddressInZone() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is('ajax') && !empty($this->request->data['address']) && !empty($this->request->data['city']) && !empty($this->request->data['state']) && !empty($this->request->data['zipcode'])) {
            $data['DeliveryAddress']['address'] = $this->request->data['address'];
            $data['DeliveryAddress']['city'] = $this->request->data['city'];
            $data['DeliveryAddress']['state'] = $this->request->data['state'];
            $data['DeliveryAddress']['zipcode'] = $this->request->data['zipcode'];
            $dlocation = $data['DeliveryAddress']['address'] . " " . $data['DeliveryAddress']['city'] . " " . $data['DeliveryAddress']['state'] . " " . $data['DeliveryAddress']['zipcode'];
            $adjuster_address = str_replace(' ', '+', $dlocation);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address . '&sensor=false');
            $output = json_decode($geocode);
            if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                $response['msg'] = "Order cannot be delivered to this address.";
                $response['status'] = "Error";
            } else {
                $latitude = @$output->results[0]->geometry->location->lat;
                $longitude = @$output->results[0]->geometry->location->lng;
                $data['DeliveryAddress']['latitude'] = $latitude;
                $data['DeliveryAddress']['longitude'] = $longitude;
            }
            $zoneData = $this->Common->addressInZone($data);
            if (empty($zoneData)) {
                $response['msg'] = "Order cannot be delivered to this address.";
                $response['status'] = "Error";
            } else {
                $response['status'] = "Success";
            }
            return json_encode($response);
        }
    }

}
