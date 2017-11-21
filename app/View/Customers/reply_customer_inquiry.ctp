<div class="row">
    <div class="col-lg-6">
        <h3>Reply Customer</h3> <br>
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
<?php
echo $this->Form->create('StoreInquiries', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'UsersRegistration', 'enctype' => 'multipart/form-data'));
echo $this->Form->input('StoreInquiries.id', array('type' => 'hidden'));
?>
<div class="row clearfix">        
    <div class="col-lg-6 clearfix">            
        <div class="form-group">		 
            <b>Name    :</b> <?php echo $this->request->data['StoreInquiries']['name']; ?><br>
            <b>Email   :</b> <?php echo $this->request->data['StoreInquiries']['email']; ?><br>
            <b>Phone   :</b> <?php echo $this->request->data['StoreInquiries']['phone']; ?><br>
            <b>Message :</b> <?php echo $this->request->data['StoreInquiries']['message']; ?>
        </div>
        <div class="form-group clearfix">		 
            <label>Reply<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('StoreInquiries.admin_message', array('type' => 'textarea', 'class' => 'form-control valid', 'placeholder' => 'Message', 'label' => false, 'div' => false));
            ?>
        </div>
    </div>
    <div class='col-lg-12 clearfix'>
        <?php echo $this->Form->button('Reply', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/customers/customerInquiries", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>     

<script>
    $(document).ready(function () {
        $("#UsersRegistration").validate({
            rules: {
                "data[StoreInquiries][admin_message]": {
                    required: true,
                }
            },
            messages: {
                "data[StoreInquiries][admin_message]": {
                    required: "Please enter message.",
                }
            }
        });
    });
</script>