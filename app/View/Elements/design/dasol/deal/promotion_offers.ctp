<?php if (!empty($promotionalOfferData)) { ?>
    <?php
    foreach ($promotionalOfferData as $data) {
        ?>
        <div class="deal-wrap">
            <?php if (in_array(KEYWORD, array('IOF-D2-H', 'IOF-D2-V', 'IOF-D4-H', 'IOF-D4-V','IOF-D3-V','IOF-D3-H'))) { ?>
                <a href="javascript:void(0);" class="deal-domo">
                    <div class="deal-img-wrap">
                        <?php
                        if (!empty($data['Offer']['offerImage']) && file_exists(WWW_ROOT . '/Offer-Image/thumb/' . $data['Offer']['offerImage'])) {
                            $imageUrl = "/Offer-Image/thumb/" . $data['Offer']['offerImage'];
                        } elseif (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) {
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
                            <?php echo substr($data['Offer']['description'], 0, 140); ?> <span class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="tooltip" title="<?php echo $data['Offer']['description']; ?>">
                        </p>
                    </div>
                </a>
            <?php } else { ?>
                <div class="deal-img-wrap">
                    <?php
                    if (!empty($data['Offer']['offerImage']) && file_exists(WWW_ROOT . '/Offer-Image/thumb/' . $data['Offer']['offerImage'])) {
                        $imageUrl = "/Offer-Image/thumb/" . $data['Offer']['offerImage'];
                    } elseif (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) {
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
                                    <h4><?php echo substr($data['Offer']['description'], 0, 140); ?> <span class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="tooltip" title="<?php echo $data['Offer']['description']; ?>"></h4>
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
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>