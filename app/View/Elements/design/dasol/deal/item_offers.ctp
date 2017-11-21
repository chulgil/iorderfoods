<?php if (!empty($itemOfferData)) { ?>
    <?php
    foreach ($itemOfferData as $data) {
        ?>
        <div class="deal-wrap">
            <?php if (in_array(KEYWORD, array('IOF-D2-H', 'IOF-D2-V', 'IOF-D4-H', 'IOF-D4-V','IOF-D3-V','IOF-D3-H'))) { ?>
                <a href="javascript:void(0);" class="deal-domo">
                    <div class="deal-img-wrap">
                        <?php
                        if (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) {
                            $imageUrl = "/MenuItem-Image/deals-images/" . $data['Item']['image'];
                        } else {
                            $imageUrl = "img/deals-item.jpg";
                        }
                        ?>
                        <img src="<?php echo $imageUrl; ?>" alt="deals-img">
                        <div class="deal-overlay">
                            <div class="deal-content">
                                <div class="deal-table">
                                    <div class="deal-cell">
                                        <span><?php echo $data['Item']['name']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="deals-detail clearfix">
                        <p>
                            <?php 
                            
                            $numSurfix = $this->Common->addOrdinalNumberSuffix($data['ItemOffer']['unit_counter']);
                            if(!empty($numSurfix)){
                                 echo "Buy " . ($data['ItemOffer']['unit_counter']-1) . " unit and get the ". $numSurfix." Item free on " . $data['Item']['name'].'.';
                             }else{
                                echo "Buy " . ($data['ItemOffer']['unit_counter']-1) . " get 1 free.";
                             }
                            
                            
                            
                            //echo 'On the Purchase of ' . ($data['ItemOffer']['unit_counter']-1) . ' units of ' . $data['Item']['name'] . ' , 1 unit will be free of cost.';
                            
                            
                            
                            ?>
                        </p>
                    </div>
                </a>
            <?php } else { ?>
                <div class="deal-img-wrap">
                    <?php
                    if (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) {
                        $imageUrl = "/MenuItem-Image/deals-images/" . $data['Item']['image'];
                    } else {
                        $imageUrl = "img/deals-item.jpg";
                    }
                    ?>
                    <div class="deal-overlay" style="background-image:url(<?php echo $imageUrl; ?>)">
                        <div class="deal-content">
                            <p><?php echo $data['Item']['name']; ?></p>
                            <div class="deal-table">
                                <div class="deal-cell">
                                    <h4><?php 
                                    
                                    $numSurfix = $this->Common->addOrdinalNumberSuffix($data['ItemOffer']['unit_counter']);
                                    if(!empty($numSurfix)){
                                         echo "Buy " . ($data['ItemOffer']['unit_counter']-1) . " unit and get the ". $numSurfix." Item free on " . $data['Item']['name'].'.';
                                     }else{
                                        echo "Buy " . ($data['ItemOffer']['unit_counter']-1) . " get 1 free.";
                                    }
                                    
                                    //echo 'On the Purchase of ' . ($data['ItemOffer']['unit_counter']-1) . ' units of ' . $data['Item']['name'] . ' , 1 unit will be free of cost.';
                                    
                                    
                                    
                                    ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php
    }
}
?>