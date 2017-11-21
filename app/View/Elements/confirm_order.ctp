<?php
echo $this->Html->script('store_admin/bootstrap');
 
$orderconfirm=false;
if($this->Session->check('Message.order_confirm') || $this->Session->check('Message.link_used')){
     $orderconfirm=true;
}
?>

<style>
.modal-dialog {    
    margin: 0 auto;
    width:25%; 
    position:relative; 
    z-index:41;
/*    top: 32%; 
    left:10%;    */
}
.modal-body{
    color:#000000;
}
.modal-content {
    background-color:#F1592A;
}    
.modal{
    top: 32% !important; 
}
</style>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">        
    <!-- Modal content-->
        <div class="modal-content">
            <p>
                <a href="#" style="float:right;font-weight:bold;
                                  padding:0px 2px 0 0;color:#000000;" data-dismiss="modal" >X</a>
            </p>
            <p>
                <div class="modal-body" style="font-weight: bold;text-align: center;">

                      <?php
                      if($this->Session->check('Message.order_confirm')){
                          echo $this->Session->flash('order_confirm');
                      }elseif($this->Session->check('Message.link_used')){
                          echo $this->Session->flash('link_used');
                      }
                      ?>            
                </div>  
            </p>
        </div>        
  </div> 
</div>


<script>
    
     $(window).load(function(){
         var orderconfirm = "<?php echo $orderconfirm;?>";
        
        if(orderconfirm){        
            $('#myModal').modal('show');        
        }
           
     });
</script>