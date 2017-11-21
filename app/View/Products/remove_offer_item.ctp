<?php

if (DESIGN == 1) {
    echo $this->element('design/aaron/storeMenu/cart');
} elseif (DESIGN == 2) {
    echo $this->element('design/chloe/storeMenu/cart');
} elseif (DESIGN == 3) {
    echo $this->element('design/dasol/storeMenu/cart');
} elseif (DESIGN == 4) {
    echo $this->element('design/oldlayout/product/remove_offer_item');
}
?>




