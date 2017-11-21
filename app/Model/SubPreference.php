<?php

App::uses('AppModel', 'Model');

class SubPreference extends AppModel {

    var $name = 'SubPreference';

    /* ------------------------------------------------
      Function name:savePage()
      Description:To Save Pages
      created:21/8/2015
      ----------------------------------------------------- */

    public function saveSubPreference($Data = null) {
        if ($Data) {
            if ($this->save($Data)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:checkSubPreference()
      Description:To check uniqueness
      created:26/11/2015
      ----------------------------------------------------- */

    public function checkSubPreference($subPreference = null, $storeID = null, $type_id = null, $subPreferenceId = null) {
        $conditions = array('LOWER(SubPreference.name)' => strtolower($subPreference), 'SubPreference.store_id' => $storeID, 'SubPreference.is_deleted' => 0, 'SubPreference.type_id' => $type_id);
        if ($subPreferenceId) {
            $conditions['SubPreference.id !='] = $subPreferenceId;
        }
        $subPre = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($subPre) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ------------------------------------------------
      Function name:getSubPreferenceDetail()
      Description:To get Sub-preference details
      created:26/11/2015
      ----------------------------------------------------- */

    public function getSubPreferenceDetail($SubPreferenceId = null, $storeId = null) {
        $conditions = array('SubPreference.id' => $SubPreferenceId, 'SubPreference.is_deleted' => 0);
        if ($storeId) {
            $conditions['SubPreference.store_id'] = $storeId;
        }
        $SubPreferenceDetail = $this->find('first', array('conditions' => $conditions));

        if ($SubPreferenceDetail) {
            return $SubPreferenceDetail;
        }
    }

    public function findSubpreferenceList($storeId = null) {
        $categoryList = $this->find('all', array('conditions' => array('SubPreference.store_id' => $storeId, 'SubPreference.is_deleted' => 0)));
        if ($categoryList) {
            return $categoryList;
        }
    }

    /* ------------------------------------------------
      Function name:checkTypeUniqueName()
      Description:to check Type name is unique
      created:7/8/2015
      ----------------------------------------------------- */

    public function checkSubPreferenceUniqueName($typeName = null, $storeId = null, $TypeId = null, $subPreferenceId = null) {
        $conditions = array('LOWER(SubPreference.name)' => strtolower($typeName), 'SubPreference.store_id' => $storeId, 'SubPreference.type_id' => $TypeId, 'SubPreference.is_deleted' => 0);
        if ($subPreferenceId) {
            $conditions['SubPreference.id !='] = $subPreferenceId;
        }
        $type = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($type) {
            return 0;
        } else {
            return 1;
        }
    }

    public function checkSubPreferenceWithId($subPreferenceId = null) {
        $conditions = array('SubPreference.id' => $subPreferenceId);
        $subPreferenceIdDet = $this->find('first', array('fields' => array('id', 'store_id'), 'conditions' => $conditions));
        return $subPreferenceIdDet;
    }

    public function getSubslist($storeId = null) {
        $categoryList = $this->find('all', array('fields' => array('id', 'item_id', 'size_id', 'price',
                'sub_preference_id'), 'conditions' => array('SubPreference.store_id' => $storeId, 'SubPreference.is_deleted' => 0, 'SubPreference.is_active' => 1)));
        if ($categoryList) {
            return $categoryList;
        }
    }

    public function fetchSubPreferencePrice($subPreferenceId = null, $storeId = null) {
        if ($subPreferenceId) {
            // echo $itemId;die;
            $price = $this->find('first', array('fields' => array('price', 'name'), 'conditions' => array('SubPreference.id' => $subPreferenceId, 'SubPreference.store_id' => $storeId, 'SubPreference.is_active' => 1, 'SubPreference.is_deleted' => 0)));
            if ($price) {

                return $price;
            } else {
                return false;
            }
        }
    }

}
