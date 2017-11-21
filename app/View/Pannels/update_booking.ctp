<?php
if (DESIGN == 4) {
    echo $this->element('design/oldlayout/innerpage/update_bookings');
} else {
    echo $this->element('design/common/update_bookings');
}
?>