<?php
if($itemList){     
   echo $this->Form->input('ItemOffer.item_id',array('type'=>'select','class'=>'form-control valid','label'=>'','div'=>false,'autocomplete' => 'off','options'=>$itemList,'empty'=>'select Item')); 
}else{
   echo "No Item Available";   
}
?>
