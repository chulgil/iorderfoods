<?php
if(!empty($itemList)){     
   echo $this->Form->input('Item.id',array('type'=>'select','class'=>'form-control valid','label'=>'','div'=>false,'autocomplete' => 'off','empty'=>'Select Item','options'=>@$itemList,'multiple'=>false));
}else{
   echo "No Item Available";   
}
?>