<?php

App::uses('StoreAppController', 'Controller');

class PaymentsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common', 'AuthorizeNet', 'Paypal', 'NZGateway', 'Webservice');
    public $helper = array('Encryption', 'Common');
    public $uses = array('User', 'OrderItem');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('saveSpecialComment');
        //Check permission for Admin User
        $adminfunctions = array('paymentList', 'orderDetail');
        if (in_array($this->params['action'], $adminfunctions)) {
            if (!$this->Common->checkPermissionByaction($this->params['controller'])) {
                $this->Session->setFlash(__("Permission Denied"), 'flash_error');
                $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
            }
        }
        //Restrict admin user to access Frontend Methods.
        $roleId = $this->Session->read('Auth.User.role_id');
        if ($roleId) {
            if ($roleId != 4 && !in_array($this->params['action'], $adminfunctions)) {
                $this->InvalidLogin($roleId);
            }
        }
        $userId = AuthComponent::User() ? AuthComponent::User('id') : 0;
        $this->Session->write('Cart.user_id', $userId);
        $storeId = $this->Session->read('store_id');
        if (!empty($storeId)) {
            $timeZoneInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_zone_id'), 'recursive' => -1));
            $this->Session->write('admin_time_zone_id', $timeZoneInfo['Store']['time_zone_id']);
            $Store_Gmt_diff = "-8:00";
            $this->loadModel('TimeZone');
            $storeadmintimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $timeZoneInfo['Store']['time_zone_id']), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code', 'TimeZone.gmt'), 'recursive' => -1));
            if (!empty($storeadmintimezone)) {
                $storeGmt = explode(" ", $storeadmintimezone['TimeZone']['gmt']);
                $Store_Gmt_diff = $storeGmt[1];
            }
            $this->front_store_offset = $Store_Gmt_diff;
            Configure::write('front_store_offset', $this->front_store_offset);
        }
    }

    /* ------------------------------------------------
      Function name:payments()
      Description:Registration  Form for the  End customer
      created:22/7/2015
      ----------------------------------------------------- */

    private function savePayment($data) {
        $this->loadModel('Order');
        $this->loadModel('OrderPayment');
        $this->OrderPayment->create();
        $this->OrderPayment->savePayment($data);
        $orderPaymentID = $this->OrderPayment->getLastInsertId();
        $res = $this->Order->updateAll(
                array('payment_id' => $orderPaymentID), array('Order.id' => $this->Session->read('orderOverview.orderNo'))
        );
        $this->Session->write('Cart.payment_id', $orderPaymentID);
        return $res;
    }

    public function paymentSection($response = null) {
        $this->autoRender = false;
        $this->loadModel('Order');
        $this->loadModel('Store');
        $this->loadModel('OrderPayment');
        $this->loadModel('NzsafeUser');
        $this->_addressInZone();
        $ds = $this->Order->getdatasource();
        $ds->begin($this);
        if (!empty($response)) {
            $this->request->data['payment'] = $response['payment_type'];
        }
        $gTransactionId = '';
        $store_info = $this->Store->fetchStoreDetail($this->Session->read('store_id'));
        //$this->Session->write('Cart.comment', isset($this->request->data['Payment']['order_comments']) ? $this->request->data['Payment']['order_comments'] : "");
        if (DESIGN == 4 && !empty($this->request->data['User']['comment'])) {
            $this->Session->write('Cart.comment', isset($this->request->data['User']['comment']) ? $this->request->data['User']['comment'] : "");
        }
        $this->request->data['Store'] = $store_info['Store'];
        $paymemt_type = isset($this->request->data['payment']) ? $this->request->data['payment'] : 4;
        $success = $this->orderTranSave($paymemt_type);
        $pushOrderId = $this->Order->getLastInsertID();
        switch ($paymemt_type) {
            case 4 : // Order Save Only
                if ($success) {
                    $ds->commit($this);
                    //$this->redirect(array('controller' => 'Payments', 'action' => 'orderSaveForFuture'));
                    $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
                    $encrypted_merchantId = $this->Encryption->encode($this->Session->read('merchant_id'));
                    $this->Session->setFlash(__("Order has been successfully saved"), 'alert_success');
                    $this->redirect(array('controller' => 'orders', 'action' => 'mySavedOrders', $encrypted_storeId, $encrypted_merchantId));
                } else {
                    $this->redirect(array('controller' => 'Payments', 'action' => 'statuss'));
                }
                break;
            case 1 : // VaultUse or CreditCard
                try {
                    $use_paymemt_vault = isset($this->request->data['payment_vault']) ? $this->request->data['payment_vault'] : '';
                    $vault_id = $this->_getPayItem('VaultId');
                    $amount = $this->Session->read('Cart.grand_total_final');
                    if ($this->Session->check('Discount')) {
                        $amount = number_format($amount - $this->Session->read('Discount'), 2);
                    }
                    $card_num = $this->request->data['Payment']['cardnumber'];
                    $exp_date = $this->request->data['Payment']['expiryMonth'] . date('y', strtotime('01-01-' . $this->request->data['Payment']['expiryYear']));
                    $card_cvv = $this->request->data['Payment']['cvv'];

                    $this->NZGateway->setLogin($store_info['Store']['api_username'], $store_info['Store']['api_password']);
                    $this->NZGateway->setBilling(
                            $this->request->data['Payment']['firstname'], $this->request->data['Payment']['lastname'], $company = '', $this->request->data['Payment']['address'], $address2 = '', $this->request->data['Payment']['city'], $this->request->data['Payment']['state'], $this->request->data['Payment']['zipcode'], $country = '', $phone = '', $fax = '', AuthComponent::User('email'), $website = '');
                    $this->NZGateway->setOrder($orderid = '', $this->Session->read('Cart.comment'), $tax = '', $shipping = '', $ponumber = '', $ipaddress = '');

                    if ($this->Session->read('Cart.user_id') == 0 || $this->request->data['Payment']['update_vault'] == false) {
                        $this->request->data['OrderPayment']['payment_gateway'] = 'NZGateway';
                        $response = $this->NZGateway->doSale($amount, $card_num, $exp_date, $card_cvv);
                    } else {
                        if ($use_paymemt_vault) { // vault payment only
                            $this->request->data['OrderPayment']['payment_gateway'] = 'NZGateway Vault';
			    if ($this->Session->check('Cart.credit_mask')) {
                                $this->request->data['OrderPayment']['last_digit'] = $this->Session->read('Cart.credit_mask');
                            }
                            $response = $this->NZGateway->useVault($vault_id, $amount);
                        } else { // vault update and  payment
                            $response = $this->NZGateway->doSale($amount, $card_num, $exp_date, $card_cvv);
			    if ($this->Session->check('Cart.credit_mask')) {
                                $this->request->data['OrderPayment']['last_digit'] = $this->Session->read('Cart.credit_mask');
                            }	
                            if ($response['response_code'] == '100') {
                                if ($vault_id)
                                    $this->NZGateway->delVault($vault_id);
                                $initresponse = $this->NZGateway->initVault("", $card_num, $exp_date);
                                $this->request->data['OrderPayment']['payment_gateway'] = 'NZGateway';
                                if ($initresponse['response_code'] == 100) {
                                    $NzsafeUser = $this->_getPayItem('NzsafeUser');
                                    $NzsafeUser['customer_vault_id'] = $initresponse['customer_vault_id'];
                                    $this->request->data['OrderPayment']['last_digit'] = $NzsafeUser['NzsafeUser']['credit_mask']; //save card mask
                                    $this->NzsafeUser->saveUser($NzsafeUser);
                                } else {
                                    throw new Exception("NZ Gateway Valult Update Error : " . $response['responsetext'], 400);
                                }
                            } else {
                                $gTransactionId = isset($response['transactionid']) ? $response['transactionid'] : '';
                                throw new Exception("Init Vault Error : " . $response['responsetext'], 400);
                            }
                        }
                    }

                    if ($response['response_code'] == 100) {
                        $success = $this->savePayment($this->_getPayItem('OrderPayment', $response, "NZGateway", "PAID by credit card"));
                        if ($success) {
                            $ds->commit($this);
                            //$this->Webservice->orderPushNotification($pushOrderId);
                            //$this->notification($this->Session->read('orderOverview.orderNo'));
                            $this->redirect(array('controller' => 'Payments', 'action' => 'success'));
                        } else {
                            throw new Exception("Payment DB Save Error", 200);
                        }
                    } else {
                        $gTransactionId = isset($response['transactionid']) ? $response['transactionid'] : '';
                        throw new Exception("NZ Gateway Sale Error : " . $response['responsetext'], 400);
                    }
                } catch (Exception $e) {
                    $ds->rollback($this);
                    $response['transactionid'] = $gTransactionId;
                    switch ($e->getCode()) {
                        case 200 : break;
                        case 400 : $this->savePayment($this->_getPayItem('OrderPayment', $response, "NZGateway", "Failure"));
                    }
                    $this->notificationFail($e->getMessage());
                    $this->Session->setFlash(__($e->getMessage()), 'flash_error');
                    $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
                }
                break;
            case 2 : // Paypal
                try {
                    $response['responsetext'] = 'Payment has been approved';
                    $success = $this->savePayment($this->_getPayItem('PaypalPayment', $response, "PayPal", "Paid"));
                    if ($success) {
                        $ds->commit($this);
                        //$this->Webservice->orderPushNotification($pushOrderId);
                        //$this->notification($this->Session->read('orderOverview.orderNo'));
                        $this->redirect(array('controller' => 'Payments', 'action' => 'success'));
                    } else {
                        throw new Exception("Paypal Payment Saved Fail", 400);
                    }
                } catch (Exception $e) {
                    $ds->rollback($this);
                    $result['responsetext'] = $e->getCode() ? $e->getMessage() : 'Authentication Failed';
                    $this->savePayment($this->_getPayItem('PaypalPayment', $result, "PayPal", "Failure"));
                    $this->notificationFail($result['responsetext']);
                    $this->Session->setFlash(__($result['responsetext']), 'flash_error');
                    $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
                }
                break;
            case 3 : // Cash On Delivery
                $success = $this->savePayment($this->_getPayItem('OrderPayment', "", "COD", "Cash on Delivery"));
                if ($success) {
                    $ds->commit($this);
                    //$this->Webservice->orderPushNotification($pushOrderId);
                    //$this->notification($this->Session->read('orderOverview.orderNo'));
                    $this->redirect(array('controller' => 'Payments', 'action' => 'success'));
                } else {
                    $ds->rollback($this);
                    $this->redirect(array('controller' => 'Payments', 'action' => 'statuss'));
                }
                break;
        }
    }

    private function _getPayItem($type, $response = '', $gateway = '', $status = '') {

        $userId = $this->Session->read('Cart.user_id');
        $store_id = $this->Session->read('store_id');
        $merchant_id = $this->Session->read('merchant_id');
        $amount = $this->Session->read('Cart.grand_total_final');

        switch ($type) {
            case 'VaultId' : // NZ Gateway vault id
                $nzsafe_info = $this->NzsafeUser->getUser($userId);
		$this->Session->write('Cart.credit_mask', $nzsafe_info['NzsafeUser']['credit_mask']);
                $this->request->data['NzsafeUser']['id'] = $nzsafe_info['NzsafeUser']['id'];
                $this->request->data['NzsafeUser']['customer_vault_id'] = $nzsafe_info['NzsafeUser']['customer_vault_id'];
                return $nzsafe_info['NzsafeUser']['customer_vault_id'];

            case 'NzsafeUser' : // Credit Card
                $card_num = $this->request->data['Payment']['cardnumber'];
                $credit_type = $this->request->data['Payment']['creditype'];
                $credit_temp = substr(strrev($card_num), 0, 4);
                $credit_mask = strrev($credit_temp);
                $customer_email = AuthComponent::User('email');
                $this->request->data['NzsafeUser']['email'] = $customer_email;
                $this->request->data['NzsafeUser']['user_id'] = $userId;
                $this->request->data['NzsafeUser']['store_id'] = $store_id;
                $this->request->data['NzsafeUser']['merchant_id'] = $merchant_id;
                $this->request->data['NzsafeUser']['credit_type'] = $credit_type;
                $this->request->data['NzsafeUser']['credit_mask'] = $credit_mask;
                $this->request->data['NzsafeUser']['customer_vault_id'] = $this->_getPayItem('VaultId');
                return $this->request->data['NzsafeUser'];

            case 'OrderPayment' :
                $this->request->data['OrderPayment']['order_id'] = $this->Order->getLastInsertId();
                $this->request->data['OrderPayment']['user_id'] = $userId;
                $this->request->data['OrderPayment']['store_id'] = $store_id;
                $this->request->data['OrderPayment']['merchant_id'] = $merchant_id;
                $this->request->data['OrderPayment']['transection_id'] = isset($response['transactionid']) ? $response['transactionid'] : '';
                $this->request->data['OrderPayment']['amount'] = $amount;
                $this->request->data['OrderPayment']['response'] = isset($response['responsetext']) ? $response['responsetext'] : '';
                $this->request->data['OrderPayment']['response_code'] = isset($response['response_code']) ? $response['response_code'] : '';
                $this->request->data['OrderPayment']['payment_gateway'] = isset($this->request->data['OrderPayment']['payment_gateway']) ?
                        $this->request->data['OrderPayment']['payment_gateway'] : $gateway;
                if ($gateway == "COD") {
                    $order_type = $this->Session->check('Cart.segment_type') ? $this->Session->read('Cart.segment_type') : "";
                    if ($order_type == "2")
                        $status = "Cash on Pickup - UNPAID"; // Pickup
                    if ($order_type == "3")
                        $status = "Cash on Delivery - UNPAID"; // Pickup
                }
                $this->request->data['OrderPayment']['payment_status'] = $status;
                return $this->request->data['OrderPayment'];

            case 'PaypalPayment' :
                $this->request->data['OrderPayment']['order_id'] = $this->Order->getLastInsertId();
                $this->request->data['OrderPayment']['user_id'] = $userId;
                $this->request->data['OrderPayment']['store_id'] = $store_id;
                $this->request->data['OrderPayment']['merchant_id'] = $merchant_id;
                $this->request->data['OrderPayment']['transection_id'] = isset($response['TRANSACTIONID']) ? $response['TRANSACTIONID'] : 0;
                $this->request->data['OrderPayment']['amount'] = isset($response['AMT']) ? $response['AMT'] : $this->Session->read('Cart.grand_total_final');
                $this->request->data['OrderPayment']['payment_status'] = $status;
                $this->request->data['OrderPayment']['payment_gateway'] = $gateway;
                $this->request->data['OrderPayment']['response'] = $response['responsetext'];
                if ($status != 'Paid')
                    $this->request->data['OrderPayment']['response'] = 'Please enter proper details';
                else
                    $this->request->data['OrderPayment']['response'] = 'Payment has been approved';
                return $this->request->data['OrderPayment'];
        }
    }

    function reformatDate($date, $from_format = 'm-d-Y', $to_format = 'Y-m-d') {
        $date_aux = date_create_from_format($from_format, $date);
        return date_format($date_aux, $to_format);
    }

    private function _getSessionOrder($paymemt_type) {
        $aResult = [];
        $aResult['is_future_order'] = 0;
        $aResult['order_status_id'] = 1;
        // 0:NoneMember(비회원)
        $aResult['user_id'] = $this->Session->read('Cart.user_id');
        // Read the type of Delivery

        $aResult['order_comments'] = $this->Session->check('Cart.comment') ? $this->Session->read('Cart.comment') : "";
        // Read the PreOrder Type
        $aResult['merchant_id'] = $this->Session->check('merchant_id') ? $this->Session->read('merchant_id') : "";
        $aResult['store_id'] = $this->Session->check('store_id') ? $this->Session->read('store_id') : "";

        $aResult['tax_price'] = $this->Session->check('taxPrice') ? $this->Session->read('taxPrice') : "";

        $aResult['service_amount'] = ($this->Session->check('final_service_fee') ? $this->Session->read('final_service_fee') : 0);

        if (DESIGN == 4) {
            $aResult['seqment_id'] = $this->Session->check('Cart.segment_type') ? $this->Session->read('Cart.segment_type') : "";
            $aResult['is_pre_order'] = $this->Session->check('Order.is_preorder') ? $this->Session->read('Order.is_preorder') : "";
            $aResult['delivery_address_id'] = $this->Session->check('Order.delivery_address_id') ? $this->Session->read('Order.delivery_address_id') : "";
        } else {
            $aResult['delivery_address_id'] = $this->Session->check('ordersummary.delivery_address_id') ? $this->Session->read('ordersummary.delivery_address_id') : "";
            $aResult['is_pre_order'] = $this->Session->read('ordersummary.preorder_type');
            $aResult['seqment_id'] = $this->Session->check('ordersummary.order_type') ? $this->Session->read('ordersummary.order_type') : "";
        }


        if ($aResult['seqment_id'] == 3)
            $aResult['delivery_amount'] = $this->Session->check('delivery_fee') ? $this->Session->read('delivery_fee') : "";

        $aResult['amount'] = $this->Session->check('Cart.grand_total_final') ? $this->Session->read('Cart.grand_total_final') : "";


        $aResult['payment_id'] = $this->Session->check('Cart.payment_id') ? $this->Session->read('Cart.payment_id') : "";
        $aResult['coupon_code'] = $this->Session->check('Coupon.Coupon.coupon_code') ? $this->Session->read('Coupon.Coupon.coupon_code') : "";
        $aResult['coupon_discount'] = $this->Session->check('Discount') ? $this->Session->read('Discount') : "";

        // 즐겨찾기에 주문저장
        if ($paymemt_type == 4) {
            $aResult['payment_id'] = 0;
            $aResult['is_future_order'] = 1;
        }

        if ($this->Session->check('Cart.tip')) {
            $aResult['tip']           = $this->Session->read('Cart.tip');
            $aResult['tip_option']    = ($this->Session->check('Cart.tip_option') ? $this->Session->read('Cart.tip_option') : 0);
            $aResult['tip_percent']   = ($this->Session->check('Cart.tip_select') ? $this->Session->read('Cart.tip_select') : 0);
        }
        $aResult['order_number'] = "";
        if ($paymemt_type != 4) {
            $aResult['order_number'] = $this->Common->RandomString($aResult['store_id'], $this->front_store_offset, 'W');
        }
        if (DESIGN == 4) {
            $aResult['pickup_time'] = $this->checkOrderTimeOld();
        } else {
            $aResult['pickup_time'] = $this->checkOrderTimeNew();
        }

        if (empty($aResult['pickup_time'])) {
            return false;
        }

        return $aResult;
    }

    private function _getOrderItem($type, $item = [], $paymentType = 4) {
        $result = [];
        $temp = [];
        $userId = $this->Session->read('Cart.user_id');
        $store_id = $this->Session->read('store_id');
        $merchant_id = $this->Session->read('merchant_id');

        switch ($type) {
            case 'OrderItem' :
                $temp['order_id']       = $this->Order->getLastInsertId();
                $temp['quantity']       = array_key_exists('quantity', $item) ? $item['quantity'] : 0;
                $temp['tax_price']      = array_key_exists('taxamount', $item) ? $item['taxamount'] : 0;
                $temp['service_price']  = array_key_exists('serviceamount', $item) ? $item['serviceamount'] : 0;
                $temp['item_id']        = array_key_exists('id', $item) ? $item['id'] : 0;
                $temp['size_id']        = array_key_exists('size_id', $item) ? $item['size_id'] : 0;
                $temp['type_id']        = array_key_exists('type_id', $item) ? $item['type_id'] : 0;
                $temp['interval_id']    = array_key_exists('interval_id', $item) ? $item['interval_id'] : 0;

                $temp['item_price']     = array_key_exists('actual_price', $item) ? $item['actual_price'] : 0;
                $temp['total_item_price'] = array_key_exists('final_price', $item) ? $item['final_price'] : 0;
                $temp['discount']       = 0; // Flow is not known for now for this particual field
                $temp['user_id']        = $userId;
                $temp['store_id']       = $store_id;
                $temp['merchant_id']    = $merchant_id;
                $result['OrderItem']    = $temp;
                if ($paymentType == 4) {
                    $result['is_future']    = 1;
                }
                break;

            case 'OrderItemFree' :
                $temp['item_id'] = $item['id'];
                $temp['free_quantity'] = $item['freeQuantity'];
                $temp['price'] = $item['SizePrice'];
                $temp['order_id'] = $this->Order->getLastInsertId();
                $temp['store_id'] = $store_id;
                $temp['user_id'] = $userId;
                $result['OrderItemFree'] = $temp;
                break;

            case 'OrderOffer' :
                $temp['order_id'] = $this->Order->getLastInsertId();
                $temp['order_item_id'] = $this->OrderItem->getLastInsertId();
                $temp['offer_id'] = $item['offer_id'];
                $temp['offered_item_id'] = $item['offered_item_id'];
                $temp['offered_size_id'] = $item['offered_size_id'];
                $temp['quantity'] = $item['quantity'];
                $temp['store_id'] = $store_id;
                $temp['merchant_id'] = $merchant_id;
                $result['OrderOffer'] = $temp;
                break;

            case 'OrderTopping' :
                $temp['order_id'] = $this->Order->getLastInsertId();
                $temp['order_item_id'] = $this->OrderItem->getLastInsertId();
                $temp['topping_id'] = $item['id'];
                $temp['addon_size_id'] = $item['size'];
                $temp['price'] = array_key_exists('price', $item) ? $item['price'] : 0; 
                $temp['topType'] = "defaultTop";
                $temp['store_id'] = $store_id;
                $temp['merchant_id'] = $merchant_id;
                $result['OrderTopping'] = $temp;
                break;

            case 'PaidTopping' :
                $temp['order_id'] = $this->Order->getLastInsertId();
                $temp['order_item_id'] = $this->OrderItem->getLastInsertId();
                $temp['topping_id'] = $item['id'];
                $temp['addon_size_id'] = $item['size'];
                $temp['price'] = array_key_exists('price', $item) ? $item['price'] : 0;
                $temp['topType'] = "paidTop";
                $temp['store_id'] = $store_id;
                $temp['merchant_id'] = $merchant_id;
                $result['OrderTopping'] = $temp;
                break;

            case 'OrderPreference' :
                $temp['order_id'] = $this->Order->getLastInsertId();
                $temp['order_item_id'] = $this->OrderItem->getLastInsertId();
                $temp['sub_preference_id'] = $item['id'];
                $temp['size'] = $item['size'];
                $temp['price'] = array_key_exists('price', $item) ? ($item['price'] / $item['size']) : 0;
                $temp['store_id'] = $store_id;
                $temp['merchant_id'] = $merchant_id;
                $result['OrderPreference'] = $temp;
                break;

            case 'KitchenPrinter' :
                $temp['id'] = '';
                $temp['merchant_id'] = $this->Session->read('merchant_id');
                $temp['store_id'] = $this->Session->read('store_id');
                $temp['order_id'] = $this->Order->getLastInsertId();
                $temp['order_number'] = $this->Session->read('orderOverview.orderID');
                $temp['type'] = '1'; //Kitchen Printer
                $result = $temp;
                break;

            case 'ReceiptPrinter' :
                $temp['id'] = '';
                $temp['merchant_id'] = $this->Session->read('merchant_id');
                $temp['store_id'] = $this->Session->read('store_id');
                $temp['order_id'] = $this->Order->getLastInsertId();
                $temp['order_number'] = $this->Session->read('orderOverview.orderID');
                $temp['type'] = '2'; //Receipt Printer
                $result = $temp;
                break;
        }

        return $result;
    }

    /* ------------------------------------------------
      Function name:orderSave()
      Description:It will save the order after payment confimation
      created:22/7/2015
      ----------------------------------------------------- */

    public function orderTranSave($paymemt_type = null) {
        $this->loadModel('Order');
        $this->loadModel('OrderItem');
        $this->loadModel('OrderItemFree');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderPreference');
        $this->loadModel('StorePrintHistory');
        $this->loadModel('OrderPayment');

        //----------------------------------------------------------------------//
        // 1: Save OrderInfo
        //----------------------------------------------------------------------//
        $orderSucess = false;
        $orderInfo = $this->_getSessionOrder($paymemt_type);
        try {
	    if (!empty($orderInfo['order_number'])) {
                if ($this->Order->checkorderNumber($orderInfo['order_number'])) {
                    $fString = substr($orderInfo['order_number'], 0, strrpos($orderInfo['order_number'], '-'));
                    $lString = substr($orderInfo['order_number'], strrpos($orderInfo['order_number'], '-') + 1);
                    $lString = $lString + 1;
                    $orderInfo['order_number'] = $fString . "-" . $lString;
                }
            }
            $orderSucess = $this->Order->saveOrder($orderInfo);
        } catch (Exception $e) {
            $this->Session->setFlash(__('Please choose a different time for Delivery/Pickup.'), 'flash_error');
	    $this->Session->Write('timeError', 'Please choose a different time for Delivery/Pickup.');
            $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
        }

        if ($orderSucess) {
            //----------------------------------------------------------------------//
            // 주문저장 성공시 화면에서 사용
            //----------------------------------------------------------------------//
            $this->Session->write('orderOverview.orderNo', $this->Order->getLastInsertId());
            $this->Session->write('orderOverview.orderID', $orderInfo['order_number']);
            $this->Session->write('orderOverview.items', $this->Session->read('cart'));
            $this->Session->write('orderOverview.taxPrice', $this->Session->read('taxPrice'));
            $this->Session->write('orderOverview.service_fee', ($this->Session->check('final_service_fee') ? $this->Session->read('final_service_fee') : 0));
            $this->Session->write('orderOverview.delivery_fee', $this->Session->read('delivery_fee'));
            $this->Session->write('orderOverview.Discount', $this->Session->read('Discount'));
            $this->Session->write('orderOverview.Coupon', $this->Session->read('Coupon'));
            $this->Session->write('orderOverview.grand_total_final', $this->Session->read('Cart.grand_total_final'));
            $this->Session->write('orderOverview.tip', $this->Session->read('Cart.tip'));

            //----------------------------------------------------------------------//
            // 2: Save Print History
            //----------------------------------------------------------------------//
            if ($paymemt_type != 4) {
                $this->StorePrintHistory->create();
                $store_info = $this->Store->fetchStoreDetail($this->Session->read('store_id'));

                $this->loadModel('StoreSetting');
                $storeSetting = $this->StoreSetting->findByStoreId($this->Session->read('store_id'));
                if ($store_info['Store']['is_kitchen_printer'] == 1 && !empty($storeSetting['StoreSetting']['kitchen_printer_allow'])) {
                    $this->StorePrintHistory->saveStorePrintHistory($this->_getOrderItem('KitchenPrinter'));
                }
                if ($store_info['Store']['is_receipt_printer'] == 1) {
                    $this->StorePrintHistory->saveStorePrintHistory($this->_getOrderItem('ReceiptPrinter'));
                }
            }
        } else {
            return false;
        }

        if ($paymemt_type != 4) {

            $futureOrderId = $this->Session->read('FutureOrderId');
            if (!empty($futureOrderId)) {
                $this->Order->delete($futureOrderId);
                $this->OrderOffer->deleteAll(array('OrderOffer.order_id' => $futureOrderId), false);
                $this->OrderItem->deleteAll(array('OrderItem.order_id' => $futureOrderId), false);
                $this->OrderTopping->deleteAll(array('OrderTopping.order_id' => $futureOrderId), false);
                $this->OrderItemFree->deleteAll(array('OrderItemFree.order_id' => $futureOrderId), false);
                $this->OrderPreference->deleteAll(array('OrderPreference.order_id' => $futureOrderId), false);
            }
            $this->Session->delete('FutureOrderId');
        }
        foreach ($this->Session->read('cart') as $result) {

            // TODO : throw new Exception('Item key is null');
            if (!array_key_exists('Item', $result))
                return false;
	    // TODO : throw new Exception('Item id is null or empty');
            if(empty($result['Item']['id'])){
                return false;
            }
            //----------------------------------------------------------------------//
            // 3: Save OrderItem
            //----------------------------------------------------------------------//
            $this->OrderItem->create();
            $this->OrderItem->save($this->_getOrderItem("OrderItem", $result['Item'], $paymemt_type));

            if (!empty($result['Item']['freeQuantity'])) {
                $this->OrderItemFree->create();
                $this->OrderItemFree->saveItemFree($this->_getOrderItem("OrderItemFree", $result['Item']));
            }

            //----------------------------------------------------------------------//
            // 4: Save OrderOffer
            //----------------------------------------------------------------------//
            if (!empty($result['Item']['StoreOffer'])) {
                foreach ($result['Item']['StoreOffer'] as $item) {
                    $this->OrderOffer->create();
                    $this->OrderOffer->saveOfferOrder($this->_getOrderItem('OrderOffer', $item));
                }
            }

            //----------------------------------------------------------------------//
            // 5: Save DefaultTopping
            //----------------------------------------------------------------------//
            if (!empty($result['Item']['default_topping'])) {
                foreach ($result['Item']['default_topping'] as $item) {
                    $this->OrderTopping->create();
                    $this->OrderTopping->saveTopping($this->_getOrderItem('OrderTopping', $item));
                }
            }

            //----------------------------------------------------------------------//
            // 6: Save PaidTopping
            //----------------------------------------------------------------------//
            if (!empty($result['Item']['paid_topping'])) {
                foreach ($result['Item']['paid_topping'] as $item) {
                    $this->OrderTopping->create();
                    $this->OrderTopping->saveTopping($this->_getOrderItem('PaidTopping', $item));
                }
            }

            //----------------------------------------------------------------------//
            // 7: Save SubPreference
            //----------------------------------------------------------------------//
            if (!empty($result['Item']['subPreferenceOld'])) {//subpreference//subPreferenceOld
                foreach ($result['Item']['subPreferenceOld'] as $item) {
                    	$this->OrderPreference->create();
                    	$this->OrderPreference->saveSubpreference($this->_getOrderItem('OrderPreference', $item));
                }
            }
        }

        return true;
    }

    private function notificationFail($reason) {

        $this->loadModel('EmailTemplate');
        $this->loadModel('CountryCode');

        $store_id = $this->Session->read('store_id');
        $merchant_id = $this->Session->read('merchant_id');
        $emailSend = 0;
        $smsNotification = 0;
        if (AuthComponent::User()) {
            $user_email = AuthComponent::User('email');
            $fullName = AuthComponent::User('fname');
            $phone = AuthComponent::User('phone');
            $userDetail = $this->User->find("first", array('conditions' => array('User.store_id' => $store_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.merchant_id' => $merchant_id, 'User.role_id' => array(4, 5), 'User.email' => $user_email), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.phone', 'User.country_code_id', 'User.is_smsnotification', 'User.is_emailnotification')));
            if ($userDetail['User']['is_emailnotification'] == 1) {
                $emailSend = 1;
            }
            if ($userDetail['User']['is_smsnotification'] == 1) {
                $smsNotification = 1;
            }
            $country_code = $this->CountryCode->fetchCountryCodeId(AuthComponent::User('country_code_id'));
        } else {
            $userid = '';
            $emailSend = 1;
            $smsNotification = 1;
            $this->loadModel('DeliveryAddress');
            $delivery_address_id = $this->Session->read('Order.delivery_address_id');
            if (empty($delivery_address_id)) {
                $delivery_address_id = $this->Session->read('ordersummary.delivery_address_id');
            }
            $delivery_address = $this->DeliveryAddress->fetchAddress($delivery_address_id, $userid, $store_id);
            $country_code = $this->CountryCode->fetchCountryCodeId($delivery_address['DeliveryAddress']['country_code_id']);
            $user_email = $delivery_address['DeliveryAddress']['email'];
            $phone = $delivery_address['DeliveryAddress']['phone'];
            $fullName = $delivery_address['DeliveryAddress']['name_on_bell'];
        }

        $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, 'payment_error');
        $storeEmail = $this->Store->fetchStoreDetail($this->Session->read('store_id'));
        $storeAddressemail = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
        $storePhoneemail = $storeEmail['Store']['phone'];
        if ($emailSuccess) {
            if ($emailSend == 1) {
                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                $emailData = str_replace('{STORE_NAME}', $this->Session->read('storeName'), $emailData);
                $emailData = str_replace('{REASON}', $reason, $emailData);
                $url = "http://" . $storeEmail['Store']['store_url'];
                $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                $emailData = str_replace('{STORE_ADDRESS}', $storeAddressemail, $emailData);
                $emailData = str_replace('{STORE_PHONE}', $storePhoneemail, $emailData);
                $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
                $this->Email->to = $user_email;
                $this->Email->subject = $subject;
                $this->Email->from = $this->Session->read('storeEmail');
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
            }
            if ($smsNotification == 1) {
                $mobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'), '', $phone);
                $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                $smsData = str_replace('{STORE_NAME}', $this->Session->read('storeName'), $smsData);
                $smsData = str_replace('{REASON}', $reason, $smsData);
                $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                $smsData = str_replace('{STORE_PHONE}', $storeEmail['Store']['notification_number'], $smsData);
                $message = $smsData;
                $this->Common->sendSmsNotificationFront($mobnumber, $message);
            }
        }
    }

    public function notification($orderId) {
        $this->loadModel('Item');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->loadModel('EmailTemplate');
        $this->loadModel('CountryCode');

        $store_id = $this->Session->read('store_id');
        $merchant_id = $this->Session->read('merchant_id');
        $segment_type = $this->Session->read('Cart.segment_type');

        // 주문정보만 저장하는 경우는 처리하지 않는다. if $paymemt_type == 4 return;

        $printdata = $this->Common->getOrderFaxFormat($orderId);
        $this->Item->bindModel(array('belongsTo' => array('Category' => array('foreignKey' => 'category_id', 'fields' => array('name')))));
        $this->Order->bindModel(array('belongsTo' => array('Segment' => array('foreignKey' => 'seqment_id', 'fields' => array('name')))));
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))));
        $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name', 'category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))));
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount')))));
        $result_order = $this->Order->getfirstOrder($merchant_id, $store_id, $orderId);
        if ($result_order) {
            $this->loadModel('Store');
            $storeEmail = $this->Store->fetchStoreDetail($store_id);
            $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
            $encrypted_merchantId = $this->Encryption->encode($this->Session->read('merchant_id'));
            $order_type = "";
            if (!empty($result_order['Segment']['name'])) {
                $order_type = $result_order['Segment']['name'];
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
            $emailSend = 0;
            $smsNotification = 0;
            if (AuthComponent::User()) {
                $user_email = AuthComponent::User('email');
                $fullName = AuthComponent::User('fname');
                $phone = AuthComponent::User('phone');
                $userDetail = $this->User->find("first", array('conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0, 'User.merchant_id' => $merchant_id, 'User.role_id' => array(4, 5), 'User.email' => $user_email), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.phone', 'User.country_code_id', 'User.is_smsnotification', 'User.is_emailnotification')));
                if ($userDetail['User']['is_emailnotification'] == 1) {
                    $emailSend = 1;
                }
                if ($userDetail['User']['is_smsnotification'] == 1) {
                    $smsNotification = 1;
                }
                $country_code = $this->CountryCode->fetchCountryCodeId(AuthComponent::User('country_code_id'));
            } else {
                $userid = '';
                $emailSend = 1;
                $smsNotification = 1;
                $this->loadModel('DeliveryAddress');
                if (DESIGN == 4) {
                    $delivery_address_id = $this->Session->read('Order.delivery_address_id');
                } else {
                    $delivery_address_id = $this->Session->read('ordersummary.delivery_address_id');
                }


                $delivery_address = $this->DeliveryAddress->fetchAddress($delivery_address_id, $userid, $store_id);
                $country_code = $this->CountryCode->fetchCountryCodeId($delivery_address['DeliveryAddress']['country_code_id']);



                if (empty($delivery_address['DeliveryAddress']['country_code_id'])) {
                    $country_code['CountryCode']['code'] = $this->Session->read('GuestUser.countryCode');
                }
                if (empty($delivery_address['DeliveryAddress']['email'])) {
                    $user_email = $this->Session->read('GuestUser.email');
                } else {
                    $user_email = $delivery_address['DeliveryAddress']['email'];
                }
                if (empty($delivery_address['DeliveryAddress']['phone'])) {
                    $phone = $this->Session->read('GuestUser.userPhone');
                } else {
                    $phone = $delivery_address['DeliveryAddress']['phone'];
                }
                if (empty($delivery_address['DeliveryAddress']['name_on_bell'])) {
                    $fullName = $this->Session->read('GuestUser.name');
                } else {
                    $fullName = $delivery_address['DeliveryAddress']['name_on_bell'];
                }
            }

            $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, $template_type);
            if ($emailSuccess) {
                if ($emailSend == 1) {
                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $preorderDateTime = $this->Common->storeTimeFormateUser($result_order['Order']['pickup_time'], true);
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

                    // $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
                    $orderType = ($segment_type == 2) ? "Pick-up" : "Delivery";
                    $newSubject = "Your " . $storeEmail['Store']['store_name'] . " Online Order Confirmation #" . $result_order['Order']['order_number'] . "/" . $orderType;
                    $this->Email->to = $user_email;
                    $this->Email->subject = $newSubject;
                    $this->Email->from = $storeEmail['Store']['email_id'];
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
                }
                try {

                    $this->loadModel('DefaultTemplate');
                    $template_type = 'order_notification';
                    $emailTemplate = $this->DefaultTemplate->adminTemplates($template_type);
                    $storeEmailData = $emailTemplate['DefaultTemplate']['template_message'];
                    $storesmsData = $emailTemplate['DefaultTemplate']['sms_template'];

                    //Store ORder Email Notification
                    if (($storeEmail['Store']['notification_type'] == 1 || $storeEmail['Store']['notification_type'] == 3) && (!empty($storeEmail['Store']['notification_email']))) {


                        $EncorderID = $this->Encryption->encode($orderId);
                        $surl = HTTP_ROOT . 'orders/confirmOrder/' . $EncorderID;
                        $orderconHtml = '<table style="width:100%;height:100px;" border="0" cellpadding="10" cellspacing="0"><tbody><tr><td style="text-align:center;">';
                        $orderconHtml .= '<a href="' . $surl . '" style="padding:15px 15px;background-color:#F1592A;color:#FFFFFF;font-weight:bold;text-decoration: none;border:1px solid #000000;">CONFIRM ORDER</a></td></tr></tbody></table> ';
                        $storeEmailData = $orderconHtml . $printdata;
                        $subject = ucwords(str_replace('_', ' ', $emailTemplate['DefaultTemplate']['template_subject']));

                        $this->Email->to = $storeEmail['Store']['notification_email'];
                        $this->Email->subject = $subject;
                        $this->Email->from = $storeEmail['Store']['email_id'];
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
                    // Store ORder Email Notification
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
                            $this->Common->sendSmsNotificationFront($storemobnumber, $storesmsData);
                        }
                    }
                    //STore Order Notification via SMS
                } catch (Exception $e) {

                }
                if ($smsNotification == 1) {
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
                    $this->Common->sendSmsNotificationFront($mobnumber, $message);
                }
            }
        }
        $this->loadModel('StoreSetting');
        $storeSetting = $this->StoreSetting->findByStoreId($store_id);
        if (!empty($storeSetting) && !empty($storeSetting['StoreSetting']['fax_allow'])) {
            try {
                $this->orderFaxrelay($orderId, $this->Session->read('store_id'));
            } catch (Exception $e) {

            }
        }
    }

    /* ------------------------------------------------
      Function name:sucess()
      Description:It will provide the success of order
      created:22/7/2015
      ----------------------------------------------------- */

    public function success($data = null) {
        if ($this->Session->read('Coupon')) {
            $this->loadModel('Coupon');
            $data['Coupon']['id'] = $this->Session->read('Coupon.Coupon.id');
            $data['Coupon']['used_count'] = $this->Session->read('Coupon.Coupon.used_count') + 1;
            $this->Coupon->saveCoupon($data);
        }
        $cartcount = 0;
        $this->set(compact('cartcount'));
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Session->read('store_id');
        $decrypt_merchantId = $this->Session->read('merchant_id');
        $encrypted_storeId = $this->Encryption->encode($decrypt_storeId); // Encrypted Store Id
        $encrypted_merchantId = $this->Encryption->encode($decrypt_merchantId); // Encrypted Merchant Id
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));

        $finalItem = $this->Session->read('orderOverview.items');

        $this->loadModel('StoreSetting');
        $storeSetting = $this->StoreSetting->findByStoreId($decrypt_storeId);
        $this->set(compact('finalItem', 'storeSetting'));
    }

    /* ------------------------------------------------
      Function name:status()
      Description:It will provide the unsuccess of the error
      created:22/7/2015
      ----------------------------------------------------- */

    public function status($data = null) {
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Session->read('store_id');
        $decrypt_merchantId = $this->Session->read('merchant_id');
        $encrypted_storeId = $this->Encryption->encode($decrypt_storeId); // Encrypted Store Id
        $encrypted_merchantId = $this->Encryption->encode($decrypt_merchantId); // Encrypted Merchant Id
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
    }

    /* ------------------------------------------------
      Function name:status()
      Description:It will provide the unsuccess of the error
      created:22/7/2015
      ----------------------------------------------------- */

    public function statuss($data = null) {
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Session->read('store_id');
        $decrypt_merchantId = $this->Session->read('merchant_id');
        $encrypted_storeId = $this->Encryption->encode($decrypt_storeId); // Encrypted Store Id
        $encrypted_merchantId = $this->Encryption->encode($decrypt_merchantId); // Encrypted Merchant Id
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
    }

    /* ------------------------------------------------
      Function name:paymentList()
      Description:Display the list of transaction
      created:20/08/2015
      ----------------------------------------------------- */

    public function paymentList($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $this->loadModel('OrderPayment');
        $criteria = "OrderPayment.store_id =$storeID AND OrderPayment.is_deleted=0 AND OrderPayment.merchant_id=$merchantId";
        if ($this->Session->read('TransactionSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('TransactionSearchData'), true);
        } else {
            $this->Session->delete('TransactionSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        $value = '';
        if (!empty($this->request->data)) {
            $this->Session->write('TransactionSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Payment']['is_active'])) {
                $active = trim($this->request->data['Payment']['is_active']);
                $criteria .= " AND (OrderPayment.payment_status LIKE '%" . $active . "%')";
            }
            if (!empty($this->request->data['User']['search'])) {
                $value = trim($this->request->data['User']['search']);
                $criteria .= " AND (OrderPayment.transection_id LIKE '%" . $value . "%' OR Order.order_number LIKE '%" . $value . "%')";
            }
            if ($this->request->data['User']['from'] != '' && $this->request->data['User']['to'] != '') {
                $stratdate = $this->Dateform->formatDate($this->request->data['User']['from']);
                $enddate = $this->Dateform->formatDate($this->request->data['User']['to']);
                // echo $stratdate;echo "<br>";echo $enddate;die;
                // $criteria .= " AND (User.created BETWEEN ? AND ?) =" array($stratdate,$enddate);

                $criteria .= " AND (DATE(OrderPayment.created) BETWEEN '" . $stratdate . "' AND '" . $enddate . "')";
            }
        }

        $this->OrderPayment->bindModel(array('belongsTo' => array(
                'Order' => array('className' => 'Order', 'foreignKey' => 'order_id'))), false);
        $this->paginate = array('conditions' => array($criteria), 'order' => array('OrderPayment.created' => 'DESC'));
        $transactionDetail = $this->paginate('OrderPayment');
        $this->set('list', $transactionDetail);
        $this->set('keyword', $value);
    }

    
    /* ------------------------------------------------
      Function name:exportPaymentList()
      Description:Export excel list of transaction
      created:10/26/2015
      ----------------------------------------------------- */
    public function exportPaymentList() {
        $this->layout = false;
        $this->autoRender = false;
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $this->loadModel('OrderPayment');
        $criteria = "OrderPayment.store_id =$storeID AND OrderPayment.is_deleted=0 AND OrderPayment.merchant_id=$merchantId";
        if ($this->Session->read('TransactionSearchData')) {
            $this->request->data = json_decode($this->Session->read('TransactionSearchData'), true);
        }
        $value = '';
        if (!empty($this->request->data)) {
            $this->Session->write('TransactionSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Payment']['is_active'])) {
                $active = trim($this->request->data['Payment']['is_active']);
                $criteria .= " AND (OrderPayment.payment_status LIKE '%" . $active . "%')";
            }
            if (!empty($this->request->data['User']['search'])) {
                $value = trim($this->request->data['User']['search']);
                $criteria .= " AND (OrderPayment.transection_id LIKE '%" . $value . "%' OR Order.order_number LIKE '%" . $value . "%')";
            }
            if ($this->request->data['User']['from'] != '' && $this->request->data['User']['to'] != '') {
                $stratdate = $this->Dateform->formatDate($this->request->data['User']['from']);
                $enddate = $this->Dateform->formatDate($this->request->data['User']['to']);
                $criteria .= " AND (DATE(OrderPayment.created) BETWEEN '" . $stratdate . "' AND '" . $enddate . "')";
            }
        }

        $this->OrderPayment->bindModel(array('belongsTo' => array(
                'Order' => array('className' => 'Order', 'foreignKey' => 'order_id'))), false);
        $transactions = $this->OrderPayment->find('all', array('conditions' => array($criteria), 'order' => array('OrderPayment.created' => 'DESC')));
        if(!empty($transactions))
        {
            Configure::write('debug', 0);
            App::import('Vendor', 'PHPExcel');
            $objPHPExcel = new PHPExcel;
            ;
            $styleArray2 = array(
                'font' => array('name' => 'Arial', 'size' => '10', 'color' => array('rgb' => '444555'), 'bold' => true),
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'D6D6D6'))
            );
            $styleArray = array(
                'font' => array(
                    'name' => 'Arial',
                    'size' => '10',
                    'color' => array('rgb' => 'ffffff'),
                    'bold' => true,
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '0295C9'),
                ),
            );
            ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
            $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setTitle('Transactions' . date("Y-m-d"));

            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Order Id');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Transaction Id');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Sub Total');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Tax($)');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Tip');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Discount');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Total Sales Amount ($)');
            $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Date');
            $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Payment Type');
            $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Payment Status');
            $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Reason');
            $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Response Code');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('L1')->applyFromArray($styleArray);
            $i = 2;
            foreach ($transactions as $key => $data) {
                // Order No 
                $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Order']['order_number']);
                
                // Transaction No 
                $objPHPExcel->getActiveSheet()->setCellValue("B$i", (($data['OrderPayment']['transection_id'] != 0) ? $data['OrderPayment']['transection_id'] : ''));
                
                // Product Price
                $orderDetail = $this->Common->orderItemDetail($data['OrderPayment']['order_id']);
                $totalItemPrice = 0;
                if($orderDetail)
                {
                    foreach ($orderDetail as $itemKey => $itemVal)
                    {
                        if ($itemVal['OrderItem']['total_item_price']) {
                            $totalItemPrice += $itemVal['OrderItem']['total_item_price'];
                        }
                    }
                }
                $objPHPExcel->getActiveSheet()->setCellValue("C$i", $this->Common->amount_format($totalItemPrice));
                
                // Tax
                $objPHPExcel->getActiveSheet()->setCellValue("D$i", $this->Common->amount_format($data['Order']['tax_price']));
                
                // Tip
                $tipValue = (($data['Order']['tip'] && $data['Order']['tip'] > 0) ? $this->Common->amount_format($data['Order']['tip']) : '-');
                $objPHPExcel->getActiveSheet()->setCellValue("E$i", $tipValue);
                
                // Discount
                $discountData = '';
                $showcount = 0;
                if($data['Order']['coupon_code'] != null)
                {
                    $coupon_amount = $this->Common->amount_format($data['Order']['coupon_discount']);
                    $discountData .= $coupon_amount . "\n\r";
                }
                
                $promotionCount = $this->Common->usedOfferDetailCount($data['OrderPayment']['order_id']);       
                if($promotionCount > 0)
                {
                    $discountData .= "Promotions\n\r";
                }
                
                $extendedOffersCount = $this->Common->usedItemOfferDetailCount($data['OrderPayment']['order_id']);
                if($extendedOffersCount > 0)
                {
                    $discountData .= "Extended Offers\n\r";
                }
                $discountData = trim($discountData, "\n\r");
                if($discountData == '')
                {
                    $discountData = '-';
                }
                $objPHPExcel->getActiveSheet()->setCellValue("F$i", $discountData);
                $objPHPExcel->getActiveSheet()->getStyle("F$i")->getAlignment()->setWrapText(true);
                
                // Total Sales Amount ($)
                $totalPrice = $this->Common->amount_format(($data['OrderPayment']['amount'] - $data['Order']['coupon_discount']));
                $objPHPExcel->getActiveSheet()->setCellValue("G$i", $totalPrice);
                
                // Date
                $objPHPExcel->getActiveSheet()->setCellValue("H$i", $this->Dateform->us_format($this->Common->storeTimezone('', $data['OrderPayment']['created'])));
                
                // Payment Type
                $objPHPExcel->getActiveSheet()->setCellValue("I$i", $data['OrderPayment']['payment_gateway']);
                
                // Payment Status
                $objPHPExcel->getActiveSheet()->setCellValue("J$i", $data['OrderPayment']['payment_status']);
                
                // Reason
                $sReason = $data['OrderPayment']['response'];
                if ($sReason) {
                    if ($data['OrderPayment']['user_id'] == 0) {
                        $sReason .= '\nNon-members Payment';
                    }
                } else {
                    $sReason = "-";
                }
                $objPHPExcel->getActiveSheet()->setCellValue("K$i", $sReason);
                
                // Response Code
                $response = (($data['OrderPayment']['response_code']) ? $data['OrderPayment']['response_code'] : '-');
                $objPHPExcel->getActiveSheet()->setCellValue("L$i", $response);
                
                $i++;
            }
            $filename = 'Transactions' . date("Y-m-d") . ".xls"; //create a file
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=' . $filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
        else {
            $this->Session->setFlash(__('Record not Found.'), 'alert_failed');
            $this->redirect('/payments/paymentList/');
        }
        exit;
    }
    
    
    
    
    
    
    /* ------------------------------------------------
      Function name: orderDetail()
      Description: Dispaly the detail of perticular order
      created:12/8/2015
      ----------------------------------------------------- */

    public function orderDetail($order_id = null, $payment_id = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $orderId = $this->Encryption->decode($order_id);
        $paymentId = $this->Encryption->decode($payment_id);
        $this->loadModel('OrderItemFree');
        $this->loadModel('OrderPreference');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->loadModel('Item');
        $this->loadModel('OrderPayment');
        // $this->OrderItem->bindModel(array('belongsTo'=>array('Item'=>array('className' => 'Item','foreignKey'=>'item_id','fields'=>array('id','name')))), false);
        $this->OrderItemFree->bindModel(array('belongsTo' => array('Item' => array('fields' =>
                    array('id', 'name', 'category_id')))), false);
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->Item->bindModel(array('belongsTo' => array('category' => array('fields' =>
                    array('id', 'name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id'), 'order' => array('OrderTopping.id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id', 'size'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name', 'category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
        $this->Order->bindModel(
                array(
            'hasMany' => array(
                'OrderItem' => array(
                    'fields' => array('id',
                        'quantity', 'order_id', 'user_id', 'type_id',
                        'item_id', 'size_id', 'total_item_price', 'tax_price', 'interval_id')),
                'OrderItemFree' => array('foreignKey' => 'order_id', 'fields' => array('id', 'item_id', 'order_id', 'free_quantity', 'price'))
            ),
            'belongsTo' => array(
                'User' => array('className' => 'User', 'foreignKey' => 'user_id'),
                'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'),
                'DeliveryAddress' => array('className' => 'DeliveryAddress', 'foreignKey' => 'delivery_address_id'),
                'OrderStatus' => array('fields' => array('id', 'name')),
            )), false);
        $orderDetails = $this->Order->getSingleOrderDetail($merchantId, $storeID, $orderId);
        $orderPayment = $this->OrderPayment->find(
                'first', array('fields' => array('store_id', 'merchant_id', 'user_id', 'order_id', 'payment_gateway', 'amount', 'transection_id', 'response_code', 'last_digit'), 'conditions' => array('id' => $paymentId)
        ));
        if (empty($orderPayment['OrderPayment'])) {
            $orderPayment['OrderPayment'] = '';
        }
        $orderDetails[0]['OrderPayment'] = $orderPayment['OrderPayment'];
        $this->set('orderDetail', $orderDetails);
        $this->loadModel('OrderStatus');
        $statusList = $this->OrderStatus->OrderStatusList($storeID);
        $this->set('statusList', $statusList);
        $printerIP = $this->Store->fetchStorePrinterIP($storeID);
        $this->set('printerIP', $printerIP['Store']['printer_location']);
    }

    public function CancelTransaction() {
        $this->loadModel('OrderPayment');

        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $store_info = $this->Store->fetchStoreDetail($this->data['OrderPayment']['store_id']);
        $this->NZGateway->setLogin($store_info['Store']['api_username'], $store_info['Store']['api_password']);


        if (empty($this->data)) {
            $this->Session->setFlash(__("Please retry. Session data not being passed"), 'alert_failed');
            $this->redirect(array('action' => 'paymentList', 'controller' => 'payments'));
        }

        $transactionID = $this->data['OrderPayment']['transection_id'];
        if (!$transactionID) {
            $this->Session->setFlash(__("Please retry. TransactionID is not passed"), 'alert_failed');
            $this->redirect(array('action' => 'paymentList', 'controller' => 'payments'));
        }

        $response = "";
        $request_type = $this->data['Payments']['CancelType'];
        $partial_refund = "";



        switch ($request_type) {

            case "void" :
                $response = $this->NZGateway->doVoid($transactionID);
                break;
            case "refund" :
                $amount = $this->data['OrderPayment']['amount'];
                $basic_amount = $this->data['OrderPayment']['basic_amount'];
                if ($amount !== $basic_amount) {
                    $partial_refund = "Partial ";
                }

                if (!is_numeric($amount)) {
                    $this->Session->setFlash(__("Please retry. amount is not passed"), 'alert_failed');
                    $this->redirect(array('action' => 'paymentList', 'controller' => 'payments'));
                    exit;
                }
                $response = $this->NZGateway->doRefund($transactionID, $amount);
                break;
            default :
                $this->Session->setFlash(__("Please retry. Select your cancel type"), 'alert_failed');
                $this->redirect(array('action' => 'paymentList', 'controller' => 'payments'));
                break;
        }

        $Payments = $this->data['OrderPayment'];
        $Payments['response_code'] = $response['response_code'];
        $Payments['response'] = $response['responsetext'];
        $Payments['transection_id'] = $response['transactionid'];
        $Payments['payment_gateway'] = "NZGateway DPA";
        $status = $response['response_code'] != '100' ? "Failure : " : "";
        $Payments['payment_status'] = $status . $partial_refund . ucfirst($request_type) . " request";

        $datetime = new DateTime(date('Y-m-d H:i:s'));
        $datetime->setTimezone(new DateTimeZone('America/Los_Angeles'));
        $this->Logger->setup("../tmp/logs/".$datetime->format('Y-m-d H:i:s')."_cancelDPA.log");
        $this->Logger->AddRow("TRAN ID:" .$transactionID. " LOGIN ID:".$store_info['Store']['api_username']. " PASS:". $store_info['Store']['api_password']);
        $this->Logger->AddRow("SAVE DATA : ".json_encode($Payments));
        $this->Logger->AddRow("RESPONCE DATA : ".json_encode($response));
        $this->Logger->Commit();

        $success = $this->savePayment($Payments);
        if ($response['response_code'] != '100') {
            $this->Session->setFlash(__($response['responsetext']), 'alert_failed');
            $this->redirect(array('action' => 'paymentList', 'controller' => 'payments'));
        }
        $this->Session->setFlash(__($request_type . " requested successfully."), 'alert_success');
        $this->redirect(array('action' => 'paymentList', 'controller' => 'payments'));
    }

    // Set the values and begin paypal process
    public function express_checkout() {
        $this->autoRender = false;
        $store_id = $this->Session->read('store_id');
        $this->_addressInZone();
        //$orderTime = $this->checkOrderTime();
        if (DESIGN == 4) {
            $orderTime = $this->checkOrderTimeOld();
        } else {
            $orderTime = $this->checkOrderTimeNew();
        }
        if (empty($orderTime)) {
            $this->Session->setFlash(__('Please choose a different time for Delivery/Pickup.'), 'flash_error');
	    $this->Session->Write('timeError', 'Please choose a different time for Delivery/Pickup.');
            $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
        }


        if (!empty($store_id)) {
            try {
                $this->loadModel('Store');
                $storeInfo = $this->Store->fetchStorePaypalDetail($store_id);
                if (empty($storeInfo)) {
                    $this->Session->setFlash("Store Paypal detail missing.", 'flash_error');
                    $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
                }
                if (empty($storeInfo['Store']['paypal_mode'])) {
                    $this->Paypal->sandboxMode = true;
                    $this->Paypal->sandboxConfig = array(
                        'webscr' => 'https://www.sandbox.paypal.com/webscr/',
                        'endpoint' => 'https://api-3t.sandbox.paypal.com/nvp/',
                        'password' => trim($storeInfo['Store']['paypal_password']),
                        'email' => trim($storeInfo['Store']['paypal_email']),
                        'signature' => trim($storeInfo['Store']['paypal_signature'])
                    );
                } else {
                    $this->Paypal->sandboxMode = false;
                    $this->Paypal->config = array(
                        'webscr' => 'https://www.paypal.com/webscr/',
                        'endpoint' => 'https://api-3t.paypal.com/nvp/',
                        'password' => trim($storeInfo['Store']['paypal_password']),
                        'email' => trim($storeInfo['Store']['paypal_email']),
                        'signature' => trim($storeInfo['Store']['paypal_signature'])
                    );
                }
                $amount = $this->Session->check('Cart.grand_total_final') ? number_format($this->Session->read('Cart.grand_total_final'), 2) : "0.00";
                if ($this->Session->check('Discount')) {
                    $amount = number_format($amount - $this->Session->read('Discount'), 2);
                }
                $this->Paypal->amount = $amount;
                $this->Paypal->currencyCode = 'USD';
                $this->Paypal->returnUrl = Router::url(array('action' => 'get_details'), true);
                $this->Paypal->cancelUrl = Router::url(array('controller' => 'Products', 'action' => 'orderDetails'), true);
                $this->Paypal->orderDesc = 'Food Package';
                $this->Paypal->itemName = 'Food Package';
                //$this->Paypal->quantity = 1;
                $this->Paypal->expressCheckout();
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage(), 'flash_error');
                $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
            }
        } else {
            $this->Session->setFlash("Something went wrong!", 'flash_error');
            $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
        }
    }

    // Use the token in the return URL to fetch details
    public function get_details() {
        $this->autoRender = false;
        try {
            $this->Paypal->token = $this->request->query['token'];
            $this->Paypal->payerId = $this->request->query['PayerID'];
            $customer_details = $this->Paypal->getExpressCheckoutDetails();
            $this->_completeExpressCheckout($customer_details['TOKEN'], $customer_details['PAYERID'], $customer_details['AMT']);
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage(), 'flash_error');
            $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
        }
    }

    // Complete the payment, pass back the token and payerId
    private function _completeExpressCheckout($token, $payerId, $amt) {
        $this->autoRender = false;
        try {
            $this->Paypal->amount = $amt;
            $this->Paypal->currencyCode = 'USD';
            $this->Paypal->token = $token;
            $this->Paypal->payerId = $payerId;
            $response = $this->Paypal->doExpressCheckoutPayment();
            $this->_paypalCheckOutReturn($response);
        } catch (Exception $e) {
            $this->Session->setFlash($e->getMessage(), 'flash_error');
            $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
        }
    }

    /* ------------------------------------------------
      Function name:_paypalCheckOutReturn()
      Description : Save data into db after returning response from paypal checkout (return url)
      created:30/08/2016
      created By : Vikas Singh
      ----------------------------------------------------- */

    private function _paypalCheckOutReturn($response = null) {
        //pr($this->request->data); exit;
        $this->autoRender = false;
        $this->loadModel('Store');
        $this->loadModel('OrderPayment');
        $useId = "";
        if (AuthComponent::User()) {
            $userId = AuthComponent::User('id');
        } else {
            $userId = 0;
        }
        $this->request->data = $response;
        if (isset($this->request->data) && !empty($this->request->data)) {
            $orderPayment = $this->OrderPayment->find('first', array('fields' => array('id', 'store_id', 'user_id'),
                'conditions' => array('transection_id' => $this->request->data['TRANSACTIONID']),
                'recursive' => -1,
            ));
            $paymentStatus = $this->request->data['PAYMENTSTATUS'];
            if (empty($orderPayment)) {
                $store_id = $this->Session->read('store_id');
                $merchant_id = $this->Session->read('merchant_id');
                $store_info = $this->Store->fetchStoreDetail($store_id);
                $comment = " Paypal Express Check-Out : " . $this->request->data['PAYMENTSTATUS'];
                if (($this->request->data['PAYMENTSTATUS'] == "Pending") || ($this->request->data['PAYMENTSTATUS'] == "Completed")) { //paypal
                    $store_id = $this->Session->read('store_id');
                    $merchant_id = $this->Session->read('merchant_id');
                    $transaction_id = $this->request->data['TRANSACTIONID'];
                    $responsetext = 'Payment has been approved';
                    $amount = $this->Session->read('Cart.grand_total_final');
                    $payment_gateway = "PayPal";
                    $payment_status = $this->request->data['PAYMENTSTATUS'];
                    $this->request->data['payment_type'] = 2;
                    $this->paymentSection($this->request->data);
                } else {
                    $responsetext = 'Please enter proper details';
                    $payment_gateway = "PayPal";
                    $payment_status = "Failure";
                    $this->request->data['OrderPayment']['user_id'] = $userId;
                    $this->request->data['OrderPayment']['store_id'] = $store_id;
                    $this->request->data['OrderPayment']['merchant_id'] = $merchant_id;
                    $this->request->data['OrderPayment']['transection_id'] = $this->request->data['TRANSACTIONID'];
                    $this->request->data['OrderPayment']['amount'] = $this->Session->read('Cart.grand_total_final');
                    $this->request->data['OrderPayment']['payment_status'] = $payment_status;
                    $this->request->data['OrderPayment']['payment_gateway'] = $payment_gateway;
                    $this->request->data['OrderPayment']['response'] = $responsetext;
                    $this->loadModel('OrderPayment');
                    $this->OrderPayment->savePayment($this->request->data['OrderPayment']);
                    $this->loadModel('EmailTemplate');
                    $this->loadModel('CountryCode');
                    $emailSend = 0;
                    $smsNotification = 0;
                    if (AuthComponent::User()) {
                        $user_email = AuthComponent::User('email');
                        $fullName = AuthComponent::User('fname');
                        $phone = AuthComponent::User('phone');
                        $userDetail = $this->User->find("first", array('conditions' => array('User.store_id' => $store_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.merchant_id' => $merchant_id, 'User.role_id' => array(4, 5), 'User.email' => $user_email), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.phone', 'User.country_code_id', 'User.is_smsnotification', 'User.is_emailnotification')));
                        if ($userDetail['User']['is_emailnotification'] == 1) {
                            $emailSend = 1;
                        }
                        if ($userDetail['User']['is_smsnotification'] == 1) {
                            $smsNotification = 1;
                        }
                        $country_code = $this->CountryCode->fetchCountryCodeId(AuthComponent::User('country_code_id'));
                    } else {
                        $userid = '';
                        $emailSend = 1;
                        $smsNotification = 1;
                        $this->loadModel('DeliveryAddress');
                        $delivery_address_id = $this->Session->read('Order.delivery_address_id');
                        if (empty($delivery_address_id)) {
                            $delivery_address_id = $this->Session->read('ordersummary.delivery_address_id');
                        }
                        $delivery_address = $this->DeliveryAddress->fetchAddress($delivery_address_id, $userid, $store_id);
                        $country_code = $this->CountryCode->fetchCountryCodeId($delivery_address['DeliveryAddress']['country_code_id']);
                        $user_email = $delivery_address['DeliveryAddress']['email'];
                        $phone = $delivery_address['DeliveryAddress']['phone'];
                        $fullName = $delivery_address['DeliveryAddress']['name_on_bell'];
                    }

                    $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, 'payment_error');
                    $storeEmail = $this->Store->fetchStoreDetail($this->Session->read('store_id'));
                    $storeAddressemail = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                    $storePhoneemail = $storeEmail['Store']['phone'];
                    if ($emailSuccess) {
                        if ($emailSend == 1) {
                            $emailData = $emailSuccess['EmailTemplate']['template_message'];
                            $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                            $emailData = str_replace('{STORE_NAME}', $store_info['Store']['store_name'], $emailData);
                            $emailData = str_replace('{REASON}', $responsetext, $emailData);
                            $url = "http://" . $store_info['Store']['store_url'];
                            $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store_info['Store']['store_url'] . "</a>";
                            $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                            $emailData = str_replace('{STORE_ADDRESS}', $storeAddressemail, $emailData);
                            $emailData = str_replace('{STORE_PHONE}', $storePhoneemail, $emailData);
                            $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
                            $this->Email->to = $user_email;
                            $this->Email->subject = $subject;
                            $this->Email->from = $store_info['Store']['email_id'];
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
                        }
                        if ($smsNotification == 1) {
                            $mobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'), '', $phone);
                            $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                            $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                            $smsData = str_replace('{STORE_NAME}', $store_info['Store']['store_name'], $smsData);
                            $smsData = str_replace('{REASON}', $responsetext, $smsData);
                            $smsData = str_replace('{STORE_NAME}', $store_info['Store']['store_name'], $smsData);
                            $smsData = str_replace('{STORE_PHONE}', $store_info['Store']['notification_number'], $smsData);
                            $message = $smsData;
                            $this->Common->sendSmsNotificationFront($mobnumber, $message);
                        }
                    }
                    $this->Session->setFlash(__('Please enter proper details'), 'flash_error');
                    $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
                }
            } else {
                if ($paymentStatus == 'Completed') {
                    $flag = $this->OrderPayment->updateAll(array('payment_status' => $paymentStatus), array('id' => $orderPayment['OrderPayment']['id']));
                    if ($flag) {
                        $this->redirect(array('controller' => 'Payments', 'action' => 'success'));
                    } else {
                        $this->redirect(array('controller' => 'Payments', 'action' => 'success'));
                    }
                } else {
                    $this->redirect(array('controller' => 'Payments', 'action' => 'success'));
                }
            }
        } else {
            $this->Session->setFlash(__('Please enter proper details'), 'flash_error');
            $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
        }
    }

    function checkOrderTimeNew() {
        if ($this->Session->check('store_id')) {
            $currentTime = $this->Common->gettodayDate(3);
            $orderTime = '';
            if ($this->Session->check('ordersummary')) {
                $curr_orderDate = $this->reformatDate($this->Session->read('ordersummary.pickup_date'));
                $curr_orderTime = $this->Session->read('ordersummary.pickup_hour') . ':' . $this->Session->read('ordersummary.pickup_minute') . ":00";
                $orderTime = $curr_orderDate . ' ' . $curr_orderTime;
            }
            $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $this->Session->read('store_id')), 'fields' => array('Store.time_formate', 'Store.delivery_delay', 'Store.pick_up_delay')));
            $orderType = $this->Session->read('ordersummary.order_type');
            if ($orderType == 3) {//delivery
                if (!empty($storeInfo['Store']['delivery_delay'])) {
                    $delayTime = '+' . $storeInfo['Store']['delivery_delay'] . ' minutes';
                    $currentTime = strtotime($delayTime, strtotime($currentTime));
                } else {
                    $currentTime = strtotime($currentTime);
                }
            } else {//pick up
                if (!empty($storeInfo['Store']['pick_up_delay'])) {
                    $delayTime = '+' . $storeInfo['Store']['pick_up_delay'] . ' minutes';
                    $currentTime = strtotime($delayTime, strtotime($currentTime));
                } else {
                    $currentTime = strtotime($currentTime);
                }
            }

            if (strtotime($orderTime) < $currentTime || empty($orderTime)) {
                $this->Session->setFlash(__('Please choose a different time for Delivery/Pickup.'), 'flash_error');
                $this->Session->Write('timeError', 'Please choose a different time for Delivery/Pickup.');
                $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
            }
            return $orderTime;
        }
    }

    public function saveSpecialComment() {
        $this->autoRender = false;
        if ($this->request->is(array('ajax')) && !empty($this->request->data['specialComment'])) {
            if ($this->Session->check('Cart.comment')) {
                $msg = 'Comment update successfully.';
            } else {
                $msg = 'Comment save successfully.';
            }
            $this->Session->write('Cart.comment', $this->request->data['specialComment']);
            $response['status'] = 'Success';
            $response['msg'] = $msg;
            return json_encode($response);
        }
    }

    function checkOrderTimeOld() {
        if ($this->Session->check('store_id')) {
            $orderTime = '';
            $orderType = $this->Session->read('Order.order_type');
            if ($this->Session->check('Order.store_pickup_time')) {
                $this->loadModel('Store');
                $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $this->Session->read('store_id')), 'fields' => array('Store.time_formate', 'Store.delivery_delay', 'Store.pick_up_delay')));
                if ($storeInfo['Store']['time_formate'] == 1) {
                    $orderhours = date("H:i", strtotime($this->Session->read('Order.store_pickup_time')));
                } else {
                    $orderhours = $this->Session->read('Order.store_pickup_time');
                }
                $orderDate = $this->reformatDate($this->Session->read('Order.store_pickup_date'));
                $orderTime = $orderDate . ' ' . $orderhours;
            } else if ($this->Session->check('Cart.order_time')) {
                $orderTimearr = explode(' ', $this->Session->read('Cart.order_time'));
                $orderTime = $orderTimearr[0] . ' ' . $orderTimearr[1];
            }
            //$orderTime=date("Y-m-d H:i:s",strtotime($orderTime));
            $currentTime = $this->Common->gettodayDate(3);
            if ($orderType == 3) {//delivery
                if (!empty($storeInfo['Store']['delivery_delay'])) {
                    $delayTime = '+' . $storeInfo['Store']['delivery_delay'] . ' minutes';
                    $currentTime = strtotime($delayTime, strtotime($currentTime));
                } else {
                    $currentTime = strtotime($currentTime);
                }
            } else {//pick up
                if (!empty($storeInfo['Store']['pick_up_delay'])) {
                    $delayTime = '+' . $storeInfo['Store']['pick_up_delay'] . ' minutes';
                    $currentTime = strtotime($delayTime, strtotime($currentTime));
                } else {
                    $currentTime = strtotime($currentTime);
                }
            }

            //echo $orderTime . '==========' . date("Y-m-d H:i:s", $currentTime);
            if (strtotime($orderTime) < $currentTime) {
                $this->Session->setFlash(__('Please choose a different time for Delivery/Pickup.'), 'flash_error');
                $this->Session->Write('timeError', 'Please choose a different time for Delivery/Pickup.');
                $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
            }
            if (DateTime::createFromFormat('Y-m-d H:i:s', $orderTime) == false) {
                if (DateTime::createFromFormat('Y-m-d H:i', $orderTime)) {
                    $orderTime = $orderTime . ":00"; //date("Y-m-d H:i:s",strtotime($orderTime));
                } else {
                    $this->Session->setFlash(__('Please choose a different time for Delivery/Pickup.'), 'flash_error');
                    $this->Session->Write('timeError', 'Please choose a different time for Delivery/Pickup.');
                    $this->redirect(array('controller' => 'Products', 'action' => 'orderDetails'));
                }
            }

            return $orderTime;
        }
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $storeID = $this->Session->read('admin_store_id');
            $merchantId = $this->Session->read('admin_merchant_id');
            $this->loadModel('Order');
