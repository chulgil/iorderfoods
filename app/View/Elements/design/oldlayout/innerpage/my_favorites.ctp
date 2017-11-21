<style>
    .btn{
        font-size: 14px;
    }
      .col-lg-4.filter{
        margin-left: -13px !important;
    }
    .blue{
       
        font-size: 12px
    }
     .paging_full_numbers { margin: 25px 0;padding: 0 25px;text-align: right;width: 100%;}
.spaceclear {
    float: left;
    width: 100%;
    margin-left:35px;    
    
}
</style>
<?php 
$storeId = $this->Session->read('store_id');

$url = HTTP_ROOT;?>
<?php $imageurl = HTTP_ROOT.'storeLogo/'.$store_data_app['Store']['store_logo'];?>
    <div class="pad-TP60">
		 <?php //echo $this->Session->flash(); ?>
         <div class="order-hostory form-layout clearfix">
<!--            <form name="select-order-type" method="post" action="javascript:void(0);">-->
                <h2><span><?php echo __('Favorite & Order History');?></span></h2>
                <div id="horizontalTab">
                    <!-- TABS -->
                    <ul class="resp-tabs-list clearfix">
                        <li class="active"><?php echo $this->Html->link(__('My Favorites'),array('controller'=>'orders','action'=>'myFavorites',$encrypted_storeId,$encrypted_merchantId));?></li>
                        <li><?php echo $this->Html->link(__('My Orders'),array('controller'=>'orders','action'=>'myOrders',$encrypted_storeId,$encrypted_merchantId));?></li>
                        <li><?php echo $this->Html->link(__('My Saved Orders'),array('controller'=>'orders','action'=>'mySavedOrders',$encrypted_storeId,$encrypted_merchantId));?></li>
                    
                    </ul>
                    <!-- /TABS -->
                    
                    <div class="spaceclear">
        <hr>
        <?php echo $this->Form->create('Orders', array('url' => array('controller' => 'orders', 'action' => 'myFavorites'), 'id' => 'AdminId', 'type' => 'post')); ?>
        <div class="row">
        <?php echo $this->element('userprofile/filter_store'); ?>
        <div class="col-lg-4">
        <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn green-btn')); ?>
        <?php echo $this->Html->link('Clear', array('controller' => 'orders', 'action' => 'myFavorites', 'clear'), array('class' => 'btn green-btn')); ?>
              </div>
         </div>
        <?php echo $this->Form->end(); ?>
    </div>
