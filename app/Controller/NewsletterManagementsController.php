<?php

App::uses('StoreAppController', 'Controller');

class NewsletterManagementsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common');
    public $uses = array('User', 'StoreGallery', 'Store', 'StoreBreak', 'StoreAvailability', 'StoreHoliday', 'Category', 'Tab', 'Permission', 'StoreTheme', 'Merchant', 'StoreTax', 'StoreFont');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    /* ------------------------------------------------
      Function name:configuration()
      Description:Manage Images for Somepage slider
      created:27/7/2015
      ----------------------------------------------------- */

    public function configuration() {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], $this->params['action'])) {
            $this->Session->setFlash(__("Permission Denied"));
            // $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }

        $this->layout = "admin_dashboard";
        $merchantId = $this->Session->read('admin_merchant_id');
        $storeId = $this->Session->read('admin_store_id');
        $this->set('userid', $this->Session->read('Auth.Admin.id'));
        $this->set('roleid', $this->Session->read('Auth.Admin.role_id'));
        $this->set('storeId', $storeId);
        if ($this->request->data) {
            if (!isset($this->request->data['Store']['is_booking_open'])) {
                $this->request->data['Store']['is_booking_open'] = 0;
            }
            if (!isset($this->request->data['Store']['is_take_away'])) {
                $this->request->data['Store']['is_take_away'] = 0;
            }
            if (!isset($this->request->data['Store']['is_delivery'])) {
                $this->request->data['Store']['is_delivery'] = 0;
            }
            $latitude = "";
            $longitude = "";
            if (trim($this->request->data['Store']['address']) && trim($this->request->data['Store']['city']) && trim($this->request->data['Store']['state']) && trim($this->request->data['Store']['zipcode'])) {

                $dlocation = trim($this->request->data['Store']['address']) . " " . trim($this->request->data['Store']['city']) . " " . trim($this->request->data['Store']['state']) . " " . trim($this->request->data['Store']['zipcode']);
                $address2 = str_replace(' ', '+', $dlocation);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $address2 . '&sensor=false');
                $output = json_decode($geocode);


                if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {

                } else {
                    $latitude = @$output->results[0]->geometry->location->lat;
                    $longitude = @$output->results[0]->geometry->location->lng;
                }
            }

            //Background Image Upload
            if (isset($this->data['Store']['back_image'])) {
                if ($this->data['Store']['back_image']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Store']['back_image'], '/storeBackground-Image/', $storeId);
                } elseif ($this->data['Store']['back_image']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }

                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $this->request->data['Store']['background_image'] = $response['imagename'];
                    }
                }
            }



            //Background Image Upload
            //Store Logo Upload
            if (isset($this->data['Store']['store_logophoto'])) {
                if ($this->data['Store']['store_logophoto']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Store']['store_logophoto'], '/storeLogo/', $storeId);
                } elseif ($this->data['Store']['store_logophoto']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }

                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $this->request->data['Store']['store_logo'] = $response['imagename'];
                    }
                }
            }

            if (isset($this->data['Store']['is_store_logo']) && $this->data['Store']['is_store_logo']) {
                $this->request->data['Store']['is_store_logo'] = 2;
            } else {
                $this->request->data['Store']['is_store_logo'] = 1;
            }

            if ($latitude && $longitude) {
                $this->request->data['Store']['latitude'] = $latitude;
                $this->request->data['Store']['logitude'] = $longitude;
            }

            if ($this->Store->saveStoreInfo($this->request->data['Store'])) {
                if (!empty($this->request->data['StoreTax'])) {
                    foreach ($this->request->data['StoreTax'] as $key => $taxvalue) {

                        $taxdata['id'] = $taxvalue['id'];
                        $taxdata['tax_value'] = $taxvalue['tax_value'];
                        $this->StoreTax->saveStoreTax($taxdata);
                    }
                }
                $this->Session->write('storeName', $this->request->data['Store']['store_name']);
                $this->Session->setFlash(__("Store Configuration details successfully Updated"), 'alert_success');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            }
        }

        $fontOptions = $this->StoreFont->getFonts();
        $this->set('fontOptions', $fontOptions);

        $themeOptions = $this->StoreTheme->getThemes();
        $this->set('themeOptions', $themeOptions);
        $storeInfo = $this->Store->fetchStoreDetail($storeId, $merchantId);
        if (!empty($storeInfo)) {
            $this->request->data['Store'] = $storeInfo['Store'];
        }
        $storeTax = $this->StoreTax->storeTaxInfo($storeId);
        if (!empty($storeTax)) {
            $this->request->data['StoreTax'] = $storeTax;
        } else {
            $createStoretax = array();
            for ($i = 1; $i <= 4; $i++) {
                $createStoretax['store_id'] = $storeId;
                $createStoretax['tax_name'] = "Tax" . $i;
                $createStoretax['tax_value'] = '';
                $createStoretax['id'] = '';
                $this->StoreTax->saveStoreTax($createStoretax);
            }
            $storeTax = $this->StoreTax->storeTaxInfo($storeId);
            $this->request->data['StoreTax'] = $storeTax;
        }
    }

}
