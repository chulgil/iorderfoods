
<div class="row">
    <div class="col-lg-6">
        <h3>Add Size</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php echo $this->Form->button('Upload Size', array('type' => 'button', 'onclick' => "window.location.href='/sizes/uploadfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('Size', array('url' => array('controller' => 'sizes', 'action' => 'addSize'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'SizeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            
        <div class="form-group form_margin">		 
            <label>Category<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Size.category_ids', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select Category'));
            echo $this->Form->error('Sizes.category_ids');
            ?>
        </div>

        <div class="form-group form_margin">		 
            <label>Size<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Size.size', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Size', 'label' => '', 'div' => false));
            echo $this->Form->error('Sizes.size');
            ?>
            <span class="blue">(Please enter comma for Multiple sizes.)</span>

        </div>
        <br>
        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Size.is_active1', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('Size.is_active1');
            ?>
        </div>



        <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}  ?>




        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/sizes/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {

        $("#SizeAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Size][category_ids]": {
                    required: true,
                },
                "data[Size][size]": {
                    required: true,
                }

            },
            messages: {
                "data[Size][category_ids]": {
                    required: "Please select category name",
                },
                "data[Size][size]": {
                    required: "Please enter size",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        $('#SizeSize').change(function () {
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
<hr>
<br>
<div class="row">
    <div class="col-lg-9">
        <h3>Size Listing</h3>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Size', array('url' => array('controller' => 'sizes', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('Size.category_id', array('type' => 'select', 'class' => 'form-control valid', 'id' => 'SizeCategorysizeId', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select Category')); ?>		
                </div>

                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Size.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Status'));
                    ?>		
                </div>

                <div class="col-lg-4">
                    <?php echo $this->Form->input('Size.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Size Name-Category Name')); ?>
                    <span class="blue">(<b>Search by:</b>Size Name-Category Name)</span>
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">		  
                    <div class="addbutton">  
                        <?php echo $this->Html->link('Clear', "/sizes/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                        <?php //echo $this->Form->button('Add Size', array('type' => 'button','onclick'=>"window.location.href='/sizes/addSize'",'class' => 'btn btn-default'));   ?>  
                        <?php //echo $this->Form->button('Upload Size', array('type' => 'button','onclick'=>"window.location.href='/sizes/uploadfile'",'class' => 'btn btn-default'));   ?>  

                    </div>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php echo $this->Paginator->counter('Page {:page} of {:pages}'); ?> 
                </div>
                <div class="col-sm-6 text-right">
                    <?php echo $this->Paginator->counter('showing {:current} records out of {:count} total'); ?> 
                </div>
            </div>
            <?php echo $this->Form->create('Size', array('url' => array('controller' => 'sizes', 'action' => 'deleteMultipleSize'), 'id' => 'OrderId', 'type' => 'post')); ?>

            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>

                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Size.name', 'Size'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Category.name', 'Category'); ?></th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>			
                        <th  class="th_checkbox">Action</th>
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptSizeID = $this->Encryption->encode($data['Size']['id']);
                            ?>
                            <tr <?php echo $class; ?>>
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Size.id.' . $key, array('class' => 'case', 'value' => $data['Size']['id'], 'style' => 'float:left;')); ?></td>

                                <td><?php echo $data['Size']['size']; ?></td>
                                <td><?php echo $data['Category']['name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Size']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'sizes', 'action' => 'activateSize', $EncryptSizeID, 0), array('confirm' => 'Are you sure to Deactivate Size?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'sizes', 'action' => 'activateSize', $EncryptSizeID, 1), array('confirm' => 'Are you sure to Activate Size?', 'escape' => false));
                                    }
                                    ?>


                                </td>

                                <td>

                                    <?php //$EncryptStoreID=$this->Encryption->encode($data['User']['id']); ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'sizes', 'action' => 'editSize', $EncryptSizeID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'sizes', 'action' => 'deleteSize', $EncryptSizeID), array('confirm' => 'Are you sure to delete Size?', 'escape' => false)); ?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } if ($list) { ?>
                        <tr>

                            <td colspan="6">                       

                                <?php
                                echo $this->Form->button('Delete Size', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                ?>                     


                            </td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php echo $this->Form->end(); ?>

            <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                <?php
                echo $this->Paginator->first('First');
                // Shows the next and previous links
                echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                // Shows the page numbers
                echo $this->Paginator->numbers(array('separator' => ''));
                echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                // prints X of Y, where X is current page and Y is number of pages
                //echo $this->Paginator->counter();
                echo $this->Paginator->last('Last');
                ?>
            </div>

            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-2">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-2" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-2" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-2" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";                     ?> </div-->

            </div>

        </div>
        <?php echo $this->Html->css('pagination'); ?>
        <style>
            .firstCheckbox{width:10px;}
        </style>

        <script>
            $(document).ready(function () {
                $("#SizeCategorysizeId").change(function () {
                    var sizeId = $("#SizeCategorysizeId").val();
                    $("#AdminId").submit();
                });

                $("#SizeSearch").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'sizes', 'action' => 'getSearchValues')); ?>",
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                }).autocomplete("instance")._renderItem = function (ul, item) {
                    return $("<li>")
                            .append("<div>" + item.desc + "</div>")
                            .appendTo(ul);
                };

                $("#SizeIsActive").change(function () {
                    //var catgoryId=$("#ItemCategoryId").val();
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
                    alert('Please select one size to proceed.');
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