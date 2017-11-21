<?php
if($Itemslist){     
   echo $this->Form->input('Topping.item_id',array('type'=>'select','class'=>'form-control valid','label'=>'','div'=>false,'autocomplete' => 'off','options'=>$Itemslist,'multiple'=>false)); 
}else{
   echo "No Item Available";   
}
?>