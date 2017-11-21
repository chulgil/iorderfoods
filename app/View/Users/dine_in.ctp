<?php 
if ($store) {
            $DineadvanceDay=$store['Store']['calendar_limit']-1 + $store['Store']['dineinblackout_limit'];                  
            $datetoConvert=explode('-',$currentDateVar);
            $datetoConvert=$datetoConvert[2].'-'.$datetoConvert[0].'-'.$datetoConvert[1];  
            $dinemaxdate=date('m-d-Y', strtotime($datetoConvert . ' +'.$DineadvanceDay.' day'));
            $currentDateVar=date('m-d-Y', strtotime($datetoConvert . ' +'.$store['Store']['dineinblackout_limit'].' day'));
}
    ?>

<div class="content single-frame">
    <div class="wrap">
        <?php //echo $this->Session->flash(); ?>
        <?php echo $this->Form->create('Users', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'UsersRegistration')); ?>
        <div class="clearfix">
            <section class="form-layout sign-up">
                <h2>
                    <?php
                    if ($store) {
                        echo "<span class='store_name'>" . $store['Store']['store_name'] . "</span>";
                    } else {
                        echo 'Dine-In Reservation';
                    }
                    ?>
                </h2>
                <ul class="">
                    <li>
                        <span class="title"><label>Person <em>*</em></label></span>
                        <div class="title-box"><?php
                        echo $this->Form->input('Booking.number_person', array('type' => 'select', 'class' => 'inbox', 	'options' => $number_person, 'label' => false, 'div' => false));
								
                        echo $this->Form->error('Booking.number_person');
							?>
                        </div>
					</li>

                    <li>
                        <span class="title"><label>Reservation Date <em>*</em></label></span>
                        <div class="title-box"><?php 
			echo $this->Form->input('Booking.start_date', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Reservation Date', 'label' => false, 'div' => false,'readOnly'=>true,'value'=>$currentDateVar));
			echo $this->Form->error('Booking.start_date');
                    		?>
                        </div>
                    </li>
                    <li>
                        <span class="title"><label>Reservation Time<em>*</em></label></span>
                        <div class="title-box"><span id="resvTime">
                                <?php
                                if (empty($time_range)) {
                                    echo $this->Form->input('Booking.start_time', array('type' => 'select', 'class' => 'inbox', 'empty' => 'Store is closed on this day', 'options' => $time_range, 'label' => false, 'div' => false));
                                } else { ?>
                                <select id="BookingStartTime" class="inbox" name="data[Booking][start_time]">
                                    <?php foreach($time_range as $key=>$value) {
					$flag=true;
					foreach($storeBreak as $breakKey=>$breakVlue){                        
					    if(strtotime($storeBreak[$breakKey]['start']) <= strtotime($key) && strtotime($storeBreak[$breakKey]['end']) >= strtotime($key)){
						echo "<option value='$key' disabled='disabled'>$value - Break Time </option>";
						$flag=false;
					    }
					}
					if($flag){
					    echo "<option value='$key'>$value</option>";     
					}
                                    }   ?>
                                </select>
                                <?php }
                                ?>
                            </span>	
                            <?php echo $this->Form->error('Booking.start_time'); ?></div></li>

                    <li>
                        <span class="title"><label>Special Request </label></span>
                        <div class="title-box">
				<?php echo $this->Form->input('Booking.special_request', array('type' => 'textarea', 'class' => 'inbox', 'placeholder' => 'Enter Special Request', 'maxlength' => '50', 'label' => false, 'div' => false));
                            	echo $this->Form->error('Booking.special_request');
                            ?>
			</div>
		    </li>
		    
		    <li>
			<div class="radio-btn space20 delivery-address-option" >
                    <?php if(isset($store['Store']) && !empty($store['Store']['dine_in_description'])){ ?> 
			    <label class="common-bold common-size" for="other"><span></span><i class="fa fa-caret-down"></i> Detail</label><br/>
			    <div style="font-size:14px;float:left;"> 
				<?php echo $store['Store']['dine_in_description']; ?>
			    </div>
                    <?php } ?>        
			</div>
		    </li>
		    
                </ul>
		
                
                <div class="button">
                	<?php
			    echo $this->Form->button('Request', array('type' => 'submit', 'class' => 'btn green-btn'));
			    echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/customerDashboard/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'btn green-btn'));
			?>
		</div>
            </section>
        </div>
<?php echo $this->Form->end(); ?>
    </div>
</div>
    
<script>
    $(document).ready(function () {
        
        function getTime(date,orderType,preOrder,returnspan){
            var type1 = 'Store';
            var type2 = 'pickup_time';
            var type3 = 'StorePickupTime';
            var storeId = '<?php echo $encrypted_storeId; ?>';
            var merchantId = '<?php echo $encrypted_merchantId; ?>';
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3,orderType:orderType,preOrder:preOrder},
                success: function (result) {
                    $('#'+returnspan).html(result);
                }
            });
        }
        
        $('#BookingStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: '<?php echo $currentDateVar; ?>',
           maxDate: '<?php echo $dinemaxdate; ?>',
           beforeShowDay: function(date) {
                var day = date.getDay();            
                var array ='<?php echo json_encode($closedDay); ?>';
                return [ array.indexOf(day) == -1 ];
            }
        });    
        $(".date-select").datepicker("setDate", '<?php echo $currentDateVar;?>');
        var date = '<?php echo $currentDateVar;?>';
        getTime(date,1,1,'resvTime');
        $('#BookingStartDate').on('change', function () {
            var date = $(this).val();
            var orderType = 1; // 3= Dine-in/Booking
            var preOrder = 0;
            var type1 = 'Booking';
            var type2 = 'start_time';
            var type3 = 'BookingStartTime';
            var storeId = '<?php echo $encrypted_storeId; ?>';
            var merchantId = '<?php echo $encrypted_merchantId; ?>';
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3,orderType:orderType,preOrder:preOrder},
                success: function (result) {
                    $('#resvTime').html(result);
                }
            });
        });

        $("#UsersRegistration").validate({
            rules: {
                "data[Booking][start_date]": {
                    required: true,
                }, "data[Booking][start_time]": {
                    required: true,
                }
            },
            messages: {
                "data[Booking][start_date]": {
                    required: "Please select booking date",
                }, "data[Booking][start_time]": {
                    required: "Please select booking time",
                }
            }
        });
    });
</script>