<?php
/**
 * Created by PhpStorm.
 * User: codenavi
 * Date: 3/17/16
 * Time: 9:50 AM
 */
App::uses('Controller', 'Controller');
App::uses('AuthComponent', 'Controller/Component');


class ServicesController extends Controller
{

    public $components = array('RequestHandler','Session','Common');
    public $helper = array('Encryption', 'Common');
    public $uses = array( 'Store','User','OrderPreference','OrderTopping','OrderOffer','OrderItem',
        'Order','OrderDetail','OrderStatus','Segment','StorePrintHistory','DeliveryAddress');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = null;
        $this->autoRender = false;
        $this->response->type('json');
        $req =  $this->request->data;
//        $req['store_url'] = 'teststore.iorderfoods.com';
//        $req['user_id'] = 'iorderfoods@gmail.com';
//        $req['user_pw'] = 'Bcs12345';
        if($this->Session->read('api_error')) return '';
        $this->Session->write('api_error', null);

        if(!$this->request->is('post')) {
            $this->Session->write('api_error',__("Error : Post data not received [store_url, user_id, user_pw]"));
            $this->redirect(array('controller' => 'services', 'action' => 'api_error'));
        }

        if(!array_key_exists('store_url',$req) || !array_key_exists('user_id',$req) || !array_key_exists('user_pw',$req)) {
            $this->Session->write('api_error',__("Error : required information is missing [store_url, user_id, user_pw]"));
            $this->redirect(array('controller' => 'services', 'action' => 'api_error'));
        }

        $this->loadModel('Store');
        $store_url = str_replace(array("http://","https://","www."),"",$req['store_url']);
        $requestParam = explode('/', $store_url);
        $store_url = trim($requestParam[0]); // Name of the store which we will change later with Saas
        $store_result = $this->Store->store_info($store_url);
        $store_result = $store_result['Store'];
        $store_id = $store_result["id"];
        $store_result = $this->Store->getStoreDetail($store_id);
        $store_result = $store_result['Store'];
        $merchant_id = $store_result["merchant_id"];
        $store_name = $store_result["store_name"];
        $store_phone = $store_result["phone"];
        $store_address = $store_result["address"];
        $store_timezone = $store_result["time_zone_id"];
        $store_address2 = $store_result["city"].', '.$store_result["state"].' '.$store_result["zipcode"];
        $user_id = $req["user_id"];
        $user_pw = $req["user_pw"];
        $roleId = 3; // Store Admin

        if(!$store_id) {
            $this->Session->write('api_error',__("Warning : Store url[".$store_url."] is Nothing"));
            $this->redirect(array('controller' => 'services', 'action' => 'api_error'));
        }

