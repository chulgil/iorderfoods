<?php

App::uses('StoreAppController', 'Controller');

class ZonesController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Form', 'Common');
    public $uses = array('Store', 'Zone', 'ZoneCoordinate');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add_zone');
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List Menu Items
      created:5/8/2015
      ----------------------------------------------------- */

    public function dashboard($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $latlong = $this->Store->getlatlong($storeID);
        $zones = $this->Zone->getzones($storeID);
        $this->set(compact('latlong', 'zones'));
    }

    public function dash($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $latlong = $this->Store->getlatlong($storeID);
        $zones = $this->Zone->getzones($storeID);
        $this->loadModel('StoreSetting');
        $storeDeliverySetting = $this->StoreSetting->find('first', array('conditions' => array('store_id' => $storeID), 'fields' => array('delivery_allow','delivery_zone_type')));
        $this->set(compact('latlong', 'zones', 'storeDeliverySetting'));
    }

    public function add_zone() {
        $this->layout = false;
        $this->autoRender = false;
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $decodedjson = html_entity_decode($this->request->data['zcoords']);
        $jsondata = json_decode($decodedjson);
        if ($this->request->data) {
            if ($this->Zone->checkUniqueZone($this->request->data['zname'], $storeID)) {
                $zonedata['name'] = $this->request->data['zname'];
                $zonedata['fee'] = $this->request->data['zfee'];
                $zonedata['store_id'] = $storeID;
                $zonedata['merchant_id'] = $merchant_id;

                if ($this->Zone->saveZone($zonedata)) {
                    if ($jsondata) {
                        $cordata['id'] = '';
                        $cordata['zone_id'] = $this->Zone->getLastInsertId();
                        $cordata['store_id'] = $storeID;
                        foreach ($jsondata as $jvalue) {
                            $cordata['lat'] = $jvalue->lat;
                            $cordata['long'] = $jvalue->long;
                            $this->ZoneCoordinate->saveZonecord($cordata);
                        }
                    }
                    $response['status'] = 1;
                    $response['msg'] = 'Delivery Zone successfully created';
                    $this->Session->setFlash(__("Delivery Zone successfully created"), 'alert_success');
                    echo json_encode($response);
                }
            } else {
                $response['status'] = 0;
                $response['msg'] = 'Delivery zone name already exists';
                echo json_encode($response);
            }
        }
    }

    public function deletezone($EncryptedzoneID) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Zone']['store_id'] = $this->Session->read('admin_store_id');
        $data['Zone']['id'] = $this->Encryption->decode($EncryptedzoneID);
        $data['Zone']['is_deleted'] = 1;
        if ($this->Zone->saveZone($data)) {
            $this->Session->setFlash(__("Zone deleted"), 'alert_success');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
    }

    /* ------------------------------------------------
      Function name:circle()
      Description:Add Zones using miles in circle
      created:07/10/2016
      ----------------------------------------------------- */

    function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {
        $earth_radius = 6371;

        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d;
    }

    public function circle() {

        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $this->Store->unBindModel(array('hasMany' => array('StoreGallery', 'StoreContent')));
        $latlong = $this->Store->getlatlong($storeID);
        //pr($latlong);die;
        $zones = $this->Zone->getCirclezones($storeID);
        //pr($zones);die;
        $this->loadModel('StoreSetting');
        $storeDeliverySetting = $this->StoreSetting->find('first', array('conditions' => array('store_id' => $storeID), 'fields' => array('delivery_allow','delivery_zone_type')));
        $this->set(compact('latlong', 'zones', 'storeDeliverySetting'));
//        $distance = $this->getDistance(33.8358851, -118.3112146, 33.809985, -118.3085527);
//        $zoneInfo = $this->Zone->find('all', array('conditions' => array('store_id' => 2, 'is_active' => 1, 'is_deleted' => 0, 'type' => 1), 'fields' => array('distance', 'fee', 'name'), 'order' => array('distance' => 'ASC')));
//        foreach ($zoneInfo as $zone) {
//            $zoneDistance = $zone['Zone']['distance'] / 1000;
//            if ($distance <= $zoneDistance) {
//                echo "Within 100 kilometer radius";
//                pr($zone);die;
//                $this->Session->write('Zone.id', $zone['Zone']['id']);
//                $this->Session->write('Zone.name', $zone['Zone']['name']);
//                $this->Session->write('Zone.fee', $zone['Zone']['fee']);
//                break;
//            }
//        }
    }

    /* ------------------------------------------------
      Function name:addZoneCircle()
      Description:Add Zone circle
      created:10/10/2016
      ----------------------------------------------------- */

    public function addZoneCircle() {
        $this->layout = false;
        $this->autoRender = false;
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        if ($this->request->is('post')) {
            if ($this->Zone->checkUniqueZone($this->request->data['Zone']['name'], $storeID)) {
                $this->request->data['Zone']['store_id'] = $storeID;
                $this->request->data['Zone']['merchant_id'] = $merchant_id;
                $this->request->data['Zone']['type'] = 1;
                $this->request->data['Zone']['distance'] = round($this->request->data['Zone']['distance'] * 1609.344, 2);
                $this->Zone->create();
                if ($this->Zone->saveZone($this->request->data)) {
                    $this->Session->setFlash(__("Delivery Zone successfully created"), 'alert_success');
                    $this->redirect(array('controller' => 'zones', 'action' => 'circle'));
                } else {
                    $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                    $this->redirect(array('controller' => 'zones', 'action' => 'circle'));
                }
            } else {
                $this->Session->setFlash(__("Delivery zone name already exists"), 'alert_failed');
                $this->redirect(array('controller' => 'zones', 'action' => 'circle'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:checkZoneName()
      Description:Check zone name already exist or not
      created:10/10/2016
      ----------------------------------------------------- */

    public function checkZoneName() {
        $this->autoRender = false;
        if ($_GET) {
            $zoneId = null;
            if (!empty($_GET['id'])) {
                $zoneId = $this->Encryption->decode($_GET['id']);
            }
            $zoneName = $_GET['data']['Zone']['name'];
            $storeId = $this->Session->read('admin_store_id');
            $zoneStatus = $this->Zone->checkUniqueZone($zoneName, $storeId, $zoneId);
            echo json_encode($zoneStatus);
        }
    }

    /* ------------------------------------------------
      Function name:getZoneDetail()
      Description:get zone detail using zone id
      created:10/10/2016
      ----------------------------------------------------- */

    public function getZoneDetail() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'post', 'put')) && !empty($this->request->data['zoneId'])) {
            $zoneId = $this->Encryption->decode($this->request->data['zoneId']);
            $zoneDetail = $this->Zone->findById($zoneId);
            $this->request->data['Zone']['distance'] = round($zoneDetail['Zone']['distance'] / 1609.344, 2);
            $this->request->data['Zone']['id'] = $this->request->data['zoneId'];
            $this->request->data['Zone']['name'] = $zoneDetail['Zone']['name'];
            $this->request->data['Zone']['fee'] = $zoneDetail['Zone']['fee'];
            $this->request->data = $this->request->data;
            $this->render('/Elements/zone/edit_zone');
        }
    }

    public function getDashZoneDetail() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'post', 'put')) && !empty($this->request->data['zoneId'])) {
            $zoneId = $this->Encryption->decode($this->request->data['zoneId']);
            $zoneDetail = $this->Zone->findById($zoneId);
            //$this->request->data['Zone']['distance'] = round($zoneDetail['Zone']['distance'] / 1609.344, 2);
            $this->request->data['Zone']['id'] = $this->request->data['zoneId'];
            $this->request->data['Zone']['name'] = $zoneDetail['Zone']['name'];
            $this->request->data['Zone']['fee'] = $zoneDetail['Zone']['fee'];
            $this->request->data = $this->request->data;
            $this->render('/Elements/zone/dash_edit_zone');
        }
    }

    /* ------------------------------------------------
      Function name:editZone()
      Description:Edit Zone circle
      created:10/10/2016
      ----------------------------------------------------- */

    public function editZone() {
        $this->layout = false;
        $this->autoRender = false;
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['Zone']['id'] = $this->Encryption->decode($this->request->data['Zone']['id']);
            if ($this->Zone->checkUniqueZone($this->request->data['Zone']['name'], $storeID, $this->request->data['Zone']['id'])) {
                $this->request->data['Zone']['store_id'] = $storeID;
                $this->request->data['Zone']['merchant_id'] = $merchant_id;
                if (isset($this->request->data['Zone']['type'])) {
                    $this->request->data['Zone']['type'] = 0;
                } else {
                    $this->request->data['Zone']['type'] = 1;
                }

                if (isset($this->request->data['Zone']['distance']) && !empty($this->request->data['Zone']['distance'])) {
                    $this->request->data['Zone']['distance'] = round($this->request->data['Zone']['distance'] * 1609.344, 2);
                }
                if ($this->Zone->saveZone($this->request->data)) {
                    $this->Session->setFlash(__("Delivery Zone successfully created"), 'alert_success');
                    if (isset($this->request->data['Zone']['type']) && ($this->request->data['Zone']['type'] == 0)) {
                        $this->redirect(array('controller' => 'zones', 'action' => 'dash'));
                    } else {
                        $this->redirect(array('controller' => 'zones', 'action' => 'circle'));
                    }
                } else {
                    $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                    if (isset($this->request->data['Zone']['type']) && ($this->request->data['Zone']['type'] == 0)) {
                        $this->redirect(array('controller' => 'zones', 'action' => 'dash'));
                    } else {
                        $this->redirect(array('controller' => 'zones', 'action' => 'circle'));
                    }
                }
            } else {
                $this->Session->setFlash(__("Delivery zone name already exists"), 'alert_failed');
                $this->redirect(array('controller' => 'zones', 'action' => 'circle'));
            }
        }
    }

}
