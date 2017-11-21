<?php

App::uses('StoreAppController', 'Controller');

class TypesController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common', 'Paginator');
    public $helper = array('Encryption', 'Paginator');
    public $uses = array('Type', 'ItemType');

    public function beforeFilter() {
        parent::beforeFilter();
        $adminfunctions = array('addType', 'index', 'deleteType', 'editType', 'updateOrder');
        if (in_array($this->params['action'], $adminfunctions)) {
            if (!$this->Common->checkPermissionByaction($this->params['controller'])) {
                //   echo "In Failed";die;
                $this->Session->setFlash(__("Permission Denied"));
                $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:addType()
      Description:To add Type in type table
      created:7/8/2015
      ----------------------------------------------------- */

    public function addType() {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        if ($this->request->is('post') && !empty($this->request->data['Type']['name1'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $type = trim($this->data['Type']['name']);
            $isUniqueName = $this->Type->checkTypeUniqueName($type, $storeID);
            if ($isUniqueName) {
                $typedata['store_id'] = $storeID;
                $typedata['merchant_id'] = $merchant_id;
                $typedata['name'] = trim($this->data['Type']['name1']);
                // $typedata['price'] = $this->data['Type']['price'];

                $typedata['min_value'] = $this->data['Type']['min_value'];
                $typedata['max_value'] = $this->data['Type']['max_value'];
                $typedata['is_active'] = $this->data['Type']['is_active1'];
                $this->Type->create();
                $this->Type->saveType($typedata);
                $this->request->data = '';
                $this->Session->setFlash(__("Preference Successfully Added"), 'alert_success');
                $this->redirect(array('controller' => 'types', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Preference Already exists"), 'alert_failed');
                $this->redirect(array('controller' => 'types', 'action' => 'index'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:To display the list of type
      created:7/8/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "admin_dashboard";
        $this->loadmodel("Item");
        $this->loadmodel("ItemType");
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        /*         * ****start******* */
        $value = "";
        $criteria = "Type.store_id =$storeID AND Type.is_deleted=0";
        $order = '';
        $pagingFlag = true;
        if ($this->Session->read('TypeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('TypeSearchData'), true);
        } else {
            $this->Session->delete('TypeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        //if(isset($this->params['named']['sort']) || isset($this->params['named']['page'])){

        $flag = 0;
        if (!empty($this->request->data)) {
            $this->Session->write('TypeSearchData', json_encode($this->request->data));
            if ($this->request->data['Type']['is_active'] != '') {
                $active = trim($this->request->data['Type']['is_active']);
                $criteria .= " AND (Type.is_active =$active)";
            }
            if (!empty($this->request->data['Type']['search'])) {
                $search = trim($this->request->data['Type']['search']);
                $criteria .= " AND (Type.name LIKE '%" . $search . "%')";
            }
            //if($this->request->data['Type']['item_id']!=''){
            //      $itemId = trim($this->request->data['Type']['item_id']);
            //      $typeId= $this->ItemType->find('list',array('fields'=>array('ItemType.type_id'),'conditions'=>array('ItemType.item_id' =>$itemId,'ItemType.store_id' =>$storeID,'ItemType.is_active' =>1,'ItemType.is_deleted' =>0)));
            //      $pagingFlag=false;
            //      if(count($typeId)>0){
            //         $criteria .= " AND (Type.id IN (".implode(',',array_unique($typeId))."))";
            //         $order= 'Type.position ASC';
            //      }else{
            //         $flag=1;
            //      }
            //}
        }
        if ($order == '') {
            $order = 'Type.position ASC';
        }
        if ($flag) {
            $criteria .= " AND (Type.is_active =2)";
        }

        $typedetail = '';
        $this->paginate = array('conditions' => array($criteria), 'order' => $order);
        $typedetail = $this->paginate('Type');
        $this->set('list', $typedetail);
        //$itemList=$this->Item->getallItemsByStore($storeID);
        //$this->set('itemList',$itemList);
        //$this->typelisting();
    }

    /* ------------------------------------------------
      Function name:index()
      Description:To display the list of type
      created:7/8/2015
      ----------------------------------------------------- */

    public function typelisting($clearAction = null) {//prx($this->params);
        $this->layout = "admin_dashboard";
        $this->loadmodel("Item");
        $this->loadmodel("ItemType");
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        /*         * ****start******* */
        $value = "";
        $criteria = "ItemType.store_id =$storeID AND ItemType.is_deleted=0";
        $order = '';
        $pagingFlag = true;
        if ($this->Session->read('ItemTypeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('ItemTypeSearchData'), true);
        } else {
            $this->Session->delete('ItemTypeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        //if(isset($this->params['named']['sort']) || isset($this->params['named']['page'])){

        $flag = 0;
        if (!empty($this->request->data)) {
            $this->Session->write('ItemTypeSearchData', json_encode($this->request->data));
            if (isset($this->request->data['ItemType']['is_active']) && $this->request->data['ItemType']['is_active'] != '') {
                $active = trim($this->request->data['ItemType']['is_active']);
                $criteria .= " AND (ItemType.is_active =$active)";
            }
            if (!empty($this->request->data['Type']['search'])) {
                $search = trim($this->request->data['Type']['search']);
                $criteria .= " AND (Type.name LIKE '%" . $search . "%')";
            }

            if (!empty($this->request->data['ItemType']['item_id']) && $this->request->data['ItemType']['item_id'] != '') {
                $itemId = trim($this->request->data['ItemType']['item_id']);
                $criteria .= " AND (ItemType.item_id =$itemId)";
                $typeId = $this->ItemType->find('list', array('fields' => array('ItemType.type_id'), 'conditions' => array('ItemType.item_id' => $itemId, 'ItemType.store_id' => $storeID, 'ItemType.is_deleted' => 0)));
                $pagingFlag = false;
                if (count($typeId) > 0) {
                    $criteria .= " AND (ItemType.type_id IN (" . implode(',', array_unique($typeId)) . "))";
                } else {
                    $flag = 1;
                }
            }
        }


        $this->Type->unbindModel(array('hasMany' => array('ItemType')));
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
            'belongsTo' => array('Category')
        ));
        $typedetail = '';
        //$typedetail=$this->ItemType->find('all',array('conditions'=>array($criteria), 'order' => array('ItemType.position ASC'), 'recursive' => 2));
        //pr($criteria);
        $this->paginate = array('conditions' => array($criteria), 'order' => array('ItemType.position ASC'), 'recursive' => 2);
        $typedetail = $this->paginate('ItemType');
        $this->set('list', $typedetail);

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
        $nList = array();
        if (!empty($itemList)) {
            foreach ($itemList as $iList) {
                if (!empty($iList['Item']) && !empty($iList['Type']) && !empty($iList['Item']['Category'])) {
                    $nList[$iList['Item']['id']] = $iList['Item']['name'];
                }
            }
        }
        $this->set('itemList', $nList);
    }

    /* ------------------------------------------------
      Function name:deleteType()
      Description:Delete Type
      created:7/8/2015
      ----------------------------------------------------- */

    public function deleteType($EncryptTypeID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Type']['store_id'] = $this->Session->read('admin_store_id');
        $data['Type']['id'] = $this->Encryption->decode($EncryptTypeID);
        $data['Type']['is_deleted'] = 1;
        if ($this->Type->saveType($data)) {
            $this->Session->setFlash(__("Preference deleted"), 'alert_success');
            $this->redirect(array('controller' => 'types', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'types', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:activateType()
      Description:Active/deactive type
      created:7/8/2015
      ----------------------------------------------------- */

    public function activateType($EncryptedTypeID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Type']['store_id'] = $this->Session->read('admin_store_id');
        $data['Type']['id'] = $this->Encryption->decode($EncryptedTypeID);
        $data['Type']['is_active'] = $status;
        if ($this->Type->saveType($data)) {
            if ($status) {
                $SuccessMsg = "Preference Activated";
            } else {
                $SuccessMsg = "Preference Deactivated and Preference will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'types', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'types', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editType()
      Description:Edit Type
      created:7/8/2015
      ----------------------------------------------------- */

    public function editType($EncryptTypeID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['Type']['id'] = $this->Encryption->decode($EncryptTypeID);
        $this->loadModel('Type');
        $typeDetail = $this->Type->getTypeDetail($data['Type']['id'], $storeId);
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $typedata = array();
            $type = trim($this->request->data['Type']['name']);
            $isUniqueName = $this->Type->checkTypeUniqueName($type, $storeId, $data['Type']['id']);

            if ($isUniqueName) {
                $typedata['id'] = $data['Type']['id'];
                //  $typedata['price'] = $this->data['Type']['price'];
                $typedata['name'] = trim($this->request->data['Type']['name']);
                $typedata['is_active'] = $this->request->data['Type']['is_active'];
                $typedata['min_value'] = $this->request->data['Type']['min_value'];
                $typedata['max_value'] = $this->request->data['Type']['max_value'];
                $typedata['store_id'] = $storeId;
                $typedata['merchant_id'] = $merchantId;
                $this->Type->saveType($typedata);
                $this->Session->setFlash(__("Preference Updated Successfully ."), 'alert_success');
                $this->redirect(array('controller' => 'types', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Preference Already exists"), 'alert_failed');
            }
        }

        $this->request->data = $typeDetail;
    }

    /* ------------------------------------------------
      Function name:deleteMultipleType()
      Description:Delete multiple type
      created:03/9/2015
      ----------------------------------------------------- */

    public function deleteMultipleType() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Type']['store_id'] = $this->Session->read('admin_store_id');
        $data['Type']['is_deleted'] = 1;
        if (!empty($this->request->data['Type']['id'])) {
            $filter_array = array_filter($this->request->data['Type']['id']);
            $i = 0;
            foreach ($filter_array as $k => $orderId) {
                $data['Type']['id'] = $orderId;
                $this->Type->saveType($data);
                $i++;
            }
            $del = $i . "  " . "type deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'types', 'action' => 'index'));
        }
    }

    public function uploadfile() {
        $this->layout = 'admin_dashboard';
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
                $i = 0;

                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B'])) {
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
                                $type = trim($row['B']);
                                if (!empty($row['A'])) {
                                    $isUniqueName = $this->Type->checkTypeUniqueName($type, $storeId, $row['A']);
                                } else {
                                    $isUniqueName = $this->Type->checkTypeUniqueName($type, $storeId);
                                }
                                if ($isUniqueName) {
                                    $typedata['store_id'] = $storeId;
                                    $typedata['merchant_id'] = $merchantId;
                                    $typedata['name'] = $row['B'];

                                    if (!empty($row['C'])) {
                                        $typedata['is_active'] = $row['C'];
                                    } else {
                                        $typedata['is_active'] = 0;
                                    }
                                    if (!empty($row['E']) && ($row['E'] <= 10)) {
                                        $typedata['max_value'] = trim($row['E']);
                                        if (!empty($row['D']) && ($row['D'] <= $row['E'])) {
                                            $typedata['min_value'] = $row['D'];
                                        } else {
                                            $typedata['min_value'] = 0;
                                        }
                                    } else {
                                        $typedata['min_value'] = 0;
                                        $typedata['max_value'] = 0;
                                    }

                                    if (!empty($row['A'])) {
                                        $typedata['id'] = $row['A'];
                                    } else {
                                        $typedata['id'] = "";
                                        $this->Type->create();
                                    }
                                    $this->Type->saveType($typedata);
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }
            $this->Session->setFlash(__($i . ' ' . 'Preference has been saved'), 'alert_success');
            $this->redirect(array("controller" => "types", "action" => "index"));
        }
    }

    public function download() {
        $storeId = $this->Session->read('admin_store_id');
        $result = $this->Type->findTypeList($storeId);
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
        $filename = 'Type_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Type');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Preference Name');
        //$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Price($)');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Min Sub-Preference');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Max Sub-Preference');
        // 
        // $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        //$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        //$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $data = $this->Common->trimValue($data);
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Type']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['Type']['name']);
            //$objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['Type']['price']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['Type']['is_active']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $data['Type']['min_value']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['Type']['max_value']);
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
                $this->Type->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
            }
        }
    }

    /* ------------------------------------------------
      Function name:updateitempreOrder()
      Description: Update the display order
      created Date:16/12/2015
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function updateitempreOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            $this->loadmodel("ItemType");
            foreach ($_GET as $key => $val) {
                $this->ItemType->updateAll(array('position' => $val), array('ItemType.id' => $this->Encryption->decode($key)));
            }
        }
    }

    /* ------------------------------------------------
      Function name:activateType()
      Description:Active/deactive type
      created:7/8/2015
      ----------------------------------------------------- */

    public function activatePreference($EncryptedTypeID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['ItemType']['store_id'] = $this->Session->read('admin_store_id');
        $data['ItemType']['id'] = $this->Encryption->decode($EncryptedTypeID);
        $data['ItemType']['is_active'] = $status;
        $this->loadmodel("ItemType");
        if ($this->ItemType->saveItemType($data)) {
            if ($status) {
                $SuccessMsg = "Preference Activated";
            } else {
                $SuccessMsg = "Preference Deactivated and Preference will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'types', 'action' => 'typelisting'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'types', 'action' => 'typelisting'));
        }
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Type');
            $storeID = $this->Session->read('admin_store_id');
            $searchData = $this->Type->find('list', array('fields' => array('Type.name', 'Type.name'), 'conditions' => array('OR' => array('Type.name LIKE' => '%' . $_GET['term'] . '%'), 'Type.is_deleted' => 0, 'Type.store_id' => $storeID)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

}
