<div class="row">
    <div class="col-lg-6">
        <h3>Edit Preference</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<hr>
<div class="row">        
    <?php echo $this->Form->create('Types', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'TypeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $this->Common->getHQStores($this->Session->read('merchantId'));
            echo $this->Form->input('Type.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store', 'disabled'));
            ?>
        </div>

        <div class="form-group">		 
            <label>Preference<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Type.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Type.name');
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Min Sub-Preference<span class="required"> * </span></label>
            <?php
            $options = range(0, 10);
            echo $this->Form->input('Type.min_value', array('options' => $options, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Type.min_value');
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Max Sub-Preference<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Type.max_value', array('options' => $options, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Type.max_value');
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Type.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('Type.is_active');
            ?>
        </div>
        <?php echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/hqtypes/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $("#TypeMinValue").change(function () {
            var start = $(this).val();
            var end = 10;
            var that = $("#TypeMaxValue");
            //var array = new Array();
            that.html('');
            for (var i = start; i <= end; i++)
            {
                that.append("<option value=" + i + ">" + i + "</option>");

            }
        });
        $("#TypeAdd").validate({
            rules: {
                "data[Type][store_id]": {
                    required: true,
                },
                "data[Type][name]": {
                    required: true,
                }
            },
            messages: {
                "data[Type][store_id]": {
                    required: "Please enter store.",
                },
                "data[Type][name]": {
                    required: "Please enter Preference name",
                },
            }
        });
        $('#TypeName').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#TypePrice').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
    });
</script>