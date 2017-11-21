<?php

App::uses('AppModel', 'Model');

class Size extends AppModel {

    var $name = 'Size';

    /* ------------------------------------------------
      Function name:getCategorySizes()
      Description:To find list of the categories from category table
      created:3/8/2015
      ----------------------------------------------------- */

    public function getCategorySizes($categoryId = null, $storeId = null) {
        $sizeList = $this->find('list', array('fields' => array('id', 'size'), 'conditions' => array('Size.category_id' => $categoryId, 'Size.store_id' => $storeId, 'Size.is_active' => 1, 'Size.is_deleted' => 0)));
        if ($sizeList) {
            return $sizeList;
        }
    }

    /* ------------------------------------------------
      Function name:saveSize()
      Description:To Save the Category Size
      created:07/8/2015
      ----------------------------------------------------- */

    public function saveSize($sizeData = null) {
        if ($sizeData) {
            if ($this->save($sizeData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:getSizeName()
      Description:To find list of the categories from category table
      created:3/8/2015
      ----------------------------------------------------- */

    public function getSizeName($sizeId = null) {
        $size = $this->find('first', array('fields' => array('size'), 'conditions' => array('Size.id' => $sizeId, 'Size.is_active' => 1, 'Size.is_deleted' => 0)));
        if ($size) {
            return $size;
        }
    }

    /* ------------------------------------------------
      Function name:getSizeDetail()
      Description:To find Detail of the Perticular category size from size table
      created:7/8/2015
      ----------------------------------------------------- */

    public function getSizeDetail($sizeId = null, $storeId = null) {
        $sizeDetail = $this->find('first', array('conditions' => array('Size.store_id' => $storeId, 'Size.id' => $sizeId)));
        if ($sizeDetail) {
            return $sizeDetail;
        }
    }

    /* ------------------------------------------------
      Function name:checkSizeUniqueName()
      Description:to check Category size name is unique
      created:7/8/2015
      ----------------------------------------------------- */

    public function checkSizeUniqueName($size = null, $storeId = null, $categoryId = null, $SizeId = null) {


        $conditions = array('LOWER(Size.size)' => strtolower($size), 'Size.store_id' => $storeId, 'Size.category_id' => $categoryId, 'Size.is_deleted' => 0);
        if ($SizeId) {
            $conditions['Size.id !='] = $SizeId;
        }
        $size = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($size) {
            return 0;
        } else {
            return 1;
        }
    }

    public function getSizeIdByName($categoryId = null, $storeId = null, $sizeName = null) {
        $conditions = array('LOWER(Size.size)' => strtolower($sizeName), 'Size.store_id' => $storeId, 'Size.category_id' => $categoryId, 'Size.is_active' => 1, 'Size.is_deleted' => 0);
        $categoryDetail = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        return $categoryDetail;
    }

    public function getSizeIdByNameOnly($sizeName = null, $storeId = null) {
        $conditions = array('LOWER(Size.size)' => strtolower($sizeName), 'Size.store_id' => $storeId, 'Size.is_active' => 1, 'Size.is_deleted' => 0);
        $categoryDetail = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        return $categoryDetail;
    }

    public function findSizeList($storeId = null) {
        $categoryList = $this->find('all', array('conditions' => array('Size.store_id' => $storeId, 'Size.is_deleted' => 0)));
        if ($categoryList) {
            return $categoryList;
        }
    }

    public function findSizeListByMerchantId($merchantId = null) {
        $categoryList = $this->find('all', array('conditions' => array('Size.merchant_id' => $merchantId, 'Size.is_deleted' => 0, 'Category.is_deleted' => 0)));
        if ($categoryList) {
            return $categoryList;
        }
    }

    public function getSizeDetailById($sizeId = null) {
        $sizeDetail = $this->findById($sizeId);
        if ($sizeDetail) {
            return $sizeDetail;
        }
    }

    public function checkSizeWithId($sizeId = null) {
        $conditions = array('Size.id' => $sizeId);
        $Size = $this->find('first', array('fields' => array('id','store_id'), 'conditions' => $conditions));
        return $Size;
    }

    public function getSizeListWithDuplicateName($merchant_id = null, $catIds = null) {
        $typeList = $this->find('list', array('fields' => array('size', 'size'), 'conditions' => array('category_id' => $catIds, 'merchant_id' => $merchant_id, 'is_active' => 1, 'is_deleted' => 0), 'group' => 'size'));
        return $typeList;
    }

    public function getSizeListWithIndex($merchant_id = null, $sizeName = null, $store_id = null, $category_id = null) {
        $typeList = $this->find('first', array('fields' => array('id'), 'conditions' => array('size' => strtolower($sizeName), 'merchant_id' => $merchant_id,'is_active' => 1, 'is_deleted' => 0)));
        if (!empty($typeList)) {
            $typeListArray = array();
            foreach ($typeList as $key => $List) {
                $typeListArray[] = $List;
            }
            return $typeListArray;
        } else {
            return FALSE;
        }
    }

}
