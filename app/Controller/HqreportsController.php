<?php

App::uses('HqAppController', 'Controller');

class HqreportsController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption');
    public $uses = array('Store', 'OrderPayment', 'Order', 'User', 'OrderItem', 'Segment', 'OrderOffer', 'OrderTopping', 'OrderPreference');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function moneyReport() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $this->layout = "hq_dashboard";
        $store_id = '';
        $storeId = '';
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $type = 1;
        $ordertype = "";
        $startdate = date('Y-m-d');
        $enddate = date('Y-m-d');
        $Month = date('m');
        $Year = date('Y');
        $yearFrom = date('Y', strtotime('-1 year'));
        $yearTo = date('Y');
        $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
        $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        if (!empty($this->request->data['Merchant']['store_id']) && $this->request->data['Merchant']['store_id']!='All') {
            $storeId = $this->request->data['Merchant']['store_id'];
            $store_id = $storeId;
            $storeDate=$this->Common->getcurrentTime($storeId,1);
            $storeDateTime=  explode(" ", $storeDate);
            $storeTime=$storeDateTime[0];
            $storeTime=$storeDateTime[1];
            $this->set('storeTime',$storeTime);
            $sdate = $storeDate." "."00:00:00";
            $edate = $storeDate." "."23:59:59";
            $type = 1;
            $ordertype = "";
            $startdate = $storeDate;
            $enddate = $storeDate;
            $expoladDate=  explode("-", $startdate);
            $Month = $expoladDate[1];
            $Year = $expoladDate[0];
            $yearFrom = date('Y', strtotime('-1 year',strtotime($Year)));
            $yearTo = $Year;
            $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
            $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
            
        }else{
            if(empty($this->request->data['Merchant']['store_id'])){
                $storeId='All';
            }else{
                $storeId = $this->request->data['Merchant']['store_id'];
            }
            
        }
        
        if (isset($this->params['named']) && isset($this->params['named']['store_id'])) {
            $store_id = $this->params['named']['store_id'];
            $storeId = $store_id;
            if (!empty($storeId) && ($storeId !== 'All')) {
                $storeDate = $this->Common->getcurrentTime($storeId, 1);
                $storeDateTime = explode(" ", $storeDate);
                $storeDate = $storeDateTime[0];
                $storeTime = $storeDateTime[1];
                $this->set('storeTime', $storeTime);
                $sdate = $storeDate . " " . "00:00:00";
                $edate = $storeDate . " " . "23:59:59";
                $type = 1;
                $ordertype = "";
                $startdate = $storeDate;
                $enddate = $storeDate;
                $expoladDate = explode("-", $startdate);
                $Month = $expoladDate[1];
                $Year = $expoladDate[0];
                $yearFrom = date('Y', strtotime('-1 year', strtotime($Year)));
                $yearTo = $Year;
                $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
                $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
            }else{
                if(empty($this->request->data['Merchant']['store_id'])){
                    $storeId='All';
                }else{
                    $storeId = $this->request->data['Merchant']['store_id'];
                }
                
            }
        }
        
        if ($storeId == 'All') {
            $this->loadModel('Store');
            $storeId = $this->Store->find('list', array('fields' => array('Store.id'), 'conditions' => array('Store.merchant_id' => $merchantId, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
        }
        
        if (!empty($this->data) || !empty($this->params['named'])) {
            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;
            if ($type == 1) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $startdate = date('Y-m-d 00:00:00', strtotime($this->params['named']['startdate']));
                    $enddate = date('Y-m-d 23:59:59', strtotime($this->params['named']['enddate']));
                    $ordertype = $this->params['named']['ordertype'];
                } else {
                    $startdate = $this->data['Report']['startdate'];
                    $enddate = $this->data['Report']['enddate'];
                    $ordertype = $this->data['Segment']['id'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
                }
                if (isset($this->request->params['named']) && isset($this->params['named']['sort'])) {
                    $startdate1 = $this->request->params['named']['startdate'];
                    $enddate1 = $this->request->params['named']['enddate'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate1));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate1));
                    $ordertype = $this->request->params['named']['ordertype'];
                    $type = $this->request->params['named']['type'];
                }
                $order = $this->orderListing($storeId, $startdate, $enddate, $ordertype);
                $graphorder = $this->ordergraphListing($storeId, $startdate, $enddate, $ordertype);
                $result = $graphorder;
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $this->request->data['Segment']['id'] = $ordertype;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'order', 'result', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
            } else if ($type == 2) {
                if (isset($this->params['named']) && isset($this->params['named']['date_start_from'])) {
                    $startFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_start_from']));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($this->params['named']['date_end_from']));
                    $ordertype = $this->params['named']['ordertype'];
                    $weekyear = date('Y', strtotime($this->params['named']['date_start_from']));
                } else {
                    $startFrom = $this->data['Report']['date_start_from'];
                    $endFrom = $this->data['Report']['date_end_from'];
                    $weekyear = date('Y', strtotime($startFrom));
                    $ordertype = $this->data['Segment']['id'];
                    $startFrom = date('Y-m-d 00:00:00', strtotime($startFrom));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($endFrom));
                }
                
                $expoladEndDate=  explode(" ", $endFrom);
                $endMonth = $expoladEndDate[1];
                $explodeEndYear = explode("-", $expoladEndDate[0]);
                $endYear=$explodeEndYear[0];
                $startweekNumber = date("W", strtotime($startFrom));
                $endWeekNumber = date("W", strtotime($endFrom));
                $data = array();
                $weeknumbers = '';
                $j = 0;
                for ($i = $startweekNumber; $i <= $endWeekNumber; $i++) {
                    $data[$i] = array();
                    if ($j == 0) {
                        $weeknumbers.="'Week" . $i . "'";
                    } else {
                        $weeknumbers.=",'Week" . $i . "'";
                    }
                    $j++;

                    $time = strtotime("1 January $weekyear", time());
                    $day = date('w', $time);
                    $time += ((7 * $i) - $day) * 24 * 3600;
                    $data[$i]['daywise'] = array();
                    for ($k = 0; $k <= 6; $k++) {
                        $time2 = $time + $k * 24 * 3600;
                        $data[$i]['daywise'][date('Y-m-d', $time2)] = array(0);
                        if ($k == 0) {
                            $datestring = "'" . date('Y-m-d', $time2) . "'";
                        } else {
                            $datestring.=",'" . date('Y-m-d', $time2) . "'";
                        }
                        $data[$i]['datestring'] = $datestring;
                    }
                }

                $result1 = $this->fetchWeeklyOrderToday($storeId, $startFrom, $endFrom, $ordertype,$endYear);
                $weekarray = array();
                $datearray = array();
                foreach ($result1 as $k => $result) {
                    if (in_array($result[0]['WEEKno'], $weekarray)) {
                        $data[$result[0]['WEEKno']]['week'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['totalamount'] += $result[0]['total'];
                        $data[$result[0]['WEEKno']]['totalorders'] += 1;
                    } else {
                        $weekarray[$result[0]['WEEKno']] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['totalamount'] = $result[0]['total'];
                        $data[$result[0]['WEEKno']]['totalorders'] = 1;
                    }
                    if (in_array($result[0]['order_date'], $datearray)) {
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['total'] += $result[0]['total'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['totalorders'] += 1;
                    } else {
                        $datearray[$result[0]['order_date']] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['total'] = $result[0]['total'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['totalorders'] = 1;
                    }
                }

                $order = $this->orderListingweek($storeId, $startFrom, $endFrom, $ordertype,$endYear);
                $startFrom = $this->Dateform->formatDate($startFrom);
                $endFrom = $this->Dateform->formatDate($endFrom);
                $startdate = $startFrom;
                $enddate = $endFrom;
                $paginationdata = array('store_id' => $store_id, 'date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2, 'ordertype' => $ordertype);
                $this->set(compact('paginationdata', 'order', 'weeknumbers', 'data', 'date', 'type', 'startFrom', 'endFrom', 'Month', 'Year', 'yearFrom', 'yearTo', 'startdate', 'enddate'));
            } else if ($type == 3) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    $ordertype = $this->params['named']['ordertype'];
                } else {
                    $Year = $this->data['Report']['year'];
                    $Month = $this->data['Report']['month'];
                    $dateFrom = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-01';
                    $dateTo = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-31';
                    $ordertype = $this->data['Segment']['id'];
                }
                $order = $this->orderListing($storeId, $dateFrom, $dateTo, $ordertype);
                $graphorder = $this->ordergraphListing($storeId, $dateFrom, $dateTo, $ordertype);
                $result = $graphorder;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 4) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    $ordertype = $this->params['named']['ordertype'];
                } else {
                    $yearFrom = $this->data['Report']['from_year'];
                    $yearTo = $this->data['Report']['to_year'];
                    $dateFrom = $yearFrom . '-' . '01' . '-01';
                    $dateTo = $yearTo . '-' . '12' . '-31';
                    $ordertype = $this->data['Segment']['id'];
                }
                $order = $this->orderListing($storeId, $dateFrom, $dateTo, $ordertype);
                $graphorder = $this->ordergraphListing($storeId, $dateFrom, $dateTo, $ordertype);
                $result = $graphorder;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 5) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    $ordertype = $this->params['named']['ordertype'];
                } else {
                    $dateFrom = $startdate;
                    $dateTo = $enddate;
                    $ordertype = $this->data['Segment']['id'];
                }
                $order = $this->orderListing($storeId, null, null, $ordertype);
                $graphorder = $this->ordergraphListing($storeId, null, null, $ordertype);
                $result = $graphorder;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {
            $order = $this->orderListing($storeId, $sdate, $edate, $ordertype);
            $graphorder = $this->ordergraphListing($storeId, $sdate, $edate, $ordertype);
            $result = $graphorder;
            $paginationdata = array('store_id' => $store_id, 'startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'ordertype' => $ordertype);
            $this->set(compact('order', 'result', 'paginationdata'));
        }
        $typeList = $this->Segment->OrderTypeList();
        $this->set('typeList', $typeList);
        $this->set(compact('store_id', 'ordertype', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo'));
    }

    public function moneyReportDownload($storeId = null, $type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $ordertype = null) {
        //$sdate = date('Y-m-d 00:00:00');
        //$edate = date('Y-m-d 23:59:59');
        if (empty($storeId)) {
            $this->Session->setFlash(__("Please select store."));
            $this->redirect(array('controller' => 'hqreports', 'action' => 'moneyReport'));
        }
        if ($storeId=='All') {
            $this->Session->setFlash(__("Please select store."));
            $this->redirect(array('controller' => 'hqreports', 'action' => 'moneyReport'));
        }
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderListg($storeId, $startdate, $enddate, $ordertype);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderListgweek($storeId, $startdate, $enddate, $ordertype);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->orderListg($storeId, $dateFrom, $dateTo, $ordertype);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->orderListg($storeId, $dateFrom, $dateTo, $ordertype);
            $text = 'Yearly_Report';
        } else if ($type == 5) {
            $order = $this->orderListg($storeId, null, null, $ordertype);
            $text = 'LifeTime_Report';
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
        $filename = $text . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('MoneyReport');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Order No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Customer Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Items');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Amount($)');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Phone');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Address');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Email');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Order Type');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Created');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
        $i = 2;
        foreach ($order as $key => $data) {
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Order']['order_number']);
            if ($data['DeliveryAddress']['name_on_bell']) {
                $name = $data['DeliveryAddress']['name_on_bell'];
            } else {
                $name = $data['User']['fname'] . " " . $data['User']['lname'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $name);
            $j = 0;
            $items = "";
            foreach ($data['OrderItem'] as $key => $item) {
                if ($j == 0) {
                    $items = $item['Item']['name'];
                } else {
                    $items.=", " . $item['Item']['name'];
                }
                $j++;
            }
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $items);
            if ($data['Order']['coupon_discount'] > 0) {
                $total_amount = $data['Order']['amount'] - $data['Order']['coupon_discount'];
            } else {
                $total_amount = $data['Order']['amount'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $total_amount);
            if (!empty($data['DeliveryAddress']['phone'])) {
                $phone = $data['DeliveryAddress']['phone'];
            } else {
                $phone = $data['User']['phone'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $phone);
            if (!empty($data['DeliveryAddress']['address'])) {
                $address = $data['DeliveryAddress']['address'];
            } else {
                $address = $data['User']['address'];
            }
            if ($data['Segment']['id'] == 2) {
                $address = $data['Segment']['name'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", $address);
            if (!empty($data['DeliveryAddress']['email'])) {
                $email = $data['DeliveryAddress']['email'];
            } else {
                $email = $data['User']['email'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", $email);

            $objPHPExcel->getActiveSheet()->setCellValue("H$i", $data['Segment']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", $data['Order']['created']);
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function orderListgweek($storeID = null, $startDate = null, $endDate = null, $orderType = null) {
        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if (!empty($storeID)) {
            $criteria .=" AND Order.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND WEEK(Order.created) >=WEEK('" . $startDate . "') AND WEEK(Order.created) <=WEEK('" . $endDate . "')";
        }
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
            )
                ), false
        );
        if ($orderType) {
            $criteria.=" AND Segment.id=$orderType";
        }
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));
        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderListingweek()
      Description:graph order list
      created:22/09/2015
     *
     * ********************* */

    public function orderListingweek($storeID = null, $startDate = null, $endDate = null, $orderType = null,$endYear=null) {
        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if (!empty($storeID)) {
            //$criteria .= " AND Order.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND WEEK(Order.created) >=WEEK('" . $startDate . "') AND WEEK(Order.created) <=WEEK('" . $endDate . "') AND YEAR(Order.created) ='" . $endYear . "'";
        }
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                ), 'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name')
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
            )
                ), false
        );
        if ($orderType) {
            $criteria.=" AND Segment.id=$orderType";
        }
        $this->paginate = array('recursive' => 2, 'conditions' => array_merge(array($criteria), array('Order.store_id' => $storeID)), 'order' => array('Order.created' => 'DESC'));
        $orderdetail = $this->paginate('Order');
        return $orderdetail;
    }

    public function orderReportDownload($storeId = null, $type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $ordertype = null) {
        if (empty($storeId)) {
            $this->Session->setFlash(__("Please select store."));
            $this->redirect(array('controller' => 'hqreports', 'action' => 'orderReport'));
        }
         if ($storeId=='All') {
            $this->Session->setFlash(__("Please select store."));
            $this->redirect(array('controller' => 'hqreports', 'action' => 'orderReport'));
        }
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderListg($storeId, $startdate, $enddate, $ordertype);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderListgweek($storeId, $startdate, $enddate, $ordertype);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->orderListg($storeId, $dateFrom, $dateTo, $ordertype);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->orderListg($storeId, $dateFrom, $dateTo, $ordertype);
            $text = 'Yearly_Report';
        } else {
            $order = $this->orderListg($storeId, null, null, $ordertype);
            $text = 'LifeTime_Report';
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
        $filename = $text . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('OrderReport');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Order No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Customer Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Items');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Amount($)');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Phone');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Address');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Email');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Order Type');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Created');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
        $i = 2;
        foreach ($order as $key => $data) {
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Order']['order_number']);
            if ($data['DeliveryAddress']['name_on_bell']) {
                $name = $data['DeliveryAddress']['name_on_bell'];
            } else {
                $name = $data['User']['fname'] . " " . $data['User']['lname'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $name);
            $j = 0;
            $items = "";
            foreach ($data['OrderItem'] as $key => $item) {
                if ($j == 0) {
                    $items = $item['Item']['name'];
                } else {
                    $items.=", " . $item['Item']['name'];
                }
                $j++;
            }
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $items);
            if ($data['Order']['coupon_discount'] > 0) {
                $total_amount = $data['Order']['amount'] - $data['Order']['coupon_discount'];
            } else {
                $total_amount = $data['Order']['amount'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $total_amount);
            if (!empty($data['DeliveryAddress']['phone'])) {
                $phone = $data['DeliveryAddress']['phone'];
            } else {
                $phone = $data['User']['phone'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $phone);
            if (!empty($data['DeliveryAddress']['address'])) {
                $address = $data['DeliveryAddress']['address'];
            } else {
                $address = $data['User']['address'];
            }
            if ($data['Segment']['id'] == 2) {
                $address = $data['Segment']['name'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", $address);
            if (!empty($data['DeliveryAddress']['email'])) {
                $email = $data['DeliveryAddress']['email'];
            } else {
                $email = $data['User']['email'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", $email);

            $objPHPExcel->getActiveSheet()->setCellValue("H$i", $data['Segment']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", $data['Order']['created']);
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function customerReportDownload($storeId = null, $type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null) {
        if (empty($storeId)) {
            $this->Session->setFlash(__("Please select store."));
            $this->redirect(array('controller' => 'hqreports', 'action' => 'customerReport'));
        }
        if ($storeId=='All') {
            $this->Session->setFlash(__("Please select store."));
            $this->redirect(array('controller' => 'hqreports', 'action' => 'customerReport'));
        }
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $userdata = $this->userexcelListing($storeId, $startdate, $enddate);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $userdata = $this->weekuserexcelListing($storeId, $startdate, $enddate);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $userdata = $this->userexcelListing($storeId, $dateFrom, $dateTo);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $userdata = $this->userexcelListing($storeId, $dateFrom, $dateTo);
            $text = 'Yearly_Report';
        } else {
            $userdata = $this->userexcelListing($storeId, null, null);
            $text = 'LifeTime_Report';
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
        $filename = $text . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('CustomerReport');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Customer Name');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Email');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Phone');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Address');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Created');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $i = 2;
        foreach ($userdata as $data) {
            $name = $data['User']['fname'] . " " . $data['User']['lname'];
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $name);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['User']['email']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['User']['phone']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $data['User']['address']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['User']['created']);
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /*     * ***********************
     * Function name:weekuserexcelListing()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function weekuserexcelListing($storeID = null, $startDate = null, $endDate = null) {
        $criteria = "User.is_deleted=0 AND User.is_active=1 AND User.role_id=4";
        if (!empty($storeID)) {
            $criteria .= " AND User.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND WEEK(User.created) >=WEEK('" . $startDate . "') AND WEEK(User.created) <=WEEK('" . $endDate . "')";
        }
        $userdetail = $this->User->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC')));
        return $userdetail;
    }

    public function orderReport() {
//        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport')) {
//            $this->Session->setFlash(__("Permission Denied"));
//            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
//        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $this->layout = "hq_dashboard";
        $store_id = '';
        $storeId = '';
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $type = 1;
        $ordertype = "";
        $startdate = date('Y-m-d');
        $enddate = date('Y-m-d');
        $Month = date('m');
        $Year = date('Y');
        $yearFrom = date('Y', strtotime('-1 year'));
        $yearTo = date('Y');
        $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
        $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        
       if (!empty($this->request->data['Merchant']['store_id']) && $this->request->data['Merchant']['store_id']!='All') {
            $storeId = $this->request->data['Merchant']['store_id'];
            $store_id = $storeId;
            $storeDate=$this->Common->getcurrentTime($storeId,1);
            $storeDateTime=  explode(" ", $storeDate);
            $storeTime=$storeDateTime[0];
            $storeTime=$storeDateTime[1];
            $this->set('storeTime',$storeTime);
            $sdate = $storeDate." "."00:00:00";
            $edate = $storeDate." "."23:59:59";
            $type = 1;
            $ordertype = "";
            $startdate = $storeDate;
            $enddate = $storeDate;
            $expoladDate=  explode("-", $startdate);
            $Month = $expoladDate[1];
            $Year = $expoladDate[0];
            $yearFrom = date('Y', strtotime('-1 year',strtotime($Year)));
            $yearTo = $Year;
            $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
            $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        }else{
            if(empty($this->request->data['Merchant']['store_id'])){
                $storeId='All';
            }else{
                $storeId = $this->request->data['Merchant']['store_id'];
            }
            
        }
        
        if (isset($this->params['named']) && isset($this->params['named']['store_id'])) {
            $store_id = $this->params['named']['store_id'];
            $storeId = $store_id;
            if (!empty($storeId) && ($storeId !== 'All')) {
                $storeDate = $this->Common->getcurrentTime($storeId, 1);
                $storeDateTime = explode(" ", $storeDate);
                $storeDate = $storeDateTime[0];
                $storeTime = $storeDateTime[1];
                $this->set('storeTime', $storeTime);
                $sdate = $storeDate . " " . "00:00:00";
                $edate = $storeDate . " " . "23:59:59";
                $type = 1;
                $ordertype = "";
                $startdate = $storeDate;
                $enddate = $storeDate;
                $expoladDate = explode("-", $startdate);
                $Month = $expoladDate[1];
                $Year = $expoladDate[0];
                $yearFrom = date('Y', strtotime('-1 year', strtotime($Year)));
                $yearTo = $Year;
                $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
                $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        }else{
                if(empty($this->request->data['Merchant']['store_id'])){
                    $storeId='All';
                }else{
                    $storeId = $this->request->data['Merchant']['store_id'];
                }
                
            }
        }
        if ($storeId == 'All') {
            $this->loadModel('Store');
            $storeId = $this->Store->find('list', array('fields' => array('Store.id'), 'conditions' => array('Store.merchant_id' => $merchantId, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
        }
        
        if (!empty($this->data) || !empty($this->params['named'])) {
            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;
            if ($type == 1) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $startdate = date('Y-m-d 00:00:00', strtotime($this->params['named']['startdate']));
                    $enddate = date('Y-m-d 23:59:59', strtotime($this->params['named']['enddate']));
                    $ordertype = $this->params['named']['ordertype'];
                } else {
                    $startdate = $this->data['Report']['startdate'];
                    $enddate = $this->data['Report']['enddate'];
                    $ordertype = $this->data['Segment']['id'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
                }
                if (isset($this->request->params['named']) && isset($this->params['named']['sort'])) {
                    $startdate1 = $this->request->params['named']['startdate'];
                    $enddate1 = $this->request->params['named']['enddate'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate1));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate1));
                    $ordertype = $this->request->params['named']['ordertype'];
                    $type = $this->request->params['named']['type'];
                }
                $order = $this->orderListing($storeId, $startdate, $enddate, $ordertype);
                $graphorder = $this->ordergraphListing($storeId, $startdate, $enddate, $ordertype);
                $result = $graphorder;
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $this->request->data['Segment']['id'] = $ordertype;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'order', 'result', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
            } else if ($type == 2) {
                if (isset($this->params['named']) && isset($this->params['named']['date_start_from'])) {
                    $startFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_start_from']));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($this->params['named']['date_end_from']));
                    $ordertype = $this->params['named']['ordertype'];
                    $weekyear = date('Y', strtotime($this->params['named']['date_start_from']));
                } else {
                    $startFrom = $this->data['Report']['date_start_from'];
                    $endFrom = $this->data['Report']['date_end_from'];
                    $weekyear = date('Y', strtotime($startFrom));
                    $ordertype = $this->data['Segment']['id'];
                    $startFrom = date('Y-m-d 00:00:00', strtotime($startFrom));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($endFrom));
                }
                $expoladEndDate=  explode(" ", $endFrom);
                $endMonth = $expoladEndDate[1];
                $explodeEndYear = explode("-", $expoladEndDate[0]);
                $endYear=$explodeEndYear[0];
                $startweekNumber = date("W", strtotime($startFrom));
                $endWeekNumber = date("W", strtotime($endFrom));
                $data = array();
                $weeknumbers = '';
                $j = 0;
                for ($i = $startweekNumber; $i <= $endWeekNumber; $i++) {
                    $data[$i] = array();
                    if ($j == 0) {
                        $weeknumbers.="'Week" . $i . "'";
                    } else {
                        $weeknumbers.=",'Week" . $i . "'";
                    }
                    $j++;
                    $time = strtotime("1 January $weekyear", time());
                    $day = date('w', $time);
                    $time += ((7 * $i) - $day) * 24 * 3600;
                    $data[$i]['daywise'] = array();
                    for ($k = 0; $k <= 6; $k++) {
                        $time2 = $time + $k * 24 * 3600;
                        $data[$i]['daywise'][date('Y-m-d', $time2)] = array(0);
                        if ($k == 0) {
                            $datestring = "'" . date('Y-m-d', $time2) . "'";
                        } else {
                            $datestring.=",'" . date('Y-m-d', $time2) . "'";
                        }
                        $data[$i]['datestring'] = $datestring;
                    }
                }

                $result1 = $this->fetchWeeklyOrderToday($storeId, $startFrom, $endFrom, $ordertype,$endYear);
                $weekarray = array();
                $datearray = array();
                foreach ($result1 as $k => $result) {
                    if (in_array($result[0]['WEEKno'], $weekarray)) {
                        $data[$result[0]['WEEKno']]['week'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['totalorders'] += 1;
                    } else {
                        $weekarray[$result[0]['WEEKno']] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['totalorders'] = 1;
                    }

                    if (in_array($result[0]['order_date'], $datearray)) {
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['totalorders'] += 1;
                    } else {
                        $datearray[$result[0]['order_date']] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['totalorders'] = 1;
                    }
                }
                $order = $this->orderListingweek($storeId, $startFrom, $endFrom, $ordertype,$endYear);
                $startFrom = $this->Dateform->formatDate($startFrom);
                $endFrom = $this->Dateform->formatDate($endFrom);
                $startdate = $startFrom;
                $enddate = $endFrom;
                $paginationdata = array('store_id' => $store_id, 'date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2, 'ordertype' => $ordertype);
                $this->set(compact('paginationdata', 'order', 'weeknumbers', 'data', 'date', 'type', 'startFrom', 'endFrom', 'Month', 'Year', 'yearFrom', 'yearTo', 'startdate', 'enddate'));
            } else if ($type == 3) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    $ordertype = $this->params['named']['ordertype'];
                } else {
                    $Year = $this->data['Report']['year'];
                    $Month = $this->data['Report']['month'];
                    $dateFrom = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-01';
                    $dateTo = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-31';
                    $ordertype = $this->data['Segment']['id'];
                }
                $order = $this->orderListing($storeId, $dateFrom, $dateTo, $ordertype);
                $graphorder = $this->ordergraphListing($storeId, $dateFrom, $dateTo, $ordertype);
                $result = $graphorder;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 4) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    $ordertype = $this->params['named']['ordertype'];
                } else {
                    $yearFrom = $this->data['Report']['from_year'];
                    $yearTo = $this->data['Report']['to_year'];
                    $dateFrom = $yearFrom . '-' . '01' . '-01';
                    $dateTo = $yearTo . '-' . '12' . '-31';
                    $ordertype = $this->data['Segment']['id'];
                }
                $order = $this->orderListing($storeId, $dateFrom, $dateTo, $ordertype);
                $graphorder = $this->ordergraphListing($storeId, $dateFrom, $dateTo, $ordertype);
                $result = $graphorder;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 5) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    $ordertype = $this->params['named']['ordertype'];
                } else {
                    $dateFrom = $startdate;
                    $dateTo = $enddate;
                    $ordertype = $this->data['Segment']['id'];
                }
                $order = $this->orderListing($storeId, null, null, $ordertype);
                $graphorder = $this->ordergraphListing($storeId, null, null, $ordertype);
                $result = $graphorder;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {
            $order = $this->orderListing($storeId, $sdate, $edate, $ordertype);
            $graphorder = $this->ordergraphListing($storeId, $sdate, $edate, $ordertype);
            $paginationdata = array('store_id' => $store_id, 'startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'ordertype' => $ordertype);
            $result = $graphorder;
            $this->set(compact('order', 'result', 'paginationdata'));
        }
        $typeList = $this->Segment->OrderTypeList();
        $this->set('typeList', $typeList);
        $this->set(compact('store_id', 'ordertype', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
    }

    public function customerReport() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $store_id = '';
        $storeId = '';
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $type = 1;
        $ordertype = "";
        $startdate = date('Y-m-d');
        $page = '';
        $enddate = date('Y-m-d');
        $Month = date('m');
        $Year = date('Y');
        $yearFrom = date('Y', strtotime('-1 year'));
        $yearTo = date('Y');
        $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
        $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        if (!empty($this->request->data['Merchant']['store_id']) && $this->request->data['Merchant']['store_id']!='All') {
            $storeId = $this->request->data['Merchant']['store_id'];
            $store_id = $storeId;
            $storeDate=$this->Common->getcurrentTime($storeId,1);
            $storeDateTime=  explode(" ", $storeDate);
            $storeTime=$storeDateTime[0];
            $storeTime=$storeDateTime[1];
            $this->set('storeTime',$storeTime);
            $sdate = $storeDate." "."00:00:00";
            $edate = $storeDate." "."23:59:59";
            $type = 1;
            $ordertype = "";
            $startdate = $storeDate;
            $enddate = $storeDate;
            $expoladDate=  explode("-", $startdate);
            $Month = $expoladDate[1];
            $Year = $expoladDate[0];
            $yearFrom = date('Y', strtotime('-1 year',strtotime($Year)));
            $yearTo = $Year;
            $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
            $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
            
        }else{
            if(empty($this->request->data['Merchant']['store_id'])){
                $storeId='All';
            }else{
                $storeId = $this->request->data['Merchant']['store_id'];
            }
            
        }
        if (isset($this->params['named']) && isset($this->params['named']['store_id'])) {
            $store_id = $this->params['named']['store_id'];
            $storeId = $store_id;
            if (!empty($storeId) && ($storeId !== 'All')) {
                $storeDate = $this->Common->getcurrentTime($storeId, 1);
                $storeDateTime = explode(" ", $storeDate);
                $storeDate = $storeDateTime[0];
                $storeTime = $storeDateTime[1];
                $this->set('storeTime', $storeTime);
                $sdate = $storeDate . " " . "00:00:00";
                $edate = $storeDate . " " . "23:59:59";
                $type = 1;
                $ordertype = "";
                $startdate = $storeDate;
                $enddate = $storeDate;
                $expoladDate = explode("-", $startdate);
                $Month = $expoladDate[1];
                $Year = $expoladDate[0];
                $yearFrom = date('Y', strtotime('-1 year', strtotime($Year)));
                $yearTo = $Year;
                $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
                $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
            }else{
                if(empty($this->request->data['Merchant']['store_id'])){
                    $storeId='All';
                }else{
                    $storeId = $this->request->data['Merchant']['store_id'];
                }
                
            }
        }
        if ($storeId == 'All') {
            $this->loadModel('Store');
            $storeId = $this->Store->find('list', array('fields' => array('Store.id'), 'conditions' => array('Store.merchant_id' => $merchantId, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
            if(!empty($storeId)){
            $lastID=end($storeId)+1;
            $storeArry=array($lastID=>'0');
            $storeId= array_merge($storeArry,$storeId);    
            }
            
        }
        
        if (!empty($this->data) || !empty($this->params['named'])) {
            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;
            if ($type == 1) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $startdate = date('Y-m-d 00:00:00', strtotime($this->params['named']['startdate']));
                    $enddate = date('Y-m-d 23:59:59', strtotime($this->params['named']['enddate']));
                } else {
                    $startdate = $this->data['Report']['startdate'];
                    $enddate = $this->data['Report']['enddate'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
                }
                if (isset($this->request->params['named']) && isset($this->params['named']['sort'])) {
                    $startdate1 = $this->request->params['named']['startdate'];
                    $enddate1 = $this->request->params['named']['enddate'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate1));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate1));
                    if (!empty($this->request->params['named']['ordertype'])) {
                        $ordertype = $this->request->params['named']['ordertype'];
                    }
                    $type = $this->request->params['named']['type'];
                    $page = 1;
                }
                $user = array();
                if(!empty($storeId)){
                    $result1 = $this->User->fetchUserToday($storeId, $startdate, $enddate);
                    foreach ($result1 as $key => $data) {
                        $user[$key]['User']['per_day'] = $data[0]['per_day'];
                        $user[$key]['User']['created'] = $data['User']['created'];
                    }
                }
                $result = $user;
                $userdata = $this->userListing($storeId, $startdate, $enddate);
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $paginationdata = array('store_id' => $store_id, 'startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'page' => 1);
                $this->set(compact('page', 'userdata', 'user', 'result', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
            } else if ($type == 2) {
                if (isset($this->params['named']) && isset($this->params['named']['date_start_from'])) {
                    $startFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_start_from']));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($this->params['named']['date_end_from']));
                    $weekyear = date('Y', strtotime($this->params['named']['date_start_from']));
                    if (!empty($this->params['named']['page'])) {
                        $page = $this->params['named']['page'];
                    }
                } else {
                    $startFrom = $this->data['Report']['date_start_from'];
                    $endFrom = $this->data['Report']['date_end_from'];
                    $weekyear = date('Y', strtotime($startFrom));
                    $startFrom = date('Y-m-d 00:00:00', strtotime($startFrom));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($endFrom));
                    $page = 1;
                }
                $expoladEndDate=  explode(" ", $endFrom);
                $endMonth = $expoladEndDate[1];
                $explodeEndYear = explode("-", $expoladEndDate[0]);
                $endYear=$explodeEndYear[0];
                $startweekNumber = date("W", strtotime($startFrom));
                $endWeekNumber = date("W", strtotime($endFrom));
                $data = array();
                $return = array();
                $weeknumbers = '';
                $j = 0;
                for ($i = $startweekNumber; $i <= $endWeekNumber; $i++) {
                    $data[$i] = array();
                    if ($j == 0) {
                        $weeknumbers.="'Week" . $i . "'";
                    } else {
                        $weeknumbers.=",'Week" . $i . "'";
                    }
                    $j++;

                    $time = strtotime("1 January $weekyear", time());
                    $day = date('w', $time);
                    $time += ((7 * $i) - $day) * 24 * 3600;
                    $data[$i]['daywise'] = array();
                    for ($k = 0; $k <= 6; $k++) {
                        $time2 = $time + $k * 24 * 3600;
                        $data[$i]['daywise'][date('Y-m-d', $time2)] = array(0);
                        if ($k == 0) {
                            $datestring = "'" . date('Y-m-d', $time2) . "'";
                        } else {
                            $datestring.=",'" . date('Y-m-d', $time2) . "'";
                        }
                        $data[$i]['datestring'] = $datestring;
                    }
                }
                $result1 = $this->fetchWeeklyUserToday($storeId, $startFrom, $endFrom,$endYear);
                $weekarray = array();
                $datearray = array();
                foreach ($result1 as $k => $result) {
                    if (in_array($result[0]['WEEKno'], $weekarray)) {
                        $data[$result[0]['WEEKno']]['week'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['totaluser'] += $result[0]['total'];
                    } else {
                        $weekarray[$result[0]['WEEKno']] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['totaluser'] = $result[0]['total'];
                    }
                    if (in_array($result[0]['order_date'], $datearray)) {
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['total'] += $result[0]['total'];
                    } else {
                        $datearray[$result[0]['order_date']] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['total'] = $result[0]['total'];
                    }
                }
                $userdata = $this->userListingweekly($storeId, $startFrom, $endFrom,$endYear);
                $startFrom = $this->Dateform->formatDate($startFrom);
                $endFrom = $this->Dateform->formatDate($endFrom);
                $startdate = $startFrom;
                $enddate = $endFrom;
                $paginationdata = array('store_id' => $store_id, 'date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2, 'page' => $page);
                $this->set(compact('page', 'paginationdata', 'userdata', 'weeknumbers', 'data', 'date', 'type', 'startFrom', 'endFrom', 'Month', 'Year', 'yearFrom', 'yearTo', 'startdate', 'enddate'));
            } else if ($type == 3) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    if (!empty($this->params['named']['page'])) {
                        $page = $this->params['named']['page'];
                    }
                } else {
                    $Year = $this->data['Report']['year'];
                    $Month = $this->data['Report']['month'];
                    $dateFrom = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-01';
                    $dateTo = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-31';
                    $page = 1;
                }
                    $user = array();
                    if(!empty($storeId)){
                    $result1 = $this->User->fetchUserToday($storeId, $dateFrom, $dateTo);
                    foreach ($result1 as $key => $data) {
                        $user[$key]['User']['per_day'] = $data[0]['per_day'];
                        $user[$key]['User']['created'] = $data['User']['created'];
                    }
                }
                $result = $user;
                $userdata = $this->userListing($storeId, $dateFrom, $dateTo);
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'page' => $page);
                $this->set(compact('page', 'userdata', 'user', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 4) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    if (!empty($this->params['named']['page'])) {
                        $page = $this->params['named']['page'];
                    }
                } else {
                    $yearFrom = $this->data['Report']['from_year'];
                    $yearTo = $this->data['Report']['to_year'];
                    $dateFrom = $yearFrom . '-' . '01' . '-01';
                    $dateTo = $yearTo . '-' . '12' . '-31';
                    $page = 1;
                }
                 $user = array();
                 if(!empty($storeId)){
                    $result1 = $this->User->fetchUserToday($storeId, $dateFrom, $dateTo);
                    foreach ($result1 as $key => $data) {
                        $user[$key]['User']['per_day'] = $data[0]['per_day'];
                        $user[$key]['User']['created'] = $data['User']['created'];
                    }
                 }
                $result = $user;
                $userdata = $this->userListing($storeId, $dateFrom, $dateTo);
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'page' => $page);
                $this->set(compact('page', 'userdata', 'user', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 5) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    if (!empty($this->params['named']['page'])) {
                        $page = $this->params['named']['page'];
                    }
                } else {
                    $dateFrom = $startdate;
                    $dateTo = $enddate;
                    $page = 1;
                }
                  $user = array();
                 if(!empty($storeId)){
                    $result1 = $this->User->fetchUserToday($storeId, null, null);
                    foreach ($result1 as $key => $data) {
                        $user[$key]['User']['per_day'] = $data[0]['per_day'];
                        $user[$key]['User']['created'] = $data['User']['created'];
                    }
                 }
                $result = $user;
                $userdata = $this->userListing($storeId, null, null);
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'ordertype' => $ordertype, 'page' => $page);
                $this->set(compact('page', 'userdata', 'user', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {
            $user = array();
             if(!empty($storeId)){
                $result1 = $this->User->fetchUserToday($storeId, $sdate, $edate);
                foreach ($result1 as $key => $data) {
                    $user[$key]['User']['per_day'] = $data[0]['per_day'];
                    $user[$key]['User']['created'] = $data['User']['created'];
                }
             }
            
            
            $page = 1;
            $result = $user;
            $userdata = $this->userListing($storeId, $sdate, $edate);
            $paginationdata = array('store_id' => $store_id, 'startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'ordertype' => $ordertype, 'page' => 1);
            $this->set(compact('page', 'order', 'result', 'paginationdata'));
        }
        $this->set(compact('store_id', 'page', 'userdata', 'user', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo'));
    }

    /*     * ***********************
     * Function name:userListingweekly()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function userListingweekly($storeID = null, $startDate = null, $endDate = null,$endYear=null) {
        $criteria = "User.is_deleted=0 AND User.is_active=1 AND User.role_id IN (4,5)";
        if (!empty($storeID)) {
            //$criteria .= " AND User.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $stratdate = date('Y-m-d 00:00:00', strtotime($stratdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $criteria.= " AND WEEK(User.created) >=WEEK('" . $stratdate . "') AND WEEK(User.created) <=WEEK('" . $enddate . "') AND YEAR(User.created) ='" . $endYear . "'";
        }
        $this->User->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name')
                )
            )
                ), false
        );
        $this->paginate = array('recursive' => 2, 'conditions' => array_merge(array($criteria), array('User.store_id' => $storeID)), 'order' => array('User.created' => 'DESC'));
        $userdetail = $this->paginate('User');
        return $userdetail;
    }

    /*     * ***********************
     * Function name:itemListing()
      Description:graph item list
      created:06/10/2015
     *
     * ********************* */

    public function itemListing($storeID = null, $startDate = null, $endDate = null, $itemId = null) {
        $this->OrderItem->bindModel(array('belongsTo' => array('Order')));
        if ($startDate && $endDate) {
            $conditions = array('Order.created >=' => $startDate, 'Order.created <=' => $endDate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        } else {
            $conditions = array('Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        }
        if (!empty($itemId)) {
            $conditions['OrderItem.item_id'] = $itemId;
        }
        //if (!empty($storeID)) {
            $conditions = array_merge(array($conditions), array('OrderItem.store_id' => $storeID));
        //}
        $orderdetail = $this->OrderItem->find('all', array('fields' => array('DATE(OrderItem.created) AS order_date', 'Count(OrderItem.created) AS number'), 'group' => array("DATE_FORMAT(OrderItem.created, '%Y-%m-%d')"), 'conditions' => array($conditions), 'order' => array('OrderItem.created' => 'DESC')));
        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderListing()
      Description:graph order list
      created:22/09/2015
     *
     * ********************* */

    public function orderListing($storeID = null, $startDate = null, $endDate = null, $orderType = null) {
        
        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if (!empty($storeID) && count($storeID) == 1) {
            // $criteria .= " AND Order.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                ),
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name')
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
            )
                ), false
        );
        if (!empty($orderType)) {
            $criteria.=" AND Segment.id=$orderType";
        }
        $this->paginate = array('recursive' => 2, 'conditions' => array_merge(array($criteria), array('Order.store_id' => $storeID)), 'order' => array('Order.created' => 'DESC'));
        $orderdetail = $this->paginate('Order');
        
        return $orderdetail;
    }

    public function orderListg($storeID = null, $startDate = null, $endDate = null, $orderType = null) {
        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if (!empty($storeID)) {
            $criteria .= " AND Order.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
            )
                ), false
        );
        if ($orderType) {
            $criteria.=" AND Segment.id=$orderType";
        }
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));
        return $orderdetail;
    }

    /*     * ***********************
     * Function name:ordergraphListing()
      Description:graph order list
      created:22/09/2015
     *
     * ********************* */

    public function ordergraphListing($storeID = null, $startDate = null, $endDate = null, $orderType = null) {
        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if (!empty($storeID)) {
             //$criteria .= " AND Order.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
            )
                ), false
        );
        if ($orderType) {
            $criteria.=" AND Segment.id=$orderType";
        }
        $graphorderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array_merge(array($criteria), array('Order.store_id' => $storeID)), 'order' => array('Order.created' => 'DESC')));
        return $graphorderdetail;
    }

    /*     * ***********************
     * Function name:userListing()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function userListing($storeID = null, $startDate = null, $endDate = null) {
         $role_id=array(4,5);
        $criteria = "User.is_deleted=0 AND User.is_active=1";
        if (!empty($storeID)) {
            //$criteria .= " AND User.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (User.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }
        $this->User->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name')
                )
            )
                ), false
        );
        $this->paginate = array('recursive' => 2, 'conditions' => array_merge(array($criteria), array('User.role_id' => $role_id,'User.store_id' => $storeID)), 'order' => array('User.created' => 'DESC'));
        $userdetail = $this->paginate('User');
        return $userdetail;
    }

    /*     * ***********************
     * Function name:userexcelListing()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function userexcelListing($storeID, $startDate = null, $endDate = null) {
        $criteria = "User.is_deleted=0 AND User.is_active=1 AND User.role_id=4";
        if (!empty($storeID)) {
            $criteria .= " AND User.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (User.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }
        $userdetail = $this->User->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC')));
        return $userdetail;
    }

    /*     * ***********************
     * Function name:orderProductListing()
      Description:graph order product list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductListing($storeID, $startDate = null, $endDate = null, $item = null) {
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'fields' => array('id', 'email', 'fname', 'lname', 'phone')
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id',
                    'fields' => array('id', 'name')
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id',
                    'fields' => array('id', 'name')
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id',
                    'fields' => array('id', 'address', 'email', 'city', 'state', 'zipcode', 'name_on_bell', 'phone')
                )
            )
                ), false
        );
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Order' => array('className' => 'Order', 'foreignKey' => 'order_id', 'fields' => array('id', 'store_id', 'order_number', 'seqment_id', 'order_status_id', 'user_id', 'amount', 'pickup_time', 'delivery_address_id', 'is_pre_order', 'created', 'coupon_discount')),
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id', 'fields' => array('id', 'name', 'category_id', 'description', 'units')),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id', 'fields' => array('id', 'size', 'category_id')))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Order->bindModel(
                array('belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name')
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id',
                    'fields' => array('id', 'order_id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'user_id', 'created')
                ),
            )
                ), false
        );
        if ($startDate && $endDate) {
            $conditions = array('Order.created >=' => $startDate, 'Order.created <=' => $endDate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        } else {
            $conditions = array('Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        }
        if (!empty($item)) {
            $conditions['OrderItem.item_id'] = $item;
        }
        //if (!empty($storeID)) {
            $conditions = array_merge(array($conditions), array('OrderItem.store_id' => $storeID));
       // }

        $this->paginate = array('fields' => array('id', 'order_id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'user_id', 'created'), 'recursive' => 3, 'conditions' => array($conditions), 'order' => array('Order.created' => 'DESC'), 'group' => array('OrderItem.order_id'));
        $orderdetail = $this->paginate('OrderItem');
        return $orderdetail;
    }

    public function fetchWeeklyOrderToday($storeId = null, $start = null, $end = null, $ordertype = null,$endYear=null) {
        $this->Order->bindModel(
                array(
                    'belongsTo' => array(
                        'Segment' => array(
                            'className' => 'Segment',
                            'foreignKey' => 'seqment_id'
                        )
                    )
                )
        );
        $conditions = "Order.is_future_order=0 AND Order.is_active=1 AND Order.is_deleted=0 AND WEEK(Order.created) >=WEEK('" . $start . "') AND WEEK(Order.created) <=WEEK('" . $end . "') AND YEAR(Order.created) ='" . $endYear . "'";
        if (!empty($storeId)) {
            //$conditions .=" AND Order.store_id=$storeId";
        }
        if ($ordertype) {
            $conditions .=" AND Segment.id=$ordertype";
        }
        $result = $this->Order->find('all', array('fields' => array('WEEK(Order.created) AS WEEKno', 'DATE(Order.created) AS order_date', '`amount`-`coupon_discount` AS total'), 'conditions' => array_merge(array($conditions), array('Order.store_id' => $storeId))));
        return $result;
    }

    public function fetchWeeklyUserToday($storeId = null, $start = null, $end = null,$endYear=null) {
        $conditions = " User.is_active=1 AND User.is_deleted=0 AND WEEK(User.created) >=WEEK('" . $start . "') AND WEEK(User.created) <=WEEK('" . $end . "') AND YEAR(User.created) ='" . $endYear . "' AND User.role_id IN (4,5)";
         
        $result = $this->User->find('all', array('group' => array('User.created'), 'fields' => array('WEEK(User.created) AS WEEKno', 'DATE(User.created) AS order_date', 'COUNT(User.id) as total'), 'conditions' => array_merge(array($conditions), array('User.store_id' => $storeId))));
        return $result;
    }

    public function orderListings($storeID = null, $startDate = null, $endDate = null, $ordertype = null) {
        $criteria = "Order.is_deleted=0 AND Order.is_future_order=0";
        if (!empty($storeID)) {
            $criteria = " AND Order.store_id =$storeID";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (Order.created BETWEEN '" . $stratdate . "' AND '" . $enddate . "')";
        }
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ), 'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
            )
                ), false
        );

        if ($ordertype) {
            $criteria.=" AND Segment.id=$ordertype";
        }
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));
        return $orderdetail;
    }

    /*     * ***************************
     * @Function name :productReport
     * @Descriptipn:Display the product report
     * @Author:smartdata
     * *************************** */

    public function productReport() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $store_id = '';
        $storeId = '';
        $this->layout = "hq_dashboard";
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $type = 1;
        $item = "";
        $startdate = date('Y-m-d');
        $enddate = date('Y-m-d');
        $Month = date('m');
        $Year = date('Y');
        $yearFrom = date('Y', strtotime('-1 year'));
        $yearTo = date('Y');
        $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
        $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        if (!empty($this->request->data['Merchant']['store_id']) && $this->request->data['Merchant']['store_id']!='All') {
            $storeId = $this->request->data['Merchant']['store_id'];
            $store_id = $storeId;
            $storeDate=$this->Common->getcurrentTime($storeId,1);
            $storeDateTime=  explode(" ", $storeDate);
            $storeTime=$storeDateTime[0];
            $storeTime=$storeDateTime[1];
            $this->set('storeTime',$storeTime);
            $sdate = $storeDate." "."00:00:00";
            $edate = $storeDate." "."23:59:59";
            $type = 1;
            $item = "";
            $startdate = $storeDate;
            $enddate = $storeDate;
            $expoladDate=  explode("-", $startdate);
            $Month = $expoladDate[1];
            $Year = $expoladDate[0];
            $yearFrom = date('Y', strtotime('-1 year',strtotime($Year)));
            $yearTo = $Year;
            $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
            $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
            
        }else{
            if(empty($this->request->data['Merchant']['store_id'])){
                $storeId='All';
            }else{
                $storeId = $this->request->data['Merchant']['store_id'];
            }
            
        }
        if (isset($this->params['named']) && isset($this->params['named']['store_id'])) {
            $store_id = $this->params['named']['store_id'];
            $storeId = $store_id;
            if (!empty($storeId) && ($storeId !== 'All')) {
                $storeDate = $this->Common->getcurrentTime($storeId, 1);
                $storeDateTime = explode(" ", $storeDate);
                $storeDate = $storeDateTime[0];
                $storeTime = $storeDateTime[1];
                $this->set('storeTime', $storeTime);
                $sdate = $storeDate . " " . "00:00:00";
                $edate = $storeDate . " " . "23:59:59";
                $type = 1;
                $item = "";
                $startdate = $storeDate;
                $enddate = $storeDate;
                $expoladDate = explode("-", $startdate);
                $Month = $expoladDate[1];
                $Year = $expoladDate[0];
                $yearFrom = date('Y', strtotime('-1 year', strtotime($Year)));
                $yearTo = $Year;
                $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
                $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
            }else{
                if(empty($this->request->data['Merchant']['store_id'])){
                    $storeId='All';
                }else{
                    $storeId = $this->request->data['Merchant']['store_id'];
                }
                
            }
        }
        if ($storeId == 'All') {
            $this->loadModel('Store');
            $storeId = $this->Store->find('list', array('fields' => array('Store.id'), 'conditions' => array('Store.merchant_id' => $merchantId, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
        }
        
        if (!empty($this->data) || !empty($this->params['named'])) {
            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;
            if ($type == 1) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $startdate = date('Y-m-d 00:00:00', strtotime($this->params['named']['startdate']));
                    $enddate = date('Y-m-d 00:00:00', strtotime($this->params['named']['enddate']));
                    if (!empty($this->params['named']['item'])) {
                        $item = $this->params['named']['item'];
                    } else {
                        $item = null;
                    }
                } else {
                    $startdate = $this->data['Report']['startdate'];
                    $enddate = $this->data['Report']['enddate'];
                    $item = $this->data['Item']['id'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
                }
                if (isset($this->request->params['named']) && isset($this->params['named']['sort'])) {
                    $startdate1 = $this->request->params['named']['startdate'];
                    $enddate1 = $this->request->params['named']['enddate'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate1));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate1));
                    if (!empty($this->request->params['named']['item'])) {
                        $item = $this->request->params['named']['item'];
                    } else {
                        $item = null;
                    }
                    $type = $this->request->params['named']['type'];
                }
                $orderlist = $this->itemListing($storeId, $startdate, $enddate, $item);
                $order = $this->orderProductListing($storeId, $startdate, $enddate, $item);
                $result = $orderlist;
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $this->request->data['Item']['id'] = $item;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'item' => $item);
                $this->set(compact('order', 'item', 'result', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
            } else if ($type == 2) {
                if (isset($this->params['named']) && isset($this->params['named']['date_start_from'])) {
                    $startFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_start_from']));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($this->params['named']['date_end_from']));
                    if (!empty($this->params['named']['item'])) {
                        $item = $this->params['named']['item'];
                    } else {
                        $item = "";
                    }
                    $weekyear = date('Y', strtotime($this->params['named']['date_start_from']));
                } else {
                    $startFrom = $this->data['Report']['date_start_from'];
                    $endFrom = $this->data['Report']['date_end_from'];
                    $weekyear = date('Y', strtotime($startFrom));
                    if (isset($this->data['Item']['id'])) {
                        $item = $this->data['Item']['id'];
                    }
                    $startFrom = date('Y-m-d 00:00:00', strtotime($startFrom));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($endFrom));
                }
                $expoladEndDate=  explode(" ", $endFrom);
                $endMonth = $expoladEndDate[1];
                $explodeEndYear = explode("-", $expoladEndDate[0]);
                $endYear=$explodeEndYear[0];
                $startweekNumber = date("W", strtotime($startFrom));
                $endWeekNumber = date("W", strtotime($endFrom));
                $data = array();
                $weeknumbers = '';
                $j = 0;
                for ($i = $startweekNumber; $i <= $endWeekNumber; $i++) {
                    $data[$i] = array();
                    if ($j == 0) {
                        $weeknumbers.="'Week" . $i . "'";
                    } else {
                        $weeknumbers.=",'Week" . $i . "'";
                    }
                    $j++;
                    $time = strtotime("1 January $weekyear", time());
                    $day = date('w', $time);
                    $time += ((7 * $i) - $day) * 24 * 3600;
                    $data[$i]['daywise'] = array();
                    for ($k = 0; $k <= 6; $k++) {
                        $time2 = $time + $k * 24 * 3600;
                        $data[$i]['daywise'][date('Y-m-d', $time2)] = array(0);
                        if ($k == 0) {
                            $datestring = "'" . date('Y-m-d', $time2) . "'";
                        } else {
                            $datestring.=",'" . date('Y-m-d', $time2) . "'";
                        }
                        $data[$i]['datestring'] = $datestring;
                    }
                }
                $result1 = $this->fetchWeeklyProductToday($storeId, $startFrom, $endFrom, $item,$endYear);
                $weekarray = array();
                $datearray = array();
                foreach ($result1 as $k => $result) {
                    if (in_array($result[0]['WEEKno'], $weekarray)) {
                        $data[$result[0]['WEEKno']]['week'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['totalamount'] += $result['OrderItem']['quantity'];
                    } else {
                        $weekarray[$result[0]['WEEKno']] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['totalamount'] = $result['OrderItem']['quantity'];
                    }
                    if (in_array($result[0]['order_date'], $datearray)) {
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['item_count'] += $result['OrderItem']['quantity'];
                    } else {
                        $datearray[$result[0]['order_date']] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                        $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['item_count'] = $result['OrderItem']['quantity'];
                    }
                }

                $order = $this->orderProductListingweek($storeId, $startFrom, $endFrom, $item,$endYear);
                $startFrom = $this->Dateform->formatDate($startFrom);
                $endFrom = $this->Dateform->formatDate($endFrom);
                $startdate = $startFrom;
                $enddate = $endFrom;
                $paginationdata = array('store_id' => $store_id, 'date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2);
                $this->set(compact('paginationdata', 'order', 'weeknumbers', 'data', 'date', 'type', 'startFrom', 'endFrom', 'Month', 'Year', 'yearFrom', 'yearTo', 'startdate', 'enddate'));
            } else if ($type == 3) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    if (!empty($this->params['named']['item'])) {
                        $item = $this->params['named']['item'];
                    } else {
                        $item = null;
                    }
                } else {
                    $Year = $this->data['Report']['year'];
                    $Month = $this->data['Report']['month'];
                    $dateFrom = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-01';
                    $dateTo = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-31';
                    $item = $this->data['Item']['id'];
                }
                $orderlist = $this->itemListing($storeId, $dateFrom, $dateTo, $item);
                $order = $this->orderProductListing($storeId, $dateFrom, $dateTo, $item);
                $result = $orderlist;
                $this->request->data['Item']['id'] = $item;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3);
                $this->set(compact('item', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 4) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    if (!empty($this->params['named']['item'])) {
                        $item = $this->params['named']['item'];
                    } else {
                        $item = null;
                    }
                } else {
                    $yearFrom = $this->data['Report']['from_year'];
                    $yearTo = $this->data['Report']['to_year'];
                    $dateFrom = $yearFrom . '-' . '01' . '-01';
                    $dateTo = $yearTo . '-' . '12' . '-31';
                    $item = $this->data['Item']['id'];
                }
                $orderlist = $this->itemListing($storeId, $dateFrom, $dateTo, $item);
                $order = $this->orderProductListing($storeId, $dateFrom, $dateTo, $item);
                $result = $orderlist;
                $this->request->data['Item']['id'] = $item;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4);
                $this->set(compact('item', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 5) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    if (!empty($this->params['named']['item'])) {
                        $item = $this->params['named']['item'];
                    } else {
                        $item = null;
                    }
                } else {
                    $dateFrom = $startdate;
                    $dateTo = $enddate;
                    $item = $this->data['Item']['id'];
                }
                $orderlist = $this->itemListing($storeId, null, null, $item);
                $order = $this->orderProductListing($storeId, null, null, $item);
                $result = $orderlist;
                $this->request->data['Item']['id'] = $item;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5);
                $this->set(compact('item', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {
            if(!empty($storeId)){
                $item = '';
                $orderlist = $this->itemListings($storeId, $sdate, $edate, $item);
                $order = $this->orderProductListings($storeId, $sdate, $edate, $item);
                $result = $orderlist;
                $paginationdata = array('store_id' => $store_id, 'startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'item' => null);
                $this->set(compact('order', 'result', 'paginationdata'));
            }
        }
          
            $this->loadModel('Item');
            $itemList = $this->Item->getallItemsByStore($storeId);
            $this->set('categoryList', $itemList);
          
        $this->set(compact('store_id', 'item', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
    }

    /*     * ***********************
     * Function name:orderProductListings()
      Description:graph order product list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductListings($storeID = null, $startDate = null, $endDate = null, $item = null) {
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'fields' => array('id', 'email', 'fname', 'lname', 'phone')
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id',
                    'fields' => array('id', 'name')
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id',
                    'fields' => array('id', 'name')
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id',
                    'fields' => array('id', 'address', 'email', 'city', 'state', 'zipcode', 'name_on_bell', 'phone')
                ),
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name')
                )
            )
                ), false
        );
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Order' => array('className' => 'Order', 'foreignKey' => 'order_id', 'fields' => array('id', 'store_id', 'order_number', 'seqment_id', 'order_status_id', 'user_id', 'amount', 'pickup_time', 'delivery_address_id', 'is_pre_order', 'created', 'coupon_discount')),
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id', 'fields' => array('id', 'name', 'category_id', 'description', 'units')),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id', 'fields' => array('id', 'size', 'category_id')))), false);
        $this->Order->bindModel(
                array(
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id',
                    'fields' => array('id', 'order_id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'user_id', 'created')
                ),
            )
                ), false
        );
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $stratdate = date('Y-m-d 00:00:00', strtotime($stratdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $conditions = array('Order.created >=' => $stratdate, 'Order.created <=' => $enddate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        } else {
            $conditions = array('Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        }
        //if (!empty($storeID)) {
            $conditions['Order.store_id'] = $storeID;
        //}

        if (!empty($item)) {
            $conditions['OrderItem.item_id'] = $item;
        }
        $this->paginate = array('fields' => array('id', 'order_id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'user_id', 'created'), 'recursive' => 3, 'conditions' => array($conditions), 'order' => array('Order.created' => 'DESC'), 'group' => array('OrderItem.order_id'));
        $orderdetail = $this->paginate('OrderItem');
        return $orderdetail;
    }

    /*     * ***********************
     * Function name:itemListing()
      Description:graph item list
      created:06/10/2015
     *
     * ********************* */

    public function itemListings($storeID = null, $startDate = null, $endDate = null, $itemId = null) {
        $criteria = "OrderItem.is_deleted=0 AND OrderItem.is_active=1";
        if ($startDate && $endDate) {
            $startDate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (OrderItem.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }
        if ($itemId) {
            $criteria.=" AND OrderItem.item_id=$itemId";
        }
         //if (!empty($storeID)) {
            $conditions = array_merge(array($criteria), array('OrderItem.store_id' => $storeID));
        //}
        $orderdetail = $this->OrderItem->find('all', array('fields' => array('DATE(created) AS order_date', 'Count(OrderItem.created) AS number'), 'group' => array("DATE_FORMAT(OrderItem.created, '%Y-%m-%d')"), 'conditions' => array($criteria), 'order' => array('OrderItem.created' => 'DESC')));
        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderProductListingweek()
      Description:graph order product list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductListingweek($storeID = null, $startDate = null, $endDate = null, $item = null,$endYear=null) {
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'fields' => array('id', 'email', 'fname', 'lname', 'phone')
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id',
                    'fields' => array('id', 'name')
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id',
                    'fields' => array('id', 'name')
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id',
                    'fields' => array('id', 'address', 'email', 'city', 'state', 'zipcode', 'name_on_bell', 'phone')
                ),
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name')
                )
            )
                ), false
        );
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Order' => array('className' => 'Order', 'foreignKey' => 'order_id', 'fields' => array('id', 'store_id', 'order_number', 'seqment_id', 'order_status_id', 'user_id', 'amount', 'pickup_time', 'delivery_address_id', 'is_pre_order', 'created', 'coupon_discount')),
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id', 'fields' => array('id', 'name', 'category_id', 'description', 'units')),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id', 'fields' => array('id', 'size', 'category_id')))), false);
        $this->Order->bindModel(
                array(
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id',
                    'fields' => array('id', 'order_id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'user_id', 'created')
                ),
            )
                ), false
        );
        if ($startDate && $endDate) {
            $startdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $conditions = " Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0 AND WEEK(Order.created) >=WEEK('" . $startdate . "') AND WEEK(Order.created) <=WEEK('" . $enddate . "') AND YEAR(Order.created) ='" . $endYear . "'";
        } else {
            $conditions = array('Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        }
        if (!empty($item)) {
            $conditions .=" AND OrderItem.item_id =$item";
        }
        //if (!empty($storeID)) {
            $conditions = array_merge(array($conditions), array('Order.store_id' => $storeID));
        //}
        $this->paginate = array('fields' => array('id', 'order_id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'user_id', 'created'), 'recursive' => 3, 'conditions' => array($conditions), 'order' => array('Order.created' => 'DESC'), 'group' => array('OrderItem.order_id'));
        $orderdetail = $this->paginate('OrderItem');
        return $orderdetail;
    }

    public function productReportDownload($storeId = null, $type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $item = null) {
        if (empty($storeId)) {
            $this->Session->setFlash(__("Please select store."));
           $this->redirect(array('controller' => 'hqreports', 'action' => 'productReport'));
        }
        if ($storeId=='All') {
            $this->Session->setFlash(__("Please select store."));
           $this->redirect(array('controller' => 'hqreports', 'action' => 'productReport'));
        }
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderProductexcelListing($storeId, $startdate, $enddate);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderProductexcelListingweek($storeId, $startdate, $enddate);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->orderProductexcelListing($storeId, $dateFrom, $dateTo);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->orderProductexcelListing($storeId, $dateFrom, $dateTo);
            $text = 'Yearly_Report';
        } else {
            $order = $this->orderProductexcelListing($storeId, null, null);
            $text = 'LifeTime_Report';
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
        $filename = $text . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('ProductReport');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Order No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Customer Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Items');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Amount($)');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Phone');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Address');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Email');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Order Type');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Created');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
        $i = 2;
        foreach ($order as $key => $data) {
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Order']['order_number']);
            if ($data['DeliveryAddress']['name_on_bell']) {
                $name = $data['DeliveryAddress']['name_on_bell'];
            } else {
                $name = $data['User']['fname'] . " " . $data['User']['lname'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $name);
            $j = 0;
            $items = "";
            foreach ($data['OrderItem'] as $key => $item) {
                if ($j == 0) {
                    $items = $item['Item']['name'];
                } else {
                    $items.=", " . $item['Item']['name'];
                }
                $j++;
            }
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $items);
            if ($data['Order']['coupon_discount'] > 0) {
                $total_amount = $data['Order']['amount'] - $data['Order']['coupon_discount'];
            } else {
                $total_amount = $data['Order']['amount'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $total_amount);
            if (!empty($data['DeliveryAddress']['phone'])) {
                $phone = $data['DeliveryAddress']['phone'];
            } else {
                $phone = $data['User']['phone'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $phone);
            if (!empty($data['DeliveryAddress']['address'])) {
                $address = $data['DeliveryAddress']['address'];
            } else {
                $address = $data['User']['address'];
            }
            if ($data['Segment']['id'] == 2) {
                $address = $data['Segment']['name'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", $address);
            if (!empty($data['DeliveryAddress']['email'])) {
                $email = $data['DeliveryAddress']['email'];
            } else {
                $email = $data['User']['email'];
            }
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", $email);
            $objPHPExcel->getActiveSheet()->setCellValue("H$i", $data['Segment']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", $data['Order']['created']);
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /*     * ***********************
     * Function name:orderProductexcelListingweek()
      Description:graph order product excel list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductexcelListingweek($storeID = null, $startDate = null, $endDate = null) {
        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        //if (!empty($storeID)) {
            $criteria .= " AND Order.store_id =$storeID";
        //}
        if ($startDate && $endDate) {
            $startdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND WEEK(Order.created) >=WEEK('" . $startdate . "') AND WEEK(Order.created) <=WEEK('" . $enddate . "')";
        }
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
            )
                ), false
        );
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));
        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderProductexcelListing()
      Description:graph order product excel list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductexcelListing($storeID = null, $startDate = null, $endDate = null) {
        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        //if (!empty($storeID)) {
            $criteria .= " AND Order.store_id =$storeID";
        //}
        if ($startDate && $endDate) {
            $startdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (Order.created BETWEEN '" . $startdate . "' AND '" . $enddate . "')";
        }
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
            )
                ), false
        );
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));
        return $orderdetail;
    }

    public function fetchWeeklyProductToday($storeId = null, $start = null, $end = null, $item = null,$endYear=null) {
        $this->OrderItem->bindModel(array('belongsTo' => array('Order')));
        $conditions = " Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0  AND WEEK(Order.created) >=WEEK('" . $start . "') AND WEEK(Order.created) <=WEEK('" . $end . "') AND YEAR(Order.created) ='" . $endYear . "'";
        if (!empty($item)) {
            $conditions .=" AND OrderItem.item_id =$item";
        }
        //if (!empty($storeId)) {
            $conditions = array_merge(array($conditions), array('Order.store_id' => $storeId));
        //}
        $result = $this->OrderItem->find('all', array('fields' => array('WEEK(Order.created) AS WEEKno', 'DATE(Order.created) AS order_date', 'OrderItem.quantity'), 'conditions' => $conditions));
        return $result;
    }

    /* ------------------------------------------------
      Function name: orderDetail()
      Description: Dispaly the detail of perticular order
      created:12/8/2015
      ----------------------------------------------------- */

    public function orderDetail($order_id = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->loadModel('OrderItemFree');
        $this->loadModel('Item');
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $orderId = $this->Encryption->decode($order_id);
        $this->OrderItemFree->bindModel(array('belongsTo' => array('Item' => array('fields' =>array('id', 'name','category_id')))), false);
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name','category_id')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->Item->bindModel(array('belongsTo' => array('category' => array('fields' =>
                    array('id', 'name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name','category_id')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(
                    array(
                        'hasMany' => array(
                            'OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id'), 'order' => array('OrderTopping.id')), 
                            'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 
                            'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id', 'size'))
                        ), 
                        'belongsTo' => array(
                            'Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name','category_id')), 
                            'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 
                            'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
        $this->Order->bindModel(
                    array(
                        'hasMany' => array(
                            'OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'total_item_price', 'tax_price')),
                            'OrderItemFree' => array('foreignKey' => 'order_id', 'fields' => array('id', 'item_id', 'order_id', 'free_quantity', 'price'))
                        ), 
                        'belongsTo' => array(
                            'User' => array('className' => 'User', 'foreignKey' => 'user_id'),
                            'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'), 
                            'DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 
                            'OrderStatus' => array('fields' => array('name')),
                            'OrderPayment' => array(
                                'className' => 'OrderPayment',
                                'foreignKey' => 'payment_id',
                                'fields' => array('id', 'transection_id', 'amount', 'payment_gateway', 'payment_status', 'last_digit'),
                            )
                        )
                    ), false);
        $orderDetails = $this->Order->getOrderInfo($orderId);
        $this->set('orderDetail', $orderDetails);
        
        
        $savedStatus=array();
        if($orderDetails['Order']['seqment_id'] == 2){
            if(!empty($storeSetting['StoreSetting']['pickup_status'])){
                $savedStatus=explode(',',$storeSetting['StoreSetting']['pickup_status']);
            }
        }
        
        if($orderDetails['Order']['seqment_id'] == 3){
            if(!empty($storeSetting['StoreSetting']['delivery_status'])){
                $savedStatus=explode(',',$storeSetting['StoreSetting']['delivery_status']);
            }
        }
        
        
        
        
        $this->set(compact('savedStatus'));
        
        $this->loadModel('OrderStatus');
        $statusList = $this->OrderStatus->OrderStatusList();
        $this->set('statusList', $statusList);
    }

    /* ------------------------------------------------
      Function name:orderHistory()
      Description:Display the customer all orders
      created:18/8/2015
      ----------------------------------------------------- */

    public function orderHistory($EncryptCustomerID = null, $store_id = null, $start = null, $end = null, $type = null, $page = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $storeId = $this->Session->read('selectedStoreId');
        $merchantId = $this->Session->read('merchantId');
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('OrderOffer');
        $this->loadModel('Order');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderItem');
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('name')))), false);
        $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'total_item_price', 'tax_price'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'User' => array('fields' => array('fname', 'lname', 'email', 'phone', 'country', 'city', 'state', 'address')), 'OrderStatus' => array('fields' => array('name')), 'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'))), false);
        $orderDetails = $this->Order->getUserOrderDetail($merchantId, $storeId, $userId);
        if (!empty($orderDetails)) {
            $this->set('orderDetail', $orderDetails);
        } else {
            $this->Session->setFlash(__('Record not Found.'), 'alert_failed');
            if ($type == 2) {
                $startparam = "date_start_from:" . $start;
                $endparam = "date_end_from:" . $end;
                $typeparam = "type:" . $type;
                $pageparam = "page:" . $page;
            } else {
                $startparam = "startdate:" . $start;
                $endparam = "enddate:" . $end;
                $typeparam = "type:" . $type;
                $pageparam = "page:" . $page;
            }
            $this->redirect(array('controller' => 'hqreports', 'action' => 'customerReport', $startparam, $endparam, $typeparam, $pageparam));
        }
        $this->loadModel('StoreReview');
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'fields' => array('name')))), false);
        $this->StoreReview->bindModel(array('belongsTo' => array('OrderItem' => array('className' => 'OrderItem'))), false);
        $myReviews = $this->StoreReview->getReviews($storeId, $userId);
        if (!empty($myReviews)) {
            $this->set('myReviews', $myReviews);
        }
        $this->loadModel('Booking');
        $this->Booking->bindModel(array('belongsTo' => array('BookingStatus')), false);
        $myBookings = $this->Booking->getBookingDetails($storeId, $userId);
        if (!empty($myBookings)) {
            $this->set('myBookings', $myBookings);
        }
    }

    public function ajaxRequest($id = '') {
        $this->autoRender = false;
        $this->loadModel('OrderStatus');
        $this->layout = "hq_dashboard";
        if (!empty($this->request->params['requested'])) {
            $data = $this->OrderStatus->find('first', array('conditions' => array('OrderStatus.id' => $id)));
            echo $data['OrderStatus']['name'];
        }
    }
    
    public function getcurrentStoreTime() {
        $this->autoRender = false;
        $storeId=$this->request->data['storeId'];
        if($storeId=='All'){
            $storeDate= $sdate = date('Y-m-d ');;
        }else{
            $storeDate=$this->Common->getcurrentTime($storeId,2);
        }
        
        return $storeDate;
        die;
        
    }
    
     /* ------------------------------------------------
      Function name:imageGallary()
      Description:Upload Gallery Images 
      created:21/04/2017
      ----------------------------------------------------- */
    
    public function imageGallary() {

        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $user_id = AuthComponent::User('id');
        $this->loadModel('MerchantImage');
        $encrypted_merchantId = $this->Encryption->decode($merchantId);
        $merchantGalleryImages = $this->MerchantImage->find('all', array('conditions' => array('MerchantImage.merchant_id' => $merchantId, 'MerchantImage.is_deleted' => 0)));
        $this->set(compact('encrypted_merchantId', 'user_id', 'merchantGalleryImages'));

        if ($this->data) {
            $data = $this->data;
            if (!empty($data['MerchantImage']) && $data['MerchantImage']['image'][0]['error'] == 0) {
                $response = $this->Common->checkImageExtensionAndSize($data['MerchantImage']);
                if (empty($response['status']) && !empty($response['errmsg'])) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect($this->referer());
                }
            }
            $data['MerchantImage']['merchant_id'] = $merchantId;
            if (!empty($data['MerchantImage']) && $data['MerchantImage']['image'][0]['error'] == 0) {
                $this->_uploadMerchantImages($data);
                $this->Session->setFlash(__('Upload Successfully.'), 'alert_success');
            }
            $this->redirect(array('controller' => 'hqreports', 'action' => 'imageGallary'));
        }
    }
     /* ------------------------------------------------
      Function name:imageGallary()
      Description:Upload Gallery Images 
      created:21/04/2017
      ----------------------------------------------------- */

    private function _uploadMerchantImages($data = null) {
        if (!empty($data)) {
            $this->loadModel('MerchantImage');
            //prx($data['MerchantImage']['image']);
            foreach ($data['MerchantImage']['image'] as $image) {
                if ($image['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($image, '/storeReviewImage/', $data['MerchantImage']['merchant_id'], 300, 190);
                } elseif ($image['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if ($response['imagename']) {
                    $imageData['image'] = $response['imagename'];
                    $imageData['merchant_id'] = $data['MerchantImage']['merchant_id'];
                    $imageData['created'] = date("Y-m-d H:i:s");
                    $imageData['is_active'] = 1;
                    $imageData['store_review_id'] = 0;

                    $this->MerchantImage->saveMerchantImage($imageData);
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name:activateGallaryImage()
      Description:Active/deactive GallaryImage
      created:21/04/2017
      ----------------------------------------------------- */

    public function activateGallaryImage($EncrypteditemID = null, $status = 0) {
        $this->autoRender = false;
         $this->layout = "hq_dashboard";
        $this->loadModel('MerchantImage');
        $data['MerchantImage']['id'] = $this->Encryption->decode($EncrypteditemID);
        $data['MerchantImage']['is_active'] = $status;
        if ($this->MerchantImage->save($data)) {
            if ($status) {
                $SuccessMsg = "Image Activated";
            } else {
                $SuccessMsg = "Image Deactivated";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqreports', 'action' => 'imageGallary'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqreports', 'action' => 'imageGallary'));
        }
    }

     /* ------------------------------------------------
      Function name:deleteGallaryImage()
      Description:delete GallaryImage
      created:21/04/2017
      ----------------------------------------------------- */

    public function deleteGallaryImage($EncryptGallaryImageID = null) {
        $this->autoRender = false;
         $this->layout = "hq_dashboard";
        $this->loadModel('MerchantImage');
        $data['MerchantImage']['id'] = $this->Encryption->decode($EncryptGallaryImageID);
        $data['MerchantImage']['is_deleted'] = 1;
        if ($this->MerchantImage->save($data)) {
            $this->Session->setFlash(__("Image deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqreports', 'action' => 'imageGallary'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqreports', 'action' => 'imageGallary'));
        }
    }

}
