<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('HqAppController', 'Controller');

class HqdealsController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'NZGateway');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common', 'Dateform');
    public $uses = array('Merchant', 'MerchantContent', 'SocialMedia', 'Store');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('getMerchantNewsLetterContent', 'logout', 'merchant', 'location', 'gallery', 'staticContent', 'checkMerchantEmail', 'forgetPassword', 'resetPassword', 'accountActivation', 'contact_us', 'newsletter', 'checkHqEndUserEmail', 'city', 'zip');
        $merchantDetail = $this->Merchant->getMerchantDetail($this->Session->read('hq_id'));
        $id = $merchantDetail['Merchant']['id'];
        $name = $merchantDetail['Merchant']['name'];
        $image = $merchantDetail['Merchant']['background_image'];
        $logo = $merchantDetail['Merchant']['logo'];
        $logoType = $merchantDetail['Merchant']['logotype'];
        $bannerImage = $merchantDetail['Merchant']['banner_image'];
        $phone = $merchantDetail['Merchant']['phone'];
        $m_email = $merchantDetail['Merchant']['email'];
        $hqroleId = $this->Session->read('Auth.hqusers.role_id');
        $this->set(compact('name', 'image', 'logo', 'logoType', 'bannerImage', 'id', 'hqroleId', 'phone', 'm_email'));
        $this->_hqCommonData();
    }

    private function _hqCommonData() {
        $storeCity = $this->Store->find('all', array('fields' => array('city'), 'conditions' => array('Store.merchant_id' => $this->Session->read('hq_id'), 'Store.is_deleted' => 0, 'Store.is_active' => 1), 'group' => array('Store.city')));
        $merchantList = $this->MerchantContent->find('all', array('conditions' => array('MerchantContent.merchant_id' => $this->Session->read('hq_id'), 'MerchantContent.is_active' => 1, 'MerchantContent.is_deleted' => 0), 'order' => array('MerchantContent.position' => 'ASC')));
        $socialLinks = $this->SocialMedia->find('first', array('conditions' => array('merchant_id' => $this->Session->read('hq_id'), 'store_id' => NULL, 'is_active' => 1, 'is_deleted' => 0)));
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $store = $this->Store->find('all', array('fields' => array('id', 'merchant_id', 'store_name', 'store_url', 'phone', 'address', 'city', 'state', 'latitude', 'logitude', 'zipcode'), 'conditions' => array('Store.merchant_id' => $this->Session->read('hq_id'), 'Store.is_deleted' => 0, 'Store.is_active' => 1)));
        $this->loadModel('MerchantConfiguration');
        $logoPosition = $this->MerchantConfiguration->find('first', array('conditions' => array('merchant_id' => $this->Session->read('hq_id')), 'fields' => array('logo_position')));
        $this->set(compact('store', 'merchantList', 'storeCity', 'socialLinks', 'logoPosition'));
        $this->set('rem', $this->Cookie->read('Auth.email'));
        if ($this->Cookie->read('Auth.email')) {
            $this->request->data['User']['email-m'] = $this->Cookie->read('Auth.email');
            $this->request->data['User']['password-m'] = $this->Cookie->read('Auth.password');
        }
    }

    public function index() {
        $this->layout = "merchant_front";
        $this->loadModel('Coupon');
        $this->loadModel('ItemOffer');
        $this->loadModel('Offer');
        $this->loadModel('OfferDetail');
        $merchantId = $this->Session->read('hq_id');
        $condition = $ItemOfferCondition = $promotionalOfferCondition = '';
        if (!empty($this->request->data['Store']['store_id'])) {
            if ($this->request->data['Store']['store_id'] == 'All') {
                $condition = $ItemOfferCondition = $promotionalOfferCondition = '';
            } else {
                $storeId = $this->request->data['Store']['store_id'];
                $condition = 'store_id=' . $storeId;
                $ItemOfferCondition = 'ItemOffer.store_id=' . $storeId;
                $promotionalOfferCondition = 'Offer.store_id=' . $storeId;
            }
        }
        $date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $this->Coupon->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name','city'),
                )
            )
                ), false
        );
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $couponsData = $this->Coupon->find('all', array('recursive' => 2, 'conditions' => array($condition, 'Coupon.merchant_id' => $merchantId, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0, 'Coupon.number_can_use > Coupon.used_count', 'Coupon.start_date <= ' => $date, 'Coupon.end_date >= ' => $date), 'fields' => array('id', 'name', 'coupon_code', 'discount', 'discount_type', 'image', 'start_date', 'end_date', 'store_id')));
        $this->ItemOffer->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name', 'image'),
                    'type' => "INNER"
                )
            )
                ), false
        );
        $this->ItemOffer->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name','city'),
                )
            )
                ), false
        );
        $itemOfferData = $this->ItemOffer->find('all', array('conditions' => array($ItemOfferCondition, 'ItemOffer.merchant_id' => $merchantId, 'ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.start_date <= ' => $date, 'ItemOffer.end_date >= ' => $date), 'fields' => array()));
        $this->Offer->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('name', 'image'),
                    'type' => 'INNER'
                ),
            ),
                ), false
        );
        $this->Offer->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name','city'),
                )
            )
                ), false
        );
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $promotionalOfferData = $this->Offer->find('all', array('recursive' => 2, 'conditions' => array($promotionalOfferCondition, 'Offer.merchant_id' => $merchantId, 'Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'OR' => array(array('Offer.offer_start_date <= ' => $date, 'Offer.offer_end_date >= ' => $date), array('Offer.offer_start_date' => NULL, 'Offer.offer_end_date' => NULL))), 'fields' => array('item_id', 'description', 'offerImage', 'store_id','offer_start_date','offer_end_date')));
        $userId = $this->Session->read('Auth.hqusers.id');
        $this->loadModel('UserCoupon');
        $couponIdList = $this->UserCoupon->find('list', array('conditions' => array($condition, 'merchant_id' => $merchantId, 'user_id' => $userId, 'is_active' => 1, 'is_deleted' => 0), 'fields' => array('coupon_id')));
        $storeList = $this->Store->getMerchantStoresDet($this->Session->read('hq_id'));
//        pr($couponsData);
//        pr($itemOfferData);
//        pr($promotionalOfferData);
        $this->set(compact('couponsData', 'itemOfferData', 'promotionalOfferData', 'couponIdList', 'userId', 'storeList'));
    }

    public function addCoupon() {
        $this->autoRender = false;
        $userId = $this->Session->read('Auth.hqusers.id');
        if ($this->request->is('ajax') && !empty($this->request->data['coupon_id']) && !empty($userId) && !empty($this->request->data['coupon_code'])) {
            $this->loadModel('UserCoupon');
            $this->loadModel('Coupon');
            $coupon_id = $this->Encryption->decode($this->request->data['coupon_id']);
            $cData = $this->Coupon->findById($coupon_id, array('store_id'));
            $coupon_code = $this->request->data['coupon_code'];
            $store_id = $this->Session->read('store_id');
            $count = $this->UserCoupon->find('count', array('conditions' => array('user_id' => $userId, 'coupon_id' => $coupon_id, 'store_id' => $store_id)));
            if ($count == 0) {
                $data['user_id'] = $userId;
                $data['store_id'] = $cData['Coupon']['store_id'];
                $data['coupon_id'] = $coupon_id;
                $data['coupon_code'] = $coupon_code;
                $data['merchant_id'] = $this->Session->read('hq_id');
                $data['is_active'] = 1;
                $data['is_delete'] = 0;
                $this->UserCoupon->create();
                if ($this->UserCoupon->save($data)) {
                    $response['status'] = 'Success';
                    $response['msg'] = 'Coupon added successfully.';
                } else {
                    $response['status'] = 'Error';
                    $response['msg'] = 'Something went wrong!.';
                }
            } else {
                $response['status'] = 'Error';
                $response['msg'] = 'Coupon already added.';
            }
        } else {
            $response['status'] = 'Error';
            $response['msg'] = 'Something went wrong!.';
        }
        return json_encode($response);
    }

}
