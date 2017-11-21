<?php
App::uses('Controller', 'Controller');

class DeleteController extends Controller
{

    public $components = array('RequestHandler','Session','Common');
    public $helper = array('Encryption', 'Common');
    public $uses = array( 'User');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->layout = null;
        $this->autoRender = false;
    }

    public function order(){
        $store_id = $this->Session->read('store_id');
        $this->layout = null;
        $this->autoRender = false;
        $result = $this->User->query("CALL `sp_DeleteAllOrder`(".$store_id.");");
        if($result) {
            echo "Order data has been successfully deleted.";
            $this->redirect(array('controller' => 'products', 'action' => 'items'));
        }
    }

    public function product(){
        $store_id = $this->Session->read('store_id');
        $this->layout = null;
        $this->autoRender = false;
        $result = $this->User->query("CALL `sp_DeleteAllProduct`(".$store_id.");");
        if($result) {
            echo "Product data has been successfully deleted.";
            $this->redirect(array('controller' => 'products', 'action' => 'items'));

        }
    }

    public function Images($type) {
        switch ($type) {
            case "create" : $this->_createTempImages(); break;
            case "update" : $this->_updateTempImages(); break;
            case "delete" : $this->_deleteImages(); break;
        }
    }

    private function _deleteImages() {
        $this->loadModel('TempImages');
        $images = $this->TempImages->find('list', array('fields'=>array('id','image'), 'conditions' => array('TempImages.is_active' =>0)));
        $image_path=APP."webroot/MenuItem-Image/";
        foreach($images as $id => $image){
            if(!$image) continue;
            if(unlink($image_path.$image)){
                $this->TempImages->create();
                $data['TempImages']['id'] = $id;
                $data['TempImages']['is_active'] = 0;
                $data['TempImages']['is_deleted'] = 1;
                $this->TempImages->save($data);
            }
        }
    }

    private function _createTempImages() {
        $this->loadModel('TempImages');
        $path = APP."webroot/MenuItem-Image";
        $list = scandir($path);
        foreach($list as $image) {
          if (!in_array($image,array(".",".."))) {
            $this->TempImages->create();
            $data['TempImages']['image'] = $image;
            $data['TempImages']['is_active'] = 0;
            $this->TempImages->save($data);
          }
        }
    }
    private function _updateTempImages() {
        $path = "";
        $store_id = $this->Session->read('store_id');
        $this->loadModel('Items');
        $this->loadModel('TempImages');
        $images = $this->Items->find('list', array('fields'=>array('id','image'), 'conditions' => array('Items.image !=' =>'')));
        foreach($images as $key => $value) {
            $items = $this->TempImages->find('first', array('fields'=>array('id','image'), 'conditions' => array('TempImages.image' =>$value)));
            if(count($items)== 0) continue;
            $this->TempImages->create();
            $data['TempImages']['id'] = $items['TempImages']['id'];
            $data['TempImages']['is_active'] = 1;
            $this->TempImages->save($data);
        }
    }

}
