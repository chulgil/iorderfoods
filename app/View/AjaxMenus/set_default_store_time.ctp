<!-- order type section start  -->
<?php 
if(isset($ReqorderType) && $ReqorderType==2){
    echo $this->element('orderLogin/order_pick_up');
}else{
    echo $this->element('orderLogin/order_delivery'); 
}
?>

<script>
    

</script>
