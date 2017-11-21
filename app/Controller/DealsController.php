<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('StoreAppController', 'Controller');

class DealsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'NZGateway');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common', 'Dateform');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
        $roleId = $this->Session->read('Auth.User.role_id');
        if ($roleId) {
            if ($roleId != 4) {
                $this->InvalidLogin($roleId);
            }
        }
        $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
        $encrypted_merchantId = $this->Encryption->encode($this->Session->read('merchant_id'));
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
    }

    public function index() {
        $this->layout = $this->store_inner_pages;
        $this->loadModel('Coupon');
        $this->loadModel('ItemOffer');
        $this->loadModel('Offer');
        $this->loadModel('OfferDetail');
        $store_id = $this->Session->read('store_id');
        $date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $couponsData = $this->Coupon->find('all', array('conditions' => array('Coupon.store_id' => $store_id, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0, 'Coupon.number_can_use > Coupon.used_count', 'Coupon.start_date <= ' => $date, 'Coupon.end_date >= ' => $date), 'fields' => array('id', 'name', 'coupon_code', 'discount', 'discount_type', 'image', 'store_id', 'merchant_id')));
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
        $itemOfferData = $this->ItemOffer->find('all', array('conditions' => array('ItemOffer.store_id' => $store_id, 'ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.start_date <= ' => $date, 'ItemOffer.end_date >= ' => $date)));
        //prx($itemOfferData);
//        $this->OfferDetail->bindModel(
//                array(
//            'belongsTo' => array(
//                'Item' => array(
//                    'className' => 'Item',
//                    'foreignKey' => 'offerItemId',
//                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
//                    'fields' => array('id', 'name'),
//                ),
//                'Size' => array(
//                    'className' => 'Size',
//                    'foreignKey' => 'offerSize',
//                    'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
//                    'fields' => array('size')
//                )
//            ),
//                ), false
//        );
        $this->Offer->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('name', 'image', 'category_id', 'id', 'store_id', 'merchant_id'),
                    'type' => 'INNER'
                ),
//                'Size' => array(
//                    'className' => 'Size',
//                    'foreignKey' => 'size_id',
//                    'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
//                    'fields' => array('size')
//                )
            ),
