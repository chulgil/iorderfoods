<?php

if (DESIGN == 4) {
    if (empty($finalItem)) {

    } else {
        //echo $this->element('order-element');
        echo $this->element('design/oldlayout/element/order-element-calculation');
    }
} else {
    echo $this->element('orderoverview/check_delivery_type');
}
?>





