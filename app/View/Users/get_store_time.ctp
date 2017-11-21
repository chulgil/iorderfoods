<?php

$TimeArr = array();
$todayDate = date("m-d-Y");
if (strtotime($selectedDate) == strtotime($todayDate)) {
    foreach ($time_range as $rangeKey => $rangeValue) {
        $flag = true;

        $hr24 = explode(':', $rangeKey);
        foreach ($storeBreak as $breakKey => $breakVlue) {
            if (strtotime($storeBreak[$breakKey]['start']) <= strtotime($rangeKey) && strtotime($storeBreak[$breakKey]['end']) >= strtotime($rangeKey)) {
                $flag = false;
            }
        }
        if ($flag) {
            $HrMin = explode(':', $rangeValue);
            $AmPm = explode(' ', $HrMin[1]);
            if (count($AmPm) > 1) {
                $TimeArr[$hr24[0]][] = $AmPm[0];
            } else {
                $TimeArr[$HrMin[0]][] = $HrMin[1];
            }
        }
    }
} else {
    foreach ($time_range as $key => $value) {
        $hr24 = explode(':', $key);
        if (in_array($value, $time_break)) {

        } else {
            $HrMin = explode(':', $value);
            $AmPm = explode(' ', $HrMin[1]);
            if (count($AmPm) > 1) {
                $TimeArr[$hr24[0]][] = $AmPm[0];
            } else {
                $TimeArr[$HrMin[0]][] = $HrMin[1];
            }
        }
    }
}
?>
<?php echo $this->element('time/time_display', array('TimeArr' => $TimeArr, 'AMPM' => $AmPm)); ?>
