<style>
    /*  #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
      #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
      #sortable li span { position: absolute; margin-left: -1.3em; }*/
</style>
<script>
    //$(function() {
    //  $( "#sortable" ).sortable();
    //  $( "#sortable" ).disableSelection();
    //});
</script>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Preference</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                         
        <div class="addbutton">                
            <?php echo $this->Form->button('Upload Preference', array('type' => 'button', 'onclick' => "window.location.href='/types/uploadfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>   
<hr>
<div class="row">        
    <?php echo $this->Form->create('Types', array('url' => array('controller' => 'types', 'action' => 'addType'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'TypeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            
        <div class="form-group form_margin">		 
            <label>Preference<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Type.name1', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Preference', 'label' => '', 'div' => false));
            echo $this->Form->error('Type.name1');
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
        <!--<br>
             <div class="form-group form_margin">		 
           <label>Price<span class="required"> * </span></label>               
         
        <?php
        echo $this->Form->input('Type.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
        echo $this->Form->error('Type.price');
        ?>
       </div><br>-->
        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Type.is_active1', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('Type.is_active1');
            ?>
        </div>

        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->
<hr>
<br>
<div class="row">
    <div class="col-lg-9">
        <h3>Preference Listing</h3>
        <hr />
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Type', array('url' => array('controller' => 'types', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <!--<div class="col-lg-4">		     
                <?php //echo $this->Form->input('Type.item_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $itemList, 'empty' => 'Select Item')); ?>		
               </div>-->

                <div class="col-lg-3">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Type.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>
                <div class="col-lg-3">
                    <?php echo $this->Form->input('Type.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Preference Name')); ?>
                    <span class="blue">(<b>Search by:</b>Preference Name)</span>
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">		  
                    <div class="addbutton">  
                        <?php echo $this->Html->link('Clear', "/types/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                        <?php //echo $this->Form->button('Add Preference', array('type' => 'button','onclick'=>"window.location.href='/types/addType'",'class' => 'btn btn-default')); ?>  
                        <?php //echo $this->Form->button('Upload Preference', array('type' => 'button','onclick'=>"window.location.href='/types/uploadfile'",'class' => 'btn btn-default'));  ?>  

                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    if (!empty($list)) {
                        echo $this->element('show_pagination_count');
                    }
                    ?>
                </div>
            </div>
            <?php echo $this->Form->create('Type', array('url' => array('controller' => 'types', 'action' => 'deleteMultipleType'), 'id' => 'OrderId', 'type' => 'post')); ?>

            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="float:left;border:none"><input type="checkbox" id="selectall"/></th>
                        <th  class="th_checkbox">Name</th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>

                </thead>

                <tbody id="sortable" class="dyntable" >
                    <?php
                    if (!empty($list)) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="ui-state-default active "' : 'ui-state-default';
                            $EncryptTypeID = $this->Encryption->encode($data['Type']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptTypeID; ?>" >
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Type.id.' . $key, array('class' => 'case', 'value' => $data['Type']['id'], 'style' => 'float:left;')); ?></td>

                                <td><?php echo $data['Type']['name']; ?></td>

                                <td>
                                    <?php
                                    if ($data['Type']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'types', 'action' => 'activateType', $EncryptTypeID, 0), array('confirm' => 'Are you sure to Deactivate Preference?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'types', 'action' => 'activateType', $EncryptTypeID, 1), array('confirm' => 'Are you sure to Activate Preference?', 'escape' => false));
                                    }
                                    ?>
                                </td>

                                <td style="width:150px;" class='sort_order'>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'types', 'action' => 'editType', $EncryptTypeID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'types', 'action' => 'deleteType', $EncryptTypeID), array('confirm' => 'Are you sure to delete Preference?', 'escape' => false)); ?>
                                    <?php
                                    //echo $this->Html->image('uparrow.png', array('alt'=>"Up", 'title'=>"Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                    //echo $this->Html->image('downarrow.png', array('alt'=>"Down", 'title'=>"Down", 'class' => 'down_order', 'id' => 'downOrder'));
                                    ?>
                                </td> 

                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="11" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <?php if ($list) { ?>
                    <tfoot>
                        <tr>

                            <td colspan="6">                       

                                <?php
                                echo $this->Form->button('Delete Preference', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                ?>                     


                            </td>

                        </tr>
                    </tfoot> 
                <?php } ?>

            </table>
            <?php echo $this->Form->end(); ?>	
            <?php
            if (!empty($list)) {
                echo $this->element('pagination');
            }
            ?>
            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-2">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-2"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
            </div>
        </div>
        <style>
            .firstCheckbox{width:10px;}
            #btnUpdateOrder{margin-left: 1%;}
        </style>
    </div>
</div>
<script type="text/javascript">
    //$('img.up_order').hide();
    //$('img.down_order').hide();
    //
    //if ($('#TypeItemId').val()=='') {
    //	$('img.up_order').hide();
    //	$('img.down_order').hide();
    //}else{
    //	$('img.up_order').show();
    //	$('img.down_order').show();
    //}
    //
    //$('select.TypeItemId').change(function() {
    //	$('img.up_order').show();
    //	$('img.down_order').show();	
    //});
</script>	    
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
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Type][name1]": {
                    required: true,
                }
            },
            messages: {
                "data[Type][name1]": {
                    required: "Please enter Preference name",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
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

        $("#TypeIsActive").change(function () {
            var catgoryId = $("#TypeIsActive").val
            $("#AdminId").submit();
        });

        $("#TypeItemId").change(function () {
            var catgoryId = $("#TypeItemId").val
            $("#AdminId").submit();
        });

        $("#selectall").click(function () {
            var st = $("#selectall").prop('checked');
            $('.case').prop('checked', st);
        });
        // if all checkbox are selected, check the selectall checkbox
        // and viceversa
        $(".case").click(function () {
            if ($(".case").length == $(".case:checked").length) {
                $("#selectall").attr("checked", "checked");
            } else {
                $("#selectall").removeAttr("checked");
            }

        });
    });
    function check()
    {

        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select one Preference to proceed.');
            // cancel submit
            return false;
        }
        var r = confirm("Are you sure you want to delete");
        if (r == true) {
            txt = "You pressed OK!";
        } else {
            txt = "You pressed Cancel!";
            return false;

        }

    }
