<?php
$cArray = array('Products','products','Payments','payments');
$sidebarValue="";
if(in_array($this->params['controller'], $cArray)) {
    $sidebarValue = 'sidebarLatest';
}
?>
<aside class="left-col main-sidebar-old <?php echo $sidebarValue;?>">
    <a href="javascript:void(0);" class="mso-control"><i class="fa fa-bars"></i></a>
    <nav class="nav-menu">
        <a href="javascript:void(0)" class="small-screen-menu">MENU</a>
        <ul class="nav-dd clearfix">
            <?php
            if (!empty($store_data_app['StoreContent'])) {
                foreach ($store_data_app['StoreContent'] as $key => $content) {

                    if ($content['page_position'] == 1) {
                        if ($content['name'] == 'HOME') {
                            ?>
                            <li class="<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'login')) echo 'active'; ?>"><a href="/users/login">Home</a></li>
                            <?php
                        }elseif ($content['name'] == 'PLACE ORDER') {
                            if (AuthComponent::User() && !empty($store_data_app['StoreSetting']['order_allow'])) {
                                ?>
                                <li class="<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'customerDashboard')) echo 'active'; ?>">
                                <?php echo $this->Html->link('Place Order', array('controller' => 'users', 'action' => 'customerDashboard', $encrypted_storeId, $encrypted_merchantId)); ?>
                                </li>
                                <?php
                            }
                        } elseif ($content['name'] == 'RESERVATIONS') {
                            if (AuthComponent::User() && $store_data_app['Store']['is_booking_open'] == 1) {
                                ?>
                                <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'myBookings')) echo 'active'; ?>"><?php echo $this->Html->link(__('Reservations'), array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                <?php
                            }
                        }elseif ($content['name'] == 'STORE INFO') {
                            ?>
                            <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'storeLocation')) echo 'active'; ?>'><a href="/users/storeLocation/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Store Info</a></li>
                            <?php
                        }elseif ($content['name'] == 'PHOTO') {
                            if ($store_data_app['Store']['is_not_photo'] == 1) {
                                ?>
                                <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'storePhoto')) echo 'active'; ?>'><a href="/users/storePhoto/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Photos</a></li>
                                <?php
                            }
                        }elseif ($content['name'] == 'REVIEWS') {
                            if ($store_data_app['Store']['review_page'] == 1) {
                                ?>
                                <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'allReviews')) echo 'active'; ?>"> <?php echo $this->Html->link('Reviews', array('controller' => 'pannels', 'action' => 'allReviews', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                <?php
                            }
                        }elseif ($content['name'] == 'MENU') {
                            ?>
                            <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Menus') && ($this->params['action'] == 'menuItems')) echo 'active'; ?>'><a href="/products/items/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Menu</a></li>
                            <?php
                        }else {
                            if ($content['name'] != 'GALLERY' && $content['name'] != 'DEALS' && $content['name'] != 'PHOTO') {
                                ?>
                                <li class='<?php if (($this->params['controller'] == 'pannels') && ($this->params['action'] == 'staticContent') && ($this->params['pass'][3] == $content['name'])) echo 'active'; ?>'><?php echo $this->Html->link($content['name'], array('controller' => 'pannels', 'action' => 'staticContent', $this->Encryption->encode($this->Session->read('store_id')), $this->Encryption->encode($this->Session->read('merchant_id')), $this->Encryption->encode($content['id']), $content['name'])); ?></li>
                                <?php
                            }
                        }
                    }
                }
            }
            ?>
            <?php if (!empty($store_data_app['StoreContent'])) { ?>
                <?php
                $m = 0;
                foreach ($store_data_app['StoreContent'] as $content) {
                    if ($content['page_position'] == 3) {
                        if ($m == 0) {
                            ?>
                            <li>
                                <a href="javascript:void(0)">MORE INFO</a>
                                <ul class="list-style-none">
                                <?php }
                                ?>
                                <li class='<?php if (($this->params['controller'] == 'pannels') && ($this->params['action'] == 'staticContent') && ($this->params['pass'][3] == $content['name'])) echo 'active'; ?>'><?php echo $this->Html->link($content['name'], array('controller' => 'pannels', 'action' => 'staticContent', $this->Encryption->encode($this->Session->read('store_id')), $this->Encryption->encode($this->Session->read('merchant_id')), $this->Encryption->encode($content['id']), $content['name'])); ?>
                                </li>
                                <?php
                                $m++;
                            }
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }
            ?>
        </ul>
    </nav>

    <div class="call-us"><a href="tel:<?php echo $_SESSION['store_phone']; ?>"><i class="fa fa-phone"></i> CALL US @ <?php echo $_SESSION['store_phone']; ?></a></div>


    <?php
    if ($store_data_app['Store']['store_hours']) {
        echo $this->element('store_hours');
    }
    ?>


</aside>
<script>
    $(document).ready(function () {
        $(document).on('click', '.mso-control', function () {
            $(this).parent('.main-sidebar-old').toggleClass('mso-show');
        });
    });
</script>
