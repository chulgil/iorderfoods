<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppModel', 'Model');

class DefaultFeaturedSection extends AppModel {

    var $name = 'DefaultFeaturedSection';

    public function getAllDetail() {
        $featureContent = $this->find('all', array('conditions' => array('is_active' => 1, 'is_deleted' => 0)));
        return $featureContent;
    }

}
