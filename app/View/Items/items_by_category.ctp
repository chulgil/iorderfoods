<?php
if($itemList){     
   echo $this->Form->input('Topping.item_id',array('type'=>'select','class'=>'form-control valid multiOnly','label'=>'','div'=>false,'autocomplete' => 'off','multiple'=>true,'options'=>$itemList,'required'=>true)); 
}else{
   echo "No Item Available";   
}
?>
<script>
$(document).ready(function() {
    $('.multiOnly').multiselect();    
});
</script>