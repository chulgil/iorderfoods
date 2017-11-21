<style>
    .addbutton {
        float: none !important;
    }
</style>
<style>
    .days{
        float: left;
        padding: 0 5px;
        text-align: center;
        width: auto;
    }
</style>

<div class="row">
    <div class="col-lg-6">
        <h3>Add Time-Interval</h3>
        <hr></hr>
        <?php echo $this->Session->flash(); ?>   
    </div>
    <div class="col-lg-6">                        
    </div>

</div>

<div class="row">        
    <?php echo $this->Form->create('Interval', array('url' => array('controller' => 'intervals', 'action' => 'addInterval'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addInterval', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">

        <div class="form-group form_margin">		 
            <label>Interval Name<span class="required"> * </span></label>               
            <?php echo $this->Form->input('Interval.name', array('type' => 'text', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'placeholder' => 'Interval Name', 'maxlength' => '40')); ?>
            <?php echo $this->Form->error('Interval.name'); ?>

        </div>    

        <div class="form-group form_margin">		 
            <label>Start Time<span class="required"> * </span></label>
            <?php echo $this->Form->input('Interval.start', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $timeRange, 'empty' => 'Select start time')); ?>
            <?php echo $this->Form->error('Interval.start'); ?>
        </div>

        <div class="form-group form_margin">		 
            <label>End Time<span class="required"> * </span></label>
            <?php echo $this->Form->input('Interval.end', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $timeRange, 'empty' => 'Select end time')); ?>
            <?php echo $this->Form->error('Interval.end'); ?>
        </div>


        <div class="form-group form_spacing">		 
            <label>Status<span class="required"> * </span></label><span>&nbsp;&nbsp;</span>                  
            <?php
            $value = 1;
            if (isset($this->request->data['Interval']['is_active'])) {
                $value = $this->request->data['Interval']['is_active'];
            }
            echo $this->Form->input('Interval.is_active', array('type' => 'radio', 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;', 'value' => $value, 'options' => array('1' => 'Active', '0' => 'Inactive')));
            ?>		 
        </div>

        <div class="form-group form_spacing">		 
            <label>Day</label><span>&nbsp;&nbsp;</span>
            <div class="intervalDays" >
                <?php foreach ($daysArray as $key => $value) { ?>
                    <div class="days">
                        <div>
                            <?php echo $this->Form->checkbox('IntervalDay.' . $key); ?>	
                        </div>
                        <div>
                            <?php echo $value; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="clearfix"><br><br><br></div> 
        <div class="form-group form_spacing">			
            <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
            <?php echo $this->Html->link('Cancel', "/intervals/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $("#addInterval").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Interval][name]": {
                    required: true,
                },
                "data[Interval][start]": {
                    required: true,
                },
                "data[Interval][end]": {
                    required: true,
                },
            },
            messages: {
                "data[Interval][name]": {
                    required: "Please enter time-interval name",
                },
                "data[Interval][start]": {
                    required: "Please select start time",
                },
                "data[Interval][end]": {
                    required: "Please select end time",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
    });
</script>
<hr>
<br>
<div class="row">
    <div class="col-lg-12">
        <h3>Time-Interval Listing</h3>
        <hr></hr>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">
            <?php echo $this->Form->create('Interval', array('url' => array('controller' => 'intervals', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('is_Active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>

                <div class="col-lg-3">
                    <?php echo $this->Form->input('search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Interval Name')); ?>
                    <span class="blue">(<b>Search by:</b>Interval Name)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Html->link('Clear', "/intervals/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>

            <!--	    <div class="row padding_btm_20">		
                            <div class="col-lg-3">		  
                                <div class="addbutton">                
            <?php //echo $this->Form->button('Add Interval', array('type' => 'button','onclick'=>"window.location.href='/intervals/addInterval'",'class' => 'btn btn-default')); ?>  
                                 </div>
                            </div>
                        </div>-->
            <?php echo $this->element('show_pagination_count'); ?>
            <?php echo $this->Form->create('Size', array('url' => array('controller' => 'intervals', 'action' => 'deleteMultipleInterval'), 'id' => 'IntervalId', 'type' => 'post')); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Interval.name', 'Interval Name'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Interval.start', 'Start Timinig'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Interval.end', 'End Timinig'); ?></th>
                        <th  class="th_checkbox">Status :
                            <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?>
                        </th>			
                        <th  class="th_checkbox">Action</th>
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($intervalList) {
                        $i = 0;
                        foreach ($intervalList as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptIntervalID = $this->Encryption->encode($data['Interval']['id']);
                            ?>
                            <tr <?php echo $class; ?>>
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Interval.id.' . $key, array('class' => 'case', 'value' => $data['Interval']['id'], 'style' => 'float:left;')); ?></td>
                                <td><?php echo wordwrap($data['Interval']['name'], 50, "<br />"); ?></td>
                                <td><?php echo $data['Interval']['start']; ?></td>
                                <td><?php echo $data['Interval']['end']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Interval']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'intervals', 'action' => 'activateInterval', $EncryptIntervalID, 0), array('confirm' => 'Are you sure to Deactivate Size?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'intervals', 'action' => 'activateInterval', $EncryptIntervalID, 1), array('confirm' => 'Are you sure to Activate Size?', 'escape' => false));
                                    }
                                    ?>

                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'Intervals', 'action' => 'editInterval', $EncryptIntervalID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'Intervals', 'action' => 'deleteInterval', $EncryptIntervalID), array('confirm' => 'Are you sure to delete Interval?', 'escape' => false)); ?>

                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($intervalList) { ?>
                        <tr>                    
                            <td colspan="6">                                               
                                <?php echo $this->Form->button('Delete Interval', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();')); ?>                     
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php echo $this->Form->end(); ?>
            <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                <?php
                echo $this->Paginator->first('First');
                echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                echo $this->Paginator->numbers(array('separator' => ''));
                echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                echo $this->Paginator->last('Last');
                ?>
            </div>

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

        </div>
        <?php echo $this->Html->css('pagination'); ?>

        <script>
            $(document).ready(function () {
                $("#IntervalIsActive").change(function () {
                    $("#AdminId").submit();
                });
                $("#IntervalSearch").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'intervals', 'action' => 'getSearchValues')); ?>",
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                });
                
                $("#selectall").click(function () {
                    var st = $("#selectall").prop('checked');
                    $('.case').prop('checked', st);

                });
            });

            function check()
            {
                var fields = $(".case").serializeArray();
                if (fields.length == 0)
                {
                    alert('Please select one size to proceed.');
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