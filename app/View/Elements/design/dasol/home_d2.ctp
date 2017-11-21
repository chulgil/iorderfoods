<div class="slider-bg">
    <div id="carousel" class="carousel slide carousel-fade">
        <ol class="carousel-indicators">
            <?php
            $EncryptStoreID = $this->Encryption->encode($store_data_app['Store']['id']);
            $EncryptMerchantID = $this->Encryption->encode($store_data_app['Store']['merchant_id']);
            ?>
            <?php
            if (!empty($store_data_app['StoreGallery'])) {
                $i = 0;
                foreach ($store_data_app['StoreGallery'] as $gallery) {
                    if (!empty($gallery['image']) && file_exists(WWW_ROOT . '/sliderImages/thumb/' . $gallery['image'])) {
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
                    if (!empty($gallery['image']) && file_exists(WWW_ROOT . '/sliderImages/thumb/' . $gallery['image'])) {
                        if ($i == 0) {
                            ?>
                            <div data-slide-no="<?php echo $i; ?>" class="item carousel-item active">
                                <img src="/sliderImages/thumb/<?php echo $gallery['image']; ?>" alt="banner">
                                <div class="carousel-caption">
                                    <p><?php echo $gallery['description']; ?></p>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div data-slide-no="<?php echo $i; ?>" class="item carousel-item">
                                <img src="/sliderImages/thumb/<?php echo $gallery['image']; ?>" alt="banner">
                                <div class="Slider-text">
                                    <p class="r2-p"><?php echo $gallery['description']; ?></p>
                                </div>
                            </div>

                            <?php
                        }
                    }
                    $i++;
                }
            }
            ?>
        </div>
    </div>
    <?php if (KEYWORD == 'IOF-D2-H') { ?>
        <div class="Slider-text">
            <a class="close" href="javascript:void(0);">&times;</a>
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
    <?php } ?>
</div>
<?php
if (KEYWORD == 'IOF-D4-H' || KEYWORD == 'IOF-D4-V') {
    echo $this->element('design/dasol/home_menu_d4');
}
?>
<div id="MediaPlayer" class="main-container">
    <?php if (!empty($deals)) { ?>
        <div class="domo-content-wrp">
            <h3>Deals</h3>
            <div class="menu-slider">
                <ul class="bxslider">
                    <?php
                    $dealsstr = '';
                    $j = 0;
                    if (!empty($couponsData)) {
                        foreach ($couponsData as $coupons) {
                            $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID), true);
                            $dealsstr.= "<li>";
                            if (!empty($coupons['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/thumb/' . $coupons['Coupon']['image'])) {
                                $dealsstr.="<a href='" . $url . "' class='img-item'><img src='/Coupon-Image/thumb/" . $coupons['Coupon']['image'];
                                $dealsstr.="'></a>";
                            } else {
                                $dealsstr.="<a href='" . $url . "' class='img-item'><img src='/img/dasol/rcm1.png' alt='item'></a>";
                            }

                            $dealsstr.="<div class='bx-content'><h4>" . $this->Html->link(ucfirst($coupons['Coupon']['name']), $url) . '(' . $this->Html->link($coupons['Coupon']['coupon_code'], $url) . ')' . "</h4><p>";
                            if ($coupons['Coupon']['discount_type'] == 2) {//for percentage
                                $dealsstr.=$this->Html->link($coupons['Coupon']['discount'] . '% off on total order amount.', $url);
                            } else {
                                $dealsstr.=$this->Html->link('$' . $coupons['Coupon']['discount'] . ' off on total order amount.', $url);
                            }

                            $dealsstr.="</p></div></li>";
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
                                $dealsstr.="<a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/thumb/" . $itemOffer['Item']['image'];
                                $dealsstr.="'></a>";
                            } else {
                                $dealsstr.="<a href='" . $url . "' class='img-item'><img src='/img/dasol/rcm1.png'></a>";
                            }
                            $dealsstr.="<div class='bx-content'><h4><small>$</small><big>19</big>" . $this->Html->link(ucfirst($itemOffer['Item']['name']), $url) . "</h4>";
                            
                            $numSurfix = $this->Common->addOrdinalNumberSuffix($itemOffer['ItemOffer']['unit_counter']);
                            if(!empty($numSurfix)){
                            $dealsstr.=  "<small>".$this->Html->link("Buy " . ($itemOffer['ItemOffer']['unit_counter']-1) . " unit and get the ". $numSurfix." Item free on " . $itemOffer['Item']['name'].'.',$url)."</small>";
                        }else{
                            $dealsstr.=  "<small>".$this->Html->link("Buy " .($itemOffer['ItemOffer']['unit_counter']-1) . " get 1 free.",$url)."</small>";
                        }
                            
                            
                            //$dealsstr.= "<p>" . $this->Html->link("On the Purchase of " . $itemOffer['ItemOffer']['unit_counter'] . " units of " . $itemOffer['Item']['name'] . " , 1 unit will be free of cost.", $url) . "</p>";
                            $dealsstr.="</div></li>";
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
                                $dealsstr.="<a href='" . $url . "' class='img-item'><img src='/Offer-Image/thumb/" . $promotional['Offer']['offerImage'];
                                $dealsstr.="'></a>";
                            } elseif (!empty($promotional['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $promotional['Item']['image'])) {

                                $dealsstr.="<a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/deals-images/" . $promotional['Item']['image'];
                                $dealsstr.="'></a>";
                            } else {
                                $dealsstr.="<a href='" . $url . "' class='img-item'><img src = '/img/dasol/rcm1.png' alt = 'deals-img'></a>";
                            }
                            $dealsstr.="<div class='bx-content'><h4>" . $this->Html->link(ucfirst($promotional['Item']['name']), $url) . "</h4>";
                            $dealsstr.= "<p> - " . $this->Html->link($promotional['Offer']['description'], $url) . "</p>";
                            $dealsstr.="</div></li>";
                        }
                    }
                    echo $dealsstr;
                    ?>
                </ul>
            </div>
        </div>
    <?php } ?>
    <div class="recommend-menu craousal-layout">
        <?php
        if (!empty($feturedData)) {
            $if = 0;
            foreach ($feturedData as $key => $fData) {
                $if++;
                if (!empty($fData['FeaturedItem'])) {
                    ?>
                    <div class="menu-title"><h3><span class="cart-icon"><img src="/img/shop-cart.png"></span><?php echo ucfirst($fData['StoreFeaturedSection']['featured_name']); ?></h3></div>
                    <div id="owl-demo<?php echo $if; ?>" class="owl-carousel owl-theme" style="display:block;">
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
                                <div class='item'><div class='donut-menu-card'>
                                        <?php if (!empty($fItem['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/thumb/' . $fItem['Item']['image'])) { ?>
                                            <div class="img-wrap">
                                                <a href="<?php echo $url; ?>" class="img-item"><img src="/MenuItem-Image/thumb/<?php echo $fItem['Item']['image']; ?>" alt="item"></a></div>
                                        <?php } else { ?>
                                            <div class="img-wrap">
                                                <a href="<?php echo $url; ?>" class="img-item"><img src="/img/no_images.jpg" alt="item"></a>
                                            </div>
                                        <?php } ?>
                                        <div class="card-info theme-bg-2">
                                            <span><?php echo $this->Html->link(ucfirst(@$fItem['Item']['name']), $url) ?></span>
                                        </div>
                                    </div></div>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    </div>
                    <?php
                }
            }
        }
        ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".Slider-text a").click(function () {
            $(".Slider-text").slideUp();
        });

        $('.bxslider').bxSlider();
        var win = $(window).height();
        $(".slider-bg .carousel-inner .item").css("max-height", win);

        var owl = $(".owl-carousel");
        owl.owlCarousel({
            itemsCustom: [
                [0, 1],
                [450, 2],
                [600, 6],
                [700, 3],
                [1000, 4],
                [1200, 4],
                [1400, 5]
            ],
            navigation: true

        });
    });
</script>