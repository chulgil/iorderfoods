<?php

if (DESIGN == 4) {
    echo $this->element('design/oldlayout/footer');
} else {
    echo $this->element('design/common/footer');
}
?>