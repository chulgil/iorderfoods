<?php

App::uses('StoreAppController', 'Controller');

class ReportsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Common');
    public $uses = array('OrderPayment', 'Order', 'User', 'OrderItem', 'Segment');

    public function beforeFilter() {
        parent::beforeFilter();
        
        $storeId = $this->Session->read('admin_store_id');
        $storeDate=$this->Common->getcurrentTime($storeId,1);
        $storeDateTime=  explode(" ", $storeDate);
        $this->storeDate=$storeDateTime[0];
        $this->storeTime=$storeDateTime[1];
        $this->set('storeTime',$this->storeTime);
    }

    public function moneyReport() {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $sdate = $this->storeDate." "."00:00:00";
        $edate = $this->storeDate." "."23:59:59";
        $type = 1;
        $ordertype = "";
        $startdate = $this->storeDate;
        $enddate = $this->storeDate;
        $expoladDate=  explode("-", $startdate);
        $Month = $expoladDate[1];
        $Year = $expoladDate[0];
        $yearFrom = date('Y', strtotime('-1 year',strtotime($Year)));
        $yearTo = $Year;
        $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
        $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        if (!empty($this->data) || !empty($this->params['named'])) {
            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;
            if ($type == 1) {
                //  $ordertype=2;
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
//                if (isset($this->request->params['named']) && isset($this->params['named']['sort'])) {
//
//                    $sort = $this->request->params['named']['sort'];
//                    $startdate1 = $this->request->params['named']['startdate'];
//                    $enddate1 = $this->request->params['named']['enddate'];
//                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate1));
//                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate1));
//                    $ordertype = $this->request->params['named']['ordertype'];
//                    $type = $this->request->params['named']['type'];
//                }
                $order = $this->orderListing($startdate, $enddate, $ordertype);
                //pr($order);
                $graphorder = $this->ordergraphListing($startdate, $enddate, $ordertype);
                $result = $graphorder;
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $this->request->data['Segment']['id'] = $ordertype;
                $paginationdata = array('startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'ordertype' => $ordertype);
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

                    $time = strtotime("1 January $weekyear", strtotime($this->storeTime));
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
                //echo $data[$result[0]['WEEKno']]['totalorders'];die;

                $order = $this->orderListingweek($startFrom, $endFrom, $ordertype,$endYear);
                $startFrom = $this->Dateform->formatDate($startFrom);
                $endFrom = $this->Dateform->formatDate($endFrom);
                $startdate = $startFrom;
                $enddate = $endFrom;
                $paginationdata = array('date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2, 'ordertype' => $ordertype);
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

                $order = $this->orderListing($dateFrom, $dateTo, $ordertype);
                $graphorder = $this->ordergraphListing($dateFrom, $dateTo, $ordertype);

                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'ordertype' => $ordertype);

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

                $order = $this->orderListing($dateFrom, $dateTo, $ordertype);
                $graphorder = $this->ordergraphListing($dateFrom, $dateTo, $ordertype);

                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'ordertype' => $ordertype);

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

                $order = $this->orderListing(null, null, $ordertype);
                $graphorder = $this->ordergraphListing(null, null, $ordertype);

                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {
            $order = $this->orderListing($sdate, $edate, $ordertype);
            $graphorder = $this->ordergraphListing($sdate, $edate, $ordertype);
            $result = $graphorder;
            $paginationdata = array('startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'ordertype' => $ordertype);
            $this->set(compact('order', 'result', 'paginationdata'));
        }
        $typeList = $this->Segment->OrderTypeList($storeId);
        $this->set('typeList', $typeList);
        $this->set(compact('ordertype', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
    }

    public function moneyReportDownload($type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $ordertype = null) {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $storeId = $this->Session->read('admin_store_id');

        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderListg($startdate, $enddate, $ordertype);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderListgweek($startdate, $enddate, $ordertype);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->orderListg($dateFrom, $dateTo, $ordertype);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->orderListg($dateFrom, $dateTo, $ordertype);
            $text = 'Yearly_Report';
        } else if ($type == 5) {
            $order = $this->orderListg(null, null, $ordertype);
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

    public function orderReportDownload($type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $ordertype = null) {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $storeId = $this->Session->read('admin_store_id');
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderListg($startdate, $enddate, $ordertype);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderListgweek($startdate, $enddate, $ordertype);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->orderListg($dateFrom, $dateTo, $ordertype);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->orderListg($dateFrom, $dateTo, $ordertype);
            $text = 'Yearly_Report';
        } else {

            $order = $this->orderListg(null, null, $ordertype);
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

    public function customerReportDownload($type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null) {

        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $storeId = $this->Session->read('admin_store_id');
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $userdata = $this->userexcelListing($startdate, $enddate);

            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $userdata = $this->weekuserexcelListing($startdate, $enddate);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $userdata = $this->userexcelListing($dateFrom, $dateTo);

            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $userdata = $this->userexcelListing($dateFrom, $dateTo);
            $text = 'Yearly_Report';
        } else {
            // echo $sdate;echo '<br>';echo $edate;echo '<br>';echo  $ordertype;die;
            $userdata = $this->userexcelListing(null, null);
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

    public function orderReport() {

        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $sdate = $this->storeDate." "."00:00:00";
        $edate = $this->storeDate." "."23:59:59";
        $type = 1;
        $ordertype = "";
        $startdate = $this->storeDate;
        $enddate = $this->storeDate;
        $expoladDate=  explode("-", $startdate);
        $Month = $expoladDate[1];
        $Year = $expoladDate[0];
        $yearFrom = date('Y', strtotime('-1 year',strtotime($Year)));
        $yearTo = $Year;
        $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
        $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        
        if (!empty($this->data) || !empty($this->params['named'])) {
            if (isset($this->data['Report']['type'])) {
                $type = $this->data['Report']['type'];
            } else {
                $type = $this->params['named']['type'];
            }
            $this->request->data['Report']['type'] = $type;
            if ($type == 1) {
                //  $ordertype=2;
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

//                if (isset($this->request->params['named']) && isset($this->params['named']['sort'])) {
//
//                    $sort = $this->request->params['named']['sort'];
//                    $startdate1 = $this->request->params['named']['startdate'];
//                    $enddate1 = $this->request->params['named']['enddate'];
//                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate1));
//                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate1));
//                    $ordertype = $this->request->params['named']['ordertype'];
//                    $type = $this->request->params['named']['type'];
//                }

                $order = $this->orderListing($startdate, $enddate, $ordertype);
                $graphorder = $this->ordergraphListing($startdate, $enddate, $ordertype);

                $result = $graphorder;
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $this->request->data['Segment']['id'] = $ordertype;
                $paginationdata = array('startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'ordertype' => $ordertype);
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

                $order = $this->orderListingweek($startFrom, $endFrom, $ordertype,$endYear);
                $startFrom = $this->Dateform->formatDate($startFrom);
                $endFrom = $this->Dateform->formatDate($endFrom);
                $startdate = $startFrom;
                $enddate = $endFrom;
                $paginationdata = array('date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2, 'ordertype' => $ordertype);
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

                $order = $this->orderListing($dateFrom, $dateTo, $ordertype);
                $graphorder = $this->ordergraphListing($dateFrom, $dateTo, $ordertype);
                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'ordertype' => $ordertype);

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
                $order = $this->orderListing($dateFrom, $dateTo, $ordertype);
                $graphorder = $this->ordergraphListing($dateFrom, $dateTo, $ordertype);

                $result = $graphorder;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'ordertype' => $ordertype);

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
                $order = $this->orderListing(null, null, $ordertype);
                $graphorder = $this->ordergraphListing(null, null, $ordertype);

                $result = $graphorder;

                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'ordertype' => $ordertype);
                $this->set(compact('ordertype', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {
            $order = $this->orderListing($sdate, $edate, $ordertype);
            $graphorder = $this->ordergraphListing($sdate, $edate, $ordertype);
            $paginationdata = array('startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'ordertype' => $ordertype);
            $result = $graphorder;
            $this->set(compact('order', 'result', 'paginationdata'));
        }
        $typeList = $this->Segment->OrderTypeList($storeId);
        $this->set('typeList', $typeList);
        $this->set(compact('ordertype', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
    }

    public function customerReport() {

        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $type = 1;
        $ordertype = 3;
        $startdate = date('Y-m-d');
        $page = '';
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
            if ($type == 1) {
                if (isset($this->params['named']) && isset($this->params['named']['startdate'])) {
                    $startdate = date('Y-m-d 00:00:00', strtotime($this->params['named']['startdate']));
                    $enddate = date('Y-m-d 00:00:00', strtotime($this->params['named']['enddate']));
                } else {
                    $startdate = $this->data['Report']['startdate'];
                    $enddate = $this->data['Report']['enddate'];
                    $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
                    $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
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
                    $page = 1;
                }

                $result1 = $this->User->fetchUserToday($storeId, $startdate, $enddate);
                $user = array();
                foreach ($result1 as $key => $data) {
                    $user[$key]['User']['per_day'] = $data[0]['per_day'];
                    $user[$key]['User']['created'] = $data['User']['created'];
                }

                $result = $user;

                $userdata = $this->userListing($startdate, $enddate);
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $paginationdata = array('startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'page' => 1);
                $this->set(compact('page', 'userdata', 'user', 'result', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
            } else if ($type == 2) {

                if (isset($this->params['named']) && isset($this->params['named']['date_start_from'])) {
                    $startFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_start_from']));
                    $endFrom = date('Y-m-d 00:00:00', strtotime($this->params['named']['date_end_from']));
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

                $result1 = $this->fetchWeeklyUserToday($storeId, $startFrom, $endFrom);
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



                $userdata = $this->userListingweekly($startFrom, $endFrom);

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
                $userdata = $this->userListing($dateFrom, $dateTo);

                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3, 'page' => $page);

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


                $result1 = $this->User->fetchUserToday($storeId, $dateFrom, $dateTo);
                $user = array();
                foreach ($result1 as $key => $data) {
                    $user[$key]['User']['per_day'] = $data[0]['per_day'];
                    $user[$key]['User']['created'] = $data['User']['created'];
                }

                $result = $user;
                $userdata = $this->userListing($dateFrom, $dateTo);

                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4, 'page' => $page);

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
                $result1 = $this->User->fetchUserToday($storeId, null, null);


                $user = array();
                foreach ($result1 as $key => $data) {
                    $user[$key]['User']['per_day'] = $data[0]['per_day'];
                    $user[$key]['User']['created'] = $data['User']['created'];
                }

                $result = $user;

                $userdata = $this->userListing(null, null);

                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5, 'ordertype' => $ordertype, 'page' => $page);
                $this->set(compact('page', 'userdata', 'user', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {
            $result1 = $this->User->fetchUserToday($storeId, $sdate, $edate);
            $user = array();
            foreach ($result1 as $key => $data) {
                $user[$key]['User']['per_day'] = $data[0]['per_day'];
                $user[$key]['User']['created'] = $data['User']['created'];
            }
            $page = 1;
            $result = $user;

            $userdata = $this->userListing($sdate, $edate);
            $paginationdata = array('startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'ordertype' => $ordertype, 'page' => 1);
            $this->set(compact('page', 'order', 'result', 'paginationdata'));
        }
        $this->set(compact('page', 'userdata', 'user', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo'));
    }

    /*     * ***********************
     * Function name:itemListing()
      Description:graph item list
      created:06/10/2015
     *
     * ********************* */

    public function itemListing($startDate = null, $endDate = null, $itemId = null) {



        $storeID = $this->Session->read('admin_store_id');
        $this->OrderItem->bindModel(array('belongsTo' => array('Order')));
        if ($startDate && $endDate) {
            $conditions = array('Order.store_id' => $storeID, 'Order.created >=' => $startDate, 'Order.created <=' => $endDate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        } else {
            $conditions = array('Order.store_id' => $storeID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        }
        if (!empty($itemId)) {
            $conditions['OrderItem.item_id'] = $itemId;
        }

        $orderdetail = $this->OrderItem->find('all', array('fields' => array('DATE(OrderItem.created) AS order_date', 'Count(OrderItem.created) AS number'), 'group' => array("DATE_FORMAT(OrderItem.created, '%Y-%m-%d')"), 'conditions' => array($conditions), 'order' => array('OrderItem.created' => 'DESC')));


        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderListingweek()
      Description:graph order list
      created:22/09/2015
     *
     * ********************* */

    public function orderListingweek($startDate = null, $endDate = null, $orderType = null,$endYear=null) {

        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND WEEK(Order.created) >=WEEK('" . $startDate . "') AND WEEK(Order.created) <=WEEK('" . $endDate . "') AND YEAR(Order.created) ='" . $endYear . "'";
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

        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC'));
        $orderdetail = $this->paginate('Order');


        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderListing()
      Description:graph order list
      created:22/09/2015
     *
     * ********************* */

    public function orderListing($startDate = null, $endDate = null, $orderType = null) {
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Order.store_id = $storeID AND Order.is_deleted = 0 AND Order.is_active=1 AND Order.is_future_order=0";
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

        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC'));
        $orderdetail = $this->paginate('Order');


        return $orderdetail;
    }

    public function orderListg($startDate = null, $endDate = null, $orderType = null) {

        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (Order.pickup_time BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
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

    public function orderListgweek($startDate = null, $endDate = null, $orderType = null) {

        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";


        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND WEEK(Order.pickup_time) >=WEEK('" . $startDate . "') AND WEEK(Order.pickup_time) <=WEEK('" . $endDate . "')";
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

    public function ordergraphListing($startDate = null, $endDate = null, $orderType = null) {
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
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
        $graphorderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));


        return $graphorderdetail;
    }

    /*     * ***********************
     * Function name:userListing()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function userListing($startDate = null, $endDate = null) {
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "User.store_id =$storeID AND User.is_deleted=0 AND User.is_active=1 AND User.role_id IN (4,5)";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (User.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }
        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $userdetail = $this->paginate('User');

        return $userdetail;
    }

    /*     * ***********************
     * Function name:userListingweekly()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function userListingweekly($startDate = null, $endDate = null) {
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "User.store_id =$storeID AND User.is_deleted=0 AND User.is_active=1 AND User.role_id IN (4,5)";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);

            $stratdate = date('Y-m-d 00:00:00', strtotime($stratdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));

            $criteria.= " AND WEEK(User.created) >=WEEK('" . $stratdate . "') AND WEEK(User.created) <=WEEK('" . $enddate . "')";
        }


        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $userdetail = $this->paginate('User');

        return $userdetail;
    }

    /*     * ***********************
     * Function name:weekuserexcelListing()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function weekuserexcelListing($startDate = null, $endDate = null) {
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "User.store_id =$storeID AND User.is_deleted=0 AND User.is_active=1 AND User.role_id=4";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND WEEK(User.created) >=WEEK('" . $startDate . "') AND WEEK(User.created) <=WEEK('" . $endDate . "')";
        }

        $userdetail = $this->User->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC')));

        return $userdetail;
    }

    /*     * ***********************
     * Function name:userexcelListing()
      Description:graph user list
      created:05/10/2015
     *
     * ********************* */

    public function userexcelListing($startDate = null, $endDate = null) {
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "User.store_id =$storeID AND User.is_deleted=0 AND User.is_active=1 AND User.role_id=4";
        if ($startDate && $endDate) {
            $stratdate = $this->Dateform->formatDate($startDate);
            $enddate = $this->Dateform->formatDate($endDate);
            $criteria.= " AND (User.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }

        $userdetail = $this->User->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC')));

        return $userdetail;
    }

    /*     * ***********************
     * Function name:orderProductListingweek()
      Description:graph order product list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductListingweek($startDate = null, $endDate = null, $item = null,$endYear=null) {

        $storeID = $this->Session->read('admin_store_id');
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
                'Order' => array('className' => 'Order', 'foreignKey' => 'order_id', 'fields' => array('id', 'order_number', 'seqment_id', 'order_status_id', 'user_id', 'amount', 'pickup_time', 'delivery_address_id', 'is_pre_order', 'created', 'coupon_discount')),
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

            $conditions = " Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0 AND Order.store_id=$storeID AND WEEK(Order.created) >=WEEK('" . $stratdate . "') AND WEEK(Order.created) <=WEEK('" . $enddate . "') AND YEAR(Order.created) ='" . $endYear . "'";
        } else {
            $conditions = array('Order.store_id' => $storeID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        }
        if (!empty($item)) {

            $conditions .=" AND OrderItem.item_id =$item";
        }

        $this->paginate = array('fields' => array('id', 'order_id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'user_id', 'created'), 'recursive' => 3, 'conditions' => array($conditions), 'order' => array('Order.created' => 'DESC'), 'group' => array('OrderItem.order_id'));
        $orderdetail = $this->paginate('OrderItem');
        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderProductListing()
      Description:graph order product list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductListing($startDate = null, $endDate = null, $item = null) {

        $storeID = $this->Session->read('admin_store_id');
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
                'Order' => array('className' => 'Order', 'foreignKey' => 'order_id', 'fields' => array('id', 'order_number', 'seqment_id', 'order_status_id', 'user_id', 'amount', 'pickup_time', 'delivery_address_id', 'is_pre_order', 'created', 'coupon_discount')),
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

            $conditions = array('Order.store_id' => $storeID, 'Order.created >=' => $startDate, 'Order.created <=' => $endDate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        } else {
            $conditions = array('Order.store_id' => $storeID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        }
        if (!empty($item)) {
            $conditions['OrderItem.item_id'] = $item;
        }

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


        $conditions = "Order.is_future_order=0 AND Order.is_active=1 AND Order.is_deleted=0 AND Order.store_id=$storeId AND WEEK(Order.created) >=WEEK('" . $start . "') AND WEEK(Order.created) <=WEEK('" . $end . "') AND YEAR(Order.created) ='" . $endYear . "'";

        if ($ordertype) {
            $conditions.=" AND Segment.id=$ordertype";
        }

        $result = $this->Order->find('all', array('fields' => array('WEEK(Order.created) AS WEEKno', 'DATE(Order.created) AS order_date', '`amount`-`coupon_discount` AS total'), 'conditions' => array($conditions)));
        return $result;
    }

    public function fetchWeeklyProductToday($storeId = null, $start = null, $end = null, $item = null,$endYear=null) {
        
        $this->OrderItem->bindModel(array('belongsTo' => array('Order')));
        $conditions = " Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0 AND Order.store_id=$storeId AND WEEK(Order.created) >=WEEK('" . $start . "') AND WEEK(Order.created) <=WEEK('" . $end . "') AND YEAR(Order.created) ='" . $endYear . "'";

        if (!empty($item)) {
            $conditions .=" AND OrderItem.item_id =$item";
        }
        $result = $this->OrderItem->find('all', array('fields' => array('WEEK(Order.created) AS WEEKno', 'DATE(Order.created) AS order_date', 'OrderItem.quantity'), 'conditions' => $conditions));
        return $result;
    }

    public function fetchWeeklyUserToday($storeId = null, $start = null, $end = null) {

        $conditions = " User.is_active=1 AND User.is_deleted=0 AND User.store_id=$storeId AND WEEK(User.created) >=WEEK('" . $start . "') AND WEEK(User.created) <=WEEK('" . $end . "') AND User.role_id IN (4,5)";

        $result = $this->User->find('all', array('group' => array('User.created'), 'fields' => array('WEEK(User.created) AS WEEKno', 'DATE(User.created) AS order_date', 'COUNT(User.id) as total'), 'conditions' => $conditions));

        return $result;
    }

    public function orderListings($startDate = null, $endDate = null, $ordertype = null) {
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_future_order=0";
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
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $sdate = $this->storeDate." "."00:00:00";
        $edate = $this->storeDate." "."23:59:59";
        $type = 1;
        $item = "";
        $startdate = $this->storeDate;
        $enddate = $this->storeDate;
        $expoladDate=  explode("-", $startdate);
        $Month = $expoladDate[1];
        $Year = $expoladDate[0];
        $yearFrom = date('Y', strtotime('-1 year',strtotime($Year)));
        $yearTo = $Year;
        $dateFrom = date('Y-m-d', strtotime('last Sunday', strtotime($startdate)));
        $dateTo = date('Y-m-d', strtotime('next saturday', strtotime($dateFrom)));
        
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

                $orderlist = $this->itemListing($startdate, $enddate, $item);
                // echo '<pre>';print_r($orderlist);die;
                $order = $this->orderProductListing($startdate, $enddate, $item);
                $result = $orderlist;
                $startdate = $this->Dateform->formatDate($startdate);
                $enddate = $this->Dateform->formatDate($enddate);
                $this->request->data['Item']['id'] = $item;

                $paginationdata = array('startdate' => $startdate, 'enddate' => $enddate, 'type' => 1, 'item' => $item);
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

                $result1 = $this->fetchWeeklyProductToday($storeId, $startFrom, $endFrom, $item,$endYear);

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

                $order = $this->orderProductListingweek($startFrom, $endFrom, $item,$endYear);
                //  echo '<pre>';print_r($order);die;

                $startFrom = $this->Dateform->formatDate($startFrom);
                $endFrom = $this->Dateform->formatDate($endFrom);
                $startdate = $startFrom;
                $enddate = $endFrom;
                $paginationdata = array('date_start_from' => $startFrom, 'date_end_from' => $endFrom, 'type' => 2);
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

                $orderlist = $this->itemListing($dateFrom, $dateTo, $item);
                $order = $this->orderProductListing($dateFrom, $dateTo, $item);
                $result = $orderlist;
                $this->request->data['Item']['id'] = $item;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 3);

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
                $orderlist = $this->itemListing($dateFrom, $dateTo, $item);
                $order = $this->orderProductListing($dateFrom, $dateTo, $item);
                $result = $orderlist;
                $this->request->data['Item']['id'] = $item;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 4);

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
                $orderlist = $this->itemListing(null, null, $item);
                $order = $this->orderProductListing(null, null, $item);
                $result = $orderlist;
                $this->request->data['Item']['id'] = $item;
                $paginationdata = array('startdate' => $dateFrom, 'enddate' => $dateTo, 'type' => 5);
                $this->set(compact('item', 'result', 'date', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata', 'order'));
            }
        } else {
            $item = '';
            $orderlist = $this->itemListings($sdate, $edate, $item);
            $order = $this->orderProductListings($sdate, $edate, $item);
            $result = $orderlist;
            $paginationdata = array('startdate' => $sdate, 'enddate' => $edate, 'type' => 1, 'item' => null);
            $this->set(compact('order', 'result', 'paginationdata'));
        }
        $this->loadModel('Item');
        $itemList = $this->Item->getallItemsByStore($storeId);
        $this->set('categoryList', $itemList);
        $this->set(compact('item', 'startdate', 'enddate', 'type', 'dateFrom', 'dateTo', 'Month', 'Year', 'yearFrom', 'yearTo', 'paginationdata'));
    }

    /*     * ***********************
     * Function name:orderProductListings()
      Description:graph order product list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductListings($startDate = null, $endDate = null, $item = null) {

        $storeID = $this->Session->read('admin_store_id');
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
                'Order' => array('className' => 'Order', 'foreignKey' => 'order_id', 'fields' => array('id', 'order_number', 'seqment_id', 'order_status_id', 'user_id', 'amount', 'pickup_time', 'delivery_address_id', 'is_pre_order', 'created', 'coupon_discount')),
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
            $conditions = array('Order.store_id' => $storeID, 'Order.created >=' => $stratdate, 'Order.created <=' => $enddate, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        } else {
            $conditions = array('Order.store_id' => $storeID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        }

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

    public function itemListings($startDate = null, $endDate = null, $itemId = null) {

        $storeID = $this->Session->read('admin_store_id');
        $criteria = "OrderItem.store_id =$storeID AND OrderItem.is_deleted=0 AND OrderItem.is_active=1";

        if ($startDate && $endDate) {

            $criteria.= " AND (OrderItem.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
        }

        if ($itemId) {
            $criteria.=" AND OrderItem.item_id=$itemId";
        }
        $orderdetail = $this->OrderItem->find('all', array('fields' => array('DATE(created) AS order_date', 'Count(OrderItem.created) AS number'), 'group' => array("DATE_FORMAT(OrderItem.created, '%Y-%m-%d')"), 'conditions' => array($criteria), 'order' => array('OrderItem.created' => 'DESC')));
        return $orderdetail;
    }

    public function productReportDownload($type = null, $startdate = null, $enddate = null, $Month = null, $Year = null, $yearFrom = null, $yearTo = null, $item = null) {
        $sdate = date('Y-m-d 00:00:00');
        $edate = date('Y-m-d 23:59:59');
        $storeId = $this->Session->read('admin_store_id');
        if ($type == 1) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderProductexcelListing($startdate, $enddate);
            $text = 'Daily_Report';
        } else if ($type == 2) {
            $startdate = date('Y-m-d 00:00:00', strtotime($startdate));
            $enddate = date('Y-m-d 23:59:59', strtotime($enddate));
            $order = $this->orderProductexcelListingweek($startdate, $enddate);
            $text = 'Weekly_Report';
        } else if ($type == 3) {
            $dateFrom = $Year . '-' . $Month . '-01';
            $dateTo = $Year . '-' . $Month . '-31';
            $order = $this->orderProductexcelListing($dateFrom, $dateTo);
            $text = 'Monthly_Report';
        } else if ($type == 4) {
            $dateFrom = $yearFrom . '-' . '01' . '-01';
            $dateTo = $yearTo . '-' . '12' . '-31';
            $order = $this->orderProductexcelListing($dateFrom, $dateTo);
            $text = 'Yearly_Report';
        } else {
            $order = $this->orderProductexcelListing(null, null);
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
     * Function name:orderProductexcelListing()
      Description:graph order product excel list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductexcelListing($startDate = null, $endDate = null) {

        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
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

        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));

        return $orderdetail;
    }

    /*     * ***********************
     * Function name:orderProductexcelListingweek()
      Description:graph order product excel list
      created:22/09/2015
     *
     * ********************* */

    public function orderProductexcelListingweek($startDate = null, $endDate = null) {

        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Order.store_id =$storeID AND Order.is_deleted=0 AND Order.is_active=1 AND Order.is_future_order=0";
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

        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC')));

        return $orderdetail;
    }

    /* ------------------------------------------------
      Function name:orderHistory()
      Description:Display the customer all orders
      created:18/8/2015
      ----------------------------------------------------- */

    public function orderHistory($EncryptCustomerID = null, $start = null, $end = null, $type = null, $page = null) {
        //echo $EncryptCustomerID;echo '<br>';echo $start;echo '<br>';echo $end;echo '<br>';echo $type; echo '<br>';echo $page;die;
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('OrderOffer');

        $this->loadModel('Order');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderItem');
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);

        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('name')))), false);
        $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'User' => array('fields' => array('fname', 'lname', 'email', 'phone', 'country', 'city', 'state', 'address')), 'OrderStatus' => array('fields' => array('name')))), false);

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
            $this->redirect(array('controller' => 'reports', 'action' => 'customerReport', $startparam, $endparam, $typeparam, $pageparam));
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

    public function imageGallary() {

        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $user_id = AuthComponent::User('id');
        $this->loadModel('StoreReviewImage');
        $encrypted_storeId = $this->Encryption->decode($storeId);
        $encrypted_merchantId = $this->Encryption->decode($merchantId);
        $storeReviewImages = $this->StoreReviewImage->find('all', array('conditions' => array('StoreReviewImage.store_id' => $storeId, 'StoreReviewImage.store_review_id' => 0, 'StoreReviewImage.is_deleted' => 0)));
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId', 'user_id', 'storeReviewImages'));

        if ($this->data) {
            $data = $this->data;
            if (!empty($data['StoreReviewImage']) && $data['StoreReviewImage']['image'][0]['error'] == 0) {
                $response = $this->Common->checkImageExtensionAndSize($data['StoreReviewImage']);
                if (empty($response['status']) && !empty($response['errmsg'])) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect($this->referer());
                }
            }
            $data['StoreReview']['store_id'] = $storeId;
//            pr($data);
//            die;
            if (!empty($data['StoreReviewImage']) && $data['StoreReviewImage']['image'][0]['error'] == 0) {
                $this->_uploadStoreReviewImages($data);
                $this->Session->setFlash(__('Upload Successfully.'), 'alert_success');
            }
            $this->redirect(array('controller' => 'reports', 'action' => 'imageGallary'));
        }
    }

    private function _uploadStoreReviewImages($data = null) {
        if (!empty($data)) {
            $this->loadModel('StoreReviewImage');
            //prx($data['StoreReviewImage']['image']);
            foreach ($data['StoreReviewImage']['image'] as $image) {
                if ($image['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($image, '/storeReviewImage/', $data['StoreReview']['store_id'], 300, 190);
                } elseif ($image['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if ($response['imagename']) {
                    $imageData['image'] = $response['imagename'];
                    $imageData['store_id'] = $data['StoreReview']['store_id'];
                    $imageData['created'] = date("Y-m-d H:i:s");
                    $imageData['is_active'] = 1;
                    $imageData['store_review_id'] = 0;

                    $this->StoreReviewImage->saveStoreReviewImage($imageData);
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name:activateItem()
      Description:Active/deactive items
      created:4/10/2016
      ----------------------------------------------------- */

    public function activateGallaryImage($EncrypteditemID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $this->loadModel('StoreReviewImage');
        $data['StoreReviewImage']['store_id'] = $this->Session->read('admin_store_id');
        $data['StoreReviewImage']['id'] = $this->Encryption->decode($EncrypteditemID);
        $data['StoreReviewImage']['is_active'] = $status;
        if ($this->StoreReviewImage->save($data)) {
            if ($status) {
                $SuccessMsg = "Image Activated";
            } else {
                $SuccessMsg = "Image Deactivated";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'reports', 'action' => 'imageGallary'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'reports', 'action' => 'imageGallary'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteItem()
      Description:Delete item
      created:4/10/2016
      ----------------------------------------------------- */

    public function deleteGallaryImage($EncryptGallaryImageID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $this->loadModel('StoreReviewImage');
        $data['StoreReviewImage']['store_id'] = $this->Session->read('admin_store_id');
        $data['StoreReviewImage']['id'] = $this->Encryption->decode($EncryptGallaryImageID);
        $data['StoreReviewImage']['is_deleted'] = 1;
        if ($this->StoreReviewImage->save($data)) {
            $this->Session->setFlash(__("Image deleted"), 'alert_success');
            $this->redirect(array('controller' => 'reports', 'action' => 'imageGallary'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'reports', 'action' => 'imageGallary'));
        }
    }

}
