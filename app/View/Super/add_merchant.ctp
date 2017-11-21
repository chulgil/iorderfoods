
<div class="row">
    <div class="col-lg-6">
        <h3>Add Merchant</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('super', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addmerchant')); ?>
    <div class="col-lg-6">
        <div class="form-group form_margin">
            <label>Merchant Name<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Merchant.name', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'placeholder' => 'Enter Name', 'div' => false)); ?>
        </div>

        <div class="form-group form_margin">
            <label>Merchant Domain<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Merchant.domain_name', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'placeholder' => 'Enter Domain', 'div' => false)); ?>
        </div>

        <div class="form-group form_margin">
            <label>Email<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Merchant.email', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Email', 'label' => '', 'div' => false)); ?>
            <span class="blue">(This email address is used for notification purpose)</span> 
        </div>


        <div class="form-group form_margin">
            <label>Contact no.<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Merchant.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone_number', 'placeholder' => 'Enter Contact Number', 'label' => '', 'div' => false)); ?>
            <span class="blue">(eg. 111-111-1111)</span> 
        </div> 

        <div class="form-group form_margin">
            <label>Company Name<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Merchant.company_name', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'div' => false)); ?>
        </div>


        <div class="form-group form_margin">
            <label>Address<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Merchant.address', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Address', 'label' => '', 'div' => false)); ?>
        </div>

        <div class="form-group form_margin">
            <label>City<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Merchant.city', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter City', 'label' => '', 'div' => false)); ?>
        </div>

        <div class="form-group form_margin">
            <label>State<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Merchant.state', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter State', 'label' => '', 'div' => false)); ?>
        </div>

        <div class="form-group form_margin">
            <label>Zipcode<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Merchant.zipcode', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Zipcode', 'label' => '', 'div' => false)); ?>
        </div> 


        <br/>

        <div class="form-group form_margin">
            <label>Salutation<span class="required"> * </span></label>
            <?php echo $this->Form->input('User.salutation', array('type' => 'select', 'options' => array('Mr.' => 'Mr.', 'Ms.' => 'Ms.', 'Mrs.' => 'Mrs.'), 'class' => 'form-control valid', 'label' => '', 'div' => false)); ?>

        </div>
        <div class="form-group form_margin">
            <label>First Name<span class="required"> * </span></label> 
            <?php echo $this->Form->input('User.fname', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Your First Name', 'label' => '', 'div' => false));
            echo $this->Form->error('User.fname');
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Last Name</label>
            <?php echo $this->Form->input('User.lname', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Your Last Name', 'label' => '', 'div' => false));
            echo $this->Form->error('User.lname');
            ?>
        </div>

        <div class="form-group form_margin">
            <label>Email<span class="required"> * </span></label>                

            <?php
            echo $this->Form->input('User.email', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Your Email', 'label' => '', 'div' => false, 'required' => true, 'autocomplete' => 'off'));
            echo $this->Form->error('User.email');
            ?>
            <span class="blue">(This email address is used for login)</span> 
        </div>

        <div class="form-group form_margin">
            <label>Password<span class="required"> * </span></label>
            <?php
            echo $this->Form->input('User.password', array('type' => 'password', 'class' => 'form-control valid', 'placeholder' => 'Enter Your password', 'label' => '', 'div' => false, 'required' => true));
            echo $this->Form->error('User.password');
            ?>
        </div>

        <div class="form-group form_margin">
            <label>Confirm Password<span class="required"> * </span></label>
            <?php
            echo $this->Form->input('User.password_match', array('type' => 'password', 'class' => 'form-control valid', 'placeholder' => 'Enter Confirm Password', 'label' => '', 'div' => false, 'required' => true));
            echo $this->Form->error('User.password_match');
            ?>
        </div>

        <div class="form-group form_margin">
            <label>Mobile Phone<span class="required"> * </span></label>
            <?php
            echo $this->Form->input('User.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control valid phone_number', 'placeholder' => 'Enter Mobile Phone', 'label' => '', 'div' => false, 'required' => true));
            echo $this->Form->error('User.phone');
            ?>
            <span class="blue">(eg. 111-111-1111)</span> 
        </div>    

        <?php
        echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
        echo "&nbsp;";
        echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/super/viewMerchantDetails'", 'class' => 'btn btn-default'));
        ?>
    </div>
<?php echo $this->Form->end(); ?>
</div><!-- /.row -->

<script>
    $(document).ready(function () {
        $(".phone_number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("[data-mask='mobileNo']").mask("(999) 999-9999");

        jQuery.validator.addMethod("passw", function (pass, element) {
            pass = pass.replace(/\s+/g, "");
            return this.optional(element) || pass.length > 7 &&
                    pass.match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[A-Za-z\d$@$!%*#?& ]{8,}$/);
            //pass.match(/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?& ])[A-Za-z\d$@$!%*#?& ]{8,}$/);
        }, "Atleast one digit, one upper and one lower case letter");
        $("#addmerchant").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Merchant][name]": {
                    required: true,
                },
                "data[Merchant][domain_name]": {
                    required: true,
                    remote: "/super/checkAllDomainsMerchant"
                },
                "data[Merchant][email]": {
                    required: true,
                    email: true,
                    remote: "/super/checkMerchantNotificationEmail"
                },
                "data[Merchant][phone]": {
                    required: true,
                },
                "data[Merchant][address]": {
                    required: true,
                },
                "data[Merchant][city]": {
                    required: true,
                },
                "data[Merchant][state]": {
                    required: true,
                },
                "data[Merchant][zipcode]": {
                    required: true,
                },
                "data[Merchant][company_name]": {
                    required: true,
                },
                "data[User][fname]": {
                    required: true,
                    lettersonly: true,
                },
                "data[User][lname]": {
                    required: false,
                    lettersonly: true,
                },
                "data[User][email]": {
                    required: true,
                    email: true,
                    remote: "/super/checkMerchantEmail"
                },
                "data[User][password]": {
                    required: true,
                    minlength: 8,
                    maxlength: 20,
                    passw: true,
                },
                "data[User][password_match]": {
                    required: true,
                    equalTo: "#UserPassword"
                },
                "data[User][phone]": {
                    required: true,
                },
            },
            messages: {
                "data[Merchant][name]": {
                    required: "Please enter Merchant name",
                },
                "data[Merchant][domain_name]": {
                    required: "Please enter Merchant Domain name",
                    remote: "Domain name already exists",
                },
                "data[Merchant][email]": {
                    required: "Please enter merchant email",
                    email: "Please enter valid email",
                    remote: "Email Already exist",
                },
                "data[Merchant][phone]": {
                    required: "Contact number required",
                },
                "data[Merchant][address]": {
                    required: "Please enter address",
                },
                "data[Merchant][city]": {
                    required: "Please enter city name",
                },
                "data[Merchant][state]": {
                    required: "Please enter state",
                },
                "data[Merchant][zipcode]": {
                    required: "Please enter zipcode",
                },
                "data[Merchant][company_name]": {
                    required: "Please company name",
                },
                "data[User][fname]": {
                    required: "Please enter your first name",
                    lettersonly: "Only alphabates Allowed",
                },
                "data[User][lname]": {
                    required: "Please enter your last name",
                    lettersonly: "Only alphabates Allowed",
                },
                "data[User][email]": {
                    required: "Please enter your email",
                    email: "Please enter valid email",
                    remote: "Email Already exist",
                },
                "data[User][password]": {
                    required: "Please enter your password",
                    minlength: "Password must be at least 8 characters",
                    maxlength: "Please enter no more than 20 characters",
                    passw: "Atleast one digit, one upper and lower case letter"
                },
                "data[User][password_match]": {
                    required: "Please enter your password again.",
                    equalTo: "Password not matched"
                },
                "data[User][phone]": {
                    required: "Contact number required",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
    });
</script>    