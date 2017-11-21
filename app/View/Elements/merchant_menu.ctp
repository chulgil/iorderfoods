<style>
    .front-nav ul li a {
        color: #000;
        display: block;
        padding: 10px 20px;
    }
</style>
<div class="front-nav">
    <div class="wrap">
        <ul>
            <li class="dropdown">
                <?php echo $this->Html->link('Locations <b class="caret"></b>', array('controller' => 'hqusers', 'action' => 'location', $this->Encryption->encode($this->Session->read('hq_id'))), array('onclick' => "window.location.href='/hqusers/merchant'","class" => "dropdown-toggle", "data-toggle" => "dropdown", 'escape' => false)); ?>
                <ul class="dropdown-menu">
                    <?php
                    if (!empty($storeCity)) {
                        foreach ($storeCity as $city) {
                            ?>
                            <li><?php echo $this->Html->link(ucfirst($city['Store']['city']), array('controller' => 'hqusers', 'action' => 'location', $city['Store']['city']), array('escape' => false)); ?></li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </li>
            <!--            <li class="locate <?php
            if (($this->params['controller'] == 'hqusers') && ($this->params['action'] == 'merchant' || $this->params['action'] == 'location')) {
                echo 'active';
            }
            ?>">
            <?php echo $this->Html->link('Locations', array('controller' => 'hqusers', 'action' => 'location', $this->Encryption->encode($this->Session->read('hq_id')))); ?>
                        </li>-->
            <li class="locate <?php
            if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'gallery') {
                echo 'active';
            }
            ?>">
                    <?php echo $this->Html->link('Gallery', array('controller' => 'hqusers', 'action' => 'gallery', $this->Encryption->encode($this->Session->read('hq_id')))); ?>
            </li>
            <li class="locate <?php
                if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'newsletter') {
                    echo 'active';
                }
                ?>">
                    <?php echo $this->Html->link('Newsletter', array('controller' => 'hqusers', 'action' => 'newsletter', $this->Encryption->encode($this->Session->read('hq_id')))); ?>
            </li>
            <!--            <li>
                            <a href='javascript:void(0);' class='menu' merchantId='<?php echo $this->Session->read('hq_id'); ?>' contentId='0' typeId='3'>Photos</a>
                        </li>-->
            <li class="locate <?php
                if ($this->params['controller'] == 'hqdeals' && $this->params['action'] == 'index') {
                    echo 'active';
                }
                ?>">
                <?php echo $this->Html->link('Promotions', array('controller' => 'hqdeals', 'action' => 'index')); ?>
            </li>
            <?php
            if (!empty($merchantList)) {
                foreach ($merchantList as $content) {
                    if ($content['MerchantContent']['page_position'] == 1) {
                        ?>
                        <li class='<?php if (($this->params['controller'] == 'hqusers') && ($this->params['action'] == 'staticContent') && ($this->params['pass'][2] == $content['MerchantContent']['name'])) echo 'active'; ?>'><?php echo $this->Html->link($content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'staticContent', $this->Encryption->encode($this->Session->read('hq_id')), $this->Encryption->encode($content['MerchantContent']['id']), $content['MerchantContent']['name'])); ?></li>
                        <!--                        <li>
                                                    <a href='javascript:void(0);' class='menu' merchantId='<?php echo $content['MerchantContent']['merchant_id']; ?>' contentId='<?php echo $content['MerchantContent']['id']; ?>' typeId='2'><?php echo $content['MerchantContent']['name']; ?></a>
                                                </li>-->
                        <?php
                    }
                }
            }
            ?>
        </ul>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('li.dropdown').hover(function () {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
        }, function () {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
        });
    });
</script>