<?php
echo $this->Form->input('Size.category_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$categoryList, 'empty' => 'Select Category'));
echo $this->Form->error('Sizes.category_id');
?>