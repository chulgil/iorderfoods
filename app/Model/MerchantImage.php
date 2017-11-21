<?php

App::uses('AppModel', 'Model');

class MerchantImage extends AppModel {

    public function saveMerchantImage($imageData = null) {
        if ($imageData) {
            $this->create();
            if ($this->save($imageData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }
}
