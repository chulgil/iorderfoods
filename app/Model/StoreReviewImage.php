<?php

App::uses('AppModel', 'Model');

class StoreReviewImage extends AppModel {

    public function saveStoreReviewImage($imageData = null) {
        if ($imageData) {
            $this->create();
            if ($this->save($imageData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    public function getAllReviewImages($store_review_id = null) {
        if (!empty($store_review_id)) {
            $reviewImages = $this->find('all', array('conditions' => array('StoreReviewImage.store_review_id' => $store_review_id, 'StoreReviewImage.is_deleted' => 0)));
            return $reviewImages;
        } else {
            return false;
        }
    }

    public function getAllStoreReviewImages($storeId = null) {
        $Reviews = $this->find('all', array('limit' => '10', 'order' => array('StoreReviewImage.created DESC'), 'conditions' => array('StoreReviewImage.store_id' => $storeId, 'StoreReviewImage.is_active' => 1, 'StoreReviewImage.is_deleted' => 0), 'fields' => array('StoreReviewImage.image')));
        return $Reviews;
    }

    public function countStoreReviewImages($storeId = null) {
        if(!empty($storeId)) {
            $Reviews = $this->find('count', array('conditions' => array('StoreReviewImage.store_id' => $storeId, 'StoreReviewImage.is_active' => 1, 'StoreReviewImage.is_deleted' => 0)));
            return $Reviews;
        }else{
            return false;
        }
    }

}
