<style>
    .modal-dialog {    
        margin: 0 auto;        
        position:relative;          
    }
    .modal-body{
        color:#000000;
    } 
    .modal{
         top: 32% !important;  
    }
    .order-form-layout{
        float: left;
        width: 100%;
        margin: 15px auto;
        }

    .modal-backdrop {
        z-index: 0;
    }
    @media all and ( max-width: 680px) {
        .modal-dialog {
            width: auto;
        }
    }
    @media all and ( min-width: 700px) {
        .modal-dialog {
            width: 400px;
        }
    }  
    
    .modal-header {
        border-bottom: none;
    }
</style>
<?php $StoreDetails=$this->Common->getStoreDetails();?>
 
<!-- -------modal for ups calculator -->
<div class="modal fade" id="deliveryzone" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog select-modal">
        <div class="modal-content">
            <div class="modal-header">  
                <button type="button" class="close" id="closemodal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title" id="lineModalLabel">Add Delivery Zone</h3>
            </div>
            <div class="modal-body">
                <span class="errorMsg"></span>
                <div class="container" style="width:auto;">
                   <?php echo $this->Form->create('addzone', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addzone')); ?>
                    <section class="col-12">
                        <ul class="clearfix">
                            <li class="col-xs-12">
                                <span class="title"><label>Name <em>*</em></label></span>
                                <div class="title-box"><?php echo $this->Form->input('Zone.name', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Delivery Zone Name'));
                                echo $this->Form->error('zone.name'); ?></div>
                            </li>

                            <li class="col-xs-12">
                                <span class="title"><label>Fee <em>*</em></label></span>
                                <div class="title-box"><?php echo $this->Form->input('Zone.fee', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Amount', 'label' => false, 'div' => false));
                                echo $this->Form->error('zone.fee'); ?></div>
                            </li>
                            <li class="col-xs-12" id="failmsg" style="color:#ff0000;">
                                
                            </li>
                            
                        </ul>
                        <input type="hidden" value="" name="coordinates" id="hiddencord">
                        <div class="button-grp">
                            <?php
                                    echo $this->Form->button('Add', array('type' => 'button', 'class' => 'btn btn-primary','id'=>'addbtn'));
                                    echo $this->Form->button('Cancel', array('type' => 'button', 'class' => 'btn btn-primary','id'=>'canbtn'));
                            ?>
                        </div>
                    </section>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div> 
        </div>
    </div>
    
</div>


<script>
    	
    $("#addzone").validate({
        rules: {
            "data[zone][name]": {
                required: true,                
            },
            "data[zone][fee]": {
                required: true,
            }
        },
        messages: {
            "data[zone][name_on_bell]": {
                required: "Please enter zone name",
                lettersonly: "Only alphabates allowed",
            },
            "data[zone][fee]": {
                required: "Please enter delivery fee",
            }
        }
    });
    
    
    
    $('#addbtn').click(function () {       
        if($("#addzone").valid()){
             //$('#loading').show();
            $.ajax({
                type: 'POST',                
                url: '/zones/add_zone',
                data: $('#addzone').serialize(),                
                success: function (successResult) {
                    response = jQuery.parseJSON(successResult);
                    if(response.status == 1) {
                        window.location=window.location;
                    }else{
                        $('#failmsg').html(response.msg)
                    }                 
                }
            });
        }
    });
    
    $('#canbtn').click(function () {       
        window.location=window.location;
    });
    
    $('#closemodal').click(function () {       
        window.location=window.location;
    });
    
</script>