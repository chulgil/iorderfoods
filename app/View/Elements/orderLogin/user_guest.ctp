<div class="order-form-layout" >
    <?php echo $this->Form->create('orderUserGuest', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'name' => 'chkOrderUserGuest', 'id' => 'chkOrderUserGuest')); ?>
    <div class="login-form clearfix">                          

        <h2>Proceed as Guest</h2>
        <ul class="clearfix guest-list">
            <li class="col-xs-6">
                <span class="title"><label>Name <em>*</em></label></span>
                <div class="title-box"> 
                    <?php
                    echo $this->Form->input('orderUserGuest.name', array('type' => 'name', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                    echo $this->Form->error('orderUserGuest.name');
                    ?> 
                </div>
            </li>
            <li class="col-xs-6">
                <span class="title"><label>Email <em>*</em></label></span>
                <div class="title-box"> 
                    <?php
                    echo $this->Form->input('orderUserGuest.email', array('type' => 'email', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Email'));
                    echo $this->Form->error('orderUserGuest.email');
                    ?> 
                </div>
            </li>

            <li class="col-xs-12">
                <span class="title"><label>Phone Number <em>*</em></label></span>
                <div class="title-box"> 
                    <?php
                    $countryCode = $this->Common->getAllCountryCode();
                    ;
                    echo $this->Form->input('orderUserGuest.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false));
                    echo $this->Form->input('orderUserGuest.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Enter Your Phone Number', 'label' => false, 'div' => false));
                    echo $this->Form->error('PickUpAddress.phone');
                    ?>
                    <span style='margin:9px 0px 0px 10px;font-size:12px;display:inline-block;float:left;width:auto'>(eg. 111-111-1111)</span>
                </div>
            </li>
        </ul>

    </div>

    <div class="button-frame clearfix">
        <button type="button" id='btnUserGuest' class="btn btn-primary theme-bg-1"> <span>Submit</span> </button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>



<script>
    $(".phone-number").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {	       
                  return false;
        }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999");
    
    $("#chkOrderUserGuest").validate({
        rules: {
            "data[orderUserGuest][name]": {
                required: true,
                lettersonly: true,
            },
            "data[orderUserGuest][email]": { 
                required: true,
                email:true,
                minlength:10,
                maxlength:50,
            },            
            "data[orderUserGuest][phone]": { 
                required: true,
            }
        },
        messages: {            
            "data[orderUserGuest][name]": {
                required: "Please enter name",
                lettersonly:"Only alphabates allowed",
            },
             "data[orderUserGuest][email]": {
                required: "Please enter email",
                email:"Please enter valid email"
            },            
            "data[orderUserGuest][phone]": {
                required: "Please enter phone number",                
            },
        }
    });
    
    $('#btnUserGuest').click(function(){
        if($("#chkOrderUserGuest").valid()){
            var name = $('#orderUserGuestName').val();
            var email = $('#orderUserGuestEmail').val();
            var country_code_id = $('#orderUserGuestCountryCodeId').val();
            var phone = $('#orderUserGuestPhone').val(); 
            
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'ajaxMenus', 'action' => 'user_guest')); ?>",
                type: "Post",
                dataType: 'html',
                data: {name: name, email: email,country_code_id:country_code_id, phone: phone},
                success: function (successResult) {
                    response = jQuery.parseJSON(successResult);
                    if(response.status == 1) {
                       changeTabPan('chkOrderType','chkLogin');
                       setDefaultStoreTime();
                    }
                }
            });
        }
    });

</script>