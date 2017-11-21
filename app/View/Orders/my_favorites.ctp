<?php

if (DESIGN == 4) {
    echo $this->element('design/oldlayout/innerpage/my_favorites');
} else {
    echo $this->element('design/common/my_favorites');
}
?>

