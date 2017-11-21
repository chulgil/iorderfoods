<?php
$cartlink = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId;
$EncryptStoreID = $this->Encryption->encode($store_data_app['Store']['id']);
$EncryptMerchantID = $this->Encryption->encode($store_data_app['Store']['merchant_id']);
?>
<div class="slider-bg clearfix">
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
                                <img src="/sliderImages/<?php echo $gallery['image']; ?>" alt="banner">
                                <div class="slider-text">
                                    <!--<h4>Description</h4>-->
                                    <p><?php echo $gallery['description']; ?></p>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div data-slide-no="<?php echo $i; ?>" class="item carousel-item">
                                <img src="/sliderImages/<?php echo $gallery['image']; ?>" alt="banner">
                                <div class="slider-text">
                                    <!--<h4>Description</h4>-->
                                    <p><?php echo $gallery['description']; ?></p>
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
        <a class="carousel-control left" href="#carousel" data-slide="prev"></a>
        <a class="carousel-control right" href="#carousel" data-slide="next"></a>
    </div>
    <div class="online-order-wrap">
        <a href="<?php echo $cartlink; ?>" class="oow-button theme-bg-1">ORDER ONLINE</a>
    </div>
</div>
<div class="main-container home-page-container clearfix" id="MediaPlayer">
    <?php //echo $this->Session->flash(); ?>
    <?php
    if (!empty($deals)) {
        if (!empty($storeDealData['StoreDeals']['background_image']) && file_exists(WWW_ROOT . '/StoreDeals-BgImage/' . $storeDealData['StoreDeals']['background_image'])) {
            $image = "/StoreDeals-BgImage/" . $storeDealData['StoreDeals']['background_image'];
            $bgImage = "style=background-image:url('" . $image . "')";
        } else {
            $bgImage = "";
        }
        ?>
        <div class="dasol-liw hm-deals-wrap" <?php echo $bgImage; ?>>
            <div class="main-container">
                <h2>
                    <?php if (!empty($storeDealData['StoreDeals']['icon_image']) && file_exists(WWW_ROOT . '/StoreDeals-IconImage/' . $storeDealData['StoreDeals']['icon_image'])) { ?>
                        <img src="/StoreDeals-IconImage/<?php echo $storeDealData['StoreDeals']['icon_image']; ?>" alt="item">
                    <?php } else { ?>
                        <img src="/img/fork-60.png" alt=""/>
                    <?php } ?>
                    <br><?php echo (@$storeDealData['StoreDeals']['title'])?$storeDealData['StoreDeals']['title']:'Deals';?>
                </h2>

                <div class="container">
                    <div class="row">

                    <?php
                $dealsstr = '';
                $j = 0;
                if (!empty($couponsData)) {
                    foreach ($couponsData as $coupons) {
                        $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID), true);
                        $dealsstr.= "<div class=\"col-xs-12 col-sm-12 col-md-6\"><div class=\"item-container\"><div class=\"row\">";
                        if (!empty($coupons['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/' . $coupons['Coupon']['image'])) {
                            $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6\"><a class=\"img-item\"><img src='/Coupon-Image/" . $coupons['Coupon']['image'];
                            $dealsstr.="'></a></div>";
                        } else {
                            $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6\"><a class=\"img-item\"><img src='/img/no_images.jpg' alt='item'></a></div>";
                        }
                        $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6 description\"><h3>" . ucfirst($coupons['Coupon']['name']) . ' (' . $coupons['Coupon']['coupon_code'] . ')' . "</h3><small>";
                        if ($coupons['Coupon']['discount_type'] == 2) {//for percentage
                            $dealsstr.=$coupons['Coupon']['discount'] . '% off on total order amount.';
                        } else {
                            $dealsstr.='$' . $coupons['Coupon']['discount'] . ' off on total order amount.';
                        }
                        $dealsstr.="</small><div class=\"btn-cart-container\">";
                        $dealsstr.= $this->Html->link('Add to Cart', 'javascript:void(0)', array('escape' => false, 'class' => 'add-cart addCouponToCart', 'data-id' => $this->Encryption->encode($coupons['Coupon']['id'])));
                        $dealsstr.="</div></div></div></div></div>";
                        $j++;
                    }
                }

                if (!empty($itemOfferData)) {

                    foreach ($itemOfferData as $itemOffer) {
                        $EncryptItemId = $this->Encryption->encode($itemOffer['ItemOffer']['item_id']);
                        $EncryptCatId = $this->Encryption->encode($itemOffer['ItemOffer']['category_id']);
                        $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
                        $dealsstr.= "<div class=\"col-xs-12 col-sm-12 col-md-6\"><div class=\"item-container\"><div class=\"row\">";
                        if (!empty($itemOffer['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/' . $itemOffer['Item']['image'])) {
                            $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6\"><a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/" . $itemOffer['Item']['image'];
                            $dealsstr.="'></a></div>";
                        } else {
                            $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6\"><a href='" . $url . "' class='img-item'><img src='/img/no_images.jpg'></a></div>";
                        }

                        $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6 description\"><h3>" . $this->Html->link(ucfirst($itemOffer['Item']['name']), $url) . "</h3>";

                        $numSurfix = $this->Common->addOrdinalNumberSuffix($itemOffer['ItemOffer']['unit_counter']);
                        if (!empty($numSurfix)) {
                            $dealsstr.= "<small>" . $this->Html->link("Buy " . ($itemOffer['ItemOffer']['unit_counter'] - 1) . " unit and get the " . $numSurfix . " Item free on " . $itemOffer['Item']['name'] . '.', $url) . "</small><div class=\"btn-cart-container\">";
                            $dealsstr.= $this->Html->link('Add to Cart', $url, array('class' => 'add-cart'));
                        } else {
                            $dealsstr.= "<small>" . $this->Html->link("Buy " . ($itemOffer['ItemOffer']['unit_counter'] - 1) . " get 1 free.", $url) . "</small><div class=\"btn-cart-container\">";
                            $dealsstr.= $this->Html->link('Add to Cart', $url, array('class' => 'add-cart'));
                        }

                        $dealsstr.="</div></div></div></div></div>";
                        $j++;
                    }
                }

                if (!empty($promotionalOfferData)) {
                    foreach ($promotionalOfferData as $promotional) {
                        if (!empty($promotional['Item']['Category'])) {
                            $EncryptItemId = $this->Encryption->encode($promotional['Offer']['item_id']);
                            $EncryptCatId = $this->Encryption->encode($this->common->getCategoryID($promotional['Offer']['item_id']));
                            $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
                            $dealsstr.=  "<div class=\"col-xs-12 col-sm-12 col-md-6\"><div class=\"item-container\"><div class=\"row\">";
                            if (!empty($promotional['offerImage']['image']) && file_exists(WWW_ROOT . '/Offer-Image/' . $promotional['offerImage']['image'])) {
                                $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6\"><a href='" . $url . "' class='img-item'><img src='/Offer-Image/" . $promotional['Offer']['offerImage'];
                                $dealsstr.="'></a></div>";
                            } elseif (!empty($promotional['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $promotional['Item']['image'])) {

                                $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6\"><a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/deals-images/" . $promotional['Item']['image'];
                                $dealsstr.="'></a></div>";
                            } else {
                                $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6\"><a href='" . $url . "' class='img-item'><img src = '/img/no_images.jpg' alt = 'deals-img'></a></div>";
                            }

                            $dealsstr.="<div class=\"col-xs-12 col-sm-6 col-md-6 description\"><h3>" . $this->Html->link(ucfirst($promotional['Item']['name']), $url) . "</h3>";
                            $dealsstr.= "<small> - " . $this->Html->link($promotional['Offer']['description'], $url) . "</small>";
                            $dealsstr.= $this->Html->link('Add to Cart', $url, array('class' => 'add-cart'));
                            $dealsstr.="</div></div></div></div></div>";
                        }
                    }
                }
                echo $dealsstr;
                ?>
                    </div>
                </div>

            </div>
        </div>
    <?php } ?>
    <?php if (!empty($feturedData)) {

        foreach ($feturedData as $key => $fData) {
            $bgImage = "";
            if (empty($fData['FeaturedItem'])) {
                unset($feturedData[$key]);
            }
        }

        ?>
        <?php
        $fi=1;

        $totalsections=count($feturedData);
        foreach ($feturedData as $key => $fData) {
            $bgImage = "";
            if (!empty($fData['FeaturedItem'])) {
                if (!empty($fData['StoreFeaturedSection']['background_image']) && file_exists(WWW_ROOT . '/FeatureSection-BgImage/' . $fData['StoreFeaturedSection']['background_image'])) {
                    $image = "/FeatureSection-BgImage/" . $fData['StoreFeaturedSection']['background_image'];
                    $bgImage = "style=background-image:url('" . $image . "')";
                } else {
                    $bgImage = "";
                }
                ?>
                <div class="dasol-liw" <?php echo $bgImage; ?>>
                    <div class="main-container">
                        <h2>
                            <?php if (!empty($fData['StoreFeaturedSection']['image']) && file_exists(WWW_ROOT . '/FeatureSection-IconImage/' . $fData['StoreFeaturedSection']['image'])) { ?>
                                <img src="/FeatureSection-IconImage/<?php echo $fData['StoreFeaturedSection']['image']; ?>" alt="item">
                            <?php } else { ?>
                                <img src="/img/fork-60.png" alt=""/>
                            <?php } ?>
                            <br><?php echo ucfirst($fData['StoreFeaturedSection']['featured_name']); ?>
                        </h2>
                        <div class="container">
                            <div class="row">
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
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="item-container">
                                        <?php if (!empty($fItem['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/' . $fItem['Item']['image'])) { ?>
                                            <a href="<?php echo $url; ?>" class="img-item"><img src="/MenuItem-Image/<?php echo $fItem['Item']['image']; ?>" alt="item"></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $url; ?>" class="img-item"><img src="/img/no_images.jpg" alt="item"></a>
                                        <?php } ?>

                                        <h3>
                                            <?php echo $this->Html->link(ucfirst(@$fItem['Item']['name']), $url) ?>
                                        </h3>
                                        <?php echo $this->Html->link('Add to Cart', $url, array('class' => 'add-to-cart-btn')); ?>
                                    </div>
                        </div>
                                <?php
                            }
                        }
                        ?>


                            </div>
                        </div>



                    </div>
                    <?php if($totalsections==$fi){?>
                    <div class="online-order">
                        <a href="<?php echo $cartlink; ?>" class="online-btn theme-bg-1"> ORDER ONLINE</a>
                    </div>
            <?php } ?>
                </div>
                <?php
            }
            $fi++;
        }
        ?>
    <?php } ?>
</div>
<script type="text/javascript">
    $(document).on('click', '.addCouponToCart', function () {
        var couponId = $(this).data('id');
        if (couponId) {
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'products', 'action' => 'addCouponToCart')); ?>",
                type: "Post",
                dataType: 'html',
                async: false,
                data: {couponId: couponId},
                beforeSend: function () {
                    $.blockUI({css: {
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .5,
                            color: '#fff'
                        }});
                },
                success: function (result) {
                    if (result) {
                        response = $.parseJSON(result);
                        if (response.status == "Error") {
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(response.msg);
                            return false;
                        } else if (response.status == "Success" && response.url) {
                            window.location.href = response.url;
                        }
                    }
                },
                complete: function () {
                    $.unblockUI();
                },
            });
        }
    });
</script>
