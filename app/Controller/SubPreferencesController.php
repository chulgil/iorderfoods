<?php

App::uses('StoreAppController', 'Controller');

class SubPreferencesController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption');
    public $uses = array('Type', 'SubPreference');

    public function beforeFilter() {
        parent::beforeFilter();
        $adminfunctions = array('addSubPreference', 'index', 'deleteSubPreference', 'editSubPreference', 'uploadfile', 'updateOrder');
        if (in_array($this->params['action'], $adminfunctions)) {
            if (!$this->Common->checkPermissionByaction($this->params['controller'])) {
                $this->Session->setFlash(__("Permission Denied"));
                $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:addSubPreference()
      Description:To add addSubPreference in SubPreference table
      created:25/11/2015
      ----------------------------------------------------- */

    public function addSubPreference() {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $storePreferences = $this->Type->getStoreType($storeID);
        $this->set('storePreferences', $storePreferences);
        if ($this->request->is('post') && !empty($this->request->data['SubPreference']['name1'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $subPreference = trim($this->data['SubPreference']['name1']);
            $isUniqueName = $this->SubPreference->checkSubPreference($subPreference, $storeID, $this->data['SubPreference']['type_id1']);
            if ($isUniqueName) {
                $typedata['store_id'] = $storeID;
                $typedata['name'] = trim($this->data['SubPreference']['name1']);
                $typedata['price'] = trim($this->data['SubPreference']['price']);
                $typedata['is_active'] = $this->data['SubPreference']['is_active1'];
                $typedata['type_id'] = $this->data['SubPreference']['type_id1'];
                $this->SubPreference->create();
                $this->SubPreference->saveSubPreference($typedata);
                $this->request->data = '';
                $this->Session->setFlash(__("SubPreference Successfully Added"), 'alert_success');
                $this->redirect(array('controller' => 'SubPreferences', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("SubPreference Already exists"), 'alert_failed');
                $this->redirect(array('controller' => 'SubPreferences', 'action' => 'index'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:To display the list of SubPreference
      created:25/11/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        /*         * ****start******* */
        $value = "";
        $criteria = "SubPreference.store_id =$storeID AND SubPreference.is_deleted=0";
        $order = '';
        $pagingFlag = true;
        if ($this->Session->read('SubPreferenceSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('SubPreferenceSearchData'), true);
        } else {
            $this->Session->delete('SubPreferenceSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('SubPreferenceSearchData', json_encode($this->request->data));
            if ($this->request->data['SubPreference']['is_active'] != '') {
                $active = trim($this->request->data['SubPreference']['is_active']);
                $criteria .= " AND (SubPreference.is_active =$active)";
            }

            if (!empty($this->request->data['SubPreference']['search'])) {
                $search = trim($this->request->data['SubPreference']['search']);
                $criteria .= " AND (SubPreference.name LIKE '%" . $search . "%')";
            }
            if ($this->request->data['SubPreference']['type_id']) {
                $criteria .= " AND (SubPreference.type_id =" . $this->request->data['SubPreference']['type_id'] . ")";
                $order = 'SubPreference.position ASC';
                $pagingFlag = false;
            }
        }
        $this->SubPreference->bindModel(
                array(
            'belongsTo' => array(
                'Type' => array(
                    'className' => 'Type',
                    'foreignKey' => 'type_id',
                    'type' => 'inner',
                    'conditions' => array('Type.is_deleted' => 0, 'Type.is_active' => 1),
                    'fields' => array('id', 'name'),
                    'type' => 'INNER'
                )
            )
                ), false
        );

        if ($order == '') {
            $order = 'SubPreference.created DESC';
        }

        $typedetail = '';
        if ($pagingFlag) {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $typedetail = $this->paginate('SubPreference');
        } else {
            //$typedetail=$this->SubPreference->find('all',array('conditions'=>array($criteria),'order'=>$order));
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $typedetail = $this->paginate('SubPreference');
        }
        //prx($typedetail);
        $this->set('list', $typedetail);
        $this->set('pagingFlag', $pagingFlag);

        $storePreference = $this->Type->getStoreType($storeID);
        $this->set('types', $storePreference);
        $storePreferences = $this->SubPreference->find('all', array('conditions' => array('SubPreference.store_id' => $storeID, 'SubPreference.is_deleted' => 0), 'group' => array('SubPreference.type_id')));
        $nList = array();
        if (!empty($storePreferences)) {
            foreach ($storePreferences as $iList) {
                if (!empty($iList['SubPreference']) && !empty($iList['Type'])) {
                    $nList[$iList['Type']['id']] = $iList['Type']['name'];
                }
            }
        }
        $this->set('customtypes', $nList);
    }

    /* ------------------------------------------------
      Function name:deleteSubPreference()
      Description:Delete SubPreference
      created:25/11/2015
      ----------------------------------------------------- */

    public function deleteSubPreference($EncryptTypeID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['SubPreference']['store_id'] = $this->Session->read('admin_store_id');
        $data['SubPreference']['id'] = $this->Encryption->decode($EncryptTypeID);
        $data['SubPreference']['is_deleted'] = 1;
        if ($this->SubPreference->saveSubPreference($data)) {
            $this->Session->setFlash(__("SubPreference deleted"), 'alert_success');
            $this->redirect(array('controller' => 'SubPreferences', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'SubPreferences', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:activateSubPreference()
      Description:Active/deactive type
      created:25/11/2015
      ----------------------------------------------------- */

    public function activateSubPreference($EncryptedTypeID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['SubPreference']['store_id'] = $this->Session->read('admin_store_id');
        $data['SubPreference']['id'] = $this->Encryption->decode($EncryptedTypeID);
        $data['SubPreference']['is_active'] = $status;
        if ($this->SubPreference->saveSubPreference($data)) {
            if ($status) {
                $SuccessMsg = "SubPreference Activated";
            } else {
                $SuccessMsg = "SubPreference Deactivated and will not get Display in the Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'SubPreferences', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'SubPreferences', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editType()
      Description:Edit Type
      created:7/8/2015
      ----------------------------------------------------- */

    public function editSubPreference($EncryptTypeID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['SubPreference']['id'] = $this->Encryption->decode($EncryptTypeID);
        $this->loadModel('SubPreference');
        $typeDetail = $this->SubPreference->getSubPreferenceDetail($data['SubPreference']['id'], $storeId);
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $typedata = array();
            $subpreferenceName = trim($this->data['SubPreference']['name']);
            $isUniqueName = $this->SubPreference->checkSubPreference($subpreferenceName, $storeId, $this->data['SubPreference']['type_id'], $data['SubPreference']['id']);
            if ($isUniqueName) {
                $typedata['id'] = $data['SubPreference']['id'];
                $typedata['price'] = trim($this->data['SubPreference']['price']);
                $typedata['name'] = trim($this->data['SubPreference']['name']);
                $typedata['type_id'] = $this->data['SubPreference']['type_id'];
                $typedata['is_active'] = $this->data['SubPreference']['is_active'];
                $typedata['store_id'] = $storeId;
                $this->SubPreference->saveSubPreference($typedata);
                $this->Session->setFlash(__("SubPreference Updated Successfully ."), 'alert_success');
                $this->redirect(array('controller' => 'SubPreferences', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("SubPreference Already exists"), 'alert_failed');
            }
        }
        $storePreferences = $this->Type->getStoreType($storeId);
        $this->set('storePreferences', $storePreferences);
        $this->request->data = $typeDetail;
    }

    /* ------------------------------------------------
      Function name:deleteMultipleSubPreference()
      Description:Delete multiple SubPreference
      created:25/11/2015
      ----------------------------------------------------- */

    public function deleteMultipleSubPreference() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['SubPreference']['store_id'] = $this->Session->read('admin_store_id');
        $data['SubPreference']['is_deleted'] = 1;
        if (!empty($this->request->data['SubPreference']['id'])) {
            $filter_array = array_filter($this->request->data['SubPreference']['id']);
            $i = 0;
            foreach ($filter_array as $k => $orderId) {
                $data['SubPreference']['id'] = $orderId;
                $this->SubPreference->saveSubPreference($data);
                $i++;
            }
            $del = $i . "  " . "SubPreference deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'SubPreferences', 'action' => 'index'));
        }
    }

    public function uploadfile() {
        $this->layout = 'admin_dashboard';
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            if ($tmp['SubPreference']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['SubPreference']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['SubPreference']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['SubPreference']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['SubPreference']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $i = 0;
                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B']) && !empty($row['C'])) {
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
                                $type = trim($row['C']);
                                $TypeData = $this->Type->getTypeIdByName($type, $storeId);
                                if (!empty($TypeData)) {
                                    $typeId = $TypeData['Type']['id'];
                                    if (!empty($row['A'])) {
                                        $isUniqueName = $this->SubPreference->checkSubPreferenceUniqueName($row['B'], $storeId, $typeId, $row['A']);
                                    } else {
                                        $isUniqueName = $this->SubPreference->checkSubPreferenceUniqueName($row['B'], $storeId, $typeId);
                                    }
                                    if ($isUniqueName) {
                                        $typedata['store_id'] = $storeId;
                                        $typedata['merchant_id'] = $merchantId;
                                        $typedata['name'] = $row['B'];
                                        if (!empty($row['C'])) {
                                            $typedata['type_id'] = $typeId;
                                        } else {
                                            $typedata['type_id'] = 0;
                                        }
                                        if (!empty($row['D'])) {
                                            $typedata['price'] = $row['D'];
                                        } else {
                                            $typedata['price'] = 0;
                                        }

                                        if (!empty($row['F'])) {
                                            $typedata['position'] = $row['F'];
                                        } else {
                                            $typedata['position'] = 0;
                                        }

                                        if (!empty($row['A'])) {
                                            $typedata['id'] = $row['A'];
                                        } else {
                                            $typedata['id'] = "";
                                            $this->SubPreference->create();
                                        }
                                        $this->SubPreference->saveSubPreference($typedata);
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->Session->setFlash(__($i . ' ' . 'Sub Preference has been saved'), 'alert_success');
            $this->redirect(array("controller" => "SubPreferences", "action" => "index"));
        }
    }

    public function download() {
        $storeId = $this->Session->read('admin_store_id');
        $this->SubPreference->bindModel(array('belongsTo' => array('Type' => array('fields' => 'Type.name'))));
        $result = $this->SubPreference->find('all', array('conditions' => array('SubPreference.store_id' => $storeId, 'SubPreference.is_deleted' => 0), 'order' => array('Type.name' => 'ASC', 'SubPreference.position' => 'ASC')));

        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
        $styleArray2 = array(
            'font' => array('name' => 'Arial', 'size' => '10', 'color' => array('rgb' => '444555'), 'bold' => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'D6D6D6'))
        );
        $styleArray = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '10',
                'color' => array('rgb' => 'ffffff'),
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '0295C9'),
            ),
        );
        ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
        $filename = 'SubPreference_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Type');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Sub Preference Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Preference Name');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Price($)');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Position');

        // $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", trim($data['SubPreference']['id']));
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", trim($data['SubPreference']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", trim($data['Type']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", trim($data['SubPreference']['price']));
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", trim($data['SubPreference']['is_active']));
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", trim($data['SubPreference']['position']));
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /* ------------------------------------------------
      Function name:updateOrder()
      Description: Update the display order
      created Date:16/12/2015
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function updateOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            foreach ($_GET as $key => $val) {
                $this->SubPreference->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
            }
        }
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('SubPreference');
            $storeID = $this->Session->read('admin_store_id');
            $this->SubPreference->bindModel(
                    array(
                'belongsTo' => array(
                    'Type' => array(
                        'className' => 'Type',
                        'foreignKey' => 'type_id',
                        'type' => 'inner',
                        'conditions' => array('Type.is_deleted' => 0, 'Type.is_active' => 1),
                        'fields' => array('id', 'name'),
                        'type' => 'INNER'
                    )
                )
                    ), false
            );
            $searchData = $this->SubPreference->find('all', array('conditions' => array('OR' => array('SubPreference.name LIKE' => '%' . $_GET['term'] . '%', 'Type.name LIKE' => '%' . $_GET['term'] . '%'), 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $storeID)));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['SubPreference']['name'], 'value' => $val['SubPreference']['name'], 'desc' => $val['SubPreference']['name'] . '-' . $val['Type']['name']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

}
