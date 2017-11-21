<!--
<div class="main-container">
    <div class=" inner-wrap store-info clearfix">
<?php if (isset($availabilityInfo) && !empty($availabilityInfo)) { ?>
                                                                                                                            <div class="open-hours">
                                                                                                                                <div class="common-title-hour">
                                                                                                                                    <h3>OPEN HOURS</h3>
                                                                                                                                </div>
                                                                                                                                <div class="hours-detail">
                                                                                                                                    <ul class="timing">
    <?php
    $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    foreach ($days as $key => $value) {
        ?>
                                                                                                                                                                                                                                                            <li>
                                                                                                                                                                                                                                                                <label class="day">
        <?php echo $value; ?>
                                                                                                                                                                                                                                                                </label>
                                                                                                                                                                                                                                                                <span class="time"><?php
        if ($availabilityInfo[$key]['StoreAvailability']['is_closed'] == 1) {
            echo "Closed";
        } else {
            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['start_time']) . " - ";
            if ($store_data['Store']['is_break_time'] == 1) {
                if ($store_data['Store']['is_break1'] == 1) {
                    if ($availabilityInfo[$key]['StoreBreak']['break1_start_time'] != $availabilityInfo[$key]['StoreBreak']['break1_end_time']) {
                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_start_time']) . ",   ";
                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_end_time']) . " - ";
                    }
                }
                if ($store_data['Store']['is_break2'] == 1) {
                    if ($availabilityInfo[$key]['StoreBreak']['break2_start_time'] != $availabilityInfo[$key]['StoreBreak']['break2_end_time']) {
                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_start_time']) . ",   ";
                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_end_time']) . " - ";
                    }
                }
            }
            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['end_time']);
        }
        ?>
                                                                                                                                                                                                                                                                </span>
                                                                                                                                                                                                                                                            </li>
    <?php } ?>
                                                                                                                                    </ul>
                                                                                                                                </div>
                                                                                                                            </div>
<?php } ?>
        <div class="map-info">
            <div class="map-info-inner">
                <div class="map-wrap" id="map" style="height: 600px;">
                    <img alt="map" src="/img/map-sample.jpg">
                </div>
            </div>
            <div class="store-address">
                <h3>STORE INFORMATION</h3>
                <address>
                    <strong> <img src="/img/store.png" alt="#"><?php echo $store_data['Store']['store_name']; ?></strong><br>
                    <img src="/img/location.png" alt="#">&nbsp;<?php echo $store_data['Store']['address'] . ' ,' . $store_data['Store']['city'] . ' ,' . $store_data['Store']['state'] . ' ,' . $store_data['Store']['zipcode']; ?><br>
                    <img src="/img/phone-ad.png" alt="#"><?php echo $store_data['Store']['phone']; ?><br>
                    <img src="/img/phone-ad.png" alt="#"><?php
if (!empty($store_data['Store']['display_fax'])) {
    echo "Fax: " . $store_data['Store']['display_fax'];
}
?><br>
                    <img src="/img/phone-ad.png" alt="#"><?php
if (!empty($store_data['Store']['display_email'])) {
    echo $store_data['Store']['display_email'];
}
?>
                </address>
            </div>
        </div>
    </div>
</div>-->

