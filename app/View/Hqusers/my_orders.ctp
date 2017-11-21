<?php $url = HTTP_ROOT; ?>
<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo __('Favorite & Order History'); ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <!-- ORDER TABS -->
                    <div class="sign-up order-content order-content-tabs clearfix">
                        <!-- NAV TABS -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation">
                                <?php echo $this->Html->link(__('My Favorites'), array('controller' => 'hqusers', 'action' => 'myFavorites')); ?>
                            </li>
                            <li role="presentation" class="active">
                                <?php echo $this->Html->link(__('My Orders'), array('controller' => 'hqusers', 'action' => 'myOrders')); ?>
                            </li>
                            <li role="presentation">
                                <?php echo $this->Html->link(__('My Saved Orders'), array('controller' => 'hqusers', 'action' => 'mySavedOrders')); ?>
                            </li>
                        </ul>

                        <!-- SEARCH -->
                        <div class="tabs-search clearfix">
                            <?php echo $this->Form->create('Orders', array('url' => array('controller' => 'hqusers', 'action' => 'myOrders'), 'id' => 'AdminId', 'type' => 'post')); ?>
                            <div class="col-2">
                                <?php
                                $merchantList = $this->Common->getStores($this->Session->read('hq_id'));
                                echo $this->Form->input('Merchant.store_id', array('options' => $merchantList, 'class' => 'inbox', 'div' => false, 'empty' => 'Please Select Store', 'label' => FALSE));
                                ?>
                            </div>
                            <div class="col-2 tab-search-right">
                                <div>
                                    <?php
                                    $val = '';
                                    if (isset($keyword) && !empty($keyword)) {
                                        $val = $keyword;
                                    }
                                    ?>
                                    <?php echo $this->Form->input('User.keyword', array('value' => $val, 'label' => false, 'div' => false, 'placeholder' => 'Search (Order Number, Name, Address, City)', 'class' => 'inbox')); ?>
                                </div>
                                <div class="searchh-btn">
                                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn common-config black-bg')); ?>
                                    <?php echo $this->Html->link('Clear', array('controller' => 'hqusers', 'action' => 'myOrders', 'clear'), array('class' => 'btn common-config black-bg')); ?>
                                </div>
                            </div>
                            <?php echo $this->Form->end(); ?>
                        </div>

                        <!-- PAGINATION -->
                        <div class="clearfix"><?php echo $this->element('pagination'); ?></div>
                        <!-- TAB PANES -->
                        <div class="show-paginaiton-count"><?php echo $this->element('show_pagination_count'); ?></div>
                        <div class="tab-content">
                            <!-- MY FAVORITES -->
                            <div role="tabpanel" class="tab-pane active" id="my_favorites">
                                <?php
                                if (!empty($myOrders)) {
                                    foreach ($myOrders as $orders) {
                                        ?>
                                        <div class="tab-panes">
                                            <div class="order-invoice clearfix">
                                                <div class="col-2">
                                                    <p><span>Order Id:</span> <?php echo $orders['Order']['order_number']; ?> | <span>Cost: $<?php echo $orders['Order']['amount']- $orders['Order']['coupon_discount']; ?><?php echo ' - ' . $orders['OrderStatus']['name']; ?></span></p>
                                                    <p><span>Order Address:</span>
                                                        <?php
                                                        if ($orders['Order']['seqment_id'] == 1) {
                                                            echo 'Dine-In';
                                                        } elseif ($orders['Order']['seqment_id'] == 2) {
                                                            echo 'PickUp';
                                                        } elseif ($orders['Order']['seqment_id'] == 3) {
                                                            echo $orders['DeliveryAddress']['name_on_bell'] . ', ' . $orders['DeliveryAddress']['address'] . ' ,' . $orders['DeliveryAddress']['city'];
                                                        }
                                                        ?>
                                                    </p>
                                                    <p><span>Order Placed On:</span> <?php echo $this->Hq->storeTimeFormate($this->Hq->storeTimezone('', $orders['Order']['created'], '', $orders['Order']['store_id']), true, $orders['Order']['store_id']); ?></p>

                                                    <p><span>Order Time:</span> <?php echo $this->Hq->storeTimeFormate($orders['Order']['pickup_time'], true, $orders['Order']['store_id']); ?></p>
                                                    <?php if (!empty($orders['Order']['order_comments'])) { ?>
                                                        <p><span>Comments:</span> <?php echo $orders['Order']['order_comments']; ?></p>

                                                    <?php } ?>



                                                </div>
                                                <div class="col-2 text-right">
                                                    <!--00000000000-->
                                                    <?php
                                                    $desc = '';
                                                    $offers = '';
                                                    $result = '';
                                                    foreach ($orders['OrderItem'] as $order) {
                                                        //$desc = $order['quantity'] . ' ' . @$order['Size']['size'] . ' ' . @$order['Type']['name'] . ' ' . $order['Item']['name'];
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
                                                                $offers .= $offer['quantity'] . 'X' . $offer['Item']['name'] . '&nbsp;';
                                                            }
                                                        }
                                                        if (!empty($offers)) {
                                                            $result .= $desc . ' ( Offer : ' . $offers . '), ';
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
                                                    <a href="#" onclick='window.open("<?php echo $strShareLink; ?>", "", "width=500, height=300");'>
                                                        <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>
                                                    </a>
                                                    <?php if (!empty($orders['Store']['store_name'])) { ?>
                                                        <span style="display: inline-block; margin: 0px 5px; vertical-align: text-top;">
                                                            <a target="blank" href= "http://twitter.com/share?text=I ordered <?php echo $result; ?> from <?php echo $orders['Store']['store_name']; ?>&url=<?php echo $url; ?>&via=<?php echo $orders['Store']['store_name']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
                                                        </span>
                                                    <?php } ?>
                                                    <!--00000000000-->
                                                    <p>
                                                        <?php if (!in_array($orders['Order']['id'], $compare)) {
                                                            ?>
                                                            <?php
                                                            echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-heart')) . 'Add to Fav', array('controller' => 'hqusers', 'action' => 'myFavorite', $this->Encryption->encode($orders['Order']['id'])), array('confirm' => 'Are you sure you want to add this order to your favorite list ?', 'class' => '', 'escape' => false));
                                                        }
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-striped tab-panes-table">
                                                    <thead>
                                                        <tr>
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
                                                    </thead>

                                                    <tbody>
                                                        <?php foreach ($orders['OrderItem'] as $order) { ?>
                                                            <tr>
                                                                <td><?php
                                                                    $Interval = "";
                                                                    if (isset($order['interval_id'])) {
                                                                        $intervalId = $order['interval_id'];
                                                                        $Interval = $this->Hq->getIntervalName($intervalId);
                                                                    }

                                                                    echo $order['quantity'] . 'X' . $order['Item']['name'];
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
                                                                <td><?php
                                                                    if (!empty($order['Size'])) {
                                                                        echo $order['Size']['size'];
                                                                    } else {
                                                                        echo ' - ';
                                                                    }
                                                                    ?></td>
                                                                <td><?php
                                                                    if (!empty($order['OrderPreference'])) {
                                                                        $preference = "";
                                                                        $prefix = '';
                                                                        foreach ($order['OrderPreference'] as $key => $opre) {
                                                                            $preference .= $prefix . '' . $opre['SubPreference']['name'] . "";
                                                                            $prefix = ', ';
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
                                                                                echo $prefix . '' . $topping['Topping']['name'] . '';
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
                                                                    ?> </td>
                                                                <?php
                                                                if ($orders['Order']['order_status_id'] == 5 || $orders['Order']['order_status_id'] == 7) {
                                                                    if (!empty($order['StoreReview'])) {
                                                                        if ($order['StoreReview']['is_approved'] == 1) {
                                                                            ?>
                                                                            <td><span class='review' storeID=<?php echo $this->Encryption->encode($orders['Store']['id']); ?> name=<?php echo $this->Encryption->encode($order['Item']['name']); ?> status=<?php echo $this->Encryption->encode('Done'); ?>  orderId=<?php echo $this->Encryption->encode($order['order_id']); ?> orderItemId=<?php echo $this->Encryption->encode($order['id']); ?> itemId=<?php echo $this->Encryption->encode($order['item_id']); ?>><input type="number" class="rating" min=0 max=5 data-glyphicon=0 readOnly=true value=<?php echo $order['StoreReview']['review_rating']; ?> ></span></td>
                                                                        <?php } else { ?>
                                                                            <td><span class='review' storeID=<?php echo $this->Encryption->encode($orders['Store']['id']); ?> name=<?php echo $this->Encryption->encode($order['Item']['name']); ?> status=<?php echo $this->Encryption->encode('Done'); ?> orderId=<?php echo $this->Encryption->encode($order['order_id']); ?> orderItemId=<?php echo $this->Encryption->encode($order['id']); ?> itemId=<?php echo $this->Encryption->encode($order['item_id']); ?>><input type="number" class="rating" min=0 max=5 data-glyphicon=0 value=0 readOnly=true ></span></td>
                                                                        <?php } ?>
                                                                    <?php } else { ?>
                                                                        <td><span class='review' storeID=<?php echo $this->Encryption->encode($orders['Store']['id']); ?> name=<?php echo $this->Encryption->encode($order['Item']['name']); ?> status=<?php echo $this->Encryption->encode('Pending'); ?> orderId=<?php echo $this->Encryption->encode($order['order_id']); ?> orderItemId=<?php echo $this->Encryption->encode($order['id']); ?> itemId=<?php echo $this->Encryption->encode($order['item_id']); ?>><input type="number" class="rating" min=0 max=5 data-glyphicon=0 ></span></td>
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
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "No Record Found";
                                }
                                ?>
                            </div>
                        </div>
                        <!-- /TAB PANES END -->

                        <!-- PAGINATION -->
                        <?php echo $this->element('pagination'); ?>
                    </div>
                    <!-- ORDER TABS END -->
                    <!-- -->
                    <div class="ext-border">
                        <?php echo $this->Html->image('hq/thick-border.png', array('alt' => 'user')) ?>
                    </div>
                    <!-- -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.review').click(function () {
            var orderItemId = $(this).attr('orderItemId');
            var storeID = $(this).attr('storeID');
            var orderId = $(this).attr('orderId');
            var itemId = $(this).attr('itemId');
            var status = $(this).attr('status');
            var orderName = $(this).attr('name');
            var orderRating = $(this).find("input[type='number']").val();
            window.location = "/hqusers/rating/" + storeID + "/" + orderItemId + "/" + orderId + "/" + status + "/" + orderName + "/" + orderRating + "/" + itemId;

        });

//        $('.reorder').click(function () {
//            var orderId = $(this).attr('name');
//            window.location = "/Users/customerDashboard/<?php echo @$encrypted_storeId; ?>/<?php echo @$encrypted_merchantId; ?>/" + orderId;
//
//        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#MerchantStoreId").change(function () {
            $("#AdminId").submit();
        });

    });

</script>
