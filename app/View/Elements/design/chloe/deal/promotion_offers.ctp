<?php if (!empty($promotionalOfferData)) { ?>
    <?php
    $i = 0;
    foreach ($promotionalOfferData as $data) {
        ?>
        <div class="c-deal-wrap clearfix <?php
        if ($i % 2 == 1) {
            echo "c-odd-wrap";
        } $i++;
        ?>">
            <div class="deal-img">
                <?php if (!empty($data['Offer']['offerImage']) && file_exists(WWW_ROOT . '/Offer-Image/thumb/' . $data['Offer']['offerImage'])) { ?>
                    <img src="/Offer-Image/thumb/<?php echo $data['Offer']['offerImage']; ?>" alt="deals-img">
                    <?php
                } elseif (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) {
                    ?>
                    <img src="/MenuItem-Image/deals-images/<?php echo $data['Item']['image']; ?>" alt="deals-img">
                <?php } else { ?>
                    <img src = "img/deals-item.jpg" alt = "deals-img">
                <?php } ?>
            </div>
            <div class="deal-info">
                <h3><?php echo $data['Item']['name']; ?></h3>
                <p><?php echo substr($data['Offer']['description'], 0, 140); ?> <span class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="tooltip" title="<?php echo $data['Offer']['description']; ?>"></span></p>
            </div>
        </div>
    <?php } ?>
<?php } ?>