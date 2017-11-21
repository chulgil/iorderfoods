<?php

App::uses('SupersAppController', 'Controller');

class SuperreportsController extends SupersAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption');
    public $uses = array('Store', 'OrderPayment', 'Order', 'User', 'OrderItem', 'Segment', 'OrderOffer', 'OrderTopping', 'OrderPreference');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function moneyReport($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $storeId = '';
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
        // $storeId = 0;

        if (!empty($this->data) || !empty($this->params['named'])) {

            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;

            //   pr($this->params['named']);
            //pr($this->data);
            //die;
            if (!empty($this->request->data['Store']['id'])) {
                $storeId = $this->request->data['Store']['id'];
                if (!empty($storeId) && $storeId != 'All') {
                    $storeDate = $this->Common->getcurrentTime($storeId, 1);
                    $storeDateTime = explode(" ", $storeDate);
                    $storeDate = $storeDateTime[0];
                    $storeTime = $storeDateTime[1];
                    $this->set('storeTime', $storeTime);
                    $sdate = $storeDate . " " . "00:00:00";
                    $edate = $storeDate . " " . "23:59:59";
                    $type = $type;
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
                }
            } else {
                $storeId = null;
            }
            //  echo $type;die;
            if ($type == 1) {
                //  $ordertype=2;

                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $startdate = date('Y-m-d 00:00:00', strtotime($this->params['named']['startdate']));
                    $enddate = date('Y-m-d 23:59:59', strtotime($this->params['named']['enddate']));
                    $ordertype = $this->params['named']['ordertype'];
                    if (isset($this->params['named']['storeId'])) {
                        $storeId = $this->params['named']['storeId'];
                    }
                } else {
                    $startdate = $this->data['Report']['startdate'];
                    $enddate = $this->data['Report']['enddate'];
                    $ordertype = $this->data['Segment']['id'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
                }
                if (isset($this->request->params['named']) && isset($this->params['named']['sort'])) {

                        $sort = $this->request->params['named']['sort'];
                        $startdate1 = $this->request->params['named']['startdate'];
                        $enddate1 = $this->request->params['named']['enddate'];
                        $startdate = date('Y-m-d 00:00:00', strtotime($startdate1));
                        $enddate = date('Y-m-d 23:59:59', strtotime($enddate1));
                        $ordertype = $this->request->params['named']['ordertype'];
                        $type = $this->request->params['named']['type'];
                    }

                $order = $this->orderListing($startdate, $enddate, $ordertype, $storeId);
                $graphorder = $this->ordergraphListing($startdate, $enddate, $ordertype, $storeId);
                $result = $graphorder;
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $this->request->data['Segment']['id'] = $ordertype;
                $paginationdata = array('startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'ordertype' => $ordertype, 'storeId' => $storeId);
                $this->set(compact('storeId', 'ordertype', 'order', 'result', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
            } else if ($type == 2) {

                if (isset($this->params['named']) && isset($this->params['named']['date_start_from'])) {
                    $startFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_start_from']));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($this->params['named']['date_end_from']));
                    $ordertype = $this->params['named']['ordertype'];
                    $storeId = $this->params['named']['storeId'];

                    $weekyear = date('Y', strtotime($this->params['named']['date_start_from']));
                } else {
                    $startFrom = $this->data['Report']['date_start_from'];
                    $endFrom = $this->data['Report']['date_end_from'];
                    $weekyear = date('Y', strtotime($startFrom));
                    $ordertype = $this->data['Segment']['id'];
                    $startFrom = date('Y-m-d 00:00:00', strtotime($startFrom));
                    $endFrom = date('Y-m-d 23:59:59', strtotime($endFrom));
                }
                $expoladEndDate = explode(" ", $endFrom);
                $endMonth = $expoladEndDate[1];
                $explodeEndYear = explode("-", $expoladEndDate[0]);
                $endYear = $explodeEndYear[0];
                $startweekNumber = date("W", strtotime($startFrom));
                $endWeekNumber = date("W", strtotime($endFrom));
                $data = array();
                $return = array();
                $weeknumbers = '';
                $j = 0;
                for ($i = $startweekNumber; $i <= $endWeekNumber; $i++) {
                    $data[$i] = array();
                    if ($j == 0) {
                        $weeknumbers .= "'Week" . $i . "'";
                    } else {
                        $weeknumbers .= ",'Week" . $i . "'";
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
                            $datestring .= ",'" . date('Y-m-d', $time2) . "'";
                        }
                        $data[$i]['datestring'] = $datestring;
                    }
                }
                $result1 = $this->fetchWeeklyOrderToday($storeId, $startFrom, $endFrom, $ordertype, $endYear);

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
                $order = $this->orderListingweek($startFrom, $endFrom, $ordertype, $storeId, $endYear);
                // echo '<pre>';print_r($order);die;

                $startFrom = $this->Dateform->formatDate($startFrom);
                $endFrom = $this->Dateform->formatDate($endFrom);
                $startdate = $startFrom;
                $enddate = $endFrom;
                $paginationdata = array('date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2, 'ordertype' => $ordertype, 'storeId' => $storeId);
                $this->set(compact('storeId', 'paginationdata', 'order', 'weeknumbers', 'data', 'date', 'type', 'startFrom', 'endFrom', 'Month', 'Year', 'yearFrom', 'yearTo', 'startdate', 'enddate'));
            } else if ($type == 3) {

                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    $ordertype = $this->params['named']['ordertype'];
                    $storeId = $this->params['named']['storeId'];
                } else {
                    $Year = $this->data['Report']['year'];
                    $Month = $this->data['Report']['month'];
                    $dateFrom = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-01';
                    $dateTo = $this->data['Report']['year'] . '-' . $this->data['Report']['month'] . '-31';
                    $ordertype = $this->data['Segment']['id'];
                }
                $order = $this->orderListing($dateFrom, $dateTo, $ordertype, $storeId);
                $graphorder = $this->ordergraphListing($dateFrom, $dateTo, $ordertype, $storeId);

                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'ordertype' => $ordertype, 'storeId' => $storeId);

                $this->set(compact('storeId', 'ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 4) {

                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    $ordertype = $this->params['named']['ordertype'];
                    $storeId = $this->params['named']['storeId'];
                } else {
                    $yearFrom = $this->data['Report']['from_year'];
                    $yearTo = $this->data['Report']['to_year'];
                    $dateFrom = $yearFrom . '-' . '01' . '-01';
                    $dateTo = $yearTo . '-' . '12' . '-31';
                    $ordertype = $this->data['Segment']['id'];
                }

                $order = $this->orderListing($dateFrom, $dateTo, $ordertype, $storeId);
                $graphorder = $this->ordergraphListing($dateFrom, $dateTo, $ordertype, $storeId);

                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'ordertype' => $ordertype, 'storeId' => $storeId);

                $this->set(compact('storeId', 'ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            } else if ($type == 5) {

                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $dateFrom = $this->params['named']['startdate'];
                    $dateTo = $this->params['named']['enddate'];
                    $ordertype = $this->params['named']['ordertype'];
                    $storeId = $this->params['named']['storeId'];
                } else {
                    $dateFrom = $startdate;
                    $dateTo = $enddate;
                    $ordertype = $this->data['Segment']['id'];
                }

                $order = $this->orderListing(null, null, $ordertype, $storeId);
                $graphorder = $this->ordergraphListing(null, null, $ordertype, $storeId);

                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'ordertype' => $ordertype, 'storeId' => $storeId);
                $this->set(compact('storeId', 'ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {

            $order = $this->orderListing($sdate, $edate, $ordertype, $storeId = null);
            $graphorder = $this->ordergraphListing($sdate, $edate, $ordertype, $storeId = null);

            $result = $graphorder;
            $paginationdata = array('startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'ordertype' => $ordertype);
            $this->set(compact('order', 'result', 'paginationdata'));
        }

        $typeList = $this->Segment->OrderTypeList($storeId);

        $this->set('typeList', $typeList);
        $this->set(compact('ordertype', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo'));
    }

    public function moneyReportDownload($storeId = null, $type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $ordertype = null) {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $storeId = $storeId;

        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            //  echo $startdate ;echo '<br>';echo $enddate;echo '<br>';echo $ordertype;die;

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
                    $items .= ", " . $item['Item']['name'];
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

    public function moneysecondReportDownload($type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $ordertype = null) {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');

        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            //  echo $startdate ;echo '<br>';echo $enddate;echo '<br>';echo $ordertype;die;

            $order = $this->superorderListg($startdate, $enddate, $ordertype);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->superweekorderListg($startdate, $enddate, $ordertype);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->superorderListg($dateFrom, $dateTo, $ordertype);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->superorderListg($dateFrom, $dateTo, $ordertype);
            $text = 'Yearly_Report';
        } else if ($type == 5) {
            $order = $this->superorderListg(null, null, $ordertype);
            $text = 'LifeTime_Report';
        }


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
                    $items .= ", " . $item['Item']['name'];
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

    public function orderReportDownload($storeId = null, $type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $ordertype = null) {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $storeId = $storeId;
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
            // echo $sdate;echo '<br>';echo $edate;echo '<br>';echo  $ordertype;die;
            $order = $this->orderListg($storeId, null, null, $ordertype);
            $text = 'LifeTime_Report';
        }
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
                    $items .= ", " . $item['Item']['name'];
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

    public function superorderReportDownload($type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $ordertype = null) {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->supeorderListg($startdate, $enddate, $ordertype);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->supeorderListgweek($startdate, $enddate, $ordertype);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->supeorderListg($dateFrom, $dateTo, $ordertype);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->supeorderListg($dateFrom, $dateTo, $ordertype);
            $text = 'Yearly_Report';
        } else {
            // echo $sdate;echo '<br>';echo $edate;echo '<br>';echo  $ordertype;die;
            $order = $this->supeorderListg(null, null, $ordertype);
            $text = 'LifeTime_Report';
        }
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
                    $items .= ", " . $item['Item']['name'];
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
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
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
            // echo $sdate;echo '<br>';echo $edate;echo '<br>';echo  $ordertype;die;
            $userdata = $this->userexcelListing($storeId, null, null);
            $text = 'LifeTime_Report';
        }



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
        foreach ($userdata as $key => $data) {

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

    public function weekuserexcelListing($storeId = null, $startDate = null, $endDate = null) {
        if (!empty($storeId)) {
            $criteria = "User.store_id =$storeId AND User.is_deleted=0 AND User.is_active=1 AND User.role_id=4";
        } else {
            $criteria = "User.is_deleted=0 AND User.is_active=1 AND User.role_id=4";
        }

        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            //$criteria.= " AND (User.created BETWEEN '".$startDate."' AND '".$endDate."')";
            $criteria .= " AND WEEK(User.created) >=WEEK('" . $startDate . "') AND WEEK(User.created) <=WEEK('" . $endDate . "')";
        }

        $userdetail = $this->User->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC')));

        return $userdetail;
    }

    public function supecustomerReportDownload($type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null) {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $userdata = $this->supeuserexcelListing($startdate, $enddate);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $userdata = $this->weekuserexcelListing(null, $startdate, $enddate);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $userdata = $this->supeuserexcelListing($dateFrom, $dateTo);

            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $userdata = $this->supeuserexcelListing($dateFrom, $dateTo);
            $text = 'Yearly_Report';
        } else {
            // echo $sdate;echo '<br>';echo $edate;echo '<br>';echo  $ordertype;die;
            $userdata = $this->supeuserexcelListing(null, null);
            $text = 'LifeTime_Report';
        }


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
        foreach ($userdata as $key => $data) {

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

    public function orderReport($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }

        $this->layout = "super_dashboard";
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $type = 1;
        $storeId = '';
        $ordertype = "";
        $startdate = date('Y-m-d');
        $enddate = date('Y-m-d');
        $Month = date('m');
        $Year = date('Y');
        $yearFrom = date('Y', strtotime('-1 year'));
        $yearTo = date('Y');
        $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
        $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        if (!empty($this->data) || !empty($this->params['named'])) {
            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;

            if (!empty($this->request->data['Store']['id'])) {
                $this->Session->write('ResultSearchData', json_encode($this->request->data));
                $storeId = $this->request->data['Store']['id'];

                if (!empty($storeId) && $storeId != 'All') {
                    $storeDate = $this->Common->getcurrentTime($storeId, 1);
                    $storeDateTime = explode(" ", $storeDate);
                    $storeDate = $storeDateTime[0];
                    $storeTime = $storeDateTime[1];
                    $this->set('storeTime', $storeTime);
                    $sdate = $storeDate . " " . "00:00:00";
                    $edate = $storeDate . " " . "23:59:59";
                    $type = $type;
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
                }
            } else {
                $storeId = null;
            }
            if ($type == 1) {
                //  $ordertype=2;
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $startdate = date('Y-m-d 00:00:00', strtotime($this->params['named']['startdate']));
                    $enddate = date('Y-m-d 00:00:00', strtotime($this->params['named']['enddate']));
                    $ordertype = $this->params['named']['ordertype'];
                } else {
                    $startdate = $this->data['Report']['startdate'];
                    $enddate = $this->data['Report']['enddate'];
                    $ordertype = $this->data['Segment']['id'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
                }

                if (isset($this->request->params['named']) && isset($this->params['named']['sort'])) {

                    $sort = $this->request->params['named']['sort'];
                    $startdate1 = $this->request->params['named']['startdate'];
                    $enddate1 = $this->request->params['named']['enddate'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate1));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate1));
                    $ordertype = $this->request->params['named']['ordertype'];
                    $type = $this->request->params['named']['type'];
                }

                $order = $this->orderListing($startdate, $enddate, $ordertype, $storeId);
                $graphorder = $this->ordergraphListing($startdate, $enddate, $ordertype, $storeId);

                $result = $graphorder;
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $this->request->data['Segment']['id'] = $ordertype;
                $paginationdata = array('startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'ordertype' => $ordertype);
                $this->set(compact('storeId', 'ordertype', 'order', 'result', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
            } else if ($type == 2) {

                if (isset($this->params['named']) && isset($this->params['named']['date_start_from'])) {
                    $startFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_start_from']));
                    $endFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_end_from']));
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
                $expoladEndDate = explode(" ", $endFrom);
                $endMonth = $expoladEndDate[1];
                $explodeEndYear = explode("-", $expoladEndDate[0]);
                $endYear = $explodeEndYear[0];
                $startweekNumber = date("W", strtotime($startFrom));
                $endWeekNumber = date("W", strtotime($endFrom));
                $data = array();
                $return = array();
                $weeknumbers = '';
                $j = 0;
                for ($i = $startweekNumber; $i <= $endWeekNumber; $i++) {
                    $data[$i] = array();
                    if ($j == 0) {
                        $weeknumbers .= "'Week" . $i . "'";
                    } else {
                        $weeknumbers .= ",'Week" . $i . "'";
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
                            $datestring .= ",'" . date('Y-m-d', $time2) . "'";
                        }
                        $data[$i]['datestring'] = $datestring;
                    }
                }

                $result1 = $this->fetchWeeklyOrderToday($storeId, $startFrom, $endFrom, $ordertype, $endYear);
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
                $order = $this->orderListingweek($startFrom, $endFrom, $ordertype, $storeId, $endYear);
                $startFrom = $this->Dateform->formatDate($startFrom);
                $endFrom = $this->Dateform->formatDate($endFrom);
                $startdate = $startFrom;
                $enddate = $endFrom;
                $paginationdata = array('date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2, 'ordertype' => $ordertype);
                $this->set(compact('storeId', 'result', 'paginationdata', 'order', 'weeknumbers', 'data', 'date', 'type', 'startFrom', 'endFrom', 'Month', 'Year', 'yearFrom', 'yearTo', 'startdate', 'enddate'));
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

                $order = $this->orderListing($dateFrom, $dateTo, $ordertype, $storeId);
                $graphorder = $this->ordergraphListing($dateFrom, $dateTo, $ordertype, $storeId);
                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'ordertype' => $ordertype);

                $this->set(compact('storeId', 'ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
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
                $order = $this->orderListing($dateFrom, $dateTo, $ordertype, $storeId);
                $graphorder = $this->ordergraphListing($dateFrom, $dateTo, $ordertype, $storeId);

                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'ordertype' => $ordertype);

                $this->set(compact('storeId', 'ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
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
                $order = $this->orderListing(null, null, $ordertype, $storeId);
                $graphorder = $this->ordergraphListing(null, null, $ordertype, $storeId);

                $result = $graphorder;

                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'ordertype' => $ordertype);
                $this->set(compact('storeId', 'ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {
            $order = $this->orderListing($sdate, $edate, $ordertype, $storeId = null);
            $graphorder = $this->ordergraphListing($sdate, $edate, $ordertype, $storeId = null);
            $paginationdata = array('startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'ordertype' => $ordertype);
            $result = $graphorder;
            $this->set(compact('order', 'result', 'paginationdata'));
        }
        $typeList = $this->Segment->OrderTypeList($storeId);
        $this->set('typeList', $typeList);
        $this->set(compact('ordertype', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
    }

    public function customerReport($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }

        $this->layout = "super_dashboard";
        $page = '';
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
        if (!empty($this->data) || !empty($this->params['named'])) {
            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;
            if ($this->Session->read('ResultSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
                $this->request->data = json_decode($this->Session->read('ResultSearchData'), true);
            } else {
                $this->Session->delete('ResultSearchData');
            }
            if (!empty($this->request->data['Store']['id'])) {
                $this->Session->write('ResultSearchData', json_encode($this->request->data));
                $storeId = $this->request->data['Store']['id'];
                if (!empty($storeId) && $storeId != 'All') {
                    $storeDate = $this->Common->getcurrentTime($storeId, 1);
                    $storeDateTime = explode(" ", $storeDate);
                    $storeDate = $storeDateTime[0];
                    $storeTime = $storeDateTime[1];
                    $this->set('storeTime', $storeTime);
                    $sdate = $storeDate . " " . "00:00:00";
                    $edate = $storeDate . " " . "23:59:59";
                    $type = $type;
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
                }
            } else {
                  $storeId = null;
            }
                if ($type == 1) {

                    //  $ordertype=2;
                    if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                        $startdate = date('Y-m-d 00:00:00', strtotime($this->params['named']['startdate']));
                        $enddate = date('Y-m-d 23:59:59', strtotime($this->params['named']['enddate']));
                        if (!empty($this->params['named']['page'])) {
                            $page = $this->params['named']['page'];
                        } else {
                            $page = 1;
                        }
                    } else {
                        $startdate = $this->data['Report']['startdate'];
                        $enddate = $this->data['Report']['enddate'];
                        $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
                        $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
                        $page = 1;
                    }

                    if (isset($this->request->params['named']) && isset($this->params['named']['sort'])) {

                        $sort = $this->request->params['named']['sort'];
                        $startdate1 = $this->request->params['named']['startdate'];
                        $enddate1 = $this->request->params['named']['enddate'];
                        $startdate = date('Y-m-d 00:00:00', strtotime($startdate1));
                        $enddate = date('Y-m-d 23:59:59', strtotime($enddate1));
                        if (!empty($this->request->params['named']['ordertype'])) {
                            $ordertype = $this->request->params['named']['ordertype'];
                        }
                        $type = $this->request->params['named']['type'];
                        if (!empty($this->params['named']['page'])) {
                            $page = $this->params['named']['page'];
                        } else {
                            $page = 1;
                        }
                    }

                    $result1 = $this->User->fetchUserToday($storeId, $startdate, $enddate);
                    $user = array();
                    foreach ($result1 as $key => $data) {
                        $user[$key]['User']['per_day'] = $data[0]['per_day'];
                        $user[$key]['User']['created'] = $data['User']['created'];
                    }

                    $result = $user;

                    $userdata = $this->userListing($startdate, $enddate, $storeId);
                    $startdate = $this->Dateform->formatDate($startdate);
                    $enddate = $this->Dateform->formatDate($enddate);
                    $paginationdata = array('startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'page' => $page);
                    $this->set(compact('page', 'storeId', 'userdata', 'user', 'result', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
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
                    $expoladEndDate = explode(" ", $endFrom);
                    $endMonth = $expoladEndDate[1];
                    $explodeEndYear = explode("-", $expoladEndDate[0]);
                    $endYear = $explodeEndYear[0];
                    $startweekNumber = date("W", strtotime($startFrom));
                    $endWeekNumber = date("W", strtotime($endFrom));
                    $data = array();
                    $return = array();
                    $weeknumbers = '';
                    $j = 0;
                    for ($i = $startweekNumber; $i <= $endWeekNumber; $i++) {
                        $data[$i] = array();
                        if ($j == 0) {
                            $weeknumbers .= "'Week" . $i . "'";
                        } else {
                            $weeknumbers .= ",'Week" . $i . "'";
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
                                $datestring .= ",'" . date('Y-m-d', $time2) . "'";
                            }
                            $data[$i]['datestring'] = $datestring;
                        }
                    }

                    $result1 = $this->fetchWeeklyUserToday($storeId, $startFrom, $endFrom, $endYear);

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
                    //echo $startFrom;echo '<br>';echo $endFrom;echo '<br>';echo $storeId;die;

                    $userdata = $this->userListingweekly($startFrom, $endFrom, $storeId, $endYear);



                    $startFrom = $this->Dateform->formatDate($startFrom);
                    $endFrom = $this->Dateform->formatDate($endFrom);
                    $startdate = $startFrom;
                    $enddate = $endFrom;
                    $paginationdata = array('date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2, 'page' => $page);
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

                    $result1 = $this->User->fetchUserToday($storeId, $dateFrom, $dateTo);
                    $user = array();
                    foreach ($result1 as $key => $data) {
                        $user[$key]['User']['per_day'] = $data[0]['per_day'];
                        $user[$key]['User']['created'] = $data['User']['created'];
                    }

                    $result = $user;
                    $userdata = $this->userListing($dateFrom, $dateTo, $storeId);

                    $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'page' => $page);

                    $this->set(compact('page', 'storeId', 'userdata', 'user', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
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


                    $result1 = $this->User->fetchUserToday($storeId, $dateFrom, $dateTo);
                    $user = array();
                    foreach ($result1 as $key => $data) {
                        $user[$key]['User']['per_day'] = $data[0]['per_day'];
                        $user[$key]['User']['created'] = $data['User']['created'];
                    }

                    $result = $user;
                    $userdata = $this->userListing($dateFrom, $dateTo, $storeId);

                    $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'page' => $page);

                    $this->set(compact('page', 'storeId', 'userdata', 'user', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
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
                    $result1 = $this->User->fetchUserToday($storeId, null, null);


                    $user = array();
                    foreach ($result1 as $key => $data) {
                        $user[$key]['User']['per_day'] = $data[0]['per_day'];
                        $user[$key]['User']['created'] = $data['User']['created'];
                    }

                    $result = $user;
                    $userdata = $this->userListing(null, null, $storeId);
                    $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'ordertype' => $ordertype, 'page' => $page);
                    $this->set(compact('page', 'storeId', 'userdata', 'user', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
                }
        } else {

            $result1 = $this->User->fetchUserToday($storeId = null, $sdate, $edate);
            //echo '<pre>';print_r($result1);die;
            $user = array();
            foreach ($result1 as $key => $data) {
                $user[$key]['User']['per_day'] = $data[0]['per_day'];
                $user[$key]['User']['created'] = $data['User']['created'];
            }
            $page = 1;
            $result = $user;

            $userdata = $this->userListing($sdate, $edate, $storeId = null);
            $paginationdata = array('startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'ordertype' => $ordertype, 'page' => 1);

            $this->set(compact('page', 'order', 'result', 'paginationdata'));
        }
        $this->set(compact('page', 'paginationdata', 'storeId', 'userdata', 'user', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo'));
    }

    /*     * ***********************
     * Function name:userListingweekly()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function userListingweekly($startDate = null, $endDate = null, $storeId = null, $endYear = null) {
        if (!empty($storeId)) {
            $criteria = "User.store_id =$storeId AND User.is_deleted=0 AND User.is_active=1 AND User.role_id IN (4,5)";
        } else {
            $criteria = "User.is_deleted=0 AND User.is_active=1 AND User.role_id=4";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);

            $stratdate = date('Y-m-d 00:00:00', strtotime($stratdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            // $criteria.= " AND (User.created BETWEEN '".$startDate."' AND '".$endDate."')";

            $criteria .= " AND WEEK(User.created) >=WEEK('" . $stratdate . "') AND WEEK(User.created) <=WEEK('" . $enddate . "') AND YEAR(User.created) ='" . $endYear . "'";
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
        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $userdetail = $this->paginate('User');
        return $userdetail;
    }

    /*     * ***********************
     * Function name:itemListing()
      Description:graph item list
      created:06/10/2015
     *
     * ********************* */

    public function itemListing($startDate = null, $endDate = null, $itemId = null, $storeID = null) {

        $this->OrderItem->bindModel(array('belongsTo' => array('Order')));
        if (!empty($storeID)) {

            if ($startDate && $endDate) {
                //$stratdate = $this->Dateform->formatDate($startDate);
                //$enddate = $this->Dateform->formatDate($endDate);
                //  echo $stratdate;echo '<br>';echo $enddate;echo '<br>';echo $itemId;die;
                $conditions = array('Order.store_id' => $storeID, 'Order.created >=' => $startDate, 'Order.created <=' => $endDate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            } else {
                $conditions = array('Order.store_id' => $storeID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            }
        } else {

            if ($startDate && $endDate) {
                //$stratdate = $this->Dateform->formatDate($startDate);
                //$enddate = $this->Dateform->formatDate($endDate);
                //  echo $stratdate;echo '<br>';echo $enddate;echo '<br>';echo $itemId;die;
                $conditions = array('Order.created >=' => $startDate, 'Order.created <=' => $endDate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            } else {
                $conditions = array('Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            }
        }

        if (!empty($itemId)) {
            $conditions['OrderItem.item_id'] = $itemId;
        }

        $orderdetail = $this->OrderItem->find('all', array('fields' => array('DATE(OrderItem.created) AS order_date', 'Count(OrderItem.created) AS number'), 'group' => array("DATE_FORMAT(OrderItem.created, '%Y-%m-%d')"), 'conditions' => array($conditions), 'order' => array('OrderItem.created' => 'DESC')));


        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderListing()
      Description:graph order list
      created:22/09/2015
     *
     * ********************* */

    public function orderListing($startDate = null, $endDate = null, $orderType = null, $storeId = null) {

        // $this->autoRender=false;
        if (isset($storeId)) {
            $storeID = $storeId;
            $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        } else {

            $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        }


        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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

            $criteria .= " AND Segment.id=$orderType";
        }

        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC'));
        $orderdetail = $this->paginate('Order');
        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderListingweek()
      Description:graph order list
      created:22/09/2015
     *
     * ********************* */

    public function orderListingweek($startDate = null, $endDate = null, $orderType = null, $storeId = null, $endYear = null) {

        // $this->autoRender=false;
        if (isset($storeId)) {
            $storeID = $storeId;
            $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        } else {

            $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        }


        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND WEEK(Order.created) >=WEEK('" . $startDate . "') AND WEEK(Order.created) <=WEEK('" . $endDate . "') AND YEAR(Order.created) ='" . $endYear . "'";
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

            $criteria .= " AND Segment.id=$orderType";
        }

        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC'));
        $orderdetail = $this->paginate('Order');
        return $orderdetail;
    }

    public function orderListg($storeId = null, $startDate = null, $endDate = null, $orderType = null) {

        // $this->autoRender=false;
        if (isset($storeId)) {
            $storeID = $storeId;
        } else {
            $storeID = 0;
        }

        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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

            $criteria .= " AND Segment.id=$orderType";
        }

        //  $this->paginate= array('recursive'=>2,'conditions'=>array($criteria),'order'=>array('Order.created'=> 'DESC'));
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));

        return $orderdetail;
    }

    public function orderListgweek($storeId = null, $startDate = null, $endDate = null, $orderType = null) {

        // $this->autoRender=false;
        if (isset($storeId)) {
            $storeID = $storeId;
        } else {
            $storeID = 0;
        }

        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND WEEK(Order.created) >=WEEK('" . $startDate . "') AND WEEK(Order.created) <=WEEK('" . $endDate . "')";
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

            $criteria .= " AND Segment.id=$orderType";
        }

        //  $this->paginate= array('recursive'=>2,'conditions'=>array($criteria),'order'=>array('Order.created'=> 'DESC'));
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));

        return $orderdetail;
    }

    public function supeorderListg($startDate = null, $endDate = null, $orderType = null) {

        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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

            $criteria .= " AND Segment.id=$orderType";
        }

        //  $this->paginate= array('recursive'=>2,'conditions'=>array($criteria),'order'=>array('Order.created'=> 'DESC'));
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));

        return $orderdetail;
    }

    public function supeorderListgweek($startDate = null, $endDate = null, $orderType = null) {

        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND WEEK(Order.created) >=WEEK('" . $startDate . "') AND WEEK(Order.created) <=WEEK('" . $endDate . "')";
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

            $criteria .= " AND Segment.id=$orderType";
        }

        //  $this->paginate= array('recursive'=>2,'conditions'=>array($criteria),'order'=>array('Order.created'=> 'DESC'));
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));

        return $orderdetail;
    }

    public function superorderListg($startDate = null, $endDate = null, $orderType = null) {

        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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

            $criteria .= " AND Segment.id=$orderType";
        }

        //  $this->paginate= array('recursive'=>2,'conditions'=>array($criteria),'order'=>array('Order.created'=> 'DESC'));
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));
        return $orderdetail;
    }

    public function superweekorderListg($startDate = null, $endDate = null, $orderType = null) {

        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);

            $criteria .= " AND WEEK(Order.created) >=WEEK('" . $startDate . "') AND WEEK(Order.created) <=WEEK('" . $endDate . "')";
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

            $criteria .= " AND Segment.id=$orderType";
        }

        //  $this->paginate= array('recursive'=>2,'conditions'=>array($criteria),'order'=>array('Order.created'=> 'DESC'));
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));
        return $orderdetail;
    }

    /*     * ***********************
     * Function name:ordergraphListing()
      Description:graph order list
      created:22/09/2015
     *
     * ********************* */

    public function ordergraphListing($startDate = null, $endDate = null, $orderType = null, $storeId = null) {

        // $this->autoRender=false;
        if (isset($storeId)) {
            $storeID = $storeId;
            $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        } else {
            $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        }

        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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

            $criteria .= " AND Segment.id=$orderType";
        }
        $graphorderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));


        return $graphorderdetail;
    }

    /*     * ***********************
     * Function name:userListing()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function userListing($startDate = null, $endDate = null, $storeID = null) {
        if (!empty($storeID)) {
            $criteria = "User.store_id =$storeID AND User.is_deleted=0 AND User.is_active=1 AND User.role_id IN (4,5)";
        } else {
            $criteria = "User.is_deleted=0 AND User.is_active=1 AND User.role_id=4";
        }

        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (User.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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

        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $userdetail = $this->paginate('User');
        return $userdetail;
    }

    /*     * ***********************
     * Function name:userexcelListing()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function userexcelListing($storeID = null, $startDate = null, $endDate = null) {
        $criteria = "User.store_id =$storeID AND User.is_deleted=0 AND User.is_active=1";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (User.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }

        $userdetail = $this->User->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC')));

        return $userdetail;
    }

    /*     * ***********************
     * Function name:supeuserexcelListing()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function supeuserexcelListing($startDate = null, $endDate = null) {
        $criteria = "User.is_deleted=0 AND User.is_active=1 AND User.role_id=4";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (User.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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

    public function orderProductListing($startDate = null, $endDate = null, $item = null, $storeID = null) {


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



        if (!empty($storeID)) {

            if ($startDate && $endDate) {
                // $stratdate = $this->Dateform->formatDate($startDate);
                // $enddate = $this->Dateform->formatDate($endDate);
                $conditions = array('Order.store_id' => $storeID, 'Order.created >=' => $startDate, 'Order.created <=' => $endDate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            } else {
                $conditions = array('Order.store_id' => $storeID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            }
        } else {

            if ($startDate && $endDate) {
                $stratdate = $this->Dateform->formatDate($startDate);
                $enddate = $this->Dateform->formatDate($endDate);
                $conditions = array('date(Order.created) >=' => $stratdate, 'date(Order.created) <=' => $enddate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            } else {
                $conditions = array('Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            }
        }


        if (!empty($item)) {
            $conditions['OrderItem.item_id'] = $item;
        }

        $this->paginate = array('fields' => array('id', 'order_id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'user_id', 'created'), 'recursive' => 3, 'conditions' => array($conditions), 'order' => array('Order.created' => 'DESC'), 'group' => array('OrderItem.order_id'));
        $orderdetail = $this->paginate('OrderItem');
        return $orderdetail;
    }

    public function fetchWeeklyOrderToday($storeId = null, $start = null, $end = null, $ordertype = null, $endYear = null) {
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
        if (!empty($storeId)) {

            $conditions = "Order.is_future_order=0 AND Order.is_active=1 AND Order.is_deleted=0 AND Order.store_id=$storeId AND WEEK(Order.created) >=WEEK('" . $start . "') AND WEEK(Order.created) <=WEEK('" . $end . "') AND YEAR(Order.created) ='" . $endYear . "'";
        } else {
            $conditions = "Order.is_future_order=0 AND Order.is_active=1 AND Order.is_deleted=0 AND WEEK(Order.created) >=WEEK('" . $start . "') AND WEEK(Order.created) <=WEEK('" . $end . "') AND YEAR(Order.created) ='" . $endYear . "'";
        }
        if ($ordertype) {
            $conditions .= " AND Segment.id=$ordertype";
        }

        $result = $this->Order->find('all', array('fields' => array('WEEK(Order.created) AS WEEKno', 'DATE(Order.created) AS order_date', '`amount`-`coupon_discount` AS total'), 'conditions' => $conditions));
        return $result;
    }

    public function fetchWeeklyUserToday($storeId = null, $start = null, $end = null, $endYear = null) {
        //$conditions=array('User.store_id'=>$storeId,'User.created >='=>$start,'User.created <='=>$end,'User.is_active'=>1,'User.is_deleted'=>0);
        if (!empty($storeId)) {
            $conditions = " User.is_active=1 AND User.is_deleted=0 AND User.store_id=$storeId AND WEEK(User.created) >=WEEK('" . $start . "') AND WEEK(User.created) <=WEEK('" . $end . "') AND YEAR(User.created) ='" . $endYear . "' AND User.role_id IN (4,5)";
        } else {
            $conditions = " User.is_active=1 AND User.is_deleted=0  AND WEEK(User.created) >=WEEK('" . $start . "') AND WEEK(User.created) <=WEEK('" . $end . "')  AND YEAR(User.created) ='" . $endYear . "' AND User.role_id=4";
        }
        $result = $this->User->find('all', array('group' => array('WEEK(User.created)'), 'fields' => array('WEEK(User.created) AS WEEKno', 'DATE(User.created) AS order_date', 'COUNT(User.id) as total'), 'conditions' => $conditions));
        return $result;
    }

    public function orderListings($startDate = null, $endDate = null, $ordertype = null) {
        // $this->autoRender=false;
        //$storeID=$this->Session->read('admin_store_id');

        $storeID = $this->Session->read('selectedStoreId');
        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (Order.created BETWEEN '" . $stratdate . "' AND '" . $enddate . "')";
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
            $criteria .= " AND Segment.id=$ordertype";
        }
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));

        return $orderdetail;
    }

    /*     * ***************************
     * @Function name :productReport
     * @Descriptipn:Display the product report
     * @Author:smartdata
     * *************************** */

    public function productReport($clearAction = null) {
        $this->layout = "super_dashboard";
        $storeId = '';
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
        if (!empty($this->data) || !empty($this->params['named'])) {
            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;

            if ($this->Session->read('ResultSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
                $this->request->data = json_decode($this->Session->read('ResultSearchData'), true);
            } else {
                $this->Session->delete('ResultSearchData');
            }
            if (!empty($this->request->data['Store']['id'])) {
                $this->Session->write('ResultSearchData', json_encode($this->request->data));
                $storeId = $this->request->data['Store']['id'];
                if (!empty($storeId) && $storeId != 'All') {
                    $storeDate = $this->Common->getcurrentTime($storeId, 1);
                    $storeDateTime = explode(" ", $storeDate);
                    $storeDate = $storeDateTime[0];
                    $storeTime = $storeDateTime[1];
                    $this->set('storeTime', $storeTime);
                    $sdate = $storeDate . " " . "00:00:00";
                    $edate = $storeDate . " " . "23:59:59";
                    $type = $type;
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
                }
            }else{
                $storeId = null;
            }

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

                        $sort = $this->request->params['named']['sort'];
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

                    $orderlist = $this->itemListing($startdate, $enddate, $item, $storeId);

                    $order = $this->orderProductListing($startdate, $enddate, $item, $storeId);

                    $result = $orderlist;
                    $startdate = $this->Dateform->formatDate($startdate);
                    $enddate = $this->Dateform->formatDate($enddate);
                    $this->request->data['Item']['id'] = $item;
                    //pr($orderlist);
                    // pr($order);
                    // die;

                    $paginationdata = array('startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'item' => $item, 'storeId' => $storeId);
                    $this->set(compact('storeId', 'order', 'item', 'result', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
                } else if ($type == 2) {

                    if (isset($this->params['named']) && isset($this->params['named']['date_start_from'])) {
                        $startFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_start_from']));
                        $endFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_end_from']));
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
                    $expoladEndDate = explode(" ", $endFrom);
                    $endMonth = $expoladEndDate[1];
                    $explodeEndYear = explode("-", $expoladEndDate[0]);
                    $endYear = $explodeEndYear[0];
                    $startweekNumber = date("W", strtotime($startFrom));
                    $endWeekNumber = date("W", strtotime($endFrom));
                    $data = array();
                    $return = array();
                    $weeknumbers = '';
                    $j = 0;
                    for ($i = $startweekNumber; $i <= $endWeekNumber; $i++) {
                        $data[$i] = array();
                        if ($j == 0) {
                            $weeknumbers .= "'Week" . $i . "'";
                        } else {
                            $weeknumbers .= ",'Week" . $i . "'";
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
                                $datestring .= ",'" . date('Y-m-d', $time2) . "'";
                            }
                            $data[$i]['datestring'] = $datestring;
                        }
                    }

                    $result1 = $this->fetchWeeklyProductToday($storeId, $startFrom, $endFrom, $item, $endYear);
                    $weekarray = array();
                    $datearray = array();

                    foreach ($result1 as $k => $result) {
                        if (in_array($result[0]['WEEKno'], $weekarray)) {
                            $data[$result[0]['WEEKno']]['week'] = $result[0]['WEEKno'];
                            $data[$result[0]['WEEKno']]['totalamount'] += $result['OrderItem']['quantity'];

                            //1;//$result[0]['item_count'];
                        } else {
                            $weekarray[$result[0]['WEEKno']] = $result[0]['WEEKno'];
                            $data[$result[0]['WEEKno']]['totalamount'] = $result['OrderItem']['quantity'];
                            //1;//$result[0]['item_count'];
                        }

                        if (in_array($result[0]['order_date'], $datearray)) {
                            $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                            $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                            $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['item_count'] += $result['OrderItem']['quantity'];
                            //1;//$result[0]['item_count'] ;
                        } else {
                            $datearray[$result[0]['order_date']] = $result[0]['order_date'];
                            $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['WEEKno'] = $result[0]['WEEKno'];
                            $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['order_date'] = $result[0]['order_date'];
                            $data[$result[0]['WEEKno']]['daywise'][$result[0]['order_date']]['item_count'] = $result['OrderItem']['quantity'];
                            //1;//$result[0]['item_count'] ;
                        }
                    }
                    $order = $this->orderProductListingweek($startFrom, $endFrom, $item, $storeId, $endYear);

                    $startFrom = $this->Dateform->formatDate($startFrom);
                    $endFrom = $this->Dateform->formatDate($endFrom);
                    $startdate = $startFrom;
                    $enddate = $endFrom;
                    $paginationdata = array('date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2, 'storeId' => $storeId);
                    $this->set(compact('storeId', 'paginationdata', 'order', 'weeknumbers', 'data', 'date', 'type', 'startFrom', 'endFrom', 'Month', 'Year', 'yearFrom', 'yearTo', 'startdate', 'enddate'));
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

                    $orderlist = $this->itemListing($dateFrom, $dateTo, $item, $storeId);
                    $order = $this->orderProductListing($dateFrom, $dateTo, $item, $storeId);
                    // echo '<pre>';print_r($orderlist);echo '<br>';echo '<pre>';print_r($order);die;
                    $result = $orderlist;
                    $this->request->data['Item']['id'] = $item;
                    $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'storeId' => $storeId);

                    $this->set(compact('storeId', 'item', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
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
                    $orderlist = $this->itemListing($dateFrom, $dateTo, $item, $storeId);
                    $order = $this->orderProductListing($dateFrom, $dateTo, $item, $storeId);
                    // echo '<pre>';print_r($orderlist);echo '<br>';echo '<pre>';print_r($order);die;
                    $result = $orderlist;
                    $this->request->data['Item']['id'] = $item;
                    $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'storeId' => $storeId);

                    $this->set(compact('storeId', 'item', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
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
                    $orderlist = $this->itemListing(null, null, $item, $storeId);
                    $order = $this->orderProductListing(null, null, $item, $storeId);

                    // echo '<pre>';print_r($orderlist);echo '<br>';echo '<pre>';print_r($order);die;
                    $result = $orderlist;
                    $this->request->data['Item']['id'] = $item;
                    $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'storeId' => $storeId);
                    $this->set(compact('storeId', 'item', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
                }
        } else {

            $item = '';
            $orderlist = $this->itemListing($sdate, $edate, $item, null);

            $order = $this->orderProductListing($sdate, $edate, $item, null);

            $paginationdata = array('startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'item' => null);

            $result = $orderlist;
            $this->set(compact('order', 'result', 'paginationdata'));
        }

        $this->loadModel('Item');
        $itemList = $this->Item->getallItemsByStore($storeId);
        $this->set('categoryList', $itemList);
        $this->set(compact('item', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
    }

    /*     * ***********************
     * Function name:orderProductListingweek()
      Description:graph order product list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductListingweek($startDate = null, $endDate = null, $item = null, $storeID = null, $endYear = null) {

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
                ), 'Store' => array(
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



        if (!empty($storeID)) {

            if ($startDate && $endDate) {
                $stratdate = $this->Dateform->formatDate($startDate);
                $enddate = $this->Dateform->formatDate($endDate);
                //  $conditions=array('Order.store_id'=>$storeID,'Order.created >='=>$stratdate,'Order.created <='=>$enddate,'Order.is_active'=>1,'Order.is_deleted'=>0,'Order.is_future_order'=>0);


                $conditions = " Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0 AND Order.store_id=$storeID AND WEEK(Order.created) >=WEEK('" . $stratdate . "') AND WEEK(Order.created) <=WEEK('" . $enddate . "') AND YEAR(Order.created) ='" . $endYear . "'";
            } else {
                $conditions = array('Order.store_id' => $storeID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            }
        } else {

            if ($startDate && $endDate) {
                $stratdate = $this->Dateform->formatDate($startDate);
                $enddate = $this->Dateform->formatDate($endDate);
                //  $conditions=array('Order.store_id'=>$storeID,'Order.created >='=>$stratdate,'Order.created <='=>$enddate,'Order.is_active'=>1,'Order.is_deleted'=>0,'Order.is_future_order'=>0);


                $conditions = " Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0 AND WEEK(Order.created) >=WEEK('" . $stratdate . "') AND WEEK(Order.created) <=WEEK('" . $enddate . "')";
            } else {
                $conditions = array('Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
            }
        }


        if (!empty($item)) {
            $conditions .= " AND OrderItem.item_id =$item";
        }

        $this->paginate = array('fields' => array('id', 'order_id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'user_id', 'created'), 'recursive' => 3, 'conditions' => array($conditions), 'order' => array('Order.created' => 'DESC'), 'group' => array('OrderItem.order_id'));
        $orderdetail = $this->paginate('OrderItem');
        return $orderdetail;
    }

    public function productReportDownload($storeId = null, $type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $item = null) {

        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderProductexcelListing($startdate, $enddate, $storeId);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderProductexcelListingweek($startdate, $enddate, $storeId);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->orderProductexcelListing($dateFrom, $dateTo, $storeId);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->orderProductexcelListing($dateFrom, $dateTo, $storeId);
            $text = 'Yearly_Report';
        } else {
            // echo $sdate;echo '<br>';echo $edate;echo '<br>';echo  $ordertype;die;
            $order = $this->orderProductexcelListing(null, null, $storeId);
            $text = 'LifeTime_Report';
        }
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
                    $items .= ", " . $item['Item']['name'];
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

    public function orderProductexcelListingweek($startDate = null, $endDate = null, $storeID = null) {

        // $this->autoRender=false;
        if (!empty($storeID)) {
            $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        } else {
            $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        }
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND WEEK(Order.created) >=WEEK('" . $startDate . "') AND WEEK(Order.created) <=WEEK('" . $endDate . "')";
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

    public function superproductReportDownload($type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $item = null) {

        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        if ($type == 1) {

            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->superorderProductexcelListing($startdate, $enddate);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderProductexcelListingweek($startdate, $enddate, null);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->superorderProductexcelListing($dateFrom, $dateTo);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->superorderProductexcelListing($dateFrom, $dateTo);
            $text = 'Yearly_Report';
        } else {
            // echo $sdate;echo '<br>';echo $edate;echo '<br>';echo  $ordertype;die;
            $order = $this->superorderProductexcelListing(null, null);
            $text = 'LifeTime_Report';
        }
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
                    $items .= ", " . $item['Item']['name'];
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
     * Function name:orderProductexcelListing()
      Description:graph order product excel list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductexcelListing($startDate = null, $endDate = null, $storeId = null) {

        // $this->autoRender=false;
        $storeID = $storeId;
        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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
     * Function name:superorderProductexcelListing()
      Description:graph order product excel list
      created:22/09/2015
     *
     * ********************* */

    public function superorderProductexcelListing($startDate = null, $endDate = null) {

        $criteria = "Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria .= " AND (Order.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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

    public function fetchWeeklyProductToday($storeId = null, $start = null, $end = null, $item = null, $endYear = null) {


        $this->OrderItem->bindModel(array('belongsTo' => array('Order')));
        if (!empty($storeId)) {
            //$conditions=array('Order.store_id'=>$storeId,'Order.created >='=>$start,'Order.created <='=>$end,'Order.is_active'=>1,'Order.is_deleted'=>0,'Order.is_future_order'=>0);

            $conditions = " Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0 AND Order.store_id=$storeId AND WEEK(Order.created) >=WEEK('" . $start . "') AND WEEK(Order.created) <=WEEK('" . $end . "') AND YEAR(Order.created) ='" . $endYear . "'";
        } else {
            $conditions = " Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0 AND WEEK(Order.created) >=WEEK('" . $start . "') AND WEEK(Order.created) <=WEEK('" . $end . "') AND YEAR(Order.created) ='" . $endYear . "'";
        }


        if (!empty($item)) {
            $conditions .= " AND OrderItem.item_id =$item";
        }
        //$result = $this->OrderItem->find('all',array('group'=>array('WEEK(Order.created)'),'fields' => array('WEEK(Order.created) AS WEEKno','DATE(Order.created) AS order_date','COUNT(OrderItem.id) as item_count'),'conditions'=>$conditions));

        $result = $this->OrderItem->find('all', array('fields' => array('WEEK(Order.created) AS WEEKno', 'DATE(Order.created) AS order_date', 'OrderItem.quantity'), 'conditions' => $conditions));
        return $result;
    }

    /* ------------------------------------------------
      Function name:orderHistory()
      Description:Display the customer all orders
      created:18/8/2015
      ----------------------------------------------------- */

    public function orderHistory($EncryptCustomerID = null, $start = null, $end = null, $type = null, $page = null, $storeId = null, $merchantId = null) {

        // echo $EncryptCustomerID;echo '<br>';echo $start;echo '<br>';echo $end;echo '<br>';echo $type;echo '<br>';echo $page;echo '<br>';echo $storeId;
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        //$storeId=$this->Session->read('store_id');
        //$merchantId= $this->Session->read('merchant_id');
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
            //  echo $startparam;echo '<br>';echo $endparam;echo '<br>';echo $typeparam;echo '<br>';echo $pageparam;die;
            $this->redirect(array('controller' => 'superreports', 'action' => 'customerReport', $startparam, $endparam, $typeparam, $pageparam));
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
        $this->layout = "admin_dashboard";
        if (!empty($this->request->params['requested'])) {
            $data = $this->OrderStatus->find('first', array('conditions' => array('OrderStatus.id' => $id)));
            echo $data['OrderStatus']['name'];
        }
    }

    /* ------------------------------------------------
      Function name: orderDetail()
      Description: Dispaly the detail of perticular order
      created:12/8/2015
      ----------------------------------------------------- */

    public function orderDetail($order_id = null) {

        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'moneyReport', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->loadModel('Item');
        $this->layout = "super_dashboard";
        $storeID = $this->Session->read('selectedStoreId');
        $merchantId = $this->Session->read('merchantId');
        $orderId = $this->Encryption->decode($order_id);

        // $this->OrderItem->bindModel(array('belongsTo'=>array('Item'=>array('className' => 'Item','foreignKey'=>'item_id','fields'=>array('id','name')))), false);
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name','category_id')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->Item->bindModel(array('belongsTo' => array('category' => array('fields' =>
                    array('id', 'name')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name','category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'total_item_price', 'tax_price'))), 'belongsTo' => array('Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'), 'DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('name')))), false);
        $orderDetails = $this->Order->getsuperSingleOrderDetail(null, null, $orderId);
        $this->set('orderDetail', $orderDetails);
        $this->loadModel('OrderStatus');
        $statusList = $this->OrderStatus->OrderStatusList($storeID);
        $this->set('statusList', $statusList);
    }

}
