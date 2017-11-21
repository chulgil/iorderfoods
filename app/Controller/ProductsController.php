<?php

App::uses('StoreAppController', 'Controller');

class ProductsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'NZGateway');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common', 'Dateform', 'Session');

    public function beforeFilter() {
        //pr($this->Session->read());
        parent::beforeFilter();
        $this->Auth->allow('checkPreference', 'checkOrderType', 'checkdeliveryadd', 'subPreferencePrice', 'getcartCount', 'fetchProductSize', 'items', 'checkOrderTime', 'addTip', 'addtosession', 'getitemdata', 'removefrmSession', 'ajaxFetchSubPrefrenceSizePrice');
//        $roleId = $this->Session->read('Auth.User.role_id');
//        if ($roleId) {
//            if ($roleId != 4) {
//                $this->InvalidLogin($roleId);
//            }
//        }
        $storeId = $this->Session->read('store_id');
        $encrypted_storeId = $this->Encryption->encode($storeId);
        $encrypted_merchantId = $this->Encryption->encode($this->Session->read('merchant_id'));
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));


        $this->loadModel('StoreSetting');
        $storeSetting = $this->StoreSetting->findByStoreId($storeId);
        $this->set('storeSetting', $storeSetting);
    }

    /* ------------------------------------------------
      Function name:items()
      Description:This will fetch the category of the menus from the table category
      created:22/7/2015
      ----------------------------------------------------- */

    public function items($encrypted_storeId = null, $encrypted_merchantId = null, $orderId = null, $encrypted_itemId = null, $encrypted_catId = null) {
        //pr($this->Session->read('cart'));
        $this->Session->delete('orderOverview');
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $decrypt_itemId = '';
        $decrypt_catId = '';
        if ($encrypted_itemId) {
            $decrypt_itemId = $this->Encryption->decode($encrypted_itemId);
        }
        if ($encrypted_catId) {
            $decrypt_catId = $this->Encryption->decode($encrypted_catId);
        }
        $this->loadModel('Category');
        $this->loadModel('Item');
        $this->loadModel('ItemPrice');
        $this->loadModel('Type');

        $cartTocheck = $this->Session->read('cart');
        if (empty($cartTocheck)) {
            $this->Session->delete('CartOfferM');
            $this->Session->delete('CartOffer');
            if (DESIGN != 4) {
                $this->Session->delete('Order');
            }
        }
        //echo "<pre>";
        //pr($this->Session->read('cart'));
        //$this->cart();
        $this->ItemPrice->bindModel(
                array('belongsTo' => array(
                        'Size' => array(
                            'className' => 'Size',
                            'foreignKey' => 'size_id',
                            'conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $decrypt_storeId),
                            'fields' => array('id', 'size', 'category_id'),
                            'order' => array('Size.id ASC')
                        ),
                        'StoreTax' => array(
                            'className' => 'StoreTax',
                            'foreignKey' => 'store_tax_id',
                            'fields' => array('id', 'tax_name', 'tax_value'),
                            'conditions' => array('StoreTax.is_active' => 1, 'StoreTax.is_deleted' => 0, 'StoreTax.store_id' => $decrypt_storeId)
                        )
        )));


        $this->Item->bindModel(
                array('hasMany' => array(
                        'ItemPrice' => array(
                            'className' => 'ItemPrice',
                            'foreignKey' => 'item_id',
                            'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $decrypt_storeId),
                            'fields' => array('id', 'item_id', 'price', 'store_tax_id', 'size_id', 'position'),
                            'order' => array('ItemPrice.position ASC')
                        )
                    )
        ));
        $nowDate = date("Y-m-d H:i:s");
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', $nowDate))));


        $this->Category->bindModel(
                array('hasMany' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'category_id',
                            'fields' => array('id', 'name', 'category_id', 'start_date', 'end_date', 'image', 'description', 'is_seasonal_item', 'position', 'mandatory_item_units', 'is_deliverable'),
                            'conditions' => array('Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.store_id' => $decrypt_storeId),
                            'order' => array('position' => 'asc')
                        )
                    )
        ));
        $this->Type->unBindModel(array('hasMany' => array('ItemType')));
        $categoryList = $this->Category->findCategotyList($decrypt_storeId, $decrypt_merchantId);         // It will find the list of categories of the menus
        //prx($categoryList);
        foreach ($categoryList as $ckey => $cvalue) {
            if (!empty($cvalue['Category']['days']) && !empty($cvalue['Category']['is_meal'])) {
                $days = explode(',', $cvalue['Category']['days']);
                $day_number = date('N', strtotime($current_date));
                //pr($days);
                //pr($day_number);
                if (!in_array($day_number, $days)) {
                    //prx($categoryList[$ckey]);
                    unset($categoryList[$ckey]);
                    continue;
                }
            }
            if (empty($cvalue['Item'])) {
                unset($categoryList[$ckey]);
                continue;
            }
            if (!empty($cvalue['Item'])) {
                foreach ($cvalue['Item'] as $Ikey => $productInfo) {
                    if (!empty($productInfo['is_seasonal_item'])) {
                        if (!(strtotime($current_date) >= strtotime($productInfo['start_date']) && strtotime($current_date) <= strtotime($productInfo['end_date']))) {
                            unset($categoryList[$ckey]['Item'][$Ikey]);
                            continue;
                        }
                    }

                    if ($productInfo['ItemPrice']) {
                        $display_offer = "";
                        $i = 0;
                        foreach ($productInfo['ItemPrice'] as $checkSize) {
                            $default_price = '0.00';
                            if ($checkSize['size_id'] == 0) {
                                $querySize = $checkSize['size_id'];
                                $default_price = $checkSize['price'];
                                $intervalPrice = 0;
                                $intervalPrice = $this->getTimeIntervalPrice($checkSize['item_id'], $checkSize['size_id']);
                                if (!empty($intervalPrice['Interval']['IntervalDay']) && !empty($intervalPrice['IntervalPrice'])) {
                                    $default_price = $intervalPrice['IntervalPrice']['price'];
                                    $categoryList[$ckey]['Item'][$Ikey]['Interval']['name'] = $intervalPrice['Interval']['name'];
                                    $categoryList[$ckey]['Item'][$Ikey]['Interval']['id'] = $intervalPrice['Interval']['id'];
                                }
                                //$default_price = $checkSize['price'];
                                if (!empty($checkSize['StoreTax'])) {
                                    $productInfo['Item']['taxvalue'] = $checkSize['StoreTax']['tax_value'];
                                } else {
                                    $productInfo['Item']['taxvalue'] = '';
                                }

                                $categoryList[$ckey]['Item'][$Ikey]['applicablePrice'] = $default_price;
                            } else {
                                if (!empty($checkSize['Size'])) {
                                    $querySize = $checkSize['size_id'];
                                    if ($i == 0) { //enter first price of size
                                        $default_price = $checkSize['price'];
                                        $categoryList[$ckey]['Item'][$Ikey]['applicablePrice'] = $default_price;
                                        $intervalPrice = 0;
                                        $intervalPrice = $this->getTimeIntervalPrice($checkSize['item_id'], $checkSize['size_id']);
                                        if (!empty($intervalPrice['Interval']['IntervalDay']) && !empty($intervalPrice['IntervalPrice'])) {
                                            $default_price = $intervalPrice['IntervalPrice']['price'];
                                            $categoryList[$ckey]['Item'][$Ikey]['applicablePrice'] = $default_price;
                                            $categoryList[$ckey]['Item'][$Ikey]['Interval']['name'] = $intervalPrice['Interval']['name'];
                                            $categoryList[$ckey]['Item'][$Ikey]['Interval']['id'] = $intervalPrice['Interval']['id'];
                                        }
                                    }
                                    $i++;


                                    if (!empty($checkSize['StoreTax'])) {
                                        $productInfo['Item']['taxvalue'] = $checkSize['StoreTax']['tax_value'];
                                    } else {
                                        $productInfo['Item']['taxvalue'] = '';
                                    }
                                }
                            }


                            if (!empty($checkSize['item_id'])) {
                                $this->loadModel('Offer');
                                $this->loadModel('ItemOffer');
                                $this->Offer->bindModel(array('hasMany' => array('OfferDetail')));
                                $display_offer = $this->Offer->allOfferOnItem($checkSize['item_id'], $desc = 'description');
                                $itemOffer = $this->ItemOffer->allItemOfferOnItem($checkSize['item_id'], $current_date);
                            }
                            $categoryList[$ckey]['Item'][$Ikey]['offerDisplay'] = $display_offer;
                            $categoryList[$ckey]['Item'][$Ikey]['ItemOfferDisplay'] = $itemOffer;
                            if (!isset($categoryList[$ckey]['Item'][$Ikey]['applicablePrice'])) {
                                $categoryList[$ckey]['Item'][$Ikey]['applicablePrice'] = 0.00;
                            }
                        }
                    }
                }
            }
        }
        if ($categoryList) {
            $this->set(compact('orderId', 'categoryList', 'encrypted_storeId', 'encrypted_ merchantId', 'decrypt_storeId', 'display_offer', 'decrypt_itemId', 'decrypt_catId'));
        } else {
            $this->set(compact('orderId', 'encrypted_storeId', 'encrypted_merchantId', 'decrypt_storeId', 'display_offer', 'decrypt_itemId', 'decrypt_catId'));
        }
        $this->Session->delete('CartOffer');
        //$this->Session->delete('Order.subPreference');
        if ($this->Session->check('cart')) {
            $final_cart = $this->Session->read('cart');
            $this->set(compact('final_cart'));
        }
        $this->loadModel('DeliveryAddress');
        $userId = AuthComponent::User('id'); // Customer Id
        $roleId = AuthComponent::User('role_id');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $decrypt_storeId, $decrypt_merchantId); // It will call the function in the model to check the address either exist or not
        if (!$checkaddress) {
            $checkaddress = array();
        }
        $this->set(compact('checkaddress'));
    }

    public function position($position, $arrposition) {
        if (!in_array($position, $arrposition)) {
            return $position;
        } else {
            $position++;
            return $this->position($position, $arrposition);
        }
    }

    /* ------------------------------------------------
      Function name:fetchProduct()
      Description:It will fetch the item inofrmation under the particular caltegory
      created:22/7/2015
      ----------------------------------------------------- */

    public function fetchProduct() {
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $responsOAC = $this->orderAllowedCheck();
            $responsOACD = json_decode($responsOAC, true);
            if ($responsOACD["status"] == 'Error') {
                $this->autoRender = false;
                return $responsOAC;
            }
            $itemId = $_POST['item_id'];
            /* if order type is home delivery check item is deliverable or not. START */
            if (!empty($itemId) && !empty($_SESSION['Order']['order_type']) && $_SESSION['Order']['order_type'] == 3) {
                $productInfo = array();
                $this->loadModel('Item');
                $iData = $this->Item->findById($itemId, array('name', 'is_deliverable'));
                if (!empty($iData) && $iData['Item']['is_deliverable'] == 0) {
                    $this->autoRender = false;
                    $msg = $iData['Item']['name'] . " is non-deliverable.<br>Please change your order type or select other item.";
                    $response['status'] = 'Error';
                    $response['msg'] = $msg;
                    return json_encode($response);
//                    $nonDeliverable = true;
//                    $this->set(compact('productInfo', 'nonDeliverable'));
//                    $this->autoRender = false;
//                    $msg = $iData['Item']['name'] . " is non-deliverable.<br>Please change your order type or select other item.";
//                    $this->Session->delete('Itemcartdata');
//                    $this->Session->setFlash(__($msg), 'flash_error');
//                    $this->render('/Elements/design/oldlayout/product/fetch_product');
                }
            }
            /* if order type is home delivery check item is deliverable or not. END */
            $decrypt_storeId = $this->Session->read('store_id');
            $decrypt_merchantId = $this->Session->read('merchant_id');
            $categoryId = $_POST['categoryId'];
            $storeId = $_POST['storeId'];
            $this->Session->delete('FetchProductData');
            $this->Session->write('FetchProductData.itemId', $_POST['item_id']);
            $this->Session->write('FetchProductData.categoryId', $_POST['categoryId']);
            $this->Session->write('FetchProductData.storeId', $_POST['storeId']);
            $this->Session->write('FetchProductData.sizeType', @$_POST['sizeType']);
            $this->Session->delete('Order.Item');
            $this->Session->delete('Order.subPreference');
            $this->Session->delete('CartOffer');
            $this->Session->delete('OfferAddIndex');
            $this->Session->delete('Offer');
            $this->loadModel('Item');
            $this->loadModel('ItemPrice');
            $this->loadModel('ItemType');
            $this->loadModel('Category');
            $this->loadModel('AddonSize');
            $this->loadModel('Topping');
            $this->loadModel('StoreTax');
            $this->loadModel('SubPreference');
            $this->loadModel('Type');
            //$date = date('Y-m-d');
            $date = $this->Common->gettodayDate();
            if (isset($_POST['itemzero'])) {
                $this->set('itemzero', 0);
            }
            $this->Type->bindModel(
                    array('hasMany' => array(
                            'SubPreference' => array(
                                'className' => 'SubPreference',
                                'foreignKey' => 'type_id',
                                'order' => array('SubPreference.position ASC'),
                                'conditions' => array('SubPreference.is_active' => 1, 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $decrypt_storeId)
                            )
                        )
            ));


            $this->ItemType->bindModel(
                    array('belongsTo' => array(
                            'Type' => array(
                                'className' => 'Type',
                                'foreignKey' => 'type_id',
                                'conditions' => array('Type.is_active' => 1, 'Type.is_deleted' => 0, 'Type.store_id' => $decrypt_storeId)
                            )
            )));




            $this->ItemPrice->bindModel(
                    array('belongsTo' => array(
                            'Size' => array(
                                'className' => 'Size',
                                'foreignKey' => 'size_id',
                                'conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $decrypt_storeId),
                                'order' => array('Size.id ASC')
                            ),
                            'StoreTax' => array(
                                'className' => 'StoreTax',
                                'foreignKey' => 'store_tax_id',
                                'conditions' => array('StoreTax.is_active' => 1, 'StoreTax.is_deleted' => 0, 'StoreTax.store_id' => $decrypt_storeId)
                            )
            )));
            $this->Topping->bindModel(
                    array('hasMany' => array(
                            'Topping' => array(
                                'className' => 'Topping',
                                'foreignKey' => 'addon_id',
                                'order' => array('Topping.position ASC'),
                                'conditions' => array('Topping.item_id' => $itemId, 'Topping.is_addon_category' => 0, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $decrypt_storeId),
                                'Type' => 'INNER'
                            )
                        ),
                        'hasOne' => array(
                            'ItemDefaultTopping' => array(
                                'className' => 'ItemDefaultTopping',
                                'foreignKey' => 'topping_id',
                                'conditions' => array('ItemDefaultTopping.is_deleted' => 0)
                            )))
            );
            $this->Item->bindModel(
                    array('hasMany' => array(
                            'ItemType' => array(
                                'className' => 'ItemType',
                                'foreignKey' => 'item_id',
                                'order' => array('ItemType.position ASC'),
                                'conditions' => array('ItemType.is_active' => 1, 'ItemType.is_deleted' => 0, 'ItemType.store_id' => $decrypt_storeId)
                            ), 'ItemPrice' => array(
                                'className' => 'ItemPrice',
                                'foreignKey' => 'item_id',
                                'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $decrypt_storeId),
                                'order' => array('ItemPrice.position ASC')
                            ), 'Topping' => array(
                                'className' => 'Topping',
                                'foreignKey' => 'item_id',
                                'order' => array('Topping.position ASC'),
                                'conditions' => array('Topping.is_addon_category' => 1, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $decrypt_storeId),
                                'Type' => "INNER",
                            )
                        )
            ));
            $this->Type->unBindModel(array('hasMany' => array('ItemType')));
            $positionvalue = array();
            $itemTypearray = array();
            $productInfo = $this->Item->fetchItemDetail($itemId, $storeId);
            //pr($productInfo);die;
            $toppingSizes = $this->AddonSize->fetchAddonSize($storeId);
            $productInfoToppingarr = array();
            if (!empty($productInfo['Topping'])) {
                $i = 0;
                foreach ($productInfo['Topping'] as $top) {
                    if (!empty($top['Topping'])) {
                        $productInfoToppingarr[$i] = $top;
                        $i++;
                    }
                }
            }
            unset($productInfo['Topping']);
            $productInfo['Topping'] = $productInfoToppingarr;
            if ($productInfo) {
                $querySize = 0;
                $default_price = 0;
                $taxprice = 0;
                if ($productInfo['ItemPrice']) {
                    foreach ($productInfo['ItemPrice'] as $checkSize) {
                        if ($checkSize['size_id'] == 0) {
                            $querySize = $checkSize['size_id'];
                            $default_price = $checkSize['price'];

                            $intervalPrice = 0;
                            $intervalPrice = $this->getTimeIntervalPrice($checkSize['item_id'], $checkSize['size_id']);
                            if (!empty($intervalPrice['Interval']['IntervalDay']) && !empty($intervalPrice['IntervalPrice'])) {
                                $default_price = $intervalPrice['IntervalPrice']['price'];
                                $this->Session->write('Order.Item.interval_id', $intervalPrice['IntervalPrice']['interval_id']);
                            }
                            //$default_price = $checkSize['price'];
                            if (!empty($checkSize['StoreTax'])) {
                                $productInfo['Item']['taxvalue'] = $checkSize['StoreTax']['tax_value'];
                            } else {
                                $productInfo['Item']['taxvalue'] = '';
                            }
                            $this->Session->write('Order.Item.SizePrice', $default_price);
                        } else {
                            if (!empty($checkSize['Size'])) {
                                $querySize = $checkSize['size_id'];

                                $default_price = $checkSize['price'];

                                $intervalPrice = 0;
                                $intervalPrice = $this->getTimeIntervalPrice($checkSize['item_id'], $checkSize['size_id']);

                                if (!empty($intervalPrice['Interval']['IntervalDay']) && !empty($intervalPrice['IntervalPrice'])) {
                                    $default_price = $intervalPrice['IntervalPrice']['price'];
                                    $this->Session->write('Order.Item.interval_id', $intervalPrice['IntervalPrice']['interval_id']);
                                }

                                if (!empty($checkSize['StoreTax'])) {
                                    $productInfo['Item']['taxvalue'] = $checkSize['StoreTax']['tax_value'];
                                } else {
                                    $productInfo['Item']['taxvalue'] = '';
                                }
                                //$productInfo['Item']['taxvalue']=$checkSize['StoreTax']['tax_value'];

                                $this->Session->write('Order.Item.SizePrice', $default_price);
                                break;
                            }
                        }
                    }
                }

                if ($this->Session->check('Auth.User.id')) {
                    $itemisFree = $this->checkItemOffer($productInfo['Item']['id'], $this->Session->read('Auth.User.id'));
                } else {
                    $itemisFree = $this->checkItemOffer($productInfo['Item']['id'], null);
                }




                $itemId = $productInfo['Item']['id'];
                $itemName = $productInfo['Item']['name'];
                $categoryId = $productInfo['Item']['category_id'];
                $itemName = $productInfo['Item']['name'];
                $itemtaxvalue = $productInfo['Item']['taxvalue'];
                $deliver_check = $productInfo['Item']['is_deliverable'];
                $default_quantity = 1;
                $this->Session->write('Order.Item.quantity', $default_quantity);
                $this->Session->write('Order.Item.actual_price', $default_price);
                $this->Session->write('Order.Item.is_deliverable', $deliver_check);
                $this->Session->write('Order.Item.id', $itemId);
                $this->Session->write('Order.Item.name', $itemName);
                $this->Session->write('Order.Item.categoryid', $categoryId);
                $this->Session->write('Order.Item.price', $default_price);
                $this->Session->write('Order.Item.taxvalue', $itemtaxvalue);
                $this->Session->write('Order.Item.final_price', $default_price);
                $this->loadModel('Offer');
                $this->loadModel('ItemOffer');
                $this->Offer->bindModel(array('hasMany' => array('OfferDetail')));
                $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
                $today = 1;
                $orderType = 3;
                $display_offer = $this->Offer->allOfferOnItem($itemId);
                $itemOffer = $this->ItemOffer->allItemOfferOnItem($itemId, $current_date);
                $itemQuantity = $this->Session->read('Order.Item.quantity');
                if ($querySize == 0) {
                    $offer_result = $this->Offer->offerOnItemm($itemId, $itemQuantity);
                } else {
                    $offer_result = $this->Offer->offerOnItemSizee($itemId, $querySize, $itemQuantity);
                }
                if (!empty($offer_result)) {
                    $this->Session->write('Offer', $offer_result['Offer']);
                }
                $productInfo['Item']['sizeOnly'] = @$_POST['sizeType'];

                ///Check Now Time//

                $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
                $setPre = $finaldata['setPre'];
                $avalibilty_status = $this->Common->checkStoreAvalibility($decrypt_storeId);
                ///Check Now Time//
                if ($avalibilty_status != 1) {
                    $this->set(compact('avalibilty_status'));
                }
                $directAddtoCart = false;
                if (empty($productInfo['Topping']) && empty($productInfo['ItemType'])) {
                    if (isset($productInfo['ItemPrice'][0]['Size']) && empty($productInfo['ItemPrice'][0]['Size'])) {
                        $directAddtoCart = true;
                    }
                    if (empty($productInfo['ItemPrice'])) {
                        $directAddtoCart = true;
                    }
                }


                $this->set(compact('toppingSizes', 'productInfo', 'default_price', 'display_offer', 'itemId', 'itemisFree', 'setPre', 'itemOffer', 'directAddtoCart'));
            } else {
                $directAddtoCart = false;
                if (empty($productInfo['Topping']) && empty($productInfo['ItemType'])) {
                    if (isset($productInfo['ItemPrice'][0]['Size']) && empty($productInfo['ItemPrice'][0]['Size'])) {
                        $directAddtoCart = true;
                    }
                    if (empty($productInfo['ItemPrice'])) {
                        $directAddtoCart = true;
                    }
                }

                $this->set(compact('productInfo', 'directAddtoCart'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:sizePrice()
      Description:To Fetch price based on the size
      created:22/7/2015
      ----------------------------------------------------- */

    public function sizePrice() {
        $this->autoRender = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $itemId = $_POST['itemId'];
            $sizeId = $_POST['sizeId'];

            $this->Session->delete('Order.subPreference');
            $this->Session->delete('Order.Order.Item.subPreferenceOld'); //NEW
            $this->Session->read('Order.Item.subPreferenceOld'); //NEW
            $this->Session->delete('Order.Item.PaidTopping');
            /*             * *********Offer in case of Size*********** */
            $this->loadModel('Offer');
            $this->Offer->bindModel(array('hasMany' => array('OfferDetail')));
            $offer_result = $this->Offer->offerOnItemSize($itemId, $sizeId);
            if (!empty($offer_result)) {
                $this->Session->delete('Offer');
                if ($offer_result['Offer']['unit'] == 1) {
                    $this->Session->write('Offer', $offer_result['Offer']);
                }
            } else {
                $this->Session->delete('Offer');
            }
            /*             * ***************************************** */


            $this->loadModel('ItemPrice');
            $storeId = $this->Session->read('store_id');
            $price = $this->ItemPrice->fetchItemPrice($itemId, $sizeId, $storeId);

            if ($price) {
                $default_price = $price['ItemPrice']['price'];

                $intervalPrice = 0;
                $intervalPrice = $this->getTimeIntervalPrice($itemId, $sizeId);
                if (!empty($intervalPrice['Interval']['IntervalDay']) && !empty($intervalPrice['IntervalPrice'])) {
                    //$default_price = $intervalPrice;
                    $default_price = $intervalPrice['IntervalPrice']['price'];
                    $this->Session->write('Order.Item.interval_id', $intervalPrice['IntervalPrice']['interval_id']);
                }

                $this->Session->write('Order.Item.actual_price', $default_price);
                $this->Session->write('Order.Item.SizePrice', $default_price);
                $this->Session->write('Order.Item.price', $default_price);
                $this->Session->write('Order.Item.final_price', $default_price);
            }
            return $default_price;
        }
    }

    /* ------------------------------------------------
      Function name:typePrice()
      Description:To Fetch price based on the type
      created:07/9/2015
      ----------------------------------------------------- */

    public function typePrice() {
        $this->autoRender = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $itemId = $_POST['itemId'];
            $typeId = $_POST['typeId'];

            $this->loadModel('ItemType');
            $storeId = $this->Session->read('store_id');
            $this->ItemType->bindModel(
                    array('belongsTo' => array(
                            'Type' => array(
                                'className' => 'Type',
                                'foreignKey' => 'type_id',
                                'conditions' => array('Type.is_active' => 1, 'Type.is_deleted' => 0, 'Type.store_id' => $storeId)
                            )
            )));
            $price = $this->ItemType->fetchItemType($itemId, $typeId, $storeId);
            if (!empty($price['Type'])) {
                $default_price = $price['Type']['price'];
                if ($this->Session->check('Order.Item.topping_total')) {
                    $topping = $this->Session->read('Order.Item.topping_total');
                    if ($topping == 0.00) {
                        $this->Session->delete('Order.Item.topping_total');
                    } else {
                        $default_price = ($default_price) + ($topping);
                        $default_price = round($default_price, 2);
                    }
                }
                if ($this->Session->check('Order.Item.SizePrice')) {
                    $sizePrice = $this->Session->read('Order.Item.SizePrice');
                    $default_price = $default_price + $sizePrice;
                    $default_price = round($default_price, 2);
                }
                $this->Session->write('Order.Item.TypePrice', $price['Type']['price']);
                $this->Session->write('Order.Item.price', $default_price);
                $this->Session->write('Order.Item.final_price', $default_price);
            }
            return $default_price;
        }
    }

    /* ------------------------------------------------
      Function name:subPreferencePrice()
      Description:To Fetch price based on the sub Preference
      created:30/11/2015
      ----------------------------------------------------- */



    /* ------------------------------------------------
      Function name:ajaxsSbPreferencePrice()
      Description:To Fetch price based on the sub Preference
      created:30/11/2015
      ----------------------------------------------------- */

    public function ajaxSubPreferencePrice() {
        $this->autoRender = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $itemId = $_POST['itemId'];
            $subpreId = $_POST['subPreferenceId'];
            $sizeId = $_POST['sizeId'];
            $checked = $_POST['checked'];
            $subSizeID = $_POST['subSizeID'];
            $countSizes = $_POST['countSizes'];
            $subDefault = $_POST['subDefault'];
            //pr($this->Session->read()); exit;
            $storeId = $this->Session->read('store_id');
            $this->loadModel('SubPreference');
            if (empty($subDefault) && !empty($sizeId)) {
                $this->SubPreference->bindModel(
                        array('hasOne' => array(
                                'SubPreferencePrice' => array(
                                    'className' => 'SubPreferencePrice',
                                    'foreignKey' => 'sub_preference_id',
                                    'conditions' => array('SubPreferencePrice.is_active' => 1, 'SubPreferencePrice.is_deleted' => 0, 'SubPreferencePrice.store_id' => $storeId, 'SubPreferencePrice.size_id' => $sizeId, 'SubPreferencePrice.item_id' => $itemId),
                                    'fields' => array('SubPreferencePrice.id', 'SubPreferencePrice.item_id', 'SubPreferencePrice.size_id', 'SubPreferencePrice.price', 'SubPreferencePrice.sub_preference_id')
                                )
                            )
                ));
            }
            $supreferenceData = $this->SubPreference->getSubPreferenceDetail($subpreId, $storeId);
            $subpreference = array();
            $default_price = $new_suppreference_price = 0;
            $newDefaultPrice = 0;
            $old_sub_preference_value = $this->Session->read('Order.Item.subPreferenceOld.' . $subpreId);
            if (!empty($old_sub_preference_value)) {
                $oldPrice = $old_sub_preference_value['price'];
            } else {
                $oldPrice = 0;
            }

            if (!empty($supreferenceData)) {
                $item_price = $this->Session->read('Order.Item.price');
                if (isset($supreferenceData['SubPreferencePrice']) && !empty($supreferenceData['SubPreferencePrice']['price'])) {
                    $default_price = $supreferenceData['SubPreferencePrice']['price'];
                }
                if (!empty($subDefault) || empty($sizeId)) {
                    $default_price = $supreferenceData['SubPreference']['price'];
                }
                $new_suppreference_price = $default_price * $subSizeID;
                if ($checked == 1) {
                    $subpreference['id'] = $subpreId;
                    $subpreference['type_id'] = $supreferenceData['SubPreference']['type_id'];
                    $subpreference['name'] = $supreferenceData['SubPreference']['name'];
                    $subpreference['price'] = $new_suppreference_price;
                    $subpreference['size'] = $subSizeID;
                    $this->Session->write('Order.Item.subPreferenceOld.' . $subpreId, $subpreference); //Topping Session
                    if ($this->Session->check('Order.Item.subpreference_total')) {
                        $previous = $this->Session->read('Order.Item.subpreference_total');
                        $subpreference_total = ($previous - $oldPrice) + $new_suppreference_price;
                    }
                    $new_price = ($item_price - $oldPrice) + $new_suppreference_price;
                } else {
                    $this->Session->delete('Order.Item.subPreferenceOld.' . $subpreId);
                    if ($this->Session->check('Order.Item.subpreference_total')) {
                        $previous = $this->Session->read('Order.Item.subpreference_total');
                        $subpreference_total = $previous - $oldPrice;
                    }
                    $new_price = $item_price - $oldPrice;
                }
                $this->Session->write('Order.Item.price', $new_price);
                $this->Session->write('Order.Item.final_price', $new_price);
                if ($this->Session->check('Order.Item.subpreference_total')) {
                    $this->Session->write('Order.Item.subpreference_total', $subpreference_total);
                    //extra
                    $this->Session->write('Order.Item.TypePrice', $subpreference_total);
                } else {
                    $this->Session->write('Order.Item.subpreference_total', $new_suppreference_price);
                    //extra
                    $this->Session->write('Order.Item.TypePrice', $new_suppreference_price);
                }
                $response['status'] = 'Success';
                $response['price'] = $new_price;
            } else {
                $response['status'] = 'Error';
            }
            return json_encode($response);
        }
    }

    /* ------------------------------------------------
      Function name:fetchCategoryInfo()
      Description:To Fetch price based on the size
      created:22/7/2015
      ----------------------------------------------------- */

    public function fetchCategoryInfo() {
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            if ($this->Session->check('Order.Item')) {
                $this->Session->delete('Order.Item');
                $this->Session->delete('Order.subPreference');
            }
            $categoryId = $_POST['categoryId'];
            $storeId = $_POST['storeId'];
            $this->Session->write('Order.Item.category_id', $categoryId); // It will write the session of item
            $this->loadModel('Category');
            $storeId = $this->Session->read('store_id');
            $category_result = $this->Category->getCategorySizeType($categoryId, $storeId);
            if (isset($category_result['Category']['imgcat'])) {
                $image_name = $category_result['Category']['imgcat'];
                $this->set(compact('image_name', 'category_result'));
            }
        }
    }

    public function checkCombination() {
        $this->layout = "ajax";
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            if ($this->Session->read('cart')) {
                //pr($_POST);
                //prx($_SESSION);
                $itemId = $this->Session->read('Order.Item.id');
                $old_array = $this->Session->read('cart');
                $store_id = $this->Session->read('store_id');
                $typeCheck = 1;
                $defaultCheck = 1;
                $sizeCheck = 1;
                $paidCheck = 1;
                $count1 = 0;

                foreach ($old_array as $itemcheck) {
                    if ($itemcheck['Item']['id'] == $itemId) {
                        $count1++;
                    }
                }
                $count2 = 0;
                foreach ($old_array as $key => $itemcheck) {
                    if ($itemcheck['Item']['id'] == $itemId) {
                        $count2++;
                        $p = 0;
                        $sub_pre_array = array();
                        if (!empty($_POST['data']['Item']['subpreference'])) {
                            foreach ($_POST['data']['Item']['subpreference'] as $type_id => $pref) {
                                if ($pref['id'] != 0) {
                                    $sub_pre_array[$type_id]['id'] = $pref['id'];
                                    $sub_pre_array[$type_id]['type_id'] = $pref['type_id'];
                                    $sub_pre_array[$type_id]['name'] = $pref['name'];
                                    $sub_pre_array[$type_id]['price'] = $pref['price'];
                                    $sub_pre_array[$type_id]['size'] = $pref['size'];
                                }
                            }
                        }
                        if (!isset($itemcheck['Item']['subPreferenceOld'])) {
                            $itemcheck['Item']['subPreferenceOld'] = array();
                        }
                        if ($sub_pre_array == $itemcheck['Item']['subPreferenceOld']) {
                            $typeCheck = 1;
                        } else {
                            $typeCheck = 2;
                        }
                        if (isset($_POST['data']['Item']['price'])) {
                            if (isset($itemcheck['Item']['size_id']) && $itemcheck['Item']['size_id'] == $_POST['data']['Item']['price']) {
                                $sizeCheck = 1;
                            } else {
                                $sizeCheck = 2;
                            }
                        }
                        $default_array = array();
                        $paid_array = array();
                        if (isset($_POST['data']['Item']['toppings'])) {  //Default Toppinng
                            $this->loadModel('AddonSize');
                            $i = 0;
                            $j = 0;
                            foreach ($_POST['data']['Item']['toppings'] as $topp) {
                                if ($topp['id'] != 0) {
//                                    if ($topp['size'] == 0) {
//                                        $pricePercentage['AddonSize']['price_percentage'] = 100;
//                                    } else {
//                                        $pricePercentage = $this->AddonSize->fetchAddonPercentage($topp['size'], $store_id);
//                                    }
                                    if ($topp['type'] == 1) {
                                        if ($topp['size'] == 1) {
                                            $default_array[$j]['id'] = $topp['id'];
                                            $default_array[$j]['size'] = $topp['size'];
                                            $default_array[$j]['name'] = $topp['name'];
                                            $j++;
                                        } else {
                                            $paid_array[$i]['id'] = $topp['id'];
                                            $paid_array[$i]['size'] = $topp['size'];
                                            $paid_array[$i]['name'] = $topp['name'];
                                            $i++;
                                        }
                                    } else {
                                        $paid_array[$i]['id'] = $topp['id'];
                                        $paid_array[$i]['size'] = $topp['size'];
                                        $paid_array[$i]['name'] = $topp['name'];
                                        $i++;
                                    }
                                }
                            }
                        }

                        if (!isset($itemcheck['Item']['default_topping'])) {
                            $itemcheck['Item']['default_topping'] = array();
                        }
                        if ($default_array == $itemcheck['Item']['default_topping']) {
                            $defaultCheck = 1;
                        } else {
                            $defaultCheck = 2;
                        }

                        if (!isset($itemcheck['Item']['paid_topping'])) {
                            $itemcheck['Item']['paid_topping'] = array();
                        }

                        if ($paid_array == $itemcheck['Item']['paid_topping']) {
                            $paidCheck = 1;
                        } else {
                            $paidCheck = 2;
                        }
                        if ($defaultCheck == 1 && $paidCheck == 1 && $sizeCheck == 1 && $typeCheck == 1) {
                            $this->Session->delete('Offer');
                            return json_encode(array('index' => $key, 'quantity' => $itemcheck['Item']['quantity']));
                        } else {
                            if ($count2 >= $count1) {
                                return 0;
                            }
                        }
                    }
                }
                return 0;
            } else {
                return 0;
            }
        }
    }

    function addPostData() {
        $this->loadModel('ItemPrice');
        $store_id = $this->Session->read('store_id');
        if (isset($_POST['data']['Item']['type'])) {
            $this->loadModel('Type');
            $type_id = $this->Type->findTypeName($_POST['data']['Item']['type'], $store_id);
            $type_name = $type_id['Type']['name'];
            $this->Session->write('Order.Item.type', $type_name);
            $this->Session->write('Order.Item.type_id', $_POST['data']['Item']['type']);
        }

        if (isset($_POST['data']['Item']['subpreference'])) {
            $this->Session->write('Order.Item.subpreference', $_POST['data']['Item']['subpreference']);
        }
        $ItemtaxAmount = 0;
        if ($this->Session->read('Order.Item.taxvalue')) {
            $ItemtaxAmount = ($this->Session->read('Order.Item.taxvalue') / 100) * $this->Session->read('Order.Item.final_price');
        }
        $this->Session->write('Order.Item.taxamount', $ItemtaxAmount);

        //pr($this->Session->read('cart'));


        if (isset($_POST['data']['Item']['price'])) {
            $this->loadModel('Size');
            $sizeName = $this->Size->getSizeName($_POST['data']['Item']['price']);
            $type_name = $sizeName['Size']['size'];
            $size_id = $_POST['data']['Item']['price'];
            $this->Session->write('Order.Item.size', $type_name);
            $this->Session->write('Order.Item.size_id', $size_id);
            $querySize = $size_id;
        } else {
            $querySize = 0;
        }

        if (isset($_POST['data']['Item']['toppings'])) {  //Default Toppinng
            $this->loadModel('AddonSize');
            $default_check = array();
            $paid_check = array();
            $i = 0;
            $j = 0;
            foreach ($_POST['data']['Item']['toppings'] as $topp) {
                if ($topp['id'] != 0) {
//                    if ($topp['size'] == 0) {
//                        $pricePercentage['AddonSize']['price_percentage'] = 100;
//                    } else {
//                        $pricePercentage = $this->AddonSize->fetchAddonPercentage($topp['size'], $store_id);
//                    }
                    if ($topp['type'] == 1) {
                        if ($topp['size'] == 1) {
                            $default_check[$j]['id'] = $topp['id'];
                            $default_check[$j]['size'] = $topp['size'];
                            $default_check[$j]['name'] = $topp['name'];
                            $default_check[$j]['price'] = $topp['price'];
                            $j++;
                        } else {
                            $paid_check[$i]['id'] = $topp['id'];
                            $paid_check[$i]['size'] = $topp['size'];
                            $paid_check[$i]['name'] = $topp['name'];
                            $paid_check[$i]['price'] = $topp['price'];
                            $paid_name[$topp['name']] = $topp['price'] * $topp['size'];
                            $i++;
                        }
                    } else {
                        $paid_check[$i]['id'] = $topp['id'];
                        $paid_check[$i]['size'] = $topp['size'];
                        $paid_check[$i]['name'] = $topp['name'];
                        $paid_check[$i]['price'] = $topp['price'];
                        $paid_name[$topp['name']] = $topp['price'] * $topp['size'];
                        $i++;
                    }
                }
            }

            if (!empty($default_check)) {
                $this->Session->write('Order.Item.default_topping', $default_check);
            }
            if (!empty($paid_check)) {
                $this->Session->write('Order.Item.paid_topping', $paid_check);
                $this->Session->write('Order.Item.PaidTopping', $paid_name);
            }

            $this->Session->write('Order.Item.price', $this->Session->read('Order.Item.final_price'));
        }
    }

    /* ------------------------------------------------
      Function name:cart()
      Description:This function will add the items into the cart
      created:5/8/2015
      ----------------------------------------------------- */

    public function cart() {
        //pr($this->Session->read());
        if (!empty($this->request->data) && !empty($this->request->data['mydata']) && $this->request->data['mydata'] == 1) {
            $this->Session->write('Order.Item', $this->Session->read('Itemcartdata.data.Item'));
            $this->Session->write('Order.subPreference', $this->Session->read('Itemcartdata.data.subPreferenceOld'));
            $_POST = $this->Session->read('Itemcartdata');
            $this->request->data = $this->Session->read('Itemcartdata.data');
            $this->Session->delete('Itemcartdata');
        }
        if (!$this->Session->check('CartOffer')) {
            if ($this->Session->check('CartOfferM')) {
                $this->Session->write('CartOffer', $this->Session->read('CartOfferM'));
                $this->Session->delete('CartOfferM');
            }
        }
        if (DESIGN != 4) {
            $this->autoRender = false;
        }
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $cart_array = array();
            if (!empty($_POST['data'])) {
                $this->addPostData();
            }
            #########Add here############
            $ItemtaxAmount = 0;
            if ($this->Session->read('Order.Item.taxvalue')) {
                $ItemtaxAmount = ($this->Session->read('Order.Item.taxvalue') / 100) * $this->Session->read('Order.Item.final_price');
            }
            $this->Session->write('Order.Item.taxamount', $ItemtaxAmount);
            #########Add here############
            $this->Session->write('Order.Item.price', $this->Session->read('Order.Item.final_price'));
            if ($this->Session->read('Order.Item.final_price') <= 0) {

                $final_cart = $this->Session->read('cart');
                $this->set(compact('final_cart'));
                $this->set('Currentitem', 0);
            } else {

                $order_segment = "";
                $preOrderCheck = "";
                if ($this->Session->read('Order')) { //Here we are checking the Order type
                    $order_segment = $this->Session->read('Order.order_type');
                    $preOrderCheck = $this->Session->read('Order.is_preorder');
                }
                if ($order_segment == 2 || $order_segment == 3) {
                    if ($preOrderCheck == 0) {
                        //$orderTime = date('Y-m-d') . " " . $this->Session->read('Order.store_pickup_time');
                        $orderTime = $this->Common->gettodayDate() . " " . $this->Session->read('Order.store_pickup_time');
                    } else {
                        $order_date = $this->Session->read('Order.store_pickup_date');
                        $order_time = $this->Session->read('Order.store_pickup_time');
                        $orderDate = $this->Dateform->formatDate($order_date);
                        $orderpassedTime = $order_time;
                        $orderTime = $orderDate . " " . $orderpassedTime;
                    }
                    $this->Session->write('Cart.order_time', $orderTime);
                }

                $itemId = $this->Session->read('Order.Item.id');

                $FreeItemQuantity = 0;
                $userID = $this->Session->read('Auth.User.id');
                if ($userID) {
                    //$ItemQuantity=$item;
                    $FreeItemQuantity = $this->checkItemOffer($itemId, $userID);
                } else {
                    $FreeItemQuantity = $this->checkItemOffer($itemId, null);
                }
                $this->Session->write('Order.Item.freeQuantity', $FreeItemQuantity);
                $current_order = $this->Session->read('Order');



                $extracost = 0;
                if (!empty($current_order)) {
                    if (!empty($current_order['Item']['PaidTopping'])) {
                        $extracost = $extracost + array_sum($current_order['Item']['PaidTopping']);
                    }
                    if (!empty($current_order['Item']['subPreferenceOld'])) {
                        $subTotal = 0;
                        foreach ($current_order['Item']['subPreferenceOld'] as $sub) {
                            $subTotal = $subTotal + $sub['price'];
                        }
                        $extracost = $extracost + $subTotal;
                    }
                }

                if ($this->Session->check('cart')) {

                    $old_array = $this->Session->read('cart');
                    $CartOfferMId = $this->Session->read('CartOfferM.Offer.item_id');
                    $CartOfferId = $this->Session->read('CartOffer.Offer.item_id');
                    $OrderSID = $this->Session->read('Order.Item.id');
                    //echo $OrderSID . '#' . $CartOfferMId . '#' . $CartOfferId;
                    $OfferApplicable = false;
                    if ($CartOfferMId == $OrderSID || $CartOfferId == $OrderSID) {
                        $OfferApplicable = true;
                    }



                    if ($OfferApplicable) {
                        $storeOfferArray = array();
                        $offer_array = $this->Session->read('CartOffer');
                        $offerPrice = 0;
                        $offerItemName = '';
                        $prefix = '';
                        $i = 0;
                        foreach ($offer_array['OfferDetail'] as $off) {

                            if ($offer_array['Offer']['is_fixed_price'] == 1) {
                                $offerType = 1;
                                if ($offer_array['Offer']['offerprice'] == 0) {
                                    $offerPrice = 0;
                                    $rate = 0;
                                } else {
                                    $offerPrice = $offer_array['Offer']['offerprice'];
                                    $rate = 0;
                                }
                            } elseif ($offer_array['Offer']['is_fixed_price'] == 0) {
                                $offerType = 0;
                                if ($off['discountAmt'] == 0) {
                                    $offerPrice = $offerPrice + 0;
                                    $rate = 0;
                                } else {
                                    $offerPrice = $offerPrice + $off['discountAmt'];
                                    $rate = $off['discountAmt'];
                                }
                            }
                            if ($rate == 0) {
                                if ($offerType == 1) {
                                    $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $i . '><b><i class="fa fa-times"></i></b> ' . $off['quantity'] . ' X ' . $off['Item']['name'] . '</a>';
                                } else {
                                    $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $i . '><b><i class="fa fa-times"></i></b> ' . $off['quantity'] . ' X ' . $off['Item']['name'] . ' @ Free </a>';
                                }
                            } else {
                                $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $i . '><b><i class="fa fa-times"></i></b> ' . $off['quantity'] . ' X ' . $off['Item']['name'] . ' @ $' . $rate . '</a>';
                            }
                            $offerItemUnit = $offer_array['Offer']['unit'];
                            $prefix = '<br/> ';
                            $storeOfferArray[$i]['offer_id'] = $offer_array['Offer']['id'];
                            $storeOfferArray[$i]['offered_item_id'] = $off['offerItemID'];
                            $storeOfferArray[$i]['offered_size_id'] = $off['offerSize'];
                            $storeOfferArray[$i]['quantity'] = $off['quantity'];
                            $storeOfferArray[$i]['Item_name'] = $off['Item']['name'];
                            $storeOfferArray[$i]['offer_price'] = $rate;
                            $i++;
                        }
                        $numItems = count($old_array);
                        foreach ($old_array as $key => $old) {
                            if ($this->Session->read('OfferAddIndex')) {
                                $matchValue = $this->Session->read('OfferAddIndex.cartid');
                                if ($key == $matchValue) {
                                    if (($offer_array['Offer']['item_id'] == $old['Item']['id'])) {
                                        $old_array[$key]['Item']['OfferItemName'] = $offerItemName;
                                        $old_array[$key]['Item']['OfferItemPrice'] = $offerPrice;
                                        $old_array[$key]['Item']['OfferType'] = $offerType;
                                        $old_array[$key]['Item']['OfferItemUnit'] = $offerItemUnit;
                                        $old_array[$key]['Item']['StoreOffer'] = $storeOfferArray;
                                        if ($offerType == 1) {
                                            $old_array[$key]['Item']['final_price'] = $offerPrice + $extracost;
                                        } else {
                                            $old_array[$key]['Item']['final_price'] = $old['Item']['final_price'] + $offerPrice;
                                        }
                                    }
                                }
                            } else {
                                if ($key == $numItems - 1) {

                                    if (($offer_array['Offer']['item_id'] == $old['Item']['id'])) {
                                        $old_array[$key]['Item']['OfferItemName'] = $offerItemName;
                                        $old_array[$key]['Item']['OfferItemPrice'] = $offerPrice;
                                        $old_array[$key]['Item']['OfferType'] = $offerType;
                                        $old_array[$key]['Item']['OfferItemUnit'] = $offerItemUnit;
                                        $old_array[$key]['Item']['StoreOffer'] = $storeOfferArray;
                                        if ($offerType == 1) {
                                            $old_array[$key]['Item']['final_price'] = $offerPrice + $extracost;
                                        } else {
                                            $old_array[$key]['Item']['final_price'] = $old['Item']['final_price'] + $offerPrice;
                                        }
                                    }
                                }
                            }
                        }
                        $this->Session->delete('OfferAddIndex');
                        $this->Session->delete('CartOffer');
                        $this->Session->write('cart', $old_array);
                    } else {
                        $exist_id = array();
                        foreach ($old_array as $itemcheck) {
                            $exist_id[] = @$itemcheck['Item']['id'];
                        }
                        $old_array[] = $current_order;
                        $this->Session->write('cart', $old_array);
                    }
                } else {
                    if ($current_order) {
                        $cart_array[] = $current_order;
                    }
                    $this->Session->write('cart', $cart_array);
                }

                /*                 * ***********************Offers******************* */

                if ($this->Session->check('Offer')) {
                    $is_offer = $this->Session->read('Offer');

                    $this->loadModel('Offer');
                    $this->loadModel('OfferDetail');
                    $this->OfferDetail->bindModel(
                            array('belongsTo' => array(
                                    'Item' => array(
                                        'className' => 'Item',
                                        'foreignKey' => 'offerItemID',
                                    ),
                                    'Size' => array(
                                        'className' => 'Size',
                                        'foreignKey' => 'offerSize',
                                    ),
                                    'Type' => array(
                                        'className' => 'Type',
                                        'foreignKey' => 'offerItemType',
                                    )
                    )));
                    $this->Offer->bindModel(
                            array('hasMany' => array(
                                    'OfferDetail' => array(
                                        'className' => 'OfferDetail',
                                        'foreignKey' => 'offer_id',
                                        'conditions' => array('OfferDetail.is_deleted' => 0, 'OfferDetail.is_active' => 1)
                                    )),
                                'belongsTo' => array(
                                    'Item' => array(
                                        'className' => 'Item',
                                        'foreignKey' => 'item_id',
                                    )),
                    ));
                    $getOffer = $this->Offer->getOfferDetails($is_offer['id']);
                    $this->Session->delete('Offer');
                    $this->Session->write('CartOffer', $getOffer);
                    $this->set(compact('getOffer'));
                    //pr($this->Session->read('CartOffer'));
                } else {
                    $getOffer = array();
                    $this->set(compact('getOffer'));
                }
                /*                 * ****************************************** */
                //pr($this->Session->read());
                $this->loadModel('Store');
                $store_result = $this->Store->fetchStoreDetail($this->Session->read('store_id'));
                $this->Session->write('minprice', $store_result['Store']['minimum_order_price']);
                $final_cart = $this->Session->read('cart');
                $this->set(compact('final_cart'));
            }

            if ($this->Session->check('CartOffer')) {
                $this->Session->write('CartOfferM', $this->Session->read('CartOffer'));
            }

            //pr($this->Session->read());
            if (DESIGN != 4) {
                if (!empty($getOffer)) {
                    echo "1";  //Need this to differentiate element to display in the Front Site
                    $this->render('/Elements/item-offer');
                } else {
                    echo "0"; //Need this to differentiate element to display in the Front Site
                    $this->render('/Products/cart');
                }
            }
        }
    }

    private function _checkPrefAddons() {
        $data = $this->request->data;
        if (!empty($data['Item']['hidden_itemID']) && !empty($_SESSION['Order']['order_type']) && $_SESSION['Order']['order_type'] == 3) {
            $this->loadModel('Item');
            $iData = $this->Item->findById($data['Item']['hidden_itemID'], array('name', 'is_deliverable'));
            if (!empty($iData) && $iData['Item']['is_deliverable'] == 0) {
                $response['status'] = 'Error';
                $response['msg'] = "The items below are non-deliverable:<br><br>" . $iData['Item']['name'] . "<br><br>Please change your order type.";
                return json_encode($response);
            }
        }
        $currentSelectedItem = $this->Session->read('Order.Item.id');
        $store_id = $this->Session->read('store_id');
        $arrCretreia = $typeData = array();
        $totalSubpreferenceSelected = 0;
        $subpreferences = $subpreDetail = array();
        if (!empty($data['Item']['subpreference'])) {
            foreach ($data['Item']['subpreference'] as $subpreferenceId => $subPreData) {
                if (!isset($subpreferences[$subPreData['type_id']])) {
                    $subpreferences[$subPreData['type_id']] = 0;
                }
                if ($subPreData['id'] != 0) {
                    $totalSubpreferenceSelected +=$subPreData['size'];
                    $subpreferences[$subPreData['type_id']] += $subPreData['size'];
                }
            }
        }
        if (!empty($currentSelectedItem)) {
            $this->loadModel('ItemType');
            $this->loadModel('Type');
            $this->ItemType->bindModel(
                    array('belongsTo' => array(
                            'Type' => array(
                                'className' => 'Type',
                                'foreignKey' => 'type_id',
                                'type' => 'inner',
                                'fields' => array('Type.id', 'Type.min_value', 'Type.max_value'),
                                'conditions' => array('Type.is_active' => 1, 'Type.is_deleted' => 0, 'Type.store_id' => $store_id, 'Type.min_value >' => 0, 'Type.max_value >' => 0)
                            )
            )));
            $this->Type->unBindModel(array('hasMany' => array('ItemType')));
            $typeData = $this->ItemType->find('all', array('conditions' => array('ItemType.item_id' => $currentSelectedItem, 'ItemType.is_active' => 1, 'ItemType.is_deleted' => 0), 'fields' => array('ItemType.id', 'ItemType.type_id', 'ItemType.item_id', 'Type.id', 'Type.name', 'Type.min_value', 'Type.max_value'), 'recursive' => 2));
            if (empty($subpreferences) && count($typeData) != $totalSubpreferenceSelected) {
                $response['status'] = 'Error';
                $response['type'] = 'Preference';
                $response['msg'] = 'Please select min/max preference.';
                return json_encode($response);
            }
        }
        if (!empty($subpreferences)) {
            $this->loadModel('Type');
            foreach ($subpreferences as $preference_id => $subData) {
                $this->Type->unBindModel(array('hasMany' => array('ItemType')));
                $prefData = $this->Type->find('first', array('conditions' => array('Type.id' => $preference_id, 'Type.is_deleted' => 0, 'Type.is_active' => 1, 'Type.store_id' => $store_id), 'fields' => array('Type.id', 'Type.min_value', 'Type.max_value', 'Type.name')));
                $totalSubpreferences = $subData;
                if (!empty($prefData) && ($prefData['Type']['min_value'] > 0 || $prefData['Type']['max_value'] > 0)) {
                    if ($prefData['Type']['min_value'] == 0 && $prefData['Type']['max_value'] > 0) {
                        if ($prefData['Type']['min_value'] > $totalSubpreferences) {
                            $response['status'] = 'Error';
                            $response['type'] = 'Preference';
                            if ($prefData['Type']['min_value'] > 1) {
                                $msg = 'Choose ' . $prefData['Type']['max_value'] . ' items of ' . $prefData['Type']['name'] . '.';
                            } else {
                                $msg = 'Choose ' . $prefData['Type']['max_value'] . ' item of ' . $prefData['Type']['name'] . '.';
                            }
                            $response['msg'] = $msg;
                            return json_encode($response);
                        }
                        if ($prefData['Type']['max_value'] < $totalSubpreferences) {
                            $response['status'] = 'Error';
                            $response['type'] = 'Preference';
                            if ($prefData['Type']['max_value'] > 1) {
                                $msg = 'Choose up to ' . $prefData['Type']['max_value'] . ' items of ' . $prefData['Type']['name'] . '.';
                            } else {
                                $msg = 'Choose up to ' . $prefData['Type']['max_value'] . ' item of ' . $prefData['Type']['name'] . '.';
                            }
                            $response['msg'] = $msg;
                            return json_encode($response);
                        }
                    } elseif ($prefData['Type']['min_value'] == $prefData['Type']['max_value']) {
                        if ($prefData['Type']['min_value'] > $totalSubpreferences) {
                            $response['status'] = 'Error';
                            $response['type'] = 'Preference';
                            if ($prefData['Type']['min_value'] > 1) {
                                $msg = 'Choose ' . $prefData['Type']['max_value'] . ' items of ' . $prefData['Type']['name'] . '.';
                            } else {
                                $msg = 'Choose ' . $prefData['Type']['max_value'] . ' item of ' . $prefData['Type']['name'] . '.';
                            }
                            $response['msg'] = $msg;
                            return json_encode($response);
                        }
                        if ($prefData['Type']['max_value'] < $totalSubpreferences) {
                            $response['status'] = 'Error';
                            $response['type'] = 'Preference';
                            if ($prefData['Type']['max_value'] > 1) {
                                $msg = 'Choose up to ' . $prefData['Type']['max_value'] . ' items of ' . $prefData['Type']['name'] . '.';
                            } else {
                                $msg = 'Choose up to ' . $prefData['Type']['max_value'] . ' item of ' . $prefData['Type']['name'] . '.';
                            }
                            $response['msg'] = $msg;
                            return json_encode($response);
                        }
                    } else {
                        if ($prefData['Type']['min_value'] > $totalSubpreferences) {
                            $response['status'] = 'Error';
                            $response['type'] = 'Preference';
                            if ($prefData['Type']['min_value'] > 1) {
                                $msg = 'Choose at least ' . $prefData['Type']['min_value'] . ' items of ' . $prefData['Type']['name'] . ' (' . $prefData['Type']['max_value'] . ' max).';
                            } else {
                                $msg = 'Choose at least ' . $prefData['Type']['min_value'] . ' item of ' . $prefData['Type']['name'] . ' (' . $prefData['Type']['max_value'] . ' max).';
                            }
                            $response['msg'] = $msg;
                            return json_encode($response);
                        }
                        if ($prefData['Type']['max_value'] < $totalSubpreferences) {
                            $response['status'] = 'Error';
                            $response['type'] = 'Preference';
                            if ($prefData['Type']['max_value'] > 1) {
                                $msg = 'Choose up to ' . $prefData['Type']['max_value'] . ' items of ' . $prefData['Type']['name'] . '.';
                            } else {
                                $msg = 'Choose up to ' . $prefData['Type']['max_value'] . ' item of ' . $prefData['Type']['name'] . '.';
                            }
                            $response['msg'] = $msg;
                            return json_encode($response);
                        }
                    }
                }
            }
        }
        if (!empty($typeData)) {
            foreach ($typeData as $key => $value) {
                if ($value['ItemType']) {
                    $arrCretreia['item_id'] = $value['ItemType']['item_id'];
                }
                if ($value['Type']) {
                    $arrCretreia['Preference'][$value['Type']['id']]['min'] = $value['Type']['min_value'];
                    $arrCretreia['Preference'][$value['Type']['id']]['max'] = $value['Type']['max_value'];
                }
            }
            if (!empty($arrCretreia['Preference'])) {
                foreach ($arrCretreia['Preference'] as $pKey => $pdata) {
                    if (!array_key_exists($pKey, $subpreferences)) {
                        $response['status'] = 'Error';
                        $response['type'] = 'Preference';
                        $response['msg'] = 'Choose ' . $pdata['min'] . ' item of ' . $pKey . '.';
                        return json_encode($response);
                    }
                }
            }
        }
        ///Toppings
        if (!empty($data) && !empty($data['Item']['toppings'])) {
            $totalToppingSelected = 0;
            $this->loadModel('Topping');
            $toppings = $topDetail = array();
            foreach ($data['Item']['toppings'] as $toppingId => $subAddonData) {
                if (!isset($toppings[$subAddonData['addon_id']])) {
                    $toppings[$subAddonData['addon_id']] = 0;
                }
                if ($subAddonData['id'] != 0) {
                    $totalToppingSelected +=$subAddonData['size'];
                    $toppings[$subAddonData['addon_id']] += $subAddonData['size'];
                }
            }

            $topDetail = $this->Topping->find('all', array('conditions' => array('Topping.item_id' => $currentSelectedItem, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $store_id, 'Topping.is_addon_category' => 1, 'Topping.min_value >' => 0, 'Topping.max_value >' => 0), 'fields' => array('id', 'name', 'min_value', 'max_value')));
            if (empty($toppings) && count($topDetail) != $totalToppingSelected) {
                $response['status'] = 'Error';
                $response['type'] = 'Addon';
                $response['msg'] = 'Please select min/max addon.';
                return json_encode($response);
            }
            if (!empty($toppings)) {
                foreach ($toppings as $topping_id => $subAddons) {
                    $addonData = $this->Topping->find('first', array('conditions' => array('Topping.id' => $topping_id, 'Topping.is_addon_category' => 1, 'Topping.is_deleted' => 0, 'Topping.is_active' => 1, 'Topping.store_id' => $store_id), 'fields' => array('Topping.id', 'Topping.min_value', 'Topping.max_value', 'Topping.name')));
                    $totalSubAddons = $subAddons;
                    if (!empty($addonData) && ($addonData['Topping']['min_value'] > 0 || $addonData['Topping']['max_value'] > 0)) {
                        if ($addonData['Topping']['min_value'] == 0 && $addonData['Topping']['max_value'] > 0) {
                            if ($addonData['Topping']['min_value'] > $totalSubAddons) {
                                $response['status'] = 'Error';
                                $response['type'] = 'Addon';
                                if ($addonData['Topping']['min_value'] > 1) {
                                    $msg = 'Choose ' . $addonData['Topping']['max_value'] . ' items of ' . $addonData['Topping']['name'] . '.';
                                } else {
                                    $msg = 'Choose ' . $addonData['Topping']['max_value'] . ' item of ' . $addonData['Topping']['name'] . '.';
                                }
                                $response['msg'] = $msg;
                                return json_encode($response);
                            }
                            if ($addonData['Topping']['max_value'] < $totalSubAddons) {
                                $response['status'] = 'Error';
                                $response['type'] = 'Addon';
                                if ($addonData['Topping']['max_value'] > 1) {
                                    $msg = 'Choose up to ' . $addonData['Topping']['max_value'] . ' items of ' . $addonData['Topping']['name'] . '.';
                                } else {
                                    $msg = 'Choose up to ' . $addonData['Topping']['max_value'] . ' item of ' . $addonData['Topping']['name'] . '.';
                                }
                                $response['msg'] = $msg;
                                return json_encode($response);
                            }
                        } elseif ($addonData['Topping']['min_value'] == $addonData['Topping']['max_value']) {
                            if ($addonData['Topping']['min_value'] > $totalSubAddons) {
                                $response['status'] = 'Error';
                                $response['type'] = 'Addon';
                                if ($addonData['Topping']['min_value'] > 1) {
                                    $msg = 'Choose ' . $addonData['Topping']['max_value'] . ' items of ' . $addonData['Topping']['name'] . '.';
                                } else {
                                    $msg = 'Choose ' . $addonData['Topping']['max_value'] . ' item of ' . $addonData['Topping']['name'] . '.';
                                }
                                $response['msg'] = $msg;
                                return json_encode($response);
                            }
                            if ($addonData['Topping']['max_value'] < $totalSubAddons) {
                                $response['status'] = 'Error';
                                $response['type'] = 'Addon';
                                if ($addonData['Topping']['max_value'] > 1) {
                                    $msg = 'Choose up to ' . $addonData['Topping']['max_value'] . ' items of ' . $addonData['Topping']['name'] . '.';
                                } else {
                                    $msg = 'Choose up to ' . $addonData['Topping']['max_value'] . ' item of ' . $addonData['Topping']['name'] . '.';
                                }
                                $response['msg'] = $msg;
                                return json_encode($response);
                            }
                        } else {
                            if ($addonData['Topping']['min_value'] > $totalSubAddons) {
                                $response['status'] = 'Error';
                                $response['type'] = 'Addon';
                                if ($addonData['Topping']['min_value'] > 1) {
                                    $msg = 'Choose at least ' . $addonData['Topping']['min_value'] . ' items of ' . $addonData['Topping']['name'] . ' (' . $addonData['Topping']['max_value'] . ' max).';
                                } else {
                                    $msg = 'Choose at least ' . $addonData['Topping']['min_value'] . ' item of ' . $addonData['Topping']['name'] . ' (' . $addonData['Topping']['max_value'] . ' max).';
                                }
                                $response['msg'] = $msg;
                                return json_encode($response);
                            }
                            if ($addonData['Topping']['max_value'] < $totalSubAddons) {
                                $response['status'] = 'Error';
                                $response['type'] = 'Addon';
                                if ($addonData['Topping']['max_value'] > 1) {
                                    $msg = 'Choose up to ' . $addonData['Topping']['max_value'] . ' items of ' . $addonData['Topping']['name'] . '.';
                                } else {
                                    $msg = 'Choose up to ' . $addonData['Topping']['max_value'] . ' item of ' . $addonData['Topping']['name'] . '.';
                                }
                                $response['msg'] = $msg;
                                return json_encode($response);
                            }
                        }
                    }
                }
            }
            if (!empty($topDetail)) {
                foreach ($topDetail as $tKey => $tdata) {
                    if ($tdata['Topping']['min_value'] > 0 && $tdata['Topping']['max_value'] > 0) {
                        if (!array_key_exists($tdata['Topping']['id'], $toppings)) {
                            $response['status'] = 'Error';
                            $response['type'] = 'Addon';
                            $response['msg'] = 'Choose ' . $tdata['Topping']['min_value'] . ' item of ' . $tdata['Topping']['name'] . '.';
                            return json_encode($response);
                        }
                    }
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name:removeItem()
      Description:It will remove the item from the cart
      created:5/8/2015
      ----------------------------------------------------- */

    public function removeItem() {
        if ($this->request->is('ajax')) {
            $this->Session->delete('cart.' . $_POST['index_id']);
            $final_cart = $this->Session->read('cart');
//            if ($this->Session->check('CartOffer')) {
//                $this->Session->delete('CartOffer');
//            }
//            if (empty($final_cart)) {
//                $this->Session->delete('Coupon');
//                $this->Session->delete('CartOfferM');
//                if (DESIGN != 4) {
//                    $this->Session->delete('Order');
//                }
//            }

            $this->Session->delete('CartOffer');
            $this->Session->delete('CartOfferM');
            $this->Session->delete('Offer');

            if (empty($final_cart)) {
                $this->Session->delete('Coupon');
                if (DESIGN != 4) {
                    $this->Session->delete('Order');
                }
            }

            $this->set(compact('final_cart'));
        }
    }

    /* ------------------------------------------------
      Function name:removeOfferItem()
      Description:It will remove the offer item from the cart
      created:2/9/2015
      ----------------------------------------------------- */

    public function removeOfferItem() {
        if ($this->request->is('ajax')) {
            $present_item = $this->Session->read('cart.' . $_POST['cart_index_id']);
            $remove_quantity = $present_item['Item']['StoreOffer'][$_POST['offer_index_id']]['quantity'];
            $remove_price = $present_item['Item']['StoreOffer'][$_POST['offer_index_id']]['offer_price'];
            $actual_price = $present_item['Item']['final_price'];
            unset($present_item['Item']['StoreOffer'][$_POST['offer_index_id']]); // remove item at index 0
            $present_item['Item']['StoreOffer'] = array_values($present_item['Item']['StoreOffer']); // 'reindex' array
            $prefix = '';
            $offerItemName = '';
            $offerType = $present_item['Item']['OfferType'];
            if (!empty($present_item['Item']['StoreOffer'])) {
                foreach ($present_item['Item']['StoreOffer'] as $key => $name) {
                    if ($name['offer_price'] == 0) {
                        if ($offerType == 1) {
                            $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $name['quantity'] . ' X ' . $name['Item_name'] . '</a>';
                        } else {
                            $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $name['quantity'] . ' X ' . $name['Item_name'] . '@ Free </a>';
                        }
                    } else {
                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $name['quantity'] . ' X ' . $name['Item_name'] . ' @ $' . $name['quantity'] * $name['offer_price'] . '</a>';
                    }
                    $prefix = '<br/> ';
                }
                $this->Session->write('cart.' . $_POST['cart_index_id'] . '.Item.StoreOffer', $present_item['Item']['StoreOffer']);
                $this->Session->write('cart.' . $_POST['cart_index_id'] . '.Item.OfferItemName', $offerItemName);
                $total = $actual_price - ($remove_price * $remove_quantity);
            } else {
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.OfferType');
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.OfferItemUnit');
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.OfferItemName');
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.OfferItemPrice');
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.StoreOffer');
                $total = $present_item['Item']['price'] * $present_item['Item']['quantity'];
            }
            $total = round($total, 2);
            $this->Session->write('cart.' . $_POST['cart_index_id'] . '.Item.final_price', $total);
            $final_cart = $this->Session->read('cart');
            $this->set(compact('final_cart'));
        }
    }

    /* ------------------------------------------------
      Function name:addQuantity()
      Description: Add quantity into the cart
      created:5/8/2015
      ----------------------------------------------------- */

    public function addQuantity() {
        if (DESIGN != 4) {
            $this->autoRender = false;
        }
        $this->layout = false;
        if ($this->request->is('ajax')) {
            $item = $_POST['value'];
            $userID = $this->Session->read('Auth.User.id');
            $freeQuantity = 0;
            if ($userID) {
                $ItemQuantity = $item;
                $presentItemID = $this->Session->read('cart.' . $_POST['index_id'] . '.Item.id');
                $freeQuantity = $this->checkItemOffer($presentItemID, $userID, $ItemQuantity);
            } else {
                $ItemQuantity = $item;
                $presentItemID = $this->Session->read('cart.' . $_POST['index_id'] . '.Item.id');
                $freeQuantity = $this->checkItemOffer($presentItemID, null, $ItemQuantity);
            }
            $present_item = $this->Session->read('cart.' . $_POST['index_id']);
            if (isset($present_item['Item']['OfferItemUnit'])) {
                if ($present_item['Item']['OfferItemUnit'] >= $_POST['value']) {
                    $offer_flag = 1;
                } else {
                    $offer_flag = 0;
                }
            } else {
                $offer_flag = 1;
            }

            if (empty($present_item['Item']['price'])) {
                $this->Session->write('cart.' . $_POST['index_id'] . '.Item.price', $present_item['Item']['final_price']);
                $present_item['Item']['price'] = $present_item['Item']['final_price'];
            }

            $this->Session->write('OfferAddIndex.cartid', $_POST['index_id']);
            $this->Session->write('OfferAddIndex.cartname', 'test');
            if (isset($present_item['Item']['OfferItemPrice'])) {

                if (((($item) % ($present_item['Item']['OfferItemUnit'])) == 0) && ($present_item['Item']['OfferItemUnit'] < $item)) {
                    //$itemA = $present_item['Item']['OfferItemUnit'];
                    $offer_multiply = ($item) / ($present_item['Item']['OfferItemUnit']);
                    $offer_price = $offer_multiply * $present_item['Item']['OfferItemPrice'];
                    if ($present_item['Item']['OfferType'] == 1) {

                        $ExtrasubPrice = $present_item['Item']['price'] - $present_item['Item']['actual_price'];
                        $ExtrasubPrice = $item * $ExtrasubPrice;
                        $total = $offer_price + $ExtrasubPrice;
                    } else {

                        $total = $item * $present_item['Item']['price'];
                        $total = $total + $offer_price;
                    }
                    $prefix = '';
                    $offerItemName = '';
                    foreach ($present_item['Item']['StoreOffer'] as $key => $name) {
                        if ($name['offer_price'] == 0) {
                            if ($present_item['Item']['OfferType'] == 1) {
                                $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '</a>';
                            } else {
                                $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '@ Free </a>';
                            }
                        } else {
                            $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . ' @ $' . $offer_multiply * $name['offer_price'] . '</a>';
                        }
                        $prefix = '<br/> ';
                        $this->Session->write('cart.' . $_POST['index_id'] . '.Item.StoreOffer.' . $key . '.quantity', $offer_multiply);
                    }
                    $this->Session->write('cart.' . $_POST['index_id'] . '.Item.OfferItemName', $offerItemName);
                } else if ($present_item['Item']['OfferItemUnit'] > $item) {

                    $item_price = $present_item['Item']['price'];
                    $total = $item * $item_price;

//                    $ExtrasubPrice = $present_item['Item']['price'] - $present_item['Item']['actual_price'];
//                    $ExtrasubPrice = $item * $ExtrasubPrice;
//                    $total = $present_item['Item']['OfferItemPrice'] + $ExtrasubPrice;




                    $offer_flag = 0;
                    $this->Session->delete('cart.' . $_POST['index_id'] . '.Item.OfferType');
                    $this->Session->delete('cart.' . $_POST['index_id'] . '.Item.OfferItemUnit');
                    $this->Session->delete('cart.' . $_POST['index_id'] . '.Item.OfferItemName');
                    $this->Session->delete('cart.' . $_POST['index_id'] . '.Item.OfferItemPrice');
                    $this->Session->delete('cart.' . $_POST['index_id'] . '.Item.StoreOffer');
                } elseif ($present_item['Item']['OfferItemUnit'] == $item) {

                    $offer_flag = 0;
                    $item_price = $present_item['Item']['price'];
                    if ($present_item['Item']['OfferType'] == 1) {

                        $ExtrasubPrice = $present_item['Item']['price'] - $present_item['Item']['actual_price'];
                        $ExtrasubPrice = $item * $ExtrasubPrice;
                        $total = $present_item['Item']['OfferItemPrice'] + $ExtrasubPrice;
                        //$total = $present_item['Item']['OfferItemPrice'];
                    } else {

                        /*$ExtrasubPrice = $present_item['Item']['price'] - $present_item['Item']['actual_price'];
                        $ExtrasubPrice = $item * $ExtrasubPrice;
                        $total = $item * $item_price;
                        $total = $total + $present_item['Item']['OfferItemPrice'] + $ExtrasubPrice;*/
			$total = $item * $present_item['Item']['price'];
                        $total = $total + $present_item['Item']['OfferItemPrice'];
                    }

                    $offer_multiply = floor(($item) / ($present_item['Item']['OfferItemUnit']));
                    $prefix = '';
                    $offerItemName = '';
                    foreach ($present_item['Item']['StoreOffer'] as $key => $name) {
                        if ($name['offer_price'] == 0) {
                            if ($present_item['Item']['OfferType'] == 1) {
                                $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '</a>';
                            } else {
                                $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '@ Free </a>';
                            }
                        } else {
                            $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . ' @ $' . $offer_multiply * $name['offer_price'] . '</a>';
                        }
                        $this->Session->write('cart.' . $_POST['index_id'] . '.Item.StoreOffer.' . $key . '.quantity', $offer_multiply);
                        $prefix = '<br/> ';
                    }
                    $this->Session->write('cart.' . $_POST['index_id'] . '.Item.OfferItemName', $offerItemName);
                } else {

                    $offer_flag = 0;
                    $item_price = $present_item['Item']['price'];
                    if ($present_item['Item']['OfferType'] == 1) {

                        $offer_multiply = floor(($item) / ($present_item['Item']['OfferItemUnit']));
                        $offer_price = $offer_multiply * $present_item['Item']['OfferItemPrice'];
                        $ExtrasubPrice = $present_item['Item']['price'] - $present_item['Item']['actual_price'];
                        $ExtrasubPrice = $item * $ExtrasubPrice;
                        $total = $item_price;
                        //$total = 0;
                        $total = $total + $offer_price + $ExtrasubPrice;
                    } else {

                        $offer_multiply = floor(($item) / ($present_item['Item']['OfferItemUnit']));
                        $offer_price = $offer_multiply * $present_item['Item']['OfferItemPrice'];
                        $total = $item * $item_price;
                        $total = $total + $offer_price;
//                        $total = $item * $item_price;
//                        $total = $total + $present_item['Item']['OfferItemPrice'];
                    }

                    $offer_multiply = floor(($item) / ($present_item['Item']['OfferItemUnit']));
                    $prefix = '';
                    $offerItemName = '';
                    foreach ($present_item['Item']['StoreOffer'] as $key => $name) {
                        if ($name['offer_price'] == 0) {
                            if ($present_item['Item']['OfferType'] == 1) {
                                $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '</a>';
                            } else {
                                $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '@ Free </a>';
                            }
                        } else {
                            $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . ' @ $' . $offer_multiply * $name['offer_price'] . '</a>';
                        }
                        $this->Session->write('cart.' . $_POST['index_id'] . '.Item.StoreOffer.' . $key . '.quantity', $offer_multiply);
                        $prefix = '<br/> ';
                    }
                    $this->Session->write('cart.' . $_POST['index_id'] . '.Item.OfferItemName', $offerItemName);
                }
            } else {
                $item_price = $present_item['Item']['price'];
                $total = $item * $item_price;
            }
            $total = round($total, 2);
            $this->Session->write('cart.' . $_POST['index_id'] . '.Item.final_price', $total);
            $this->Session->write('cart.' . $_POST['index_id'] . '.Item.quantity', $item);
            $this->Session->write('cart.' . $_POST['index_id'] . '.Item.freeQuantity', $freeQuantity);
            /*             * ***********************Offers******************* */
//            if (!empty($itemA)) {
//                $item = $present_item['Item']['OfferItemUnit'];
//                $offer_flag = 1;
//            }
            if ($offer_flag == 1) {
                $this->loadModel('Offer');
                $this->loadModel('OfferDetail');
                $this->Offer->bindModel(array('hasMany' => array('OfferDetail')));
                if (isset($present_item['Item']['size_id'])) {
                    $offer_result = $this->Offer->offerOnItemSizee($present_item['Item']['id'], $present_item['Item']['size_id'], $item);
                } else {
                    $offer_result = $this->Offer->offerOnItemm($present_item['Item']['id'], $item);
                }
                $this->Session->delete('Offer');
                if (!empty($offer_result)) {
                    $this->Session->write('Offer', $offer_result['Offer']);
                }
                if ($this->Session->check('Offer')) {
                    $is_offer = $this->Session->read('Offer');
                    $this->OfferDetail->bindModel(
                            array('belongsTo' => array(
                                    'Item' => array(
                                        'className' => 'Item',
                                        'foreignKey' => 'offerItemID',
                                    ),
                                    'Size' => array(
                                        'className' => 'Size',
                                        'foreignKey' => 'offerSize',
                                    ),
                                    'Type' => array(
                                        'className' => 'Type',
                                        'foreignKey' => 'offerItemType',
                                    )
                    )));
                    $this->Offer->bindModel(
                            array('hasMany' => array(
                                    'OfferDetail' => array(
                                        'className' => 'OfferDetail',
                                        'foreignKey' => 'offer_id',
                                        'conditions' => array('OfferDetail.is_deleted' => 0, 'OfferDetail.is_active' => 1)
                                    )),
                                'belongsTo' => array(
                                    'Item' => array(
                                        'className' => 'Item',
                                        'foreignKey' => 'item_id',
                                    )),
                    ));
                    $getOffer = $this->Offer->getOfferDetails($is_offer['id']);
                    $this->Session->delete('Offer');
                    $cart_offer = $this->Session->write('CartOffer', $getOffer);
                    $this->set(compact('getOffer'));
                } else {
                    $getOffer = array();
                    $this->set(compact('getOffer'));
                }
            }
            /*             * ****************************************** */
            $final_cart = $this->Session->read('cart');
            $this->set(compact('final_cart'));
            if ($this->Session->check('CartOffer')) {
                $this->Session->write('CartOfferM', $this->Session->read('CartOffer'));
            }
            if (DESIGN != 4) {
                if (!empty($getOffer)) {
                    echo "1";  //Need this to differentiate element to display in the Front Site
                    $this->render('/Elements/item-offer');
                } else {
                    echo "0"; //Need this to differentiate element to display in the Front Site
                    $this->render('/Products/add_quantity');
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name:orderDetails()
      Description:It will show the whole details of the order to be made
      created:5/8/2015
      ---------------------------------------------------- */

    public function orderDetails() {
	if ($this->Session->check('timeError')) {
            $this->Session->setFlash(__('Please choose a different time for Delivery/Pickup.'), 'flash_error');
            $this->Session->delete('timeError');
        }
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Session->read('store_id');
        $storeSetting = $this->StoreSetting->findByStoreId($decrypt_storeId, array('order_allow'));
        if (!empty($storeSetting) && empty($storeSetting['StoreSetting']['order_allow'])) {
            $msg = 'Store is currently not taking orders.';
            $this->Session->setFlash(__($msg));
            $this->redirect($this->referer());
        }
        $decrypt_merchantId = $this->Session->read('merchant_id');
        $encrypted_storeId = $this->Encryption->encode($decrypt_storeId); // Encrypted Store Id
        $encrypted_merchantId = $this->Encryption->encode($decrypt_merchantId); // Encrypted Merchant Id
        $this->Session->delete('CartOfferM');
        $nzsafe_info = array();
        $this->loadModel('NzsafeUser');
        if (AuthComponent::User()) {
            $userId = AuthComponent::User('id');
            $nzsafe_info = $this->NzsafeUser->getUser($userId);
            $nzsafe_info = $nzsafe_info['NzsafeUser'];
        }
        $this->set(compact('nzsafe_info'));
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('countryCode'));

        //$current_date = date('Y-m-d');
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $orderType = $this->Session->read('Order.order_type');
        $today = 1;
        $orderType = 2;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);

        $pickcurrent_date = $finaldata['currentdate'];
        $explodeVal = explode("-", $pickcurrent_date);
        $pickcurrentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $this->set(compact('pickcurrentDateVar'));


        $today = 1;
        $orderType = 3;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);

        $delcurrent_date = $finaldata['currentdate'];
        $explodeVal = explode("-", $delcurrent_date);
        $delcurrentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $this->set(compact('delcurrentDateVar'));


        $time_break = $finaldata['time_break'];
        $store_data = $finaldata['store_data'];
        $storeBreak = $finaldata['storeBreak'];
        $time_range = $finaldata['time_range'];
        $current_date = $finaldata['currentdate'];
        $setPre = $finaldata['setPre'];
        $explodeVal = explode("-", $current_date);
        $currentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $this->loadModel('StoreAvailability');
        $closedDay = array();
        $storeavaibilityInfo = $this->StoreAvailability->getclosedDay($decrypt_storeId);
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

        $this->set(compact('store_data', 'storeBreak', 'setPre', 'time_break', 'time_range', 'currentDateVar'));

        if ($this->Session->check('cart') && $_SESSION['cart']) {
            $this->Session->delete('FetchProductData');
            $this->loadModel('Store');
            $store_result = $this->Store->fetchStoreDetail($this->Session->read('store_id'));
            if (isset($store_result['Store']['service_fee'])) {
                $this->Session->write('service_fee', $store_result['Store']['service_fee']);
            }
//            if (isset($store_result['Store']['delivery_fee'])) {
//                $this->Session->write('delivery_fee', $store_result['Store']['delivery_fee']);
//            }
            if (isset($store_result['Store']['tip'])) {
                $this->Session->write('tip', $store_result['Store']['tip']);
            }
            $finalItem = $this->Session->read('cart');
            $encrypted_storeId = $this->Encryption->encode($decrypt_storeId); // Encrypted Store Id
            $encrypted_merchantId = $this->Encryption->encode($decrypt_merchantId); // Encrypted Merchant Id
            $this->set(compact('finalItem', 'store_result'));
            $this->loadModel('Category');
            $this->Category->bindModel(
                    array('hasMany' => array(
                            'Item' => array(
                                'className' => 'Item',
                                'foreignKey' => 'category_id',
                                'fields' => array('id', 'name', 'category_id', 'start_date', 'end_date', 'image', 'description', 'is_seasonal_item', 'position'),
                                'conditions' => array('Item.is_active' => 1, 'Item.is_deleted' => 0),
                                'order' => array('position' => 'asc')
                            )
                        )
            ));
            $total_price = 0;
            $delivery_address_id = "";


            if ($this->Session->check('Order.delivery_address_id')) {
                $delivery_address_id = $this->Session->read('Order.delivery_address_id');
            } else {
                $this->Session->delete('Order.delivery_address_id');
            }


            foreach ($finalItem as $total) {
                $segment_type = @$total['order_type'];
                $total_price = $total_price + $total['Item']['final_price'];
            }
            $userid = "";
            $delivery_address = "";
            if ($delivery_address_id) {
                $this->loadModel('DeliveryAddress');
                $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
                $delivery_address = $this->DeliveryAddress->fetchAddress($delivery_address_id, $userid, $decrypt_storeId);
            }
            $service_fee = "";
            $service_fee = $this->Session->read('final_service_fee');
            if ($service_fee) {
                $total_price = $total_price + $service_fee;
            }
//            $delivery_fee = "";
//            $delivery_fee = $this->Session->read('delivery_fee');
//            if ($delivery_fee) {
//                $total_price = $total_price + $delivery_fee;
//            }
            $total_price = number_format($total_price, 2);
            $this->Session->write('Cart.grand_total_final', $total_price); // It will give the final totoal with all taxes
            $this->Session->write('Cart.segment_type', $segment_type);
            $categoryList = $this->Category->findCategotyList($decrypt_storeId, $decrypt_merchantId); // It will find the list of categories of the menus
            //$categoryList =$this->Category->find('all',array('fields'=>array('is_active','position','id','name','store_id','start_time','end_time','imgcat','is_meal','has_topping','is_sizeonly'),'conditions'=>array('Category.store_id'=>$decrypt_storeId,'Category.is_active'=>1,'Category.is_deleted'=>0),'order' => array('Category.position' => 'ASC','Item.position' => 'ASC')));
            $this->loadModel('DeliveryAddress');
            $userId = AuthComponent::User('id'); // Customer Id
            $roleId = AuthComponent::User('role_id');
            $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
            $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $decrypt_storeId, $decrypt_merchantId); // It will call the function in the model to check the address either exist or not
            if (!$checkaddress) {
                $checkaddress = array();
            }
            if ($categoryList) {
                $this->set(compact('categoryList', 'finalItem', 'decrypt_storeId', 'decrypt_merchantId', 'encrypted_storeId', 'encrypted_merchantId', 'delivery_address', 'checkaddress'));
            }
            $this->NZGateway->setLogin($store_result['Store']['api_username'], $store_result['Store']['api_password']);
            if (!empty($nzsafe_info)) {
                $response = $this->NZGateway->getVault($nzsafe_info["customer_vault_id"]);
                if (count($response) === 0) {
                    $nzsafe_info = array();
                }
            }
            if ($this->Session->check('ordersummary.delivery_address_id')) {
                $addressIds = $this->Session->read('ordersummary.delivery_address_id');
                $count = $this->DeliveryAddress->find('count', array('conditions' => array('DeliveryAddress.user_id' => $userId, 'DeliveryAddress.id' => $addressIds, 'DeliveryAddress.is_deleted' => 1)));
                if ($count > 0) {
                    $this->Session->delete('ordersummary');
                }
            }
            $this->set(compact('nzsafe_info'));
        } else {
            $this->redirect(array('contoller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId));
        }
        $this->loadModel('StoreSetting');
        $storeSetting = $this->StoreSetting->findByStoreId($decrypt_storeId);
        $this->set('storeSetting', $storeSetting);
        //pr($this->Session->read());
    }

    /* ------------------------------------------------
      Function name:removeOrderItem()
      Description:It will remove item from order detail page
      created:23/09/2015
      ----------------------------------------------------- */

    public function removeOrderItem() {
        if ($this->request->is('ajax')) {
            $data = $this->Session->read('cart.' . $_POST['index_id']);
            $this->Session->delete('cart.' . $_POST['index_id']);
            $finalItem = $this->Session->read('cart');
            if (empty($finalItem)) {
                $this->Session->delete('Cart');
                $this->Session->delete('cart');
                $this->Session->delete('Coupon');
                $this->Session->delete('Discount');
                $this->set(compact('finalItem'));
            } else {
                $total_price = $this->Session->read('Cart.grand_total_final');
                $total_price = $total_price - $data['Item']['final_price'];
                $this->Session->write('Cart.grand_total_final', number_format($total_price, 2)); // It will give the final totoal with all taxes
                $decrypt_storeId = $this->Session->read('store_id');
                $this->loadModel('StoreSetting');
                $storeSetting = $this->StoreSetting->findByStoreId($decrypt_storeId);

                $this->set(compact('finalItem', 'storeSetting'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:removeOrderOfferItem()
      Description:It will remove offer item from order detail page
      created:23/09/2015
      ----------------------------------------------------- */

    public function removeOrderOfferItem() {
        if ($this->request->is('ajax')) {
            $present_item = $this->Session->read('cart.' . $_POST['cart_index_id']);
            $remove_quantity = $present_item['Item']['StoreOffer'][$_POST['offer_index_id']]['quantity'];
            $remove_price = $present_item['Item']['StoreOffer'][$_POST['offer_index_id']]['offer_price'];
            $actual_price = $present_item['Item']['final_price'];
            unset($present_item['Item']['StoreOffer'][$_POST['offer_index_id']]); // remove item at index 0
            $present_item['Item']['StoreOffer'] = array_values($present_item['Item']['StoreOffer']); // 'reindex' array
            $prefix = '';
            $offerItemName = '';
            $offerType = $present_item['Item']['OfferType'];
            if (!empty($present_item['Item']['StoreOffer'])) {
                foreach ($present_item['Item']['StoreOffer'] as $key => $name) {
                    if ($name['offer_price'] == 0) {
                        if ($offerType == 1) {
                            $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $name['quantity'] . ' X ' . $name['Item_name'] . '</a>';
                        } else {
                            $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $name['quantity'] . ' X ' . $name['Item_name'] . '@ Free </a>';
                        }
                    } else {
                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $name['quantity'] . ' X ' . $name['Item_name'] . ' @ $' . $name['quantity'] * $name['offer_price'] . '</a>';
                    }
                    $prefix = '<br/> ';
                }
                $this->Session->write('cart.' . $_POST['cart_index_id'] . '.Item.StoreOffer', $present_item['Item']['StoreOffer']);
                $this->Session->write('cart.' . $_POST['cart_index_id'] . '.Item.OfferItemName', $offerItemName);
                $total = $actual_price - ($remove_price * $remove_quantity);
            } else {
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.OfferType');
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.OfferItemUnit');
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.OfferItemName');
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.OfferItemPrice');
                $this->Session->delete('cart.' . $_POST['cart_index_id'] . '.Item.StoreOffer');
                $total = $present_item['Item']['price'] * $present_item['Item']['quantity'];
            }

            $this->Session->write('cart.' . $_POST['cart_index_id'] . '.Item.final_price', $total);
            $difference = $present_item['Item']['final_price'] - $total;
            $total_price = $this->Session->read('Cart.grand_total_final');
            $total_price = $total_price - $difference;
            $finalItem = $this->Session->read('cart');
            $this->set(compact('finalItem'));
        }
    }

    /* ------------------------------------------------
      Function name:cancelOffer()
      Description:It will remove the offer cycle
      created:14/8/2015
      ----------------------------------------------------- */

    public function cancelOffer() {
        if ($this->request->is('ajax')) {
            if ($this->Session->read('CartOffer')) {
                $this->Session->delete('CartOffer');
            }
        }
    }

    /* ------------------------------------------------
      Function name:reorder()
      Description: Used for re-order cycle
      created:17/8/2015
      ----------------------------------------------------- */

    public function reorder() {
        $this->layout = false;
        $this->autoRender = false;
        $decrypted_orderId = $this->Encryption->decode($_POST['orderId']);
        $this->loadModel('Order');
        $this->loadModel('OrderItem');
        $this->loadModel('Item');
        $this->loadModel('ItemPrice');
        $this->loadModel('ItemType');
        $this->loadModel('OrderOffer');
        $this->loadModel('Topping');
        $this->loadModel('StoreTax');
        $this->loadModel('Type');
        $this->loadModel('SubPreference');
        $this->loadModel('OrderPreference');
        $this->Session->delete('cart');
        $this->Session->delete('CartOffer');

        $this->OrderItem->bindModel(
                array(
            'hasMany' => array(
                'OrderOffer' => array('fields' => array('id'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)),
                'OrderTopping' => array('fields' => array('id', 'topping_id', 'topType', 'addon_size_id'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)),
                'OrderPreference' => array('fields' => array('id', 'order_item_id', 'sub_preference_id', 'order_id', 'size'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0))
            ),
            'belongsTo' => array(
                'Item' => array('foreignKey' => 'item_id', 'fields' => array('id'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)),
                'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name', 'price'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)),
                'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)))
                ), false);

        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)))), false);
        $myOrders = $this->Order->getOrderById($decrypted_orderId);
        $count = 0;
        $activeItem = 0;
        $taxprice = 0;
        $SessionItem = array();
        if ($myOrders['Order']['is_future_order'] == 1) {
            $this->Session->write('FutureOrderId', $myOrders['Order']['id']);
        }
        foreach ($myOrders['OrderItem'] as $order) {
            $this->ItemPrice->bindModel(
                    array('belongsTo' => array(
                    'StoreTax' => array(
                        'className' => 'StoreTax',
                        'foreignKey' => 'store_tax_id',
                        'conditions' => array('StoreTax.is_active' => 1, 'StoreTax.is_deleted' => 0, 'StoreTax.store_id' => $this->Session->read('store_id'))
                    )
                )), false);


            $this->Item->bindModel(
                    array('hasMany' => array(
                    'ItemPrice' => array(
                        'className' => 'ItemPrice',
                        'foreignKey' => 'item_id',
                        'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $this->Session->read('store_id'))
                    ),
                    'ItemType' => array(
                        'className' => 'ItemType',
                        'foreignKey' => 'item_id',
                        'conditions' => array('ItemType.is_active' => 1, 'ItemType.is_deleted' => 0, 'ItemType.store_id' => $this->Session->read('store_id'))
                    )
                    , 'ItemDefaultTopping' => array(
                        'className' => 'ItemDefaultTopping',
                        'foreignKey' => 'item_id',
                        'conditions' => array('ItemDefaultTopping.store_id' => $this->Session->read('store_id'), 'ItemDefaultTopping.is_active' => 1, 'ItemDefaultTopping.is_deleted' => 0)
                    )
                )
                    ), false);
            if (!empty($order['Item']['id'])) {
                $ordItem = $this->Item->getItemById($order['Item']['id']);
            } else {
                $ordItem = array();
            }

            if (empty($ordItem)) {
                $activeItem = 1;
            } else {
                if ($ordItem['Item']['is_seasonal_item'] == 1) {
                    //$date = date('Y-m-d');
                    $date = $this->Common->gettodayDate();
                    if (($ordItem['Item']['start_date'] <= $date) && ($ordItem['Item']['end_date'] >= $date)) {
                        $activeItem = 1;
                    }
                } else {
                    if (!empty($order['OrderOffer'])) {
                        $SessionItem[$count]['isOffer'] = 1;
                    } else {
                        $SessionItem[$count]['isOffer'] = 0;
                    }
                    $SessionItem[$count]['itemId'] = $ordItem['Item']['id'];
                    $SessionItem[$count]['itemTaxValue'] = $ordItem['Item']['id'];
                    $SessionItem[$count]['categoryId'] = $ordItem['Item']['category_id'];
                    $SessionItem[$count]['itemName'] = $ordItem['Item']['name'];
                    $SessionItem[$count]['isDeliverable'] = $ordItem['Item']['is_deliverable'];
                    $SessionItem[$count]['price'] = $ordItem['ItemPrice'][0]['price'];

                    if (!empty($ordItem['ItemPrice'][0]['StoreTax'])) {
                        $SessionItem[$count]['itemTaxAmount'] = ($ordItem['ItemPrice'][0]['StoreTax']['tax_value'] / 100) * $ordItem['ItemPrice'][0]['price'];
                        $SessionItem[$count]['itemTaxValue'] = $ordItem['ItemPrice'][0]['StoreTax']['tax_value'];
                    } else {
                        $SessionItem[$count]['itemTaxAmount'] = 0;
                        $SessionItem[$count]['itemTaxValue'] = 0;
                    }

                    $SessionItem[$count]['quantity'] = $order['quantity'];
                    if (!empty($order['Size'])) {

                        $ordSize = $this->ItemPrice->getSizeById($order['Size']['id'], $order['Item']['id']);
                        if (empty($ordSize)) {
                            $activeItem = 1;
                            $SessionItem[$count]['sizeId'] = 0;
                        } else {
                            $SessionItem[$count]['sizeId'] = $ordSize['ItemPrice']['id'];
                            $SessionItem[$count]['price'] = $ordSize['ItemPrice']['price'];
                            //if(!empty($ordSize['ItemPrice']['StoreTax'])){
                            //    $taxprice = ($ordSize['ItemPrice']['StoreTax']['tax_value'] / 100) * $ordSize['ItemPrice']['price'];
                            //}
                            //$SessionItem[$count]['price'] = $taxprice + $ordSize['ItemPrice']['price'];
                        }
                        $SessionItem[$count]['sizeeId'] = $order['Size']['id'];
                        $SessionItem[$count]['sizeeName'] = $order['Size']['size'];
                    } else {
                        $SessionItem[$count]['sizeId'] = 0;
                        $SessionItem[$count]['sizeeId'] = 0;
                        $SessionItem[$count]['sizeeName'] = 0;
                    }
                    if (!empty($order['Type'])) {
                        $this->ItemType->bindModel(
                                array('belongsTo' => array(
                                        'Type' => array(
                                            'className' => 'Type',
                                            'foreignKey' => 'type_id',
                                            'conditions' => array('Type.is_active' => 1, 'Type.is_deleted' => 0, 'Type.store_id' => $this->Session->read('store_id'))
                                        )
                        )));
                        $ordType = $this->ItemType->getTypeById($order['Type']['id'], $order['Item']['id']);
                        if (empty($ordType)) {
                            $activeItem = 1;
                            $SessionItem[$count]['typeId'] = 0;
                            $SessionItem[$count]['typeName'] = 0;
                            $SessionItem[$count]['typePrice'] = 0;
                        } else {
                            $SessionItem[$count]['typeId'] = $order['Type']['id'];
                            $SessionItem[$count]['typeName'] = $order['Type']['name'];
                            $SessionItem[$count]['typePrice'] = $order['Type']['price'];
                        }
                    } else {
                        $SessionItem[$count]['typeId'] = 0;
                        $SessionItem[$count]['typeName'] = 0;
                        $SessionItem[$count]['typePrice'] = 0;
                    }

                    $default_array = array();
                    if (!empty($ordItem['ItemDefaultTopping'])) {
                        foreach ($ordItem['ItemDefaultTopping'] as $topping) {
                            $default_array[] = $topping['topping_id'];
                        }
                    }

                    $top_count = 0;
                    if (!empty($order['OrderTopping'])) {
                        foreach ($order['OrderTopping'] as $topping) {
                            $topType = $this->Topping->getToppingById($topping['topping_id'], $order['Item']['id']);
                            if (empty($topType)) {
                                $activeItem = 1;
                                $SessionItem[$count]['Topping'][$top_count]['topId'] = 0;
                                $SessionItem[$count]['Topping'][$top_count]['topPrice'] = 0;
                                $SessionItem[$count]['Topping'][$top_count]['topName'] = 0;
                                $SessionItem[$count]['Topping'][$top_count]['topSize'] = 0;
                                $SessionItem[$count]['Topping'][$top_count]['topType'] = 0;
                            } else {
                                if ($topping['topType'] == 'defaultTop') {
                                    if (in_array($topType['Topping']['id'], $default_array)) {
                                        $SessionItem[$count]['Topping'][$top_count]['topId'] = $topType['Topping']['id'];
                                        $SessionItem[$count]['Topping'][$top_count]['topPrice'] = $topType['Topping']['price'];
                                        $SessionItem[$count]['Topping'][$top_count]['topName'] = $topType['Topping']['name'];
                                        $SessionItem[$count]['Topping'][$top_count]['topSize'] = $topping['addon_size_id'];
                                        $SessionItem[$count]['Topping'][$top_count]['topType'] = 1;
                                    } else {
                                        $SessionItem[$count]['Topping'][$top_count]['topId'] = $topType['Topping']['id'];
                                        $SessionItem[$count]['Topping'][$top_count]['topPrice'] = $topType['Topping']['price'];
                                        $SessionItem[$count]['Topping'][$top_count]['topName'] = $topType['Topping']['name'];
                                        $SessionItem[$count]['Topping'][$top_count]['topSize'] = $topping['addon_size_id'];
                                        $SessionItem[$count]['Topping'][$top_count]['topType'] = 2;
                                    }
                                } else {
                                    $SessionItem[$count]['Topping'][$top_count]['topId'] = $topType['Topping']['id'];
                                    $SessionItem[$count]['Topping'][$top_count]['topPrice'] = $topType['Topping']['price'];
                                    $SessionItem[$count]['Topping'][$top_count]['topName'] = $topType['Topping']['name'];
                                    $SessionItem[$count]['Topping'][$top_count]['topSize'] = $topping['addon_size_id'];
                                    $SessionItem[$count]['Topping'][$top_count]['topType'] = 2;
                                }
                            }
                            $top_count++;
                        }
                    } else {
                        $SessionItem[$count]['Topping'][$top_count]['topId'] = 0;
                        $SessionItem[$count]['Topping'][$top_count]['topPrice'] = 0;
                        $SessionItem[$count]['Topping'][$top_count]['topName'] = 0;
                        $SessionItem[$count]['Topping'][$top_count]['topSize'] = 0;
                        $SessionItem[$count]['Topping'][$top_count]['topType'] = 0;
                    }


                    $pre_count = 0;
                    if (!empty($order['OrderPreference'])) {
                        foreach ($order['OrderPreference'] as $preference) {
                            $preData = $this->SubPreference->getSubPreferenceDetail($preference['sub_preference_id'], $this->Session->read('store_id'));
                            if ($preData) {
                                $SessionItem[$count]['Subpreference'][$pre_count]['preId'] = $preData['SubPreference']['id'];
                                $SessionItem[$count]['Subpreference'][$pre_count]['prePrice'] = $preData['SubPreference']['price'];
                                $SessionItem[$count]['Subpreference'][$pre_count]['preName'] = $preData['SubPreference']['name'];
                                $SessionItem[$count]['Subpreference'][$pre_count]['preSize'] = $preference['size'];
                                $SessionItem[$count]['Subpreference'][$pre_count]['preType'] = $preData['SubPreference']['type_id'];
                            } else {
                                $SessionItem[$count]['Subpreference'][$pre_count]['preId'] = 0;
                                $SessionItem[$count]['Subpreference'][$pre_count]['prePrice'] = 0;
                                $SessionItem[$count]['Subpreference'][$pre_count]['preName'] = 0;
                                $SessionItem[$count]['Subpreference'][$pre_count]['preSize'] = 0;
                                $SessionItem[$count]['Subpreference'][$pre_count]['preType'] = 0;
                            }
                            $pre_count++;
                        }
                    } else {
                        $SessionItem[$count]['Subpreference'][$pre_count]['preId'] = 0;
                        $SessionItem[$count]['Subpreference'][$pre_count]['prePrice'] = 0;
                        $SessionItem[$count]['Subpreference'][$pre_count]['preName'] = 0;
                        $SessionItem[$count]['Subpreference'][$pre_count]['preSize'] = 0;
                        $SessionItem[$count]['Subpreference'][$pre_count]['preType'] = 0;
                    }
                }
            }
            $count++;
        }
        //pr($SessionItem);
        //die();
        $this->Session->write('reOrder', $SessionItem);
        $data['item'] = $activeItem;
        $data['count'] = $count;
        return json_encode($data);
    }

    /* ------------------------------------------------
      Function name:fetchReorderProduct()
      Description: Used for re-order cycle
      created:17/8/2015
      ----------------------------------------------------- */

    public function fetchReorderProduct() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            if ($this->Session->read('reOrder')) {
                $data = $this->Session->read('reOrder');
                $count = 0;
                foreach ($data as $redata) {
                    $this->Session->delete('Order.Item');
                    $itemId = $redata['itemId'];
                    $categoryId = $redata['categoryId'];
                    $itemName = $redata['itemName'];
                    $deliver_check = $redata['isDeliverable'];
                    if ($redata['typeId'] != 0) {
                        $default_price = $redata['price'] + $redata['typePrice'];
                    } else {
                        $default_price = $redata['price'];
                    }
                    $default_quantity = 1;
                    $this->Session->write('Order.Item.SizePrice', $redata['price']);
                    $this->Session->write('Order.Item.size_id', $redata['sizeeId']);
                    $this->Session->write('Order.Item.TypePrice', $redata['typePrice']);
                    $this->Session->write('Order.Item.quantity', $default_quantity);
                    $this->Session->write('Order.Item.actual_price', $default_price);
                    $this->Session->write('Order.Item.is_deliverable', $deliver_check);
                    $this->Session->write('Order.Item.id', $itemId);
                    $this->Session->write('Order.Item.name', $itemName);
                    $this->Session->write('Order.Item.categoryid', $categoryId);
                    $this->Session->write('Order.Item.price', $default_price);
                    $this->Session->write('Order.Item.final_price', $default_price);

                    $topping = array();
                    $this->loadModel('AddonSize');
                    $this->loadModel('Topping');

                    $default_check = array();
                    $paid_check = array();


                    $i = 0;
                    $j = 0;
                    $this->loadModel('Item');
                    $item_result = $this->Item->find('first', array('fields' => array('default_subs_price'), 'conditions' => array('id' => $this->Session->read('Order.Item.id'))));
                    $default_subs_price = $item_result['Item']['default_subs_price'];
                    foreach ($redata['Topping'] as $top) {
                        if ($this->Topping->getToppingById($top['topId'], $itemId)) {
                            if ($top['topId'] != 0) {
//                                if ($top['topSize'] == 0) {
//                                    $pricePercentage['AddonSize']['price_percentage'] = 100;
//                                } else {
//                                    $pricePercentage = $this->AddonSize->fetchAddonPercentage($top['topSize'], $this->Session->read('store_id'));
//                                }

                                $item_price = $this->Session->read('Order.Item.price');
                                $storeId = $this->Session->read('store_id');

                                if ($this->Session->check('Order.Item.size_id') && $default_subs_price == 0) {
                                    $this->Topping->bindModel(
                                            array('hasMany' => array(
                                                    'ToppingPrice' => array(
                                                        'className' => 'ToppingPrice',
                                                        'foreignKey' => 'topping_id',
                                                        'conditions' => array('ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0, 'ToppingPrice.store_id' => $storeId, 'ToppingPrice.size_id' => $this->Session->read('Order.Item.size_id')),
                                                        'fields' => array('ToppingPrice.id', 'ToppingPrice.store_id', 'ToppingPrice.item_id', 'ToppingPrice.size_id', 'ToppingPrice.topping_id', 'ToppingPrice.price')
                                                    )
                                                )
                                            )
                                    );
                                    $price = $this->Topping->fetchToppingPrice($this->Session->read('Order.Item.id'), $top['topId'], $storeId);

                                    $price_topping = 0;
                                    if (isset($price['ToppingPrice']) && !empty($price['ToppingPrice'])) {
                                        $price_topping = $price['ToppingPrice'][0]['price'];
                                    }
                                } else {
                                    $price = $this->Topping->fetchToppingPrice($this->Session->read('Order.Item.id'), $top['topId'], $storeId);

                                    $price_topping = 0;
                                    if (isset($price['Topping']['price']) && !empty($price['Topping']['price'])) {
                                        $price_topping = $price['Topping']['price'];
                                    }
                                }


                                //$price_topping = $top['topPrice'];
                                $new_topping_price = $price_topping * $top['topSize'];

                                if ($top['topType'] == 1) {
                                    $new_topping_price = 0;
                                }

                                $new_price = $item_price + $new_topping_price;

                                $this->Session->write('Order.Item.price', $new_price);
                                $this->Session->write('Order.Item.final_price', $new_price);

                                if ($this->Session->check('Order.Item.topping_total')) {
                                    $previous = $this->Session->read('Order.Item.topping_total');
                                    $topping_total = $previous + $new_topping_price;
                                    $this->Session->write('Order.Item.topping_total', $topping_total);
                                } else {
                                    $this->Session->write('Order.Item.topping_total', $new_topping_price);
                                }
                                if ($top['topType'] == 1) {
                                    if ($top['topSize'] == 1) {
                                        $default_check[$j]['id'] = $top['topId'];
                                        $default_check[$j]['size'] = $top['topSize'];
                                        $default_check[$j]['name'] = $top['topName'];
                                        $default_check[$j]['price'] = $price_topping;
                                        $j++;
                                    } else {
                                        $paid_check[$i]['id'] = $top['topId'];
                                        $paid_check[$i]['size'] = $top['topSize'];
                                        $paid_check[$i]['name'] = $top['topName'];
                                        $paid_check[$i]['price'] = $price_topping;
                                        $paid_name[$top['topName']] = $new_topping_price;
                                        $i++;
                                    }
                                } else {
                                    $paid_check[$i]['id'] = $top['topId'];
                                    $paid_check[$i]['size'] = $top['topSize'];
                                    $paid_check[$i]['name'] = $top['topName'];
                                    $paid_check[$i]['price'] = $price_topping;
                                    $paid_name[$top['topName']] = $new_topping_price;
                                    $i++;
                                }
                            }

                            if (!empty($default_check)) {
                                $this->Session->write('Order.Item.default_topping', $default_check);
                            }
                            if (!empty($paid_check)) {
                                $this->Session->write('Order.Item.paid_topping', $paid_check);
                                $this->Session->write('Order.Item.PaidTopping', $paid_name);
                            }
                        }
                    }


                    if ($redata['isOffer'] == 1) {
                        $this->loadModel('Offer');
                        $this->loadModel('OfferDetail');
                        $this->Offer->bindModel(array('hasMany' => array('OfferDetail')));
                        if ($redata['sizeeId'] == 0) {
                            $offer_result = $this->Offer->offerOnItem($itemId);
                        } else {
                            $offer_result = $this->Offer->offerOnItemSize($itemId, $redata['sizeeId']);
                        }
                        if (!empty($offer_result)) {
                            if ($redata['quantity'] >= $offer_result['Offer']['unit']) {
                                $this->Session->write('Offer', $offer_result['Offer']);
                            }
                        }
                        if ($this->Session->check('Offer')) {
                            $is_offer = $this->Session->read('Offer');
                            $this->OfferDetail->bindModel(
                                    array('belongsTo' => array(
                                            'Item' => array(
                                                'className' => 'Item',
                                                'foreignKey' => 'offerItemID',
                                            ),
                                            'Size' => array(
                                                'className' => 'Size',
                                                'foreignKey' => 'offerSize',
                                            ),
                                            'Type' => array(
                                                'className' => 'Type',
                                                'foreignKey' => 'offerItemType',
                                            )
                            )));
                            $this->Offer->bindModel(
                                    array('hasMany' => array(
                                            'OfferDetail' => array(
                                                'className' => 'OfferDetail',
                                                'foreignKey' => 'offer_id',
                                                'conditions' => array('OfferDetail.is_deleted' => 0, 'OfferDetail.is_active' => 1)
                                            )),
                                        'belongsTo' => array(
                                            'Item' => array(
                                                'className' => 'Item',
                                                'foreignKey' => 'item_id',
                                            )),
                            ));
                            $getOffer = $this->Offer->getOfferDetails($is_offer['id']);
                            $this->Session->delete('Offer');
                            $cart_offer = $this->Session->write('CartOffer', $getOffer);
                        }
                    }

                    $cart_array = array();
                    if ($redata['typeId'] != 0) {
                        $this->Session->write('Order.Item.type', $redata['typeName']);
                        $this->Session->write('Order.Item.type_id', $redata['typeId']);
                    }

                    if ($redata['sizeeId'] != 0) {
                        $this->Session->write('Order.Item.size', $redata['sizeeName']);
                        $this->Session->write('Order.Item.size_id', $redata['sizeeId']);
                    }

                    $order_segment = "";
                    $preOrderCheck = "";

                    if ($this->Session->read('Order')) { //Here we are checking the Order type
                        $order_segment = $this->Session->read('Order.order_type');
                        $preOrderCheck = $this->Session->read('Order.is_preorder');
                    }

                    if ($order_segment == 2 || $order_segment == 3) {
                        if ($preOrderCheck == 0) {
                            //$orderTime = date('Y-m-d') . " " . $this->Session->read('Order.store_pickup_time');
                            $orderTime = $this->Common->gettodayDate() . " " . $this->Session->read('Order.store_pickup_time');
                        } else {
                            $order_date = $this->Session->read('Order.store_pickup_date');
                            $order_time = $this->Session->read('Order.store_pickup_time');
                            $orderDate = $this->Dateform->formatDate($order_date);
                            $orderpassedTime = $order_time;
                            $orderTime = $orderDate . " " . $orderpassedTime;
                        }
                        $this->Session->write('Cart.order_time', $orderTime);
                    }
                    $current_order = $this->Session->read('Order');

                    if ($this->Session->read('cart')) {
                        $old_array = $this->Session->read('cart');
                        $exist_id = array();
                        foreach ($old_array as $itemcheck) {
                            $exist_id[] = @$itemcheck['Item']['id'];
                        }
                        $old_array[] = $current_order;
                        $this->Session->write('cart', $old_array);
                        if ($this->Session->read('CartOffer')) {
                            $storeOfferArray = array();
                            $offer_array = $this->Session->read('CartOffer');
                            $offerPrice = 0;
                            $offerItemName = '';
                            $prefix = '';
                            $i = 0;
                            foreach ($offer_array['OfferDetail'] as $off) {
                                $offerType = 1;
                                if ($offer_array['Offer']['is_fixed_price'] == 1) {
                                    if ($offer_array['Offer']['offerprice'] == 0) {
                                        $offerPrice = 0;
                                        $rate = 0;
                                    } else {
                                        $offerPrice = $offer_array['Offer']['offerprice'];
                                        $rate = 0;
                                    }
                                } elseif ($offer_array['Offer']['is_fixed_price'] == 0) {
                                    $offerType = 0;
                                    if ($off['discountAmt'] == 0) {
                                        $offerPrice = $offerPrice + 0;
                                        $rate = 0;
                                    } else {
                                        $offerPrice = $offerPrice + $off['discountAmt'];
                                        $rate = $off['discountAmt'];
                                    }
                                }
                                if ($rate == 0) {
                                    if ($offerType == 1) {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $i . '><b><i class="fa fa-times"></i></b> ' . $off['quantity'] . ' X ' . $off['Item']['name'] . '</a>';
                                    } else {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $i . '><b><i class="fa fa-times"></i></b> ' . $off['quantity'] . ' X ' . $off['Item']['name'] . ' @ Free </a>';
                                    }
                                } else {
                                    $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $i . '><b><i class="fa fa-times"></i></b> ' . $off['quantity'] . ' X ' . $off['Item']['name'] . ' @ $' . $rate . '</a>';
                                }
                                $offerItemUnit = $offer_array['Offer']['unit'];
                                $prefix = '<br/> ';
                                $storeOfferArray[$i]['offer_id'] = $offer_array['Offer']['id'];
                                $storeOfferArray[$i]['offered_item_id'] = $off['offerItemID'];
                                $storeOfferArray[$i]['offered_size_id'] = $off['offerSize'];
                                $storeOfferArray[$i]['quantity'] = $off['quantity'];
                                $storeOfferArray[$i]['Item_name'] = $off['Item']['name'];
                                $storeOfferArray[$i]['offer_price'] = $rate;
                                $i++;
                            }
                            foreach ($old_array as $key => $old) {
                                if (($offer_array['Offer']['item_id'] == $old['Item']['id'])) {
                                    $old_array[$key]['Item']['OfferItemName'] = $offerItemName;
                                    $old_array[$key]['Item']['OfferItemPrice'] = $offerPrice;
                                    $old_array[$key]['Item']['OfferType'] = $offerType;
                                    $old_array[$key]['Item']['OfferItemUnit'] = $offerItemUnit;
                                    $old_array[$key]['Item']['StoreOffer'] = $storeOfferArray;
                                    if ($offerType == 1) {
                                        $old_array[$key]['Item']['final_price'] = $offerPrice;
                                    } else {
                                        $old_array[$key]['Item']['final_price'] = $old['Item']['final_price'] + $offerPrice;
                                    }
                                }
                            }
                            $this->Session->delete('CartOffer');
                            $this->Session->write('cart', $old_array);
                        }
                    } else {
                        if ($current_order) {
                            $cart_array[] = $current_order;
                        }
                        $this->Session->write('cart', $cart_array);
                        $old_array = $this->Session->read('cart');
                        if ($this->Session->read('CartOffer')) {
                            $storeOfferArray = array();
                            $offer_array = $this->Session->read('CartOffer');
                            $offerPrice = 0;
                            $offerItemName = '';
                            $prefix = '';
                            $i = 0;
                            foreach ($offer_array['OfferDetail'] as $off) {
                                $offerType = 1;
                                if ($offer_array['Offer']['is_fixed_price'] == 1) {
                                    if ($offer_array['Offer']['offerprice'] == 0) {
                                        $offerPrice = 0;
                                        $rate = 0;
                                    } else {
                                        $offerPrice = $offer_array['Offer']['offerprice'];
                                        $rate = 0;
                                    }
                                } elseif ($offer_array['Offer']['is_fixed_price'] == 0) {
                                    $offerType = 0;
                                    if ($off['discountAmt'] == 0) {
                                        $offerPrice = $offerPrice + 0;
                                        $rate = 0;
                                    } else {
                                        $offerPrice = $offerPrice + $off['discountAmt'];
                                        $rate = $off['discountAmt'];
                                    }
                                }
                                if ($rate == 0) {
                                    if ($offerType == 1) {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $i . '><b><i class="fa fa-times"></i></b> ' . $off['quantity'] . ' X ' . $off['Item']['name'] . '</a>';
                                    } else {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $i . '><b><i class="fa fa-times"></i></b> ' . $off['quantity'] . ' X ' . $off['Item']['name'] . ' @ Free </a>';
                                    }
                                } else {
                                    $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $i . '><b><i class="fa fa-times"></i></b> ' . $off['quantity'] . ' X ' . $off['Item']['name'] . ' @ $' . $rate . '</a>';
                                }
                                $offerItemUnit = $offer_array['Offer']['unit'];
                                $prefix = '<br/> ';
                                $storeOfferArray[$i]['offer_id'] = $offer_array['Offer']['id'];
                                $storeOfferArray[$i]['offered_item_id'] = $off['offerItemID'];
                                $storeOfferArray[$i]['offered_size_id'] = $off['offerSize'];
                                $storeOfferArray[$i]['quantity'] = $off['quantity'];
                                $storeOfferArray[$i]['Item_name'] = $off['Item']['name'];
                                $storeOfferArray[$i]['offer_price'] = $rate;
                                $i++;
                            }
                            foreach ($old_array as $key => $old) {
                                if (($offer_array['Offer']['item_id'] == $old['Item']['id'])) {
                                    $old_array[$key]['Item']['OfferItemName'] = $offerItemName;
                                    $old_array[$key]['Item']['OfferItemPrice'] = $offerPrice;
                                    $old_array[$key]['Item']['OfferType'] = $offerType;
                                    $old_array[$key]['Item']['OfferItemUnit'] = $offerItemUnit;
                                    $old_array[$key]['Item']['StoreOffer'] = $storeOfferArray;
                                    if ($offerType == 1) {
                                        $old_array[$key]['Item']['final_price'] = $offerPrice;
                                    } else {
                                        $old_array[$key]['Item']['final_price'] = $old['Item']['final_price'] + $offerPrice;
                                    }
                                }
                            }
                            $this->Session->delete('CartOffer');
                            $this->Session->write('cart', $old_array);
                        }
                    }
                    $item = $redata['quantity'];
                    $present_item = $this->Session->read('cart.' . $count);


                    $intervalPrice = $this->getTimeIntervalPrice($present_item['Item']['id'], $present_item['Item']['size_id']);
                    if (!empty($intervalPrice) && !empty($intervalPrice['IntervalPrice'])) {
                        $present_item['Item']['price'] = $intervalPrice['IntervalPrice']['price'];
                    }

                    if (isset($present_item['Item']['OfferItemPrice'])) {
                        if (((($item) % ($present_item['Item']['OfferItemUnit'])) == 0) && ($present_item['Item']['OfferItemUnit'] < $item)) {
                            $offer_multiply = ($item) / ($present_item['Item']['OfferItemUnit']);
                            $offer_price = $offer_multiply * $present_item['Item']['OfferItemPrice'];
                            if ($present_item['Item']['OfferType'] == 1) {
                                $total = $offer_price;
                            } else {
                                $total = $item * $present_item['Item']['price'];
                                $total = $total + $offer_price;
                            }
                            $prefix = '';
                            $offerItemName = '';
                            foreach ($present_item['Item']['StoreOffer'] as $key => $name) {
                                if ($name['offer_price'] == 0) {
                                    if ($present_item['Item']['OfferType'] == 1) {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '</a>';
                                    } else {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '@ Free </a>';
                                    }
                                } else {
                                    $offerItemName .= $prefix . $offer_multiply . ' X ' . $name['Item_name'] . ' @ $' . $offer_multiply * $name['offer_price'];
                                }
                                $prefix = '<br/> ';
                                $this->Session->write('cart.' . $count . '.Item.StoreOffer.' . $key . '.quantity', $offer_multiply);
                            }
                            $this->Session->write('cart.' . $count . '.Item.OfferItemName', $offerItemName);
                        } else if ($present_item['Item']['OfferItemUnit'] > $item) {

                            $item_price = $present_item['Item']['price'];
                            $total = $item * $item_price;

                            $this->Session->delete('cart.' . $count . '.Item.OfferType');
                            $this->Session->delete('cart.' . $count . '.Item.OfferItemUnit');
                            $this->Session->delete('cart.' . $count . '.Item.OfferItemName');
                            $this->Session->delete('cart.' . $count . '.Item.OfferItemPrice');
                            $this->Session->delete('cart.' . $count . '.Item.StoreOffer');
                        } elseif ($present_item['Item']['OfferItemUnit'] == $item) {
                            $offer_flag = 0;
                            $item_price = $present_item['Item']['price'];
                            if ($present_item['Item']['OfferType'] == 1) {
                                $total = $present_item['Item']['OfferItemPrice'];
                            } else {
                                $total = $item * $item_price;
                                $total = $total + $present_item['Item']['OfferItemPrice'];
                            }

                            $offer_multiply = floor(($item) / ($present_item['Item']['OfferItemUnit']));
                            $prefix = '';
                            $offerItemName = '';
                            foreach ($present_item['Item']['StoreOffer'] as $key => $name) {
                                if ($name['offer_price'] == 0) {
                                    if ($present_item['Item']['OfferType'] == 1) {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '</a>';
                                    } else {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '@ Free </a>';
                                    }
                                } else {
                                    $offerItemName .= $prefix . $offer_multiply . ' X ' . $name['Item_name'] . ' @ $' . $offer_multiply * $name['offer_price'];
                                }
                                $this->Session->write('cart.' . $count . '.Item.StoreOffer.' . $key . '.quantity', $offer_multiply);
                                $prefix = '<br/> ';
                            }
                            $this->Session->write('cart.' . $count . '.Item.OfferItemName', $offerItemName);
                        } else {
                            $item_price = $present_item['Item']['price'];
                            if ($present_item['Item']['OfferType'] == 1) {
                                $offer_multiply = floor(($item) / ($present_item['Item']['OfferItemUnit']));
                                $offer_price = $offer_multiply * $present_item['Item']['OfferItemPrice'];
                                $total = $item_price;
                                $total = $total + $offer_price;
                            } else {
                                $total = $item * $item_price;
                                $total = $total + $present_item['Item']['OfferItemPrice'];
                            }
                            $offer_multiply = floor(($item) / ($present_item['Item']['OfferItemUnit']));
                            $prefix = '';
                            $offerItemName = '';
                            foreach ($present_item['Item']['StoreOffer'] as $key => $name) {
                                if ($name['offer_price'] == 0) {
                                    if ($present_item['Item']['OfferType'] == 1) {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '</a>';
                                    } else {
                                        $offerItemName .= $prefix . '<a href="javascript:void(0)" class="singleItemRemove" value=' . $key . '><b><i class="fa fa-times"></i></b> ' . $offer_multiply . ' X ' . $name['Item_name'] . '@ Free </a>';
                                    }
                                } else {
                                    $offerItemName .= $prefix . $offer_multiply . ' X ' . $name['Item_name'] . ' @ $' . $offer_multiply * $name['offer_price'];
                                }
                                $this->Session->write('cart.' . $count . '.Item.StoreOffer.' . $key . '.quantity', $offer_multiply);
                                $prefix = '<br/> ';
                            }
                            $this->Session->write('cart.' . $count . '.Item.OfferItemName', $offerItemName);
                        }
                    } else {
                        $item_price = $present_item['Item']['price'];
                        $total = $item * $item_price;
                    }
                    $itemprearr = array();
                    $itemsubarr = array();
                    $preferencePrice = 0;





                    foreach ($redata['Subpreference'] as $key => $preference) {
                        if ($present_item['Item']['size_id'] && $default_subs_price == 0) {
                            $mainPreference = $this->getSubPrePriceBySize($present_item['Item']['size_id'], $present_item['Item']['id'], $this->Session->read('store_id'), $preference['preId']);
                            if (empty($mainPreference)) {
                                $mainPreference = $this->getSubPrePrice($present_item['Item']['id'], $this->Session->read('store_id'), $preference['preId']);
                                if (!empty($mainPreference)) {
                                    $mainPreference['SubPreferencePrice']['price'] = '0.00';
                                }
                            }
                            if ($mainPreference) {
                                $itemprearr[$preference['preId']]['id'] = $preference['preId'];
                                $itemprearr[$preference['preId']]['type_id'] = $preference['preType'];
                                $itemprearr[$preference['preId']]['name'] = $preference['preName'];
                                $itemprearr[$preference['preId']]['price'] = $mainPreference['SubPreferencePrice']['price'];
                                $itemprearr[$preference['preId']]['size'] = $preference['preSize'];
                                //$itemsubarr[$preference['preId']] = $mainPreference['SubPreferencePrice']['price'];
                                $preferencePrice = $preferencePrice + ($item * $mainPreference['SubPreferencePrice']['price']);
                            }
                        } else {
                            $mainPreference = $this->getSubPrePrice($present_item['Item']['id'], $this->Session->read('store_id'), $preference['preId']);
                            if ($mainPreference) {
                                $itemprearr[$preference['preId']]['id'] = $preference['preId'];
                                $itemprearr[$preference['preId']]['type_id'] = $preference['preType'];
                                $itemprearr[$preference['preId']]['name'] = $preference['preName'];
                                $itemprearr[$preference['preId']]['price'] = $mainPreference['SubPreference']['price'];
                                $itemprearr[$preference['preId']]['size'] = $preference['preSize'];
                                //$itemsubarr[$preference['preId']] = $mainPreference['SubPreference']['price'];
                                $preferencePrice = $preferencePrice + ($item * $mainPreference['SubPreference']['price']);
                            }
                        }
                        //echo "Preference Data";
                        //pr($preferenceData);
                    }

                    $total = $total + $preferencePrice;
//                    if ($this->Session->check('Order.Item.topping_total')) {
//                        //$total = $total + $this->Session->read('Order.Item.topping_total');
//                    }

                    $taxPrice = 0;
                    if (!empty($redata['itemTaxValue'])) {
                        $taxPrice = ($redata['itemTaxValue'] / 100) * $total;
                    }

                    $this->Session->write('cart.' . $count . '.Item.subpreference', $itemprearr);
                    $this->Session->write('cart.' . $count . '.subPreferenceOld', $itemprearr);
                    $this->Session->write('cart.' . $count . '.Item.taxamount', $taxPrice);
                    $this->Session->write('cart.' . $count . '.Item.taxvalue', $redata['itemTaxValue']);
                    $total = round($total, 2);

                    $this->Session->write('cart.' . $count . '.Item.final_price', $total);
                    $this->Session->write('cart.' . $count . '.Item.quantity', $item);

                    $count++;
                    $final_cart = $this->Session->read('cart');
                }
                //echo "<br>===========================";
                //pr($this->Session->read('cart'));
                //die;
                $this->loadModel('Store');
                $store_result = $this->Store->fetchStoreDetail($this->Session->read('store_id'));
                $this->Session->write('minprice', $store_result['Store']['minimum_order_price']);
                $final_cart = $this->Session->read('cart');
                $this->Session->delete('reOrder');
                echo 1;
            }
        }
    }

    public function getSubPrePriceBySize($sizeID = null, $ItemId = null, $storeId = null, $subId = null) {
        $this->loadModel('SubPreferencePrice');
        $preferenceData = $this->SubPreferencePrice->find('first', array('fields' => array('SubPreferencePrice.id', 'SubPreferencePrice.item_id', 'SubPreferencePrice.size_id', 'SubPreferencePrice.price', 'SubPreferencePrice.sub_preference_id'), 'conditions' => array('SubPreferencePrice.is_active' => 1, 'SubPreferencePrice.is_deleted' => 0, 'SubPreferencePrice.store_id' => $storeId, 'SubPreferencePrice.size_id' => $sizeID, 'SubPreferencePrice.item_id' => $ItemId, 'SubPreferencePrice.sub_preference_id' => $subId)));

        return $preferenceData;
    }

    public function getSubPrePrice($ItemId = null, $storeId = null, $preID = null) {
        $this->loadModel('SubPreference');
        $preferenceData = $this->SubPreference->find('first', array('conditions' => array('SubPreference.is_active' => 1, 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $storeId, 'SubPreference.id' => $preID)));

        return $preferenceData;
    }

    /* ------------------------------------------------
      Function name:fetchCoupon()
      Description: Used for coupon cycle
      created:20/8/2015
      -----------------------------zz------------------------ */

    public function fetchCoupon() {
        $this->layout = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $this->Session->delete('Coupon');
            $couponCode = $_POST['coupon_code'];
            if (empty($couponCode)) {
                $coupon_data = 1;
            } else {
                $this->loadModel('Coupon');
                $storeId = $this->Session->read('store_id');
                $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
                $coupon = $this->Coupon->getValidCoupon($couponCode, $storeId, $current_date);

                if ($coupon) {
                    if ($coupon['Coupon']['number_can_use'] > $coupon['Coupon']['used_count']) {
                        $verify = true;
                        $current_time = date('H:i:s', strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s'))));
                        $current_time = strtotime($current_time);
                        if (!empty($coupon['Coupon']['allow_time'])) {
                            $start_time = strtotime($coupon['Coupon']['start_time']);
                            $end_time = strtotime($coupon['Coupon']['end_time']);
                            if (($current_time >= $start_time) && ($current_time <= $end_time)) {
                                $verify = true;
                            } else {
                                $verify = false;
                            }
                            if (!empty($coupon['Coupon']['days']) && $verify) {
                                $days = explode(',', $coupon['Coupon']['days']);
                                $day_number = date('N', strtotime($current_date));
                                if (!in_array($day_number, $days)) {
                                    $verify = false;
                                }
                            } else {
                                $verify = false;
                            }
                        }
                        if ($verify) {
                            $this->Session->write('Coupon', $coupon);
                            $coupon_data = 3; //Valid
                            $this->set(compact('final_cart'));
                        } else {
                            $coupon_data = 2; //Expire
                        }
                    } else {
                        $coupon_data = 2; //Expire
                    }
                } else {
                    $coupon_data = 1; //Not valid
                }
            }
            $final_cart = $this->Session->read('cart');
            $this->set(compact('final_cart', 'coupon_data'));
        }
    }

    /* ------------------------------------------------
      Function name: menuItems()
      Description: Fetch store menu items
      created: 13/01/2016
      ----------------------------------------------------- */

    public function menuItems($encrypted_storeId = null, $encrypted_merchantId = null, $orderId = null) {

        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $this->loadModel('Category');
        $this->Category->bindModel(
                array('hasMany' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'category_id',
                            'fields' => array('id', 'name', 'category_id', 'start_date', 'end_date', 'image', 'description', 'is_seasonal_item', 'position'),
                            'conditions' => array('Item.is_active' => 1, 'Item.is_deleted' => 0),
                            'order' => array('position' => 'asc')
                        )
                    )
        ));
        $categoryList = $this->Category->findCategotyList($decrypt_storeId, $decrypt_merchantId); // It will find the list of categories of the menus


        if ($categoryList) {
            $this->set(compact('orderId', 'categoryList', 'encrypted_storeId', 'encrypted_merchantId', 'decrypt_storeId'));
        } else {
            $this->set(compact('orderId', 'encrypted_storeId', 'encrypted_merchantId', 'decrypt_storeId'));
        }
        $this->Session->delete('CartOffer');
        if ($this->Session->check('cart')) {
            $final_cart = $this->Session->read('cart');
            $this->set(compact('final_cart'));
        }
    }

    /* ------------------------------------------------
      Function name:fetchProduct()
      Description:It will fetch the item inofrmation under the particular caltegory
      created:22/7/2015
      ----------------------------------------------------- */

    public function fetchProductSize($sizeId = null, $itemId = null, $categoryId = null) {
        $this->layout = "ajax";
//$this->autoRender=false;
        if ($this->request->is('ajax')) {
            $itemId = $_POST['itemId'];
            $categoryId = $_POST['categoryId'];
            $sizeId = $_POST['sizeId'];
        }
        if (!empty($sizeId) && !empty($itemId) && !empty($categoryId)) {
            $decrypt_storeId = $this->Session->read('store_id');
            $storeId = $this->Session->read('store_id');
            $decrypt_merchantId = $this->Session->read('merchant_id');
            $this->Session->delete('FetchProductData');
            $this->Session->write('FetchProductData.itemId', $itemId);
            $this->Session->write('FetchProductData.categoryId', $categoryId);
            $this->Session->write('FetchProductData.storeId', $storeId);
            $this->loadModel("Category");
            $categorySizeType = $this->Category->find('first', array('fields' => 'Category.is_sizeonly', 'conditions' => array('Category.id' => $categoryId)));
            $categorySizeType = $categorySizeType['Category']['is_sizeonly'];

            $this->Session->write('FetchProductData.sizeType', $categorySizeType);
            $this->Session->delete('Order.Item');
            $this->Session->delete('CartOffer');
            $this->Session->delete('OfferAddIndex');
            $this->Session->delete('Offer');
            $this->loadModel('Item');
            $this->loadModel('ItemPrice');
            $this->loadModel('ItemType');
            $this->loadModel('Category');
            $this->loadModel('AddonSize');
            $this->loadModel('Topping');
            $this->loadModel('StoreTax');
            $this->loadModel('SubPreference');
            $this->loadModel('Type');
            $this->loadModel('SubPreferencePrice');
            $this->loadModel('ToppingPrice');
            $this->loadModel('Size');


            //$date = date('Y-m-d');
            $date = $this->Common->gettodayDate();

            $this->SubPreference->bindModel(
                    array('hasOne' => array(
                            'SubPreferencePrice' => array(
                                'className' => 'SubPreferencePrice',
                                'foreignKey' => 'sub_preference_id',
                                'conditions' => array('SubPreferencePrice.is_active' => 1, 'SubPreferencePrice.is_deleted' => 0, 'SubPreferencePrice.store_id' => $decrypt_storeId, 'SubPreferencePrice.size_id' => $sizeId, 'SubPreferencePrice.item_id' => $itemId),
                                'fields' => array('SubPreferencePrice.id', 'SubPreferencePrice.item_id', 'SubPreferencePrice.size_id', 'SubPreferencePrice.price', 'SubPreferencePrice.sub_preference_id')
                            )
                        )
            ));



            $this->Type->bindModel(
                    array('hasMany' => array(
                            'SubPreference' => array(
                                'className' => 'SubPreference',
                                'foreignKey' => 'type_id',
                                'order' => array('SubPreference.position ASC'),
                                'conditions' => array('SubPreference.is_active' => 1, 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $decrypt_storeId),
                                'fields' => array('SubPreference.id', 'SubPreference.name', 'SubPreference.price', 'SubPreference.position')
                            )
                        )
            ));


            $this->ItemType->bindModel(
                    array('belongsTo' => array(
                            'Type' => array(
                                'className' => 'Type',
                                'foreignKey' => 'type_id',
                                'conditions' => array('Type.is_active' => 1, 'Type.is_deleted' => 0, 'Type.store_id' => $decrypt_storeId),
                                'fields' => array('Type.id', 'Type.name', 'Type.price', 'Type.position', 'Type.min_value', 'Type.max_value')
                            )
            )));

            $this->ItemPrice->bindModel(
                    array('belongsTo' => array(
                            'Size' => array(
                                'className' => 'Size',
                                'foreignKey' => 'size_id',
                                'conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $decrypt_storeId),
                                'fields' => array('Size.id', 'Size.size', 'Size.category_id')
                            ),
                            'StoreTax' => array(
                                'className' => 'StoreTax',
                                'foreignKey' => 'store_tax_id',
                                'conditions' => array('StoreTax.is_active' => 1, 'StoreTax.is_deleted' => 0, 'StoreTax.store_id' => $decrypt_storeId),
                                'fields' => array('StoreTax.id', 'StoreTax.tax_name', 'StoreTax.tax_value', 'StoreTax.store_id')
                            )
            )));

            $this->Topping->bindModel(
                    array('hasMany' => array(
                            'Topping' => array(
                                'className' => 'Topping',
                                'foreignKey' => 'addon_id',
                                'order' => array('Topping.position ASC'),
                                'conditions' => array('Topping.item_id' => $itemId, 'Topping.is_addon_category' => 0, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $decrypt_storeId),
                                'fields' => array('Topping.id', 'Topping.name', 'Topping.is_addon_category', 'Topping.addon_id', 'Topping.size_id', 'Topping.price', 'Topping.item_id', 'Topping.no_size', 'Topping.position')
                            )
                        ),
                        'hasOne' => array(
                            'ItemDefaultTopping' => array(
                                'className' => 'ItemDefaultTopping',
                                'foreignKey' => 'topping_id',
                                'conditions' => array('ItemDefaultTopping.is_deleted' => 0)
                            ),
                            'ToppingPrice' => array(
                                'className' => 'ToppingPrice',
                                'foreignKey' => 'topping_id',
                                'conditions' => array('ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0, 'ToppingPrice.store_id' => $decrypt_storeId, 'ToppingPrice.size_id' => $sizeId),
                                'fields' => array('ToppingPrice.id', 'ToppingPrice.store_id', 'ToppingPrice.item_id', 'ToppingPrice.size_id', 'ToppingPrice.topping_id', 'ToppingPrice.price')
                            )
                        )
                    )
            );





            $this->Item->bindModel(
                    array('hasMany' => array(
                            'ItemType' => array(
                                'className' => 'ItemType',
                                'foreignKey' => 'item_id',
                                'order' => array('ItemType.position ASC'),
                                'conditions' => array('ItemType.is_active' => 1, 'ItemType.is_deleted' => 0, 'ItemType.store_id' => $decrypt_storeId),
                                'fields' => array('ItemType.id', 'ItemType.item_id', 'ItemType.type_id', 'ItemType.store_id', 'ItemType.merchant_id', 'ItemType.position')
                            ), 'ItemPrice' => array(
                                'className' => 'ItemPrice',
                                'foreignKey' => 'item_id',
                                'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $decrypt_storeId),
                                'fields' => array('ItemPrice.id', 'ItemPrice.item_id', 'ItemPrice.price', 'ItemPrice.store_tax_id', 'ItemPrice.size_id', 'ItemPrice.store_id')
                            ), 'Topping' => array(
                                'className' => 'Topping',
                                'foreignKey' => 'item_id',
                                'order' => array('Topping.position ASC'),
                                'conditions' => array('Topping.is_addon_category' => 1, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $decrypt_storeId),
                                'fields' => array('Topping.id', 'Topping.name', 'Topping.is_addon_category', 'Topping.addon_id', 'Topping.size_id', 'Topping.price', 'Topping.item_id', 'Topping.no_size', 'Topping.position', 'Topping.min_value', 'Topping.max_value')
                            )
                        )
            ));
            $this->Type->unBindModel(array('hasMany' => array('ItemType')));
            $positionvalue = array();
            $itemTypearray = array();
            $productInfo = $this->Item->fetchItemDetail($itemId, $storeId);
            //pr($productInfo);die;
            $toppingSizes = $this->AddonSize->fetchAddonSize($storeId);
            if ($productInfo) {
                $querySize = 0;
                $default_price = 0;
                $taxprice = 0;
                if ($productInfo['ItemPrice']) {
                    foreach ($productInfo['ItemPrice'] as $checkSize) {
                        if ($checkSize['size_id'] == 0) {
                            $querySize = $checkSize['size_id'];
                            $default_price = $checkSize['price'];
                            $intervalPrice = 0;
                            $intervalPrice = $this->getTimeIntervalPrice($checkSize['item_id'], $checkSize['size_id']);
                            if (!empty($intervalPrice['Interval']['IntervalDay']) && !empty($intervalPrice['IntervalPrice'])) {
                                $default_price = $intervalPrice['IntervalPrice']['price'];
                                $this->Session->write('Order.Item.interval_id', $intervalPrice['IntervalPrice']['interval_id']);
                            }
                            if (!empty($checkSize['StoreTax'])) {
                                $productInfo['Item']['taxvalue'] = $checkSize['StoreTax']['tax_value'];
                            } else {
                                $productInfo['Item']['taxvalue'] = '';
                            }
                            $this->Session->write('Order.Item.SizePrice', $default_price);
                        } else {
                            if (!empty($checkSize['Size'])) {
                                if ($sizeId == $checkSize['size_id']) {
                                    $querySize = $checkSize['size_id'];
                                    $default_price = $checkSize['price'];
                                    $intervalPrice = 0;
                                    $intervalPrice = $this->getTimeIntervalPrice($checkSize['item_id'], $checkSize['size_id']);
                                    if (!empty($intervalPrice['Interval']['IntervalDay']) && !empty($intervalPrice['IntervalPrice'])) {
                                        $default_price = $intervalPrice['IntervalPrice']['price'];
                                        $this->Session->write('Order.Item.interval_id', $intervalPrice['IntervalPrice']['interval_id']);
                                    }
                                    if (!empty($checkSize['StoreTax'])) {
                                        $productInfo['Item']['taxvalue'] = $checkSize['StoreTax']['tax_value'];
                                    } else {
                                        $productInfo['Item']['taxvalue'] = '';
                                    }
                                    //$productInfo['Item']['taxvalue']=$checkSize['StoreTax']['tax_value'];
                                    $this->Session->write('Order.Item.SizePrice', $default_price);
                                    break;
                                }
                            }
                        }
                    }
                    //pr($default_price);die;
                }


                $itemId = $productInfo['Item']['id'];
                $itemName = $productInfo['Item']['name'];
                $categoryId = $productInfo['Item']['category_id'];
                $itemName = $productInfo['Item']['name'];
                $itemtaxvalue = $productInfo['Item']['taxvalue'];
                $deliver_check = $productInfo['Item']['is_deliverable'];
                $default_quantity = 1;
                $this->Session->write('Order.Item.quantity', $default_quantity);
                $this->Session->write('Order.Item.actual_price', $default_price);
                $this->Session->write('Order.Item.is_deliverable', $deliver_check);
                $this->Session->write('Order.Item.id', $itemId);
                $this->Session->write('Order.Item.name', $itemName);
                $this->Session->write('Order.Item.categoryid', $categoryId);
                $this->Session->write('Order.Item.price', $default_price);
                $this->Session->write('Order.Item.taxvalue', $itemtaxvalue);
                $this->Session->write('Order.Item.final_price', $default_price);
                $this->loadModel('Offer');
                $this->Offer->bindModel(array('hasMany' => array('OfferDetail')));
                $display_offer = $this->Offer->allOfferOnItem($itemId);
                $itemQuantity = $this->Session->read('Order.Item.quantity');
                if ($querySize == 0) {
                    $offer_result = $this->Offer->offerOnItemm($itemId, $itemQuantity);
                } else {
                    $offer_result = $this->Offer->offerOnItemSizee($itemId, $querySize, $itemQuantity);
                }
                if (!empty($offer_result)) {
                    $this->Session->write('Offer', $offer_result['Offer']);
                }
                $productInfo['Item']['sizeOnly'] = $categorySizeType;
                $this->set(compact('toppingSizes', 'productInfo', 'default_price', 'display_offer'));

                //pr($productInfo); die;
            }
        }
    }

    /* ------------------------------------------------
      Function name:ajaxChangeOrderType()
      Description: Update the session value for order
      created Date:21/12/2015
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function ajaxChangeOrderType() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $sessionFlag = false;
        if ($this->request->is('ajax')) {
            if (!empty($_POST['orderType']) && $_POST['orderType'] == 3) {
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
	    $nowData = $this->_checkNowTime($_POST['orderType']);
            if (!empty($_POST['storePickupDate'])) {
                $_POST['deliveryType'] = 1;
            } elseif (!empty($nowData['pickup_date_time'])) {
                if ($nowData['setPre'] == 1 && empty($_POST['storePickupDate'])) {
                    $response['status'] = 'Error';
                    $response['msg'] = "Please try after some time.";
                    return json_encode($response);
                } else {
                    $_POST['deliveryType'] = 0;
                    $_POST['storePickupTime'] = $nowData['pickup_time'];
                    $_POST['storePickupDate'] = $nowData['pickup_date'];
                }
            } else {
                $response['status'] = 'Error';
                $response['msg'] = "Please try after some time.";
                return json_encode($response);
            }
            if ($_POST['deliveryType'] == 0) {
                $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
                $orderType = $_POST['orderType'];
                $today = 1;
                $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
                $timearray = array_diff($finaldata['time_range'], $finaldata['time_break']);
                $_POST['storePickupTime'] = reset($timearray);
                $explodeVal = explode("-", $finaldata['currentdate']);
                $finaldata['currentdate'] = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
                $_POST['storePickupDate'] = $finaldata['currentdate'];
            }
            if (isset($_SESSION['Order']) && !empty($_SESSION['Order'])) {
                $this->Session->write('Order.order_type', $_POST['orderType']);
                $this->Session->write('Order.is_preorder', $_POST['deliveryType']);
                $this->Session->write('Order.store_pickup_time', $_POST['storePickupTime']);
                $this->Session->write('Order.store_pickup_date', $_POST['storePickupDate']);
                $sessionFlag = true;
            }

            if (isset($_SESSION['Cart']) && !empty($_SESSION['Cart'])) {
                $this->Session->write('Cart.segment_type', $_POST['orderType']);
                $cartdate = explode('-', $_POST['storePickupDate']);
                $formatedCartDate = $cartdate[2] . '-' . $cartdate[0] . '-' . $cartdate[1];
                $this->Session->write('Cart.order_time', $formatedCartDate . ' ' . $_POST['storePickupTime']);
                $sessionFlag = true;
            }

            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $key => $value) {
                    $_SESSION['cart'][$key]['order_type'] = $_POST['orderType'];
                    $_SESSION['cart'][$key]['is_preorder'] = $_POST['deliveryType'];
                    $_SESSION['cart'][$key]['store_pickup_time'] = $_POST['storePickupTime'];
                    $_SESSION['cart'][$key]['store_pickup_date'] = $_POST['storePickupDate'];
                }
                $sessionFlag = true;
            }

            $guestUser = false;
            if (!AuthComponent::User() && $this->Session->check('Order.delivery_address_id')) {
                $guestUser = true;
            }

            if ($_POST['orderType'] == 2 && $guestUser == false) {
                $this->Session->delete('Order.delivery_address_id');
            }
            $returnArray['status'] = 1;
            $returnArray['time'] = $this->Common->storeTimeFormateUser($_POST['storePickupTime']);
            $returnArray['date'] = $_POST['storePickupDate'];
            $returnArray['ordertype'] = $_POST['orderType'];
            return json_encode($returnArray);
        } else {
            $returnArray['status'] = 0;
            return json_encode($returnArray);
        }
    }

    public function getlatesttotalamont() {
        $this->autoRender = false;
        $this->layout = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {

            $storeId = $this->Session->read('store_id');
            $this->loadModel("Store");
            $StoreBusinessMail = $this->Store->find('first', array('fields' => array('paypal_business_email'),
                'conditions' => array('id' => $storeId),
                'recursive' => -1,
            ));
            $custom['store_id'] = $storeId;
            $custom['amount'] = number_format($this->Session->read('Cart.grand_total_final'), 2);
            $custom['email'] = trim($StoreBusinessMail['Store']['paypal_business_email']);
            $custom = base64_encode($this->Encryption->encode(base64_encode(json_encode($custom))));

            $updatePaypal['expressAmount'] = number_format($this->Session->read('Cart.grand_total_final'), 2);
            $updatePaypal['expressItemNumber'] = count($this->Session->read('cart'));
            $updatePaypal['expressCustom'] = $custom;

            return json_encode($updatePaypal);
        }
    }

    /* ------------------------------------------------
      Function name:ajaxOrderOverview()
      Description: write order item listing into session
      created Date:11/02/2016
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function getTimeIntervalPrice($itemId = null, $sizeId = null) {

//$this->layout = null;
//$this->autoRender = false;
        $this->loadModel('Store');
        $this->loadModel('Interval');
        $this->loadModel('IntervalPrice');
        $this->loadModel('IntervalDay');

        $storeId = $this->Session->read('store_id');
        $currentDateTime = date("Y-m-d H:i:s", (strtotime($this->Common->storeTimeZoneUser('', date("Y-m-d H:i:s")))));
        $currentTime = date("H:i:s", strtotime($currentDateTime));
        $currentDay = date("N", strtotime($currentDateTime));
        $this->Interval->unbindModel(
                array('hasMany' => array('IntervalDay'))
        );

        $this->Interval->bindModel(
                array(
                    'hasOne' => array(
                        'IntervalDay' => array(
                            'className' => 'IntervalDay',
                            'foreignKey' => 'interval_id',
                            'conditions' => array('IntervalDay.week_day_id' => $currentDay, 'IntervalDay.day_status' => 1, 'IntervalDay.store_id' => $storeId),
                            'fields' => array('IntervalDay.id', 'IntervalDay.week_day_id', 'IntervalDay.interval_id'),
                            'type' => 'INNER',
                        )
                    )
                )
        );

        $this->IntervalPrice->bindModel(
                array(
                    'belongsTo' => array(
                        'Interval' => array(
                            'className' => 'Interval',
                            'foreignKey' => 'interval_id',
                            'conditions' => array('Interval.is_active' => 1, 'Interval.is_deleted' => 0, 'Interval.store_id' => $storeId, 'Interval.start <=' => $currentTime, 'Interval.end >=' => $currentTime),
                            'fields' => array('Interval.id', 'Interval.name'),
                            'type' => 'INNER'
                        )
                    )
                )
        );
        $intervalPriceDetail = array();
        $intervalPriceDetail = $this->IntervalPrice->find('all', array('recursive' => 2, 'conditions' => array('IntervalPrice.item_id' => $itemId, 'IntervalPrice.size_id' => $sizeId, 'IntervalPrice.store_id' => $storeId, 'IntervalPrice.is_active' => 1, 'IntervalPrice.is_deleted' => 0, 'IntervalPrice.size_active' => 1), 'fields' => array('IntervalPrice.id', 'IntervalPrice.interval_id', 'IntervalPrice.price')));

        foreach ($intervalPriceDetail as $key => $value) {
            if (!empty($value['IntervalPrice']) && !empty($value['Interval']) && !empty($value['Interval']['IntervalDay'])) {
                //pr($intervalPriceDetail[$key]);
                //return $value;
                return $intervalPriceDetail[$key];
                break;
            }
        }
    }

    public function checkItemOffer($itemId = null, $userid = null, $ItemQuantity = 1) {
        $FreeItemQuantity = 0;
        if (!empty($itemId)) {
            $this->loadModel('ItemOffer');
            $todayDate = $this->Common->gettodayDate(1);
            $offerExists = $this->ItemOffer->OfferExists($itemId, $todayDate);
            if ($offerExists) {
                $this->loadModel('OrderItem');
                $startdate = $offerExists['ItemOffer']['start_date'];
                $endDate = $offerExists['ItemOffer']['end_date'];
                $orderItemCount = 0;
                if (!empty($userid)) {
                    $orderItemCount = $this->OrderItem->getItemInfo($itemId, $userid, $startdate, $endDate);
                }
                $totalItem = 0;
                if (!empty($orderItemCount[0][0]['total'])) {
                    $totalItem = $orderItemCount[0][0]['total'];
                }
                $offerExists['ItemOffer']['unit_counter'] = $offerExists['ItemOffer']['unit_counter'] - 1;


                if ($ItemQuantity) {
                    $FreeItemQuantity = 0;

                    $applicableItemQunatity = fmod($totalItem, $offerExists['ItemOffer']['unit_counter']);
                    $totalItem = $applicableItemQunatity + $ItemQuantity;
                    if ($totalItem >= $offerExists['ItemOffer']['unit_counter']) {
                        //$Mod=fmod($totalItem,$offerExists['ItemOffer']['unit_counter']);
                        $FreeItemQuantity = (int) ($totalItem / $offerExists['ItemOffer']['unit_counter']);
                        return $FreeItemQuantity;
                    } else {
                        return $FreeItemQuantity;
                    }
                } else {
                    $applicableItemQunatity = fmod($totalItem, $offerExists['ItemOffer']['unit_counter']);
                    $totalItem = $applicableItemQunatity + 1;


                    if ($totalItem >= $offerExists['ItemOffer']['unit_counter']) {
                        //$totalItem=$totalItem+$ItemQuantity;
                        $FreeItemQuantity = (int) ($totalItem / $offerExists['ItemOffer']['unit_counter']);
                        //$Mod=fmod($totalItem,$offerExists['ItemOffer']['unit_counter']);
                        return $FreeItemQuantity;
//                        if($Mod==0){
//                            $ItemisFree=1;
//                        }
                    }
                }
            }
        }
        return $FreeItemQuantity;
    }

    public function addTip() {
        if ($this->request->is('ajax')) {
            $tipamount = 0;
            $tip = abs($_POST['tip']);
            if(strpos($_POST['tipvalue'],'$') === false)
            {
                $tipvalue = abs(trim($_POST['tipvalue']));
            } else {
                $tipvalue = explode('$', trim($_POST['tipvalue']));
                $tipvalue = abs(trim($tipvalue[1]));
            }
            $tipselect = abs($_POST['tipselect']);
            $subTotal = abs($_POST['subTotal']);

            if ($tip != '' && $tip != 0 && $tip == 2) {
                $tipamount = $tipvalue;
            } else if ($tip != '' && $tip != 0 && $tip == 3) {
                $tipamount = ($tipselect / 100) * $subTotal;
            } else {
                $tipamount = 0;
            }

            if (!is_numeric($tipamount)) {
                return false;
            }
            $total_price = $this->Session->read('Cart.grand_total_final');
            $total_price = $total_price + $tipamount;
            $this->Session->write('Cart.tip', $tipamount);
            $this->Session->write('Cart.tip_option', $tip);
            $this->Session->write('Cart.tip_value', $tipvalue);
            $this->Session->write('Cart.tip_select', $tipselect);
            $this->Session->write('Cart.grand_total_final', number_format($total_price, 2)); // It will give the final totoal with all taxes
            $finalItem = $this->Session->read('cart');
            $this->set(compact('finalItem'));
            $this->loadModel('StoreSetting');
            $storeSetting = $this->StoreSetting->findByStoreId($this->Session->read('store_id'));
            $this->set('storeSetting', $storeSetting);
        }
    }

    function checkPreference() {//No Use under review
        $this->layout = "ajax";
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $itemID = $this->Session->read('Order.Item.id');
            $this->loadModel('Item');
            $precheck = $this->Item->checkPreIsMandatory($itemID);
            if ($precheck) {
                $storeId = $this->Session->read('store_id');
                $this->loadModel('Item');
                $this->loadModel('Type');
                $this->loadModel('ItemType');
                $this->Type->unbindModel(array('hasMany' => array('ItemType')));
                $this->Type->bindModel(
                        array('hasMany' => array(
                                'SubPreference' => array(
                                    'className' => 'SubPreference',
                                    'foreignKey' => 'type_id',
                                    'order' => array('SubPreference.position ASC'),
                                    'conditions' => array('SubPreference.is_active' => 1, 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $storeId),
                                    'fields' => array('SubPreference.id')
                                )
                            )
                ));


                $this->ItemType->bindModel(
                        array('belongsTo' => array(
                                'Type' => array(
                                    'className' => 'Type',
                                    'foreignKey' => 'type_id',
                                    'conditions' => array('Type.is_active' => 1, 'Type.is_deleted' => 0, 'Type.store_id' => $storeId),
                                    'fields' => array('Type.id', 'Type.name')
                                )
                )));

                $this->Item->bindModel(
                        array('hasMany' => array(
                                'ItemType' => array(
                                    'className' => 'ItemType',
                                    'foreignKey' => 'item_id',
                                    'conditions' => array('ItemType.is_active' => 1, 'ItemType.is_deleted' => 0, 'ItemType.store_id' => $storeId),
                                    'fields' => array('ItemType.id', 'ItemType.item_id', 'ItemType.store_id', 'ItemType.type_id')
                                )
                )));

                $preferences = $this->Item->getItemPreferences($storeId, $itemID);
                $count = 0;
                foreach ($preferences['ItemType'] as $key => $parr) {
                    if (!empty($parr['Type']['SubPreference'])) {
                        $count++;
                    }
                }
                if (!empty($_POST['data']['Item']['subpreference']) && count($_POST['data']['Item']['subpreference']) == $count) {
                    return json_encode(1);
                }
                return json_encode(0);
            }
            return json_encode(1);
        }
    }

    function checkOrderType() {
        $this->layout = "ajax";
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $orderType = '';
            if ($this->Session->check('Order.order_type')) {
                $orderType = $this->Session->read('Order.order_type');
            }
            if ($orderType) {
                return json_encode(1);
            }
            return json_encode(0);
        }
    }

    function checkdeliveryadd() {
        $this->layout = "ajax";
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $orderType = '';
            $deliveryaddress = '';
            if ($this->Session->check('Order.order_type')) {
                $orderType = $this->Session->read('Order.order_type');
                if ($orderType == 3) {
                    if ($this->Session->check('Order.delivery_address_id')) {
                        $deladd = $this->Session->read('Order.delivery_address_id');
                        $this->loadModel('DeliveryAddress');
                        $delivery_address = $this->DeliveryAddress->fetchAddress($deladd);
                        if (!empty($delivery_address['DeliveryAddress']['address'])) {
                            $deliveryaddress = 1;
                        }
                    }
                } elseif ($orderType == 2) {
                    $deliveryaddress = 1;
                }
            }
            if ($deliveryaddress) {
                return json_encode(1);
            }
            return json_encode(0);
        }
    }

    function getcartCount() {
        $this->Session->write('CartOffer', $this->Session->read('CartOfferM'));
        $this->layout = "ajax";
        $this->autoRender = false;
        $cartcount = 0;
        if ($this->Session->check('cart')) {
            foreach ($this->Session->read('cart') as $key => $itemarr) {
                $cartcount+=$itemarr['Item']['quantity'];
            }
        }
        return $cartcount;
    }

    function addtosession() {
        $this->layout = "ajax";
        $this->autoRender = false;

        $this->Session->write('Itemcartdata.data.Item', $this->Session->read('Order.Item'));
        //$this->Session->write('Itemcartdata.data.subPreference', @$this->Session->read('Order.subPreference'));
        $this->Session->write('Itemcartdata.data.subPreferenceOld', @$this->Session->read('Order.subPreferenceOld'));
        $this->Session->write('Itemcartdata.data.Item.subpreference', @$_POST['data']['Item']['subpreference']);
        $this->Session->write('Itemcartdata.data.Item.price', $_POST['data']['Item']['price']);
        $this->Session->write('Itemcartdata.data.Item.toppings', @$_POST['data']['Item']['toppings']);
        return 1;
    }

    function removefrmSession() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->Session->delete('Itemcartdata');
        return 1;
    }

    function checkOrderTime() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $response = 1;
        if ($this->Session->check('Order.store_pickup_time') && $this->Session->check('Order.store_pickup_date')) {
            $response = 1;
        } else {
            $response = 0;
        }
        return $response;
    }

    function getitemdata() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $itemdata = 0;
        if ($this->Session->check('Itemcartdata')) {
            $itemdata = 1;
        }
        return $itemdata;
    }

    function blackOutDaysHideNow() {
        $this->layout = "ajax";
        $this->autoRender = false;
        if (!empty($this->request->data['orderType'])) {
            $this->loadModel('Store');
            $NowAvail = $this->Store->getNowAvailability($this->request->data['orderType'], $this->Session->read('store_id'));
            return $NowAvail;
            exit;
        } else {
            exit;
        }
    }

    function checkMinAmount() {
        $this->layout = "ajax";
        $this->autoRender = false;
        pr($this->Session->read());
        die;
    }

    public function checkMendatoryItem1() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $cartData = $this->Session->read('cart');
        if (!empty($cartData)) {
            $this->loadModel('Item');
            $mandatoryItemList = $this->Item->find('list', array('fields' => array('Item.id'), 'conditions' => array('Item.mandatory_item_units >' => 0)));
            if (empty($mandatoryItemList)) {
                $response['status'] = 'success';
                $response['msg'] = 'success';
            } else {
                //pr($mandatoryItemList);
                $i = 0;
                foreach ($cartData as $data) {
                    if (in_array($data['Item']['id'], $mandatoryItemList)) {
                        $i++;
                        $itemData = $this->Item->findById($data['Item']['id'], array('mandatory_item_units'));
                        if ($data['Item']['quantity'] < $itemData['Item']['mandatory_item_units']) {
                            $response['status'] = 'error';
                            $response['msg'] = 'Please select mandatory item.1';
                            break;
                        } else {
                            $response['status'] = 'success';
                            $response['msg'] = 'success';
                        }
                    }
                }
            }
            if (count($mandatoryItemList) != $i) {
                $response['status'] = 'error';
                $response['msg'] = 'Please select mandatory item';
            }
            return json_encode($response);
        }
    }

    public function checkMandatoryPrefAddons() {
        $this->layout = "ajax";
        $this->autoRender = false;
        if ($this->request->is('ajax') && !empty($this->request->data)) {
            $responses = $this->_checkPrefAddons();
            $returnData = json_decode($responses, true);
            if (!empty($returnData["status"]) && ($returnData["status"] == 'Error')) {
                return $responses;
            }
        }
    }

    public function removeCoupon() {
        $couponApplied = $this->Session->read('Coupon');
        if (!empty($couponApplied)) {
            $this->Session->delete('Coupon');
	    $this->Session->delete('Discount');
            $this->Session->setFlash(__("Coupon remove successfully."));
        }
        $this->redirect($this->referer());
    }

    public function addCouponToCart() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is('ajax') && (!empty($this->request->data['couponId']) || !empty($this->request->data['coupon_code']))) {
            $response['status'] = 'Error';
            $response['msg'] = 'Coupon code not valid.';
            $cartData = $this->Session->read('cart');
            if (empty($cartData)) {
                $response['status'] = 'Error';
                $response['msg'] = 'Cart is empty, Please add items to apply coupon.';
            } else {
                $this->loadModel('Coupon');
                if (!empty($this->request->data['couponId'])) {
                    $couponId = $this->Encryption->decode($this->request->data['couponId']);
                    $couponData = $this->Coupon->findById($couponId, array('coupon_code'));
                }
                if ((!empty($couponData) && !empty($this->request->data['couponId'])) || !empty($this->request->data['coupon_code'])) {
                    if (!empty($this->request->data['coupon_code'])) {
                        $couponData['Coupon']['coupon_code'] = $this->request->data['coupon_code'];
                    }
                    $storeId = $this->Session->read('store_id');
                    $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
                    $coupon = $this->Coupon->getValidCoupon($couponData['Coupon']['coupon_code'], $storeId, $current_date);
                    if ($coupon) {
                        if ($coupon['Coupon']['number_can_use'] > $coupon['Coupon']['used_count']) {
                            $verify = true;
                            $current_time = date('H:i:s', strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s'))));
                            $current_time = strtotime($current_time);
                            if (!empty($coupon['Coupon']['allow_time'])) {
                                $start_time = strtotime($coupon['Coupon']['start_time']);
                                $end_time = strtotime($coupon['Coupon']['end_time']);
                                if (($current_time >= $start_time) && ($current_time <= $end_time)) {
                                    $verify = true;
                                } else {
                                    $verify = false;
                                }
                                if (!empty($coupon['Coupon']['days']) && $verify) {
                                    $days = explode(',', $coupon['Coupon']['days']);
                                    $day_number = date('N', strtotime($current_date));
                                    if (!in_array($day_number, $days)) {
                                        $verify = false;
                                    }
                                } else {
                                    $verify = false;
                                }
                            }
                            if ($verify) {
                                $this->Session->write('Coupon', $coupon);
                                if (empty($this->request->data['page'])) {
                                    $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
                                    $encrypted_merchantId = $this->Encryption->encode($this->Session->read('merchant_id'));
                                    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/products/items/' . $encrypted_storeId . '/' . $encrypted_merchantId;
                                    $response['status'] = 'Success';
                                    $response['url'] = $url;
                                } else {
                                    //$this->redirect(array('contoller' => 'products', 'action' => 'orderDetails'));
                                    $this->Session->setFlash(__("Coupon apply successfully."));
                                    $response['status'] = 'Success';
                                }
                            } else {
                                $response['status'] = 'Error';
                                $response['msg'] = 'Coupon has been expired.';
                            }
                        } else {
                            $response['status'] = 'Error';
                            $response['msg'] = 'Coupon has been expired.';
                        }
                    } else {
                        $response['status'] = 'Error';
                        $response['msg'] = 'Please enter valid coupon code.';
                    }
                }
            }
            return json_encode($response);
        }
    }

    public function refreshCart() {
        $this->layout = false;
        $this->set('final_cart', $this->Session->read('cart'));
        $this->render('/Elements/design/aaron/storeMenu/cart');
    }

    public function ajaxFetchToppingSizePrice() {
        $this->autoRender = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax', 'post')) {
            $toppingId = $_POST['toppingId'];
            $itemId = $_POST['itemId'];
            $addonId = $_POST['sizeId'];
            $type = $_POST['type'];
            $this->loadModel('Topping');
            //$this->loadModel('AddonSize');
            $storeId = $this->Session->read('store_id');
            $this->Topping->bindModel(
                    array('hasMany' => array(
                            'ToppingPrice' => array(
                                'className' => 'ToppingPrice',
                                'foreignKey' => 'topping_id',
                                'conditions' => array('ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0, 'ToppingPrice.store_id' => $storeId, 'ToppingPrice.size_id' => $_POST['itemSizeId']),
                                'fields' => array('ToppingPrice.id', 'ToppingPrice.store_id', 'ToppingPrice.item_id', 'ToppingPrice.size_id', 'ToppingPrice.topping_id', 'ToppingPrice.price')
                            )
                        )
                    )
            );
            if ($type == 1 && $addonId == 1) { // Default
                $new_price = 0;
            } else {
                $price = $this->Topping->fetchToppingPrice($itemId, $toppingId, $storeId);
                $price_topping = 0;
                if (isset($price['ToppingPrice']) && !empty($price['ToppingPrice'])) {
                    $price_topping = $price['ToppingPrice'][0]['price'];
                }
                if ($price_topping > 0) {
                    $new_price = $price_topping * $addonId;
                } else {
                    $new_price = 0;
                }
            }
            return $new_price;
        }
    }

    public function ajaxFetchToppingPrice() {//no use
        $this->autoRender = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $toppingId = $_POST['toppingId'];
            $itemId = $_POST['itemId'];
            $addonId = $_POST['sizeId'];
            $checked = $_POST['checked'];
            $type = $_POST['type'];
            $this->loadModel('Topping');
            $storeId = $this->Session->read('store_id');
            $this->Topping->bindModel(
                    array('hasMany' => array(
                            'ToppingPrice' => array(
                                'className' => 'ToppingPrice',
                                'foreignKey' => 'topping_id',
                                'conditions' => array('ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0, 'ToppingPrice.store_id' => $storeId, 'ToppingPrice.size_id' => $_POST['itemSizeId']),
                                'fields' => array('ToppingPrice.id', 'ToppingPrice.store_id', 'ToppingPrice.item_id', 'ToppingPrice.size_id', 'ToppingPrice.topping_id', 'ToppingPrice.price')
                            )
                        )
                    )
            );

            $price = $this->Topping->fetchToppingPrice($itemId, $toppingId, $storeId);
            $old_topping_values = $this->Session->read('Order.Item.PaidTopping.' . $toppingId);
            if (!empty($old_topping_values)) {
                $oldPrice = $old_topping_values['price'];
            } else {
                $oldPrice = 0;
            }
            $topping = array();
            if ($price) {
                $item_price = $this->Session->read('Order.Item.price');
                $price_topping = 0;
                if (isset($price['ToppingPrice']) && !empty($price['ToppingPrice'])) {
                    $price_topping = $price['ToppingPrice'][0]['price'];
                }
                $new_topping_price = $price_topping * $addonId;
                if ($checked == 1) {
                    if ($type == 1 && $addonId == 1) {
                        $new_topping_price = 0;
                    }
                    $topping['name'] = $price['Topping']['name'];
                    $topping['id'] = $toppingId;
                    $topping['type'] = $type;
                    $topping['addonId'] = $addonId;
                    $topping['price'] = $new_topping_price;
                    $this->Session->write('Order.Item.PaidTopping.' . $toppingId, $topping); //Topping Session
                    if ($this->Session->check('Order.Item.topping_total')) {
                        $previous = $this->Session->read('Order.Item.topping_total');
                        $topping_total = ($previous - $oldPrice) + $new_topping_price;
                    }
                    $new_price = ($item_price - $oldPrice) + $new_topping_price;
                } else {
                    $this->Session->delete('Order.Item.PaidTopping.' . $toppingId);
                    if ($this->Session->check('Order.Item.topping_total')) {
                        $previous = $this->Session->read('Order.Item.topping_total');
                        $topping_total = $previous - $oldPrice;
                    }
                    $new_price = $item_price - $oldPrice;
                }
                $this->Session->write('Order.Item.price', $new_price);
                $this->Session->write('Order.Item.final_price', $new_price);
                if ($this->Session->check('Order.Item.topping_total')) {
                    $this->Session->write('Order.Item.topping_total', $topping_total);
                } else {
                    $this->Session->write('Order.Item.topping_total', $new_topping_price);
                }
            } else {
                return false;
            }
            return $new_price;
        }
    }

    public function fetchToppingPrice() {
        $this->autoRender = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $toppingId = $_POST['toppingId'];
            $itemId = $_POST['itemId'];
            $addonId = $_POST['sizeId'];
            $checked = $_POST['checked'];
            $type = $_POST['type'];
            $countSizes = $_POST['countSizes'];
            $subDefault = $_POST['subDefault'];
            $this->loadModel('Topping');
            $this->loadModel('AddonSize');
            $storeId = $this->Session->read('store_id');
            if (empty($subDefault) && !empty($_POST['itemSizeId'])) {
                $this->Topping->bindModel(
                        array('hasMany' => array(
                                'ToppingPrice' => array(
                                    'className' => 'ToppingPrice',
                                    'foreignKey' => 'topping_id',
                                    'conditions' => array('ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0, 'ToppingPrice.store_id' => $storeId, 'ToppingPrice.size_id' => $_POST['itemSizeId']),
                                    'fields' => array('ToppingPrice.id', 'ToppingPrice.store_id', 'ToppingPrice.item_id', 'ToppingPrice.size_id', 'ToppingPrice.topping_id', 'ToppingPrice.price')
                                )
                            )
                        )
                );
            }
            $price = $this->Topping->fetchToppingPrice($itemId, $toppingId, $storeId);
            $old_topping_values = $this->Session->read('Order.Item.PaidTopping.' . $toppingId);
            if (!empty($old_topping_values)) {
                $oldPrice = $old_topping_values['price'];
            } else {
                $oldPrice = 0;
            }
            $topping = array();
            if ($price) {
                $price_topping = 0;
                $item_price = $this->Session->read('Order.Item.price');
                if (isset($price['ToppingPrice']) && !empty($price['ToppingPrice'])) {
                    $price_topping = $price['ToppingPrice'][0]['price'];
                }
                if (!empty($subDefault) || empty($_POST['itemSizeId'])) {
                    $price_topping = $price['Topping']['price'];
                }
                $new_topping_price = $price_topping * $addonId;
                if ($checked == 1) {
                    if ($type == 1 && $addonId == 1) {
                        $new_topping_price = 0;
                    }
                    $topping['name'] = $price['Topping']['name'];
                    $topping['id'] = $toppingId;
                    $topping['type'] = $type;
                    $topping['addonId'] = $addonId;
                    $topping['price'] = $new_topping_price;
                    $this->Session->write('Order.Item.PaidTopping.' . $toppingId, $topping); //Topping Session
                    if ($this->Session->check('Order.Item.topping_total')) {
                        $previous = $this->Session->read('Order.Item.topping_total');
                        $topping_total = ($previous - $oldPrice) + $new_topping_price;
                    }
                    $new_price = ($item_price - $oldPrice) + $new_topping_price;
                } else {
                    $this->Session->delete('Order.Item.PaidTopping.' . $toppingId);
                    if ($this->Session->check('Order.Item.topping_total')) {
                        $previous = $this->Session->read('Order.Item.topping_total');
                        $topping_total = $previous - $oldPrice;
                    }
                    $new_price = $item_price - $oldPrice;
                }

                $this->Session->write('Order.Item.price', $new_price);
                $this->Session->write('Order.Item.final_price', $new_price);

                if ($this->Session->check('Order.Item.topping_total')) {
                    $this->Session->write('Order.Item.topping_total', $topping_total);
                } else {
                    $this->Session->write('Order.Item.topping_total', $new_topping_price);
                }
                $response['status'] = 'Success';
                $response['price'] = $new_price;
            } else {
                $response['status'] = 'Error';
            }
            return json_encode($response);
        }
    }

    public function fetchToppingSizePrice() {
        $this->autoRender = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax', 'post')) {
            $toppingId = $_POST['toppingId'];
            $itemId = $_POST['itemId'];
            $addonId = $_POST['sizeId'];
            $type = $_POST['type'];
            $countSizes = $_POST['countSizes'];
            $subDefault = $_POST['subDefault'];
            $this->loadModel('Topping');
            $storeId = $this->Session->read('store_id');
            if ($type == 1 && $addonId == 1) { // Default
                $new_price = 0;
            } else {
                $new_price = 0;
                if (empty($subDefault) && !empty($_POST['itemSizeId'])) {
                    $this->Topping->bindModel(
                            array('hasMany' => array(
                                    'ToppingPrice' => array(
                                        'className' => 'ToppingPrice',
                                        'foreignKey' => 'topping_id',
                                        'conditions' => array('ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0, 'ToppingPrice.store_id' => $storeId, 'ToppingPrice.size_id' => $_POST['itemSizeId']),
                                        'fields' => array('ToppingPrice.id', 'ToppingPrice.store_id', 'ToppingPrice.item_id', 'ToppingPrice.size_id', 'ToppingPrice.topping_id', 'ToppingPrice.price')
                                    )
                                )
                            )
                    );
                }
                $price = $this->Topping->fetchToppingPrice($itemId, $toppingId, $storeId);
                if (!empty($price['ToppingPrice'])) {
                    $price_topping = 0;
                    if (isset($price['ToppingPrice']) && !empty($price['ToppingPrice'])) {
                        $price_topping = $price['ToppingPrice'][0]['price'];
                    }
                    if ($price_topping > 0) {
                        $new_price = $price_topping * $addonId;
                    }
                }
                if (!empty($subDefault) || empty($_POST['itemSizeId'])) {
                    if ($price) {
                        $price_topping = $price['Topping']['price'];
                        $new_price = $price_topping * $addonId;
                    } else {
                        $new_price = 0;
                    }
                }
            }
            return $new_price;
        }
    }

    public function ajaxFetchSubPrefrenceSizePrice() {
        $this->autoRender = false;
        $this->layout = "ajax";
        if ($this->request->is('ajax', 'post')) {
            $subPreferenceId = $_POST['subPreferenceId'];
            $itemId = $_POST['itemId']; //no use
            $subPreferenceSize = $_POST['sizeId'];
            $storeId = $this->Session->read('store_id');
            $countSizes = $_POST['countSizes'];
            $subDefault = $_POST['subDefault'];
            $this->loadModel('SubPreference');
            if (empty($subDefault) && !empty($_POST['itemSizeId'])) {
                $itemSizeId = $_POST['itemSizeId'];
                $this->SubPreference->bindModel(
                        array('hasMany' => array(
                                'SubPreferencePrice' => array(
                                    'className' => 'SubPreferencePrice',
                                    'foreignKey' => 'sub_preference_id',
                                    'conditions' => array('SubPreferencePrice.is_active' => 1, 'SubPreferencePrice.is_deleted' => 0, 'SubPreferencePrice.store_id' => $storeId, 'SubPreferencePrice.size_id' => $itemSizeId, 'SubPreferencePrice.item_id' => $itemId),
                                    'fields' => array('SubPreferencePrice.id', 'SubPreferencePrice.store_id', 'SubPreferencePrice.item_id', 'SubPreferencePrice.size_id', 'SubPreferencePrice.sub_preference_id', 'SubPreferencePrice.price')
                                )
                            )
                        )
                );
            }
            $new_price = 0;
            $price = $this->SubPreference->fetchSubPreferencePrice($subPreferenceId, $storeId);
            if (!empty($price['SubPreferencePrice'])) {
                $price_sub_prefrence = 0;
                if (isset($price['SubPreferencePrice']) && !empty($price['SubPreferencePrice'])) {
                    $price_sub_prefrence = $price['SubPreferencePrice'][0]['price'];
                }
                if ($price_sub_prefrence > 0) {
                    $new_price = $price_sub_prefrence * $subPreferenceSize;
                }
            }
            if (!empty($subDefault) || empty($_POST['itemSizeId'])) {
                if ($price) {
                    $price_topping = $price['SubPreference']['price'];
                    $new_price = $price_topping * $subPreferenceSize;
                }
            }
            return $new_price;
        }
    }

    public function paymentForm() {
        $this->layout = false;
        $this->autoRender = false;
        $store_id = $this->Session->read('store_id');
        $nzsafe_info = array();
        $this->loadModel('NzsafeUser');
        if (AuthComponent::User()) {
            $userId = AuthComponent::User('id');
            $nzsafe_info = $this->NzsafeUser->getUser($userId);
            $nzsafe_info = $nzsafe_info['NzsafeUser'];
        }
        $store_result = $this->Store->fetchStoreDetail($store_id);
        $this->NZGateway->setLogin($store_result['Store']['api_username'], $store_result['Store']['api_password']);
        if (!empty($nzsafe_info)) {
            $response = $this->NZGateway->getVault($nzsafe_info["customer_vault_id"]);
            if (count($response) === 0) {
                $nzsafe_info = array();
            }
        }
        $this->set(compact('nzsafe_info', 'store_result'));
        if (DESIGN == 1) {
            $renderFile = '/Elements/design/aaron/payment';
        } elseif (DESIGN == 2) {
            $renderFile = '/Elements/design/chloe/payment';
        } elseif (DESIGN == 3) {
            $renderFile = '/Elements/design/dasol/payment';
        } elseif (DESIGN == 4) {
            $renderFile = '/Elements/design/oldlayout/product/payment_form';
        }
        $this->render($renderFile);
    }

}