</script>

<script>
    var notifLen = $('table#preferenceListing').find('tr').length;
    $(document).ready(function () {
        $("#TypeSearch").autocomplete({
            source: "<?php echo $this->Html->url(array('controller' => 'types', 'action' => 'getSearchValues')); ?>",
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });

        // Hide up arrow from first row 
        $('table#preferenceListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        // Hide down arrow from last row 
        $('table#preferenceListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();

        var $up = $(".up_order")
        $up.click(function () {
            var $tr = $(this).parents("tr");
            if ($tr.index() != 0) {
                $tr.fadeOut().fadeIn();
                $tr.prev().before($tr);

            }
            updateOrder();
        });



        //down
        var $down = $(".down_order");
        var len = $down.length;
        $down.click(function () {
            var $tr = $(this).parents("tr");

            if ($tr.index() <= len) {

                $tr.fadeOut().fadeIn();
                $tr.next().after($tr);
            }
            updateOrder();
        });
    });

    function updateOrder() {
        $('img.up_order').show();
        $('img.down_order').show();

        $('table#preferenceListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        $('table#preferenceListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();

        var orderData = getNotifOrderKeyVal();

        if (orderData) {
            $.ajax({
                url: '/types/updateOrder?' + orderData,
                type: 'get',
                success: function () {


                }
            });
        }
    }

    function getNotifOrderKeyVal() {
        if ($('table#preferenceListing tbody').eq(0).find('tr').length > 0) {
            var orderData = '';
            $('table#preferenceListing tbody').eq(0).find('tr').each(function (i) {
                var notifId = $(this).attr('notif-id');
                orderData += notifId + '=' + (i + 1) + '&';
            });
            return orderData;
        }
        return false;
    }
</script>