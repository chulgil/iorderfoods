<style>
    .icon-box {    
        padding: 3px 7px;
    }
</style>
<div class="site-info">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="pull-left">
                    <div>
                        <i class="fa fa-phone"></i>
                        <span>
                            <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a>
                        </span>
                    </div>
                    <div>
                        <i class="fa fa-envelope"></i>
                        <span>
                            <a href="mailto:<?php echo $m_email; ?>"><?php echo $m_email; ?></a>
                        </span>
                    </div>
                </div>
                <div class="pull-right">
                    <?php
                    $userId = $this->Session->read('Auth.hqusers.id');
                    if (!empty($userId)) {
                        ?>
                        <span>
                            <ul class="list-style-none">
                                <li class="dropdown">
                                    <?php echo $this->Html->link($this->Html->image('hq/admin-user.png', array('alt' => 'user')) . ucfirst($_SESSION['Auth']['hqusers']['fname']) . ' <b class="caret"></b>', '', array("class" => "dropdown-toggle", "data-toggle" => "dropdown", 'escape' => false)); ?>
                                    <ul class="dropdown-menu">
                                        <li><?php echo $this->Html->link('Delivery Addresses', array('controller' => 'hqusers', 'action' => 'myDeliveryAddress')); ?></li>
                                        <li><?php echo $this->Html->link('Profile', array('controller' => 'hqusers', 'action' => 'myProfile')); ?></li>
                                        <li><?php echo $this->Html->link(__('My Favorites & Orders'), array('controller' => 'hqusers', 'action' => 'myOrders')); ?></li>
                                        <li><?php echo $this->Html->link(__('My Coupons'), array('controller' => 'hqusers', 'action' => 'myCoupons')); ?></li>
                                        <li><?php echo $this->Html->link(__('My Reviews'), array('controller' => 'hqusers', 'action' => 'myReviews')); ?></li>     
                                        <li><?php echo $this->Html->link(__('My Reservations'), array('controller' => 'hqusers', 'action' => 'myBookings')); ?></li>     
                                    </ul>
                                </li>
                            </ul>
                        </span>
                        <span>
                            <?php echo $this->Html->image('hq/seprator-dots.png', array('alt' => 'dots')) ?>
                        </span>
                        <?php echo $this->Html->link('Logout', array('controller' => 'hqusers', 'action' => 'logout')); ?>
                        <?php
                    } else {
                        echo $this->element('hquser/login');
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>