<div class="row">
    <div class="col-lg-6">
        <h3>Manage Store Hours</h3>
        <hr>
        <?php 
       
        echo $this->Session->flash(); ?>   
    </div> 
</div>   
<div class="row">        
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if(!empty($this->request->data['Store']['id'])){
                $store_id = $this->request->data['Store']['id'];
            }
            echo $this->Form->input('Store.id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store','disabled'=>true));
            ?>
        </div>
        <span class="blue">(Please Select date on which store will remains closed)</span> 
        <div class="form-group form_spacing">
            <?php
            echo $this->Form->create('Stores', array('url' => array('controller' => 'hqstores', 'action' => 'addClosedDate'),'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'storeclosed'));
            ?>
            <label>Select Date<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('StoreHoliday.holiday_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true, 'Placeholder' => 'Select Date'));
            ?>
        </div>
        <div class="form-group form_spacing">
            <label>Description</label> 
            <?php
            echo $this->Form->input('StoreHoliday.description', array('type' => 'textarea', 'rows' => '5', 'cols' => '5', 'class' => 'form-control', 'Placeholder' => 'Enter Description'));
            ?>
        </div>

        <div class="form-group form_spacing">
            <?php
            echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
            echo "&nbsp;";
            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hqstores/index'", 'class' => 'btn btn-default'));
            echo $this->Form->end();
            ?>
        </div>
        <hr/>
        <div class="table-responsive">   
            <div class="table-responsive">
                <span class="blue">(List of store closed dates)</span> 
                <table class="table table-bordered table-hover table-striped tablesorter">
                    <thead>
                        <tr>	    
                            <th  class="th_checkbox"><?php echo "Closed Date"; ?></th>
                            <th  class="th_checkbox"><?php echo "Description"; ?></th>
                            <th  class="th_checkbox"><?php echo "Created"; ?></th>   
                            <th  class="th_checkbox">Action</th>
                        </tr>
                    </thead>

                    <tbody class="dyntable">
                        <?php if ($holidayInfo) { ?>
                            <?php foreach ($holidayInfo as $key => $data) { ?>
                                <tr>	    
                                    <td><?php echo $this->Dateform->us_format($data['StoreHoliday']['holiday_date']); ?></td>
                                    <td><?php echo $data['StoreHoliday']['description']; ?></td>
                                    <td><?php echo $this->Dateform->us_format($data['StoreHoliday']['created']); ?></td>             
                                    <td>
                                        <?php $EncryptHolidayID = $this->Encryption->encode($data['StoreHoliday']['id']); ?>
                                        <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqstores', 'action' => 'deleteHoliday', $EncryptHolidayID), array('confirm' => 'Are you sure to delete record?', 'escape' => false)); ?>

                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">
                                    No record available
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div></div>
    </div>
    <div style="clear:both;"></div>
    <hr/>
    <div class="col-lg-11">   
        <div class="table-responsive">
            <span class="blue">(Please enter open and close timing of store)</span> 
            <?php
            echo $this->Form->create('Stores', array('url' => array('controller' => 'hqstores', 'action' => 'updatestoreAvailability'),'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'storeavailable'));
            ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th class="th_checkbox">&nbsp</th>
                        <?php foreach ($daysarr as $day) { ?>
                            <th class="th_checkbox"><?php echo __($day); ?></th>
                        <?php } ?>

                    </tr>
                </thead>

                <tbody class="dyntable">                       
                    <tr>
                        <td>Start Time</td>
                        <?php
                        if (!empty($availabilityInfo)) {
                            foreach ($availabilityInfo as $key => $data) {
                                ?>
                                <td><?php echo $this->Form->input('StoreAvailability.' . $key . '.start_time', array('options' => $timeOptions, 'value' => $data['StoreAvailability']['start_time'], 'class' => 'passwrd-input hours', 'div' => false)); ?></td>

                                <?php
                                //$data['StoreAvailability']['start_time'];
                                echo $this->Form->input('StoreAvailability.' . $key . '.id', array('type' => 'hidden', 'value' => $data['StoreAvailability']['id']));
                            }
                        } else {
                            $i = 1;
                            foreach ($daysarr as $day) {
                                ?>
                                <td><?php echo $this->Form->input('StoreAvailability.' . $i . '.start_time', array('options' => $timeOptions, 'class' => 'passwrd-input hours', 'div' => false)); ?></td>

                                <?php
                                echo $this->Form->input('StoreAvailability.' . $i . '.day_name', array('type' => 'hidden', 'value' => $day));


                                $i++;
                            }
                        }
                        ?>
                    </tr>

                    <tr>
                        <td>End Time</td>
                        <?php
                        if (!empty($availabilityInfo)) {
                            foreach ($availabilityInfo as $key => $data) {
                                ?>
                                <td><?php echo $this->Form->input('StoreAvailability.' . $key . '.end_time', array('options' => $timeOptions, 'value' => $data['StoreAvailability']['end_time'], 'class' => 'passwrd-input hours', 'div' => false)); ?></td>                           

                                <?php
                                //$data['StoreAvailability']['start_time'];
                                echo $this->Form->input('StoreAvailability.' . $key . '.id', array('type' => 'hidden', 'value' => $data['StoreAvailability']['id']));
                            }
                        } else {
                            $i = 1;
                            foreach ($daysarr as $day) {
                                ?>
                                <td><?php echo $this->Form->input('StoreAvailability.' . $i . '.start_time', array('options' => $timeOptions, 'class' => 'passwrd-input hours', 'div' => false)); ?></td>

                                <?php
                                echo $this->Form->input('StoreAvailability.' . $i . '.day_name', array('type' => 'hidden', 'value' => $day));


                                $i++;
                            }
                        }
                        ?>
                    </tr>

                    <tr>
                        <td>Weekly Closed Day</td>
                        <?php
                        if (!empty($availabilityInfo)) {
                            foreach ($availabilityInfo as $key => $data) {
                                ?>


                                <td><?php
                                    $checked = "";
                                    if ($data['StoreAvailability']['is_closed']) {
                                        $checked = "checked";
                                    }
                                    echo $this->Form->input('StoreAvailability.' . $key . '.is_closed', array('type' => 'checkbox', 'class' => 'passwrd-input ', 'label' => 'Closed', 'div' => false, 'checked' => $checked));
                                    ?>
                                </td>

                                <?php
                            }
                        } else {
                            $i = 1;
                            foreach ($daysarr as $day) {
                                ?>    
                                <td><?php echo $this->Form->input('StoreAvailability.' . $i . '.is_closed', array('type' => 'checkbox', 'class' => 'passwrd-input ', 'div' => false)); ?></td>
                                <?php
                            }
                        }
                        ?>



                    </tr>


                </tbody>
            </table></div></div>
    <div style="clear:both;"><br/><br/></div>
    <div class="col-lg-7">  
        <div class="form-group">
            <label>Break Time Applicable</label>
            <?php
            $checked = "";
            if ($this->request->data['Store']['is_break_time']) {
                $checked = "checked";
            }
            echo $this->Form->checkbox('Store.is_break_time', array('checked' => $checked));
            ?>

        </div>
    </div>
    <?php
    if ($StoreBreak) {
        $display = "";
    } else {
        $display = "style='display:none;'";
    }
    ?>
    <div class="col-lg-11" id="Breaksection" <?php echo $display; ?>>   
        <div class="table-responsive">
            <span class="blue">(Please enter Break Time Day wise)</span> 
            <table class="table table-bordered table-hover table-striped tablesorter">
                <table class="table table-bordered table-hover table-striped tablesorter">
                    <thead>
                        <tr>
                            <th class="th_checkbox">Breaks</th>
                            <?php foreach ($daysarr as $day) { ?>
                                <th class="th_checkbox"><?php echo __($day); ?></th>
                            <?php } ?>

                        </tr>
                    </thead>
                    <tbody class="dyntable">
                        <tr><td colspan="8">
                                <label>First Break</label>
                                <?php
                                $checked = "";
                                if ($this->request->data['Store']['is_break1']) {
                                    $checked = "checked";
                                }
                                echo $this->Form->checkbox('Store.is_break1', array('checked' => $checked));
                                ?>  
                            </td></tr>
                        <?php
                        if ($StoreBreak1) {
                            $display = "";
                        } else {
                            $display = "style='display:none;'";
                        }
                        ?>
                        <tr class="firstBreak" <?php echo $display; ?>>
                            <td>Start Time</td>
                            <?php
                            if (!empty($availabilityInfo)) {
                                foreach ($availabilityInfo as $key => $data) {
                                    if (!$data['StoreAvailability']['is_closed']) {
                                        ?>
                                        <td><?php echo $this->Form->input('StoreBreak.' . $key . '.break1_start_time', array('options' => $timeOptions, 'value' => $data['StoreBreak']['break1_start_time'], 'class' => 'passwrd-input ', 'div' => false)); ?></td>

                                        <?php
                                    } else {
                                        echo "<td><span class='red'>Closed</span></td>";
                                    }
                                    echo $this->Form->input('StoreBreak.' . $key . '.id', array('type' => 'hidden', 'value' => $data['StoreBreak']['id']));
                                    echo $this->Form->input('StoreBreak.' . $key . '.store_availablity_id', array('type' => 'hidden', 'value' => $data['StoreAvailability']['id']));
                                }
                            } else {
                                $i = 1;
                                foreach ($daysarr as $day) {
                                    ?>
                                    <td><?php
                                        echo $this->Form->input('StoreBreak.' . $i . '.break1_start_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false));

                                        echo $this->Form->input('StoreAvailability.' . $i . '.day_name', array('type' => 'hidden', 'value' => $day));
                                        ?></td>

                                    <?php
                                    $i++;
                                }
                            }
                            ?>                                   
                        </tr>

                        <tr class="firstBreak" <?php echo $display; ?>>
                            <td>End Time</td>
                            <?php
                            if (!empty($availabilityInfo)) {
                                foreach ($availabilityInfo as $key => $data) {
                                    if (!$data['StoreAvailability']['is_closed']) {
                                        ?>
                                        <td><?php echo $this->Form->input('StoreBreak.' . $key . '.break1_end_time', array('options' => $timeOptions, 'value' => $data['StoreBreak']['break1_end_time'], 'class' => 'passwrd-input ', 'div' => false)); ?></td>                           

                                        <?php
                                    } else {
                                        echo "<td><span class='red'>Closed</span></td>";
                                    }
                                    echo $this->Form->input('StoreBreak.' . $key . '.id', array('type' => 'hidden', 'value' => $data['StoreBreak']['id']));
                                    echo $this->Form->input('StoreBreak.' . $key . '.store_availablity_id', array('type' => 'hidden', 'value' => $data['StoreAvailability']['id']));
                                }
                            } else {
                                $i = 1;
                                foreach ($daysarr as $day) {
                                    ?>
                                    <td><?php
                                        echo $this->Form->input('StoreBreak.' . $i . '.break2_end_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false));

                                        echo $this->Form->input('StoreAvailability.' . $i . '.day_name', array('type' => 'hidden', 'value' => $day));
                                        ?></td>

                                    <?php
                                    $i++;
                                }
                            }
                            ?>

                        </tr>

                        <tr><td colspan="8">
                                <label>Second Break</label>
                                <?php
                                $checked = "";
                                if ($this->request->data['Store']['is_break2']) {
                                    $checked = "checked";
                                }
                                echo $this->Form->checkbox('Store.is_break2', array('checked' => $checked));
                                ?>  
                            </td></tr>
                        <?php
                        if ($StoreBreak2) {
                            $display = "";
                        } else {
                            $display = "style='display:none;'";
                        }
                        ?>
                        <tr class="secondBreak" <?php echo $display; ?>>
                            <td>Start Time</td>
                            <?php
                            if (!empty($availabilityInfo)) {
                                foreach ($availabilityInfo as $key => $data) {
                                    if (!$data['StoreAvailability']['is_closed']) {
                                        ?>
                                        <td><?php echo $this->Form->input('StoreBreak.' . $key . '.break2_start_time', array('options' => $timeOptions, 'value' => $data['StoreBreak']['break2_start_time'], 'class' => 'passwrd-input ', 'div' => false)); ?></td>

                                        <?php
                                    } else {
                                        echo "<td><span class='red'>Closed</span></td>";
                                    }
                                    echo $this->Form->input('StoreBreak.' . $key . '.id', array('type' => 'hidden', 'value' => $data['StoreBreak']['id']));
                                    echo $this->Form->input('StoreBreak.' . $key . '.store_availablity_id', array('type' => 'hidden', 'value' => $data['StoreAvailability']['id']));
                                }
                            } else {
                                $i = 1;
                                foreach ($daysarr as $day) {
                                    ?>
                                    <td><?php
                                        echo $this->Form->input('StoreBreak.' . $i . '.break2_start_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false));

                                        echo $this->Form->input('StoreAvailability.' . $i . '.day_name', array('type' => 'hidden', 'value' => $day));
                                        ?>

                                    </td>

                                    <?php
                                    $i++;
                                }
                            }
                            ?>                                   
                        </tr>

                        <tr class="secondBreak" <?php echo $display; ?>>
                            <td>End Time</td>
                            <?php
                            if (!empty($availabilityInfo)) {
                                foreach ($availabilityInfo as $key => $data) {
                                    if (!$data['StoreAvailability']['is_closed']) {
                                        ?>
                                        <td><?php echo $this->Form->input('StoreBreak.' . $key . '.break2_end_time', array('options' => $timeOptions, 'value' => $data['StoreBreak']['break2_end_time'], 'class' => 'passwrd-input ', 'div' => false)); ?></td>                           

                                        <?php
                                    } else {
                                        echo "<td><span class='red'>Closed</span></td>";
                                    }
                                    echo $this->Form->input('StoreBreak.' . $key . '.id', array('type' => 'hidden', 'value' => $data['StoreBreak']['id']));
                                    echo $this->Form->input('StoreBreak.' . $key . '.store_availablity_id', array('type' => 'hidden', 'value' => $data['StoreAvailability']['id']));
                                }
                            } else {
                                $i = 1;
                                foreach ($daysarr as $day) {
                                    ?>
                                    <td><?php
                                        echo $this->Form->input('StoreBreak.' . $i . '.break2_end_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false));

                                        echo $this->Form->input('StoreAvailability.' . $i . '.day_name', array('type' => 'hidden', 'value' => $day));
                                        ?></td>

                                    <?php
                                    $i++;
                                }
                            }
                            ?>

                        </tr>   


                    </tbody>              

                </table>
        </div>
    </div>
    <div style="clear:both;"><br/></div>


    <div class="form-group form_spacing col-lg-6">
        <label>Store Close Details</label> 
        <?php
        echo $this->Form->input('Store.close_details', array('type' => 'textarea', 'rows' => '5', 'cols' => '5', 'class' => 'form-control', 'Placeholder' => 'Enter Details'));
        ?>
    </div> 


    <hr/>
    <div class="col-lg-11">
        <?php
        echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
        echo "&nbsp;";
        echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hqstores/index'", 'class' => 'btn btn-default'));
        echo $this->Form->end();
        ?>
    </div>

</div>
<script>
    $(document).ready(function () {
        $(".hours").change(function (event) {
        });
        $('#StoreHolidayHolidayDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime($this->Hq->storeTimezone(null, date("Y-m-d H:i:s"), null, $store_id))); ?>",
        });
        $("#storeclosed").validate({
            rules: {
                "data[StoreHoliday][holiday_date]": {
                    required: true,
                },
            },
            messages: {
                "data[StoreHoliday][holiday_date]": {
                    required: "Please select date",
                },
            }
        });
        $("#StoreIsBreak1").change(function () {
            if ($(this).is(":checked")) {
                $(".firstBreak").show();
            } else {
                $(".firstBreak").hide();
            }
        });
        $("#StoreIsBreak2").change(function () {
            if ($(this).is(":checked")) {
                $(".secondBreak").show();
            } else {
                $(".secondBreak").hide();
            }
        });
        $("#StoreIsBreakTime").change(function () {
            if ($(this).is(":checked")) {
                $("#Breaksection").show();
            } else {
                $("#Breaksection").hide();
            }
        });
    });
</script>
