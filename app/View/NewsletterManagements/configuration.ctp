<?php echo $this->Html->script('jquery.maskedinput');?>
<style>
	#map{ width: 600px; height: 200px; border-style: dotted solid; }
	@media (max-width:860px) {
		#map{ width: 100%; }
	}
</style>
<?php
$latitude = $this->request->data['Store']['latitude'];
$logitude = $this->request->data['Store']['logitude'];
?>
<div class="row">
            <div class="col-lg-6">
                <h3>Manage Store Configuration Details</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
</div>   
   

<div class="row">              
            <?php             
                  echo $this->Form->create('Stores', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'StoreConfiguration','enctype'=>'multipart/form-data'));
            ?>
<div class="col-lg-6">
            <div class="form-group form_spacing">            
                  <label>Store Name</label>
            <?php
                  echo $this->Form->input('Store.store_name',array('type'=>'text','class'=>'form-control','Placeholder'=>'Enter Store Name'));
            ?>
            </div>
    
	    <div class="form-group form_spacing">
            <?php
                  echo $this->Form->input('User.role_id',array('type'=>'hidden','value'=>$roleid));
                  echo $this->Form->input('Store.id',array('type'=>'hidden','value'=>$storeId));
                  echo $this->Form->input('User.id',array('type'=>'hidden','value'=>$userid));
		 
            ?>
                  <label>Address</label>
            <?php
                  echo $this->Form->input('Store.address',array('type'=>'textarea','rows' => '5', 'cols' => '5','class'=>'form-control','Placeholder'=>'Enter Address'));
            ?>
            </div>
            <div class="form-group form_margin">
                <label>City</label>
            <?php
                  echo $this->Form->input('Store.city',array('type'=>'text','class'=>'form-control','Placeholder'=>'Enter City'));
                  
            ?>
            </div>
            <div class="form-group form_margin">
                <label>State</label>
            <?php
                  echo $this->Form->input('Store.state',array('type'=>'text','class'=>'form-control','Placeholder'=>'Enter State'));
            ?>
            </div>
            <div class="form-group form_margin">
                <label>Zipcode</label>
            <?php
                  echo $this->Form->input('Store.zipcode',array('type'=>'text','class'=>'form-control','Placeholder'=>'Enter Zipcode', 'maxlength' => '5'));
            ?>
            </div>
            
            <div class="form-group form_margin">
                <label>Phone No.</label>
            <?php
                  echo $this->Form->input('Store.phone',array('type'=>'text','class'=>'form-control','Placeholder'=>'Enter Contact Number','data-mask'=>'mobileNo'));
            ?>
                <span class="blue">(eg. 111-111-1111)</span> 
            </div>
            
