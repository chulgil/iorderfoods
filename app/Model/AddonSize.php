<?php

App::uses('AppModel', 'Model');

class AddonSize extends AppModel {

    var $name = 'AddonSize';

    /* ------------------------------------------------
      Function name:saveAddonSize()
      Description:To Save the Category Size
      created:07/8/2015
      ----------------------------------------------------- */

    public function saveAddonSize($sizeData = null) {
        if ($sizeData) {
            if ($this->save($sizeData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:checkAddonSizeUniqueName()
      Description:to check Category size name is unique
      created:7/8/2015
      ----------------------------------------------------- */

    public function checkAddonSizeUniqueName($size = null, $storeId = null, $merchantId = null, $SizeId = null) {


        $conditions = array('LOWER(AddonSize.size)' => strtolower($size), 'AddonSize.store_id' => $storeId, 'AddonSize.merchant_id' => $merchantId, 'AddonSize.is_deleted' => 0);
        if ($SizeId) {
            $conditions['AddonSize.id !='] = $SizeId;
        }
        $size = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($size) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ------------------------------------------------
      Function name:getAddonSizeDetail()
      Description:To find Detail of the Perticular Add-ons size from size table
      created:7/8/2015
      ----------------------------------------------------- */

    public function getAddonSizeDetail($sizeId = null, $storeId = null) {
        $sizeDetail = $this->find('first', array('conditions' => array('AddonSize.store_id' => $storeId, 'AddonSize.id' => $sizeId)));
        if ($sizeDetail) {
            return $sizeDetail;
        }
    }

    /* ------------------------------------------------
      Function name : fetchAddonSize()
      Description : Fetch all add-on sizes
      created : 21/09/2015
      ----------------------------------------------------- */

    public function fetchAddonSize($storeId = null) {
        $sizeDetail = $this->find('list', array('fields' => array('id', 'size'), 'conditions' => array('AddonSize.store_id' => $storeId, 'AddonSize.is_active' => 1, 'AddonSize.is_deleted' => 0)));
        if (!empty($sizeDetail)) {
            $arr['0'] = 1;
            foreach ($sizeDetail as $key => $value) {
                $arr[$key] = $value;
            }
        } else {
            $arr['0'] = 1;
        }
        return $arr;
    }

    public function fetchAddonPercentage($addOnId = null, $storeId = null) {
        $percentage = $this->find('first', array('fields' => array('price_percentage'), 'conditions' => array('AddonSize.store_id' => $storeId, 'AddonSize.id' => $addOnId)));

        return $percentage;
    }

    public function getAddonSizeDetailById($sizeId = null) {
        $sizeDetail = $this->findById($sizeId);
        if ($sizeDetail) {
            return $sizeDetail;
        }
         
    }
    public function getAddonSizeDetailByStoreId($storeId = null) {
        $sizeDetail = $this->find('all', array('conditions' => array('AddonSize.store_id' => $storeId)));
        if ($sizeDetail) {
            return $sizeDetail;
        }
    }
     public function getAddonSizeDetailByMerchantId($merchantId = null) {
        $sizeDetail = $this->find('all', array('conditions' => array('AddonSize.merchant_id' => $merchantId)));
        if ($sizeDetail) {
            return $sizeDetail;
        }
    }
    public function getAddonSizeBySize($storeId=null,$size=null){
        $conditions = array('LOWER(AddonSize.size)'=>strtolower($size),'AddonSize.store_id'=>$storeId);        
        $categoryDetail = $this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
        return $categoryDetail; 
    }
    public function checkAddOnSize($size=null,$storeId=null,$AddonSizeId=null){
        $conditions = array('LOWER(AddonSize.size)'=>strtolower($size),'AddonSize.store_id'=>$storeId);
            if($AddonSizeId){
                $conditions['AddonSize.id !=']=$AddonSizeId;
            }
            $addonSize =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));   
            if($addonSize){
                return 0;
            }else{
                return 1;
            }
    }
    public function getHqStoresInfo($merchant_id=null){
        
        $merchantList = $this->Store->getMerchantStores($merchantId);           
        return $merchantList; 
    }
    public function checkAddOnSizeWithId($AddonSizeId=null){
        $conditions = array('AddonSize.id'=>$AddonSizeId);
            $addonSize =$this->find('first',array('fields'=>array('id','size','store_id'),'conditions'=>$conditions));  
            return $addonSize;

    }
    /* ------------------------------------------------
      Function name:getAddonSizeDetail()
      Description:To find Detail of the Perticular Add-ons size from size table
      created:7/8/2015
      ----------------------------------------------------- */

    public function getAddonSize($sizeId = null, $storeId = null) {
        $sizeDetail = $this->find('first', array('fields'=>array('AddonSize.size'),'conditions' => array('AddonSize.store_id' => $storeId, 'AddonSize.id' => $sizeId)));
        if ($sizeDetail) {
            return $sizeDetail;
        }
    }
    

}
