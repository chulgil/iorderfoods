<?php

echo $this->Form->input('StoreTax.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => @$storeTaxlist, 'empty' => 'Please Select Tax'));
?>