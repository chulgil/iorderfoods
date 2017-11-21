<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo __('Add Delivery Addresses'); ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <!-- -->
                    <?php echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Deliveryaddress', "class" => "sign-up")); ?>
                    <?php if ($label1 != 1) { ?>
                        <div class="delivery-address clearfix">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="home_address"  name="data[DeliveryAddress][label1]"  checked="checked"/> <label for="home_address">Home Address</label>
                            </div>
                        </div>
                        <div id='home_block'>
                            <!-- CONTENT -->
                            <div class="main-form margin-top35">
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/user.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Name <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress.name_on_bell', array('type' => 'text', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Address <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            City <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            State <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Zip Code <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter your Zip Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                                    </div>
                                </div>
                                <div class="form-group twin-block">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/mobile.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Mobile Phone <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control custom-text country-code', 'label' => false, 'div' => false)); ?>
                                        <div class="phone-input">
                                            <?php echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control custom-text phone-number', 'placeholder' => 'Mobile Phone ( 111-111-111)', 'label' => false, 'div' => false, 'required' => true)); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($label2 != 1) { ?>
                        <div class="delivery-address clearfix">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="work_address"  name="data[DeliveryAddress1][label2]"/> <label for="work_address">Work Address</label>
                            </div>
                        </div>
                        <div id='work_block'>
                            <!-- CONTENT -->
                            <div class="main-form margin-top35">
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/user.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Name <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress1.name_on_bell', array('type' => 'text', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Address <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress1.address', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            City <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress1.city', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            State <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress1.state', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Zip Code <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress1.zipcode', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter your Zip Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                                    </div>
                                </div>
                                <div class="form-group twin-block">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/mobile.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Mobile Phone <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress1.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control custom-text country-code', 'label' => false, 'div' => false)); ?>
                                        <div class="phone-input">
                                            <?php echo $this->Form->input('DeliveryAddress1.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control custom-text phone-number', 'placeholder' => 'Mobile Phone ( 111-111-111)', 'label' => false, 'div' => false, 'required' => true)); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($label3 != 1) { ?>
                        <div class="delivery-address clearfix">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="other_address"  name="data[DeliveryAddress2][label3]"/> <label for="other_address">Other Address</label>
                            </div>
                        </div>
                        <div id='other_block'>
                            <!-- CONTENT -->
                            <div class="main-form margin-top35">
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/user.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Name <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress2.name_on_bell', array('type' => 'text', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Address <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress2.address', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            City <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress2.city', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            State <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress2.state', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Zip Code <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress2.zipcode', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter your Zip Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                                    </div>
                                </div>
                                <div class="form-group twin-block">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/mobile.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Mobile Phone <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress2.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control custom-text country-code', 'label' => false, 'div' => false)); ?>
                                        <div class="phone-input">
                                            <?php echo $this->Form->input('DeliveryAddress2.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control custom-text phone-number', 'placeholder' => 'Mobile Phone ( 111-111-111)', 'label' => false, 'div' => false, 'required' => true)); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($label4 != 1) { ?>
                        <div class="delivery-address clearfix">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="address4"  name="data[DeliveryAddress3][label4]"/> <label for="address4">Address 4</label>
                            </div>
                        </div>
                        <div id='address4_block'>
                            <!-- CONTENT -->
                            <div class="main-form margin-top35">
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/user.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Name <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress3.name_on_bell', array('type' => 'text', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Address <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress3.address', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            City <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress3.city', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            State <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress3.state', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Zip Code <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress3.zipcode', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter your Zip Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                                    </div>
                                </div>
                                <div class="form-group twin-block">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/mobile.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Mobile Phone <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress3.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control custom-text country-code', 'label' => false, 'div' => false)); ?>
                                        <div class="phone-input">
                                            <?php echo $this->Form->input('DeliveryAddress3.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control custom-text phone-number', 'placeholder' => 'Mobile Phone ( 111-111-111)', 'label' => false, 'div' => false, 'required' => true)); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($label5 != 1) { ?>
                        <div class="delivery-address clearfix">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="address5"  name="data[DeliveryAddress4][label5]"/> <label for="address5">Address 5</label>
                            </div>
                        </div>
                        <div id='address5_block'>
                            <!-- CONTENT -->
                            <div class="main-form margin-top35">
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/user.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Name <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress4.name_on_bell', array('type' => 'text', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Address <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress4.address', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            City <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress4.city', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            State <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress4.state', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Zip Code <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress4.zipcode', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter your Zip Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                                    </div>
                                </div>
                                <div class="form-group twin-block">
                                    <div class="left-tile">
                                        <span class="label-icon">
                                            <?php echo $this->Html->image('hq/mobile.png', array('alt' => 'user')) ?>
                                        </span>
                                        <label>
                                            Mobile Phone <sup>*</sup>
                                        </label>
                                    </div>
                                    <div class="rgt-box">
                                        <?php echo $this->Form->input('DeliveryAddress4.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control custom-text country-code', 'label' => false, 'div' => false)); ?>
                                        <div class="phone-input">
                                            <?php echo $this->Form->input('DeliveryAddress4.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control custom-text phone-number', 'placeholder' => 'Mobile Phone ( 111-111-111)', 'label' => false, 'div' => false, 'required' => true)); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="submit-btn clearfix" id="sbtBtnChk">
                        <?php
                        echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'btn common-config black-bg'));
                        echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hqusers/myDeliveryAddress'", 'class' => 'btn common-config black-bg'));
                        ?>
                    </div>
                    <!-- CONTENT END -->
                    <?php echo $this->Form->end(); ?>
                    <!-- -->
                    <div class="ext-border">
                        <?php echo $this->Html->image('hq/thick-border.png', array('alt' => 'user')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
//        if ($("#Deliveryaddress input:checkbox:checked").length > 0)
//        {
//            $("#sbtBtnChk").removeClass('hidden');
//            // any one is checked
//        } else
//        {
//            $("#sbtBtnChk").addClass('hidden');
//            // none is checked
//        }
        $(".phone-number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        $("[data-mask='mobileNo']").mask("(999) 999-9999");

        $('#work_block').css('display', 'none');
        $("#work_block input").prop("disabled", true);
        $("#work_block select").prop("disabled", true);
        $('#other_block').css('display', 'none');
        $("#other_block input").prop("disabled", true);
        $("#other_block select").prop("disabled", true);

        $('#address4_block').css('display', 'none');
        $("#address4_block input").prop("disabled", true);
        $("#address4_block select").prop("disabled", true);

        $('#address5_block').css('display', 'none');
        $("#address5_block input").prop("disabled", true);
        $("#address5_block select").prop("disabled", true);
    });

    $('#home_address').on('change', function () {
        if ($(this).prop('checked')) {
            $('#home_block').css('display', 'block');
            $("#home_block input").prop("disabled", false);
            $("#home_block select").prop("disabled", false);
        } else {
            $('#home_block').css('display', 'none');
            $("#home_block input").prop("disabled", true);
            $("#home_block select").prop("disabled", true);
        }
    });
    $('#work_address').on('change', function () {
        if ($(this).prop('checked')) {
            $('#work_block').css('display', 'block');
            $("#work_block input").prop("disabled", false);
            $("#work_block select").prop("disabled", false);
        } else {
            $('#work_block').css('display', 'none');
            $("#work_block input").prop("disabled", true);
            $("#work_block select").prop("disabled", true);
        }
    });
    $('#other_address').on('change', function () {
        if ($(this).prop('checked')) {
            $('#other_block').css('display', 'block');
            $("#other_block input").prop("disabled", false);
            $("#other_block select").prop("disabled", false);
        } else {
            $('#other_block').css('display', 'none');
            $("#other_block input").prop("disabled", true);
            $("#other_block select").prop("disabled", true);
        }
    });

    $('#address4').on('change', function () {
        if ($(this).prop('checked')) {
            $('#address4_block').css('display', 'block');
            $("#address4_block input").prop("disabled", false);
            $("#address4_block select").prop("disabled", false);
        } else {
            $('#address4_block').css('display', 'none');
            $("#address4_block input").prop("disabled", true);
            $("#address4_block select").prop("disabled", true);
        }
    });

    $('#address5').on('change', function () {
        if ($(this).prop('checked')) {
            $('#address5_block').css('display', 'block');
            $("#address5_block input").prop("disabled", false);
            $("#address5_block select").prop("disabled", false);
        } else {
            $('#address5_block').css('display', 'none');
            $("#address5_block input").prop("disabled", true);
            $("#address5_block select").prop("disabled", true);
        }
    });

        jQuery.validator.addMethod("lettersonly", function(value, element) 
            {
            return this.optional(element) || /^[a-z," "]+$/i.test(value);
        }, "Letters and spaces only please"); 

    $("#Deliveryaddress").validate({
        debug: false,
        errorClass: "error",
        errorElement: 'span',
        onkeyup: false,
        rules: {
            "data[DeliveryAddress][name_on_bell]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress][address]": {
                required: true
            },
            "data[DeliveryAddress][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress][phone]": {
                required: true
            },
            "data[DeliveryAddress1][name_on_bell]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress1][address]": {
                required: true
            },
            "data[DeliveryAddress1][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress1][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress1][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress1][phone]": {
                required: true
            },
            "data[DeliveryAddress2][name_on_bell]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress2][address]": {
                required: true
            },
            "data[DeliveryAddress2][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress2][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress2][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress2][phone]": {
                required: true
            },
            "data[DeliveryAddress3][name_on_bell]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress3][address]": {
                required: true
            },
            "data[DeliveryAddress3][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress3][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress3][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress3][phone]": {
                required: true
            },
            "data[DeliveryAddress4][name_on_bell]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress4][address]": {
                required: true
            },
            "data[DeliveryAddress4][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress4][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress4][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress4][phone]": {
                required: true
            }
        },
        messages: {
            "data[DeliveryAddress][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress][address]": {
                required: "Please enter your are address"
            },
            "data[DeliveryAddress][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress][phone]": {
                required: "Contact number required"
            },
            "data[DeliveryAddress1][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress1][address]": {
                required: "Please enter your are address"
            },
            "data[DeliveryAddress1][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress1][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress1][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress1][phone]": {
                required: "Contact number required"
            },
            "data[DeliveryAddress2][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress2][address]": {
                required: "Please enter your are address"
            },
            "data[DeliveryAddress2][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress2][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress2][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress2][phone]": {
                required: "Contact number required"
            },
            "data[DeliveryAddress3][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress3][address]": {
                required: "Please enter your are address"
            },
            "data[DeliveryAddress3][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress3][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress3][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress3][phone]": {
                required: "Contact number required"
            },
            "data[DeliveryAddress4][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress4][address]": {
                required: "Please enter your are address"
            },
            "data[DeliveryAddress4][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress4][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress4][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress4][phone]": {
                required: "Contact number required"
            }
        }, highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        }
    });
</script>

