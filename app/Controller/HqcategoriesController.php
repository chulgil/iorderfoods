<?php

App::uses('HqAppController', 'Controller');

class HqcategoriesController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption');
    public $uses = 'Category';

    public function beforeFilter() {
        parent::beforeFilter();
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Add New Category for merchant
      created:3/8/2016
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "hq_dashboard";
        $this->loadModel("Store");
        $merchantId = $this->Session->read('merchantId');
        $start = "00:30";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeForHq($start, $end);
        $this->set('timeOptions', $timeRange);
        if (($this->request->is('post')) && (!empty($this->request->data['Category']['name']))) {
            $storeId = $this->request->data['Category']['store_id'];
            if ($storeId == 'All') {
                $storeList = $this->Store->find('all', array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0, 'Store.merchant_id' => $merchantId), 'fields' => array('Store.id'), 'recursive' => -1));
            } else {
                $storeList[0]['Store']['id'] = $storeId;
            }
            $this->request->data = $this->Common->trimValue($this->request->data);
            if (!empty($this->request->data['Category']['is_meal']) && !empty($this->request->data['Category']['days'])) {
                $this->request->data['Category']['days'] = implode(",", array_keys($this->request->data['Category']['days']));
            } else {
                $this->request->data['Category']['days'] = '';
            }
            $categoryName = trim($this->request->data['Category']['name']);
            foreach ($storeList as $sList) {
                $isUniqueName = $this->Category->checkCategoryUniqueName($categoryName, $sList['Store']['id']);
                if ($isUniqueName) {
                    $categorydata = array();
                    $categorydata['name'] = trim($this->request->data['Category']['name']);
                    $categorydata['is_sizeonly'] = 3;
                    $categorydata['has_topping'] = $this->request->data['Category']['has_topping'];
                    $categorydata['is_active'] = $this->request->data['Category']['is_active'];
                    $categorydata['is_meal'] = $this->request->data['Category']['is_meal'];
                    $categorydata['is_mandatory'] = $this->request->data['Category']['is_mandatory'];
                    $categorydata['min_value'] = $this->request->data['Category']['min_value'];
                    $categorydata['max_value'] = $this->request->data['Category']['max_value'];
                    $categorydata['start_time'] = $this->request->data['Category']['start_time'];
                    $categorydata['end_time'] = $this->request->data['Category']['end_time'];
                    $categorydata['store_id'] = $sList['Store']['id'];
                    $categorydata['merchant_id'] = $merchantId;
                    $categorydata['days'] = $this->request->data['Category']['days'];
                    $this->Category->create();
                    $this->Category->saveCategory($this->Common->trimValue($categorydata));
                }
            }
            $this->request->data = '';
            $this->Session->setFlash(__("Category Successfully Created"), 'alert_success');
        }
        //list category
        $this->_categoryList($clearAction = null);
    }

    /* ------------------------------------------------
      Function name:categoryList()
      Description:Display the list of Category
      created:4/8/2016
      ----------------------------------------------------- */

    private function _categoryList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        if ($clearAction == 'clear') {
            $this->Session->delete('HqSearchCategory');
            $this->redirect($this->referer());
        }
        if (empty($this->request->data['Category']['storeId'])) {
            $storeID = 'All';
        } else {
            $storeID = $this->request->data['Category']['storeId'];
        }