<div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
    
                <?php
                echo $this->Paginator->first('First');
                // Shows the next and previous links
                echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                // Shows the page numbers
                echo $this->Paginator->numbers(array('separator' => ''));
                echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                // prints X of Y, where X is current page and Y is number of pages
                //echo $this->Paginator->counter();
                echo $this->Paginator->last('Last');
                ?>
            </div>
                    <!-- FORM VIEW -->
                    <div class="resp-tabs-container">
						<?php if(!empty($myFav)){ foreach($myFav as $orders) {  ?>
                        <div class="repeat-deatil">    
                        	<div class="resp-tabs-frame">                                          
                                <div class="order-history-detail clearfix">
                                	<div class="order-history-detail-rt">
                                            <?php  $desc = '';
                                        $offers = '';
                                        $result = '';
                                        foreach($orders['Order']['OrderItem'] as $order){
                                            //$desc = $order['quantity'].' '.@$order['Size']['size'].' '.@$order['Type']['name'].' '.$order['Item']['name'];
                                            $desc = $order['quantity'];
                                            if(!empty($order['Size']['size'])){
                                               $desc.= ' ' . @$order['Size']['size'];
                                            }
                                            if(!empty($order['Type']['name'])){
                                               $desc.= ' ' . @$order['Type']['name'];
                                            }
                                            if(!empty($order['Item']['name'])){
                                               $desc.= ' ' . @$order['Item']['name'];
                                            }
                                            if(!empty($order['OrderOffer'])){ 
                                                foreach($order['OrderOffer'] as $offer){ 
                                                  $offers .=  $offer['quantity'].'X'.$offer['Item']['name'].'&nbsp;';
                                                }
                                            }
                                            if(!empty($offers)){
                                                $result .= $desc.' ( Offer : '.$offers.'), ';
                                            } else {
                                                $result .= $desc.', ';
                                            }
                                            $offers = '';
                                            $desc = '';
                                        }?>
                                            
<?php                    
            $strDomainUrl = $_SERVER['HTTP_HOST'];
            $strShareLink = "https://www.facebook.com/sharer/sharer.php?u=".$strDomainUrl;
        ?>
        <a href="#" onclick='window.open("<?php echo $strShareLink; ?>", "", "width=500, height=300");'>
            <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>
        </a>                                            
                                            
<!--                                                <a class='share_button' 
                                                    desc="I ordered <?php echo $result;?> from <?php echo $_SESSION['storeName'];?>" >
                                                 <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>   
                                                </a> -->
                                                <span style="display: inline-block; margin: 0px 5px; vertical-align: text-top;">
                                                <a target="blank" href= "http://twitter.com/share?text=I ordered <?php echo $result;?> from <?php echo $_SESSION['storeName'];?> &url=<?php echo $url; ?>&via=<?php echo $_SESSION['storeName']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
                                                </span>
                                        <?php 
                                         if (!empty($storeId)) {
                                             if ($orders['Favorite']['store_id'] == $storeId) {
                                        
                                        echo $this->Html->link($this->Html->tag('i','',array('class'=>'fa fa-cart-plus')).'Re-Order','javascript:void(0);',array('class'=>'reorder orderColor','name'=>$this->Encryption->encode($orders['Order']['id']),'escape'=>false));?>
                                        <span class="seprator-bar">|</span>
                                        <?php echo $this->Html->link($this->Html->tag('i','',array('class'=>'fa fa-times')).'Remove',array('controller'=>'orders','action'=>'myFavorite',$encrypted_storeId,$encrypted_merchantId,$this->Encryption->encode($orders['Order']['id']),$this->Encryption->encode($orders['Favorite']['id'])),array('class'=>'removeFrmFav orderColor','escape'=>false));
                                        
                                         }
                                         
                                                }
                                        ?>
                                    </div>
                                    
                                    <div class="order-history-detail-lt">
                                        <p> <span><?php echo __('Order Id:');?> <?php echo $orders['Order']['order_number'];?></span><span class="seprator-bar">|</span><span><?php echo __('Cost:');?> $<?php echo $orders['Order']['amount']-$orders['Order']['coupon_discount'];?> <?php echo ' - '.$orders['Order']['OrderStatus']['name'];?></span> </p>
                                        <p> <?php if($orders['Order']['seqment_id'] == 1){
                                            echo 'Dine-In';
                                        } elseif ($orders['Order']['seqment_id'] == 2) {
                                            echo 'PickUp';
                                        } elseif($orders['Order']['seqment_id'] == 3){
                                        echo $orders['Order']['DeliveryAddress']['name_on_bell'].', '.$orders['Order']['DeliveryAddress']['address'].' ,'.$orders['Order']['DeliveryAddress']['city']; }?></p>
                                        <p>Order Placed On: <?php //echo $this->Common->storeTimeFormate($this->Common->storeTimezone('',$orders['Order']['created']),true);?>
                                            <?php echo $this->Common->storeTimeFormateUser($orders['Order']['created'], true);?>
                                        </p>
                                    </div>
                                </div>

                                <div class="responsive-table">
                                    <table class="table table-striped order-history-table">
                                        <tr>
                                            <th style="width:30%;"><?php echo __('Items');?></th>
                                            <th style="width:15%;"><?php echo __('Size');?></th>
                                            <th style="width:20%;"><?php echo __('Preferences');?></th>
                                            <th style="width:15%;"><?php echo __('Add-ons');?></th>
                                            <th style="width:20%;"><?php echo __('Store'); ?></th>
    
                                        </tr>
                                                    <?php  foreach($orders['Order']['OrderItem'] as $order){ ?>
    
                                        <tr>
                                            <td><?php 
                                            
                                            $Interval="";
                                            if(isset($order['interval_id'])){
                                                $intervalId=$order['interval_id'];
                                                $Interval=$this->Common->getIntervalName($intervalId);                    
                                            }
                                            echo $order['quantity'].'X'.$order['Item']['name'];
                                            echo ($Interval)?"(".$Interval.")":"";
                                            
                                            
                                            ?><br>
                                            <?php if(!empty($order['OrderOffer'])){
                                                echo "<innerTag class='greyFont'>Offer Items :</innerTag>"; 
                                                foreach($order['OrderOffer'] as $offer){
                                                    echo '<br/>'.$offer['quantity'].'X'.$offer['Item']['name'];
                                                }
                                                
                                            }?>
                                            </td>
                                            <td><?php if(!empty($order['Size'])) { echo $order['Size']['size']; }else { echo ' - ';} ?></td>
                                            <td><?php
					    
					    
					   if(!empty($order['OrderPreference'])) {
						$preference="";
						$prefix = '';
						foreach($order['OrderPreference'] as $key =>$opre){			
							$preference .= $prefix . '' .$opre['SubPreference']['name']."";
							$prefix = ', ';
						}
						echo $preference;
						
					    } else { echo ' - '; } 
					    
					    
					    
					    
					    ?></td>
                                            <td> <?php if(!empty($order['OrderTopping'])) {
                                                    $prefix = ''; foreach($order['OrderTopping'] as $topping) {
						    if(!empty($topping['Topping']['name'])){
						       echo $prefix . '' .$topping['Topping']['name'] . '';
						       $prefix = ', ';
						    }
                                                    
                                            } } else {echo ' - '; }?>
    
                                                       </td>
                                        <td>
                                                    <?php
                                                    if (!empty($orders['Store'])) {
                                                        echo $orders['Store']['store_name'];
                                                    }
                                                    ?> </td>
                                        
                                        </tr><?php } ?>
    
                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php }} else { ?>
                        <div class="repeat-deatil">
                            <div class="responsive-table" style="margin-top:-1px;">
                                <table class="table table-striped order-history-table">
                                    <tr>
                                       <td style="text-align: center;"><?php echo __("No order has been added into favorite list");?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>   
                       <?php } ?>
                        
                        <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                <?php
                echo $this->Paginator->first('First');
                // Shows the next and previous links
                echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                // Shows the page numbers
                echo $this->Paginator->numbers(array('separator' => ''));
                echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                // prints X of Y, where X is current page and Y is number of pages
                //echo $this->Paginator->counter();
                echo $this->Paginator->last('Last');
                ?>
            </div>
                    </div>
                    <!-- /FROM VIEW -->
                </div>
            </form>
        </div>
    </div><div class="clr"></div>
 </div><!-- /right side end -->
        <div class="clearfix"></div>
        <?php echo $this->Html->css('pagination'); ?>
        <script>
    $(document).ready(function () {
        $('.reorder').click(function () {
            var orderId = $(this).attr('name');
            window.location = "/Users/customerDashboard/<?php echo $encrypted_storeId; ?>/<?php echo $encrypted_merchantId; ?>/" + orderId ;
         
        });
    });
        </script>
        <script>
            $(document).ready(function () {
                $("#MerchantStoreId").change(function () {
                    $("#AdminId").submit();
                });

            });
            
        </script>

