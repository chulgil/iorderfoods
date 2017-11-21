<div class = "title-bar"> My Profile </div>
<div class="main-container">
    <div class="inner-wrap no-border"><!-- profile -->
        <?php //echo $this->Session->flash(); ?>
        <div class="static-title">
            <h3>PERSONAL INFORMATION</h3>
        </div>
        <div class="form-section profile-fsection">
            <?php
            echo $this->Form->create('UsersProfile', array('url' => array('controller' => 'users', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'UsersProfile', 'class' => 'form-horizontal'));
            echo $this->Form->input('User.role_id', array('type' => 'hidden', 'value' => $roleId));
            echo $this->Form->input('User.is_news_check', array('id' => 'newsNote', 'type' => 'hidden', 'value' => 0));
            echo $this->Form->input('User.is_email_check', array('id' => 'emailNote', 'type' => 'hidden', 'value' => 0));
            echo $this->Form->input('User.is_sms_check', array('id' => 'smsNote', 'type' => 'hidden', 'value' => 0));
            ?>
            <div class="form-top clearfix">
                <h3>Personal Information</h3>
                <div class="form-group profile-input clearfix">
                    <div class="col-sm-10 col-sm-offset-1">
                        <?php
                        echo $this->Form->input('User.fname', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Your First Name', 'maxlength' => '20', 'label' => false, 'div' => false));
                        ?>
                    </div>
                </div>
                <div class="form-group profile-input clearfix">
                    <div class="col-sm-10 col-sm-offset-1">
                        <?php
                        echo $this->Form->input('User.lname', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Your Last Name', 'maxlength' => '20', 'label' => false, 'div' => false));
                        ?>
                    </div>
                </div>
                <div class="form-group profile-input clearfix">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="row">
                            <div class="col-sm-2 col-xs-3">
                                <?php
                                echo $this->Form->input('User.country_code_id', array('type' => 'select', 'options' => $countryCode, 'value' => $this->request->data['CountryCode']['id'], 'class' => 'form-control country-code', 'label' => false, 'div' => false));
                                ?>
                            </div>
                            <div class="col-sm-10 col-xs-9">
                                <?php
                                echo $this->Form->input('User.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone-number', 'placeholder' => 'Mobile Phone', 'label' => false, 'div' => false, 'required' => true));
                                ?>
                                <span>(eg. 111-111-1111)</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group profile-input clearfix">
                    <div class="col-sm-10 col-sm-offset-1">
                        <?php
                        echo $this->Form->input('User.dateOfBirth', array('type' => 'text', 'class' => 'form-control date_select', 'placeholder' => 'Date of Birth', 'maxlength' => '12', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
                        ?>
                    </div>
                </div>
                <div class="form-group profile-input clearfix">
                    <div class="col-sm-10 col-sm-offset-1">
                        <?php
                        echo $this->Form->input('User.city_id', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select City'));
                        ?>  
                    </div>
                </div>
                <div class="form-group profile-input clearfix">
                    <div class="col-sm-10 col-sm-offset-1">
                        <?php
                        echo $this->Form->input('User.state_id', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select State'));
                        ?>  
                    </div>
                </div>
                <div class="form-group profile-input clearfix">
                    <div class="col-sm-10 col-sm-offset-1">
                        <?php
                        echo $this->Form->input('User.zip_id', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Zip', 'maxlength' => '5'));
                        ?>  
                    </div>
                </div>
                <div class="profile-check-list clearfix">
                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-1">
                            <ul class="profile-notifications">
                                <?php if ($this->request->data['User']['is_newsletter'] == 1) { ?>
                                    <li>
                                        <span>
                                            <input type="checkbox" id="news"  name="data[User][is_newsletter]"  checked/>
                                            <label for="news">Opt for newsletter notification</label>
                                        </span>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <span>
                                            <input type="checkbox" id="news"  name="data[User][is_newsletter]"  />
                                            <label for="news">Opt for newsletter notification</label>
                                        </span>
                                    </li>
                                <?php } ?>
                                <?php if ($this->request->data['User']['is_emailnotification'] == 1) { ?>
                                    <li>
                                        <span>
                                            <input type="checkbox" id="email_note"  name="data[User][is_emailnotification]"  checked/> 
                                            <label for="email_note">Opt for email notification</label>
                                        </span>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <span>
                                            <input type="checkbox" id="email_note"  name="data[User][is_emailnotification]"  /> <label for="email_note">Opt for email notification</label>
                                        </span>
                                    </li>
                                <?php } ?>
                                <?php if ($this->request->data['User']['is_smsnotification'] == 1) { ?>
                                    <li>
                                        <span>
                                            <input type="checkbox" id="sms_note"  name="data[User][is_smsnotification]" checked />
                                            <label for="sms_note">Opt for SMS notification</label>
                                        </span>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <span><input type="checkbox" id="sms_note"  name="data[User][is_smsnotification]"  />
                                            <label for="sms_note">Opt for SMS notification</label>
                                        </span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div id="change_password_block">
                        <h3>Change Password</h3>
                        <div class="form-group profile-input clearfix">
                            <div class="col-sm-10 col-sm-offset-1">
                                <?php
                                echo $this->Form->input('User.oldpassword', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Old Password', 'maxlength' => '20', 'label' => false));
                                ?>
                            </div>
                        </div>
                        <div class="form-group profile-input clearfix">
                            <div class="col-sm-10 col-sm-offset-1">
                                <?php
                                echo $this->Form->input('User.password', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false, 'value' => ''));
                                ?>
                            </div>
                        </div>
                        <div class="form-group profile-input clearfix">
                            <div class="col-sm-10 col-sm-offset-1">
                                <?php
                                echo $this->Form->input('User.password_match', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="profile-btn-section clearfix">
                    <div class="row">
                        <?php if (DESIGN == 3) { ?>
                            <?php
                            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/myDeliveryAddress/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'p-cancle'));
                            ?>
                            <?php
                            echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'p-save theme-bg-1'));
                            ?>
                        <?php } else { ?>
                            <div class="col-sm-6 col-xs-6">
                                <?php
                                echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'p-save theme-bg-1'));
                                ?>
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <?php
                                echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/myDeliveryAddress/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'p-cancle theme-bg-2'));
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>