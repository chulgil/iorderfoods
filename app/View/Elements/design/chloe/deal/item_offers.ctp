<?php if (!empty($itemOfferData)) { ?>
    <?php
    $i = 0;
    foreach ($itemOfferData as $data) {
        ?>
        <div class="c-deal-wrap clearfix <?php
        if ($i % 2 == 1) {
            echo "c-odd-wrap";
        } $i++;
        ?>">
            <div class="deal-img">
                <?php if (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) { ?>
                    <img src="/MenuItem-Image/deals-images/<?php echo $data['Item']['image']; ?>" alt="deals-img">
                    <?php
                } else {
                    ?>
                    <img src = "img/deals-item.jpg" alt = "deals-img">
                <?php }
                ?>
            </div>
            <div class="deal-info">
                <h3><?php echo $data['Item']['name']; ?></h3>
                <?php 
                
                $numSurfix = $this->Common->addOrdinalNumberSuffix($data['ItemOffer']['unit_counter']);
                if(!empty($numSurfix)){
                     echo "<p>Buy " . ($data['ItemOffer']['unit_counter']-1) . " unit and get the ". $numSurfix." Item free on " . $data['Item']['name'].'.</p>';
                 }else{
                    echo "<p>Buy " . ($data['ItemOffer']['unit_counter']-1) . " get 1 free.</p>";
                 }
                 
                 
               // echo 'On the Purchase of ' . ($data['ItemOffer']['unit_counter']-1) . ' units of ' . $data['Item']['name'] . ' , 1 unit will be free of cost.';
                
                
                ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>