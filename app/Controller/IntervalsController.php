<?php

App::uses('StoreAppController', 'Controller');

class IntervalsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'DateformHelper', 'Common');
    public $uses = array('Interval', 'IntervalDay', 'WeekDay', 'Item', 'ItemPrice', 'ItemType', 'Size', 'Category', 'IntervalDetail');

    public function beforeFilter() {
        parent::beforeFilter();
        //Check permission for Admin User
        $adminfunctions = array('index', 'addInterval', 'editInterval', 'activateInterval', 'deleteInterval', 'deleteMultipleInterval');
        if (in_array($this->params['action'], $adminfunctions)) {
            if (!$this->Common->checkPermissionByaction($this->params['controller'])) {
                $this->Session->setFlash(__("Permission Denied"));
                $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List Menu Items
      created:5/8/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "admin_dashboard";
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Interval.store_id =$storeID AND Interval.is_deleted=0";
        if ($this->Session->read('IntervalSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('IntervalSearchData'), true);
        } else {
            $this->Session->delete('IntervalSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('IntervalSearchData', json_encode($this->request->data));
            if ($this->request->data['Interval']['is_Active'] != '') {
                $active = trim($this->request->data['Interval']['is_Active']);
                $criteria .= " AND (Interval.is_active =$active)";
            }
            if (!empty($this->request->data['Interval']['search'])) {
                $search = trim($this->request->data['Interval']['search']);
                $criteria .= " AND (Interval.name LIKE '%" . $search . "%')";
            }
        }
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Interval.created' => 'DESC'));
        $intervalList = $this->paginate('Interval');
        $this->set('intervalList', $intervalList);
        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeRange', $timeRange);

        $daysArray = $this->WeekDay->getWeekDaysList();
        $this->set('daysArray', $daysArray);
    }

    /* ------------------------------------------------
      Function name:addInterval()
      Description:add Interval
      created:5/8/2015
      ----------------------------------------------------- */

    public function addInterval() {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');

        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeRange', $timeRange);

        $daysArray = $this->WeekDay->getWeekDaysList();
        $this->set('daysArray', $daysArray);

        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $intervalData = $this->request->data['Interval'];
            $intervalData['store_id'] = $storeID;
            $flag = $this->Interval->saveInterval($intervalData);
            if ($flag) {
                $intervalId = $this->Interval->getLastInsertId();
                foreach ($this->request->data['IntervalDay'] as $key => $value) {
                    $intervalDayData['store_id'] = $storeID;
                    $intervalDayData['merchant_id'] = $merchantId;
                    $intervalDayData['interval_id'] = $intervalId;
                    $intervalDayData['week_day_id'] = $key;
                    $intervalDayData['day_status'] = $value;
                    $this->IntervalDay->create();
                    $this->IntervalDay->saveIntervalDay($intervalDayData);
                }
                $this->request->data = '';
                $this->Session->setFlash(__("Time-Interval Successfully Created"), 'alert_success');
                $this->redirect(array('controller' => 'intervals', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Time-Interval Not Created"), 'alert_failed');
                $this->redirect(array('controller' => 'intervals', 'action' => 'index'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:editMenuItem()
      Description:Update Menu Interval
      created:5/8/2015
      ----------------------------------------------------- */

    public function editInterval($EncryptedIntervalID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['Interval']['id'] = $this->Encryption->decode($EncryptedIntervalID);
        $intervalDetail = $this->Interval->getIntervalDetail($data['Interval']['id']);
        $this->set('intervalDetail', $intervalDetail);

        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeRange', $timeRange);

        $daysArray = $this->WeekDay->getWeekDaysList();
        $this->set('daysArray', $daysArray);

        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $data['Interval'] = $this->request->data['Interval'];
            $data['Interval']['store_id'] = $storeId;
            $flag = $this->Interval->saveInterval($data);
            if ($flag) {
                foreach ($this->request->data['IntervalDay'] as $key => $value) {
                    $intervalDayData['id'] = $key;
                    $intervalDayData['store_id'] = $storeId;
                    $intervalDayData['merchant_id'] = $merchantId;
                    $intervalDayData['interval_id'] = $data['Interval']['id'];
                    $intervalDayData['day_status'] = $value;
                    $this->IntervalDay->saveIntervalDay($intervalDayData);
                    $intervalDayData = array();
                }
                $this->Session->setFlash(__("Time-Interval Successfully Updated"), 'alert_success');
                $this->redirect(array('controller' => 'intervals', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Time-Interval is not updated"), 'alert_failed');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $intervalDetail;
        }
    }

    /* ------------------------------------------------
      Function name:activateInterval()
      Description:Active/deactive Interval
      created:5/8/2015
      ----------------------------------------------------- */

    public function activateInterval($EncryptIntervalID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Interval']['store_id'] = $this->Session->read('admin_store_id');
        $data['Interval']['id'] = $this->Encryption->decode($EncryptIntervalID);
        $data['Interval']['is_active'] = $status;
        if ($this->Interval->saveInterval($data)) {
            if ($status) {
                $SuccessMsg = "Interval Activated";
            } else {
                $SuccessMsg = "Interval Deactivated and Interval will not get Display";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
        }
        $this->redirect(array('controller' => 'Intervals', 'action' => 'index'));
    }

    /* ------------------------------------------------
      Function name:deleteInterval()
      Description:Delete Interval
      created:9/2/2016
      ----------------------------------------------------- */

    public function deleteInterval($EncryptIntervalID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Interval']['store_id'] = $this->Session->read('admin_store_id');
        $data['Interval']['id'] = $this->Encryption->decode($EncryptIntervalID);
        $data['Interval']['is_deleted'] = 1;
        if ($this->Interval->saveInterval($data)) {
            $this->Session->setFlash(__("Interval deleted"), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
        }
        $this->redirect(array('controller' => 'Intervals', 'action' => 'index'));
    }

    /* ------------------------------------------------
      Function name:deleteMultipleInterval()
      Description:Delete multiple Interval
      created:09/2/2016
      ----------------------------------------------------- */

    public function deleteMultipleInterval() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Interval']['store_id'] = $this->Session->read('admin_store_id');
        $data['Interval']['is_deleted'] = 1;
        if (!empty($this->request->data['Interval']['id'])) {
            $filter_array = array_filter($this->request->data['Interval']['id']);
            $i = 0;
            foreach ($filter_array as $k => $intervalId) {
                $data['Interval']['id'] = $intervalId;
                $this->Interval->saveInterval($data);
                $i++;
            }
            $del = $i . "  " . "Time-Intervals deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
        }
        $this->redirect(array('controller' => 'intervals', 'action' => 'index'));
    }

    /* ------------------------------------------------
      Function name:deleteIntervalPhoto()
      Description:Delete Interval Photo
      created:5/8/2015
      ----------------------------------------------------- */

    public function deleteIntervalPhoto($EncryptIntervalID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Interval']['store_id'] = $this->Session->read('admin_store_id');
        $data['Interval']['id'] = $this->Encryption->decode($EncryptIntervalID);
        $data['Interval']['offerImage'] = '';
        if ($this->Interval->saveInterval($data)) {
            $this->Session->setFlash(__("Interval Photo deleted"), 'alert_success');
            $this->redirect(array('controller' => 'Intervals', 'action' => 'editInterval', $EncryptIntervalID));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Intervals', 'action' => 'editInterval', $EncryptIntervalID));
        }
    }

    public function uploadfile() {
        $this->layout = 'admin_dashboard';
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            $this->loadModel('Store');
            $this->loadModel('Size');
            $this->loadModel('Item');
            $this->loadModel('ItemPrice');
            if ($tmp['Interval']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['Interval']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['Interval']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['Interval']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Interval']['file']['tmp_name']);
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
                                $itemId = $this->Item->getItemIdByName($storeId, trim($row['B']));
                                if (!empty($itemId)) {
                                    if (!empty($row['D'])) {
                                        $sizeId = $this->Size->getSizeIdByNameOnly(trim($row['D']), $storeId);
                                        if ($sizeId) {
                                            $itemsizeId = $this->ItemPrice->getItemPriceByName($itemId['Item']['id'], $sizeId['Size']['id'], $storeId);
                                            $itemsizeId['Size']['id'] = $sizeId['Size']['id'];
                                        } else {
                                            $itemsizeId['Size']['id'] = 0;
                                        }
                                    } else {
                                        $itemsizeId['Size']['id'] = 0;
                                    }

                                    if ($itemsizeId) {

                                        $offerData['store_id'] = $storeId;
                                        $offerData['merchant_id'] = $merchantId;
                                        $offerData['item_id'] = $itemId['Item']['id'];
                                        $offerData['unit'] = $row['C'];
                                        $offerData['description'] = $row['E'];
                                        $offerData['size_id'] = $itemsizeId['Size']['id'];

                                        if (!empty($row['F'])) {
                                            $offerData['is_fixed_price'] = $row['F'];
                                        } else {
                                            $offerData['is_fixed_price'] = 0;
                                        }
                                        if (!empty($row['G'])) {
                                            $offerData['offerprice'] = $row['G'];
                                        } else {
                                            $offerData['offerprice'] = 0;
                                        }

                                        if (!empty($row['H'])) {
                                            $offerData['offer_start_date'] = $this->Dateform->formatDate($row['H']);
                                        } else {
                                            $offerData['offer_start_date'] = '';
                                        }

                                        if (!empty($row['I'])) {
                                            $offerData['offer_end_date'] = $this->Dateform->formatDate($row['I']);
                                        } else {
                                            $offerData['offer_end_date'] = '';
                                        }

                                        if (!empty($row['J'])) {
                                            if ($row['J'] == 1) {
                                                $itemdata['is_time'] = 1;
                                                if (!empty($row['K']) && !empty($row['L'])) {
                                                    $itemdata['offer_start_time'] = $row['K'];
                                                    $itemdata['offer_end_time'] = $row['L'];
                                                } else {
                                                    $itemdata['is_time'] = 0;
                                                    $itemdata['offer_start_time'] = '00:30:00';
                                                    $itemdata['offer_end_time'] = '00:30:00';
                                                }
                                            } else {
                                                $itemdata['is_time'] = 0;
                                                $itemdata['offer_start_time'] = '00:30:00';
                                                $itemdata['offer_end_time'] = '00:30:00';
                                            }
                                        } else {
                                            $itemdata['is_time'] = 0;
                                            $itemdata['offer_start_time'] = '00:30:00';
                                            $itemdata['offer_end_time'] = '00:30:00';
                                        }

                                        if (!empty($row['M'])) {
                                            $offerData['is_active'] = $row['M'];
                                        } else {
                                            $offerData['is_active'] = 0;
                                        }

                                        if (!empty($row['A'])) {
                                            $offerData['id'] = $row['A'];
                                        } else {
                                            $offerData['id'] = "";
                                            $this->Interval->create();
                                        }

                                        $this->Interval->saveInterval($offerData);
                                        if (!empty($row['A'])) {
                                            $offerID = $row['A'];
                                        } else {
                                            $offerID = $this->Interval->getLastInsertId();
                                        }
                                        if ($offerID) {
                                            if (!empty($row['A'])) {
                                                $this->IntervalDetail->deleteallIntervalItems($offerID);
                                            }
                                            $da = 'N';
                                            while ($da) {
                                                if (empty($row[$da])) {
                                                    break;
                                                }
                                                $detailArray = array();
                                                $detailArray = explode(',', $row[$da]);
                                                if (isset($detailArray[1]) && !empty($detailArray[1])) {
                                                    $detailSizeId = $this->Size->getSizeIdByNameOnly(trim($detailArray[1]), $storeId);
                                                } else {
                                                    $detailSizeId['Size']['id'] = 0;
                                                }

                                                if (!empty($detailSizeId)) {
                                                    if (isset($detailArray[0]) && !empty($detailArray[0])) {
                                                        $detailItemId = $this->Item->getItemIdByName($storeId, trim($detailArray[0]));
                                                    } else {
                                                        $detailItemId = array();
                                                    }

                                                    if (!empty($detailItemId)) {
                                                        $detailItemSizeId = $this->ItemPrice->getItemPriceByName($detailItemId['Item']['id'], $detailSizeId['Size']['id'], $storeId);
                                                        if (!empty($detailItemSizeId)) {
                                                            $offerdetailsData['offerItemID'] = $detailItemId['Item']['id'];
                                                            $offerdetailsData['offer_id'] = $offerID;
                                                            $offerdetailsData['store_id'] = $storeId;
                                                            $offerdetailsData['merchant_id'] = $merchantId;
                                                            $offerdetailsData['offerSize'] = $detailSizeId['Size']['id'];
                                                            if (isset($detailArray[2]) && !empty($detailArray[2])) {
                                                                $offerdetailsData['discountAmt'] = $detailArray[2];
                                                            } else {
                                                                $offerdetailsData['discountAmt'] = 0;
                                                            }

                                                            $this->IntervalDetail->create();
                                                            $this->IntervalDetail->saveIntervalDetail($offerdetailsData);
                                                        }
                                                    }
                                                }
                                                $da++;
                                            }
                                        }
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
                $this->Session->setFlash(__($i . ' ' . 'Promotions has been saved'), 'alert_success');
                $this->redirect(array("controller" => "offers", "action" => "index"));
            }
        }
    }

    public function download() {
        $storeId = $this->Session->read('admin_store_id');
        $this->IntervalDetail->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offerItemID', 'fields' => array('name')), 'Size' => array('foreignKey' => 'offerSize', 'fields' => array('size')))));
        $this->Interval->bindModel(array('belongsTo' => array('Size' => array('fields' => array('size')), 'Item' => array('fields' => array('name'))), 'hasMany' => array('IntervalDetail' => array('conditions' => array('IntervalDetail.is_deleted' => 0), 'fields' => array('offerItemID', 'offerSize', 'discountAmt')))));
        $result = $this->Interval->fetchIntervalList($storeId);
        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
        ;
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
        $filename = 'Promotions_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Promotions');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Item Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Number of Units');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Size Name');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Description');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Is Fixed Price');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Interval Price');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Interval Start Date');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Interval End Date');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Is Time');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Interval Start Time');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Interval End Time');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Is Active');

        // $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('L1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('M1')->applyFromArray($styleArray);

        $i = 2;
        $k = 1;
        foreach ($result as $data) {

            if (!empty($data['IntervalDetail'])) {
                $index = 'N';
                foreach ($data['IntervalDetail'] as $detail) {
                    $objPHPExcel->getActiveSheet()->setCellValue("$index$k", 'Intervaled Item');
                    $objPHPExcel->getActiveSheet()->getStyle("$index$k")->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->setCellValue("$index$i", $detail['Item']['name'] . ',' . @$detail['Size']['size'] . ',' . $detail['discountAmt']);
                    $index++;
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", trim($data['Interval']['id']));
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", trim($data['Item']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", trim($data['Interval']['unit']));
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", trim($data['Size']['size']));
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", trim($data['Interval']['description']));
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", trim($data['Interval']['is_fixed_price']));
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", trim($data['Interval']['offerprice']));
            if (!empty($data['Interval']['offer_start_date'])) {
                $startDate = date('m-d-Y', strtotime($data['Interval']['offer_start_date']));
            } else {
                $startDate = '';
            }
            $objPHPExcel->getActiveSheet()->setCellValue("H$i", trim($startDate));
            if (!empty($data['Interval']['offer_end_date'])) {
                $endDate = date('m-d-Y', strtotime($data['Interval']['offer_end_date']));
            } else {
                $endDate = '';
            }
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", trim($endDate));
            $objPHPExcel->getActiveSheet()->setCellValue("J$i", trim($data['Interval']['is_time']));
            $objPHPExcel->getActiveSheet()->setCellValue("K$i", trim($data['Interval']['offer_start_time']));
            $objPHPExcel->getActiveSheet()->setCellValue("L$i", trim($data['Interval']['offer_end_time']));
            $objPHPExcel->getActiveSheet()->setCellValue("M$i", trim($data['Interval']['is_active']));

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
            $this->loadModel('Interval');
            $storeID = $this->Session->read('admin_store_id');
            $searchData = $this->Interval->find('list', array('fields' => array('Interval.name', 'Interval.name'), 'conditions' => array('OR' => array('Interval.name LIKE' => '%' . $_GET['term'] . '%'), 'Interval.is_deleted' => 0, 'Interval.store_id' => $storeID)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

}
