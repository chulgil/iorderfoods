<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppModel', 'Model');

class StoreFeaturedSection extends AppModel {

    var $name = 'StoreFeaturedSection';

    public function saveSection($templateData = null) {
        if ($templateData) {
            if ($this->save($templateData)) {
                return true; //Success
            } else {
                return false; // Failure
            }
        }
    }

}
