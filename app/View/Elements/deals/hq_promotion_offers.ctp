<?php
if (!empty($promotionalOfferData)) {
    $bgClass = array('maroon-bg', 'maroon-bg', 'maroon-bg', 'maroon-bg', 'maroon-bg', 'maroon-bg');
    $i = 0;
    foreach ($promotionalOfferData as $data) {
        if ($i > 5) {
            $i = 0;
        }
        ?>
        <div class="col-xs-6 col-sm-4">
            <div class="promotion-box">
                <div class="p-top <?php echo $bgClass[$i]; ?>">
                    <h3><?php 
                    
                    if (strlen($data['Offer']['description']) > 30) {
                        echo substr($data['Offer']['description'], 0, 30) . "...";
                    } else {
                        echo $data['Offer']['description'];
                    }
                    if (!empty($data['Offer']['description'])) {
                        ?>
                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="tooltip" title="<?php echo $data['Offer']['description']; ?>"></span>
                    <?php
                            }
                    
                     ?></h3>
                    <p class="promotion-box-map"><i class="fa fa-map-marker"></i> <?php echo @$data['Store']['city']; ?></p>
                </div>
                <div class="p-mid">
                    <?php if (!empty($data['Offer']['offerImage']) && file_exists(WWW_ROOT . '/Offer-Image/thumb/' . $data['Offer']['offerImage'])) { ?>
                        <img src="/Offer-Image/thumb/<?php echo $data['Offer']['offerImage']; ?>" alt="deals-img">
                        <?php
                    } elseif (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) {
                        ?>
                        <img src="/MenuItem-Image/deals-images/<?php echo $data['Item']['image']; ?>" alt="deals-img">
                    <?php } else { ?>
                        <img src = "img/no_images.jpg" alt = "deals-img">
                    <?php } ?>
                </div>
                <div class="p-bottom <?php echo $bgClass[$i]; ?>">
                    <span class="promocode"><?php echo $data['Item']['name']; ?></span>
                    <?php if (!empty($data['Offer']['offer_start_date'])) { ?>
                        <p><?php echo $data['Offer']['offer_start_date'] . ' to ' . $data['Offer']['offer_end_date']; ?></p>
                    <?php } else { ?>
                        <p>&nbsp;</p>
                    <?php } ?>
<!--                    <h3><?php
                        if (strlen($data['Offer']['description']) > 30) {
                            echo substr($data['Offer']['description'], 0, 30) . "...";
                        } else {
                            echo $data['Offer']['description'];
                        }
                        ?> 
                        
                    </h3>-->
                </div>
            </div>
        </div>
        <?php
        $i++;
    }
}
?>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>