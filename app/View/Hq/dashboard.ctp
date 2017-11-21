<!-- user input entry form start here -->
<section class="ui-form ui-form-login">
    <h2>HQ Dashboard<br></h2>
    <hr>
   <?php echo $this->Session->flash();?>
<!--    <div class="row">
         <div class="col-lg-6">
             <?php echo $this->Form->create('', array('url'=>array('controller'=>'hq','action'=>'dashboard'),'inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'StoreList'));?>
            <?php                    
                $merchantList=$this->Common->getHQStores($merchantId);
                $storeId='';
                if($this->Session->read('selectedStoreId')){
                    $storeId=$this->Session->read('selectedStoreId');
                }
                echo $this->Form->input('Merchant.store_id',array('options'=>$merchantList,'class'=>'form-control','div'=>false,'empty'=>'Please Select Store','value'=>$storeId));
	    ?>
	    <span class="blue">(For Store related features, select a store to proceed.)</span>

            <?php echo $this->Form->end(); ?>
        </br>
        </div>
    </div>-->
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
   <div class="row">
    
                <div class="col-lg-6"><div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="text-center">
	         <?php $TotalOnlineCollection=$this->Common->getTotalStoreOnlineCollection($storeId);  ?>     

                                    <div class="huge"><?php
				    if(!empty($TotalOnlineCollection)){
					echo "$";
				    echo $TotalOnlineCollection ;
				    }else{
					 echo "0";
				    }
				    ?></div>
                                    <div class="fts20">Total Online Collection</div>
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
		      <?php $TotalCustomer=$this->Common->getTotalStoreCustomers($storeId);  ?>     

                                    <div class="huge"><?php
				    if(!empty($TotalCustomer)){
				    echo $TotalCustomer ;
				    }
				    else{
					echo "0";
				    }
				    ?></div>
                                    <div class="fts20">Total Customers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
    </div>
		
    </div>
   <?php }
   else{  ?>
    
    <div class="row">               
                <div class="col-lg-6"><a href="/hqstores"><div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="text-center">
		      <?php $TotalStore=$this->Common->getTotalHQStores();  ?>     

                                    <div class="huge"><?php  echo $TotalStore ; ?></div>
                                    <div class="fts20">Total Stores</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>

                <div class="col-lg-6"><a href="/hqcustomers">
                    <div class="panel1 panel-primary1">
                        <div class="panel-heading1">
                            <div class="row">
                                <div class="text-center">
		      <?php $TotalCustomer=$this->Common->getTotalHQCustomers();  ?>     

                                    <div class="huge"><?php  echo $TotalCustomer ; ?></div>
                                    <div class="fts20">Total Customers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                        </a>
                </div>
   </div>
   <div class="row">
    <div style="margin-left:300px;">
                <div class="col-lg-9"><div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="text-center">
	         <?php $TotalOnlineCollection=$this->Common->getTotalHQOnlineCollection();  ?>     

                                    <div class="huge">$<?php  echo $TotalOnlineCollection ; ?></div>
                                    <div class="fts20">Total Online Collection</div>
                                </div>
                            </div>
                        </div>
                    </div></div></div>
               
    </div>
    
<?php  }
   
   
   ?>
   
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
