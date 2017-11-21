<?php

if (DESIGN == 4) {
    echo $this->element('design/oldlayout/innerpage/my_orders');
} else {
    echo $this->element('design/common/my_orders');
}
?>

