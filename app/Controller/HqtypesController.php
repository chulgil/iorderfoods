<?php

App::uses('HqAppController', 'Controller');

class HqtypesController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption');
    public $uses = array('Type', 'Store');
    public $layout = 'hq_dashboard';

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $merchant_id = $this->Session->read('merchantId');
        if ($this->request->is('post') && !empty($this->request->data['Type']['name'])) {
            $storeID = trim($this->request->data['Type']['store_id']);
            if ($storeID == 'All') {
                $storeList = $this->Store->find('all', array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0, 'Store.merchant_id' => $merchant_id), 'fields' => array('Store.id'), 'recursive' => -1));
            } else {
                $storeList[0]['Store']['id'] = $storeID;
            }
            $this->request->data = $this->Common->trimValue($this->request->data);
            $type = trim($this->request->data['Type']['name']);
            foreach ($storeList as $sList) {
                $isUniqueName = $this->Type->checkTypeUniqueName($type, $sList['Store']['id']);
                if ($isUniqueName) {
                    $typedata['store_id'] = $sList['Store']['id'];
                    $typedata['merchant_id'] = $merchant_id;
                    $typedata['name'] = trim($this->request->data['Type']['name']);
                    $typedata['min_value'] = $this->request->data['Type']['min_value'];
                    $typedata['max_value'] = $this->request->data['Type']['max_value'];
                    $typedata['is_active'] = $this->request->data['Type']['is_active'];
                    $this->Type->create();
                    $this->Type->saveType($typedata);
                }
            }
            $this->request->data = '';
            $this->Session->setFlash(__("Preference Successfully Added"), 'alert_success');
        }
        $this->_preferenceList();
    }

    /* ------------------------------------------------
      Function name:_preferenceList()
      Description:To display the list of type
      created:8/8/2016
      ----------------------------------------------------- */

    private function _preferenceList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $this->loadModel("Item");
        $this->loadModel("ItemType");
        $storeID = @$this->request->data['Type']['storeId'];
        $merchant_id = $this->Session->read('merchantId');
        /*         * ****start******* */
        $criteria = "Type.is_deleted=0 AND Type.merchant_id=$merchant_id";
        if (!empty($storeID)) {
            $criteria .= " AND Type.store_id =$storeID";
        }
        $order = '';
        $pagingFlag = true;
        if ($this->Session->read('HqTypeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqTypeSearchData'), true);
        } else {
            $this->Session->delete('HqTypeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        $flag = 0;
        if (!empty($this->request->data)) {
            $this->Session->write('HqTypeSearchData', json_encode($this->request->data));
            if ($this->request->data['Type']['isActive'] != '') {
                $active = trim($this->request->data['Type']['isActive']);
                $criteria .= " AND (Type.is_active =$active)";
            }

            if (!empty($this->request->data['Type']['search'])) {
                $search = trim($this->request->data['Type']['search']);
                $criteria .= " AND (Type.name LIKE '%" . $search . "%')";
            }
        }
        if ($order == '') {
            $order = 'Type.id DESC';
        }
        if ($flag) {
            $criteria .= " AND (Type.is_active =2)";
        }
        $typedetail = '';
        $this->Type->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('store_name')
                )
            )
                ), false
        );
        if ($pagingFlag) {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $typedetail = $this->paginate('Type');
        } else {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $typedetail = $this->paginate('Type');
        }
        $this->set('list', $typedetail);
        $this->set('pagingFlag', $pagingFlag);
    }

    /* ------------------------------------------------
      Function name:deleteMultipleType()
      Description:Delete multiple type
      created:08/8/2016
      ----------------------------------------------------- */

    public function deleteMultipleType() {
        $this->autoRender = false;
        $data['Type']['merchant_id'] = $this->Session->read('merchantId');
        $data['Type']['is_deleted'] = 1;
        if (!empty($this->request->data['Type']['id'])) {
            $filter_array = array_filter($this->request->data['Type']['id']);
            $i = 0;
            foreach ($filter_array as $orderId) {
                $data['Type']['id'] = $orderId;
                $this->Type->saveType($data);
                $i++;
            }
            $del = $i . "  " . "type deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'hqtypes', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:activateType()
      Description:Active/deactive type
      created:8/8/2016
      ----------------------------------------------------- */

    public function activateType($EncryptedTypeID = null, $status = 0) {
        $this->autoRender = false;
        $data['Type']['merchant_id'] = $this->Session->read('merchantId');
        $data['Type']['id'] = $this->Encryption->decode($EncryptedTypeID);
        $data['Type']['is_active'] = $status;
        if ($this->Type->saveType($data)) {
            if ($status) {
                $SuccessMsg = "Preference Activated";
            } else {
                $SuccessMsg = "Preference Deactivated and Preference will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqtypes', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtypes', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteType()
      Description:Delete Type
      created:8/8/2016
      ----------------------------------------------------- */

    public function deleteType($EncryptTypeID = null) {
        $this->autoRender = false;
        $data['Type']['merchant_id'] = $this->Session->read('merchantId');
        $data['Type']['id'] = $this->Encryption->decode($EncryptTypeID);
        $data['Type']['is_deleted'] = 1;
        if ($this->Type->saveType($data)) {
            $this->Session->setFlash(__("Preference deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqtypes', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtypes', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editType()
      Description:Edit Type
      created:7/8/2015
      ----------------------------------------------------- */

    public function editType($EncryptTypeID = null) {
        $merchantId = $this->Session->read('merchantId');
        $data['Type']['id'] = $this->Encryption->decode($EncryptTypeID);
        $this->loadModel('Type');
        $typeDetail = $this->Type->getTypeDetailById($data['Type']['id']);
        if ($this->request->is('post') && !empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $typedata = array();
            $type = trim($this->data['Type']['name']);
            $isUniqueName = $this->Type->checkTypeUniqueName($type, $typeDetail['Type']['store_id'], $data['Type']['id']);
            if ($isUniqueName) {
                $typedata['id'] = $data['Type']['id'];
                $typedata['name'] = trim($this->request->data['Type']['name']);
                $typedata['min_value'] = $this->request->data['Type']['min_value'];
                $typedata['max_value'] = $this->request->data['Type']['max_value'];
                $typedata['is_active'] = $this->request->data['Type']['is_active'];
                $typedata['merchant_id'] = $merchantId;
                $this->Type->saveType($typedata);
                $this->Session->setFlash(__("Preference Updated Successfully ."), 'alert_success');
                $this->redirect(array('controller' => 'hqtypes', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Preference Already exists"), 'alert_failed');
            }
        }
        $this->request->data = $typeDetail;
    }

    /* ------------------------------------------------
      Function name:typelisting()
      Description:To display the list of type
      created:8/8/2016
      ----------------------------------------------------- */

    public function typelisting($clearAction = null) {
        $this->loadmodel("Item");
        $this->loadmodel("ItemType");
        $storeID = @$this->request->data['ItemType']['store_id'];
        if (empty($storeID) && $this->Session->read('HqItemTypeSearchData')) {
            $data = json_decode($this->Session->read('HqItemTypeSearchData'), true);
            if (!empty($data['ItemType']['store_id'])) {
                $storeID = $data['ItemType']['store_id'];
            }
        }
        $merchant_id = $this->Session->read('merchantId');
        /*         * ****start******* */
        $criteria = "ItemType.merchant_id =$merchant_id AND ItemType.is_deleted=0";
        if (!empty($storeID)) {
            $criteria .=" AND ItemType.store_id =$storeID";
        }
        $order = '';
        $pagingFlag = true;
        if ($this->Session->read('HqItemTypeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqItemTypeSearchData'), true);
        } else {
            $this->Session->delete('HqItemTypeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        $flag = 0;
        if (!empty($this->request->data)) {
            $this->Session->write('HqItemTypeSearchData', json_encode($this->request->data));
            if ($this->request->data['ItemType']['is_active'] != '') {
                $active = trim($this->request->data['ItemType']['is_active']);
                $criteria .= " AND (ItemType.is_active =$active)";
            }

            if (!empty($this->request->data['Type']['search'])) {
                $search = trim($this->request->data['Type']['search']);
                $criteria .= " AND (Type.name LIKE '%" . $search . "%')";
            }

            if ($this->request->data['ItemType']['item_id'] != '') {
                $itemId = trim($this->request->data['ItemType']['item_id']);
                $criteria .= " AND (ItemType.item_id =$itemId)";
                $typeId = $this->ItemType->find('list', array('fields' => array('ItemType.type_id'), 'conditions' => array('ItemType.item_id' => $itemId, 'ItemType.store_id' => $storeID, 'ItemType.is_deleted' => 0)));
                $pagingFlag = false;
                if (count($typeId) > 0) {
                    $criteria .= " AND (ItemType.type_id IN (" . implode(',', array_unique($typeId)) . "))";
                    $order = 'ItemType.position ASC';
                } else {
                    $flag = 1;
                }
            }
        }
        if ($order == '') {
            $order = 'ItemType.position ASC';
        }

        $this->ItemType->bindModel(
                array(
            'belongsTo' => array(
                'Type' => array(
                    'className' => 'Type',
                    'foreignKey' => 'type_id',
                    'conditions' => array('Type.is_deleted' => 0, 'Type.is_active' => 1),
                    'fields' => array('id', 'name', 'is_active'),
                    'type' => 'INNER'
                ),
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'type' => 'INNER',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name')
                ), 'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('store_name')
                )
            )
                ), false
        );
        $typedetail = '';
        if ($pagingFlag) {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $typedetail = $this->paginate('ItemType');
        } else {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $typedetail = $this->paginate('ItemType');
        }
        $this->set('list', $typedetail);
        $this->set('pagingFlag', $pagingFlag);
        $nList = array();
        if (!empty($storeID)) {
            //$itemList = $this->Item->getallItemsByStore($storeID);
            $this->ItemType->bindModel(
                    array(
                        'belongsTo' => array(
                            'Type' => array(
                                'className' => 'Type',
                                'foreignKey' => 'type_id',
                                'conditions' => array('Type.is_deleted' => 0, 'Type.is_active' => 1),
                                'fields' => array('id', 'name', 'is_active'),
                                'type' => 'INNER'
                            ),
                            'Item' => array(
                                'className' => 'Item',
                                'foreignKey' => 'item_id',
                                'type' => 'INNER',
                                'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                                'fields' => array('id', 'name', 'category_id')
                            )
                        )
                    )
            );
            $this->Item->bindModel(array(
                'belongsTo' => array(
                    'Category' => array(
                        'className' => 'Category',
                        'foreignKey' => 'category_id',
                        'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                        'fields' => array('id', 'name', 'is_active'),
                        'type' => 'INNER'
                    ))
            ));
            $itemList = $this->ItemType->find('all', array('conditions' => array('ItemType.store_id' => $storeID, 'ItemType.is_deleted' => 0), 'group' => array('ItemType.item_id'), 'recursive' => 2));
            if (!empty($itemList)) {
                foreach ($itemList as $iList) {
                    if (!empty($iList['Item']) && !empty($iList['Type']) && !empty($iList['Item']['Category'])) {
                        $nList[$iList['Item']['id']] = $iList['Item']['name'];
                    }
                }
            }
        }
        $this->set('itemList', $nList);
    }

    public function uploadfile() {
        $this->layout = "hq_dashboard";
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            if ($tmp['Type']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['Type']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['Type']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['Type']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Type']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $merchantId = $this->Session->read('merchantId');
                $storeId = $this->request->data['Type']['store_id'];
                if ($storeId == "All") {
                    $storeId = $this->Store->find('list', array('fields' => array('id'), 'conditions' => array('Store.merchant_id' => $merchantId)));
                    $i = $this->typeForMultipleStore($storeId, $real_data, $merchantId);
                } else {
                    $i = $this->saveFileType($real_data, $storeId, $merchantId);
                }
                $this->Session->setFlash(__($i . ' ' . 'Preference has been saved'), 'alert_success');
                $this->redirect(array("controller" => "hqtypes", "action" => "index"));
            }
        }
    }

    public function typeForMultipleStore($storeIds = array(), $real_data = array(), $merchantId = null) {
        $i = 0;
        if (!empty($storeIds)) {
            foreach ($storeIds as $storeId) {
                $k = $this->saveFileType($real_data, $storeId, $merchantId);
                if (is_numeric($k)) {
                    $i = $i + $k;
                }
            }
        }
        return $i;
    }

    public function saveFileType($real_data = null, $storeId, $merchantId) {
        $i = 0;
        foreach ($real_data as $key => $row) {
            $row['A'] = trim($row['A']);
            if (!empty($row['A'])) {
                $isUniqueId = $this->Type->checkTypeWithId($row['A']);
                if (!empty($isUniqueId) && $isUniqueId['Type']['store_id'] != $storeId) {
                    continue;
                }
            }
            $row = $this->Common->trimValue($row);
            if ($key > 0) {
                if (!empty($row['B'])) {
                    $type = trim($row['B']);
                    if (!empty($row['A'])) {
                        $isUniqueName = $this->Type->checkTypeUniqueName($type, $storeId, $row['A']);
                    } else {
                        $isUniqueName = $this->Type->checkTypeUniqueName($type, $storeId);
                    }
                    if ($isUniqueName) {
                        $typedata['merchant_id'] = $merchantId;
                        $typedata['name'] = $row['B'];
                        if (!empty($row['C'])) {
                            $typedata['is_active'] = $row['C'];
                        } else {
                            $typedata['is_active'] = 0;
                        }
                        if (!empty($row['F']) && ($row['F'] <= 10)) {
                            $typedata['max_value'] = trim($row['F']);
                            if (!empty($row['E']) && ($row['E'] <= $row['F'])) {
                                $typedata['min_value'] = $row['E'];
                            } else {
                                $typedata['min_value'] = 0;
                            }
                        } else {
                            $typedata['min_value'] = 0;
                            $typedata['max_value'] = 0;
                        }
                        if (!empty($row['A'])) {
                            if (!empty($isUniqueId)) {
                                $typedata['id'] = $row['A'];
                            }
                        } else {
                            $typedata['store_id'] = $storeId;
                            $typedata['id'] = "";
                            $this->Type->create();
                        }
                        $this->Type->saveType($typedata);
                        $i++;
                    }
                }
            }
        }
        return $i;
    }

    public function downloadtype($store_id = null) {
        if (!empty($store_id)) {
            $this->Type->bindModel(array(
                'belongsTo' => array(
                    'Store' => array(
                        'className' => 'Store',
                        'foreignKey' => 'store_id',
                        'fields' => array(
                            'id', 'store_name'
                        )
                    ))), false);

            if ($store_id == "All") {
                $merchantId = $this->Session->read('merchantId');
                $result = $this->Type->findTypeListByMerchantId($merchantId);
            } else {
                $storeId = $store_id;
                $result = $this->Type->findTypeListByStoreId($storeId);
            }
        }
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
        $filename = 'HqType_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Type');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Preference Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Store Name');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Min Sub-Preference');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Max Sub-Preference');

        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $data = $this->Common->trimValue($data);
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Type']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['Type']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['Type']['is_active']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $data['Store']['store_name']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['Type']['min_value']);
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", $data['Type']['max_value']);
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Type');
            if (!empty($_GET['storeID'])) {
                $storeID = $_GET['storeID'];
                $searchData = $this->Type->find('list', array('fields' => array('Type.name', 'Type.name'), 'conditions' => array('OR' => array('Type.name LIKE' => '%' . $_GET['term'] . '%'), 'Type.is_deleted' => 0, 'Type.store_id' => $storeID)));
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $searchData = $this->Type->find('list', array('fields' => array('Type.name', 'Type.name'), 'conditions' => array('OR' => array('Type.name LIKE' => '%' . $_GET['term'] . '%'), 'Type.is_deleted' => 0, 'Type.merchant_id' => $merchant_id)));
            }

            echo json_encode($searchData);
        } else {
            exit;
        }
    }

}
