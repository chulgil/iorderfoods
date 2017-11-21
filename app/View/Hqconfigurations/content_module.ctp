<div class="row">
    <div class="col-lg-6">
        <h3>Add Content Module</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
<hr>
<div class="row">
    <div class="col-xs-6">
        <?php echo $this->Form->create('MasterContent', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'enctype' => 'multipart/form-data')); ?>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Name<span class="required"> * </span></label>
                <?php
                echo $this->Form->input('name', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'required' => true));
                echo $this->Form->input('id', array('type' => 'hidden'));
                ?>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <?php
                echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
                echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hqconfigurations/htmlModule/" . @$this->params['pass'][0] . "'", 'class' => 'btn btn-default'));
                ?>             
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div><!-- /.row -->
<script type="text/javascript">
    $("#MasterContentContentModuleForm").validate({
        rules: {
            "data[MasterContent][name]": {
                required: true,
            }
        },
        messages: {
            "data[MasterContent][name]": {
                required: "Please enter name.",
            }
        }
    });
</script>