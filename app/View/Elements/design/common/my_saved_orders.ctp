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
    <div class="title-bar">My Saved Orders</div>
    <div class="main-container ">
        <div class="ext-menu-title">
            <h4><?php echo __('Favorite & Order History'); ?></h4>
        </div>
        <div class="inner-wrap  photos mySavedOrders ">
            <?php //echo $this->Session->flash(); ?>
            <div class="fav-order-history foh-saved-order">
                <div class="">
                    <!-- Nav tabs --><div class="card">
                        <ul class="nav nav-tabs" role="tablist">
                            <li><?php echo $this->Html->link(__('My Favorites'), array('controller' => 'orders', 'action' => 'myFavorites', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                            <li><?php echo $this->Html->link(__('My Orders'), array('controller' => 'orders', 'action' => 'myOrders', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                            <li class="active"><?php echo $this->Html->link(__('My Saved Orders'), array('controller' => 'orders', 'action' => 'mySavedOrders', $encrypted_storeId, $encrypted_merchantId)); ?></li>


                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="home">
                                <?php echo $this->Form->create('Orders', array('url' => array('controller' => 'orders', 'action' => 'mySavedOrders'), 'id' => 'AdminId', 'type' => 'post', 'class' => ' clearfix tab-form')); ?>
                                <?php echo $this->element('userprofile/filter_store'); ?>
                                <div class="col-lg-4 col-sm-4 search-btm-btn">
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-6 col-xs-6">
                                            <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'srch-btn theme-bg-1')); ?>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-6">
                                            <?php echo $this->Html->link('Clear', array('controller' => 'orders', 'action' => 'mySavedOrders', 'clear'), array('class' => 'clr-link theme-bg-2')); ?>
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
                                                        <p class="margin-add15"> <span class="oid theme-bg-1">Order Id: <?php echo $orders['Order']['order_number']; ?></span><span class="seprator-bar"> | </span><span class="ostat theme-bg-2">Cost: $<?php echo $orders['Order']['amount'] - $orders['Order']['coupon_discount']; ?></span></p>
                                                        <p> <?php
                                                            if ($orders['Order']['seqment_id'] == 1) {
                                                                echo 'Dine-In';
                                                            } elseif ($orders['Order']['seqment_id'] == 2) {
                                                                echo 'PickUp';
                                                            } elseif ($orders['Order']['seqment_id'] == 3) {
                                                                echo $orders['DeliveryAddress']['name_on_bell'] . ', ' . $orders['DeliveryAddress']['address'] . ' ,' . $orders['DeliveryAddress']['city'];
                                                            }
                                                            ?></p>
                                                    </div>
                                                    <div class="order-history-detail-rt">
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
                                                        <span style="display: inline-block; margin: 0px 5px; vertical-align: text-top;">
                                                            <a target="blank" href= "http://twitter.com/share?text=I saved <?php echo $result; ?> from <?php echo $_SESSION['storeName']; ?>&url=<?php echo $url; ?>&via=<?php echo $_SESSION['storeName']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
                                                        </span>
                                                        <?php
                                                        if (!empty($storeId)) {
                                                            if ($orders['Order']['store_id'] == $storeId) {
                                                                echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-cart-plus')) . 'Order Now', 'javascript:void(0);', array('class' => 'reorder orderColor', 'name' => $this->Encryption->encode($orders['Order']['id']), 'escape' => false));
                                                                ?>
                                                                <span class="seprator-bar">|</span> <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o')) . 'Delete', array('controller' => 'orders', 'action' => 'deleteSaveOrder', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($orders['Order']['id'])), array('class' => 'delete orderColor', 'escape' => false)); ?>
                                                                <?php
                                                            }
                                                        }
                                                        ?>                                       </div>
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
                                                                <th></th>
                                                            <?php } ?>
                                                        </tr>
                                                        <?php foreach ($orders['OrderItem'] as $order) { ?>
                                                            <tr>
                                                                <td><?php
                                                                    $Interval = "";
                                                                    if (isset($order['interval_id'])) {
                                                                        $intervalId = $order['interval_id'];
                                                                        $Interval = $this->Common->getIntervalName($intervalId);
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
                                                                <td>
                                                                    <?php
                                                                    if (!empty($order['OrderTopping'])) {
                                                                        $prefix = '';
                                                                        foreach ($order['OrderTopping'] as $topping) {
                                                                            $size = ($topping['addon_size_id'] > 1) ? $topping['addon_size_id'] : '';
                                                                            echo $prefix . '' . $size . ' ' . $topping['Topping']['name'] . '';
                                                                            $prefix = ', ';
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
                                                            </tr>
                                                        <?php } ?>

                                                    </tbody></table>
                                            </div>

                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="repeat-deatil">
                                        <div class="responsive-table">
                                            <table class="table table-striped order-history-table">
                                                <tr>
                                                    <td><?php echo __("No orders have been saved yet"); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php echo $this->element('pagination'); ?>
    </div>
</main>


<script>
    $(document).ready(function () {
//        $('.reorder').click(function () {
//            var orderId = $(this).attr('name');
//            window.location = "/products/items/<?php echo $encrypted_storeId; ?>/<?php echo $encrypted_merchantId; ?>/" + orderId;
//        });
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

        $("#MerchantStoreId").change(function () {
            $("#AdminId").submit();
        });
        $("#flashMessage").fadeOut(8000);
    });
</script>