<style>
    .order-detail-section img{
        width: auto;
    }
    .message.message-success.alert-success{
        margin-top: 5px;
    }
    .chole-main .inner-wrap.photos{
        display:block;
    }
</style>
<?php
$storeId = $this->Session->read('store_id');
$url = HTTP_ROOT;
$imageurl = HTTP_ROOT . 'storeLogo/' . $store_data_app['Store']['store_logo'];
?>
<main class="main-body horizontal-theme-one theme-one">
    <div class="title-bar">My Orders</div>
    <div class="main-container ">
        <div class="ext-menu-title">
            <h4><?php echo __('Favorite & Order History'); ?></h4>
        </div>
        <div class="inner-wrap  photos ">
            <?php //echo $this->Session->flash(); ?>
            <div class="fav-order-history">
                <div class="">
                    <!-- Nav tabs --><div class="card">
                        <ul class="nav nav-tabs" role="tablist">
                            <li><?php echo $this->Html->link(__('My Favorites'), array('controller' => 'orders', 'action' => 'myFavorites', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                            <li class="active"><?php echo $this->Html->link(__('My Orders'), array('controller' => 'orders', 'action' => 'myOrders', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                            <li ><?php echo $this->Html->link(__('My Saved Orders'), array('controller' => 'orders', 'action' => 'mySavedOrders', $encrypted_storeId, $encrypted_merchantId)); ?></li>


                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="home">
                                <?php echo $this->Form->create('Orders', array('url' => array('controller' => 'orders', 'action' => 'myOrders'), 'id' => 'AdminId', 'type' => 'post', 'class' => ' clearfix tab-form')); ?>
                                <?php echo $this->element('userprofile/filter_store'); ?>
                                <div class="col-lg-4 col-sm-4 search-btm-btn">
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-6 col-xs-6">
                                            <?php echo $this->Form->button('Search', array('type' => 'submit ', 'class' => 'theme-bg-1 srch-btn')); ?>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-6">
                                            <?php echo $this->Html->link('Clear', array('controller' => 'orders', 'action' => 'myOrders', 'clear'), array('class' => 'clr-link theme-bg-2')); ?>
                                        </div>
                                    </div>
                                </div>

                                <?php echo $this->Form->end(); ?>
                                <div class="pagination-section clearfix">
                                    <?php echo $this->element('pagination'); ?>
                                </div>
                                <?php
                                if (!empty($myOrders)) {
                                    foreach ($myOrders as $orders) {
                                        ?>
                                        <div class="order-main-container clearfix">
                                            <div class="order-status clearfix">
                                                <div class="order-detail-section">
                                                    <div class="order-history-detail-lt">

                                                        <p class="margin-add15"> <span class="oid theme-bg-1">
                                                                Order Id: <?php echo $orders['Order']['order_number']; ?></span>
                                                            <span class="seprator-bar"> | </span>
                                                            <span class="ostat theme-bg-2">Cost: $<?php echo $orders['Order']['amount'] - $orders['Order']['coupon_discount']; ?><?php echo ' - ' . $orders['OrderStatus']['name']; ?></span></p>
                                                        <p><?php
                                                            if ($orders['Order']['seqment_id'] == 1) {
                                                                echo 'Dine-In';
                                                            } elseif ($orders['Order']['seqment_id'] == 2) {
                                                                echo 'PickUp';
                                                            } elseif ($orders['Order']['seqment_id'] == 3) {
                                                                echo $orders['DeliveryAddress']['name_on_bell'] . ', ' . $orders['DeliveryAddress']['address'] . ' ,' . $orders['DeliveryAddress']['city'];
                                                            }
                                                            ?></p>
                                                        <p>Order Placed On: <?php
                                                            //echo $this->Common->storeTimeFormateUser($this->Common->storeTimeZoneUser('', $orders['Order']['created']), true);
                                                            ?>
                                                            <?php echo $this->Common->storeTimeFormateUser($orders['Order']['created'], true);?>
                                                        </p>
                                                        <p>Order Time:
                                                            <?php
                                                            echo $this->Common->storeTimeFormateUser($orders['Order']['pickup_time'], true);
                                                            ?>
                                                        </p>
                                                        <?php if (!empty($orders['Order']['order_comments'])) { ?>
                                                            <p>Comments:
                                                                <?php echo $orders['Order']['order_comments']; ?></p>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="order-history-detail-rt">
                                                        <?php
                                                        $desc = '';
                                                        $offers = '';
                                                        $result = '';
                                                        foreach ($orders['OrderItem'] as $order) {
                                                            // $desc = $order['quantity'] . ' ' . @$order['Size']['size'] . ' ' . @$order['Type']['name'] . ' ' . $order['Item']['name'];
                                                            $desc = $order['quantity'];
                                                            if (!empty($order['Size']['size'])) {
                                                                $desc.= ' ' . @$order['Size']['size'];
                                                            }
                                                            if (!empty($order['Type']['name'])) {
                                                                $desc.= ' ' . @$order['Type']['name'];
                                                            }
                                                            if (!empty($order['Item']['name'])) {
                                                                $desc.= ' ' . @$order['Item']['name'];
                                                            }
                                                            if (!empty($order['OrderOffer'])) {
                                                                foreach ($order['OrderOffer'] as $offer) {
                                                                    $offers .= $offer['quantity'] . 'X' . $offer['Item']['name'] . ' & nbsp;
                                                                            ';
                                                                }
                                                            }
                                                            if (!empty($offers)) {
                                                                $result .= $desc . ' ( Offer : ' . $offers . ' ), ';
                                                            } else {
                                                                $result .= $desc . ', ';
                                                            }
                                                            $offers = '';
                                                            $desc = '';
                                                        }
                                                        ?>
                                                        <?php
                                                        $strDomainUrl = $_SERVER['HTTP_HOST'];
                                                        $strShareLink = "https://www.facebook.com/sharer/sharer.php?u=" . $strDomainUrl;
                                                        ?>
                                                        <a href="#" onclick='window.open("<?php echo $strShareLink; ?>", "", "width=500, height=300");
                                                           '>
                                                               <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>
                                                        </a>
                                                        <span style="display: inline-block; margin: 0px 5px; vertical-align: text-top;">
                                                            <a target="blank" href= "http://twitter.com/share?text=I ordered <?php echo $result; ?> from <?php echo $_SESSION['storeName']; ?>&url=<?php echo $url; ?>&via=<?php echo $_SESSION['storeName']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
                                                        </span>
                                                        <?php
                                                        if (!empty($storeId)) {
                                                            if ($orders['Order']['store_id'] == $storeId) {
                                                                $encrypted_order_id = $this->Encryption->encode($orders['Order']['id']);
                                                                echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-cart-plus')) . 'Re-Order', 'javascript:void(0);
                                                                    ', array('class' => 'reorder orderColor', 'name' => $encrypted_order_id, 'escape' => false));
                                                                if (!in_array($orders['Order']['id'], $compare)) {
                                                                    ?>
                                                                    <span class="seprator-bar">|</span>  <?php
                                                                    echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-heart')) . 'Add to Fav', array('controller' => 'orders', 'action' => 'myFavorite', $encrypted_storeId, $encrypted_merchantId, $encrypted_order_id), array('class' => 'addToFav orderColor', 'escape' => false));
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="responsive-table">
                                                <table class="table table-striped order-history-table">
                                                    <tbody><tr>
                                                            <th style="width:20%;"><?php echo __('Items'); ?></th>
                                                            <th style="width:15%;"><?php echo __('Size'); ?></th>
                                                            <th style="width:20%;"><?php echo __('Preferences'); ?></th>
                                                            <th style="width:15%;"><?php echo __('Add-ons'); ?></th>
                                                            <th style="width:20%;"><?php echo __('Store'); ?></th>
                                                            <?php if ($orders['Order']['order_status_id'] == 5 || $orders['Order']['order_status_id'] == 7) { ?>
                                                                <th style="width:15%;"><?php echo __('Review'); ?></th>
                                                            <?php } else { ?>
                                                            <?php } ?>
                                                        </tr>
                                                        <?php foreach ($orders['OrderItem'] as $order) { ?>
                                                            <tr>
                                                                <td>
                                                                    <?php
                                                                    $Interval = "";
                                                                    if (isset($order['interval_id'])) {
                                                                        $intervalId = $order['interval_id'];
                                                                        $Interval = $this->Common->getIntervalName($intervalId);
                                                                    }

                                                                    echo $order['quantity'] . ' X ' . $order['Item']['name'];
                                                                    echo ($Interval) ? "(" . $Interval . ")" : "";
                                                                    ?><br>
                                                                    <?php
                                                                    if (!empty($order['OrderOffer'])) {
                                                                        echo "<innerTag class='greyFont'>Offer Items :</innerTag>";
                                                                        foreach ($order['OrderOffer'] as $offer) {
                                                                            echo '<br/>' . $offer['quantity'] . 'X' . $offer['Item']['name'];
                                                                        }
                                                                    }
                                                                    ?>

                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if (!empty($order['Size'])) {
                                                                        echo $order['Size']['size'];
                                                                    } else {
                                                                        echo ' - ';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if (!empty($order['OrderPreference'])) {
                                                                        $preference = "";
                                                                        $prefix = '';
                                                                        foreach ($order['OrderPreference'] as $key => $opre) {
                                                                            if (!empty($opre['SubPreference'])) {
                                                                                $preference .= $prefix . '' . $opre['SubPreference']['name'] . "";
                                                                                $prefix = ', ';
                                                                            }
                                                                        }
                                                                        echo $preference;
                                                                    } else {
                                                                        echo ' - ';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if (!empty($order['OrderTopping'])) {
                                                                        $prefix = '';
                                                                        foreach ($order['OrderTopping'] as $topping) {
                                                                            if (!empty($topping['Topping']['name'])) {
                                                                                $size = ($topping['addon_size_id'] > 1) ? $topping['addon_size_id']: '';
                                                                                echo $prefix . '' . $size . ' ' . $topping['Topping']['name'] . '';
                                                                                $prefix = ', ';
                                                                            }
                                                                        }
                                                                    } else {
                                                                        echo ' - ';
                                                                    }
                                                                    ?> </td>
                                                                <td>
                                                                    <?php
                                                                    if (!empty($orders['Store'])) {
                                                                        echo $orders['Store']['store_name'];
                                                                    }
                                                                    ?>
                                                                </td>

                                                                <?php
                                                                if ($orders['Order']['order_status_id'] == 5 || $orders['Order']['order_status_id'] == 7) {
                                                                    if (!empty($order['StoreReview'])) {
                                                                        if ($order['StoreReview']['is_approved'] == 1) {
                                                                            ?>
                                                                            <td><span class='review' name=<?php echo $this->Encryption->encode($order['Item']['name']); ?> status=<?php echo $this->Encryption->encode('Done'); ?>  orderId=<?php echo $this->Encryption->encode($order['order_id']); ?> orderItemId=<?php echo $this->Encryption->encode($order['id']); ?> itemId=<?php echo $this->Encryption->encode($order['item_id']); ?>><input type="number" class="rating" min=0 max=5 data-glyphicon=0 readOnly=true value=<?php echo $order['StoreReview']['review_rating']; ?> ></span></td>

                                                                        <?php } else { ?>
                                                                            <td><span class='review' name=<?php echo $this->Encryption->encode($order['Item']['name']); ?> status=<?php echo $this->Encryption->encode('Done'); ?> orderId=<?php echo $this->Encryption->encode($order['order_id']); ?> orderItemId=<?php echo $this->Encryption->encode($order['id']); ?> itemId=<?php echo $this->Encryption->encode($order['item_id']); ?>><input type="number" class="rating" min=0 max=5 data-glyphicon=0 value=0 readOnly=true ></span></td>
                                                                        <?php } ?>
                                                                    <?php } else { ?>
                                                                        <td><span class='review' name=<?php echo $this->Encryption->encode($order['Item']['name']); ?> status=<?php echo $this->Encryption->encode('Pending'); ?> orderId=<?php echo $this->Encryption->encode($order['order_id']); ?> orderItemId=<?php echo $this->Encryption->encode($order['id']); ?> itemId=<?php echo $this->Encryption->encode($order['item_id']); ?>><input type="number" class="rating" min=0 max=5 data-glyphicon=0 ></span></td>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>


                                                            </tr>
                                                        <?php } ?>
                                                        <?php if (!empty($orders['OrderItemFree'][0])) { ?><tr>
                                                                <?php
                                                                foreach ($orders['OrderItemFree'] as $fkey => $itemfree) {
                                                                    echo '<td>' . $itemfree['free_quantity'] . ' ' . $itemfree['Item']['name'] . ' Free</td><td></td><td></td><td></td><td></td>';
                                                                }
                                                                ?>
                                                            </tr>
                                                        <?php }
                                                        ?>

                                                    </tbody></table>
                                            </div>

                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="repeat-deatil">
                                        <div class="responsive-table">
                                            <table class="table table-striped">
                                                <tr>
                                                    <td><?php echo __("No orders have been placed yet! Better start ordering now!"); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                <?php }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>



<script>
    $(document).ready(function () {
        $("#MerchantStoreId").change(function () {
            $("#AdminId").submit();
        });

        $('.review').click(function () {
            var orderItemId = $(this).attr('orderItemId');
            var orderId = $(this).attr('orderId');
            var itemId = $(this).attr('itemId');
            var status = $(this).attr('status');
            var orderName = $(this).attr('name');
            var orderRating = $(this).find("input[type='number']").val();
            window.location = "/Orders/rating/<?php echo $encrypted_storeId; ?>/<?php echo $encrypted_merchantId; ?>/" + orderItemId + "/" + orderId + "/" + status + "/" + orderName + "/" + orderRating + "/" + itemId;

        });

        $('.reorder').click(function () {
            var orderId = $(this).attr('name');
            if (orderId) {
                $.ajax({
                    type: 'post',
                    url: '/Products/reorder',
                    data: {'orderId': orderId},
                    async: false,
                    success: function (result) {
                        var parsedJson = $.parseJSON(result);
                        if (parsedJson.count == 0) {
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html('Items are no longer available.');
                            return false;
                        } else {
                            if (parsedJson.item >= 1) {
                                $("#errorPop").modal('show');
                                $("#errorPopMsg").html('Items are no longer available.');
                                return false;
                            } else {
                                $.ajax({
                                    type: 'post',
                                    url: '/Products/fetchReorderProduct',
                                    data: {},
                                    async: false,
                                    success: function (result2) {
                                        if (result2 == 1) {
                                            window.location = "/Products/items/<?php echo $encrypted_storeId; ?>/<?php echo $encrypted_merchantId; ?>";
                                        }
                                    }
                                });
                            }
                        }
                    }
                });
            }


            //window.location = "/products/items/<?php echo $encrypted_storeId; ?>/<?php echo $encrypted_merchantId; ?>/" + orderId;

        });
    });
</script>

<!--<script>
$(document).ready(function () {
    $(".carousel").carousel({
        interval: 4000
    });
    $(".carousel").on("slid", function () {
        var to_slide;
        to_slide = $(".carousel-item.active").attr("data-slide-no");
        $(".myCarousel-target.active").removeClass("active");
        $(".carousel-indicators [data-slide-to=" + to_slide + "]").addClass("active");
    });
    $(".myCarousel-target").on("click", function () {
        $(this).preventDefault();
        $(".carousel").carousel(parseInt($(this).attr("data-slide-to")));
        $(".myCarousel-target.active").removeClass("active");
        $(this).addClass("active");
    });

    // number spinner //

    (function ($) {
        $('.spinner .btn:first-of-type').on('click', function () {
            $('.spinner input').val(parseInt($('.spinner input').val(), 10) + 1);
        });
        $('.spinner .btn:last-of-type').on('click', function () {
            $('.spinner input').val(parseInt($('.spinner input').val(), 10) - 1);
        });
    })(jQuery);

    window.asd = $('.SlectBox').SumoSelect({csvDispCount: 3, captionFormatAllSelected: "Yeah, OK, so everything."});
    $('.datepicker').datepicker();
});

</script>-->