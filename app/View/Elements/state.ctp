<?php
if (!empty($stateListArr)) {
    echo $this->Form->input('User.state_id', array('type' => 'select', 'options' => @$stateListArr, 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => "Select State"));
}?>