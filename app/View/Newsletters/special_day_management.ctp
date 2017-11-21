<?php echo $this->Html->script('jquery.maskedinput'); ?>
<style>
    .form-control{width: 30%; display: inline;}	
</style>
<div class="row">
    <div class="col-lg-6">
        <h3>Newsletter Management</h3>
        <div class="form-group form_spacing">
            <?php 
            echo $this->Session->flash(); ?>   
        </div>
    </div> 
    <div class="col-lg-6">                        
    </div>
</div>   
<hr>
<div class="row">              
    <?php
    echo $this->Form->create('SpecialDayManagement', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'SpecialDayManagement'));
    ?>
    <div class="col-lg-6">
        <div class="form-group">            
        </div>
        <div class="form-group">
                <label>Time Set</label>
                <?php
                if(isset($this->request->data)){
                    $selected=$this->request->data['SpecialDay']['special_day_time_id'];
                }else{
                    $selected=1;
                }
                echo $this->Form->input('SpecialDay.cron_time', array('options' => $specialDayTimes, 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off','default'=>$selected));
                ?>
        </div>
        <div class="form-group">
            <?php echo $this->Form->input('SpecialDay.store_id', array('type' => 'hidden')); ?>
            <?php echo $this->Form->input('SpecialDay.id', array('type' => 'hidden')); ?>	    
        </div>     
        <hr/>
        <div class="form-group">
            <?php
            echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
            echo "&nbsp;";
            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/newsletters/special_day'", 'class' => 'btn btn-default'));
            ?>
        </div>            
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<script>
    $("#sendDate").show();
    $("#sendDay").hide();
    $("#sendTime").show();
</script>
<script>
    $("input[name='data[NewsletterManagement][send_type]']").change(function () {
        if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 1) {
            $("#sendDate").show();
            $("#sendDay").hide();
            $("#sendTime").show();
        } else if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 2) {
            $("#sendDate").hide();
            $("#sendDay").show();
            $("#sendTime").show();
        } else {
            $("#sendDate").hide();
            $("#sendDay").hide();
            $("#sendTime").show();
        }
    });

    $(document).ready(function () {
        if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 1) {
            $("#sendDate").show();
            $("#sendDay").hide();
            $("#sendTime").show();
        } else if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 2) {
            $("#sendDate").hide();
            $("#sendDay").show();
            $("#sendTime").show();
        } else {
            $("#sendDate").hide();
            $("#sendDay").hide();
            $("#sendTime").show();
        }
    });
</script>
