<br/>
<span id="flashError"></span>
<div class="input-fields">
    <?php
    echo $this->Form->input('DeliveryAddress.name', array('type' => 'text', "placeholder" => "Enter Your Name", 'label' => false, 'maxlength' => '20', "class" => "sign-input", 'div' => false, 'value' => $this->Session->read('GuestUser.name')));
    echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => @$countryCode, 'class' => 'sign-input SlectBox country-code', 'label' => false, 'div' => false, 'default' => $this->Session->read('GuestUser.country_code_id')));
    echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'sign-input phone', 'placeholder' => 'Mobile Phone', 'label' => false, 'div' => false, 'required' => true, 'value' => $this->Session->read('GuestUser.userPhone')));
    echo $this->Form->input('DeliveryAddress.email', array('type' => 'email', "placeholder" => "Enter Your Email", 'label' => false, 'maxlength' => '50', "class" => "sign-input", 'div' => false, 'disabled' => true, 'value' => $this->Session->read('GuestUser.email')));
    echo $this->Form->input('DeliveryAddress.address', array('type' => 'test', "placeholder" => "Enter Your address", 'autofocus' => true, 'label' => false, "class" => "sign-input", 'div' => false, 'value' => $this->Session->read('ordersummary.address')));
    echo $this->Form->input('DeliveryAddress.city', array('type' => 'test', "placeholder" => "Enter Your city", 'label' => false, "class" => "sign-input", 'div' => false, 'value' => $this->Session->read('ordersummary.city')));
    echo $this->Form->input('DeliveryAddress.state', array('type' => 'test', "placeholder" => "Enter Your state", 'label' => false, "class" => "sign-input", 'div' => false, 'value' => $this->Session->read('ordersummary.state')));
    echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'test', "placeholder" => "Enter Your Zip-Code ", 'maxlength' => '5', 'label' => false, "class" => "sign-input", 'div' => false, 'value' => $this->Session->read('ordersummary.zipcode')));
    ?>
</div>