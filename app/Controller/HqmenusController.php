<?php

App::uses('HqAppController', 'Controller');

class HqmenusController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common');
    public $uses = array('MerchantGallery', 'MerchantContent', 'User', 'StoreGallery', 'Store', 'MerchantStoreRequest', 'Category', 'Tab', 'Permission', 'Merchant', 'StoreReview', 'Plan', 'Merchant', 'StorePayment');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index($store_id = null, $encrypted_merchantId = null) {
        $this->layout = "merchant";
        $decrypt_storeId = $this->Encryption->decode($store_id);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $merchantDetail = $this->Merchant->getMerchantDetail($decrypt_merchantId);
        $id = $merchantDetail['Merchant']['id'];
        $name = $merchantDetail['Merchant']['name'];
        $image = $merchantDetail['Merchant']['background_image'];
        $logo = $merchantDetail['Merchant']['logo'];
        $hqroleId = $this->Session->read('Auth.hqusers.role_id');
        $this->set(compact('name', 'image', 'logo', 'id', 'hqroleId'));
        $storeCity = $this->Store->find('all', array('fields' => array('city'), 'conditions' => array('Store.merchant_id' => $this->Session->read('hq_id'), 'Store.is_deleted' => 0, 'Store.is_active' => 1), 'group' => array('Store.city')));
        $store = $this->Store->find('all', array('fields' => array('id', 'merchant_id', 'store_name', 'store_url', 'phone', 'address', 'city', 'state', 'latitude', 'logitude', 'zipcode'), 'conditions' => array('Store.merchant_id' => $this->Session->read('hq_id'), 'Store.is_deleted' => 0, 'Store.is_active' => 1)));
        $merchantList = $this->MerchantContent->find('all', array('conditions' => array('MerchantContent.merchant_id' => $this->Session->read('hq_id'), 'MerchantContent.is_active' => 1, 'MerchantContent.is_deleted' => 0)));
        $this->set(compact('store', 'merchantList', 'storeCity'));
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
            $this->set(compact('orderId', 'categoryList', 'decrypt_storeId', 'decrypt_merchantId', 'decrypt_storeId'));
        } else {
            $this->set(compact('orderId', 'encrypted_storeId', 'encrypted_merchantId', 'decrypt_storeId'));
        }
        $this->Session->delete('CartOffer');
        $this->Session->delete('Order.subPreference');
        if ($this->Session->check('cart')) {
            $final_cart = $this->Session->read('cart');
            $this->set(compact('final_cart'));
        }
    }

    public function menuFetchProduct() {
        $this->layout = "ajax";
        if ($this->request->is('ajax')) {
            $itemId = $_POST['item_id'];
            $categoryId = $_POST['categoryId'];
            $storeId = $_POST['storeId'];
            $this->loadModel('Item');
            $this->loadModel('ItemPrice');
            $this->loadModel('ItemType');
            $this->loadModel('Category');
            $this->loadModel('AddonSize');
            $this->loadModel('Topping');
            $this->loadModel('StoreTax');
            $this->loadModel('SubPreference');
            $this->loadModel('Type');
            $this->Type->bindModel(
                    array('hasMany' => array(
                            'SubPreference' => array(
                                'className' => 'SubPreference',
                                'foreignKey' => 'type_id',
                                'order' => array('SubPreference.position ASC'),
                                'conditions' => array('SubPreference.is_active' => 1, 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $storeId)
                            )
                        )
            ));
            $this->ItemType->bindModel(
                    array('belongsTo' => array(
                            'Type' => array(
                                'className' => 'Type',
                                'foreignKey' => 'type_id',
                                'conditions' => array('Type.is_active' => 1, 'Type.is_deleted' => 0, 'Type.store_id' => $storeId)
                            )
            )));
            $this->ItemPrice->bindModel(
                    array('belongsTo' => array(
                            'Size' => array(
                                'className' => 'Size',
                                'foreignKey' => 'size_id',
                                'conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $storeId)
                            ),
                            'StoreTax' => array(
                                'className' => 'StoreTax',
                                'foreignKey' => 'store_tax_id',
                                'conditions' => array('StoreTax.is_active' => 1, 'StoreTax.is_deleted' => 0, 'StoreTax.store_id' => $storeId)
                            )
            )));
            $this->Topping->bindModel(
                    array('hasMany' => array(
                            'Topping' => array(
                                'className' => 'Topping',
                                'foreignKey' => 'addon_id',
                                'order' => array('Topping.position ASC'),
                                'conditions' => array('Topping.item_id' => $itemId, 'Topping.is_addon_category' => 0, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $storeId)
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
                                'conditions' => array('ItemType.is_active' => 1, 'ItemType.is_deleted' => 0, 'ItemType.store_id' => $storeId)
                            ), 'ItemPrice' => array(
                                'className' => 'ItemPrice',
                                'foreignKey' => 'item_id',
                                'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $storeId)
                            ), 'Topping' => array(
                                'className' => 'Topping',
                                'foreignKey' => 'item_id',
                                'order' => array('Topping.position ASC'),
                                'conditions' => array('Topping.is_addon_category' => 1, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $storeId)
                            )
                        )
            ));
            $this->Type->unBindModel(array('hasMany' => array('ItemType')));
            $productInfo = $this->Item->fetchItemDetail($itemId, $storeId);
            $toppingSizes = $this->AddonSize->fetchAddonSize($storeId);
            if ($productInfo) {
                $querySize = 0;
                $default_price = 0;
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

}
