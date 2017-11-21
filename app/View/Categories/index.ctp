
<div class="row">
    <div class="col-lg-6">
        <h3>Add Category</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                

            <?php echo $this->Form->button('Upload Category', array('type' => 'button', 'onclick' => "window.location.href='/categories/uploadfile'", 'class' => 'btn btn-default')); ?>  

        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('Categories', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'CategoryAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            
        <div class="form-group form_margin">		 
            <label>Category Name<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('category.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Category', 'label' => '', 'div' => false));
            echo $this->Form->error('category.name');
            ?>
        </div>
        <br>

        <div class="form-group form_margin">
            <label>Upload Image</label>
            <?php echo $this->Form->input('category.imgcat', array('type' => 'file', 'div' => false)); ?>
            <span class="blue">Max upload size 2MB</span> 
        </div>

        <!--div class="form-group form_margin">
            <label>Item Options</label> 
            <?php //echo $this->Form->input('category.is_sizeonly', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'empty' => 'Choose One', 'options' => array('1' => 'Size Only', '2' => 'Preference Only', '3' => 'Size and Preference'), 'empty' => 'Select'));
            ?>
        </div!-->
        <br>
        <div class="form-group form_margin">
            <label>Has Add-Ons<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('category.has_topping', array(
                'type' => 'radio',
                'options' => array('1' => 'Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => 'No'),
                'default' => 1
            ));
            echo $this->Form->error('category.has_topping');
            ?>
        </div>

        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('category.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('category.is_active');
            ?>
        </div>
        <div class="form-group">
            <label>
                <?php
                echo $this->Form->checkbox('category.is_meal', array('value' => '1'));
                echo $this->Form->error('category.is_meal');
                ?>
                Time Restrictions
            </label>
        </div>
        <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";} ?>
        <span id="FromTodate" style="display:none"> 
            <div class="form-group">
                <label>Start Time</label>
                <td>
                    <?php
                    echo $this->Form->input('category.start_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false));
                    echo $this->Form->error('category.start_time');
                    ?>
                </td>
                <label>End Time</label>
                <td>
                    <?php
                    echo $this->Form->input('category.end_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false));
                    echo $this->Form->error('category.end_time');
                    ?>
                </td>
            </div>
            <div class="form-group form_margin">
                <?php
                $days = array('1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thur', '5' => 'Fri', '6' => 'Sat', '7' => 'Sun');
                $selectedDays = '';
                foreach ($days as $key => $data) {
                    //pr($data);
                    //echo "<div class='new-chkbx-wrap'>";
                    echo "<div class='pull-left' style='padding-right:15px;'>";
                    echo "<label>";
                    echo $this->Form->checkbox('category.days.' . $key, array('hiddenField' => false, 'multiple' => 'checkbox', 'class' => '', 'value' => $data, 'checked' => 'checked'));
                    echo $data . "</div></label>";
                }
                ?>
            </div>
        </span>
        <div class="form-group">
            <label>
                <?php
                echo $this->Form->checkbox('category.is_mandatory', array('value' => '1'));
                echo $this->Form->error('category.is_mandetory');
                ?>
                Mandatory
            </label>
        </div>
        <span id="minMaxItem" style="display:none"> 
            <div class="form-group form_margin">
                <label>Min Item<span class="required"> * </span></label>
                <?php
                $minOptions = array_slice(range(0, 10), 1, NULL, TRUE);
                echo $this->Form->input('category.min_value', array('options' => $minOptions, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
                echo $this->Form->error('category.min_value');
                ?>
            </div>
            <div class="form-group form_margin">
                <label>Max Item<span class="required"> * </span></label>  
                <?php
                $maxOptions = array_slice(range(0, 10), 1, NULL, TRUE);
                echo $this->Form->input('category.max_value', array('options' => $maxOptions, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
                echo $this->Form->error('category.max_value');
                ?>
            </div>
        </span>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/categories/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $("#categoryMinValue").change(function () {
            var start = $(this).val();
//            var categoryMaxValue = $("#categoryMaxValue").val();
//            if (start > categoryMaxValue) {
//                alert("Select value small than Max Item.");
//                $(this).val($("#categoryMinValue option:first").val());
//                return false;
//            }
            var end = 10;
            var that = $("#categoryMaxValue");
            //var array = new Array();
            that.html('');
            for (var i = start; i <= end; i++)
            {
                that.append("<option value=" + i + ">" + i + "</option>");

            }
        });
        $("#CategoryAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[category][name]": {
                    required: true,
                    //lettersonly: true
                },
                "data[category][has_topping]": {
                    required: true,
                },
                "data[category][is_active]": {
                    required: true,
                },
                "data[category][start_time]": {
                    required: true,
                },
                "data[category][position]": {
                    required: true,
                },
                "data[category][end_time]": {
                    required: true,
                },
            },
            messages: {
                "data[category][name]": {
                    required: "Please enter category name",
                    lettersonly: "Only alphabates allowed",
                },
                "data[category][has_topping]": {
                    required: "Please select topping",
                },
                "data[category][is_active]": {
                    required: "Please select status ",
                },
                "data[category][start_time]": {
                    required: "Please select start time",
                },
                "data[category][position]": {
                    required: "Please select category position",
                },
                "data[category][end_time]": {
                    required: "Please select end time",
                },
            }
            , highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });

        $("#categoryIsMeal").change(function () {
            var flag = $("#categoryIsMeal").val();
            if ($(this).is(":checked")) {
                $("#FromTodate").show();
            } else {
                $("#FromTodate").hide();
            }
        });
        $("#categoryIsMandatory").change(function () {
            var flag = $(this).val();
            if ($(this).is(":checked")) {
                $("#minMaxItem").show();
            } else {
                $("#minMaxItem").hide();
            }
        });
    });


    $('#categoryName').change(function () {
        var str = $(this).val();
        if ($.trim(str) === '') {
            $(this).val('');
            $(this).css('border', '1px solid red');
            $(this).focus();
        } else {
            $(this).css('border', '');
        }
    });
</script>

<hr>
<br>

<div class="row">
    <div class="col-lg-8">
        <h3>Category Listing</h3>
        <br>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('category', array('url' => array('controller' => 'categories', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('category.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>

                <div class="col-lg-4">
                    <?php echo $this->Form->input('Category.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Category Name')); ?>
                    <span class="blue">(<b>Search by:</b>Category Name)</span>
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Html->link('Clear', "/categories/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->Form->create('Category', array('url' => array('controller' => 'categories', 'action' => 'deleteMultipleCategory'), 'id' => 'OrderId', 'type' => 'post')); ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="categoriesListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                        <!--<th  class="th_checkbox"><?php //echo $this->Paginator->sort('Category.name', 'Name');                                 ?></th>-->
                        <th  class="th_checkbox">Name</th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>

                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptCategoryID = $this->Encryption->encode($data['Category']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptCategoryID; ?>">
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Category.id.' . $key, array('class' => 'case', 'value' => $data['Category']['id'], 'style' => 'float:left;')); ?></td>
                                <td style="width:200px;">
                                    <?php
                                    echo $data['Category']['name'];
                                    if (!empty($data['Category']['is_mandatory'])) {
                                        echo '&nbsp;&nbsp;<small>(Mandatory)</small>';
                                    }
                                    ?>

                                </td>

                                <td style="width:150px;">
                                    <?php
                                    if ($data['Category']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'categories', 'action' => 'activateCategory', $EncryptCategoryID, 0), array('confirm' => 'Are you sure to Deactivate Category?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'categories', 'action' => 'activateCategory', $EncryptCategoryID, 1), array('confirm' => 'Are you sure to Activate Category?', 'escape' => false));
                                    }
                                    ?>
                                </td>

                                <td style="width:150px;" class='sort_order'>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'categories', 'action' => 'editCategory', $EncryptCategoryID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'categories', 'action' => 'deleteCategory', $EncryptCategoryID), array('confirm' => 'Are you sure to delete Category?', 'escape' => false)); ?>
                                    <?php
                                    echo $this->Html->image('uparrow.png', array('alt' => "Up", 'title' => "Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                    echo $this->Html->image('downarrow.png', array('alt' => "Down", 'title' => "Down", 'class' => 'down_order', 'id' => 'downOrder'));
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
                                echo $this->Form->button('Delete Category', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                ?>                     


                            </td>

                        </tr>
                    </tfoot>
                <?php } ?>

            </table>
            <?php echo $this->Form->end(); ?>
            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-2">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-2"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";                                      ?> </div-->

            </div>

        </div>
        <style>
            .firstCheckbox{width:10px;}
        </style>

        <script>
            $(document).ready(function () {
                $("#categoryIsActive").change(function () {
                    var catgoryId = $("#categoryIsActive").val
                    $("#AdminId").submit();
                });
                $("#CategorySearch").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'categories', 'action' => 'getSearchValues')); ?>",
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
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
                    alert('Please select one category to proceed.');
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
            var notifLen = $('table#categoriesListing').find('tr').length;
            $(document).ready(function () {

                // Hide up arrow from first row 
                $('table#categoriesListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
                // Hide down arrow from last row 
                $('table#categoriesListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();

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

                $('table#categoriesListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
                $('table#categoriesListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();

                var orderData = getNotifOrderKeyVal();

                if (orderData) {
                    $.ajax({
                        url: '/categories/updateOrder?' + orderData,
                        type: 'get',
                        success: function () {


                        }
                    });
                }
            }

            function getNotifOrderKeyVal() {
                if ($('table#categoriesListing tbody').eq(0).find('tr').length > 0) {
                    var orderData = '';
                    $('table#categoriesListing tbody').eq(0).find('tr').each(function (i) {
                        var notifId = $(this).attr('notif-id');
                        orderData += notifId + '=' + (i + 1) + '&';
                    });
                    return orderData;
                }
                return false;
            }
        </script>