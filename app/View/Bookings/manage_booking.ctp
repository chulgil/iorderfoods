<div class="row">
    <div class="col-lg-13">
        <h3>Dine-In</h3>
        <br>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
<!--            <div class="col-lg-3">		     
                Booking Request Id : <?php echo $list[0]['Booking']['id']; ?>	    
            </div>-->

            <div class="row padding_btm_20">
                <div class="col-lg-2">		     
                </div>



                <div class="col-lg-4">		     
                </div>



                <div class="col-lg-2">		 
                </div>
                <div class="col-lg-2">		  

                </div>
            </div>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
<!--                        <th  class="th_checkbox">Request Id</th>-->
                        <th  class="th_checkbox">Customer Name</th>
                        <th  class="th_checkbox">Special Request</th>
                        <th  class="th_checkbox">Email</th> 
                        <th  class="th_checkbox">Persons</th>
                        <th  class="th_checkbox">Date</th>
<!--                        <th  class="th_checkbox">Time</th>-->
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    //if($list){
                    // $i = 0;			
                    //foreach($list as $key => $data){
                    //$class = ($i%2 == 0) ? ' class="active"' : '';
                    //$EncryptOrderID=$this->Encryption->encode($data['Booking']['id']); 
                    ?>
                    <tr>	    
<!--                        <td>
                            <?php //echo $list[0]['Booking']['id']; ?>	
                        </td>-->
                        <td><?php echo $list[0]['User']['fname'] . " " . $list[0]['User']['lname']; ?></td>
                        <td><?php echo $list[0]['Booking']['special_request']; ?>	</td>
                        <td>
                            <?php echo $list[0]['User']['email']; ?></td>
                        <td><?php echo $list[0]['Booking']['number_person']; ?></td>
                        <td>
                            
                            <?php
//                            $date = explode(" ", $this->Common->storeTimezone('',$list[0]['Booking']['reservation_date']));
//                           echo $this->Dateform->us_format($date[0]);
                           
                           echo $this->Common->storeTimeFormateUser($list[0]['Booking']['reservation_date'],true);
                            ?>
                        
                        </td>

<!--                        <td><?php //echo $date[1]; ?></td>-->

                    </tr>

                </tbody>
            </table><br><br>
            <div class="col-lg-6">
                <div class="form-group form_spacing">
                    <?php
                    echo $this->Form->create('Booking', array('action' => 'manageBooking', 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'storeclosed'));
                    ?>
                    <label>To<span class="required"> * </span></label>  
                    <?php
                    echo $this->Form->input('Data.to', array('readonly' => 'readonly', 'type' => 'text', 'class' => 'form-control', 'value' => @$list[0]['User']['email'], 'div' => false));
                    echo $this->Form->input('Data.id', array('type' => 'hidden', 'value' => @$list[0]['Booking']['id']));

                    echo $this->Form->input('Data.name', array('type' => 'hidden', 'value' => @$list[0]['User']['fname'] . " " . @$list[0]['User']['lname']));
                     echo $this->Form->input('Data.special_request', array('type' => 'hidden', 'value' => @$list[0]['Booking']['special_request']));
                    echo $this->Form->input('Data.emailnotify', array('type' => 'hidden', 'value' => @$list[0]['User']['is_emailnotification']));
                    echo $this->Form->input('Data.code', array('type' => 'hidden', 'value' => @$list[0]['User']['CountryCode']['code']));

                    echo $this->Form->input('Data.smsnotify', array('type' => 'hidden', 'value' => @$list[0]['User']['is_smsnotification']));
                    if (!empty($list[0]['User']['DeliveryAddress']['phone'])) {
                        echo $this->Form->input('Data.phone', array('type' => 'hidden', 'value' => @$list[0]['User']['DeliveryAddress']['phone']));
                    } else {

                        echo $this->Form->input('Data.phone', array('type' => 'hidden', 'value' => @$list[0]['User']['phone']));
                    }
                    echo $this->Form->input('Data.ordercode', array('type' => 'hidden', 'value' => @$list[0]['Booking']['id']));
                    echo $this->Form->input('Data.number', array('type' => 'hidden', 'value' => @$list[0]['Booking']['number_person']));
                    echo $this->Form->input('Data.datetime', array('type' => 'hidden', 'value' => @$list[0]['Booking']['reservation_date']));
                    ?>
                </div>
                <div class="form-group form_spacing">
                    <label>Booking Status</label> 

<?php echo $this->Form->input('BookingStatus.name', array('default' => @$list[0]['Booking']['booking_status_id'], 'type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $statusList)); ?>	

                </div>  
                <div class="form-group form_spacing">
                    <label>Comments</label> 
<?php
echo $this->Form->input('Data.comment', array('type' => 'textarea', 'rows' => '5', 'cols' => '5', 'class' => 'form-control'));
?>
                </div>    
                <div class="form-group form_spacing">
<?php
echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
echo "&nbsp;";
echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/bookings/index'", 'class' => 'btn btn-default'));
echo $this->Form->end();
?>
                </div>
            </div>



        </div>
    </div>
</div>
<?php echo $this->Html->css('pagination'); ?>

<script>
    $(document).ready(function () {
        $("#OrderStatusId").change(function () {
            // var catgoryId=$("#OrderOrderStatusId").val();
            $("#AdminId").submit();
        });

        $("#SegmentId").change(function () {
            //var catgoryId=$("#OrderSeqmentId").val();
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

        var statusId = $("#OrderOrderStatusId").val();

        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select one order to proceed.');
            // cancel submit
            return false;
        }
        if (statusId == '') {
            alert('Please select status.');
            return false;
        }


    }


</script>