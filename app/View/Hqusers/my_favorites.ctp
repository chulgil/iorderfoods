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
                            <li role="presentation" class="active">
                                <?php echo $this->Html->link(__('My Favorites'), array('controller' => 'hqusers', 'action' => 'myFavorites')); ?>
                            </li>
                            <li role="presentation">
                                <?php echo $this->Html->link(__('My Orders'), array('controller' => 'hqusers', 'action' => 'myOrders')); ?>
                            </li>
                            <li role="presentation">
                                <?php echo $this->Html->link(__('My Saved Orders'), array('controller' => 'hqusers', 'action' => 'mySavedOrders')); ?>
                            </li>
                        </ul>

                        <!-- SEARCH -->
                        <div class="tabs-search clearfix">
                            <?php echo $this->Form->create('Orders', array('url' => array('controller' => 'hqusers', 'action' => 'myFavorites'), 'id' => 'AdminId', 'type' => 'post')); ?>
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
                                    <?php echo $this->Form->input('User.keyword', array('value' => $val, 'label' => false, 'div' => false, 'placeholder' => 'Search (Order Number)', 'class' => 'inbox')); ?>
                                </div>
                                <div class="searchh-btn">
                                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn common-config black-bg')); ?>
                                    <?php echo $this->Html->link('Clear', array('controller' => 'hqusers', 'action' => 'myFavorites', 'clear'), array('class' => 'btn common-config black-bg')); ?>
                                </div>
                            </div>
                            <?php echo $this->Form->end(); ?>
                        </div>

                        <!-- PAGINATION -->
                        <?php echo $this->element('pagination'); ?>
                        <!-- TAB PANES -->
                        <?php echo $this->element('show_pagination_count'); ?>
                        <div class="tab-content">
                            <!-- MY FAVORITES -->
                            <div role="tabpanel" class="tab-pane active">
                                <?php
                                if (!empty($myFav)) {
                                    foreach ($myFav as $orders) {
                                        ?>
                                        <div class="tab-panes">

                                            <div class="order-invoice clearfix">
                                                <div class="col-2">
                                                    <p><span>Order Id:</span> <?php echo $orders['Order']['order_number']; ?> | <span>Cost: $<?php echo $orders['Order']['amount'] - $orders['Order']['coupon_discount']; ?> <?php echo ' - ' . $orders['Order']['OrderStatus']['name']; ?></span></p>
                                                    <p><span>Order Address:</span>
                                                        <?php
                                                        if ($orders['Order']['seqment_id'] == 1) {
                                                            echo 'Dine-In';
                                                        } elseif ($orders['Order']['seqment_id'] == 2) {
                                                            echo 'PickUp';
                                                        } elseif ($orders['Order']['seqment_id'] == 3) {
                                                            echo $orders['Order']['DeliveryAddress']['name_on_bell'] . ', ' . $orders['Order']['DeliveryAddress']['address'] . ' ,' . $orders['Order']['DeliveryAddress']['city'];
                                                        }
                                                        ?>
                                                    </p>
                                                    <p><span>Order Placed On:</span> <?php echo $this->Hq->storeTimeFormate($this->Hq->storeTimezone('', $orders['Order']['created'], '', $orders['Favorite']['store_id']), true, $orders['Favorite']['store_id']); ?></p>

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
                                                    foreach ($orders['Order']['OrderItem'] as $order) {
                                                        //$desc = $order['quantity'] . ' ' . @$order['Size']['size'] . ' ' . @$order['Type']['name'] . ' ' . $order['Item']['name'];
                                                        $desc = $order['quantity'];
                                                        if(!empty($order['Size']['size'])){
                                                           $desc.= ' ' . @$order['Size']['size'];
                                                        }
                                                        if(!empty($order['Type']['name'])){
                                                           $desc.= ' ' . @$order['Type']['name'];
                                                        }
                                                        if(!empty($order['Item']['name'])){
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
                                                            <a target="blank" href= "http://twitter.com/share?text=I ordered <?php echo $result; ?> from <?php echo $orders['Store']['store_name']; ?> &url=<?php echo $url; ?>&via=<?php echo $orders['Store']['store_name']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
                                                        </span>
                                                    <?php } ?>
                                                    <!--00000000000-->
                                                    <p><?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-times')) . 'Remove', array('controller' => 'hqusers', 'action' => 'myFavorite', $this->Encryption->encode($orders['Order']['id']), $this->Encryption->encode($orders['Favorite']['id'])), array('class' => '', 'escape' => false)); ?></p>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-striped tab-panes-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:30%;"><?php echo __('Items'); ?></th>
                                                            <th style="width:15%;"><?php echo __('Size'); ?></th>
                                                            <th style="width:20%;"><?php echo __('Preferences'); ?></th>
                                                            <th style="width:15%;"><?php echo __('Add-ons'); ?></th>
                                                            <th style="width:20%;"><?php echo __('Store'); ?></th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php foreach ($orders['Order']['OrderItem'] as $order) { ?>
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
                                                                    ?></td>
                                                                <td> <?php
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
                                                                    ?>

                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if (!empty($orders['Store'])) {
                                                                        echo $orders['Store']['store_name'];
                                                                    }
                                                                    ?> </td>

                                                            </tr><?php } ?>
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
        $("#MerchantStoreId").change(function () {
            $("#AdminId").submit();
        });

    });

</script>

