<?php

App::uses('AppModel', 'Model');

class StoreGallery extends AppModel {

    var $name = 'StoreGallery';

    /* ------------------------------------------------
      Function name:getStoreSliderImages()
      Description:To get images of store slider
      created:22/7/2015
      ----------------------------------------------------- */

    public function getStoreSliderImages($storeID = null, $merchantID = null) {
        $storeSliderImages = $this->find('all', array('conditions' => array('StoreGallery.store_id' => $storeID, 'StoreGallery.merchant_id' => $merchantID, 'StoreGallery.is_deleted' => 0), 'fields' => array('StoreGallery.id', 'StoreGallery.store_id', 'StoreGallery.merchant_id', 'StoreGallery.image', 'StoreGallery.description', 'StoreGallery.is_active'),'order'=>array('position'=>'ASC')));
        return $storeSliderImages;
    }

    /* ------------------------------------------------
      Function name:getStoreSliderImages()
      Description:To get images of store slider
      created:22/7/2015
      ----------------------------------------------------- */

    public function saveStoreSliderImage($imageData = null) {
        if ($imageData) {
            if ($this->save($imageData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

}
