<?php
echo $this->Form->input('SubPreference.type_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$storePreferences, 'empty' => 'Select Preferences'));
echo $this->Form->error('SubPreference.type_id');
?>