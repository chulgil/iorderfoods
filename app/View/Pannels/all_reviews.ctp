<?php

if (DESIGN == 1) {
    echo $this->element('design/aaron/review');
} elseif (DESIGN == 2) {
    echo $this->element('design/chloe/review');
} elseif (DESIGN == 3) {
    echo $this->element('design/dasol/review');
} elseif (DESIGN == 4) {
    echo $this->element('design/oldlayout/innerpage/all_reviews');
}
?>

