<div class="row">
    <div class="col-lg-12">
        <h3>Manage Template Css</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
<hr>
<div class="row">        
    <?php echo $this->Form->create('StoreStyle', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addCategory')); ?>
    <div class="col-lg-6">            
        <div class="form-group">
            <label>Themes</label>
            <?php
//$options=array('1'=>'Brown Theme');
            echo $this->Form->input('store_theme_id', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $themeOptions, 'empty' => false));
            ?>
        </div>
        <div class="form-group">
            <?php
            echo $this->Form->input('navigation', array(
                'type' => 'radio',
                'div' => false,
                'legend' => false,
                'options' => array('1' => '&nbsp;&nbsp;Vertical Navigation&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => '&nbsp;&nbsp;Horizontal Navigation'),
                'default' => 1
            ));
            ?>
            <span class="blue"></span>
        </div>
        <div class="form-group">		 
            <label>Css<span class="required"> * </span></label>
            <div class="cssDiv">
                <?php
                echo $this->Form->input('css', array('type' => 'textarea', 'class' => 'form-control', 'placeholder' => 'Enter Css', 'label' => false, 'div' => false, 'rows' => "20"));
                echo $this->Form->input('id', array('type' => 'hidden'));
                ?>
            </div>
        </div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/stores/dashboard", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    function getFormData() {
        var storeStyleThemeId = $("#StoreStyleStoreThemeId").val();
        var storeStyleNavigation = $("input[name='data[StoreStyle][navigation]']:checked").val();
        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'stores', 'action' => 'getStoreAdminStyle')); ?>",
            type: "post",
            dataType: 'html',
            async: false,
            data: {storeStyleThemeId: storeStyleThemeId, storeStyleNavigation: storeStyleNavigation},
            success: function (result) {
                if (result) {
                    $(".cssDiv").html(result);
                } else {
                    $("#StoreStyleCss").val('');
                    $("#StoreStyleId").val('');
                }
            }
        });
    }
    $(document).ready(function () {
        $("input[name='data[StoreStyle][navigation]']").on('click', function () {
            getFormData();
        });
        $("#StoreStyleStoreThemeId").change(function () {
            getFormData();
        });
        if ($("#StoreStyleStoreThemeId").val()) {
            getFormData();
        }
    });

</script>