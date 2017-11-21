<div class="row">
    <div class="col-lg-6">
        <h3>Edit Store Details</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('hq', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addstore')); ?>
    <div class="col-lg-6">
            <?php
            echo $this->Form->input('User.id', array('type' => 'hidden'));
            echo $this->Form->input('Store.merchant_id', array('type' => 'hidden'));
            echo $this->Form->input('Store.id', array('type' => 'hidden'));
            echo $this->Form->input('Store.user_id', array('type' => 'hidden'));
            ?>
            
        <div class="form-group form_margin">
            <label>Store Name<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Store.store_name', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'div' => false)); ?>
        </div>

        <div class="form-group form_margin">
            <label>Store Domain<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Store.store_url', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'div' => false,'readOnly'=>true)); ?>
        </div>

        <div class="form-group form_margin">
            <label>Email<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Store.email_id', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'div' => false, 'readOnly' => true)); ?>
            <span class="blue">(This email address is used for notification purpose)</span> 
        </div>


        <div class="form-group form_margin">
            <label>Phone no<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Store.phone', array('data-mask'=>'mobileNo','type' => 'text', 'class' => 'form-control phone_number', 'label' => '', 'div' => false)); ?>
            <span class="blue">(eg. 111-111-1111)</span> 
        </div>

        <div class="form-group form_margin">
            <label>Address<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Store.address', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'div' => false)); ?>
        </div>

        <div class="form-group form_margin">
            <label>City<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Store.city', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'div' => false)); ?>
        </div>

        <div class="form-group form_margin">
            <label>State<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Store.state', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'div' => false)); ?>
        </div>

        <div class="form-group form_margin">
            <label>Zipcode<span class="required"> * </span></label> 
            <?php echo $this->Form->input('Store.zipcode', array('type' => 'text', 'class' => 'form-control', 'label' => '', 'div' => false, 'maxlength' => '5')); ?>
        </div> 
        <div class="form-group form_margin">
            <label class="radioLabel">Status<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('Store.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Enable', '0' => 'Disable'),
                'default' => 1,
                'label'=>false,
                'legend'=>false,
                'div'=>false
            ));
            ?>
        </div>

        <?php
        echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
        echo "&nbsp;";
        echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hq/viewStoreDetails'", 'class' => 'btn btn-default'));
        ?>
    </div>
<?php echo $this->Form->end(); ?>
</div>

<script>
    $(document).ready(function () {
        $(".phone_number").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {	       
                  return false;
        }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999");
        $("#addstore").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Store][store_name]": {
                    required: true,
                },
                "data[Store][phone]": {
                    required: true,
                },
                "data[Store][address]": {
                    required: true,
                },
                "data[Store][city]": {
                    required: true,
                },
                "data[Store][state]": {
                    required: true,
                },
                "data[Store][zipcode]": {
                    required: true,
                    alphanumeric:true
                }
            },
            messages: {
                "data[Store][store_name]": {
                    required: "Please enter store name",
                },
                "data[Store][phone]": {
                    required: "Contact number required",
                },
                "data[Store][address]": {
                    required: "Please enter Email",
                    email: "Please enter valid email",
                },
                "data[Store][city]": {
                    required: "Please enter store name",
                },
                "data[Store][state]": {
                    required: "Please enter Email",
                    email: "Please enter valid email id ",
                },
                "data[Store][zipcode]": {
                    required: "Please enter Email",
                    email: "Please enter valid email id ",
                }
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
    });
</script>    
<style>
input[type="radio"] {
    line-height: normal;
    margin: 4px 10px;
}
.radioLabel{
    margin-right: 45px;
}
</style>