        $this->Session->write('Auth.Admin.role_id', 3);
        $this->Session->write('admin_time_zone_id', $store_timezone);
        $this->Session->write('admin_store_id', $store_id);
        $this->Session->write('store_id', $store_id);
        $this->Session->write('admin_merchant_id', $merchant_id);
        $this->Session->write('admin_store_name', $store_name);
        $this->Session->write('admin_store_url', $store_url);
        $this->Session->write('admin_store_phone', $store_phone);
        $this->Session->write('admin_store_address', $store_address);
        $this->Session->write('admin_store_address2', $store_address2);
        $this->Session->write('Auth.Admin.role_id', 3);
        $this->Session->write('is_signature', $store_result["is_signature"]);
        $this->Session->write('is_kitchen_category', $store_result["is_kitchen_category"]);
        $this->Session->write('is_receipt_category', $store_result["is_receipt_category"]);
        $this->Session->write('print_newline', '</br>');
        $this->loadModel('User');
        $result = $this->User->find('first', array(
            'conditions' => array(
                'User.email' => trim($user_id),
                'User.role_id' => $roleId,
                'User.is_deleted' => 0,
                'User.store_id' => $store_id),
            'fields' => array('id','password')));
        $result= $result["User"];
        if($result['password'] !== $user_pw) {
            if($result['password'] !== AuthComponent::password($user_pw)){
                $this->Session->write('api_error',__("Warning : Authentication Failed. Please Retry"));
                $this->redirect(array('controller' => 'services', 'action' => 'api_error'));
            }
        }
//        $log = $this->User->getDataSource()->getLog(false, false);
//        debug($log);
    }

    public function loginCheck() {
        $message = array('result'=>'1','data'=>'','message'=>'Success');
        return $this->response->body(json_encode($message));
    }

    public function getPrintList($print_type, $marchine=''){
        $this->layout="";
        $this->autoRender = false;
        $this->response->type('json');
        $message = array('result'=>'0','data'=>'','message'=>'');

        $store_id = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');

        $result = array();
        $arr_print_type = array('kitchen'=>1,'receipt'=>2,'dinein'=>3);
        if(array_key_exists($print_type,$arr_print_type)) {
            $_print_type = $arr_print_type[$print_type];
            $result = $this->StorePrintHistory->fetchPrintList($merchant_id,$store_id,$_print_type);
        } else {
            $message['message'] = 'Warning : Request fail Print type is nothing';
            return $this->response->body(json_encode($message));
        }

        if($marchine) {
            $this->loadModel('StorePrinterStatus');
            $store_id = $this->Session->read('admin_store_id');
            $merchant_id = $this->Session->read('admin_merchant_id');
            $print_result = $this->StorePrinterStatus->find('first', array(
                'conditions' => array(
                    'store_id' => $store_id,
                    'merchant_id' => $merchant_id,
                    'is_deleted' => 0,
                    'machine_name' => $marchine,
                ),
                'fields' => array('id','machine_name')));
            if(count($print_result)>0) {
                $this->StorePrinterStatus->save($print_result);
            }
        }

        if(is_array($result)){
            $message['result'] =count($result);
            $message['data'] =  array_keys($result);
            $message['message'] = 'Success';
        } else {
            $message['message'] = 'Warning : Print list is nothing';
        }

        return $this->response->body(json_encode($message));
    }

    public function getPrintData($print_type, $request_id){
        $this->layout="";
        $this->autoRender = false;
        $this->response->type('json');
        $message = array('result'=>'0','data'=>'','message'=>'');
        $req =  $this->request->data;

        // Data Initialization
        $_sp = '{{t}}';
        $_br = '\n';
        $_data = '';
        $_header = '';
        $_footer = '';

        $order_number = $this->StorePrintHistory->getPrintNumber($request_id);
        if(!$order_number) {
            $message['message'] = 'Warning : Print list is nothing : order number ';
            return $this->response->body(json_encode($message));
        }

        if($print_type == "dinein") {

            $this->loadModel('Booking');
            $result = $this->Booking->getBookingDetailsById($order_number);
             if(count($result)==0) {
                $message['message'] = 'Warning : Booking data is nothing [ id : '.$order_number.']';
                return $this->response->body(json_encode($message));
            }

            $res_date = $result['Booking']['reservation_date'];
            $res_date = $this->Common->storeTimeFormateUser($res_date, true);
            $res_temp = explode(" ", $res_date);

            $_header .= 'Dine-In Reservations'.$_br;
            $_data .= $_br;
            $_data .= 'Online reservation notification'.$_br;
            $_data .= 'Booking Date : '.$res_temp[0].$_br;
            $_data .= 'Booking Time : '.$res_temp[1].$res_temp[2].$_br;
            $_data .= 'No of persons: : '.$result['Booking']['number_person'].$_br;
            $_data .= 'Special Request : '.$result['Booking']['special_request'].$_br;
            $_data .= 'Contact Person : '.$result['User']['fname'].' '.$result['User']['lname'].$_br;
            $_data .= $result['User']['email'].$_br;


        } else {

            $result = $this->_getOrderData($order_number);
            if(count($result)==0) {
                $message['message'] = 'Warning : Order data is nothing [ order_id : '.$req['order_id'];
                return $this->response->body(json_encode($message));
            }

            $Order = $result['Order'];
            $Payment = $result['OrderPayment'];

            //$order_date = $this->Common->storeTimezone(null, $Order['created']);
            //$order_date = $this->Common->storeTimeFormateUser($order_date,true);
            $order_date = $this->Common->storeTimeFormateUser($Order['created'], true);

            if( $Order['is_pre_order'] == 1) $_pre_order = 'PreOrder ';
            else $_pre_order = '';
            $pickup_date = ' - '.$this->Common->storeTimeFormateUser($Order['pickup_time'], true);
        }

        //------------------------------------------------------------------------------------------//
        if($print_type=='kitchen' ) {
            $_header .= 'Order List Up'.$_br;
            $_data .= $_br.$_br.$_br;
            $_data .= 'Order Id: ' . $Order['order_number'] . $_br;
            $_data .= $order_date.$_br;
            $_data .= $_pre_order.$result['Segment']['name'].$pickup_date. $_br;
            if($Order['delivery_address_id'] !== null) {
                $_data .= 'Name: ' . $result['DeliveryAddress']['name_on_bell'].$_br;
                $_data .= 'Tel: ' . $result['DeliveryAddress']['phone'].$_br;
            } else {
                $_data .= 'Name: ' . $result['User']['fname'] . ' ' . $result['User']['lname'] . $_br;
                $_data .= 'Tel: ' . $result['User']['phone'] . $_br;
            }
            $_data .= $Payment['payment_status'] . $_br;

            foreach($result['OrderItembyCate'] as $arrCate) {

                if($this->Session->read('is_kitchen_category')){
                    $onlyOnce = 1;
                } else {
                    $onlyOnce = 0;
                }

                foreach($arrCate as $order) {
                    $_data .= $_br;
                    if ($onlyOnce) {
                        $_data .= $order['Item']['category_name'] . $_br;
                        $onlyOnce = 0;
                    }
                    $_data .= $order['quantity'] . ' ' . $order['Size']['size'] . ' ' . $order['Item']['name'];
                    $_data .= $_br;

                    if (!empty($order['OrderPreference'])) {
                        foreach ($order['OrderPreference'] as $Preferences) {
                            if (!empty($Preferences['SubPreference']['name'])) {
                                $quantity = ($Preferences['size'] > 1) ? $Preferences['size'] : '1';
                                $_data .= $_sp;
                                $_data .= $quantity . ' ' . $Preferences['SubPreference']['name'];
                                $_data .= $_br;
                            }
                        }
                    }

                    if (!empty($order['OrderTopping'])) {
                        foreach ($order['OrderTopping'] as $topping) {
                            $_data .= $_sp;
                            $addonsize = ($topping['addon_size_id'] > 1) ? $topping['addon_size_id'] : '1';
                            $_data .=  $addonsize . ' ' . $topping['Topping']['name'];
                            $_data .= $_br;
                        }
                    }
                }
                $_data .= $_br;
            }

            $_data .= $_br.$_br;
            $_data .= 'Special Instructions:'.$_br;
            $_data .= $Order['order_comments'].$_br;
        }

        //------------------------------------------------------------------------------------------//
        if($print_type=='receipt'){

            $total_item_price = 0;

            // Receipt header TODO:Center
            $_header .= ''.$this->Session->read('admin_store_name').$_br;
            $_header .= ''.$this->Session->read('admin_store_address').$_br;
            $_header .= ''.$this->Session->read('admin_store_address2').$_br;
            $_header .= ''.$this->Session->read('admin_store_phone').$_br;
            $_header .= ''.$this->Session->read('admin_store_url').$_br;

            // Order Info
            $_data .= 'Online Order# ' . $Order['order_number'] . $_br;
            $_data .= 'Date: ' . $order_date. $_br;
            $_data .= $_pre_order.$result['Segment']['name'] .$pickup_date. $_br;

            if($Order['delivery_address_id'] !== null) {
                $_data .= 'Name: ' . $result['DeliveryAddress']['name_on_bell'].$_br;
                $_data .= 'Tel: ' . $result['DeliveryAddress']['phone'].$_br;

            } else {
                $_data .= 'Name: ' . $result['User']['fname'].' '.$result['User']['lname'].$_br;
                $_data .= 'Tel: ' . $result['User']['phone'].$_br;
            }
            $_data .= $Payment['payment_status'] . $_br . $_br;

            if($Order['delivery_address_id'] !== null && $result['DeliveryAddress']['address'] !== null) {
                $address = $result['DeliveryAddress']['address'].' '. $result['DeliveryAddress']['city']. ', ';
                $address .= $result['DeliveryAddress']['state'].' '.$result['DeliveryAddress']['zipcode'];
                $_data .=  'Delivery Address : '.$_br;
                $_data .=  $address;
            }

            $_data .= $_br.'{{line}}'.$_br;
            //---------------------------------------------------------------//

            foreach($result['OrderItembyCate'] as $arrCate) {

                if($this->Session->read('is_receipt_category')){
                    $onlyOnce = 1;
                } else {
                    $onlyOnce = 0;
                }

                foreach($arrCate as $order) {

                    if($onlyOnce) {
                        $_data .= $order['Item']['category_name'].$_br;
                        $onlyOnce = 0;
                    }

                    if($order['total_item_price']>0){
                        $total_item_price += $order['total_item_price'];
                    }

                    $_item = $order['quantity'].' '.@$order['Size']['size'].' '.$order['Item']['name'];
                    $_data .= '' . $this->_textBoth($_item, $order['total_item_price']).$_br;

                    if(!empty($order['OrderPreference'])) {
                        foreach($order['OrderPreference'] as $Preferences) {
                            if(!empty($Preferences['SubPreference']['name'])){
                                $quantity = ($Preferences['size'] > 1) ? $Preferences['size'] : '1';
                                $_data .= $_sp;
                                $_data .= $quantity . ' ' . $Preferences['SubPreference']['name'];
                                $_data .= $_br;
                            }
                        }
                    }

                    if(!empty($order['OrderTopping'])) {
                        foreach($order['OrderTopping'] as $topping) {
                            $_data .=  $_sp;
                            $addonsize = ($topping['addon_size_id'] > 1) ? $topping['addon_size_id'] : '1';
                            $_data .=  $addonsize . ' ' . $topping['Topping']['name'];
                            $_data .=  $_br;
                        }
                    }
                }
                $_data .= $_br;
            }

            if($Order['order_comments']){
                $_data .= $_br.'{{line}}'.$_br;
                //---------------------------------------------------------------//
                $_data .= 'Special Instructions:'.$_br;
                $_data .= $Order['order_comments'].$_br;
                $_data .= $_br.'{{line}}'.$_br;
            }
            //---------------------------------------------------------------//
            $_data .= '' . $this->_textBoth('Total ' .count($result['OrderItem']).' items(s):', $total_item_price).$_br;
            if($Order['delivery_amount'] > 0)
                $_data .= '' . $this->_textBoth('Delivery Fee:', $Order['delivery_amount']).$_br;
            if($Order['service_amount'] > 0)
                $_data .= '' . $this->_textBoth('Service Fee:', $Order['service_amount']).$_br;
            if($Order['tip'] > 0)
                $_data .= '' . $this->_textBoth('Tip:', $Order['tip']).$_br;

            $coupon_code = '';
            if($Order['coupon_code']) {
                $coupon_code = '('.$Order['coupon_code'].')';
                $coupon_discount = '';
                if($Order['coupon_discount']) $coupon_discount = '-'.$Order['coupon_discount'];
                $_data .= '' . $this->_textBoth('Coupon Code: '.$coupon_code, $coupon_discount).$_br;
                $Order['amount'] = number_format($Order['amount'],2) - number_format($Order['coupon_discount'],2);
            }

            $_data .= '' . $this->_textBoth('Sales Tax:', $Order['tax_price']).$_br;
            $_data .= '{{doubleline}}'.$_br;
            //---------------------------------------------------------------//
            $_data .= '' . $this->_textBoth('GRAND TOTAL:', $Order['amount']).$_br;
            $_footer .= $_br;

            if($this->Session->read('is_signature') === "1"){
                $_footer .= $_br.'{{signature}}'.$_br;
                $_footer .= 'I agree to pay above total amount'.$_br;
                $_footer .= 'according to card issuer'.$_br;
                $_footer .= 'agreement'.$_br;
                $_footer .= $_br;
            }

            $_footer .= $_br.'Thank you very much.'.$_br;
            $_footer .= 'Come back again.'.$_br;
        }
        $issuedDate = $this->Common->storeTimeFormateUser(date('YmdHis'), true);
        $_footer .= 'Issued Date : '.$issuedDate.$_br;
        $_footer .= ' '.$_br;
        $_footer .= ' .'.$_br;
        $_footer .= ' '.$_br;
        $message['result'] = 1;
        $message['message'] = 'Success';
        $message['data'] = array('header'=>$_header, 'body'=>$_data,'footer'=>$_footer);
        return $this->response->body(json_encode($message));
    }

    public function _textBoth($item, $price=''){
        if($price){
            $price = '{{$}}'.money_format('%i',$price);
        }
        return $item.$price;
    }

    public function setPrinterInfo() {

        $this->autoRender = false;
        $this->response->type('json');
        $data = $this->request->data;
        $this->loadModel('StorePrinterStatus');
        $store_id = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');

        $result = $this->StorePrinterStatus->find('first', array(
            'conditions' => array(
                'store_id' => $store_id,
                'merchant_id' => $merchant_id,
                'is_deleted' => 0,
                'machine_name' => $data['machine'],
            ),
            'fields' => array('id','machine_name')));

        $save_data = ['StorePrinterStatus'];
        $save_data['StorePrinterStatus'] = [];
        $save_data['StorePrinterStatus']['id'] = '';
        $save_data['StorePrinterStatus']['store_id'] = $store_id;
        $save_data['StorePrinterStatus']['merchant_id'] = $merchant_id;
        $save_data['StorePrinterStatus']['machine_name'] = $data['machine'];
        $save_data['StorePrinterStatus']['current_version'] = $data['version'];

        // UPDATE
        if(count($result)>0){
            $save_data['StorePrinterStatus']['id'] = $result['StorePrinterStatus']['id'];
        }
        $result = $this->StorePrinterStatus->save($save_data);
    }

    public function setPrintData($print_id, $response_code='', $print_ip='', $printer_name=''){
        $this->autoRender = false;
        $this->response->type('json');

        $data = array();
        $data['id'] = $print_id;
        $data['response_code'] = $response_code;
        $data['ip'] = $print_ip;
        $data['printer'] = $printer_name;

        if(!$print_id) {
            $this->Session->write('api_error',__("Error : required information is missing [print_id] ex: https://xxx.com/setPrintData/print_key"));
            $this->redirect(array('controller' => 'services', 'action' => 'api_error'));
        }

        if($response_code && $response_code == '200') {
            $data['is_active'] = 0;
        }

        $result = $this->StorePrintHistory->save($data);
        if(is_array($result)) {
            $message['result'] = 1;
            $message['data'] = $result['StorePrintHistory'];
            $message['message'] = 'Success';
        } else {
            $this->Session->write('api_error',__("Error : required information is missing ex: https://xxx.com/setPrintData/print_key/response_code/printer_ip/printer_name"));
            $this->redirect(array('controller' => 'services', 'action' => 'api_error'));
        }

        return $this->response->body(json_encode($message));
    }


    public function cateImage() {
        $this->response->type('json');
        try {
            $image_url = $this->_saveImage("Category-Image");
            if($image_url){
                return $this->response->body(json_encode(array('result'=>'1','data'=>$image_url,'message'=>'Success')));
            } else {
                throw new Exception('Image file Save Fail');
            }
        } catch (Exception $e) {
            $this->Session->write('api_error',__("Error : ".$e->getMessage()));
            $this->redirect(array('controller' => 'services', 'action' => 'api_error'));
        }
    }

    public function itemImage() {
       $this->response->type('json');
        try {
            $image_url = $this->_saveImage("MenuItem-Image");
            if($image_url){
                return $this->response->body(json_encode(array('result'=>'1','data'=>$image_url,'message'=>'Success')));
            } else {
                throw new Exception('Image file Save Fail');
            }
        } catch (Exception $e) {
            $message = array('result'=>0,'data'=>'','message'=>$e->getMessage());
            return $this->response->body(json_encode($message));
        }
    }

    private function _saveImage($save_folder) {
        $data = $this->request->data;

        if(!isset($data['image_name'])) throw new Exception('nothing variable : image_name');
        if(!isset($data['image_base64'])) throw new Exception('nothing variable : image_base64');
        $store_id = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $name = $data['image_name']; // base64 decoded image data
        $file = base64_decode($data['image_base64']); // base64 decoded image data
        $source_img = @imagecreatefromstring($file);
        if ($source_img !== false) {
            $system = explode(".", $name);
            $file_name = $merchant_id. "-".$store_id."-".uniqid().".".$system[1];
            $file_path = '../webroot/'.$save_folder.'/'.  $file_name;
            $result = false;
            if (preg_match("/png/", $system[1]))
            {
                $result = imagepng($source_img,$file_path);
            }
            else if (preg_match("/gif/", $system[1]))
            {
                $result = imagegif($source_img, $file_path);
            }
            else
            {
                $result = imagejpeg($source_img, $file_path);
            }
            if($result) return $file_name;

        }
        return false;
    }

    /*------------------------------------------------
       Function name:index()
       Description:List Menu Items
       created:5/8/2015
      -----------------------------------------------------*/
    public function _getOrderData($order_id)
    {
        $this->loadModel('Category');
        $decrypt_storeId = $this->Session->read('admin_store_id');
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('name')), 'AddonSize' => array('fields' => array('size')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'offered_size_id', 'fields' => array('size')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array(
            'OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id')),
            'OrderPreference' => array('fields' => array('id', 'sub_preference_id','order_item_id','size'))
        ), 'belongsTo' => array(
            'Item' => array('foreignKey' => 'item_id', 'fields' => array('name','id','category_id'),
            'Type' => array('foreignKey' => 'type_id', 'fields' => array('name','id')),
            'Size' => array('foreignKey' => 'size_id', 'fields' => array('size','id')))
        ), false));
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'total_item_price','quantity', 'order_id', 'type_id', 'item_id', 'size_id')))), false);
        $this->Order->bindModel(array('belongsTo' => array('OrderPayment' => array('foreignKey' => 'payment_id','fields' => array('id', 'payment_gateway', 'payment_status', 'amount')))), false);
        $this->Order->bindModel(array('belongsTo' => array('Segment' => array('foreignKey' => 'seqment_id','fields' => array('name'),'conditions' => array('Segment.is_active'=>1)))), false);
        $this->Order->bindModel(array('belongsTo' => array('OrderStatus' => array('foreignKey' => 'order_status_id','fields' => array('id', 'name'),'conditions' => array('OrderStatus.is_active'=>1)))), false);
        $this->Order->bindModel(array('belongsTo' => array('DeliveryAddress' => array('foreignKey' => 'delivery_address_id','fields' => array('name_on_bell', 'address', 'city', 'state', 'zipcode', 'phone')))), false);
        $this->Order->bindModel(array('belongsTo' => array('User' => array('foreignKey' => 'user_id','fields' => array('fname', 'lname','city', 'phone')))), false);
        $myOrders = $this->Order->find('all',array('recursive' => 3,
            'conditions' => array(
                'Order.store_id'=>$decrypt_storeId,
                'Order.order_number'=>$order_id,
                'Order.is_active'=>1,'Order.is_deleted'=>0),
            'order' => array('Order.created' => 'DESC')));
        if(isset($myOrders[0])){
            $myOrders = $myOrders[0];
            $orderItem = $myOrders['OrderItem'];
            $arrOrderByCate = array();
            for($i=0;$i<count($orderItem);$i++) {
                $_cateKey = $orderItem[$i]['Item']['category_id'];
                $_cateName = $this->Category->find('list',array('fields'=>array('name'), 'conditions'=>array('Category.id'=>$_cateKey)));
                $orderItem[$i]['Item']['category_name'] = $_cateName[$_cateKey];
                if(!is_array($arrOrderByCate[$_cateKey])) $arrOrderByCate[$_cateKey] = [];
                array_push($arrOrderByCate[$_cateKey], $orderItem[$i]);
            }
            $myOrders['OrderItembyCate'] = $arrOrderByCate;
        }
        return $myOrders;
    }

    public function api_error(){
        $this->autoRender = false;
        $this->response->type('json');
        $message = array('result'=>0,'data'=>'','message'=>$this->Session->read('api_error'));
        $this->Session->destroy();
        return $this->response->body(json_encode($message));
    }

    public function index(){
        $this->layout="";
        $this->autoRender = false;
        echo "index page";
    }
}

