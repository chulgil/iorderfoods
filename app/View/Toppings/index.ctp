<style>
    .col-lg-1 {
        margin-left: 14px;
    }
</style>
<?php
echo $this->Html->script('bootstrap-multiselect');
echo $this->Html->css('bootstrap-multiselect');
?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Add-on</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php echo $this->Form->button('Upload Add-on', array('type' => 'button', 'onclick' => "window.location.href='/toppings/uploadfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('Toppings', array('url' => array('controller' => 'Toppings', 'action' => 'addTopping'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addTopping')); ?>
    <div class="col-lg-6">            
        <div class="form-group form_margin">		 
            <label>Add-on Name<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Topping.name1', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Add-on', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.name1');
            ?>
        </div>


        <div class="form-group form_margin">
            <label>Category<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Category.id1', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'required' => true, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select'));
            ?>
        </div>

        <?php
        if ($itemList) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group form_spacing" id="ItemsDiv" <?php echo $display; ?> >
            <label>Items<span class="required"> * </span></label>                
            <span id="ItemsBox" <?php echo $display; ?> >
                <?php
                echo $this->Form->input('Topping.item_id1', array('type' => 'select', 'class' => 'form-control valid multiOnly', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'multiple' => true, 'options' => $itemList));
                ?>
            </span>
        </div>
        <div class="form-group form_margin">
            <label>Min Sub-Add-on<span class="required"> * </span></label>
            <?php
            $options = range(0, 10);
            echo $this->Form->input('Topping.min_value', array('options' => $options, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.min_value');
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Max Sub-Add-on<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Topping.max_value', array('options' => $options, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.max_value');
            ?>
        </div>

        <!--
        <div class="form-group form_spacing">		 
            <label>Prices<span class="required"> * </span></label>       
          
        <?php
//echo $this->Form->input('Topping.price',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Price','label'=>'','div'=>false));
// echo $this->Form->error('Topping.price'); 
        ?>
              <span class="blue">(Please enter multiple prices by comma separated,if comma separated price not entered for Multiple Add-on first price will be applicable for others.)</span>
        </div>          
        -->        
        <div class="form-group form_spacing">		 
            <label>Status<span class="required"> * </span></label><span>&nbsp;&nbsp;</span>                  
            <?php
            $value = 1;
//                if(isset($this->request->data['Topping']['is_active'])){
//                    $value=$this->request->data['Topping']['is_active'];
//                }
            echo $this->Form->input('Topping.is_active1', array('type' => 'radio', 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;', 'value' => $value, 'options' => array('1' => 'Active', '0' => 'Inactive')));
            ?>		 
        </div>          


        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/toppings/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $("#ToppingMinValue").change(function () {
            var start = $(this).val();
            var end = 10;
            var that = $("#ToppingMaxValue");
            //var array = new Array();
            that.html('');
            for (var i = start; i <= end; i++)
            {
                that.append("<option value=" + i + ">" + i + "</option>");

            }
        });
        $('.alert-success').fadeOut(3000);

        $('.multiOnly').multiselect();

        $("#addTopping").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Topping][name1]": {
                    required: true,
                },
                "data[Topping][category_id1]": {
                    required: true,
                },
                "data[Topping][item_id1][]": {
                    required: true,
                }
                /*,
                 "data[Topping][price]": {
                 required: true,                    
                 }*/

            },
            messages: {
                "data[Topping][name1]": {
                    required: "Please enter Add-on name",
                },
                "data[Topping][category_id1]": {
                    required: "Please select category",
                },
                "data[Topping][item_id1][]": {
                    required: "Please select item",
                }/*,
                 "data[Topping][price]": {
                 required: "Please enter price",            
                 }*/
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });

        $("#CategoryId1").change(function () {
            var catgoryId = $("#CategoryId1").val();
            if (catgoryId) {
                $.ajax({url: "/items/itemsByCategory/" + catgoryId, success: function (result) {
                        $("#ItemsDiv").show();
                        $("#ItemsBox").show();
                        $("#ItemsBox").html(result);
                    }});
            }
        });

        $('#ToppingPrice').keyup(function () {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });

        $('#ToppingName1').change(function () {
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
    <div class="col-lg-11">
        <h3>Add-ons Listing</h3>
        <hr />
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Topping', array('url' => array('controller' => 'Toppings', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('Topping.item_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $customTopping, 'empty' => 'Select Item')); ?>		
                </div>

                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Topping.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>

                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('keyword', array('value' => $keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                    <span class="blue">(<b>Search by:</b>Add-on name,Item name)</span>
                </div>

                <div class="col-lg-1">		 
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>&nbsp;&nbsp;
                </div>
                &nbsp;&nbsp;
                <div class="col-lg-1">		  
                    <div class="addbutton">                
                        <?php echo $this->Html->link('Clear', array('controller' => 'Toppings', 'action' => 'index', 'clear'), array('class' => 'btn btn-default')); ?>
                    </div>
                </div>
            </div>
            <!--		<div class="row padding_btm_20">-->
            <!--			<div class="col-lg-7">		 
                                    </div>-->

            <!--			<div class="col-lg-2">		  
                                        <div class="addbutton">                
            <?php //echo $this->Form->button('Add Add-ons', array('type' => 'button','onclick'=>"window.location.href='/toppings/addTopping'",'class' => 'btn btn-default'));   ?>
                                        </div>
                                    </div>-->

            <!--			 <div class="col-lg-2">		  
                                        <div class="addbutton">                
            <?php //echo $this->Form->button('Upload Add-on', array('type' => 'button','onclick'=>"window.location.href='/toppings/uploadfile'",'class' => 'btn btn-default'));   ?>  
                                        </div>
                                    </div>-->

            <!--			 <div class="col-lg-1">		  
                                        <div class="addbutton">                
            <?php //echo $this->Html->link('Clear',array('controller'=>'Toppings','action'=>'index','clear'),array('class' => 'btn btn-default')); ?>
                                        </div>
                                    </div>
                            </div>-->
            <?php echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>

                        <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>			
                        <?php if ($pagingFlag) { ?>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Topping.name', 'Add-on name'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Item.name', 'Item name'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Category.name', 'Category name'); ?></th>
                        <?php } else { ?>
                            <th  class="th_checkbox">Add-on name</th>
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
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptToppingID = $this->Encryption->encode($data['Topping']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptToppingID; ?>">

                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Topping.no.' . $key, array('class' => 'case', 'value' => $data['Topping']['id'], 'style' => 'float:left;')); ?></td>                   

                                <td><?php echo $data['Topping']['name']; ?></td>
                                <td><?php echo $data['Item']['name']; ?></td>
                                <td><?php echo $data['Category']['name']; ?></td>			
                                <td>
                                    <?php
                                    if ($data['Topping']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'Toppings', 'action' => 'activateTopping', $EncryptToppingID, 0), array('confirm' => 'Are you sure to Deactivate Add-on?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'Toppings', 'action' => 'activateTopping', $EncryptToppingID, 1), array('confirm' => 'Are you sure to Activate Add-on?', 'escape' => false));
                                    }
                                    ?>
                                </td>


                                <td style="width:150px;" class='sort_order'>
                                    <?php //$EncryptStoreID=$this->Encryption->encode($data['User']['id']);  ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'Toppings', 'action' => 'editTopping', $EncryptToppingID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'Toppings', 'action' => 'deleteTopping', $EncryptToppingID), array('confirm' => 'Are you sure to delete Addon?', 'escape' => false)); ?>
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
                            <td colspan="8" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <?php if ($list) { ?>
                    <tfoot>
                        <tr>
                            <td colspan="6">                       
                                <?php echo $this->Form->button('Delete Add-ons', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check1();')); ?>                     
                            </td>
                        </tr>
                    </tfoot>
                <?php } ?>
                <?php echo $this->Form->end(); ?>

            </table>
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
                <div class="col-lg-1">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-1" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";              ?> </div-->

            </div>

        </div></div></div>
<?php echo $this->Html->css('pagination'); ?>
<style>
    .firstCheckbox{width:10px;}
</style>

<script>
    $(document).ready(function () {
        $("#ToppingItemId").change(function () {
            //var catgoryId=$("#ToppingItemId").val();
            $("#AdminId").submit();
        });

        $("#ToppingIsActive").change(function () {
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
            alert('Please select one Add-ons to proceed.');
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

    if ($('#ToppingItemId').val() == '') {
        $('img.up_order').hide();
        $('img.down_order').hide();
    } else {
        $('img.up_order').show();
        $('img.down_order').show();
    }

    $('select.ToppingItemId').change(function () {
        $('img.up_order').show();
        $('img.down_order').show();
    });
</script>
<script>
    var notifLen = $('table#preferenceListing').find('tr').length;
    $(document).ready(function () {
        $("#ToppingKeyword").autocomplete({
            source: "<?php echo $this->Html->url(array('controller' => 'toppings', 'action' => 'getSearchValues')); ?>",
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        };

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
                url: '/toppings/updateAddOnsOrder?' + orderData,
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