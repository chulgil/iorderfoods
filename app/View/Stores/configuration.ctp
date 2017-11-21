<?php echo $this->Html->script('jquery.maskedinput'); ?>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<style>
    #map{ width: 600px; height: 200px; border-style: dotted solid; }
    @media (max-width:860px) {
        #map{ width: 100%; }
    }
    .spacing{margin-left:5px;}
    .order-mode td { vertical-align:top;}
</style>
<?php
$latitude = $this->request->data['Store']['latitude'];
$logitude = $this->request->data['Store']['logitude'];
?>
<div class="row">
    <div class="col-lg-6">
        <h3>Manage Store Configuration Details</h3>
        <?php echo $this->Session->flash(); ?>
    </div>
    <div class="col-lg-6">
        <div class="addbutton">
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>


<div class="row">
    <?php
    echo $this->Form->create('Stores', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'StoreConfiguration', 'enctype' => 'multipart/form-data'));
    echo $this->Form->input('store_theme', array('type' => 'hidden', 'value' => @$this->request->data['Store']['store_theme_id']));
    ?>
    <div class="col-lg-6">
        <div class="form-group form_spacing">
            <label>Store Name</label>
            <?php
            echo $this->Form->input('Store.store_name', array('type' => 'text', 'class' => 'form-control', 'Placeholder' => 'Enter Store Name'));
            ?>
        </div>

        <div class="form-group form_spacing">
            <?php
            echo $this->Form->input('User.role_id', array('type' => 'hidden', 'value' => $roleid));
            echo $this->Form->input('Store.id', array('type' => 'hidden', 'value' => $storeId));
            echo $this->Form->input('User.id', array('type' => 'hidden', 'value' => $userid));
            ?>
            <label>Address</label>
            <?php
            echo $this->Form->input('Store.address', array('type' => 'textarea', 'rows' => '5', 'cols' => '5', 'class' => 'form-control', 'Placeholder' => 'Enter Address'));
            ?>
        </div>
        <div class="form-group form_margin">
            <label>City</label>
            <?php
            echo $this->Form->input('Store.city', array('type' => 'text', 'class' => 'form-control', 'Placeholder' => 'Enter City'));
            ?>
        </div>
        <div class="form-group form_margin">
            <label>State</label>
            <?php
            echo $this->Form->input('Store.state', array('type' => 'text', 'class' => 'form-control', 'Placeholder' => 'Enter State'));
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Zipcode</label>
            <?php
            echo $this->Form->input('Store.zipcode', array('type' => 'text', 'class' => 'form-control', 'Placeholder' => 'Enter Zipcode', 'maxlength' => '5'));
            ?>
        </div>

        <div class="form-group form_margin">
            <label>Phone No.</label>
            <?php
            echo $this->Form->input('Store.phone', array('type' => 'text', 'class' => 'form-control', 'Placeholder' => 'Enter Contact Number', 'data-mask' => 'mobileNo'));
            ?>
            <span class="blue">(eg. 111-111-1111)</span>
        </div>

        <?php if ($latitude && $logitude) { ?>

            <script src="//maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY;?>&callback=initMap" async="" defer="defer" type="text/javascript"></script>

            </style>
            <div id="map"></div>
            <script>
                var marker;
                function initMap() {
                    var myLatlng = new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $logitude; ?>);
                    var mapOptions = {
                        zoom: 14,
                        center: myLatlng,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    }
                    var map = new google.maps.Map(document.getElementById("map"), mapOptions);
                    marker = new google.maps.Marker({
                        position: myLatlng
                    });
                    marker.setMap(map);
                }
            </script>
        <?php } ?>

        <hr />
        <div class="form-group form_spacing">
            <label>Time Zone</label>
            <?php
            echo $this->Form->input('Store.time_zone_id', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $timeZoneList, 'empty' => 'Select'));
            ?>
        </div>
        <!--        <div class="form-group form_spacing">
                    <label>Daylight Saving Time </label>
        <?php
