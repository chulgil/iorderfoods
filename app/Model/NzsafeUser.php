<?php
/**
 * Created by Bankcardservices,INC.
 * User: CG.LEE
 * Date: 2016. 1. 27.
 * Time:  10:54
 */

App::uses('AppModel', 'Model');
class NZSafeUser extends AppModel {

    var $name = 'nzsafe_users';

    public function saveUser($data=null){
        if($data){
            $res=$this->save($data);
            if($res){
                return true;
            }else{
                return false;
            }
        }

    }


    /* ------------------------------------------------
     Function name:getUser()
     Description:To find Detail of the Perticular user from user_nzsafe table
     created:01/24/2016
    ----------------------------------------------------- */
    public function getUser($userId = null) {
        if ($userId) {
            $userData = $this->find('first', array('limit'=>1,'order'=>'modified DESC','conditions' => array('user_id' => $userId,'is_deleted' => 0), 'recursive' => -1));
            if ($userData) {
                return $userData;
            } else {
                return false;
            }
        }
    }
}