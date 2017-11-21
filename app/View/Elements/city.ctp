<?php
if (!empty($result)) {
    echo $this->Form->input('User.city_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$result, 'empty' => 'Select City'));
}?>