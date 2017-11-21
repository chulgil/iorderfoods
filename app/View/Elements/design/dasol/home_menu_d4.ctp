<nav class="CTA-menu">
    <div class="navbar-header">
        <button type = "button" class = "navbar-toggle"
                data-toggle = "collapse" data-target = "#example-navbar-collapse">
            <span class = "sr-only">Toggle navigation</span>
            <span class = "icon-bar"></span>
            <span class = "icon-bar"></span>
            <span class = "icon-bar"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse custom-colapse" id = "example-navbar-collapse">
        <ul class="main-menu-list menu-inr-left">
            <?php
            if (!empty($store_data_app['StoreContent'])) {
                foreach ($store_data_app['StoreContent'] as $content) {
                    if ($content['page_position'] == 1) {
                        if ($content['name'] == 'HOME') {
                            ?>
                            <li class = "<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'login')) echo 'active'; ?>"><a href = "/users/login">Home</a></li>
                            <?php
                        }elseif ($content['name'] == 'REVIEWS') {
                            if ($store_data_app['Store']['review_page'] == 1) {
                                ?>
                                <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'allReviews')) echo 'active'; ?>"> <?php echo $this->Html->link('Reviews', array('controller' => 'pannels', 'action' => 'allReviews')); ?></li>
                                <?php
                            }
                        }elseif ($content['name'] == 'DEALS') {
                            if ($store_data_app['Store']['deal_page'] == 2) {
                                ?>
                                <li class="<?php if (($this->params['controller'] == 'deals' || $this->params['controller'] == 'Deals') && ($this->params['action'] == 'index')) echo 'active'; ?>"> <?php echo $this->Html->link('Deals', array('controller' => 'deals', 'action' => 'index')); ?></li>
                                <?php
                            }
                        }elseif ($content['name'] == 'RESERVATIONS') {
                            if (AuthComponent::User() && !empty($store_data_app['Store']['is_booking_open'])) {
                                ?>
                                <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'myBookings')) echo 'active'; ?>"><?php echo $this->Html->link(__('Reservations'), array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                <?php
                            }
                        }elseif ($content['name'] == 'STORE INFO') {
                            ?>
                            <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'storeLocation')) echo 'active'; ?>'><a href="/users/storeLocation/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Store Info</a></li>
                        <?php }elseif ($content['name'] == 'MENU') {
                            ?>
                            <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'products') && ($this->params['action'] == 'items')) echo 'active'; ?>'><a href="/products/items/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Menu</a></li>
                            <?php
                        }elseif ($content['name'] == 'GALLERY') {
                            if ($store_data_app['Store']['is_not_photo'] == 1) {
                                ?>
                                <li class="<?php if (($this->params['controller'] == 'pannels') && ($this->params['action'] == 'orderImages')) echo 'active'; ?>"><?php echo $this->Html->link('Gallery', array('controller' => 'pannels', 'action' => 'orderImages', $this->Encryption->encode($this->Session->read('store_id')), $this->Encryption->encode($this->Session->read('merchant_id')))); ?></li>
                                <?php
                            }
                        }else {
                            if ($content['name'] != 'PLACE ORDER' && $content['name'] != 'PHOTO') {
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
    </div>
</nav>