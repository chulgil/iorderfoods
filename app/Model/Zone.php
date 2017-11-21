<?php

App::uses('AppModel', 'Model');

class Zone extends AppModel {

    var $name = 'Zone';

    /* ------------------------------------------------
      Function name:saveZone()
      Description:To Save the Zone info
      created:15/7/2016
      ----------------------------------------------------- */

    public function saveZone($Data = null) {
        if ($Data) {
            if ($this->save($Data)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    public function getzones($storeId = null) {
        $this->bindModel(array('hasMany' => array('ZoneCoordinate' => array('className' => 'ZoneCoordinate', 'foreignKey' => 'zone_id', 'conditions' => array('ZoneCoordinate.is_deleted' => 0, 'ZoneCoordinate.is_active' => 1, 'ZoneCoordinate.store_id' => $storeId), 'fields' => array('id', 'lat', 'long')))), false);
        $zoneDetail = $this->find('all', array('recursive' => 3, 'conditions' => array('Zone.store_id' => $storeId, 'Zone.is_active' => 1, 'Zone.is_deleted' => 0, 'Zone.type' => 0), 'fields' => array('id', 'name', 'fee')));
        return $zoneDetail;
    }

    public function checkUniqueZone($Name = null, $storeId = null, $zoneId = null) {
        $conditions = array('LOWER(Zone.name)' => strtolower($Name), 'Zone.store_id' => $storeId, 'Zone.is_deleted' => 0);
        if ($zoneId) {
            $conditions['Zone.id !='] = $zoneId;
        }
        $zone = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($zone) {
            return false;
        } else {
            return true;
        }
    }

    public function getzoneinfo($id = null) {
        $zoneDetail = $this->find('first', array('recursive' => 0, 'conditions' => array('Zone.id' => $id, 'Zone.is_active' => 1, 'Zone.is_deleted' => 0), 'fields' => array('id', 'name', 'fee')));
        return $zoneDetail;
    }

    public function getCirclezones($storeId = null) {
        $zoneDetail = $this->find('all', array('recursive' => -1, 'conditions' => array('Zone.store_id' => $storeId, 'Zone.is_active' => 1, 'Zone.is_deleted' => 0, 'Zone.type' => 1), 'fields' => array('id', 'name', 'fee', 'distance')));
        return $zoneDetail;
    }

}