//            'hasMany' => array(
//                'OfferDetail' => array(
//                    'className' => 'OfferDetail',
//                    'foreignKey' => 'offer_id',
//                    'conditions' => array('OfferDetail.is_deleted' => 0, 'OfferDetail.is_active' => 1),
//                    'fields' => array('id','offerItemId','offerSize','quantity','discountAmt')
//                )
//            )
                ), false
        );
        $promotionalOfferData = $this->Offer->find('all', array('recursive' => 2, 'conditions' => array('Offer.store_id' => $store_id, 'Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'OR' => array(array('Offer.offer_start_date <= ' => $date, 'Offer.offer_end_date >= ' => $date), array('Offer.offer_start_date' => NULL, 'Offer.offer_end_date' => NULL))), 'fields' => array('item_id', 'description', 'offerImage')));
        $userId = AuthComponent::User('id');
        $this->loadModel('UserCoupon');
        $couponIdList = $this->UserCoupon->find('list', array('conditions' => array('store_id' => $store_id, 'user_id' => $userId, 'is_active' => 1, 'is_deleted' => 0), 'fields' => array('coupon_id')));
        //prx($couponIdList);

        $this->set(compact('couponsData', 'itemOfferData', 'promotionalOfferData', 'couponIdList', 'userId'));
    }

    public function addCoupon() {
        $this->autoRender = false;
        $userId = AuthComponent::User('id');
        if ($this->request->is('ajax') && !empty($this->request->data['coupon_id']) && !empty($userId) && !empty($this->request->data['coupon_code'])) {
            $this->loadModel('UserCoupon');
            $coupon_id = $this->Encryption->decode($this->request->data['coupon_id']);
            $coupon_code = $this->request->data['coupon_code'];
            $store_id = $this->Session->read('store_id');
            $count = $this->UserCoupon->find('count', array('conditions' => array('user_id' => $userId, 'coupon_id' => $coupon_id, 'store_id' => $store_id)));
            if ($count == 0) {
                $data['user_id'] = $userId;
                $data['store_id'] = $store_id;
                $data['coupon_id'] = $coupon_id;
                $data['coupon_code'] = $coupon_code;
                $data['merchant_id'] = $this->Session->read('merchant_id');
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

    public function deals() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('post', 'put'))) {
            if (!empty($this->request->data['StoreDeals']['store_id'])) {
                $merchantId = $this->Session->read('merchantId');
                $storeId = $this->request->data['StoreDeals']['store_id'];
            } else {
                $storeId = $this->Session->read('admin_store_id');
                $merchantId = $this->Session->read('admin_merchant_id');
            }
            $this->loadModel('StoreDeals');
            $storeDealData = $this->StoreDeals->findByStoreId($storeId, array('id'));
            if (!empty($storeDealData)) {
                $this->request->data['StoreDeals']['id'] = $storeDealData['StoreDeals']['id'];
            }
            $this->request->data['StoreDeals']['store_id'] = $storeId;
            $this->request->data['StoreDeals']['merchant_id'] = $merchantId;
            $this->request->data = $this->Common->trimValue($this->request->data);
            if ($this->request->data['StoreDeals']['icon_image']['error'] == 0) {
                $responseIcon = $this->Common->uploadMenuItemImages($this->request->data['StoreDeals']['icon_image'], '/StoreDeals-IconImage/', $storeId);
            } elseif ($this->request->data['StoreDeals']['icon_image']['error'] == 4) {
                $responseIcon['status'] = true;
                $responseIcon['imagename'] = '';
                unset($this->request->data['StoreDeals']['icon_image']);
            }
            if ($responseIcon['imagename']) {
                $this->request->data['StoreDeals']['icon_image'] = $responseIcon['imagename'];
            }
            if ($this->request->data['StoreDeals']['background_image']['error'] == 0) {
                $responseBg = $this->Common->uploadMenuItemImages($this->request->data['StoreDeals']['background_image'], '/StoreDeals-BgImage/', $storeId);
            } elseif ($this->request->data['StoreDeals']['background_image']['error'] == 4) {
                $responseBg['status'] = true;
                $responseBg['imagename'] = '';
                unset($this->request->data['StoreDeals']['background_image']);
            }
            if ($responseBg['imagename']) {
                $this->request->data['StoreDeals']['background_image'] = $responseBg['imagename'];
            }
            $msg = '';
            if (!$responseIcon['status']) {
                $msg.=$response['errmsg'];
            }
            if (!$responseBg['status']) {
                $msg.='<br>' . $response['errmsg'];
            }
            $this->StoreDeals->save($this->request->data);
            if (!empty($msg)) {
                $this->Session->setFlash(__($msg), 'alert_failed');
            } else {
                $this->Session->setFlash(__("Save Successfully."), 'alert_success', array(), 'form1');
            }
            $this->redirect($this->referer());
        }
    }

    public function deleteStoreDealImage($EncryptStoreDealID = null, $imageType = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['StoreDeals']['store_id'] = $this->Session->read('admin_store_id');
        $data['StoreDeals']['id'] = $this->Encryption->decode($EncryptStoreDealID);
        if ($imageType == 'IconImage') {
            $data['StoreDeals']['icon_image'] = '';
        } elseif ($imageType == 'BgImage') {
            $data['StoreDeals']['background_image'] = '';
        }
        $this->loadModel('StoreDeals');
        if ($this->StoreDeals->save($data)) {
            $this->Session->setFlash(__("Image deleted successfully."), 'alert_success', array(), 'form1');
        } else {
            $this->Session->setFlash(__("Some problem occured."), 'alert_failed', array(), 'form1');
        }
        $this->redirect($this->referer());
    }

    public function getDealForm() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is('ajax') && $this->request->data['storeId']) {
            $this->Session->write('deal_store_id', $this->request->data['storeId']);
            $this->loadModel('StoreDeals');
            $storeDealData = $this->StoreDeals->findByStoreId($this->request->data['storeId']);
            $this->set('storeDealData', $storeDealData);
            $this->render('/Elements/deals/deal_form');
        }else{
            $this->Session->delete('deal_store_id');
        }
    }

}
