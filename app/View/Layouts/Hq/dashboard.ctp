<!-- user input entry form start here -->
<section class="ui-form ui-form-login">
    <h2>HQ Dashboard<br></h2>
   <?php echo $this->Session->flash();?>
    <div class="row">
         <div class="col-lg-6">
             <?php echo $this->Form->create('', array('url'=>array('controller'=>'hq','action'=>'dashboard'),'inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'StoreList'));?>
            <?php                    
                $merchantList=$this->Common->getHQStores($merchantId);
                echo $this->Form->input('Merchant.store_id',array('options'=>$merchantList,'class'=>'form-control','div'=>false,'empty'=>'Please select Store'));
            ?>
            <?php echo $this->Form->end(); ?>
        </br>
        </div>
    </div>
    <?php if($storeId){?>
   <div class="row">               
                <div class="col-lg-6"><div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="text-center">
                                    <?php $todaysTotalOrders=$this->Common->getTodaysOrder($storeId);  ?>     
                                    <div class="huge"><?php echo $todaysTotalOrders; ?></div>
                                    <div class="fts20">Today's Orders</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="panel1 panel-primary1">
                        <div class="panel-heading1">
                            <div class="row">
                                <div class="text-center">
                                    <?php $todaysPendingOrders=$this->Common->getTodaysPendingOrder($storeId);  ?>        
                                    <div class="huge"><?php echo $todaysPendingOrders; ?></div>
                                    <div class="fts20">Pending Orders</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
   </div>
   <div class="row">
                
                <div class="col-lg-6"><div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="text-center">
                                    <?php $todaysbookingRequest=$this->Common->getTodaysBookingRequest($storeId);  ?>       
                                    <div class="huge"><?php echo $todaysbookingRequest;?></div>
                                    <div class="fts20">Today's Booking Requests</div>
                                </div>
                            </div>
                        </div>
                    </div></div>

                <div class="col-lg-6">
                    <div class="panel1 panel-primary1">
                        <div class="panel-heading1">
                            <div class="row">
                                <div class="text-center">
                                     <?php $todayspendingbooking=$this->Common->getTodaysPendingBookings($storeId);  ?>        
                                    <div class="huge"><?php echo $todayspendingbooking; ?></div>
                                    <div class="fts20">Pending Booking Requests</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
   <?php } ?>
   
</section><!-- /user input entry form end -->

<script>
    $(document).ready(function() {
        $("#MerchantStoreId").change(function(){
            var StoreId=$("#MerchantStoreId").val();
            //if(StoreId!="") {
                $("#StoreList").submit(); 
            //}
        });
			
    });
</script>
