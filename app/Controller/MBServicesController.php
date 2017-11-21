<?php
/**
 * Created by EBankcardservice.
 * User: codenavi
 * Date: 10/17/16
 * Time: 9:50 AM
 */
App::uses('Controller', 'Controller');

class MBServicesController extends AppController
{ 

    public $components = array('Session', 'Cookie','RequestHandler','Encryption' ,'Common', 'NZGateway', 'MobileDetect','Email', 'Dateform', 'Webservice');
    public $helpers = array('Html', 'Form');
      public $uses = array('User', 'Store', 'Merchant', 'DeliveryAddress', 'Order', 'OrderPayment', 'MobileOrder');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('payment', 'message', 'nzgateway','nzgateway_response','checkCardType');
        $this->layout = 'Services/mobile';
        $this->gatewayURL = "https://secure.nzgateway.com/api/v2/three-step";
    }


   public function checkCardType(){
        $this->autoRender=false;
        $this->layout = false;
        if ($this->request->is('ajax')) {
            $this->loadModel('Store');
            $this->loadModel('Order');
            if($this->Session->check('payment.order_seq')){
                
                $reqCarNumber=$this->request->data['result'];
                if(empty($reqCarNumber)){
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
                
                
                $order_id= $this->Session->read('payment.order_seq');
                $orderDet= $this->Order->find('first',array('fields'=>array('id','store_id'),'conditions'=>array('Order.id'=>$order_id)));
                $storeId=$orderDet['Order']['store_id'];
                $orderDet= $this->Store->find('first',array('fields'=>array('id','credit_card_type'),'conditions'=>array('Store.id'=>$storeId)));
                
               // Visa,Master,Discover,Amex - Database
                //echo $orderDet['Store']['credit_card_type'];
               // amex, visa_electron, visa, mastercard, discover
                if(empty($orderDet['Store']['credit_card_type'])){                    
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
                
                $creditCardType=array_map('strtolower',explode(",",$orderDet['Store']['credit_card_type']));
                $cardnamearr=explode("_",$reqCarNumber);
                $cardname=str_replace('card','',strtolower($cardnamearr[0]));
                //echo $cardname;
                if(in_array($cardname, $creditCardType)){
		    //echo "In condition";
                    $responsedata['response'] = 1;
                    return json_encode($responsedata);
                }else{
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            }
        }
    }


     public function payment($api_key, $order_seq,$store_id)
    {
        $api_key=$this->Encryption->decode($api_key);
        $order_seq=$this->Encryption->decode($order_seq);
        $this->loadModel('Order');
        $this->loadModel('Store');
        $this->loadModel('User');
        $this->loadModel('OrderPayment');
        $this->loadModel('NzsafeUser');
        $this->Session->delete('payment');

        //---------------------------------------------------------------------------//
        // Check the Account Info
        //---------------------------------------------------------------------------//
        if(isset($store_id) && !empty($store_id)){
            $store_id = $this->Encryption->decode($store_id);
        }else{
            $store_id = $this->Session->read('store_id');
        }
        $this->Store->unBindModel(array('belongsTo' => array('StoreTheme')), false);
                $this->Store->unBindModel(array('belongsTo' => array('StoreFont')), false);
                $this->Store->unBindModel(array('hasOne' => array('SocialMedia')), false);
                $this->Store->unBindModel(array('hasMany' => array('StoreContent')), false);
                $this->Store->unBindModel(array('hasMany' => array('StoreGallery')), false);

        $store_info = $this->Store->fetchStoreDetail($store_id);
        $customer_vault_id = "";
        if (count($store_info) > 0) {
            $store_info = $store_info['Store'];
        } else {
             $responsedata['response'] = 0;
            $responsedata['message'] = "Warning : session time out. retry payment";
            return json_encode($responsedata);
            //$success = false;
            //$message = "Warning : session time out. retry payment";
            //$this->Session->write('api_message', __($message));
            //$this->Session->write('api_success', __($success));
            //$this->redirect(array('controller' => 'mbservices', 'action' => 'message'));
        }
       if ($api_key === $store_info['nzgateway_apikey']) {
            $this->Session->write('payment.nzgateway_apikey', $api_key);
            $this->set(compact('api_key'));
        } else {
            $responsedata['response'] = 0;
            $responsedata['message'] = "Store API Key does not match";
            return json_encode($responsedata);
            //$success = false;
            //$message = "Store API Key does not match.";
            //$this->Session->write('api_message', __($message));
            //$this->Session->write('api_success', __($success));
            //$this->redirect(array('controller' => 'mbservices', 'action' => 'message'));
        }

        //echo $this->gatewayURL;
        $order_info = $this->Order->find('all',array('recursive'=>3,'conditions'=>array('Order.id'=>$order_seq)));       
        if (count($order_info) > 0) {
            $order_info = $order_info[0]['Order'];
            $this->set(compact('order_info'));
            $this->set(compact('amount'));
            $this->Session->write('payment.order_seq', $order_seq);
            $this->Session->write('payment.order_id', $order_info['order_number']);
            $this->Session->write('payment.seqment_id', $order_info['seqment_id']);
//pr($order_info);
//echo $order_info['user_id']."<br>";
            if ($order_info['user_id'] > 0) {
                $is_member = true;
                $this->Session->write('payment.user_id', $order_info['user_id']);
                $user_info = $this->User->getUser($order_info['user_id'], $store_id);
                $this->Session->write('payment.user_email', $user_info['User']['email']);
                $nzsafe_info = $this->NzsafeUser->getUser($order_info['user_id']);
                $nzsafe_info = $nzsafe_info['NzsafeUser'];
                $this->Session->write('payment.nzsafe_id', $nzsafe_info["id"]);
                $this->NZGateway->setLogin($store_info['api_username'], $store_info['api_password']);
                $response = $this->NZGateway->getVault($nzsafe_info["customer_vault_id"]);
                if (count($response) > 0) {
                    $customer_vault_id = $nzsafe_info["customer_vault_id"];
                }
            }
        } else {
            $responsedata['response'] = 0;
            $responsedata['message'] = "Order seq dose not match";
            return json_encode($responsedata);
            //$success = false;
            //$message = "Order seq dose not match.";
            //$this->Session->write('api_message', __($message));
            //$this->Session->write('api_success', __($success));
            //$this->redirect(array('controller' => 'mbservices', 'action' => 'message'));
        }
        $this->set(compact('customer_vault_id'));
    }

    public function nzgateway()
    {
        
        //$_POST['amount']=2.01;
        $user_id=$this->Session->read('payment.user_id');
        $user_email=$this->Session->read('payment.user_email');
        //echo $user_id."<br>";
        //pr($_POST)."<br>";;
        //die;
        if (!$_POST['DO_STEP_1']) {
            $success = false;
            $message = "Warning : session time out. retry payment";
            $this->Session->write('api_message', __($message));
            $this->Session->write('api_success', __($success));
            //$this->message();
            //$this->redirect(array('controller' => 'mbservices', 'action' => 'message'));
        }
        

        $RedirectUrl = Router::fullBaseUrl() . "/MBServices/nzgateway_response";
        // Initiate Step One: Now that we've collected the non-sensitive payment information, we can combine other order information and build the XML format.
        $xmlRequest = new DOMDocument('1.0', 'UTF-8');
        $xmlRequest->formatOutput = true;
        $xmlSale = $xmlRequest->createElement('sale');
        // Amount, authentication, and Redirect-URL are typically the bare minimum.
        $this->appendXmlNode($xmlRequest, $xmlSale, 'api-key', $_POST['api_key']);
        $this->appendXmlNode($xmlRequest, $xmlSale, 'redirect-url', $RedirectUrl);
        $this->appendXmlNode($xmlRequest, $xmlSale, 'amount', $_POST['amount']);
        $this->appendXmlNode($xmlRequest, $xmlSale, 'ip-address', $_SERVER["REMOTE_ADDR"]);
        $this->appendXmlNode($xmlRequest, $xmlSale, 'currency', 'USD');

        // Some additonal fields may have been previously decided by user
        if (isset($_POST["use_vault"])) {
            if (!empty($_POST['customer-vault-id'])) {
                $this->appendXmlNode($xmlRequest, $xmlSale, 'customer-vault-id', $_POST['customer-vault-id']);
                $this->Session->write('payment.vault_id', $_POST['customer-vault-id']);
            } else {
                $this->Session->delete('payment.vault_id');
                $xmlAdd = $xmlRequest->createElement('add-customer');
                $this->appendXmlNode($xmlRequest, $xmlAdd, 'customer-vault-id', $_POST['customer-vault-id']);
                $xmlSale->appendChild($xmlAdd);
            }
        }

        // Set the Billing and Shipping from what was collected on initial shopping cart form
        $xmlBillingAddress = $xmlRequest->createElement('billing');
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress, 'first-name', $_POST['billing-address-first-name']);
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress, 'last-name', $_POST['billing-address-last-name']);
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress, 'address1', $_POST['billing-address-address1']);
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress, 'city', $_POST['billing-address-city']);
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress, 'state', $_POST['billing-address-state']);
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress, 'postal', $_POST['billing-address-zip']);
        //billing-address-email
        $xmlSale->appendChild($xmlBillingAddress);
        $xmlRequest->appendChild($xmlSale);

        // Process Step One: Submit all transaction details to the Payment Gateway except the customer's sensitive payment information.
        // The Payment Gateway will return a variable form-url.
        $data = $this->sendXMLviaCurl($xmlRequest, $this->gatewayURL);
        // Parse Step One's XML response
        $gwResponse = @new SimpleXMLElement($data);
        if ((string)$gwResponse->result == 1) {
            // The form url for used in Step Two below
            
            $formURL = $gwResponse->{'form-url'};
            //echo $formURL; die;
            //$this->formUrlData($formURL,$_POST);
            $this->set(compact('formURL'));
        } else {
            //throw New Exception(print " Error, received " . $data);
	    $responsedata['response'] = 0;
            $responsedata['message'] = "Error, received " . $data;
            return json_encode($responsedata);
        }
    }

    public function nzgateway_response()
    {
        
        $this->autoRender = false;
        $this->loadModel('Order');
        $this->loadModel('Store');
        $this->loadModel('OrderPayment');
        
        $this->loadModel('NzsafeUser');

        if (!empty($_GET['token-id'])) {
            // Step Three: Once the browser has been redirected, we can obtain the token-id and complete
            // the transaction through another XML HTTPS POST including the token-id which abstracts the
            // sensitive payment information that was previously collected by the Payment Gateway.
            $APIKey = $this->Session->read('payment.nzgateway_apikey');
            $tokenId = $_GET['token-id'];
            $order_id= $this->Session->read('payment.order_seq');
            
            $xmlRequest = new DOMDocument('1.0', 'UTF-8');
            $xmlRequest->formatOutput = true;
            $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
            $this->appendXmlNode($xmlRequest, $xmlCompleteTransaction, 'api-key', $APIKey);
            $this->appendXmlNode($xmlRequest, $xmlCompleteTransaction, 'token-id', $tokenId);
            $xmlRequest->appendChild($xmlCompleteTransaction);
            $data = $this->sendXMLviaCurl($xmlRequest, $this->gatewayURL);
            $gwResponse = @new SimpleXMLElement((string)$data);
            
            //echo "transaction-id: - ".(string)$gwResponse->{'transaction-id'}."<br>";
            //echo "result-text: - ".(string)$gwResponse->{'result-text'}."<br>";
            //echo "result-code: - ".(string)$gwResponse->{'result-code'}."<br>";
            //pr($gwResponse);
            //die;
            $orderDet= $this->Order->find('first',array('conditions'=>array('Order.id'=>$order_id)));
            $storeId=$orderDet['Order']['store_id'];
            $merchant_id=$orderDet['Order']['merchant_id'];
            $user_id=$orderDet['Order']['user_id'];
            $address_id=$orderDet['Order']['delivery_address_id'];
            if ((string)$gwResponse->result == 1) {                
                $this->Session->write('payment.vault_id', (string)$gwResponse->{'customer-vault-id'});
                
                $amount=$orderDet['Order']['amount'];
                $orderData['id']=$orderDet['Order']['id'];
                $orderData['is_active']=1;
                $orderData['is_future_order']=0;
                if($this->Order->save($orderData)){
                $responsedata['message'] = "Order has been placed successfully";
                $responsedata['orderid'] = $orderDet['Order']['id'];
                $responsedata['response'] = 1;
                $responsedata['payment_type'] = 'CreditCard';
                $responsedata['total_amount'] = $orderDet['Order']['amount'];
                
                // Order payment save
                
                $requestBody['OrderPayment']['order_id'] = $orderDet['Order']['id'];
                $requestBody['OrderPayment']['user_id'] = $orderDet['Order']['user_id'];
                $requestBody['OrderPayment']['store_id'] = $orderDet['Order']['store_id'];
                $requestBody['OrderPayment']['merchant_id'] = $orderDet['Order']['merchant_id'];
                $requestBody['OrderPayment']['transection_id'] = isset($gwResponse->{'transaction-id'}) ? (string)$gwResponse->{'transaction-id'} : '';
                $requestBody['OrderPayment']['amount'] = $amount;
                $requestBody['OrderPayment']['response'] = isset($gwResponse->{'result-text'}) ? (string)$gwResponse->{'result-text'} : '';
                $requestBody['OrderPayment']['response_code'] = isset($gwResponse->{'result-code'}) ? (string)$gwResponse->{'result-code'}: '';
                $requestBody['OrderPayment']['payment_gateway'] ='NZGateway';
                $requestBody['OrderPayment']['payment_status'] = 'PAID by credit card';
                //pr($requestBody);
                $this->OrderPayment->save($requestBody['OrderPayment']);
                $orderPaymentID=$this->OrderPayment->getLastInsertId();   
                $this->Order->updateAll(array('payment_id' => $orderPaymentID), array('Order.id' => $orderDet['Order']['id']));
                $this->Webservice->orderPushNotification($orderDet['Order']['id']);
                $this->notification($orderDet['Order']['id'], $storeId, $merchant_id, $user_id);
                return json_encode($responsedata);
                }
            } else {
                $message = (string)$gwResponse->{'result-text'};
                $responsedata['response'] = 0;
                $responsedata['message'] = $message.", Please try again";
                $this->notificationFail($responsedata['message'], $storeId, $merchant_id, $user_id, $address_id);
                return json_encode($responsedata);
                //$success = false;
                //$message = (string)$gwResponse->{'result-text'};
            }
//            $result = $this->_getItem("OrderPayment", $gwResponse, "NZGateway 3Step");
//            $this->savePayment($result);

            //$success = true;
            //$this->Session->write('api_token', __($_GET['token-id']));
            //$this->Session->write('api_message', __(""));
            //$this->Session->write('api_success', __($success));
            //$this->message();
            //$this->redirect(array('controller' => 'mbservices', 'action' => 'message'));
        }
    }


    private function sendXMLviaCurl($xmlRequest, $gatewayURL)
    {
        // helper function demonstrating how to send the xml with curl
        $ch = curl_init(); // Initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $gatewayURL); // Set POST URL
        $headers = array();
        $headers[] = "Content-type: text/xml";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add http headers to let it know we're sending XML
        $xmlString = $xmlRequest->saveXML();
        curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return into a variable
        curl_setopt($ch, CURLOPT_PORT, 443); // Set the port number
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Times out after 30s
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString); // Add XML directly in POST
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // This should be unset in production use. With it on, it forces the ssl cert to be valid
        // before sending info.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if (!($data = curl_exec($ch))) {
            print  "curl error =>" . curl_error($ch) . "\n";
            throw New Exception(" CURL ERROR :" . curl_error($ch));
        }
        curl_close($ch);
        return $data;
    }

    // Helper function to make building xml dom easier
    private function appendXmlNode($domDocument, $parentNode, $name, $value)
    {
        $childNode = $domDocument->createElement($name);
        $childNodeValue = $domDocument->createTextNode($value);
        $childNode->appendChild($childNodeValue);
        $parentNode->appendChild($childNode);
    }

    public function message()
    {

        //$isiOS = $this->MobileDetect->detect("isiOS");
        //$isAndroidOS = $this->MobileDetect->detect("isAndroidOS");
        $isiOS = "isiOS";
        $isAndroidOS = "isAndroidOS";
        $token = $this->Session->read('api_token');
        $message = $this->Session->read('api_message');
        $success = $this->Session->read('api_success');
        $this->Session->delete('api_token');
        $this->Session->delete('api_message');
        $this->Session->delete('api_success');

        $this->set(compact('isiOS'));
        $this->set(compact('isAndroidOS'));
        $this->set(compact('token'));
        $this->set(compact('message'));
        $this->set(compact('success'));
    }
    
    
 //function notification($orderId,$store_id,$merchant_id,$segment_type)
    function notification($orderId,$store_id,$merchant_id,$user_id) {
        $this->loadModel('Item');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->loadModel('EmailTemplate');
        $this->loadModel('CountryCode');
        
//        $orderId=1070;
//        $store_id = 2;
//        $merchant_id = 1;
       
        // ????? ???? ??? ???? ???. if $paymemt_type == 4 return;
                                    
        $printdata = $this->Webservice->getOrderFaxFormat($orderId,$store_id,$merchant_id);
        $this->Item->bindModel(array('belongsTo' => array('Category' => array('foreignKey' => 'category_id', 'fields' => array('name')))));
         $this->Order->bindModel(array('belongsTo' => array('Segment' => array('foreignKey' => 'seqment_id', 'fields' => array('name')))));
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))));
        $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name', 'category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))));
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount')))));
        $result_order = $this->Order->getfirstOrder($merchant_id, $store_id, $orderId);
        $segment_type =$result_order['Order']['seqment_id'];
         $order_type="";
            if(!empty($result_order['Segment']['name'])){
                $order_type=$result_order['Segment']['name'];
            }
        
      
        if ($result_order) {
            $this->loadModel('Store');
            $storeEmail = $this->Store->fetchStoreDetail($store_id);
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
            if ($user_id) {
                $userDetail=$this->User->find("first",array('conditions'=>array('User.id'=>$user_id,'User.is_active'=>1,'User.is_deleted'=>0),'fields'=>array('User.id','User.email','User.fname','User.phone','User.country_code_id')));
                $user_email = $userDetail['User']['email'];
                $fullName = $userDetail['User']['fname'];
                $phone = $userDetail['User']['phone'];
                $country_code = $this->CountryCode->fetchCountryCodeId($userDetail['User']['country_code_id']);
            } else {
                $userid = '';
                $this->loadModel('DeliveryAddress');
                $delivery_address_id = $result_order['Order']['delivery_address_id'];
                $delivery_address = $this->DeliveryAddress->fetchAddress($delivery_address_id, $userid, $store_id);
                $country_code = $this->CountryCode->fetchCountryCodeId($delivery_address['DeliveryAddress']['country_code_id']);
                $user_email = $delivery_address['DeliveryAddress']['email'];
                $phone = $delivery_address['DeliveryAddress']['phone'];
                $fullName = $delivery_address['DeliveryAddress']['name_on_bell'];
            }

            $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, $template_type);
            if ($emailSuccess) {
                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                $preorderDateTime = $this->Webservice->storeTimeFormateUser($result_order['Order']['pickup_time'], true,$store_id);
                if(isset($preorderDateTime) && !empty($preorderDateTime)){
                                $orderDateTime= explode(" ", $preorderDateTime);
                                $date=$orderDateTime[0];
                                $time=$orderDateTime[1];
                                if(isset($orderDateTime[2]) && !empty($orderDateTime[2])){
                                    $storeTimeAm=trim($orderDateTime[2]);
                                    $time=$time.$storeTimeAm;
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
                $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                $url = "http://" . $storeEmail['Store']['store_url'];
                $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
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
                    if(!empty($storeEmail['Store']['twilio_api_key']) && !empty($storeEmail['Store']['twilio_api_token']) && !empty($storeEmail['Store']['twilio_number'])){
                        if (($storeEmail['Store']['notification_type'] == 2 || $storeEmail['Store']['notification_type'] == 3) && (!empty($storeEmail['Store']['notification_number']))) {
                            $storemobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'), '', $storeEmail['Store']['notification_number']);
                            if ($storesmsData) {
                                $storesmsData = str_replace('{ORDER_NUMBER}', $result_order['Order']['order_number'], $storesmsData);
                                $storesmsData = str_replace('{ORDER_DATE}', $date, $storesmsData);
                                $storesmsData = str_replace('{ORDER_TIME}', $time, $storesmsData);
                                $storesmsData = str_replace('{ORDER_TYPE}', $order_type, $storesmsData);
                                $storesmsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $storesmsData);
                                $storesmsData = str_replace('{STORE_PHONE}', $storeEmail['Store']['notification_number'], $storesmsData);
                                $this->Webservice->sendSmsNotificationFront($storemobnumber, $storesmsData,$store_id);
                            }
                        }
                        $mobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'), '', $phone);
                        $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                        $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                        $smsData = str_replace('{ORDER_STATUS}', 'Pending', $smsData);
                        $smsData = str_replace('{ORDER_NUMBER}', $result_order['Order']['order_number'], $smsData);
                        $smsData = str_replace('{ORDER_DATE}', $date, $smsData);
                        $smsData = str_replace('{ORDER_TIME}', $time, $smsData);
                        $smsData = str_replace('{ORDER_TYPE}', $order_type, $smsData);
                        $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                        $smsData = str_replace('{PRE_ORDER_DATE_TIME}', date('m-d-Y H:i a', strtotime($result_order['Order']['pickup_time'])), $smsData);
                                        $smsData = str_replace('{STORE_PHONE}', $storeEmail['Store']['notification_number'], $smsData);
                        $message = $smsData;
                        $this->Webservice->sendSmsNotificationFront($mobnumber, $message,$store_id);
                        
                        }
                    //STore Order Notification via SMS
                } catch (Exception $e) {

                }
                
            }
        }
        try {
            $this->orderFaxrelay($orderId, $store_id,$merchant_id,$printdata);
        } catch (Exception $e) {

        }
    }
    
    public function orderFaxrelay($orderId = null, $storeID = null,$merchant_id=null,$printdata=null) {
        if(isset($printdata) && !empty($printdata)){
            $printdata = $printdata;
        }else{
            $printdata = $this->Webservice->getOrderFaxFormat($orderId,$storeID,$merchant_id);
        }
        
        //$username = 'ecomm2015'; // Enter your Interfax username here
        //$password = 'ecomm2015'; // Enter your Interfax password here
        $this->loadModel('Store');
        //$storeID = $this->Session->read('admin_store_id');
        $storeInfo = $this->Store->fetchStoreDetail($storeID);
        $faxnumber = $storeInfo['Store']['fax_number']; // Enter your designated fax number here in the format +[country code][area code][fax number], for example: +12125554874
        $username = $storeInfo['Store']['fax_username'];
        $password = $storeInfo['Store']['fax_password'];

        if (!empty($faxnumber) && !empty($username) && !empty($password)) {
            $filetype = 'HTML';
            try {
                $params = (object) [];
                $client = new SoapClient("http://ws.interfax.net/dfs.asmx?wsdl");
                $params->Username = $username;
                $params->Password = $password;
                $params->FaxNumber = $faxnumber;
                $params->Data = $printdata;
                $params->FileType = $filetype;
                $faxResult = $client->SendCharFax($params);
            } catch (Exception $e) {

            }
        }
    }
    
    function notificationFail($reason,$store_id,$merchant_id,$user_id,$delivery_address_id) {

        $this->loadModel('EmailTemplate');
        $this->loadModel('CountryCode');
        $this->loadModel('User');
         $this->loadModel('DeliveryAddress');
//        $store_id=2;
//        $merchant_id=1;
//        $user_id=1;
//        $delivery_address_id=1;

        if ($user_id) {
            $userDetail=$this->User->find("first",array('conditions'=>array('User.id'=>$user_id,'User.is_active'=>1,'User.is_deleted'=>0),'fields'=>array('User.id','User.email','User.fname','User.phone','User.country_code_id')));
            
                $user_email = $userDetail['User']['email'];
                $fullName = $userDetail['User']['fname'];
                $phone = $userDetail['User']['phone'];
                $country_code = $this->CountryCode->fetchCountryCodeId($userDetail['User']['country_code_id']);
        } else {
            $userid = '';
            $delivery_address_id = $delivery_address_id;
            $delivery_address = $this->DeliveryAddress->fetchAddress($delivery_address_id, $userid, $store_id);
            if(!empty($delivery_address['DeliveryAddress']['country_code_id'])){
                $country_code = $this->CountryCode->fetchCountryCodeId($delivery_address['DeliveryAddress']['country_code_id']);
            }else{
                $country_code ='+1';
            }
            
            $user_email = $delivery_address['DeliveryAddress']['email'];
            $phone = $delivery_address['DeliveryAddress']['phone'];
            $fullName = $delivery_address['DeliveryAddress']['name_on_bell'];
        }

        $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, 'payment_error');
        $storeEmail = $this->Store->fetchStoreDetail($store_id);
        $storeAddressemail = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
        $storePhoneemail = $storeEmail['Store']['phone'];
        if ($emailSuccess) {
            $emailData = $emailSuccess['EmailTemplate']['template_message'];
            $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
            $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
            $emailData = str_replace('{REASON}', $reason, $emailData);
            $url = "http://" . $storeEmail['Store']['store_url'];
            $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
            $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
            $emailData = str_replace('{STORE_ADDRESS}', $storeAddressemail, $emailData);
            $emailData = str_replace('{STORE_PHONE}', $storePhoneemail, $emailData);
            $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
            $this->Email->to = $user_email;
            $this->Email->subject = $subject;
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
            if(!empty($storeEmail['Store']['twilio_api_key']) && !empty($storeEmail['Store']['twilio_api_token']) && !empty($storeEmail['Store']['twilio_number'])){
            $mobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'), '', $phone);
            $smsData = $emailSuccess['EmailTemplate']['sms_template'];
            $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
            $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
            $smsData = str_replace('{REASON}', $reason, $smsData);
            $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
            $smsData = str_replace('{STORE_PHONE}', $storeEmail['Store']['notification_number'], $smsData);
            $message = $smsData;
            $this->Webservice->sendSmsNotificationFront($mobnumber, $message,$store_id);
            }
        }
    }
}
