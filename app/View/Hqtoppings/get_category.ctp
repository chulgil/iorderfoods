<?php
echo $this->Form->input('Category.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$categoryList, 'empty' => 'Select Category', 'id' => 'CategoryId'));
echo $this->Form->error('Category.category_id');
?>