<main class="main-body">
    <div class="ext-menu inner-title-padding">
        <div class="ext-menu-title">
            <h4>STORE INFO</h4>
        </div>
        <div class="ext-menu-img">
            <div class="emi-tbl">
                <div class="emi-tbl-cell">
                    <h3><?php echo $store_data['Store']['store_name']; ?></h3>
                </div>
                <div class="emi-tbl-cell">
                    <?php echo $store_data['Store']['store_info_description']; ?>
                </div>
            </div>
            <div class="emi-overlay"></div>
            <?php
            if (!empty($store_data['Store']['store_info_bg_image'])) {
                $image = "/storeBackground-Image/" . $store_data['Store']['store_info_bg_image'];
            } else {
                $image = "/img/store-mid-banner.png";
            }
            ?>
            <img src="<?php echo $image; ?>">
        </div>
    </div>
    <div class="store-mid-map" id="map" style="height: 600px;">
        <img src="/img/store-mid-banner.png">
    </div>



        <div class="row scott-contact">
            <div class="scott-contact-info col-md-6 col-xs-12">
                <h3>CONTACT INFO</h3>
                <p><?php echo $store_data['Store']['address']; ?></p>
                <p><?php echo $store_data['Store']['city'] . ' ,' . $store_data['Store']['state'] . ' ,' . $store_data['Store']['zipcode']; ?></p>
                <p><?php echo $store_data['Store']['phone']; ?></p>
                <p><?php
                    if (!empty($store_data['Store']['display_fax'])) {
                        echo "Fax: " . $store_data['Store']['display_fax'];
                    }
                    ?></p>
                <p><?php
                    if (!empty($store_data['Store']['display_email'])) {
                        echo $store_data['Store']['display_email'];
                    }
                    ?></p>

                <h3>Opening <strong>Hours</strong></h3>
                <ul class="time-list">
                    <?php
                    $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                    foreach ($days as $key => $value) {
                        ?>

                        <li class="clearfix">

                                    <span class="lft-txt">
                                        <!--<i class="fa fa-calendar-o" aria-hidden="true"></i>-->
                                        <?php echo $value; ?>
                                    </span>
                            <span class="rgt-txt">
                                        <?php
                                        if ($availabilityInfo[$key]['StoreAvailability']['is_closed'] == 1) {
                                            echo "Closed";
                                        } else {
                                            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['start_time']) . " - ";
                                            if ($store_data['Store']['is_break_time'] == 1) {
                                                if ($store_data['Store']['is_break1'] == 1) {
                                                    if ($availabilityInfo[$key]['StoreBreak']['break1_start_time'] != $availabilityInfo[$key]['StoreBreak']['break1_end_time']) {
                                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_start_time']) . ",   ";
                                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_end_time']) . " - ";
                                                    }
                                                }
                                                if ($store_data['Store']['is_break2'] == 1) {
                                                    if ($availabilityInfo[$key]['StoreBreak']['break2_start_time'] != $availabilityInfo[$key]['StoreBreak']['break2_end_time']) {
                                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_start_time']) . ",   ";
                                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_end_time']) . " - ";
                                                    }
                                                }
                                            }
                                            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['end_time']);
                                        }
                                        ?>

                                    </span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="row">
                <?php if (!empty($displayContactUsForm['StoreSetting']['display_contact_us_form'])) { ?>
                    <div class="message-form clearfix">
                        <?php
                        echo $this->Form->create('StoreInquiries', array('url' => array('controller' => 'customers', 'action' => 'contact_us'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'contactUs', 'enctype' => 'multipart/form-data'));
                        ?>
                        <div class="row">
                            <div class="col-md-6 col-xs-12 no-gutters">
                                <?php echo $this->Form->input('StoreInquiries.name', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter your name', 'label' => false, 'div' => false)); ?>
                            </div>
                            <div class="col-md-6 col-xs-12 no-gutters">
                                <?php echo $this->Form->input('StoreInquiries.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'user-detail pad-left-30', 'placeholder' => 'Enter your phone', 'label' => false, 'div' => false)); ?>
                            </div>

                        </div>
                          <div class="row">
                              <div class="input-group col-md-12 col-xs-12">
                                  <?php echo $this->Form->input('StoreInquiries.email', array('type' => 'email', 'class' => 'user-detail', 'placeholder' => 'Enter your email', 'label' => false, 'div' => false)); ?>
                              </div>
                          </div>

                        <div class="row">
                            <div class="input-group user-detail-txtarea col-md-12 col-xs-12">
                                <?php echo $this->Form->input('StoreInquiries.message', array('type' => 'textarea', 'class' => 'user-detail', 'placeholder' => 'Enter your message', 'label' => false, 'div' => false, 'maxlength' => '1200')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="btm-msg-txt col-md-4 col-xs-12  no-gutters">
                                <?php echo $this->Form->button('Send Message', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                                <p class="txtbx-msg">(Max Word 1200)</p>
                            </div>
                        </div>

                        <?php echo $this->Form->end(); ?>
                    </div>
                <?php } ?>
                </div>
            </div>
    </div>
</main>
<script type="text/javascript">
    $(document).ready(function () {
        $(".phone").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("[data-mask='mobileNo']").mask("(999) 999-9999");
        $("#contactUs").validate({
            rules: {
                "data[StoreInquiries][name]": {
                    required: true,
                    lettersonly: true,
                },
                "data[StoreInquiries][email]": {
                    required: true,
                    email: true,
                },
                "data[StoreInquiries][message]": {
                    required: true,
                    maxlength: 1200
                }
            },
            messages: {
                "data[StoreInquiries][name]": {
                    required: "Please enter name.",
                },
                "data[StoreInquiries][email]": {
                    required: " Please enter email."
                },
                "data[StoreInquiries][message]": {
                    required: "Please enter message."
                }
            }
        });
    });
</script>