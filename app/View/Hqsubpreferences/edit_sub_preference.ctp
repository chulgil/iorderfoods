<div class="row">
    <div class="col-lg-6">
        <h3>Edit Sub-Preference</h3> 
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
    <?php echo $this->Form->create('SubPreference', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'TypeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $this->Common->getHQStores($this->Session->read('merchantId'));
//            if (!empty($merchantList)) {
//                $allOption = array('All' => 'All');
//                $merchantList = array_replace($allOption, $merchantList);
//            }
            echo $this->Form->input('SubPreference.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store', 'disabled'));
            ?>
        </div>
        <div class="form-group form_spacing">		 
            <label>Preferences<span class="required"> * </span></label> 
            <?php echo $this->Form->input('SubPreference.type_id', array('type' => 'select', 'class' => 'form-control valid serialize', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $storePreferences)); ?>               
        </div>
        <div class="form-group">		 
            <label>Preference<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('SubPreference.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('SubPreference.name');
            ?>
        </div>             
        <div class="form-group">		 
            <label>Price<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('SubPreference.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
            echo $this->Form->error('SubPreference.price');
            ?>
        </div>




        <div class="form-group">
            <label class='radioLabel'>Status<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('SubPreference.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active', '0' => 'In-Active'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            echo $this->Form->error('SubPreference.is_active');
            ?>
        </div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/hqsubpreferences/index", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->
<script>
    $(document).ready(function () {

        $("#TypeAdd").validate({
            rules: {
                "data[SubPreference][store_id]": {
                    required: true,
                },
                "data[SubPreference][type_id]": {
                    required: true,
                },
                "data[SubPreference][name]": {
                    required: true,
                },
                "data[SubPreference][price]": {
                    required: true,
                    number: true,
                }

            },
            messages: {
                "data[SubPreference][store_id]": {
                    required: "Please enter Store",
                },
                "data[SubPreference][type_id]": {
                    required: "Please enter preference.",
                },
                "data[SubPreference][name]": {
                    required: "Please enter Preference name",
                },
                "data[SubPreference][price]": {
                    required: "Please enter price",
                },
            }
        });
        $('#SubPreferenceName').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#SubPreferencePrice').change(function () {
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