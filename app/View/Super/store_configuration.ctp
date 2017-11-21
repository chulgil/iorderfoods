<div class="row">
    <div class="col-lg-12">
        <h3>Manage Store Configuration Details</h3>
        <hr>
        <?php echo $this->Session->flash(); ?>
    </div>
</div>
<div class="row">
    <?php
    echo $this->Form->create('Store', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off')));
    echo $this->Form->input('StoreSetting.id', array('type' => 'hidden', 'value' => $ssData['StoreSetting']['id']));
    echo $this->Form->input('StoreSetting.store_id', array('type' => 'hidden', 'value' => $ssData['StoreSetting']['store_id']));
    echo $this->Form->input('ModulePermission.id', array('type' => 'hidden', 'value' => $mpData['ModulePermission']['id']));
    echo $this->Form->input('ModulePermission.store_id', array('type' => 'hidden', 'value' => $mpData['ModulePermission']['store_id']));
    ?>
    <div class="col-lg-12">
        <h4><strong>Payment :</strong></h4>
        <?php
        if (!empty($ssData['StoreSetting']['is_creditcard_allow'])) {
            $checkCCA = true;
        } else {
            $checkCCA = false;
        }
        echo $this->Form->input('StoreSetting.is_creditcard_allow', array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="col-lg-3"><label> Credit card',
            'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
            'div' => false,
            'checked' => $checkCCA
        ));
        if (!empty($ssData['StoreSetting']['paypal_allow'])) {
            $checkPA = true;
        } else {
            $checkPA = false;
        }
        echo $this->Form->input('StoreSetting.paypal_allow', array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="col-lg-4"><label> Paypal',
            'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
            'div' => false,
            'checked' => $checkPA
        ));
        ?>
    </div>
    <div class="clearfix"></div>
    <hr>
    <div class="col-lg-12">
        <h4><strong>Order Notification :</strong></h4>
        <?php
        if (!empty($ssData['StoreSetting']['kitchen_printer_allow'])) {
            $checkKPA = true;
        } else {
            $checkKPA = false;
        }
        echo $this->Form->input('StoreSetting.kitchen_printer_allow', array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="col-lg-3"><label> Kitchen Printer',
            'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
            'div' => false,
            'checked' => $checkKPA
        ));
        if (!empty($ssData['StoreSetting']['fax_allow'])) {
            $checkFA = true;
        } else {
            $checkFA = false;
        }
        echo $this->Form->input('StoreSetting.fax_allow', array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="col-lg-2"><label> Fax',
            'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
            'div' => false,
            'checked' => $checkFA
        ));
        if (!empty($ssData['StoreSetting']['twilio_sms_allow'])) {
            $checkTA = true;
        } else {
            $checkTA = false;
        }
        echo $this->Form->input('StoreSetting.twilio_sms_allow', array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="col-lg-2"><label> Twilio Sms',
            'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
            'div' => false,
            'checked' => $checkTA
        ));
        if (!empty($ssData['StoreSetting']['twilio_voice_allow'])) {
            $checkTVA = true;
        } else {
            $checkTVA = false;
        }
        echo $this->Form->input('StoreSetting.twilio_voice_allow', array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="col-lg-2"><label> Twilio Voice',
            'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
            'div' => false,
            'checked' => $checkTVA
        ));
        ?>
    </div>
    <div class="clearfix"></div>
    <hr>
    <div class="col-lg-12">
        <h4><strong>Order Type :</strong></h4>
        <div class="row">
            <div class="col-sm-3">
                <?php
                if (!empty($ssData['StoreSetting']['delivery_allow'])) {
                    $checkDA = true;
                } else {
                    $checkDA = false;
                }
                echo $this->Form->input('StoreSetting.delivery_allow', array(
                    'type' => 'checkbox',
                    'label' => false,
                    'before' => '<div class="col-lg-12"><label> Delivery',
                    'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                    'div' => false,
                    'checked' => $checkDA
                ));
                ?>
                <div class="col-lg-12">
                    <?php
                    if (!empty($ssData['StoreSetting']['before_tax_delivery'])) {
                        $checkBT = true;
                    } else {
                        $checkBT = false;
                    }
                    echo $this->Form->input('StoreSetting.before_tax_delivery', array(
                        'type' => 'checkbox',
                        'label' => false,
                        'before' => '<div class="col-lg-12"><label> Before Tax',
                        'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                        'div' => false,
                        'checked' => $checkBT
                    ));
                    ?>
                    <?php
                    if (!empty($ssData['StoreSetting']['delivery_zone_type'])) {
                        $checkDZT = true;
                    } else {
                        $checkDZT = false;
                    }
                    echo $this->Form->input('StoreSetting.delivery_zone_type', array(
                        'type' => 'checkbox',
                        'label' => false,
                        'before' => '<div class="col-lg-12"><label> Delivery Zone Type',
                        'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                        'div' => false,
                        'checked' => $checkDZT
                    ));
                    ?>
                </div>
            </div>
            <div class="col-sm-3">
                <?php
                if (!empty($ssData['StoreSetting']['pickup_allow'])) {
                    $checkPUA = true;
                } else {
                    $checkPUA = false;
                }
                echo $this->Form->input('StoreSetting.pickup_allow', array(
                    'type' => 'checkbox',
                    'label' => false,
                    'before' => '<div class="col-sm-12"><label> Pickup',
                    'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                    'div' => false,
                    'checked' => $checkPUA
                ));
                ?>
                <div class="col-lg-12">
                    <?php
                    if (!empty($ssData['StoreSetting']['before_tax_pickup'])) {
                        $checkBTP = true;
                    } else {
                        $checkBTP = false;
                    }
                    echo $this->Form->input('StoreSetting.before_tax_pickup', array(
                        'type' => 'checkbox',
                        'label' => false,
                        'before' => '<div class="col-lg-12"><label> Before Tax',
                        'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                        'div' => false,
                        'checked' => $checkBTP
                    ));
                    ?>
                </div>
            </div>
            <div class="col-sm-3">
                <?php
                if (!empty($ssData['StoreSetting']['reservations_allow'])) {
                    $checkRA = true;
                } else {
                    $checkRA = false;
                }
                echo $this->Form->input('StoreSetting.reservations_allow', array(
                    'type' => 'checkbox',
                    'label' => false,
                    'before' => '<div class="col-sm-12"><label> Reservations',
                    'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                    'div' => false,
                    'checked' => $checkRA
                ));
                ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div class="col-lg-12">
            <h4><strong>Other :</strong></h4>
            <?php
            if (!empty($mpData['ModulePermission']['kitchen_dashboard_allow'])) {
                $checkKDA = true;
            } else {
                $checkKDA = false;
            }
            echo $this->Form->input('ModulePermission.kitchen_dashboard_allow', array(
                'type' => 'checkbox',
                'label' => false,
                'before' => '<div class="col-lg-3"><label> Kitchen Dashboard',
                'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                'div' => false,
                'checked' => $checkKDA
            ));
            if (!empty($ssData['StoreSetting']['service_fee_allow'])) {
                $checkSFA = true;
            } else {
                $checkSFA = false;
            }
            echo $this->Form->input('StoreSetting.service_fee_allow', array(
                'type' => 'checkbox',
                'label' => false,
                'before' => '<div class="col-lg-2"><label> Service Fee',
                'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                'div' => false,
                'checked' => $checkSFA
            ));
            ?>
            <?php
            if (!empty($mpData['ModulePermission']['social_media_allow'])) {
                $checkSMA = true;
            } else {
                $checkSMA = false;
            }
            echo $this->Form->input('ModulePermission.social_media_allow', array(
                'type' => 'checkbox',
                'label' => false,
                'before' => '<div class="col-lg-2"><label> Social Media',
                'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                'div' => false,
                'checked' => $checkSMA
            ));
            ?>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div class="col-lg-12">
            <h4><strong>Merchant Online Order Button:</strong></h4>
            <?php
            if (!empty($ssData['StoreSetting']['merchant_online_order_btn'])) {
                $checkMOOB = true;
            } else {
                $checkMOOB = false;
            }
            echo $this->Form->input('StoreSetting.merchant_online_order_btn', array(
                'type' => 'checkbox',
                'label' => false,
                'before' => '<div class="col-lg-3"><label> Order Online',
                'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                'div' => false,
                'checked' => $checkMOOB
            ));
            if (!empty($ssData['StoreSetting']['merchant_btn_redirect'])) {
                $checkMBR = true;
            } else {
                $checkMBR = false;
            }
            echo $this->Form->input('StoreSetting.merchant_btn_redirect', array(
                'type' => 'checkbox',
                'label' => false,
                'before' => '<div class="col-lg-3"><label> Menu Redirect',
                'after' => '</label></div> &nbsp;&nbsp;&nbsp;',
                'div' => false,
                'checked' => $checkMBR
            ));
            ?>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div class="col-lg-12">
            <?php
            echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
            ?>             
            <?php
            echo $this->Html->link('Cancel', "/super/viewStoreDetails", array("class" => "btn btn-default", 'escape' => false));
            ?>
        </div>
    </div>
<?php echo $this->Form->end(); ?>
</div>