//        if (!empty($this->request->data['Category']['storeId'])) {
//            $storeID = $this->request->data['Category']['storeId'];
        $merchantId = $this->Session->read('merchantId');
        if ($storeID == 'All') {
            $criteria = "Category.is_deleted=0 AND Category.merchant_id=$merchantId";
        } else {
            $criteria = "Category.store_id =$storeID AND Category.is_deleted=0 AND Category.merchant_id=$merchantId";
        }

        $this->Session->write('HqSearchCategory', json_encode($this->request->data));
        if (!empty($this->request->data)) {
            if ($this->request->data['Category']['isActive'] != '') {
                $active = trim($this->request->data['Category']['isActive']);
                $criteria .= " AND (Category.is_active =$active)";
            }
            if (!empty($this->request->data['Category']['search'])) {
                $search = trim($this->request->data['Category']['search']);
                $criteria .= " AND (Category.name LIKE '%" . $search . "%')";
            }
        }
        $this->Category->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'type' => 'inner',
                    'foreignKey' => 'store_id',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name')
                )
            )), false
        );
        //$this->paginate = array('conditions' => array($criteria), 'recursive' => 1, 'order' => array('Category.position' => 'ASC'));
        //$categorydetail = $this->paginate('Category');
        $categorydetail = $this->Category->find('all', array('conditions' => array($criteria), 'recursive' => 1, 'order' => array('Category.position' => 'ASC')));
        $this->set('list', $categorydetail);
        //}
    }

    /* ------------------------------------------------
      Function name:activateCategory()
      Description:Active/deactive Category
      created:6/8/2015
      ----------------------------------------------------- */

    public function activateCategory($EncryptCategoryID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['Category']['id'] = $this->Encryption->decode($EncryptCategoryID);
        $data['Category']['is_active'] = $status;
        if ($this->Category->saveCategory($data)) {
            if ($status) {
                $SuccessMsg = "Category Activated";
            } else {
                $SuccessMsg = "Category Deactivated and Category will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqcategories', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqcategories', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteCategory()
      Description:Delete Category
      created:6/8/2015
      ----------------------------------------------------- */

    public function deleteCategory($EncryptCategoryID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Category']['id'] = $this->Encryption->decode($EncryptCategoryID);
        $data['Category']['is_deleted'] = 1;
        if ($this->Category->saveCategory($data)) {
            $this->_deleteCategoryRelatedData($data['Category']['id']);
            $this->Session->setFlash(__("Category deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqcategories', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqcategories', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editCategory()
      Description:Edit Category
      created:6/8/2015
      ----------------------------------------------------- */

    public function editCategory($EncryptCategoryID = null) {
        $this->layout = "hq_dashboard";
        $seasonalpost = 0;
        $start = "00:30";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeForHq($start, $end);
        $this->set('timeOptions', $timeRange);
        $merchantId = $this->Session->read('merchantId');
        $data['Category']['id'] = $this->Encryption->decode($EncryptCategoryID);
        $categoryDetail = $this->Category->getCategoryDetailById($data['Category']['id']);
        if ($categoryDetail['Category']['is_meal'] == 1) {
            $seasonalpost = 1;
        }
        $this->set('seasonalpost', $seasonalpost);
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $categorydata = array();
            $categoryName = trim($this->request->data['Category']['name']);
            $isUniqueName = $this->Category->checkCategoryUniqueName($categoryName, $categoryDetail['Category']['store_id'], $data['Category']['id']);
            if ($isUniqueName) {
                if (!empty($this->request->data['Category']['is_meal']) && !empty($this->request->data['Category']['days'])) {
                    $this->request->data['Category']['days'] = implode(",", array_keys($this->request->data['Category']['days']));
                } else {
                    $this->request->data['Category']['days'] = '';
                }
                $categorydata['id'] = $data['Category']['id'];
                $categorydata['name'] = trim($this->request->data['Category']['name']);
                $categorydata['is_sizeonly'] = 3;
                $categorydata['has_topping'] = $this->request->data['Category']['has_topping'];
                $categorydata['is_active'] = $this->request->data['Category']['is_active'];
                $categorydata['is_meal'] = $this->request->data['Category']['is_meal'];
                $categorydata['is_mandatory'] = $this->request->data['Category']['is_mandatory'];
                $categorydata['min_value'] = $this->request->data['Category']['min_value'];
                $categorydata['max_value'] = $this->request->data['Category']['max_value'];
                $categorydata['start_time'] = $this->request->data['Category']['start_time'];
                $categorydata['end_time'] = $this->request->data['Category']['end_time'];
                $categorydata['merchant_id'] = $merchantId;
                $categorydata['days'] = $this->request->data['Category']['days'];
                if ($this->Category->saveCategory($this->Common->trimValue($categorydata))) {
                    $this->loadModel('Item');
                    if ($categorydata['is_mandatory'] == 1) {
                        $this->Item->updateAll(array('mandatory_item_units' => 1), array('category_id' => $categorydata['id']));
                    } elseif ($categorydata['is_mandatory'] == 0) {
                        $this->Item->updateAll(array('mandatory_item_units' => 0), array('category_id' => $categorydata['id']));
                    }
                }
                $this->Session->setFlash(__("Category Updated Successfully."), 'alert_success');
                $this->redirect(array('controller' => 'hqcategories', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Category name Already exists"), 'alert_failed');
            }
        }
        $this->request->data = $categoryDetail;
    }

    /* ------------------------------------------------
      Function name:deleteMultipleCategory()
      Description:Delete multiple category
      created:03/9/2015
      ----------------------------------------------------- */

    public function deleteMultipleCategory() {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['Category']['is_deleted'] = 1;
        if (!empty($this->request->data['Category']['id'])) {
            $filter_array = array_filter($this->request->data['Category']['id']);
            $i = 0;
            foreach ($filter_array as $orderId) {
                $data['Category']['id'] = $orderId;
                $this->Category->saveCategory($data);
                $this->_deleteCategoryRelatedData($data['Category']['id']);
                $i++;
            }
            $del = $i . "  " . "category deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'hqcategories', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:updateOrder()
      Description: Update the display order for Categories
      created Date:21/12/2015
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function updateOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
	    $_GET = array_filter($_GET);
            foreach ($_GET as $key => $val) {
                $this->Category->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
            }
        }
    }

    public function uploadfile() {
        $this->layout = "hq_dashboard";
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            $this->loadModel('Store');
            if ($tmp['Category']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['Category']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['Category']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['Category']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Category']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $merchantId = $this->Session->read('merchantId');
                $storeId = $this->request->data['Category']['store_id'];
                if ($storeId == "All") {
                    $storeId = $this->Store->find('list', array('fields' => array('id'), 'conditions' => array('Store.merchant_id' => $merchantId)));
                    $i = $this->categoryForMultipleStore($storeId, $real_data, $merchantId);
                } else {
                    $i = $this->saveFileCategory($real_data, $storeId, $merchantId);
                }
                $this->Session->setFlash(__($i . ' ' . 'Category has been saved'), 'alert_success');
                $this->redirect(array("controller" => "hqcategories", "action" => "index"));
            }
        }
    }

    public function categoryForMultipleStore($storeIds = array(), $real_data = array(), $merchantId = null) {
        $i = 0;
        if (!empty($storeIds)) {
            foreach ($storeIds as $storeId) {
                $k = $this->saveFileCategory($real_data, $storeId, $merchantId);
                if (is_numeric($k)) {
                    $i = $i + $k;
                }
            }
        }
        return $i;
    }

    public function saveFileCategory($real_data = null, $storeId, $merchantId) {
        $i = 0;
        foreach ($real_data as $key => $row) {

            $row['A'] = trim($row['A']);
            if (!empty($row['A'])) {
                $isUniqueId = $this->Category->checkCategoryWithId($row['A']);
                if (!empty($isUniqueId) && $isUniqueId['Category']['store_id'] != $storeId) {
                    continue;
                }
            }
            $row = $this->Common->trimValue($row);
            if ($key > 0) {
                if (!empty($row['B']) && !empty($row['C'])) {
                    $categoryName = trim($row['B']);
                    if (!empty($row['A'])) {
                        $isUniqueName = $this->Category->checkCategoryUniqueName($categoryName, $storeId, $row['A']);
                    } else {
                        $isUniqueName = $this->Category->checkCategoryUniqueName($categoryName, $storeId);
                    }
                    if ($isUniqueName) {
                        $tmpp['Category']['merchant_id'] = $merchantId;
                        $tmpp['Category']['name'] = trim($row['B']);
                        if (!empty($row['C'])) {
                            $tmpp['Category']['position'] = trim($row['C']);
                        } else {
                            $tmpp['Category']['position'] = 0;
                        }
//                        if (!empty($row['D'])) {
//                            $option = 0;
//                            $size = array("1" => "Size Only", "2" => "Preference Only", "3" => "Size and Preference");
//                            foreach ($size as $key => $value) {
//                                if (strtolower($row['D']) == strtolower($value)) {
//                                    $option = $key;
//                                }
//                            }
//                            $tmpp['Category']['is_sizeonly'] = trim($option);
//                        } else {
//                            $tmpp['Category']['is_sizeonly'] = 0;
//                        }
                        if (!empty($row['D'])) {
                            $tmpp['Category']['has_topping'] = trim($row['D']);
                        } else {
                            $tmpp['Category']['has_topping'] = 0;
                        }
                        if (!empty($row['E'])) {
                            $tmpp['Category']['is_active'] = trim($row['E']);
                        } else {
                            $tmpp['Category']['is_active'] = 0;
                        }
                        if (!empty($row['F'])) {
                            if ($row['F'] == 1) {
                                $tmpp['Category']['is_meal'] = 1;
                                if (!empty($row['G']) && !empty($row['H'])) {
                                    $tmpp['Category']['start_time'] = trim($row['G']);
                                    $tmpp['Category']['end_time'] = ($row['H']);
                                } else {
                                    $tmpp['Category']['start_time'] = '00:30:00';
                                    $tmpp['Category']['end_time'] = '00:30:00';
                                }
                            } else {
                                $tmpp['Category']['is_meal'] = 0;
                            }
                        } else {
                            $tmpp['Category']['is_meal'] = 0;
                        }

                        if (!empty($row['A'])) {
                            if (!empty($isUniqueId)) {
                                $tmpp['Category']['id'] = trim($row['A']);
                            }
                        } else {
                            $tmpp['Category']['store_id'] = $storeId;
                            $tmpp['Category']['id'] = "";
                            $this->Category->create();
                        }
                        $tmpp['Category']['is_sizeonly'] = 3;
                        $this->Category->save($tmpp);
                        $i++;
                    }
                }
            }
        }
        return $i;
    }

    public function downloadcategory($store_id = null) {

        if (!empty($store_id)) {

            $this->Category->bindModel(array(
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
                $result = $this->Category->findAllCategotyByMerchantId($merchantId);
            } else {
                $storeId = $store_id;
                $result = $this->Category->findAllCategotyByStoreId($storeId);
            }
        }
        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
//        $styleArray2 = array(
//            'font' => array('name' => 'Arial', 'size' => '10', 'color' => array('rgb' => '444555'), 'bold' => true),
//            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'D6D6D6'))
//        );
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
        $filename = 'HqCategories_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Categories');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Category Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Position');
        //$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Size/Preference');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Has Add-on');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Time Restriction');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Start Time');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'End Time');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Store Name');

        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
        //$objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", trim($data['Category']['id']));
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", trim($data['Category']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", trim($data['Category']['position']));

//            if ($data['Category']['is_sizeonly'] == 1) {
//                $size = 'Size Only';
//            } elseif ($data['Category']['is_sizeonly'] == 2) {
//                $size = 'Preference Only';
//            } elseif ($data['Category']['is_sizeonly'] == 3) {
//                $size = 'Size and Preference';
//            }
//            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $size);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", trim($data['Category']['has_topping']));
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", trim($data['Category']['is_active']));
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", trim($data['Category']['is_meal']));
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", trim($data['Category']['start_time']));
            $objPHPExcel->getActiveSheet()->setCellValue("H$i", trim($data['Category']['end_time']));
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", trim($data['Store']['store_name']));
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
            $this->loadModel('Category');
            if (!empty($_GET['storeID']) && ($_GET['storeID'] != 'All')) {
                $storeID = $_GET['storeID'];
                $searchData = $this->Category->find('list', array('fields' => array('Category.name', 'Category.name'), 'conditions' => array('OR' => array('Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Category.is_deleted' => 0, 'Category.store_id' => $storeID)));
            } elseif (!empty($_GET['storeID']) && ($_GET['storeID'] == 'All')) {
                $merchant_id = $this->Session->read('merchantId');
                $searchData = $this->Category->find('list', array('fields' => array('Category.name', 'Category.name'), 'conditions' => array('OR' => array('Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Category.is_deleted' => 0, 'Category.merchant_id' => $merchant_id)));
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $searchData = $this->Category->find('list', array('fields' => array('Category.name', 'Category.name'), 'conditions' => array('OR' => array('Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Category.is_deleted' => 0, 'Category.merchant_id' => $merchant_id)));
            }
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

}
