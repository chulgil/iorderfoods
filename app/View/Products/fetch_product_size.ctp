<?php
if (DESIGN == 4) {
    echo $this->element('design/oldlayout/product/fetch_product_size');
} else {
    echo $this->element('design/common/fetch_product_size');
}
?>