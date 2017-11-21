<?php
if (!empty($itemOfferData)) {
    $bgClass = array('maroon-bg', 'maroon-bg', 'maroon-bg', 'maroon-bg', 'maroon-bg', 'maroon-bg');
    $i = 0;
    foreach ($itemOfferData as $data) {
        if ($i > 5) {
            $i = 0;
        }
        $numSurfix = $this->Common->addOrdinalNumberSuffix($data['ItemOffer']['unit_counter']);
        ?>
        <div class="col-xs-6 col-sm-4">
            <div class="promotion-box">
                <div class="p-top <?php echo $bgClass[$i]; ?>">
                    <h3>
                        <?php
                        if (!empty($numSurfix)) {
                            echo "<p>Buy " . ($data['ItemOffer']['unit_counter'] - 1) . ' ' . $data['Item']['name'] . " get " . $numSurfix . " free </p>";
                        } else {
                            echo "<p>Buy " . ($data['ItemOffer']['unit_counter'] - 1) . " get 1 free</p>";
                        }
                        ?>

                    </h3>
                    <p class="promotion-box-map"><i class="fa fa-map-marker"></i> <?php echo @$data['Store']['city']; ?></p>
                </div>

                <div class="p-mid">
                    <?php if (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) { ?>
                        <img src="/MenuItem-Image/deals-images/<?php echo $data['Item']['image']; ?>" alt="deals-img">
                        <?php
                    } else {
                        ?>
                        <img src = "img/no_images.jpg" alt = "deals-img">
                    <?php }
                    ?>
                </div>
                <div class="p-bottom <?php echo $bgClass[$i]; ?>">
                    <span class="promocode">
                        <?php
                        echo $data['Item']['name'];
                        ?>
                    </span>
                    <?php if (!empty($data['ItemOffer']['start_date'])) { ?>
                        <p><?php echo $data['ItemOffer']['start_date'] . ' to ' . $data['ItemOffer']['end_date']; ?></p>
                    <?php } else { ?>
                        <p>&nbsp;</p>
                    <?php } ?>
                    <h3>
                        <?php
                        if (!empty($numSurfix)) {
                            echo "Buy " . ($data['ItemOffer']['unit_counter'] - 1) . " unit and get the " . $numSurfix . " Item free on " . $data['Item']['name'];
                        } else {
                            echo "Buy " . ($data['ItemOffer']['unit_counter'] - 1) . " get 1 free.";
                        }
                        ?>
                    </h3>

                </div>
            </div>
        </div>
        <?php
        $i++;
    }
}
?>