<?php

App::uses('StoreAppController', 'Controller');

class CategoriesController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption');
    public $uses = 'Category';

    public function beforeFilter() {
        parent::beforeFilter();
        $adminfunctions = array('index', 'categoryList', 'activateCategory', 'deleteCategory', 'deleteCategoryPhoto', 'editCategory', 'DeleteMultipleCategory', 'updateOrder');
        if (in_array($this->params['action'], $adminfunctions) && !$this->Common->checkPermissionByaction($this->params['controller'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Add New Category
      created:5/8/2015
      ----------------------------------------------------- */

    public function index() {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeOptions', $timeRange);
        $count = $this->Category->find('count', array('conditions' => array('is_active' => 1, 'is_deleted' => 0, 'store_id' => $storeId)));
        $this->set('total', $count);
        if (($this->request->is('post')) && (!empty($this->request->data['category']['name']))) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $categoryName = trim($this->request->data['category']['name']);
            $isUniqueName = $this->Category->checkCategoryUniqueName($categoryName, $storeId);
            if ($isUniqueName) {
                $categorydata = array();
                if (empty($this->request->data['category']['imgcat']['name'])) {
                    $categorydata['imgcat'] = "";
                } else {
                    $response = $this->Common->uploadMenuItemImages($this->request->data['category']['imgcat'], '/Category-Image/', $storeId);
                    if (!$response['status']) {
                        $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                        $this->redirect($this->referer());
                    } else {
                        $categorydata['imgcat'] = $response['imagename'];
                    }
                }
                if (!empty($this->request->data['category']['is_meal']) && !empty($this->request->data['category']['days'])) {
                    $this->request->data['category']['days'] = implode(",", array_keys($this->request->data['category']['days']));
                } else {
                    $this->request->data['category']['days'] = '';
                }
                $categorydata['name'] = trim($this->request->data['category']['name']);
                $categorydata['is_sizeonly'] = 3;
                $categorydata['has_topping'] = $this->request->data['category']['has_topping'];
                $categorydata['is_active'] = $this->request->data['category']['is_active'];
                $categorydata['is_meal'] = $this->request->data['category']['is_meal'];
                $categorydata['is_mandatory'] = $this->request->data['category']['is_mandatory'];
                $categorydata['min_value'] = $this->request->data['category']['min_value'];
                $categorydata['max_value'] = $this->request->data['category']['max_value'];
                $categorydata['start_time'] = $this->request->data['category']['start_time'];
                $categorydata['end_time'] = $this->request->data['category']['end_time'];
                $categorydata['store_id'] = $storeId;
                $categorydata['merchant_id'] = $merchantId;
                $categorydata['days'] = $this->request->data['category']['days'];
                $this->Category->create();
                $this->Category->saveCategory($this->Common->trimValue($categorydata));
                $this->request->data = '';
                $this->Session->setFlash(__("Category Successfully Created"), 'alert_success');
            } else {
                $this->Session->setFlash(__("Category name Already exists"), 'alert_failed');
            }
        }
        $this->categoryList();
    }

    /* ------------------------------------------------
      Function name:categoryList()
      Description:Display the list of Category
      created:5/8/2015
      ----------------------------------------------------- */

    public function categoryList($clearAction = null) {
        error_reporting(0);
        $this->layout = "admin_dashboard";
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Category.store_id =$storeID AND Category.is_deleted=0";
        if ($this->Session->read('CategorySearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('CategorySearchData'), true);
        } else {
            $this->Session->delete('CategorySearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('CategorySearchData', json_encode($this->request->data));
            if ($this->request->data['category']['is_active'] != '') {
                $active = trim($this->request->data['category']['is_active']);
                $criteria .= " AND (Category.is_active =$active)";
            }
            if (!empty($this->request->data['Category']['search'])) {
                $search = trim($this->request->data['Category']['search']);
                $criteria .= " AND (Category.name LIKE '%" . $search . "%')";
            }
        }
        //$this->paginate = array('conditions' => array($criteria), 'order' => array('Category.position' => 'ASC'));
        //$categorydetail = $this->paginate('Category');
        $categorydetail = $this->Category->find('all', array('conditions' => array($criteria), 'order' => array('Category.position' => 'ASC')));
        $this->set('list', $categorydetail);
    }

    /* ------------------------------------------------
      Function name:activateCategory()
      Description:Active/deactive Category
      created:6/8/2015
      ----------------------------------------------------- */

    public function activateCategory($EncryptCategoryID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Category']['store_id'] = $this->Session->read('admin_store_id');
        $data['Category']['id'] = $this->Encryption->decode($EncryptCategoryID);
        $data['Category']['is_active'] = $status;
        if ($this->Category->saveCategory($data)) {
            if ($status) {
                $SuccessMsg = "Category Activated";
            } else {
                $SuccessMsg = "Category Deactivated and Category will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'categories', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'categories', 'action' => 'index'));
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
        $data['Category']['store_id'] = $this->Session->read('admin_store_id');
        $data['Category']['id'] = $this->Encryption->decode($EncryptCategoryID);
        $data['Category']['is_deleted'] = 1;
        if ($this->Category->saveCategory($data)) {
            $this->_deleteCategoryRelatedData($data['Category']['id']);
            $this->Session->setFlash(__("Category deleted"), 'alert_success');
            $this->redirect(array('controller' => 'categories', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'categories', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editCategory()
      Description:Edit Category
      created:6/8/2015
      ----------------------------------------------------- */

    public function editCategory($EncryptCategoryID = null) {
        $this->layout = "admin_dashboard";
        $seasonalpost = 0;
        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeOptions', $timeRange);
        $count = $this->Category->find('count', array('conditions' => array('is_active' => 1, 'is_deleted' => 0)));
        $this->set('total', $count);
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['Category']['id'] = $this->Encryption->decode($EncryptCategoryID);
        $this->loadModel('Category');
        $categoryDetail = $this->Category->getCategoryDetail($data['Category']['id'], $storeId);
        if ($categoryDetail['Category']['is_meal'] == 1) {
            $seasonalpost = 1;
        }
        $this->set('seasonalpost', $seasonalpost);
        $this->set('imgpath', $categoryDetail['Category']['imgcat']);
        if ($this->request->data && $this->request->is(array('post', 'put'))) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $categorydata = array();
            $categoryName = trim($this->request->data['Category']['name']);
            $isUniqueName = $this->Category->checkCategoryUniqueName($categoryName, $storeId, $data['Category']['id']);
            if ($isUniqueName) {
                if (empty($this->request->data['Category']['imgcat']['name'])) {
                    $categorydata['imgcat'] = $categoryDetail['Category']['imgcat'];
                } else {
                    $response = $this->Common->uploadMenuItemImages($this->request->data['Category']['imgcat'], '/Category-Image/', $storeId);
                    if (!$response['status']) {
                        $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                        $this->redirect($this->referer());
                    } else {
                        $categorydata['imgcat'] = $response['imagename'];
                    }
                }
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
                $categorydata['store_id'] = $storeId;
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
                $this->redirect(array('controller' => 'categories', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Category name Already exists"), 'alert_failed');
            }
        }
        $this->request->data = $categoryDetail;
    }

    /* ------------------------------------------------
      Function name:deleteCategoryPhoto()
      Description:Delete category Photo
      created:7/8/2015
      ----------------------------------------------------- */

    public function deleteCategoryPhoto($EncryptCategoryID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Category']['store_id'] = $this->Session->read('admin_store_id');
        $data['Category']['id'] = $this->Encryption->decode($EncryptCategoryID);
        $data['Category']['imgcat'] = '';
        if ($this->Category->saveCategory($data)) {
            $this->Session->setFlash(__("Category Photo deleted"), 'alert_success');
            $this->redirect(array('controller' => 'categories', 'action' => 'editCategory', $EncryptCategoryID));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'categories', 'action' => 'editCategory', $EncryptCategoryID));
        }
    }

    /* ------------------------------------------------
      Function name:deleteMultipleCategory()
      Description:Delete multiple category
      created:03/9/2015
      ----------------------------------------------------- */

    public function deleteMultipleCategory() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Category']['store_id'] = $this->Session->read('admin_store_id');
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
            $this->redirect(array('controller' => 'categories', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:uploadfile()
      Description:Upload excel file
      created:5/8/2015
      ----------------------------------------------------- */

    public function uploadfile() {
        $this->layout = 'admin_dashboard';
        $maxFilesize = 20000000;
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
            } else if ($tmp['Category']['file']['size'] > $maxFilesize) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Category']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $i = 0;
                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B']) && !empty($row['C']) && !empty($row['E'])) {
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
                                $categoryName = trim($row['B']);
                                if (!empty($row['A'])) {
                                    $isUniqueName = $this->Category->checkCategoryUniqueName($categoryName, $storeId, $row['A']);
                                } else {
                                    $isUniqueName = $this->Category->checkCategoryUniqueName($categoryName, $storeId);
                                }
                                if ($isUniqueName) {
                                    $tmpp['Category']['store_id'] = $storeId;
                                    $tmpp['Category']['merchant_id'] = $merchantId;
                                    $tmpp['Category']['name'] = trim($row['B']);
                                    if (!empty($row['C'])) {
                                        $tmpp['Category']['position'] = trim($row['C']);
                                    } else {
                                        $tmpp['Category']['position'] = 0;
                                    }
//                                    if (!empty($row['D'])) {
//                                        $option = 0;
//                                        $size = array("1" => "Size Only", "2" => "Preference Only", "3" => "Size and Preference");
//                                        foreach ($size as $key => $value) {
//                                            if (strtolower($row['D']) == strtolower($value)) {
//                                                $option = $key;
//                                            }
//                                        }
//                                        $tmpp['Category']['is_sizeonly'] = trim($option);
//                                    } else {
//                                        $tmpp['Category']['is_sizeonly'] = 0;
//                                    }
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
                                        $tmpp['Category']['id'] = trim($row['A']);
                                    } else {
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
                }
                $this->Session->setFlash(__($i . ' ' . 'Category has been saved'), 'alert_success');
                $this->redirect(array("controller" => "categories", "action" => "categoryList"));
            }
        }
    }

    /* ------------------------------------------------
      Function name:download()
      Description:Download excel file
      created:5/8/2015
      ----------------------------------------------------- */

    public function download() {
        $storeId = $this->Session->read('admin_store_id');
        $result = $this->Category->findAllCategotyList($storeId);
        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
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
        $filename = 'Categories_' . date("Y-m-d") . ".xls"; //create a file
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

        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        //$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);

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
      Description: Update the display order for Categories
      created Date:21/12/2015
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function updateOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            $_GET = array_filter($_GET);
            foreach ($_GET as $key => $val) {
                if (!empty($val)) {
                    $this->Category->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
                }
            }
        }
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Category');
            $storeID = $this->Session->read('admin_store_id');
            $searchData = $this->Category->find('list', array('fields' => array('Category.name', 'Category.name'), 'conditions' => array('OR' => array('Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Category.is_deleted' => 0, 'Category.store_id' => $storeID)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    public function deleteItem() {
        $this->layout = false;
        $this->autoRender = false;
        $cData = $this->Category->find('list', array('fields' => array('id'), 'conditions' => array('is_deleted' => 1)));
        if (!empty($cData)) {
            $this->loadModel('Item');
            foreach ($cData as $cID) {
                $this->Item->updateAll(array('is_deleted' => 1), array('category_id' => $cID));
            }
        }
    }

}