//            $this->loadModel('OrderPayment');
//            $this->OrderPayment->bindModel(array(
//                'belongsTo' => array(
//                    'Order' => array(
//                        'className' => 'Order',
//                        'foreignKey' => 'order_id'
//                    )
//                )
//                    ), false);
            //$searchData = $this->OrderPayment->find('all', array('fields' => array('OrderPayment.id', 'OrderPayment.transection_id', 'Order.order_number'), 'conditions' => array('OR' => array('OrderPayment.transection_id LIKE' => '%' . $_GET['term'] . '%', 'Order.order_number LIKE' => '%' . $_GET['term'] . '%'), 'OrderPayment.store_id' => $storeID, 'OrderPayment.is_deleted' => 0, 'OrderPayment.merchant_id' => $merchantId), 'order' => array('OrderPayment.created' => 'DESC')));
            $searchData = $this->Order->find('list', array('fields' => array('Order.order_number', 'Order.order_number'), 'conditions' => array('Order.order_number LIKE' => '%' . $_GET['term'] . '%', 'Order.store_id' => $storeID, 'Order.is_deleted' => 0, 'Order.merchant_id' => $merchantId), 'order' => array('Order.created' => 'DESC')));
//            $new_array = array();
//            if (!empty($searchData)) {
//                foreach ($searchData as $key => $val) {
//                    $new_array[] = array('label' => $val['Order']['order_number'], 'value' => $val['Order']['order_number'], 'desc' => $val['Order']['order_number'] . ", " . $val['OrderPayment']['transection_id']);
//                };
//            }
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    private function _addressInZone() {
        if (DESIGN == 1) {
            $order_type = $this->Session->read('Order.order_type');
            $delivery_address_id = $this->Session->read('Order.delivery_address_id');
        } else {
            $order_type = $this->Session->read('ordersummary.order_type');
            $delivery_address_id = $this->Session->read('ordersummary.delivery_address_id');
        }
        if (!empty($order_type) && $order_type == 3) {
            $this->loadModel('DeliveryAddress');
            $DelAddress = $this->DeliveryAddress->fetchAddress($delivery_address_id);
            $this->Common->setZonefee($DelAddress);
            $zoneData = $this->Session->read('Zone.id');
            if (empty($zoneData)) {
                $this->Session->setFlash(__("Orders cannot be delivered to this address. Please choose another address."), 'flash_error');
                $this->redirect($this->referer());
            }
        }
    }

}
