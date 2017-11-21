<div class="row">
   <div class="col-lg-13">
        <div class="updateOrdersData">
            <?php if($type == 1){ ?>
                <!-- <h1 style="background: #222222;color:white;text-align: center;">Locations</h1> -->
                <?php if(!empty($store)){
            foreach($store as $stor){ ?>
            <div class="container">
                <ul class="noeffect">
                    <?php $address = $stor['Store']['address'].'+'.$stor['Store']['city'].'+'.$stor['Store']['state'].'+'.$stor['Store']['zipcode'];
                    $address = str_replace(' ','+', $address); ?>
                    <li> <b><?php echo ucfirst($stor['Store']['store_name']);?></b>  </li>
                    <li> <?php echo ucwords($stor['Store']['address']);?>  </li>
                    <li> <?php echo ucfirst($stor['Store']['city']).', '.ucfirst($stor['Store']['state']).' '.$stor['Store']['zipcode'];?>  </li>
                    <li> <?php echo $stor['Store']['phone'];?> Phone </li>
                    <li> <a target='blank' href='http://<?php echo $stor['Store']['store_url'];?>'>Order Online</a> | 
                        <?php if(empty($stor['Store']['latitude'])){ ?>
                            <a target='blank' href="http://maps.google.com/maps?q=<?php echo $address;?>">Map</a>
                        <?php } else { ?>
                            <a target='blank' href="http://maps.google.com/maps?q=loc:<?php echo $stor['Store']['latitude'];?>,<?php echo $stor['Store']['logitude'];?>">Map</a>
                        <?php } ?>
                    </li>    
                </ul>
            </div>
            <?php } } else {
                echo 'No Locations Found';
            }?>
            <?php } elseif($type == 2) { ?>
                <!-- <h1 style="background: #222222;color:white;text-align: center;"><?php echo $content['MerchantContent']['name'];?></h1> -->
                <div class="container container2">
                       <?php echo $content['MerchantContent']['content'];?>
                </div>
            <?php } elseif($type == 3) {
                echo $this->Element('merchant_photo');
            }?> 
        </div>
    </div>	    
</div>

