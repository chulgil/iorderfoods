<?php if ($this->Session->check('Message.flash')) { ?>
    <script type="text/javascript">
        $("#MediaPlayer").ready(function () {
            $("html, body").delay(2000).animate({
                scrollTop: $('#MediaPlayer').offset().top
            }, 1000);
        });
    </script>
<?php } ?>
<?php
$cartlink = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId;
$EncryptStoreID = $this->Encryption->encode($store_data_app['Store']['id']);
$EncryptMerchantID = $this->Encryption->encode($store_data_app['Store']['merchant_id']);
?>
<!--slider-->
<div class="slider-bg">
    <div id="carousel" class="carousel slide carousel-fade">
        <ol class="carousel-indicators">
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
                                <div class="slider-text">
                                    <p class="r2-p"><?php echo $gallery['description']; ?></p>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div data-slide-no="<?php echo $i; ?>" class="item carousel-item">
                                <img src="/sliderImages/thumb/<?php echo $gallery['image']; ?>" alt="banner">
                                <div class="slider-text">
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
        <!-- Carousel nav -->
        <a class="carousel-control left" href="#carousel" data-slide="prev">‹</a>
        <a class="carousel-control right" href="#carousel" data-slide="next">
            <span class="right-control">›</span>
        </a>
    </div>
    <?php if (KEYWORD == "IOF-C1-H" || KEYWORD == "IOF-C1-V") { ?>
        <div class="open-hours-card">
            <div class="open-top theme-bg-1">
    <!--                <span class="close"><i class="fa fa-times-circle" aria-hidden="true"></i></span>-->
            </div>
            <div class="open-mid">
                <h3>Opening <strong>Hours</strong></h3>
                <ul class="time-list">
                    <?php
                    $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
                    foreach ($days as $key => $value) {
                        ?>

                        <li class="clearfix">

                            <span class="lft-txt">
                                <i class="fa fa-calendar-o" aria-hidden="true"></i>
                                <?php echo $value; ?>
                            </span>
                            <span class="rgt-txt">
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

                            </span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="open-bottom">
                <div class="contact-info theme-txt-col-1">
                    <span><i class="fa fa-phone" aria-hidden="true"></i>call Us <strong> @<?php echo $store_data_app['Store']['phone']; ?></strong></span>
                </div>
            </div>

        </div>
    <?php } ?>
    <div class="center-title">
<!--        <h3><?php echo $store_data_app['Store']['store_name']; ?></h3>
        <p>Online Ordering Now Available</p>-->
        <a class="order-link theme-bg-2" href="<?php echo $cartlink; ?>"> ORDER ONLINE</a>
    </div>
    <div class="overlay-bg"></div>
</div>

<div class="main-container" id="MediaPlayer">
    <?php echo $this->Session->flash(); ?>
    <?php if (in_array(KEYWORD, array('IOF-C2-H', 'IOF-C2-V', 'IOF-C3-H', 'IOF-C3-V'))) { ?>
        <div class="recommend-menu chole-recommended-menu">
            <div class="crm-top clearfix">
                <div class="crm-lft">
                    <div class="crm-img-wrap">
                        <?php if (!empty($themeImage['HomeImage']['contact_left']) && file_exists(WWW_ROOT . '/sliderImages/thumb/' . $themeImage['HomeImage']['contact_left'])) { ?>
                            <img src="/sliderImages/thumb/<?php echo $themeImage['HomeImage']['contact_left']; ?>" alt="item">
                        <?php } else { ?>
                            <img src="/img/crm-img-1.1.png" alt="#crm">
                        <?php } ?>
                    </div>
                </div>
                <div class="crm-rgt">
                    <div class="crm-im-wrap-top">
                        <?php if (!empty($themeImage['HomeImage']['contact_right']) && file_exists(WWW_ROOT . '/sliderImages/thumb/' . $themeImage['HomeImage']['contact_right'])) { ?>
                            <img src="/sliderImages/thumb/<?php echo $themeImage['HomeImage']['contact_right']; ?>" alt="item">
                        <?php } else { ?>
                            <img src="/img/crm-img-2.1.png" alt="#crm">
                        <?php } ?>
                    </div>
                    <div class="crm-wrap-bottom theme-bg-2">
                        <h3>Contact Us</h3>
                        <p><?php echo $store_data_app['Store']['address'] . ' ' . $store_data_app['Store']['city'] . ' ' . $store_data_app['Store']['state'] . ' ' . $store_data_app['Store']['zipcode'] . ' ' . $store_data_app['Store']['phone']; ?></p>
                    </div>
                </div> 
            </div>
            <div class="crm-top-mid clearfix">

                <div class="crm-rgt">
                    <div class="crm-wrap-bottom theme-bg-2">
                        <h3>Opening Hours	</h3>
                        <div class="open-mid">
                            <ul class="time-list">
                                <?php
                                $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
                                foreach ($days as $key => $value) {
                                    ?>
                                    <li class="clearfix">
                                        <span class="lft-txt">
                                            <i class="fa fa-calendar-o" aria-hidden="true"></i>
                                            <?php echo $value; ?>
                                        </span>
                                        <span class="rgt-txt">
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

                                        </span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="crm-im-wrap-top">
                        <?php if (!empty($themeImage['HomeImage']['opening_left']) && file_exists(WWW_ROOT . '/sliderImages/thumb/' . $themeImage['HomeImage']['opening_left'])) { ?>
                            <img src="/sliderImages/thumb/<?php echo $themeImage['HomeImage']['opening_left']; ?>" alt="item">
                        <?php } else { ?>
                            <img src="/img/crm-img-4.1.png" alt="#crm">
                        <?php } ?>
                    </div>
                </div> 
                <div class="crm-lft">
                    <div class="crm-img-wrap">
                        <?php if (!empty($themeImage['HomeImage']['opening_right']) && file_exists(WWW_ROOT . '/sliderImages/thumb/' . $themeImage['HomeImage']['opening_right'])) { ?>
                            <img src="/sliderImages/thumb/<?php echo $themeImage['HomeImage']['opening_right']; ?>" alt="item">
                        <?php } else { ?>
                            <img src="/img/crm-img-3.1.png" alt="#crm">
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="recommend-menu craousal-layout">
        <?php
        if (!empty($deals)) {
            if (!empty($storeDealData['StoreDeals']['background_image']) && file_exists(WWW_ROOT . '/StoreDeals-BgImage/' . $storeDealData['StoreDeals']['background_image'])) {
                $image = "/StoreDeals-BgImage/" . $storeDealData['StoreDeals']['background_image'];
                $bgImage = "style=background-image:url('" . $image . "')";
            } else {
                $bgImage = "";
            }
            ?>
            <div class="menu-title">
                <h3>
                    <?php if (!empty($storeDealData['StoreDeals']['icon_image']) && file_exists(WWW_ROOT . '/StoreDeals-IconImage/' . $storeDealData['StoreDeals']['icon_image'])) { ?>
                        <img src="/StoreDeals-IconImage/<?php echo $storeDealData['StoreDeals']['icon_image']; ?>" alt="item">
                    <?php } else { ?>
                        <img src="/img/fork-60.png" alt=""/>
                    <?php } ?>
                    <br><?php echo (@$storeDealData['StoreDeals']['title']) ? $storeDealData['StoreDeals']['title'] : 'Deals'; ?>
                </h3>
            </div>
            <div id="owl-demo" class="owl-carousel owl-theme" <?php echo $bgImage; ?>>
                <?php
                $dealsstr = '';
                $j = 0;
                if (!empty($couponsData)) {
                    foreach ($couponsData as $coupons) {
                        $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID), true);
                        $dealsstr.= "<div class='item'><div class='donut-menu-card'>";
                        if (!empty($coupons['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/thumb/' . $coupons['Coupon']['image'])) {
                            $dealsstr.="<div class='img-wrap'><a href='" . $url . "' class='img-item'><img src='/Coupon-Image/thumb/" . $coupons['Coupon']['image'];
                            $dealsstr.="'></a></div>";
                        } else {
                            $dealsstr.="<div class='img-wrap'><a href='" . $url . "' class='img-item'><img src='/img/no_images.jpg' alt='item'></a></div>";
                        }
                        $dealsstr.="<div class='card-info theme-bg-2'>";
                        $dealsstr.="<h4>" . $this->Html->link(ucfirst($coupons['Coupon']['name']), $url) . '(' . $coupons['Coupon']['coupon_code'] . ')' . "</h4>";
                        if ($coupons['Coupon']['discount_type'] == 2) {//for percentage
                            $dealsstr.="<small>" . $this->Html->link($coupons['Coupon']['discount'] . '% off on total order amount.', $url) . "</small>";
                        } else {
                            $dealsstr.="<small>" . $this->Html->link('$' . $coupons['Coupon']['discount'] . ' off on total order amount.', $url) . "</small>";
                        }
                        $dealsstr.="</div></div></div>";
                        $j++;
                    }
                }
                if (!empty($itemOfferData)) {
                    foreach ($itemOfferData as $itemOffer) {
                        $EncryptItemId = $this->Encryption->encode($itemOffer['ItemOffer']['item_id']);
                        $EncryptCatId = $this->Encryption->encode($itemOffer['ItemOffer']['category_id']);
                        $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
                        $dealsstr.= "<div class='item'><div class='donut-menu-card'>";
                        if (!empty($itemOffer['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/thumb/' . $itemOffer['Item']['image'])) {
                            $dealsstr.="<div class='img-wrap'><a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/thumb/" . $itemOffer['Item']['image'];
                            $dealsstr.="'></a></div>";
                        } else {
                            $dealsstr.="<div class='img-wrap'><a href='" . $url . "' class='img-item'><img src='/img/no_images.jpg'></a></div>";
                        }
                        $dealsstr.="<div class='card-info theme-bg-2'>";
                        $dealsstr.="<h4>" . $this->Html->link(ucfirst($itemOffer['Item']['name']), $url) . "</h4>";
                        $numSurfix = $this->Common->addOrdinalNumberSuffix($itemOffer['ItemOffer']['unit_counter']);
                        if (!empty($numSurfix)) {
                            $dealsstr.= "<small>" . $this->Html->link("Buy " . ($itemOffer['ItemOffer']['unit_counter'] - 1) . " unit and get the " . $numSurfix . " Item free on " . $itemOffer['Item']['name'] . '.', $url) . "</small>";
                        } else {
                            $dealsstr.= "<small>" . $this->Html->link("Buy " . ($itemOffer['ItemOffer']['unit_counter'] - 1) . " get 1 free.", $url) . "</small>";
                        }

                        //$dealsstr.= "<small>" . $this->Html->link('On the Purchase of ' . ($itemOffer['ItemOffer']['unit_counter']-1) . ' units of ' . $itemOffer['Item']['name'] . ' , 1 unit will be free of cost.', $url) . "</small>";
                        $dealsstr.="</div></div></div>";
                        $j++;
                    }
                }
                if (!empty($promotionalOfferData)) {
                    foreach ($promotionalOfferData as $promotional) {
                        $EncryptItemId = $this->Encryption->encode($promotional['Offer']['item_id']);
                        $EncryptCatId = $this->Encryption->encode($this->common->getCategoryID($promotional['Offer']['item_id']));
                        $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
                        $dealsstr.= "<div class='item'><div class='donut-menu-card'>";
                        if (!empty($promotional['offerImage']['image']) && file_exists(WWW_ROOT . '/Offer-Image/thumb/' . $promotional['offerImage']['image'])) {
                            $dealsstr.="<div class='img-wrap'><a href='" . $url . "' class='img-item'><img src='/Offer-Image/thumb/" . $promotional['Offer']['offerImage'];
                            $dealsstr.="'></a></div>";
                        } elseif (!empty($promotional['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $promotional['Item']['image'])) {

                            $dealsstr.="<div class='img-wrap'><a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/deals-images/" . $promotional['Item']['image'];
                            $dealsstr.="'></a></div>";
                        } else {
                            $dealsstr.="<div class='img-wrap'><a href='" . $url . "' class='img-item'><img src = '/img/no_images.jpg' alt = 'deals-img'></a></div>";
                        }
                        $dealsstr.="<div class='card-info theme-bg-2'>";
                        $dealsstr.="<h4>" . $this->Html->link(ucfirst($promotional['Item']['name']), $url) . "</h4>";
                        $dealsstr.= "<small> - " . $this->Html->link($promotional['Offer']['description'], $url) . "</small>";
                        $dealsstr.="</div></div></div>";
                    }
                }
                echo $dealsstr;
                ?>
            </div>
        <?php } ?>
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
    <div class="online-order">
        <a href="<?php echo $cartlink; ?>" class="online-btn"> ORDER ONLINE</a>
    </div>
</div>




