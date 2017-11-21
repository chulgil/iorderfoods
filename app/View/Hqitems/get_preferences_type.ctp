<?php

echo $this->Html->script('bootstrap-multiselect');
echo $this->Html->css('bootstrap-multiselect');
?>
<?php

echo $this->Form->input('Type.id', array('type' => 'select', 'class' => 'form-control multiOnly valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $typeList, 'multiple' => true));
echo $this->Form->error('Type.id');
?>
<script>
    $('.multiOnly').multiselect();
</script>