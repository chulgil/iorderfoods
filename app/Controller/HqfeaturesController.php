<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('HqAppController', 'Controller');

class HqfeaturesController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'NZGateway');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common', 'Dateform');
    public $uses = array('StoreFeaturedSection');
    public $layout = "hq_dashboard";

    public function beforeFilter() {
        parent::beforeFilter();
        //$this->Auth->allow();
    }

    public function index($clearAction = null) {
        if ($this->Session->read('HqFeaturedSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqFeaturedSearchData'), true);
        } else {
            $this->Session->delete('HqFeaturedSearchData');
        }
        if (!empty($this->request->data)) {
            $this->Session->write('HqFeaturedSearchData', json_encode($this->request->data));
            $store_id = $this->request->data['Store']['store_id'];
            $list = $this->StoreFeaturedSection->find('all', array('conditions' => array('store_id' => $store_id), 'order' => array('position' => 'ASC')));
            if (empty($list)) {
                $this->_addDefaultFeaturedList($store_id);
            }
            $this->set(compact('list'));
        }
    }

    /* ------------------------------------------------
      Function name:activateFeature()
      Description:Active/deactive Feature
      created:04/11/2016
      ----------------------------------------------------- */

    public function activateFeature($EncryptFeaturedID = null, $status = 0) {
        $this->autoRender = false;
        $data['StoreFeaturedSection']['id'] = $this->Encryption->decode($EncryptFeaturedID);
        $data['StoreFeaturedSection']['is_active'] = $status;
        if ($this->StoreFeaturedSection->save($data)) {
            if ($status) {
                $SuccessMsg = "Featured Section Activated";
            } else {
                $SuccessMsg = "Featured Section Deactivated";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
    }

    /* ------------------------------------------------
      Function name:updateFeaturePosition()
      Description: Update position
      created Date:04/11/2016
      created By:Vikas Singh
      ----------------------------------------------------- */

    public function updateFeaturePosition() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            foreach ($_GET as $key => $val) {
                $this->StoreFeaturedSection->updateAll(array('StoreFeaturedSection.position' => $val), array('StoreFeaturedSection.id' => $this->Encryption->decode($key)));
            }
        }
    }

    /* ------------------------------------------------
      Function name:editSize()
      Description:Edit Category Size
      created:6/8/2015
      ----------------------------------------------------- */

    public function edit_features($EncryptFeaturedID = null) {
        $merchantId = $this->Session->read('merchantId');
        $featuredID = $this->Encryption->decode($EncryptFeaturedID);
        $this->loadModel('Size');
        $featuredDetail = $this->StoreFeaturedSection->findById($featuredID, array('id', 'featured_name', 'is_active'));
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['StoreFeaturedSection']['id'] = $featuredID;
            $conditions = array('LOWER(featured_name)' => strtolower($this->request->data['StoreFeaturedSection']['featured_name']), 'id !=' => $featuredID, 'merchant_id' => $merchantId, 'is_deleted' => 0);
            $isUniqueName = $this->StoreFeaturedSection->find('count', array('conditions' => $conditions));
            if ($isUniqueName == 0) {
                $this->StoreFeaturedSection->save($this->request->data);
                $this->Session->setFlash(__("Updated Successfully ."), 'alert_success');
                $this->redirect(array('controller' => 'hqfeatures', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Feature name already exists"), 'alert_failed');
            }
        }
        $this->request->data = $featuredDetail;
        $this->loadModel('FeaturedItem');
        $this->loadModel('Item');
        $this->FeaturedItem->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name', 'category_id'),
                    'type' => 'INNER'
                )
            )
                ), false
        );
        $this->Item->bindModel(
                array(
            'belongsTo' => array(
                'Category' => array(
                    'className' => 'Category',
                    'foreignKey' => 'category_id',
                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                    'fields' => array('id', 'name'),
                    'type' => 'INNER'
                )
            )
                ), false
        );
        $this->paginate = array('recursive' => 2, 'conditions' => array('FeaturedItem.store_featured_section_id' => $featuredID, 'FeaturedItem.is_active' => 1, 'FeaturedItem.is_deleted' => 0), 'fields' => array('FeaturedItem.id', 'FeaturedItem.item_id', 'FeaturedItem.position'), 'order' => array('FeaturedItem.position' => 'ASC'));
        $featuredItemDetail = $this->paginate('FeaturedItem');
        $this->set('list', $featuredItemDetail);
        $this->set('featuredID', $featuredID);
    }

    /* ------------------------------------------------
      Function name:updateFeatureItemPosition()
      Description: Update position
      created Date:17/11/2016
      created By:Vikas Singh
      ----------------------------------------------------- */

    public function updateFeatureItemPosition() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            foreach ($_GET as $key => $val) {
                $this->loadModel('FeaturedItem');
                $this->FeaturedItem->updateAll(array('FeaturedItem.position' => $val), array('FeaturedItem.id' => $this->Encryption->decode($key)));
            }
        }
    }

    public function featuredItemList($clearAction = null) {
        if ($this->Session->read('HqFeatureItemSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqFeatureItemSearchData'), true);
        } else {
            $this->Session->delete('HqFeatureItemSearchData');
        }
        if (!empty($this->request->data['Item']['store_id'])) {
            $merchantId = $this->Session->read('merchantId');
            $value = "";
            $storeID = $this->request->data['Item']['store_id'];
            $criteria = "Item.store_id =$storeID AND Item.merchant_id=$merchantId AND Item.is_deleted=0 AND Item.is_active=1";
            if (!empty($this->request->data)) {
                $this->Session->write('FeatureItemSearchData', json_encode($this->request->data));
                if (!empty($this->request->data['Item']['keyword'])) {
                    $value = trim($this->request->data['Item']['keyword']);
                    $criteria .= " AND (Item.name LIKE '%" . $value . "%' OR Item.description LIKE '%" . $value . "%' OR Category.name LIKE '%" . $value . "%')";
                }
                if (!empty($this->request->data['Item']['category_id'])) {
                    $categoryID = trim($this->request->data['Item']['category_id']);
                    $criteria .= " AND (Category.id =$categoryID)";
                }
            }
            $this->loadModel('Item');
            $this->Item->bindModel(
                    array(
                'belongsTo' => array(
                    'Category' => array(
                        'className' => 'Category',
                        'foreignKey' => 'category_id',
                        'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                        'fields' => array('id', 'name'),
                        'type' => 'INNER'
                    )
                )
                    ), false
            );
            $this->paginate = array('conditions' => array($criteria), 'fields' => array('Item.id', 'Item.name', 'Item.description', 'Item.category_id', 'Category.name'));
            $itemdetail = $this->paginate('Item');
            $this->set('list', $itemdetail);
            $this->loadModel('Category');
            $categoryList = $this->Category->getCategoryList($storeID);
            $this->set('categoryList', $categoryList);
            $this->set('keyword', $value);
            $list = $this->StoreFeaturedSection->find('all', array('order' => array('position' => 'ASC'), 'conditions' => array('is_deleted' => 0, 'is_active' => 1, 'store_id' => $storeID, 'merchant_id' => $merchantId), 'fields' => array('id', 'featured_name')));
            $this->set('sfList', $list);
        }
    }

    public function ajaxfeatureUpdate() {
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'post')) && $this->request->data['featured_id'] && $this->request->data['item_id']) {
            $featuredID = $this->Encryption->decode($this->request->data['featured_id']);
            $itemID = $this->Encryption->decode($this->request->data['item_id']);
            $status = $this->request->data['status'];
            if ($status) {
                $msg = 'Added to featured section.';
            } else {
                $msg = 'Removed from featured section.';
            }
            if (!empty($itemID) && !empty($featuredID)) {
                $this->loadModel('FeaturedItem');
                $count = $this->FeaturedItem->find('count', array('conditions' => array('item_id' => $itemID, 'store_featured_section_id' => $featuredID, 'is_deleted' => 0)));
                if ($count == 0) {
                    $sfsData = $this->StoreFeaturedSection->find('first', array('conditions' => array('is_deleted' => 0, 'is_active' => 1, 'id' => $featuredID), 'fields' => array('store_id')));
                    $data['FeaturedItem']['store_id'] = $sfsData['StoreFeaturedSection']['store_id'];
                    $data['FeaturedItem']['merchant_id'] = $this->Session->read('merchantId');
                    $data['FeaturedItem']['item_id'] = $itemID;
                    $data['FeaturedItem']['store_featured_section_id'] = $featuredID;
                    $data['FeaturedItem']['is_active'] = $status;
                    $this->FeaturedItem->create();
                    if ($this->FeaturedItem->save($data)) {
                        $response['status'] = 'Success';
                        $response['msg'] = $msg;
                    } else {
                        $response['status'] = 'Error';
                        $response['msg'] = 'Something went wrong!';
                    }
                } else {
                    $flag = $this->FeaturedItem->updateAll(array('is_active' => $status), array('item_id' => $itemID, 'store_featured_section_id' => $featuredID));
                    if ($flag) {
                        $response['status'] = 'Success';
                        $response['msg'] = $msg;
                    } else {
                        $response['status'] = 'Error';
                        $response['msg'] = 'Something went wrong!';
                    }
                }
            } else {
                $response['status'] = 'Error';
                $response['msg'] = 'Something went wrong!';
            }
        } else {
            $response['status'] = 'Error';
            $response['msg'] = 'Something went wrong!';
        }
        return json_encode($response);
    }

    /* ------------------------------------------------
      Function name:deleteFeaturedItem()
      Description:Delete Featured item
      created:17/11/2016
      ----------------------------------------------------- */

    public function deleteFeaturedItem($EncryptFeatureItemID = null) {
        $this->autoRender = false;
        $data['FeaturedItem']['id'] = $this->Encryption->decode($EncryptFeatureItemID);
        $data['FeaturedItem']['is_deleted'] = 1;
        $this->loadModel('FeaturedItem');
        if ($this->FeaturedItem->save($data)) {
            $this->Session->setFlash(__("Feature item deleted."), 'alert_success');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
    }

    public function addFeaturedItem() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'post')) && !empty($this->request->data['featuredSectionId'])) {
            $featuredId = $this->Encryption->decode($this->request->data['featuredSectionId']);
            $merchantId = $this->Session->read('merchantId');
            $sfsData = $this->StoreFeaturedSection->find('first', array('conditions' => array('is_deleted' => 0, 'is_active' => 1, 'merchant_id' => $merchantId, 'id' => $featuredId), 'fields' => array('store_id')));
            $this->loadModel('Category');
            $store_id = $sfsData['StoreFeaturedSection']['store_id'];
            $categoryList = $this->Category->find('list', array('fields' => array('id', 'name'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0, 'store_id' => $store_id)));
            $viewObject = new View($this, false);
            echo $viewObject->Form->input('Category.category_id', array('type' => 'select', 'options' => @$categoryList, 'class' => 'inbox', 'label' => false, 'div' => false, 'empty' => 'Select Category'));
        }
    }

    public function getItemByCategoryId() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'post')) && !empty($this->request->data['categoryId']) && !empty($this->request->data['featuredSectionId'])) {
            $merchantId = $this->Session->read('merchantId');
            $this->loadModel('Item');
            $this->loadModel('FeaturedItem');
            $featuredSectionId = $this->Encryption->decode($this->request->data['featuredSectionId']);
            $sfsData = $this->StoreFeaturedSection->find('first', array('conditions' => array('is_deleted' => 0, 'is_active' => 1, 'merchant_id' => $merchantId, 'id' => $featuredSectionId), 'fields' => array('store_id')));
            $store_id = $sfsData['StoreFeaturedSection']['store_id'];
            $itemList = $this->Item->find('all', array('fields' => array('id', 'name'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0, 'store_id' => $store_id, 'category_id' => $this->request->data['categoryId'])));
            $featuredItemdata = $this->FeaturedItem->find('list', array('fields' => array('item_id'), 'conditions' => array('store_featured_section_id' => $featuredSectionId, 'store_id' => $store_id, 'is_active' => 1, 'is_deleted' => 0)));
            $html = '';
            if (!empty($itemList)) {
                foreach ($itemList as $sfl) {
                    if (!empty($featuredItemdata) && in_array($sfl['Item']['id'], $featuredItemdata)) {
                        $check = true;
                    } else {
                        $check = false;
                    }
                    $viewObject = new View($this, false);
                    $html.= $viewObject->Form->input('featured_check', array(
                        'type' => 'checkbox',
                        'label' => false,
                        'before' => '<label>' . $sfl['Item']['name'],
                        'after' => '</label> &nbsp;&nbsp;&nbsp;',
                        'data-id' => $this->request->data['featuredSectionId'],
                        'data-itemid' => $this->Encryption->encode($sfl['Item']['id']),
                        'checked' => $check,
                        'div' => false
                    ));
                }
            } else {
                $html = "No item found.";
            }
            echo $html;
        }
    }

}
