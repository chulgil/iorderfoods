<?php if ($this->Session->check('Message.flash')) { ?>
    <script type="text/javascript">
        $("#MediaPlayer").ready(function () {
            $("html, body").delay(2000).animate({
                scrollTop: $('#MediaPlayer').offset().top
            }, 1000);
        });
    </script>
<?php } ?>
<?php $cartlink = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId; ?>
<?php
$EncryptStoreID = $this->Encryption->encode($store_data_app['Store']['id']);
$EncryptMerchantID = $this->Encryption->encode($store_data_app['Store']['merchant_id']);
?>
<div class="slider-bg">
    <div id="carousel" class="carousel slide carousel-fade">
        <ol class="carousel-indicators">
            <?php
            if (!empty($store_data_app['StoreGallery'])) {
                $i = 0;
                foreach ($store_data_app['StoreGallery'] as $gallery) {
                    if (!empty($gallery['image']) && file_exists(WWW_ROOT . '/sliderImages/' . $gallery['image'])) {
                        if ($i == 0) {
                            ?>
                            <li data-target="#carousel" data-slide-to="<?php echo $i; ?>" class="active"></li>
                            <?php
                        } else {
                            ?>
                            <li data-target="#carousel" data-slide-to="<?php echo $i; ?>"></li>
                            <?php
                        }
                    }
                    $i++;
                }
            }
            ?>
        </ol>
        <!-- Carousel items -->
        <div class="carousel-inner">
            <?php
            if (!empty($store_data_app['StoreGallery'])) {
                $i = 0;
                foreach ($store_data_app['StoreGallery'] as $gallery) {
                    if (!empty($gallery['image']) && file_exists(WWW_ROOT . '/sliderImages/' . $gallery['image'])) {
                        if ($i == 0) {
                            ?>
                            <div data-slide-no="<?php echo $i; ?>" class="item carousel-item active">
                                <img src="/sliderImages/thumb/<?php echo $gallery['image']; ?>" alt="banner">
                            </div>
                            <?php
                        } else {
                            ?>
                            <div data-slide-no="<?php echo $i; ?>" class="item carousel-item">
                                <img src="/sliderImages/thumb/<?php echo $gallery['image']; ?>" alt="banner">
                            </div>

                            <?php
                        }
                    }
                    $i++;
                }
            } else {
                ?>
                <div data-slide-no="0" class="item carousel-item active">
                <?php echo $this->Html->image('dasol/banner.jpg', array('alt' => 'Image')) ?>
                </div>
            <?php }
            ?>
        </div>
        <!-- Carousel nav -->
        <a class="carousel-control left" href="#carousel" data-slide="prev">‹</a>
        <a class="carousel-control right" href="#carousel" data-slide="next">›</a>
    </div>


    <div class="Slider-text">
        <a class="close" href="javascript:void(0);"></a>
        <h4>opening hours</h4>
        <?php
        $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
        foreach ($days as $key => $value) {
            ?>
            <em class="days">
    <?php echo $value; ?>
            </em>
            <em>
                <?php
                if ($availabilityInfo[$key]['StoreAvailability']['is_closed'] == 1) {
                    echo "Closed";
                } else {
                    echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['start_time']) . " - ";
                    if ($store_data['Store']['is_break_time'] == 1) {
                        if ($store_data['Store']['is_break1'] == 1) {
                            if ($availabilityInfo[$key]['StoreBreak']['break1_start_time'] != $availabilityInfo[$key]['StoreBreak']['break1_end_time']) {
                                echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_start_time']) . ",   ";
                                echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_end_time']) . " - ";
                            }
                        }
                        if ($store_data['Store']['is_break2'] == 1) {
                            if ($availabilityInfo[$key]['StoreBreak']['break2_start_time'] != $availabilityInfo[$key]['StoreBreak']['break2_end_time']) {
                                echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_start_time']) . ",   ";
                                echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_end_time']) . " - ";
                            }
                        }
                    }
                    echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['end_time']);
                }
                ?>
            </em>
<?php } ?>
        <span><i class="fa fa-phone" aria-hidden="true"></i>call Us <strong> @<?php echo $store_data_app['Store']['phone']; ?></strong></span>
    </div>
</div>

<div class="dasol-listing-item-wrap clearfix">
    <div class="main-container" id="MediaPlayer">
        <?php echo $this->Session->flash(); ?>
