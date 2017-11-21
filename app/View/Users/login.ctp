<?php

if (DESIGN == 1) {
    echo $this->element('design/aaron/home');
} elseif (DESIGN == 2) {
    echo $this->element('design/chloe/home');
} elseif (DESIGN == 3) {
    if (in_array(KEYWORD, array('IOF-D2-H', 'IOF-D2-V', 'IOF-D4-H', 'IOF-D4-V'))) {
        echo $this->element('design/dasol/home_d2');
    } elseif (KEYWORD == 'IOF-D3-H' || KEYWORD == 'IOF-D3-V') {
        echo $this->element('design/dasol/home_d3');
    } else {
        echo $this->element('design/dasol/home');
    }
} elseif (DESIGN == 4) {
    echo $this->element('design/oldlayout/login');
}
?>