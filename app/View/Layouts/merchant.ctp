<!DOCTYPE html>
<html lang="en">
    <head>

        <?php echo $this->Html->charset('UTF-8'); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">  
        <link rel="shortcut icon" href="/img/favicon.png">
        <title><?php echo $name;?></title>
        <?php
        echo $this->Html->css('store_admin/bootstrap');
        echo $this->Html->css('store_admin/sb-admin');
        echo $this->Html->css('store_admin/custom_admin');
        echo $this->Html->css('horizontal/responsiveslides');
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('vertical/master');
        echo $this->Html->css('star-rating');
        echo $this->Html->script('store_admin/jquery-1.11.0.min');
        echo $this->Html->script('responsiveslides.min');
        echo $this->Html->script('bootstrap.min.js');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('validation/jquery.validate.js');
        echo $this->Html->script('validation/additional-methods');
        echo $this->Html->script('jquery.maskedinput');
        echo $this->Html->script('star-rating');
        echo $this->Html->script('jquery.blockUI.js');
        ?>
        <style>
            .header form {
                border-style: solid;
                border-width: 1px;
                float: right;
                padding: 15px;
                width: 650px;
            }
            .right-col{
                margin-left: 0;
            }
            .bg-btn-color{
                background-color:#ff3300;
            }
            <?php
            if (!empty($image)) {
                $image = "/merchantBackground-Image/" . $image;
                ?>
                body {
                    background: url("<?php echo $image; ?>") 0 0 / cover !important;
                    background-attachment:fixed;
                }
                <?php
            }
            ?>
        </style>
    </head>


    <body class="front-page-body theme-one">
        <div class="header clearfix">
            <div class="wrap">
                <div class="row">
                    <div class="col-md-4">
                        <?php if (empty($logo)) { ?>
                            <h1 class="pull-left">
                                <?php //echo $name; ?>
                                <?php echo $this->Html->link($name, array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false)); ?>
                            </h1>
                        <?php } else { ?>
                            <h1 class="restaurant-logo pull-left">
                                <?php echo $this->Html->link($this->Html->image('/merchantLogo/' . $logo, array('width' => 200)), array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false)); ?>
                            </h1>
                        <?php } ?>
                    </div>
                    <?php
                    if (empty($hqroleId)) {
                        echo $this->element('hquser/login');
                    } elseif ($this->Session->check('Auth.hqusers')) {
                        ?>
                        <div class="col-md-8">
                            <div class="welcome-text pull-right">Welcome : <span><?php echo ucfirst($_SESSION['Auth']['hqusers']['fname']); ?> | </span>
                                <ul>
                                    <li class="dropdown">
                                        <?php echo $this->Html->link('Profile <b class="caret"></b>', '', array("class" => "dropdown-toggle", "data-toggle" => "dropdown", 'escape' => false)); ?>
                                        <ul class="dropdown-menu">
                                            <li><?php echo $this->Html->link('Delivery Addresses', array('controller' => 'hqusers', 'action' => 'myDeliveryAddress')); ?></li>
                                            <li><?php echo $this->Html->link('Profile', array('controller' => 'hqusers', 'action' => 'myProfile')); ?></li>
                                            <li><?php echo $this->Html->link(__('My Favorites & Orders'), array('controller' => 'hqusers', 'action' => 'myOrders')); ?></li>
                                            <li><?php echo $this->Html->link(__('My Coupons'), array('controller' => 'hqusers', 'action' => 'myCoupons')); ?></li>
                                            <li><?php echo $this->Html->link(__('My Reviews'), array('controller' => 'hqusers', 'action' => 'myReviews')); ?></li>     
                                            <li><?php echo $this->Html->link(__('My Reservations'), array('controller' => 'hqusers', 'action' => 'myBookings')); ?></li>     
                                            <li><?php echo $this->Html->link('Logout', array('controller' => 'hqusers', 'action' => 'logout')); ?></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php }
                    ?>
                </div>

            </div>
        </div>

        <?php echo $this->element('merchant_menu'); ?>        
        <?php echo $this->fetch('content'); ?>
        <?php echo $this->element('merchant_footer'); ?>
    </body>
</html>
<script>
    $('.menu').on('click', function () {
        $('li').removeClass('active');
        $(this).parent().addClass('active');
        var merchant_id = $(this).attr('merchantId');
        var content_id = $(this).attr('contentId');
        var type_id = $(this).attr('typeId');
        $.ajax({
            url: "/hq/ajaxStaticContent",
            type: "Post",
            data: {typeId: type_id, contentId: content_id, merchantId: merchant_id},
            success: function (result) {
                $('#contentChange').html(result);
            }
        });
    });
    $(document).ready(function () {
        $('li.dropdown').hover(function () {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
        }, function () {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
        });
    });
</script>