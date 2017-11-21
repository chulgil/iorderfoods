<div class="row">
    <div class="col-lg-6">
        <h3>Add Logo Position</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
<hr>
<div class="row">
    <div class="col-xs-6">
        <?php echo $this->Form->create('MerchantConfiguration', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'enctype' => 'multipart/form-data')); ?>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Select Logo Position<span class="required"> * </span></label>
                <?php
                $positionList = array('1' => 'TOP', '2' => 'LEFT', '3' => 'CENTER');
                echo $this->Form->input('logo_position', array('type' => 'select', 'options' => @$positionList, 'class' => 'form-control', 'label' => false, 'div' => false));
                echo $this->Form->input('id', array('type' => 'hidden'));
                ?>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label class="radioLabel">Contact Us<span class="required"> * </span></label>                
                <?php
                echo $this->Form->input('contact_active', array(
                    'type' => 'radio',
                    'options' => array('1' => 'Enable', '0' => 'Disable'),
                    'default' => 0,
                    'label' => false,
                    'legend' => false,
                    'div' => false
                ));
                ?>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div><!-- /.row -->