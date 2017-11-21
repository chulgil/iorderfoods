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
    <?php echo $this->Form->create('Interval', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addInterval', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($merchantList)) {
                $allOption = array('All' => 'All Store');
                $merchantList = array_replace($allOption, $merchantList);
            }
            echo $this->Form->input('Interval.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
            ?>
        </div>
        <div class="form-group">		 
            <label>Interval Name<span class="required"> * </span></label>               
            <?php echo $this->Form->input('Interval.name', array('type' => 'text', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'placeholder' => 'Interval Name', 'maxlength' => '40')); ?>
            <?php echo $this->Form->error('Interval.name'); ?>
        </div>    
        <div class="form-group">		 
            <label>Start Time<span class="required"> * </span></label>
            <?php echo $this->Form->input('Interval.start', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $timeRange, 'empty' => 'Select start time')); ?>
            <?php echo $this->Form->error('Interval.start'); ?>
        </div>
        <div class="form-group">		 
            <label>End Time<span class="required"> * </span></label>
            <?php echo $this->Form->input('Interval.end', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $timeRange, 'empty' => 'Select end time')); ?>
            <?php echo $this->Form->error('Interval.end'); ?>
        </div>
        <div class="form-group">		 
            <label>Status<span class="required"> * </span></label><span>&nbsp;&nbsp;</span>                  
            <?php
            $value = 1;
            if (isset($this->request->data['Interval']['is_active'])) {
                $value = $this->request->data['Interval']['is_active'];
            }
            echo $this->Form->input('Interval.is_active', array('type' => 'radio', 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;', 'value' => $value, 'options' => array('1' => 'Active', '0' => 'Inactive')));
            ?>		 
        </div>
        <div class="form-group">		 
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
        <div class="clearfix"></div> 
        <div class="form-group">			
            <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
            <?php echo $this->Html->link('Cancel', "/hqintervals/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->
<hr>
<div class="row">
    <div class="col-lg-12">
        <h3>Time-Interval Listing</h3>
        <hr>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive">   
            <?php echo $this->Form->create('Interval', array('url' => array('controller' => 'hqintervals', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('Interval.storeId', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store', 'id' => 'storeId')); ?>
                </div>
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
                    <?php echo $this->Html->link('Clear', "/hqintervals/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->Form->create('Interval', array('url' => array('controller' => 'hqintervals', 'action' => 'deleteMultipleInterval'), 'id' => 'IntervalId', 'type' => 'post')); ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Interval.name', 'Interval Name'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Interval.start', 'Start Timinig'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Interval.end', 'End Timinig'); ?></th>
                        <th  class="th_checkbox">Store Name</th>
                        <th  class="th_checkbox">Status :
                            <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?>
                        </th>			
                        <th  class="th_checkbox">Action</th>
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if (!empty($intervalList)) {
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
                                <td><?php echo @$data['Store']['store_name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Interval']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqintervals', 'action' => 'activateInterval', $EncryptIntervalID, 0), array('confirm' => 'Are you sure to Deactivate Size?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqintervals', 'action' => 'activateInterval', $EncryptIntervalID, 1), array('confirm' => 'Are you sure to Activate Size?', 'escape' => false));
                                    }
                                    ?>

                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqintervals', 'action' => 'editInterval', $EncryptIntervalID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqintervals', 'action' => 'deleteInterval', $EncryptIntervalID), array('confirm' => 'Are you sure to delete Interval?', 'escape' => false)); ?>
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
                    <?php if (!empty($intervalList)) { ?>
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
            </div>
        </div>
    </div>
</div>
<?php echo $this->Html->css('pagination'); ?>
<script>
    $(document).ready(function () {
        var storeId=$('#storeId').val();
        $("#IntervalSearch").autocomplete({
                    source: "/hqintervals/getSearchValues?storeID="+storeId,
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                });
        $("#IntervalIsActive").change(function () {
                   $("#AdminId").submit();
                });        
                
        $("#addInterval").validate({
            rules: {
                "data[Interval][store_id]": {
                    required: true,
                },
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
                "data[Interval][store_id]": {
                    required: "Please select store.",
                },
                "data[Interval][name]": {
                    required: "Please enter time-interval name",
                },
                "data[Interval][start]": {
                    required: "Please select start time",
                },
                "data[Interval][end]": {
                    required: "Please select end time",
                },
            }
        });
        $("#storeId").change(function () {
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