//            $checked = "";
//            if ($this->request->data['Store']['dst'] == 1) {
//                $checked = "checked";
//            }
        //echo $this->Form->checkbox('Store.dst', array('checked' => $checked));
        ?>
                </div>-->

        <div class="form-group form_spacing">
            <label>Time Format<span class="required"> * </span></label>
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Store.time_formate', array(
                'type' => 'radio',
                'options' => array('1' => '12-hour&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => '24-hour'),
                'default' => 1
            ));
            echo $this->Form->error('Store.time_formate');
            ?>
        </div>

        <hr/>

        <?php if (!empty($storeSetting['StoreSetting']['is_creditcard_allow'])) { ?>
            <div class="form-group form_spacing">
                <label>Pay by Credit Card </label>
                <?php
                $checkedC = "";
                if ($this->request->data['Store']['is_pay_by_credit_card'] == 1) {
                    $checkedC = "checked";
                }
                echo $this->Form->checkbox('Store.is_pay_by_credit_card', array('checked' => $checkedC));
                ?>
            </div>
            <?php
            $cType = array('Visa' => 'Visa', 'Master' => 'Master', 'Discover' => 'Discover', 'Amex' => 'Amex');
            $cardType = '';
            if (!empty($this->request->data['Store']['credit_card_type'])) {
                $cardType = explode(',', $this->request->data['Store']['credit_card_type']);
            }
            foreach ($cType as $key => $data) {
                //pr($data);
                if (!empty($cardType) && in_array($data, $cardType)) {
                    $checked = true;
                } else {
                    $checked = false;
                }
                echo "<div class='new-chkbx-wrap'>";
                echo "<label>" . $data . "</label>";
                echo $this->Form->checkbox('Store.credit_card_type.' . $key, array('hiddenField' => false, 'multiple' => 'checkbox', 'class' => 'case', 'value' => $data, 'checked' => $checked));
                echo "</div>";
            }
            ?>
        <?php } ?>
        <?php if (!empty($storeSetting['StoreSetting']['paypal_allow'])) { ?>
            <div class="form-group form_spacing">
                <label>PayPal Express</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['is_express_check_out'] == 1) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.is_express_check_out', array('checked' => $checked));
                ?>
            </div>
            <div class="form-group form_spacing">
                <label>Paypal Mode<span class="required"> * </span></label>
                &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                echo $this->Form->input('Store.paypal_mode', array(
                    'type' => 'radio',
                    'options' => array('0' => 'Sandbox&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '1' => 'Live'),
                    'default' => 0
                ));
                echo $this->Form->error('Store.paypal_mode');
                ?>
            </div>
            <div>
                <span class="blue">(Paypal Configuration details)</span>
            </div>

            <div class="form-group form_margin">
                <label>Paypal Business Email</label>
                <?php
                echo $this->Form->input('Store.paypal_business_email', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Paypal Email'));
                ?>
            </div>
            <!-- Paypal Do Direct Start -->

            <div class="form-group form_margin">
                <label>Paypal Api Username</label>
                <?php
                echo $this->Form->input('Store.paypal_email', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Paypal Email'));
                ?>
            </div>
            <div class="form-group form_margin">
                <label>Paypal Api Password</label>
                <?php
                echo $this->Form->input('Store.paypal_password', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Paypal Password'));
                ?>
            </div>
            <div class="form-group form_margin">
                <label>Paypal Api Signature</label>
                <?php
                echo $this->Form->input('Store.paypal_signature', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Paypal Signature'));
                ?>
            </div>
        <?php } ?>
        <!-- Paypal Do Direct Start -->

        <div class="form-group form_margin">
            <?php if (!empty($storeSetting['StoreSetting']['kitchen_printer_allow'])) { ?>
                <table width="100%"><thead>
                        <tr>
                            <th width="25%" >
                                <label>Kitchen Printer</label>
                                <?php
                                $checked = "";
                                if ($this->request->data['Store']['is_kitchen_printer']) {
                                    $checked = "checked";
                                }
                                echo $this->Form->checkbox('Store.is_kitchen_printer', array('checked' => $checked));
                                ?>
                            </th>
                            <th width="25%" >
                                <label>Receipt Printer</label>
                                <?php
                                $checked = "";
                                if ($this->request->data['Store']['is_receipt_printer']) {
                                    $checked = "checked";
                                }
                                echo $this->Form->checkbox('Store.is_receipt_printer', array('checked' => $checked));
                                ?>
                            </th><th width="25%" >
                                <label>DineIn Printer</label>
                                <?php
                                $checked = "";
                                if ($this->request->data['Store']['is_dinein_printer']) {
                                    $checked = "checked";
                                }
                                echo $this->Form->checkbox('Store.is_dinein_printer', array('checked' => $checked));
                                ?>
                            </th>
                            <th width="25%" >
                                <label>Signature</label>
                                <?php
                                $checked = "";
                                if ($this->request->data['Store']['is_signature']) {
                                    $checked = "checked";
                                }
                                echo $this->Form->checkbox('Store.is_signature', array('checked' => $checked));
                                ?>
                            </th>
                            <th width="25%" ></th>
                        <tr>
                            <th width="25%" >
                                <label>Kitchen Category</label>
                                <?php
                                $checked = "";
                                if ($this->request->data['Store']['is_kitchen_category']) {
                                    $checked = "checked";
                                }
                                echo $this->Form->checkbox('Store.is_kitchen_category', array('checked' => $checked));
                                ?>
                            </th>
                            <th width="25%" >
                                <label>Receipt Category</label>
                                <?php
                                $checked = "";
                                if ($this->request->data['Store']['is_receipt_category']) {
                                    $checked = "checked";
                                }
                                echo $this->Form->checkbox('Store.is_receipt_category', array('checked' => $checked));
                                ?>
                            </th><th width="25%" >
                                <label></label>
                            </th>
                            <th width="25%" >
                                <label></label>
                            </th>
                            <th width="25%" ></th>
                    </thead>
                </table>

                <div class="form-group form_margin"><label>Printer IP</label>
                    <?php
                    echo $this->Form->input('Store.printer_location', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Printer IP'));
                    ?>
                    <span class="blue">(eg. "192.168.0.251")</span>
                </div>
            <?php } ?>
            <?php if (!empty($storeSetting['StoreSetting']['fax_allow'])) { ?>
                <div><span class="blue">(Interfax Configuration details)</span></div>

                <div class="form-group form_margin">
                    <label>Username</label>
                    <?php
                    echo $this->Form->input('Store.fax_username', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Interfax Username'));
                    ?>
                </div>

                <div class="form-group form_margin">
                    <label>Password</label>
                    <?php
                    echo $this->Form->input('Store.fax_password', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Interfax Password'));
                    ?>
                </div>

                <div class="form-group form_margin">
                    <label>Fax Number</label>
                    <?php
                    echo $this->Form->input('Store.fax_number', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Fax Number'));
                    ?>
                    <span class="blue">(eg. "+11552534942")</span>
                </div>
            <?php } ?>
            <div>
                <?php if (!empty($storeSetting['StoreSetting']['twilio_sms_allow'])) { ?>
                    <hr/>
                    <span class="blue">(Twilio Configuration details)</span>
                </div>
                <div class="form-group form_margin">
                    <label>Twilio Sms Gateway Number</label>
                    <?php
                    echo $this->Form->input('Store.twilio_number', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Twilio Sms Gateway Number'));
                    ?>
                </div>
                <div class="form-group form_margin">
                    <label>Twilio api Key</label>
                    <?php
                    echo $this->Form->input('Store.twilio_api_key', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Twilio Api Key'));
                    ?>
                </div>
                <div class="form-group form_margin">
                    <label>Twilio api token</label>
                    <?php
                    echo $this->Form->input('Store.twilio_api_token', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Twilio Api Token'));
                    ?>
                </div>
                <div>
                <?php } ?>
                <hr/>
            </div>

            <?php if (!empty($storeSetting['StoreSetting']['delivery_allow']) || !empty($storeSetting['StoreSetting']['pickup_allow']) || !empty($storeSetting['StoreSetting']['reservations_allow'])) { ?>
                <div class="form-group form_spacing">
                    <label>Calendar # Advance Days</label>
                    <table width="100%">
                        <thead>
                            <tr>
                                <?php if (!empty($storeSetting['StoreSetting']['delivery_allow'])) { ?>
                                    <th width="33%" >
                                        <label>Delivery</label>
                                    </th>
                                <?php } ?>
                                <?php if (!empty($storeSetting['StoreSetting']['pickup_allow'])) { ?>
                                    <th width="33%" >
                                        <label>Pick-up</label>
                                    </th>
                                <?php } ?>
                                <?php if (!empty($storeSetting['StoreSetting']['reservations_allow'])) { ?>
                                    <th width="33%" >
                                        <label>Dine-in</label>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <?php if (!empty($storeSetting['StoreSetting']['delivery_allow'])) { ?>
                                    <td>
                                        <?php
                                        echo $this->Form->input('Store.deliverycalendar_limit', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Calendar Limit'));
                                        ?>
                                    </td>
                                <?php } ?>
                                <?php if (!empty($storeSetting['StoreSetting']['pickup_allow'])) { ?>
                                    <td>
                                        <?php
                                        echo $this->Form->input('Store.pickcalendar_limit', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Calendar Limit'));
                                        ?>
                                    </td>
                                <?php } ?>
                                <?php if (!empty($storeSetting['StoreSetting']['reservations_allow'])) { ?>
                                    <td>
                                        <?php
                                        echo $this->Form->input('Store.calendar_limit', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Calendar Limit'));
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
            <?php if (!empty($storeSetting['StoreSetting']['delivery_allow']) || !empty($storeSetting['StoreSetting']['pickup_allow']) || !empty($storeSetting['StoreSetting']['reservations_allow'])) { ?>
                <div class="form-group form_spacing">
                    <label>Calendar # Blackout Days</label>
                    <table width="100%">
                        <thead>
                            <tr>
                                <?php if (!empty($storeSetting['StoreSetting']['delivery_allow'])) { ?>
                                    <th width="33%" >
                                        <label>Delivery</label>
                                    </th>
                                <?php } ?>
                                <?php if (!empty($storeSetting['StoreSetting']['pickup_allow'])) { ?>
                                    <th width="33%" >
                                        <label>Pick-up</label>
                                    </th>
                                <?php } ?>
                                <?php if (!empty($storeSetting['StoreSetting']['reservations_allow'])) { ?>
                                    <th width="33%" >
                                        <label>Dine-in</label>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <?php if (!empty($storeSetting['StoreSetting']['delivery_allow'])) { ?>
                                    <td>
                                        <?php
                                        echo $this->Form->input('Store.deliveryblackout_limit', array('type' => 'number', 'class' => 'form-control', 'Placeholder' => 'Enter Calendar Blackout days'));
                                        ?>
                                    </td>
                                <?php } ?>
                                <?php if (!empty($storeSetting['StoreSetting']['pickup_allow'])) { ?>
                                    <td>
                                        <?php
                                        echo $this->Form->input('Store.pickblackout_limit', array('type' => 'number', 'class' => 'form-control', 'Placeholder' => 'Enter Calendar Blackout days'));
                                        ?>
                                    </td>
                                <?php } ?>
                                <?php if (!empty($storeSetting['StoreSetting']['reservations_allow'])) { ?>
                                    <td>
                                        <?php
                                        echo $this->Form->input('Store.dineinblackout_limit', array('type' => 'number', 'class' => 'form-control', 'Placeholder' => 'Enter Calendar Blackout days'));
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        </tbody>


                    </table><hr/>
                </div>
            <?php } ?>
            <?php if (!empty($storeSetting['StoreSetting']['delivery_allow'])) { ?>
                <div class="form-group form_margin">
                    <label>Cut-off Time</label>
                    <?php
                    echo $this->Form->input('Store.cutoff_time', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Cutoff Time'));
                    ?>
                    <span class="blue">Please enter Time in Minutes. i.e 10,20,60,90,..</span>
                </div>
            <?php } ?>

            <?php if (!empty($storeSetting['StoreSetting']['delivery_allow'])) { ?>
                <div class="form-group form_margin">
                    <label>Delivery Delay Time</label>
                    <?php
                    echo $this->Form->input('Store.delivery_delay', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Delivery Delay time'));
                    ?>
                    <span class="blue">Please enter Time in Minutes. i.e 10,20,60,90,..</span>
                </div>
            <?php } ?>
            <?php if (!empty($storeSetting['StoreSetting']['pickup_allow'])) { ?>
                <div class="form-group form_margin">
                    <label>Pick-up Delay Time</label>
                    <?php
                    echo $this->Form->input('Store.pick_up_delay', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Pickup Delay time'));
                    ?>
                    <span class="blue">Please enter Time in Minutes. i.e 10,20,60,90,..</span>
                </div>
            <?php } ?>

            <div class="form-group form_spacing">
                <!--<table cellpadding="6">-->
                <table width="100%" class="order-mode">
                    <thead>
                        <tr>
                            <?php if (!empty($storeSetting['StoreSetting']['delivery_allow'])) { ?>
                                <th width="33%" >
                                    <label>Delivery</label>
                                    <?php
                                    $checked = "";
                                    if ($this->request->data['Store']['is_delivery']) {
                                        $checked = "checked";
                                    }
                                    echo $this->Form->checkbox('Store.is_delivery', array('checked' => $checked));
                                    ?>
                                </th>
                            <?php } ?>
                            <?php if (!empty($storeSetting['StoreSetting']['pickup_allow'])) { ?>
                                <th width="33%" >
                                    <label>Pick-up</label>
                                    <?php
                                    $checked = "";
                                    if ($this->request->data['Store']['is_take_away']) {
                                        $checked = "checked";
                                    }
                                    echo $this->Form->checkbox('Store.is_take_away', array('checked' => $checked));
                                    ?>

                                </th>
                            <?php } ?>
                            <?php if (!empty($storeSetting['StoreSetting']['reservations_allow'])) { ?>
                                <th width="33%" >
                                    <label>Dine-in</label>
                                    <?php
                                    $checked = "";
                                    if ($this->request->data['Store']['is_booking_open']) {
                                        $checked = "checked";
                                    }
                                    echo $this->Form->checkbox('Store.is_booking_open', array('checked' => $checked));
                                    ?>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    
                    
                    
                    <tbody>
                        <?php if (empty($storeSetting['StoreSetting']['pos_menu_allow'])) { ?>
                        <tr>
                            <td><label>Set Order Status</label>
                            
                            <?php                                 
                                if($deliveryStatus){
                                    foreach($deliveryStatus as $dkey => $value){
                                ?>
                                
                                <?php
                                    
                                    $checked = "";
                                    if(in_array($value['OrderStatus']['id'],$this->request->data['StoreSetting']['delivery_status']))    {
                                        $checked = "checked";
                                    }
                                    echo "<br>";
                                    echo $this->Form->checkbox('deliverystatus.'.$value['OrderStatus']['id'], array('checked' => $checked));
                                    echo "<span class='spacing'>".$value['OrderStatus']['name']."</span>";
                               
                                
                                
                                    } 
                                }
                            ?>
                            
                            </td>
                            
                            <td><label>Set Order Status</label>
                            
                                <?php     
                                    
                                    if($pickupStatus){
                                        foreach($pickupStatus as $pkey => $pvalue){
                                    $checked = "";
                                    
                                    if(in_array($pvalue['OrderStatus']['id'],$this->request->data['StoreSetting']['pickup_status']))    {
                                        $checked = "checked";
                                    }
                                    
                                    
                                    
                                    echo "<br>";
                                    
                                    echo $this->Form->checkbox('pickupstatus.'.$pvalue['OrderStatus']['id'], array('checked' => $checked));
                                    echo "<span class='spacing'>".$pvalue['OrderStatus']['name']."</span>";

                                        } 
                                    }
                                ?>
                            
                            
                            
                            
                            
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php } ?>

                        <tr>
                                
                            
                                
                            
                        </tr>

                    </tbody>
                    
                    
                    
                    
                    
                    
                    

                    <tbody>
                        <tr>
                            <?php if (!empty($storeSetting['StoreSetting']['delivery_allow'])) { ?>
                                <td>

                                    <label>Min. amount($)</label>
                                    <?php
                                    echo $this->Form->input('Store.minimum_order_price', array('type' => 'text', 'class' => 'form-control', 'style' => "width:95%;"));
                                    ?>

                                    <?php
                                    if (!empty($storeSetting['StoreSetting']['delivery_allow'])) {
                                        if (!empty($storeSetting['StoreSetting']['before_tax_delivery'])) {
                                            ?>
                                            <label>Before Tax</label>
                                            <?php
                                            $checked = "";
                                            if ($this->request->data['Store']['is_delivery_beftax']) {
                                                $checked = "checked";
                                            }
                                            echo $this->Form->checkbox('Store.is_delivery_beftax', array('checked' => $checked));
                                            ?>
                                        <?php } else {
                                            ?>
                                            <label>&nbsp;</label>
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                            <?php } ?>
                            <?php if (!empty($storeSetting['StoreSetting']['pickup_allow'])) { ?>
                                <td>
                                    <label>Min. amount($)</label>
                                    <?php
                                    echo $this->Form->input('Store.minimum_takeaway_price', array('type' => 'text', 'class' => 'form-control', 'style' => "width:95%;"));
                                    ?>
                                    <?php
                                    if (!empty($storeSetting['StoreSetting']['pickup_allow'])) {
                                        if (!empty($storeSetting['StoreSetting']['before_tax_pickup'])) {
                                            ?>
                                            <label>Before Tax</label>
                                            <?php
                                            $checked = "";
                                            if ($this->request->data['Store']['is_pick_beftax']) {
                                                $checked = "checked";
                                            }
                                            echo $this->Form->checkbox('Store.is_pick_beftax', array('checked' => $checked));
                                            ?>
                                            <?php
                                        } else {
                                            echo "<label>&nbsp;</label>";
                                        }
                                    }
                                    ?>
                                </td>
                            <?php } ?>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <?php if (!empty($storeSetting['StoreSetting']['delivery_allow'])) { ?>
                                <td>
                                    <label>Delivery Details</label>
                                    <div class="form-group form_spacing">
                                        <?php
                                        echo $this->Form->input('Store.delivery_description', array('type' => 'textarea', 'rows' => '5', 'cols' => '5', 'class' => 'form-control', 'Placeholder' => 'Delivery Details', 'style' => "width:95%;"));
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (!empty($storeSetting['StoreSetting']['pickup_allow'])) { ?>
                                <td>
                                    <label>Pick-up Details</label>
                                    <div class="form-group form_spacing">
                                        <?php
                                        echo $this->Form->input('Store.take_away_description', array('type' => 'textarea', 'rows' => '5', 'cols' => '5', 'class' => 'form-control', 'Placeholder' => 'Pick-up Details', 'style' => "width:95%;"));
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (!empty($storeSetting['StoreSetting']['reservations_allow'])) { ?>
                                <td>
                                    <label>Dine-in Details</label>
                                    <div class="form-group form_spacing">
                                        <?php
                                        echo $this->Form->input('Store.dine_in_description', array('type' => 'textarea', 'rows' => '5', 'cols' => '5', 'class' => 'form-control', 'Placeholder' => 'Dine-in Details', 'style' => "width:95%;"));
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>

                    </tbody>

                </table>




                <!--<label>Minimum Order amount($)</label>-->
                <?php
//echo $this->Form->input('Store.minimum_order_price',array('type'=>'text','class'=>'form-control'));
                ?>
                <!--<br/><label>Is Booking Open</label>-->
                <?php
//$checked="";
//if($this->request->data['Store']['is_booking_open']){
//  $checked="checked";
//}
// echo $this->Form->checkbox('Store.is_booking_open',array('checked'=>$checked));
                ?>
            </div>

            <hr/>
            <div class="form-group form_margin">
                <label>Notification Email</label>
                <?php
                echo $this->Form->input('Store.notification_email', array('type' => 'text', 'class' => 'form-control'));
                ?>
                <span class="blue">(Store admin will get Notifications on this email)</span>
            </div>
            <div class="form-group form_margin">
                <label>Notification Phone no.</label>
                <?php
                echo $this->Form->input('Store.notification_number', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone_number'));
                ?>
                <span class="blue">(Store admin will get Notifications on this phone number eg. 111-111-1111)</span>
            </div>
            <div class="form-group form_margin">
                <label>Notification Voice Calls</label>
                <?php
                echo $this->Form->input('Store.notification_voice', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone_number'));
                ?>
                <span class="blue">(Store admin will get voice calls on this phone number eg. 111-111-1111)</span>
            </div>

            <div class="form-group form_margin">
                <label>Notification Type</label>
                <?php
                $options = array('1' => 'Voice/Email', '2' => 'Text/Voice', '3' => 'All');
                echo $this->Form->input('Store.notification_type', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => false));
                ?>
            </div>


            <div class="form-group form_margin">
                <label>Kitchen Dashboard Display</label>
                <?php
                $options = array('1' => 'List', '2' => 'Grid/Post-it');
                echo $this->Form->input('Store.kitchen_dashboard_type', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => false));
                ?>
            </div>


            <hr/>
            <!--            <div class="form-group form_margin">
                            <label>Delivery fee($)</label>
            <?php
            //echo $this->Form->input('Store.delivery_fee', array('type' => 'text', 'class' => 'form-control'));
            ?>
                        </div>-->
            <?php if (!empty($storeSetting['StoreSetting']['service_fee_allow'])) { ?>
                <div class="form-group form_margin">
                    <label>Service fee($)</label><br/>
                    <?php
                    echo $this->Form->input('Store.service_fee_type', array('type' => 'radio', 'class' => '', 'options' => array(1 => '&nbsp;Price&nbsp;&nbsp;&nbsp;', 2 => '&nbsp;%&nbsp;&nbsp;'), 'label' => true, 'default' => 1));
                    ?>
                    <?php
                    echo $this->Form->input('Store.service_fee', array('type' => 'text', 'class' => 'form-control'));
                    ?>
                </div>
            <?php } ?>
            <?php if (empty($storeSetting['StoreSetting']['pos_menu_allow'])) { ?>
            <div>
                <hr/>
                <span class="blue">(Tax Configuration details)</span>
            </div>
            <div class="form-group form_spacing">
                <table cellpadding="6">
                    <thead>
                        <tr>
                            <th style="width:90px;">
                                <label>Tax 1 (%)</label>
                            </th>
                            <th style="width:90px;">
                                <label>Tax 2 (%)</label>
                            </th>
                            <th style="width:90px;">
                                <label>Tax 3 (%)</label>
                            </th>
                            <th style="width:90px;">
                                <label>Tax 4 (%)</label>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <?php foreach ($this->request->data['StoreTax'] as $key => $value) {
                                ?>
                                <td>
                                    <?php
                                    echo $this->Form->input('StoreTax.' . $key . '.id', array('type' => 'hidden', 'value' => $value['StoreTax']['id']));
                                    echo $this->Form->input('StoreTax.' . $key . '.tax_value', array('type' => 'text', 'class' => 'form-control', 'style' => "width:80px;", 'value' => $value['StoreTax']['tax_value']));
                                    ?>
                                </td>
                            <?php } ?>
                        </tr>
                    </tbody>

                </table>
                <span class="blue">(Enter Tax in the % format. i.e 8%)</span>
            </div>
            <?php } ?>

            <hr/>
            
            <div class="form-group form_spacing">
                <label>Tip allowed</label>
                <?php
                $checked = "";
                $displayTipDiv = 'hidden';
                if ($this->request->data['Store']['tip'] == 1) {
                    $checked = "checked";
                    $displayTipDiv = '';
                }
                echo $this->Form->checkbox('Store.tip', array('checked' => $checked));
                ?>
            </div>
            <div id="tip_section" class="<?php echo $displayTipDiv;?>">
                <div>
                    <span class="blue">(Tip Configuration details)</span>
                </div>
                <div class="form-group form_spacing ">
                    <table cellpadding="6">
                        <thead>
                            <tr>
                                <th style="width:90px;">
                                    <label>Tip 1 (%)</label>
                                </th>
                                <th style="width:90px;">
                                    <label>Tip 2 (%)</label>

                                </th>
                                <th style="width:90px;">
                                    <label>Tip 3 (%)</label>
                                </th>
                                <th style="width:90px;">
                                    <label>Tip 4 (%)</label>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <?php foreach ($this->request->data['StoreTip'] as $key => $value) {
                                    ?>
                                    <td>
                                        <?php
                                        $checked = false;
                                        if($value['StoreTip']['is_checked'] == 1)
                                        {
                                            $checked = true;
                                        }
                                        echo $this->Form->input('StoreTip.' . $key . '.id', array('type' => 'hidden', 'value' => $value['StoreTip']['id']));
                                        echo $this->Form->input('StoreTip.' . $key . '.tip_value', array('type' => 'text', 'class' => 'form-control', 'style' => "width:80px;", 'value' => $value['StoreTip']['tip_value']));
                                        echo $this->Form->input('StoreTip.' . $key . '.is_checked', array('type' => 'checkbox', 'value' => 1, 'checked' => $checked));
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        </tbody>

                    </table>
                    <span class="blue">(Enter Tip in the % format. i.e 8%)</span>
                </div>
            </div>


            <hr/>
            
            
            
            
            <div class="form-group form_margin">
                <label>Themes</label>
                <?php
//$options=array('1'=>'Brown Theme');
                echo $this->Form->input('Store.store_theme_id', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $themeOptions, 'empty' => false));
                ?>
            </div>
            <?php
            if (!empty($this->request->data['Store']['store_theme_id']) && $this->request->data['Store']['store_theme_id'] > 10) {
                $hClass = "display:block";
            } else {
                $hClass = "display:none";
            }
            ?>
            <div class="form-group form_margin" style="<?php echo $hClass ?>">
                <label>Theme Color</label>
                <?php
                echo $this->Form->input('Store.theme_color_id', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $themeColors, 'empty' => false));
                ?>
            </div>

            <div class="form-group form_margin">
                <?php
                echo $this->Form->input('Store.navigation', array(
                    'type' => 'radio',
                    'div' => false,
                    'legend' => false,
                    'options' => array('1' => '&nbsp;&nbsp;Vertical Navigation&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => '&nbsp;&nbsp;Horizontal Navigation'),
                    'default' => 1
                ));
                ?>
                <br/><span class="blue">(Please select navigation for front end)</span>
            </div>



            <div class="form-group form_margin">
                <label>Fonts</label>
                <?php
                echo $this->Form->input('Store.store_font_id', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $fontOptions, 'empty' => false));
                ?>
            </div>


            <div class="form-group form_spacing">
                <div style="float:left;">
                    <label>Background Image</label>
                    <?php
                    echo $this->Form->input('Store.back_image', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
                    echo $this->Form->error('Store.background_image');
                    ?>
                    <span class="blue">Max Upload Size 2MB (1920*1080)</span>
                </div>

                <?php
                $EncryptStoreID = $this->Encryption->encode($this->request->data['Store']['id']);
                ?>
                <div style="float:right;">
                    <?php
                    if ($this->request->data['Store']['background_image']) {
                        echo $this->Html->image('/storeBackground-Image/' . $this->request->data['Store']['background_image'], array('alt' => 'Item Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                        echo $this->Html->link("X", array('controller' => 'Stores', 'action' => 'deleteStoreBackgroundPhoto', $EncryptStoreID), array('confirm' => 'Are you sure to delete Background Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                    }
                    ?>


                </div>

            </div>
            <div style="clear:both;"><br/></div>

            <div class="form-group form_spacing">
                <div style="float:left;">
                    <label>Store Logo</label>
                    <?php
                    echo $this->Form->input('Store.store_logophoto', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
                    echo $this->Form->error('Store.store_logophoto');
                    ?>
		   <span class="blue">Max Upload Size 2MB</span>
                </div>

                <?php
                $EncryptStoreID = $this->Encryption->encode($this->request->data['Store']['id']);
                ?>
                <div style="float:right;">
                    <?php
                    if ($this->request->data['Store']['store_logo']) {
                        echo $this->Html->image('/storeLogo/' . $this->request->data['Store']['store_logo'], array('alt' => 'Item Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Store Logo'));
                        echo $this->Html->link("X", array('controller' => 'Stores', 'action' => 'deleteStoreLogo', $EncryptStoreID), array('confirm' => 'Are you sure to delete Store Logo?', 'title' => 'Delete Logo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                    }
                    ?>


                </div>
            </div>

            <div style="clear:both;"><br/></div>
            <div class="form-group form_spacing">
                <label>Logo Type<span class="required"> * </span></label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                echo $this->Form->input('Store.logotype', array(
                    'type' => 'radio',
                    'options' => array('1' => 'Square&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => 'Rectangle'),
                    'default' => 1
                ));
                echo $this->Form->error('Store.logotype');
                ?>
            </div>
            <!--store info background image start-->
            <div class="form-group form_spacing">
                <div style="float:left;">
                    <label>Store Info Background Image</label>
                    <?php
                    echo $this->Form->input('Store.store_info_bgimage', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
                    echo $this->Form->error('Store.store_info_bg_image');
                    ?>
                    <span class="blue">Max Upload Size 2MB (1920*380)</span>
                </div>

                <?php
                $EncryptStoreID = $this->Encryption->encode($this->request->data['Store']['id']);
                ?>
                <div style="float:right;">
                    <?php
                    if ($this->request->data['Store']['store_info_bg_image']) {
                        echo $this->Html->image('/storeBackground-Image/' . $this->request->data['Store']['store_info_bg_image'], array('alt' => 'Item Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Store Logo'));
                        echo $this->Html->link("X", array('controller' => 'Stores', 'action' => 'deleteStoreInfoBgImage', $EncryptStoreID), array('confirm' => 'Are you sure to delete Store Info Background Image?', 'title' => 'Delete Store Info Background Image', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                    }
                    ?>


                </div>
            </div>
            <!--store info background image end-->
            <div style="clear:both;"><br/></div>
            <div class="form-group">
                <label>Store Info Description</label> 
                <?php
                echo $this->Form->input('Store.store_info_description', array('type' => 'textarea', 'rows' => '6', 'cols' => '8', 'label' => '', 'class' => 'form-control ckeditor', 'Placeholder' => 'Enter Description'));
                ?>

            </div>
            <div style="clear:both;"><br/></div>

            <div class="form-group form_margin">
                <label>Display Email</label>
                <?php
                echo $this->Form->input('Store.display_email', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $fontOptions, 'empty' => false));
                ?>
            </div>
            <div class="form-group form_margin">
                <label>Display Fax</label>
                <?php
                echo $this->Form->input('Store.display_fax', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $fontOptions, 'empty' => false));
                ?>
            </div>


            <div style="clear:both;"><br/></div>
            <div class="form-group form_spacing">
                <label>Display Store Logo</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['is_store_logo'] == 2) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.is_store_logo', array('checked' => $checked));
                ?>
            </div>


            <div class="form-group form_spacing">
                <label>Hide Store Photos</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['is_not_photo'] == 1) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.is_not_photo', array('checked' => $checked));
                ?>
            </div>

            <div class="form-group form_spacing">
                <label>Cash On Delivery</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['cash_on_delivery'] == 1) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.cash_on_delivery', array('checked' => $checked));
                ?>
            </div>


            <div class="form-group form_spacing">
                <label>Display store hours</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['store_hours'] == 1) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.store_hours', array('checked' => $checked));
                ?>
            </div>


            <div class="form-group form_spacing">
                <label>Allow guest user</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['guest_user'] == 1) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.guest_user', array('checked' => $checked));
                ?>
            </div>


            <div class="form-group form_spacing">
                <label>COD (Guest user)</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['guest_user_cod'] == 1) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.guest_user_cod', array('checked' => $checked));
                ?>
            </div>

            <div class="form-group form_spacing">
                <label>Pre-order allowed</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['pre_order_allowed'] == 1) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.pre_order_allowed', array('checked' => $checked));
                ?>
            </div>

            <div class="form-group form_spacing hideVersion2">
                <label>Popup login allowed</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['allow_pop_up'] == 1) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.allow_pop_up', array('checked' => $checked));
                ?>
            </div>

            <div class="form-group form_spacing hideVersion2">
                <label>Front Page Forms</label>
                <?php
                $checked = "";
                if ($this->request->data['Store']['order_type_forms'] == 1) {
                    $checked = "checked";
                }
                echo $this->Form->checkbox('Store.order_type_forms', array('checked' => $checked));
                ?>
            </div>
            <div class="form-group form_spacing">
                <label>Delivery Zone Type<span class="required"> * </span></label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                echo $this->Form->input('Store.delivery_zone_type', array(
                    'type' => 'radio',
                    'options' => array('1' => 'Draw&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => 'Circle'),
                    'default' => 1
                ));
                echo $this->Form->error('Store.delivery_zone_type');
                ?>
            </div>
            <div class="form-group form_spacing hideVersion1">
                <label>Deal Page<span class="required"> * </span></label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                echo $this->Form->input('Store.deal_page', array(
                    'type' => 'radio',
                    'options' => array('1' => 'Front&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => 'Separate'),
                    'default' => 1
                ));
                echo $this->Form->error('Store.deal_page');
                ?>
            </div>
            <div class="form-group form_spacing">
                <label>Review Page<span class="required"> * </span></label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                echo $this->Form->input('Store.review_page', array(
                    'type' => 'radio',
                    'options' => array('1' => 'Enabled&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Disabled'),
                    'default' => 1
                ));
                echo $this->Form->error('Store.review_page');
                ?>
            </div>
            <div class="form-group form_spacing">
                <?php
                echo $this->Form->input('StoreSetting.id', array('type' => 'hidden'));
                if (!empty($this->request->data['StoreSetting']['store_closed'])) {
                    $checkSC = true;
                } else {
                    $checkSC = false;
                }
                echo $this->Form->input('StoreSetting.store_closed', array(
                    'type' => 'checkbox',
                    'label' => false,
                    'before' => '<label> Store Open/Close',
                    'after' => '</label> &nbsp;&nbsp;&nbsp;',
                    'div' => false,
                    'checked' => $checkSC
                ));
                ?>
            </div>
            <div class="form-group form_spacing">
                <?php
                if (!empty($this->request->data['StoreSetting']['save_to_order_btn'])) {
                    $checkSTOB = true;
                } else {
                    $checkSTOB = false;
                }
                echo $this->Form->input('StoreSetting.save_to_order_btn', array(
                    'type' => 'checkbox',
                    'label' => false,
                    'before' => '<label> Save order for future allowed',
                    'after' => '</label> &nbsp;&nbsp;&nbsp;',
                    'div' => false,
                    'checked' => $checkSTOB
                ));
                ?>
            </div>
            <div class="form-group form_spacing">
                <?php
                if (!empty($this->request->data['StoreSetting']['order_allow'])) {
                    $checkOA = true;
                } else {
                    $checkOA = false;
                }
                echo $this->Form->input('StoreSetting.order_allow', array(
                    'type' => 'checkbox',
                    'label' => false,
                    'before' => '<label> Online ordering allowed',
                    'after' => '</label> &nbsp;&nbsp;&nbsp;',
                    'div' => false,
                    'checked' => $checkOA
                ));
                ?>
            </div>
            <div class="form-group form_spacing">
                <?php
                if (!empty($this->request->data['StoreSetting']['display_contact_us_form'])) {
                    $checkDCUF = true;
                } else {
                    $checkDCUF = false;
                }
                echo $this->Form->input('StoreSetting.display_contact_us_form', array(
                    'type' => 'checkbox',
                    'label' => false,
                    'before' => '<label> Display contact us form',
                    'after' => '</label> &nbsp;&nbsp;&nbsp;',
                    'div' => false,
                    'checked' => $checkDCUF
                ));
                ?>
            </div>
            
            <div class="form-group form_spacing">
                <?php
                if (!empty($this->request->data['StoreSetting']['discount_on_extra_fee'])) {
                    $checkDCUF = true;
                } else {
                    $checkDCUF = false;
                }
                echo $this->Form->input('StoreSetting.discount_on_extra_fee', array(
                    'type' => 'checkbox',
                    'label' => false,
                    'before' => '<label> Apply discount on Extra fee',
                    'after' => '</label> &nbsp;&nbsp;&nbsp;',
                    'div' => false,
                    'checked' => $checkDCUF
                ));
                ?>
            </div>


            <div class="form-group form_spacing">
                <?php
                if (!empty($this->request->data['StoreSetting']['tax_on_item_price'])) {
                    $checkDCUF = true;
                } else {
                    $checkDCUF = false;
                }
                echo $this->Form->input('StoreSetting.tax_on_item_price', array(
                    'type' => 'checkbox',
                    'label' => false,
                    'before' => '<label> Tax on Items Original Prices(Coupons)',
                    'after' => '</label> &nbsp;&nbsp;&nbsp;',
                    'div' => false,
                    'checked' => $checkDCUF
                ));
                ?>
            </div>





            <div class="form-group form_margin">
                <?php
                echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
                echo "&nbsp;";
                echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/stores/dashboard'", 'class' => 'btn btn-default'));
                ?>
            </div>
            <?php
            echo $this->Form->end();
            ?>

        </div>
        <script>

            $(document).ready(function () {
                $('#StoreDeliverycalendarLimit,#StorePickcalendarLimit,#StoreCalendarLimit,.addRule,#StoreMinimumOrderPrice,#StoreMinimumTakeawayPrice,#StoreDeliveryFee,#StoreServiceFee,#StoreDeliveryblackoutLimit,#StorePickblackoutLimit,#StoreDineinblackoutLimit').keyup(function () {
                    this.value = this.value.replace(/[^0-9.,]/g, '');
                });
                $.validator.addMethod('IP4Checker', function (value) {
                    if (value == '') {
                        return true;
                    }
                    var ip = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
                    return value.match(ip);
                }, 'Invalid IP address');

                $(".phone_number").keypress(function (e) {
                    //if the letter is not digit then display error and don't type anything
                    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                        return false;
                    }
                });
                $.validator.addMethod('checkVal', function (value) {
                    if (value == '') {
                        return true;
                    }
                    var ch = /^ *([0-9]+( *, *[0-9]+)*)? *$/;
                    return value.match(ch);
                }, 'Invalid input value.');


                $("#StoreConfiguration").validate({
                    debug: false,
                    errorClass: "error",
                    errorElement: 'span',
                    onkeyup: false,
                    rules: {
                        "data[Store][phone]": {
                            required: true,
                            minlength: 14,
                            maxlength: 14,
                        },
                        "data[Store][time_zone_id]": {
                            required: true,
                        },
                        "data[Store][zipcode]": {
                            alphanumeric: true
                        },
                        "data[Store][paypal_business_email]": {
                            email: true
                        },
                        "data[Store][printer_location]": {
                            //required:false,
                            IP4Checker: true
                        },
                        "data[Store][minimum_order_price]": {
                            number: true
                        },
                        "data[Store][minimum_takeaway_price]": {
                            number: true
                        },
                        "data[Store][delivery_fee]": {
                            number: true
                        },
                        "data[Store][service_fee]": {
                            number: true
                        },
                        "data[Store][display_email]": {
                            email: true
                        },
                        "data[Store][cutoff_time]": {
                            checkVal: true
                        },
                        "data[Store][delivery_delay]": {
                            checkVal: true
                        },
                        "data[Store][pick_up_delay]": {
                            checkVal: true
                        }

                    },
                    messages: {
                        "data[Store][phone]": {
                            required: "Contact number required",
                            minlength: "Number must be at 10 characters"
                        },
                        "data[Store][time_zone_id]": {
                            required: "Please select timezone",
                        }
                    }, highlight: function (element, errorClass) {
                        $(element).removeClass(errorClass);
                    },
                });
                $("[data-mask='mobileNo']").mask("(999) 999-9999");
                $('.addRule').each(function () {
                    $(this).rules("add", {
                        number: true,
                    });
                });
                var storeThemeID = $('#StoresStoreTheme').val();
                checkTheme(storeThemeID);
                $("#StoreStoreThemeId").on('change', function () {
                    var storeThemeID = $(this).val();
                    if (storeThemeID) {
                        checkTheme(storeThemeID);
                    }
                });
                CKEDITOR.config.toolbar = 'Custom';
                
                
                $("#StoreTip").on('click', function(){
                    if($(this).is(":checked"))
                    {
                        $("#tip_section").removeClass('hidden');
                    } else {
                        $("#tip_section").addClass('hidden');
                    }
                });
            });

            function checkTheme(storeThemeID) {
                if (storeThemeID && (storeThemeID < 11)) {
                    $("#StoreThemeColorId").parent('.form-group').css('display', 'none');
                    $(".hideVersion1").css('display', 'none');
                    $(".hideVersion2").css('display', 'block');
                } else {
                    $("#StoreThemeColorId").parent('.form-group').css('display', 'block');
                    $(".hideVersion2").css('display', 'none');
                    $(".hideVersion1").css('display', 'block');
                }
            }
        </script>


