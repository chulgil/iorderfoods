<?php

App::uses('AppModel', 'Model');

class MerchantGallery extends AppModel {

    var $name = 'MerchantGallery';

    /* ------------------------------------------------
      Function name:getStoreSliderImages()
      Description:To get images of store slider
      created:22/7/2015
      ----------------------------------------------------- */

    public function getSliderImages($merchantID = null) {
        $storeSliderImages = $this->find('all', array('conditions' => array('MerchantGallery.merchant_id' => $merchantID, 'MerchantGallery.is_deleted' => 0), 'fields' => array('MerchantGallery.id', 'MerchantGallery.merchant_id', 'MerchantGallery.image', 'MerchantGallery.description', 'MerchantGallery.is_active'), 'order' => array('MerchantGallery.position' => 'ASC')));
        return $storeSliderImages;
    }

    public function getSlidersImages($merchantID = null) {
        $storeSliderImages = $this->find('all', array('conditions' => array('MerchantGallery.merchant_id' => $merchantID, 'MerchantGallery.is_deleted' => 0, 'MerchantGallery.is_active' => 1), 'fields' => array('MerchantGallery.id', 'MerchantGallery.merchant_id', 'MerchantGallery.image', 'MerchantGallery.description', 'MerchantGallery.is_active'), 'order' => array('MerchantGallery.position' => 'ASC')));
        return $storeSliderImages;
    }

    /* ------------------------------------------------
      Function name:getStoreSliderImages()
      Description:To get images of store slider
      created:22/7/2015
      ----------------------------------------------------- */

    public function saveSliderImage($imageData = null) {
        if ($imageData) {
            if ($this->save($imageData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

}
