<?php

if (DESIGN == 4) {
    echo $this->element('design/oldlayout/element/item-offer');
} else {
    echo $this->element('design/common/item-offer');
}
?>