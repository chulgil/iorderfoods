<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('StoreAppController', 'Controller');

class FeaturesController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'NZGateway');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common', 'Dateform');
    public $uses = array('StoreFeaturedSection');
    public $layout = "admin_dashboard";

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function index() {
        $storeId = $this->Session->read('admin_store_id');
        $list = $this->StoreFeaturedSection->find('all', array('conditions' => array('store_id' => $storeId), 'order' => array('position' => 'ASC')));
        if (empty($list)) {
            $this->_addDefaultFeaturedList($storeId);
        }
        $this->set(compact('list'));
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
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $featuredID = $this->Encryption->decode($EncryptFeaturedID);
        $this->loadModel('Size');
        $featuredDetail = $this->StoreFeaturedSection->findById($featuredID, array('id', 'featured_name', 'is_active', 'image', 'background_image'));
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['StoreFeaturedSection']['id'] = $featuredID;
            $conditions = array('LOWER(featured_name)' => strtolower($this->request->data['StoreFeaturedSection']['featured_name']), 'id !=' => $featuredID, 'store_id' => $storeId, 'merchant_id' => $merchantId, 'is_deleted' => 0);
            $isUniqueName = $this->StoreFeaturedSection->find('count', array('conditions' => $conditions));
            if ($isUniqueName == 0) {
                if ($this->request->data['StoreFeaturedSection']['image']['error'] == 0) {
                    $responseIcon = $this->Common->uploadMenuItemImages($this->request->data['StoreFeaturedSection']['image'], '/FeatureSection-IconImage/', $storeId);
                } elseif ($this->request->data['StoreFeaturedSection']['image']['error'] == 4) {
                    $responseIcon['status'] = true;
                    $responseIcon['imagename'] = '';
                    unset($this->request->data['StoreFeaturedSection']['image']);
                }
                if ($responseIcon['imagename']) {
                    $this->request->data['StoreFeaturedSection']['image'] = $responseIcon['imagename'];
                }
                if ($this->request->data['StoreFeaturedSection']['background_image']['error'] == 0) {
                    $responseBg = $this->Common->uploadMenuItemImages($this->request->data['StoreFeaturedSection']['background_image'], '/FeatureSection-BgImage/', $storeId);
                } elseif ($this->request->data['StoreFeaturedSection']['background_image']['error'] == 4) {
                    $responseBg['status'] = true;
                    $responseBg['imagename'] = '';
                    unset($this->request->data['StoreFeaturedSection']['background_image']);
                }
                if ($responseBg['imagename']) {
                    $this->request->data['StoreFeaturedSection']['background_image'] = $responseBg['imagename'];
                }
                $msg = '';
                if (!$responseIcon['status']) {
                    $msg.=$response['errmsg'];
                }
                if (!$responseBg['status']) {
                    $msg.='<br>' . $response['errmsg'];
                }
                $this->StoreFeaturedSection->save($this->request->data);
                if (!empty($msg)) {
                    $this->Session->setFlash(__($msg), 'alert_failed');
                } else {
                    $this->Session->setFlash(__("Updated Successfully."), 'alert_success');
                }
                $this->redirect($this->referer());
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
        $this->paginate = array('recursive' => 2, 'conditions' => array('FeaturedItem.store_featured_section_id' => $featuredID, 'FeaturedItem.is_active' => 1, 'FeaturedItem.is_deleted' => 0), 'fields' => array('FeaturedItem.id', 'FeaturedItem.store_featured_section_id', 'FeaturedItem.item_id', 'FeaturedItem.position'), 'order' => array('FeaturedItem.position' => 'ASC'));
        $featuredItemDetail = $this->paginate('FeaturedItem');
        $this->set('list', $featuredItemDetail);
        $this->set('featuredID', $featuredID);
    }

    /* ------------------------------------------------
      Function name:deleteCouponPhoto()
      Description:Delete coupon Photo
      created:8/11/2016
      ----------------------------------------------------- */

    public function deleteFeaturedImage($EncryptFeaturedID = null, $imageType = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['StoreFeaturedSection']['store_id'] = $this->Session->read('admin_store_id');
        $data['StoreFeaturedSection']['id'] = $this->Encryption->decode($EncryptFeaturedID);
        if ($imageType == 'IconImage') {
            $data['StoreFeaturedSection']['image'] = '';
        } elseif ($imageType == 'BgImage') {
            $data['StoreFeaturedSection']['background_image'] = '';
        }
        if ($this->StoreFeaturedSection->save($data)) {
            $this->Session->setFlash(__("Feature Image deleted"), 'alert_success');
            $this->redirect(array('controller' => 'features', 'action' => 'edit_features', $EncryptFeaturedID));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'features', 'action' => 'edit_features', $EncryptFeaturedID));
        }
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
        $storeID = $this->Session->read('admin_store_id');
        $value = "";
        $criteria = "Item.store_id =$storeID AND Item.is_deleted=0 AND Item.is_active=1";
        if ($this->Session->read('FeatureItemSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('FeatureItemSearchData'), true);
        } else {
            $this->Session->delete('FeatureItemSearchData');
        }
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
        $list = $this->StoreFeaturedSection->find('all', array('order' => array('position' => 'ASC'), 'conditions' => array('is_deleted' => 0, 'is_active' => 1, 'store_id' => $storeID), 'fields' => array('id', 'featured_name')));
        $this->set('sfList', $list);
    }

    public function ajaxfeatureUpdate() {
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'post')) && $this->request->data['formData']) {
            parse_str($this->request->data['formData'], $data);
            if (!empty($data['data']['store_feature_section_id'])) {
                asort($data['data']['featured_check']);
                $featuredID = $data['data']['store_feature_section_id'];
                if (!empty($featuredID) && !empty($data['data']['featured_check'])) {
                    foreach ($data['data']['featured_check'] as $key => $fData) {
                        $status = $fData;
                        $itemID = $key;
                        if (!empty($itemID) && !empty($featuredID)) {
                            $this->loadModel('FeaturedItem');
//                            $this->loadModel('Item');
//                            $storeID = $this->Session->read('admin_store_id');
//                            $this->FeaturedItem->bindModel(
//                                    array(
//                                'belongsTo' => array(
//                                    'Item' => array(
//                                        'className' => 'Item',
//                                        'foreignKey' => 'item_id',
//                                        'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
//                                        'fields' => array('id', 'name', 'category_id'),
//                                        'type' => 'INNER'
//                                    )
//                                )
//                                    ), false
//                            );
//                            $this->Item->bindModel(
//                                    array(
//                                'belongsTo' => array(
//                                    'Category' => array(
//                                        'className' => 'Category',
//                                        'foreignKey' => 'category_id',
//                                        'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
//                                        'fields' => array('id', 'name'),
//                                        'type' => 'INNER'
//                                    )
//                                )
//                                    ), false
//                            );
//                            if ($status == 1) {
//                                $totalFeaturedItemCount = $this->FeaturedItem->find('count', array('conditions' => array('store_featured_section_id' => $featuredID, 'FeaturedItem.store_id' => $storeID, 'FeaturedItem.is_deleted' => 0, 'FeaturedItem.is_active' => 1)));
//                                if ($totalFeaturedItemCount > 4) {
//                                    $response['status'] = 'Error';
//                                    $response['msg'] = 'You can add only 4 items per list.';
//                                    return json_encode($response);
//                                }
//                            }
                            $count = $this->FeaturedItem->find('count', array('conditions' => array('FeaturedItem.item_id' => $itemID, 'store_featured_section_id' => $featuredID, 'FeaturedItem.is_deleted' => 0)));
                            if ($count == 0) {
                                $data['FeaturedItem']['store_id'] = $this->Session->read('admin_store_id');
                                $data['FeaturedItem']['merchant_id'] = $this->Session->read('admin_merchant_id');
                                $data['FeaturedItem']['item_id'] = $itemID;
                                $data['FeaturedItem']['store_featured_section_id'] = $featuredID;
                                $data['FeaturedItem']['is_active'] = $status;
                                $this->FeaturedItem->create();
                                $this->FeaturedItem->save($data);
                            } else {
                                $flag = $this->FeaturedItem->updateAll(array('is_active' => $status), array('item_id' => $itemID, 'store_featured_section_id' => $featuredID));
                            }
                            $response['status'] = 'Success';
                            $response['msg'] = 'Action perform successfully.';
                        } else {
                            $response['status'] = 'Error';
                            $response['msg'] = 'Something went wrong!';
                        }
                    }
                }
            } else {
                $response['status'] = 'Error';
                $response['msg'] = 'No item selected.';
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
        $data['FeaturedItem']['store_id'] = $this->Session->read('admin_store_id');
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
            $this->loadModel('Category');
            $store_id = $this->Session->read('admin_store_id');
            $categoryList = $this->Category->find('list', array('fields' => array('id', 'name'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0, 'store_id' => $store_id)));
            $viewObject = new View($this, false);
            echo $viewObject->Form->input('Category.category_id', array('type' => 'select', 'options' => @$categoryList, 'class' => 'inbox', 'label' => false, 'div' => false, 'empty' => 'Select Category'));
        }
    }

    public function getItemByCategoryId() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'post')) && !empty($this->request->data['categoryId']) && !empty($this->request->data['featuredSectionId'])) {
            $this->loadModel('Item');
            $this->loadModel('FeaturedItem');
            $store_id = $this->Session->read('admin_store_id');
            $itemList = $this->Item->find('all', array('fields' => array('id', 'name', 'is_seasonal_item', 'start_date', 'end_date'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0, 'store_id' => $store_id, 'category_id' => $this->request->data['categoryId'])));
            $featuredSectionId = $this->Encryption->decode($this->request->data['featuredSectionId']);
            $featuredItemdata = $this->FeaturedItem->find('list', array('fields' => array('item_id'), 'conditions' => array('store_featured_section_id' => $featuredSectionId, 'store_id' => $store_id, 'is_active' => 1, 'is_deleted' => 0)));
            $html = '';
            if (!empty($itemList)) {
                $viewObject = new View($this, false);
                $html.= $viewObject->Form->input('store_feature_section_id', array('type' => 'hidden', 'value' => $featuredSectionId));
                $currentDate = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
                foreach ($itemList as $sfl) {
                    if ($sfl['Item']['is_seasonal_item'] == 1) {
                        if (strtotime($sfl['Item']['end_date']) < strtotime($currentDate)) {
                            continue;
                        }
                    }
                    if (!empty($featuredItemdata) && in_array($sfl['Item']['id'], $featuredItemdata)) {
                        $check = true;
                    } else {
                        $check = false;
                    }

                    $html.= $viewObject->Form->input('featured_check', array(
                        'type' => 'checkbox',
                        'label' => false,
                        'before' => '<label>' . $sfl['Item']['name'],
                        'after' => '</label> &nbsp;&nbsp;&nbsp;',
                        'data-id' => $this->request->data['featuredSectionId'],
                        'data-itemid' => $this->Encryption->encode($sfl['Item']['id']),
                        'name' => "data[featured_check][" . $sfl['Item']['id'] . "]",
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
