<?php

if (DESIGN == 4) {
    echo $this->element('design/oldlayout/innerpage/delivery_address');
} else {
    echo $this->element('design/common/delivery_address');
}
?>

