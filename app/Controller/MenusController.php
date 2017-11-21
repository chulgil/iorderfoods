<?php

App::uses('StoreAppController', 'Controller');

class MenusController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common');

    public function beforeFilter() {
        parent::beforeFilter();

        $roleId = $this->Session->read('Auth.User.role_id');
        if ($roleId) {
            if ($roleId != 4) {
                $this->InvalidLogin($roleId);
            }
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

    public function menuFetchProduct() {
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $itemId = $_POST['item_id'];
            $categoryId = $_POST['categoryId'];
            $storeId = $_POST['storeId'];
            $decrypt_storeId = $this->Session->read('store_id');
            $decrypt_merchantId = $this->Session->read('merchant_id');
            //$this->Session->delete('FetchProductData');
            //$this->Session->write('FetchProductData.itemId',$_POST['item_id']);
            //$this->Session->write('FetchProductData.categoryId',$_POST['categoryId']);
            //$this->Session->write('FetchProductData.storeId',$_POST['storeId']);
            //$this->Session->write('FetchProductData.sizeType',$_POST['sizeType']);
            //$this->Session->delete('Order.Item');
            //$this->Session->delete('CartOffer');
            //$this->Session->delete('OfferAddIndex');
            //$this->Session->delete('Offer');
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
                                'conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $decrypt_storeId)
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
                                'conditions' => array('Topping.item_id' => $itemId, 'Topping.is_addon_category' => 0, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $decrypt_storeId)
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
                                'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $decrypt_storeId)
                            ), 'Topping' => array(
                                'className' => 'Topping',
                                'foreignKey' => 'item_id',
                                'order' => array('Topping.position ASC'),
                                'conditions' => array('Topping.is_addon_category' => 1, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $decrypt_storeId)
                            )
                        )
            ));
            $this->Type->unBindModel(array('hasMany' => array('ItemType')));
            $positionvalue = array();
            $itemTypearray = array();
            $productInfo = $this->Item->fetchItemDetail($itemId, $storeId);
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
                                if (!empty($checkSize['StoreTax'])) {
                                    $productInfo['Item']['taxvalue'] = $checkSize['StoreTax']['tax_value'];
                                } else {
                                    $productInfo['Item']['taxvalue'] = '';
                                }
                                $this->Session->write('Order.Item.SizePrice', $default_price);
                                break;
                            }
                        }
                    }
                }

                $itemId = $productInfo['Item']['id'];
                $itemName = $productInfo['Item']['name'];
                $categoryId = $productInfo['Item']['category_id'];
                $itemName = $productInfo['Item']['name'];
                $itemtaxvalue = @$productInfo['Item']['taxvalue'];
                $deliver_check = $productInfo['Item']['is_deliverable'];
                $default_quantity = 1;
                /* $this->Session->write('Order.Item.quantity',$default_quantity);
                  $this->Session->write('Order.Item.actual_price',$default_price);
                  $this->Session->write('Order.Item.is_deliverable',$deliver_check);
                  $this->Session->write('Order.Item.id',$itemId);
                  $this->Session->write('Order.Item.name',$itemName);
                  $this->Session->write('Order.Item.categoryid',$categoryId);
                  $this->Session->write('Order.Item.price',$default_price);
                  $this->Session->write('Order.Item.taxvalue',$itemtaxvalue);
                  $this->Session->write('Order.Item.final_price',$default_price); */
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
                $productInfo['Item']['sizeOnly'] = $_POST['sizeType'];
                $this->set(compact('toppingSizes', 'productInfo', 'default_price', 'display_offer'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:menuFetchCategoryInfo()
      Description:To Fetch price based on the size
      created:22/7/2015
      ----------------------------------------------------- */

    public function menuFetchCategoryInfo() {
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

    /* ------------------------------------------------
      Function name:fbProductShare()
      Description:To share product on fb
      created:18/02/2016
      ----------------------------------------------------- */

    public function fbProductShare($itemId = null) {
        $this->layout = "";
        //$this->autoRender=false;
        $ItemDetails = array();
        if (!empty($itemId)) {
            $this->loadModel('Item');
            $ItemDetails = $this->Item->getItemById($itemId);
            if (!empty($ItemDetails)) {
                $imageLink = $_SERVER['HTTP_HOST'] . "/MenuItem-Image/" . $ItemDetails['Item']['image'];
                if (!empty($ItemDetails['Item']['image'])) {
                    $ItemDetails['Item']['image'] = $imageLink;
                }
            }
        }
        $this->set(compact('ItemDetails'));
    }

}
