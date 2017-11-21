<style>
	.container { font-style: italic; float: left; width: 18.4%;  margin:0 0.8% 2%; padding:10px 8px 4px; border-radius:5px; background-color:#fff; font-size:12px; -webkit-box-shadow:px 2px 2px 0px rgba(0, 0, 0, 0.2); -moz-box-shadow:0px 2px 2px 0px rgba(0, 0, 0, 0.2); box-shadow:0px 2px 2px 0px rgba(0, 0, 0, 0.2); min-height:130px; }
	.container2 { float: none !important; width: auto !important;  min-height:250px; }
	.noeffect { list-style-type: none;  margin-right:10px; padding:1px; text-align: center; color:#777; }
	.noeffect li b { color:#ff3300; }
	.noeffect li a { color:#0D0D0D; }
	.noeffect li a:hover { color:#ff3300; } 
	.logoimg { font-size: 22px; } 
	
	@media (max-width: 767px) { .container { width:23.4%; } }
	@media (max-width: 640px) { .container { width:31.7%; min-height:130px; } }
	@media (max-width: 480px) { .container { width:48%; } }
	@media (max-width: 320px) { .container { min-height:165px; } }
	
</style>
<div class="content">
    <div id='contentChange' class="container">
        <div class="row">
           <div class="col-xs-12">
                <div class="updateOrdersData clearfix">
                    <!-- <h1 style="background: #222222;color:white;text-align: center;">Locations</h1> -->
                <?php if(!empty($store)){
                    foreach($store as $stor){ 
                        ?>
                    <div class="container">
                        <ul class="noeffect">
                            <?php $address = $stor['Store']['address'].'+'.$stor['Store']['city'].'+'.$stor['Store']['state'].'+'.$stor['Store']['zipcode'];
                            $address = str_replace(' ','+', $address); ?>
                            <li> <b><?php echo ucfirst($stor['Store']['store_name']);?></b>  </li>
                            <li> <?php echo ucwords($stor['Store']['address']);?>  </li>
                            <li> <?php echo ucfirst($stor['Store']['city']).', '.ucfirst($stor['Store']['state']).' '.$stor['Store']['zipcode'];?>  </li>
                            <li> <?php echo $stor['Store']['phone'];?> Phone </li>
                            <li> <a target='blank' href='http://<?php echo $stor['Store']['store_url'];?>'>Order Online</a> | 
                                <?php echo $this->Html->link('Map',array('controller'=>'hqusers','action'=>'storeLocation',$this->Encryption->encode($stor['Store']['id']),$stor['Store']['store_name']));?>
                                <?php  if(empty($stor['Store']['latitude'])){ ?>
<!--                                    <a target='blank' href="http://maps.google.com/maps?q=<?php //echo $address;?>">Map</a>-->
                                <?php //} else { ?>
<!--                                    <a target='blank' href="http://maps.google.com/maps?q=loc:<?php //echo $stor['Store']['latitude'];?>,<?php //echo $stor['Store']['logitude'];?>">Map</a>-->
                                <?php  } ?> |
                                <?php     $store_id= $this->Encryption->encode($stor['Store']['id']);
                                $merchant_id= $this->Encryption->encode($stor['Store']['merchant_id']);
                                echo $this->Html->link('View Menu', "/hqmenus/index/".$store_id."/".$merchant_id , array('escape' => false));  ?>
<!--                            </li>
                            <li> <a target='blank' href='http://<?php //echo $stor['Store']['store_url'];?>'>View Menu</a> | -->
                        </ul>
                    </div>
                    <?php } } else {
                        echo 'No Locations Found';
                    }?>
                </div>
            </div>	    
        </div>
    </div>
</div>

