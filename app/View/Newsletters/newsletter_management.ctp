<?php echo $this->Html->script('jquery.maskedinput'); ?>
<style>
    .form-control{width: 30%; display: inline;}	
</style>
<div class="row">
    <div class="col-lg-6">
        <h3>Newsletter Management</h3>
        <div class="form-group form_spacing">
            <?php echo $this->Session->flash(); ?>   
        </div>
    </div> 
    <div class="col-lg-6">                        
    </div>
</div>   
<hr>
<div class="row">              
    <?php
    echo $this->Form->create('Newsletters', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'NewsletterManagement'));
    ?>
    <div class="col-lg-6">
        <div class="form-group">            
        </div>
        <div class="form-group">            
            <label>Frequency</label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
            <?php
            echo $this->Form->input('NewsletterManagement.send_type', array(
                'type' => 'radio',
                'options' => array('1' => ' Monthly&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                    '2' => ' Weekly&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                    '3' => ' Daily'),
                'default' => 1
            ));
            ?>           
        </div>
        <div class="form-group">
            <span id="sendDate" class="sendMail">
                <label>Date</label>
                &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                $dateOptions = array();
                for ($i = 1; $i <= 31; $i++) {
                    $dateOptions[$i] = $i;
                }
                echo $this->Form->input('NewsletterManagement.send_date', array('options' => $dateOptions, 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
                ?>
            </span>
        </div>
        <div class="form-group">
            <span id="sendDay" class="sendMail">
                <label>Day</label>
                &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                $dayOptions = array('1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday',
                    '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday');
                echo $this->Form->input('NewsletterManagement.send_day', array('options' => $dayOptions, 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
                ?>
            </span>
        </div>
        <div class="form-group">            
            <span id="sendTime" class="sendMail">
                <label>Time</label>
                &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                //$timeOptions = array_merge(array("00:00:00" => "00:00"), $timeOptions);
                echo $this->Form->input('NewsletterManagement.timezone_send_time', array('options' => $timeOptions, 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
                ?>
            </span>
        </div>
        <div class="form-group">
            <?php echo $this->Form->input('NewsletterManagement.store_id', array('type' => 'hidden')); ?>
            <?php echo $this->Form->input('NewsletterManagement.newsletter_id', array('type' => 'hidden')); ?>	    
        </div>     
        <hr/>
        <div class="form-group">
            <?php
            echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
            echo "&nbsp;";
            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/newsletters/index'", 'class' => 'btn btn-default'));
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