<?php if($latitude && $logitude){ ?>
            <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
        
        </style>
        <div id="map"></div>
        <script>
            var origin1 = new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $logitude; ?>);
           
            var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: origin1,
            mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            
         var marker = new google.maps.Marker({
	 
	 position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $logitude; ?>),
	 map: map,
         //draggable: true,
	 icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=S|f6f6f6',
	 });
         </script>
        <?php } ?>
            <div>
            <hr/>
            <span class="blue">(NZ Gateway Configuration details)</span> 
            </div>
            
            <div class="form-group form_margin">
                <label>NZ Gateway Login ID</label>
            <?php
                  echo $this->Form->input('Store.api_username',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Authorized.net Api User Name'));
            ?>
            </div>
	    
	    <div class="form-group form_margin">
                <label>NZ Gateway Password</label>
            <?php
                  echo $this->Form->input('Store.api_key',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Authorized.net Api Key'));
            ?>
            </div>
	    <!--
            <div class="form-group form_margin">
                <label>Authorized.net api password</label>
            <?php                       
                  echo $this->Form->input('Store.api_password',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Authorized.net Api Password'));
            ?>
            </div>           
	    -->
            <div>
            <hr/>
            <span class="blue">(Paypal Configuration details)</span> 
            </div>
            <div class="form-group form_margin">
                <label>Paypal Email</label>
            <?php
                  echo $this->Form->input('Store.paypal_email',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Paypal Email'));
            ?>
            </div>
            <div class="form-group form_margin">
                <label>Paypal Password</label>
            <?php
                  echo $this->Form->input('Store.paypal_password',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Paypal Password'));
            ?>
            </div>
            <div class="form-group form_margin">
                <label>Paypal Signature</label>
            <?php      
                  
                  echo $this->Form->input('Store.paypal_signature',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Paypal Signature'));
            ?>
            </div>           
            
           
            <div>
                
            <hr/>            
            </div>
            <div class="form-group form_margin">
                <label>Printer IP</label>
            <?php
                  echo $this->Form->input('Store.printer_location',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Printer IP'));
            ?>
             <span class="blue">(eg. "192.168.0.251")</span>
	     
	     <div class="form-group form_margin">
                <label>Fax Number</label>
            <?php
                  echo $this->Form->input('Store.fax_number',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Fax Number'));
            ?>
             <span class="blue">(eg. "+11552534942")</span>
            </div>
            
            <div>
            <hr/>
            <span class="blue">(Twilio Configuration details)</span> 
            </div>
            <div class="form-group form_margin">
                <label>Twilio Sms Gateway Number</label>
            <?php
                  echo $this->Form->input('Store.twilio_number',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Twilio Sms Gateway Number'));
            ?>
            </div>
            <div class="form-group form_margin">
                <label>Twilio api Key</label>
            <?php
                  echo $this->Form->input('Store.twilio_api_key',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Twilio Api Key'));
            ?>
            </div>
            <div class="form-group form_margin">
                <label>Twilio api token</label>
            <?php      
                  
                  echo $this->Form->input('Store.twilio_api_token',array('type'=>'input','class'=>'form-control','Placeholder'=>'Enter Twilio Api Token'));
            ?>
            </div>
            <div>
            <hr/>
            </div>
            
            
            
            <div class="form-group form_spacing">
                <table cellpadding="6">
                    <thead>
                        <tr>                            
                            <th>
                                <label>Delivery</label>
                                <?php
                                    $checked="";
                                    if($this->request->data['Store']['is_delivery']){
                                      $checked="checked";
                                    }                 
                                    echo $this->Form->checkbox('Store.is_delivery',array('checked'=>$checked));
                                ?> 
                            </th>
                            <th>
                                <label>Pickup</label>
                                <?php
                                    $checked="";
                                    if($this->request->data['Store']['is_take_away']){
                                      $checked="checked";
                                    }                 
                                    echo $this->Form->checkbox('Store.is_take_away',array('checked'=>$checked));
                                ?> 
                                
                            </th>
                            <th>
                                <label>Dine-in</label>
                                <?php
                                    $checked="";
                                    if($this->request->data['Store']['is_booking_open']){
                                      $checked="checked";
                                    }                 
                                    echo $this->Form->checkbox('Store.is_booking_open',array('checked'=>$checked));
                                ?>                                 
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <tr>
                            <td>
                                <label>Min. amount($)</label>
                                <?php
                                    echo $this->Form->input('Store.minimum_order_price',array('type'=>'text','class'=>'form-control','style'=>"width:120px;"));
                                ?>
                            </td>
                            <td>
                                <label>Min. amount($)</label>
                                <?php
                                    echo $this->Form->input('Store.minimum_takeaway_price',array('type'=>'text','class'=>'form-control','style'=>"width:120px;"));
                                ?>                         
                                
                            </td>
                            <td>&nbsp;</td>
                        </tr>                        
                    </tbody>
                    
                </table>
                
                
                
                
                <!--<label>Minimum Order amount($)</label>-->
            <?php      
                  
                  //echo $this->Form->input('Store.minimum_order_price',array('type'=>'text','class'=>'form-control'));
            ?>            
                <!--<br/><label>Is Booking Open</label>-->
            <?php
                  //$checked="";
                  //if($this->request->data['Store']['is_booking_open']){
                  //  $checked="checked";
                  //}                 
                 // echo $this->Form->checkbox('Store.is_booking_open',array('checked'=>$checked));
            ?>
            </div>
            
	    <hr/>   
	    <div class="form-group form_margin">
                <label>Notification Email</label>
            <?php 
                  echo $this->Form->input('Store.notification_email',array('type'=>'text','class'=>'form-control'));
            ?>
	    <span class="blue">(Store admin will get Notifications on this email)</span> 
            </div>
            <div class="form-group form_margin">
                <label>Notification Phone no.</label>
            <?php 
                  echo $this->Form->input('Store.notification_number',array('data-mask'=>'mobileNo','type'=>'text','class'=>'form-control phone_number'));
            ?>
	    <span class="blue">(Store admin will get Notifications on this phone number eg. 111-111-1111)</span> 
            </div>
	    
	    <div class="form-group form_margin">
                <label>Notification Type</label>
		    <?php		    
		    $options=array('1'=>'Email Notification','2'=>'Text Message Notification','3'=>'Both Text & Email');
		    echo $this->Form->input('Store.notification_type',array('type'=>'select','class'=>'form-control','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$options,'empty'=>false)); ?>		
	    </div>
	    
	    
	     <div class="form-group form_margin">
                <label>Kitchen Dashboard Display</label>
		    <?php		    
		    $options=array('1'=>'List','2'=>'Grid/Post-it');
		    echo $this->Form->input('Store.kitchen_dashboard_type',array('type'=>'select','class'=>'form-control','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$options,'empty'=>false)); ?>		
	    </div>
	    
	    
            <hr/>            
            <div class="form-group form_margin">
                <label>Delivery fee($)</label>
            <?php      
                  
                  echo $this->Form->input('Store.delivery_fee',array('type'=>'text','class'=>'form-control'));
            ?>
            </div>
            <div class="form-group form_margin">
                <label>Service fee($)</label>
            <?php      
                  
                  echo $this->Form->input('Store.service_fee',array('type'=>'text','class'=>'form-control'));
            ?>
            </div>
            
	    
	    <div>
	    <hr/>
	    <span class="blue">(Tax Configuration details)</span> 
            </div>
	     <div class="form-group form_spacing">
                <table cellpadding="6">
                    <thead>
                        <tr>                            
                            <th style="width:90px;">
                                <label>Tax 1 (%)</label>
                            </th>
                            <th style="width:90px;">
                                <label>Tax 2 (%)</label> 
                            </th>
                            <th style="width:90px;">
                                <label>Tax 3 (%)</label>                                                             
                            </th>
			    <th style="width:90px;">
                                <label>Tax 4 (%)</label>                                                             
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <tr>
			<?php foreach($this->request->data['StoreTax'] as $key =>$value){				   
				    ?>
			    <td>                                
                                <?php
				    echo $this->Form->input('StoreTax.'.$key.'.id',array('type'=>'hidden','value'=>$value['StoreTax']['id']));
                                    echo $this->Form->input('StoreTax.'.$key.'.tax_value',array('type'=>'text','class'=>'form-control','style'=>"width:80px;",'value'=>$value['StoreTax']['tax_value']));
                                ?>
                            </td>
			<?php } ?>                           
                        </tr>                        
                    </tbody>
                    
                </table>
		<span class="blue">(Enter Tax in the % format. i.e 8%)</span>
            </div>
	     
	    
	    <hr/>  
            <div class="form-group form_margin">
                <label>Themes</label>
		    <?php		    
		    //$options=array('1'=>'Brown Theme');
		    echo $this->Form->input('Store.store_theme_id',array('type'=>'select','class'=>'form-control','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$themeOptions,'empty'=>false)); ?>		
	    </div>
	    
	    <div class="form-group form_margin">
                <label>Fonts</label>
		    <?php		    
		    
		    echo $this->Form->input('Store.store_font_id',array('type'=>'select','class'=>'form-control','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$fontOptions,'empty'=>false)); ?>		
	    </div>
            
             
            <div class="form-group form_spacing">
                <div style="float:left;"> 
                    <label>Background Image</label>             
                    <?php
                       echo $this->Form->input('Store.back_image',array('type'=>'file','label'=>'','div'=>false,'class'=>'form-control','style'=>"box-sizing:initial;"));
                       echo $this->Form->error('Store.background_image');
                    ?>
                     
		</div>
                
		<?php
		$EncryptStoreID=$this->Encryption->encode($this->request->data['Store']['id']);
		?>
		<div style="float:right;">
		<?php
		if($this->request->data['Store']['background_image']){
			echo $this->Html->image('/storeBackground-Image/'.$this->request->data['Store']['background_image'],array('alt' => 'Item Image','height'=>150,'width'=>150,'style'=>'border:1px solid #000000;margin:5px 0px 5px 5px;','title'=>'Item Image'));
			echo $this->Html->link("X",array('controller' => 'Stores', 'action' => 'deleteStoreBackgroundPhoto', $EncryptStoreID),array('confirm' => 'Are you sure to delete Background Photo?','title'=>'Delete Photo','style'=>'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
		}
		?>  
               
            
            </div>
            
	    </div>
	    <div style="clear:both;"><br/></div>
	    
	    <div class="form-group form_spacing">
                <div style="float:left;"> 
                    <label>Store Logo</label>             
                    <?php
                       echo $this->Form->input('Store.store_logophoto',array('type'=>'file','label'=>'','div'=>false,'class'=>'form-control','style'=>"box-sizing:initial;"));
                       echo $this->Form->error('Store.store_logophoto');
                    ?>
                     
		</div>
                
		<?php
		$EncryptStoreID=$this->Encryption->encode($this->request->data['Store']['id']);
		?>
		<div style="float:right;">
		<?php
		if($this->request->data['Store']['store_logo']){
			echo $this->Html->image('/storeLogo/'.$this->request->data['Store']['store_logo'],array('alt' => 'Item Image','height'=>150,'width'=>150,'style'=>'border:1px solid #000000;margin:5px 0px 5px 5px;','title'=>'Store Logo'));
			echo $this->Html->link("X",array('controller' => 'Stores', 'action' => 'deleteStoreLogo', $EncryptStoreID),array('confirm' => 'Are you sure to delete Store Logo?','title'=>'Delete Logo','style'=>'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
		}
		?>  
               
            
	    </div>
            </div>            
	    <div style="clear:both;"><br/></div>
	    <div class="form-group form_spacing">
	    <label>Display Store Logo</label>
	    <?php
		$checked="";
		if($this->request->data['Store']['is_store_logo']==2){
		  $checked="checked";
		}                 
		echo $this->Form->checkbox('Store.is_store_logo',array('checked'=>$checked));
	    ?>
	    </div>
            
            <div style="clear:both;"><br/></div>
	    <div class="form-group form_spacing">
	    <label>Hide Store Photos</label>
	    <?php
		$checked="";
		if($this->request->data['Store']['is_not_photo']==1){
		  $checked="checked";
		}                 
		echo $this->Form->checkbox('Store.is_not_photo',array('checked'=>$checked));
	    ?>
	    </div>
	    
            <div class="form-group form_margin">
            <?php
                  
                  echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default')); echo "&nbsp;";
                  echo $this->Form->button('Cancel', array('type' => 'button','onclick'=>"window.location.href='/stores/dashboard'",'class' => 'btn btn-default'));
            ?>
            </div>            
            <?php
                  echo $this->Form->end();?>
               
</div>
<script>
  
$(document).ready(function(){
   
    $(".phone_number").keypress(function (e) {
	    //if the letter is not digit then display error and don't type anything
	    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {	       
		      return false;
	    }
    });
        

    $("#StoreConfiguration").validate({
	     rules: {
                "data[Store][phone]": {
                     required: true,
                     minlength: 14,
		     maxlength: 14,
                    
                },
		  },
            messages: {
                "data[Store][phone]": {
                required: "Contact number required",
                minlength: "Number must be at 10 characters"
                },
	    }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999"); 

});

  
</script>


   