<?php if (!empty($deals)) { ?>
            <div class="dasol-liw">
                <h2>Deals</h2>
                <ul class="clearfix">
                    <?php
                    $dealsstr = '';
                    $j = 0;
                    if (!empty($couponsData)) {
                        foreach ($couponsData as $coupons) {
                            $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID), true);
                            $dealsstr.= "<li>";
                            if (!empty($coupons['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/thumb/' . $coupons['Coupon']['image'])) {
                                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/Coupon-Image/thumb/" . $coupons['Coupon']['image'];
                                $dealsstr.="'></a></div>";
                            } else {
                                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/img/no_images.jpg' alt='item'></a></div>";
                            }
                            $dealsstr.="<h3>" . $this->Html->link(ucfirst($coupons['Coupon']['name']) . '(' . $coupons['Coupon']['coupon_code'], $url) . ')' . "</h3><small>";
                            if ($coupons['Coupon']['discount_type'] == 2) {//for percentage
                                $dealsstr.=$this->Html->link($coupons['Coupon']['discount'] . '% off on total order amount.', $url);
                            } else {
                                $dealsstr.=$this->Html->link('$' . $coupons['Coupon']['discount'] . ' off on total order amount.', $url);
                            }

                            $dealsstr.="</small></li>";
                            $j++;
                        }
                    }


                    if (!empty($itemOfferData)) {
                        foreach ($itemOfferData as $itemOffer) {
                            $EncryptItemId = $this->Encryption->encode($itemOffer['ItemOffer']['item_id']);
                            $EncryptCatId = $this->Encryption->encode($itemOffer['ItemOffer']['category_id']);
                            $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
                            $dealsstr.= "<li>";
                            if (!empty($itemOffer['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/thumb/' . $itemOffer['Item']['image'])) {
                                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/thumb/" . $itemOffer['Item']['image'];
                                $dealsstr.="'></a></div>";
                            } else {
                                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/img/no_images.jpg'></a></div>";
                            }

                            $dealsstr.="<h3>" . $this->Html->link(ucfirst($itemOffer['Item']['name']), $url) . "</h3>";
                            $numSurfix = $this->Common->addOrdinalNumberSuffix($itemOffer['ItemOffer']['unit_counter']);
                            if(!empty($numSurfix)){
                                $dealsstr.=  "<small>".$this->Html->link("Buy " . ($itemOffer['ItemOffer']['unit_counter']-1) . " unit and get the ". $numSurfix." Item free on " . $itemOffer['Item']['name'].'.',$url)."</small>";
                            }else{
                                $dealsstr.=  "<small>".$this->Html->link("Buy " .($itemOffer['ItemOffer']['unit_counter']-1) . " get 1 free.",$url)."</small>";
                            }
                            
                            
                            
                            //$dealsstr.= "<small>" . $this->Html->link("On the Purchase of " . $itemOffer['ItemOffer']['unit_counter'] . " units of " . $itemOffer['Item']['name'] . " , 1 unit will be free of cost.", $url) . "</small>";
                            $dealsstr.="</li>";
                            $j++;
                        }
                    }

                    if (!empty($promotionalOfferData)) {
                        foreach ($promotionalOfferData as $promotional) {
                            $EncryptItemId = $this->Encryption->encode($promotional['Offer']['item_id']);
                            $EncryptCatId = $this->Encryption->encode($this->common->getCategoryID($promotional['Offer']['item_id']));
                            $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
                            $dealsstr.= "<li>";
                            if (!empty($promotional['offerImage']['image']) && file_exists(WWW_ROOT . '/Offer-Image/thumb/' . $promotional['offerImage']['image'])) {
                                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/Offer-Image/thumb/" . $promotional['Offer']['offerImage'];
                                $dealsstr.="'></a></div>";
                            } elseif (!empty($promotional['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $promotional['Item']['image'])) {

                                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/deals-images/" . $promotional['Item']['image'];
                                $dealsstr.="'></a></div>";
                            } else {
                                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src = '/img/no_images.jpg' alt = 'deals-img'></a></div>";
                            }

                            $dealsstr.="<h3>" . $this->Html->link(ucfirst($promotional['Item']['name']), $url) . "</h3>";
                            $dealsstr.= "<small> - " . $this->Html->link($promotional['Offer']['description'], $url) . "</small>";
                            $dealsstr.="</li>";
                        }
                    }
                    echo $dealsstr;
                    ?>
                </ul>
            </div>
        <?php } ?>
        <?php if (!empty($feturedData)) { ?>
            <?php
            foreach ($feturedData as $key => $fData) {
                if (!empty($fData['FeaturedItem'])) {
                    ?>
                    <div class="dasol-liw">
                        <h2><?php echo ucfirst($fData['StoreFeaturedSection']['featured_name']); ?></h2>
                        <ul class="clearfix">
                            <?php
                            $i = 0;
                            foreach ($fData['FeaturedItem'] as $fItem) {
                                if (!empty($fItem['Item']['name'])) {
                                    if ($fItem['Item']['is_seasonal_item'] == 1) {
                                        if (strtotime($fItem['Item']['end_date']) < strtotime($currentDate)) {
                                            continue;
                                        }
                                    }
                                    $EncryptItemId = $this->Encryption->encode($fItem['item_id']);
                                    $EncryptCatId = $this->Encryption->encode($this->common->getCategoryID($fItem['item_id']));
                                    $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
                                    ?>
                                    <li>
                                        <div class="dasol-img-frame">
                                            <?php if (!empty($fItem['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/thumb/' . $fItem['Item']['image'])) { ?>
                                                <a href="<?php echo $url; ?>" class="img-item"><img src="/MenuItem-Image/thumb/<?php echo $fItem['Item']['image']; ?>" alt="item"></a>
                                                <?php } else { ?>
                                                <a href="<?php echo $url; ?>" class="img-item"><img src="/img/no_images.jpg" alt="item"></a>
                    <?php } ?>
                                        </div>
                                        <h3><?php echo $this->Html->link(ucfirst(@$fItem['Item']['name']), $url) ?></h3>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                }
            }
            ?>
<?php } ?>
    </div>
</div>