<style>
    .btn-margin{margin-left: 1%;}
</style>

<?php
echo $this->Html->script('bootstrap-multiselect');
echo $this->Html->css('bootstrap-multiselect');
?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Sub Add-ons</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php echo $this->Form->button('Upload Sub Add-on', array('type' => 'button', 'onclick' => "window.location.href='/toppings/uploadsubfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('Topping', array('url' => array('controller' => 'toppings', 'action' => 'addSubTopping'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'SizeAdd')); ?>
    <div class="col-lg-6">

        <div class="form-group form_margin">		 
            <label>Sub Add-ons <span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Topping.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Sub Add-ons Name', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.name');
            ?>

        </div>


        <div class="form-group form_margin">
            <label>Category<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Category.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'required' => true, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select'));
            ?>
        </div>
        <!--
        <div class="form-group form_margin">		 
            <label>Add-ons<span class="required"> * </span></label>               
          
        <?php
        echo $this->Form->input('Topping.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $addonList, 'empty' => 'Select Add-ons'));
        echo $this->Form->error('Topping.id');
        ?>
        </div>
        -->

        <?php
//        if ($addonpost) {
//            $display = "style='display:block;'";
//        } else {
        $display = "style='display:none;'";
        //}
        ?>
        <div class="form-group form_spacing" id="addonDiv" <?php echo $display; ?> >
            <label>Add-ons<span class="required"> * </span></label>                
            <span id="addonBox" <?php echo $display; ?> >
                <?php
                echo $this->Form->input('Topping.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'multiple' => false, 'options' => $addonList));
                ?>
            </span>
        </div>

        <?php //if($itempost){ $display="style='display:block;'";}else{$display="style='display:none;'";}  ?>
        <div class="form-group form_spacing" id="ItemsDiv" style="display:none;" >
            <label>Item<span class="required"> * </span></label>                
            <span id="ItemsBox" >               
            </span>
            <span id="OfferId-errors" class="error-message hidden">Please select item.</span>
        </div>


        <div class="form-group form_margin">		 
            <label>Price<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Topping.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.price');
            ?>

        </div>

        <div class="form-group form_spacing">
            <label>No Size applicable</label>
            <?php
            echo $this->Form->checkbox('Topping.no_size');
            ?>
            <label>Default</label>
            <?php
            echo $this->Form->checkbox('Topping.defaultcheck');
            ?>
        </div>


        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Topping.is_active1', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('Topping.is_active1');
            ?>
        </div>



        <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}?>




        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default submit')); ?>             
        <?php echo $this->Html->link('Cancel', "/toppings/listSubTopping/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {

        //$('.multiOnly').multiselect();
        $(document).on('click', '.submit', function (e) {
            e.preventDefault();
            if ($('.multiselect-container li').hasClass('active')) {
                $("#OfferId-errors").addClass('hidden');
            } else {
                $("#OfferId-errors").removeClass('hidden');
            }
            if ($('#SizeAdd').valid()) {
                $('#SizeAdd').submit();
            }
        });
        $(document).on('click', '.multiselect', function (e) {
            if ($('.multiselect-container li').hasClass('active')) {
                $("#OfferId-errors").addClass('hidden');
            } else {
                $("#OfferId-errors").removeClass('hidden');
            }
        });

        $("#SizeAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Topping][name]": {
                    required: true,
                },
                "data[Topping][id]": {
                    required: true,
                },
                "data[Topping][price]": {
                    required: true,
                    number: true
                },
                "data[Topping][item_id][]": {
                    required: true,
                    minlength: 1,
                }

            },
            messages: {
                "data[Topping][name]": {
                    required: "Please enter sub add-ons name",
                },
                "data[Topping][id]": {
                    required: "Please select add-ons",
                },
                "data[Topping][price]": {
                    required: "Please enter price",
                },
                "data[Topping][item_id][]": {
                    required: "Please select Item",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        $('#ToppingName').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#ToppingPrice').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });



        $("#CategoryId").change(function () {
            $("#ItemsDiv").hide();
            var catgoryId = $("#CategoryId").val();
            if (catgoryId) {
                $.ajax({url: "/toppings/addonByCategory/" + catgoryId, success: function (result) {
                        $("#addonDiv").show();
                        $("#addonBox").show();
                        $("#addonBox").html(result);
                    }});
            }
        });

        $('#ToppingPrice').keyup(function () {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });




    });
</script>
<hr>
<br>
<div class="row">
    <div class="col-lg-11">
        <h3>Sub Add-ons Listing</h3>
        <hr />
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Topping', array('url' => array('controller' => 'toppings', 'action' => 'listSubTopping'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">

                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('Topping.category_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $categoryListHasSubAddons, 'empty' => 'Select Category')); ?>		
                </div>

                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('Topping.item_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $itemList, 'empty' => 'Select Item')); ?>		
                </div>


                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('Topping.add_ons_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $addonList, 'empty' => 'Select Add-ons')); ?>		
                </div>





            </div>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Topping.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>
                <div class="col-lg-4">
                    <?php echo $this->Form->input('Topping.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Sub Add-ons,Item,Category')); ?>
                    <span class="blue">(<b>Search by:</b>Sub Add-ons,Item,Category)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">		  
                    <div class="addbutton">                
                        <?php echo $this->Html->link('Clear', array('controller' => 'toppings', 'action' => 'listSubTopping', 'clear'), array('class' => 'btn btn-default')); ?>
                    </div>
                </div>
            </div>
            <!--	    <div class="row padding_btm_20">
                           <div class="col-lg-7">		 
                           </div>
            <div class="col-lg-2">		  
                               <div class="addbutton">                
            <?php //echo $this->Form->button('Add Sub Add-ons', array('type' => 'button','onclick'=>"window.location.href='/toppings/addSubTopping'",'class' => 'btn btn-default'));  ?>
                               </div>
                           </div>
                            <div class="col-lg-2">		  
                               <div class="addbutton">                
            <?php //echo $this->Form->button('Upload Sub Add-on', array('type' => 'button','onclick'=>"window.location.href='/toppings/uploadsubfile'",'class' => 'btn btn-default'));  ?>  
                               </div>
                           </div>
                           
                           
            <!--	    </div>-->
            <?php //echo $this->Form->end(); ?>
            <?php //echo $this->Form->create('Topping', array('url' => array('controller' => 'toppings', 'action' => 'deleteMultipleSubAddon'),'id'=>'OrderId','type'=>'post'));  ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="text-align:left;border:none;"><input type="checkbox" id="selectall"/></th>
                        <?php
                        // if(isset($this->request->data['Topping']['item_id']) && $this->request->data['Topping']['item_id']){
                        ?>
                        <th  class="th_checkbox" style="text-align:left;border:none;"><input type="checkbox" id="selectalldefault"/><span>&nbsp;Default</span></th>
                        <?php
                        //  }
                        ?>
                        <?php if ($pagingFlag) { ?>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Topping.name', 'Sub Add-ons'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Topping.name', 'Add-ons'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Item.name', 'Item name'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Category.name', 'Category name'); ?></th>
                        <?php } else { ?>
                            <th  class="th_checkbox">Sub Add-ons</th>
                            <th  class="th_checkbox">Add-ons</th>
                            <th  class="th_checkbox">Item name</th>
                            <th  class="th_checkbox">Category name</th>
                        <?php } ?>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>			
                        <th  class="th_checkbox">Action</th>
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        //pr($list);
                        $i = 0;
                        foreach ($list as $key => $data) {
                            //pr($data);die;
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptToppingID = $this->Encryption->encode($data['Topping']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptToppingID; ?>">
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Topping.no.' . $key, array('class' => 'case', 'value' => $data['Topping']['id'], 'style' => 'float:left;')); ?></td>
                                <?php
                                /*
                                  if(isset($this->request->data['Topping']['item_id']) && $this->request->data['Topping']['item_id']){
                                  ?>
                                  <td>
                                  <?php
                                  $checked=false;
                                  if(isset($data['ItemDefaultTopping'][0]['topping_id'])){
                                  if($data['Topping']['id']==$data['ItemDefaultTopping'][0]['topping_id']){
                                  $checked=true;
                                  }
                                  }
                                  echo $this->Form->checkbox('Topping.id.'.$key,array('checked' =>$checked,'value'=>$data['Topping']['id'],'class'=>'checkid defaultsub'));
                                  ?></td>
                                  <?php
                                  }
                                 */
                                $checked = false;
                                if (isset($data['ItemDefaultTopping'][0]['topping_id'])) {
                                    if ($data['Topping']['id'] == $data['ItemDefaultTopping'][0]['topping_id']) {
                                        $checked = true;
                                    }
                                }
                                ?>


                                <td><?php echo $this->Form->checkbox('Topping.id.' . $key, array('checked' => $checked, 'class' => 'checkid defaultsub', 'value' => $data['Topping']['id'], 'style' => 'float:left;')); ?></td>
                                <td><?php echo $data['Topping']['name']; ?></td>
                                <td><?php echo $data['ParentGroup']['name']; ?></td>
                                <td><?php echo $data['Item']['name']; ?></td>
                                <td><?php echo $data['Category']['name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Topping']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'toppings', 'action' => 'activateSubTopping', $EncryptToppingID, 0), array('confirm' => 'Are you sure to Deactivate Sub Add-ons?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'toppings', 'action' => 'activateSubTopping', $EncryptToppingID, 1), array('confirm' => 'Are you sure to Activate Sub Add-ons?', 'escape' => false));
                                    }
                                    ?>

                                </td>

                                <td style="width:150px;" class='sort_order'>			
                                    <?php //$EncryptStoreID=$this->Encryption->encode($data['User']['id']); ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'toppings', 'action' => 'editSubTopping', $EncryptToppingID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'toppings', 'action' => 'deleteSubTopping', $EncryptToppingID), array('confirm' => 'Are you sure to delete Sub Add-ons?', 'escape' => false)); ?>
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

                            <td colspan="8">                       

                                <?php
                                echo $this->Form->button('Delete Sub Add-ons', array('name' => 'subaddondelete', 'type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check1();'));
                                echo $this->Form->button('Set Default', array('type' => 'submit', 'class' => 'btn btn-default btn-margin', 'name' => 'set', 'onclick' => 'return check();'));
                                echo $this->Form->button('Unset Default', array('type' => 'submit', 'class' => 'btn btn-default btn-margin', 'name' => 'unset', 'onclick' => 'return check();'));
                                ?>                     


                            </td>

                        </tr>
                    </tfoot>
                <?php } ?>

            </table>
            <?php echo $this->Form->end(); ?>
            <?php if ($pagingFlag) { ?>
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
            <?php } ?>

            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-2">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>

            </div>

        </div>
        <?php echo $this->Html->css('pagination'); ?>
        <style>
            .firstCheckbox{width:10px;}
        </style>

        <script>
            $(document).ready(function () {
                $("#ToppingSearch").autocomplete({
                    source: '/toppings/getSearchValues?sub=sub',
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                }).autocomplete("instance")._renderItem = function (ul, item) {
                    return $("<li>")
                            .append("<div>" + item.desc + "</div>")
                            .appendTo(ul);
                };

                if ($(".defaultsub").length == $(".defaultsub:checked").length) {
                    var o = $(".defaultsub").length;
                    var p = $(".defaultsub:checked").length;
                    if (o != 0 && p != 0) {
                        $("#selectalldefault").prop("checked", true);
                    }

                }
                $("#ToppingItemId").change(function () {
                    //var catgoryId=$("#ToppingItemId").val();
                    $("#AdminId").submit();
                });

                $("#ToppingAddonid").change(function () {
                    $("#AdminId").submit();
                });

                $("#ToppingCategoryId").change(function () {
                    $("#AdminId").submit();
                });

                $("#ToppingIsActive").change(function () {
                    //var catgoryId=$("#ItemCategoryId").val();
                    $("#AdminId").submit();
                });

                $("#ToppingAddOnsId").change(function () {
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
                        $("#selectall").prop("checked", true);
                    } else {
                        $("#selectall").removeAttr("checked");
                    }

                });


                $("#selectalldefault").click(function () {
                    var st = $("#selectalldefault").prop('checked');
                    $('.defaultsub').prop('checked', st);
                });
                // if all checkbox are selected, check the selectall checkbox
                // and viceversa
                $(".defaultsub").click(function () {
                    if ($(".defaultsub").length == $(".defaultsub:checked").length) {
                        $("#selectalldefault").prop("checked", true);
                    } else {
                        $("#selectalldefault").removeAttr("checked");
                    }
                });




            });


            function check() {
                var fields = $(".checkid").serializeArray();
                if (fields.length == 0) {
                    alert('Please select Add-on.');
                    // cancel submit
                    return false;
                }
                return true;
            }


            function check1()
            {

                var fields = $(".case").serializeArray();
                if (fields.length == 0)
                {
                    alert('Please select one Sub Add-ons to proceed.');
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
        <script type="text/javascript">
            $('img.up_order').hide();
            $('img.down_order').hide();

            if (($('#ToppingAddOnsId').val() != '') && ($('#ToppingItemId').val() != '')) {
                $('img.up_order').show();
                $('img.down_order').show();
            } else {
                $('img.up_order').hide();
                $('img.down_order').hide();
            }

            $('select.ToppingAddOnsId').change(function () {
                $('img.up_order').show();
                $('img.down_order').show();
            });

            $('select.ToppingItemId').change(function () {
                $('img.up_order').show();
                $('img.down_order').show();
            });
        </script>
        <script>
            var notifLen = $('table#preferenceListing').find('tr').length;
            $(document).ready(function () {

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
                        url: '/toppings/updateSubAddOnsOrder?' + orderData,
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