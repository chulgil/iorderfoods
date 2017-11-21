<div class="row">
        <div class="col-lg-10">
	
	<br><br>
	<div>

  <!-- Nav tabs -->
   <table class="table table-bordered table-hover table-striped tablesorter" style="width:50%;"><thead>
	<tr>
		<th  class="th_checkbox"><a href="#personal" aria-controls="home" role="tab" data-toggle="tab">Personal Information</a></th>
		<th  class="th_checkbox"><a href="#order" aria-controls="profile" role="tab" data-toggle="tab">Orders</a></th>
		<th  class="th_checkbox"><a href="#review" aria-controls="messages" role="tab" data-toggle="tab">Reviews</a></th>
		<th  class="th_checkbox"><a href="#reservation" aria-controls="settings" role="tab" data-toggle="tab">Reservation</a></th>
		</tr></thead>
  </table>

  <!-- Tab panes -->
  <br>
  <div class="tab-content">
	
	 <!--******************Personal information area start here***********************-->
	
    <div role="tabpanel" class="tab-pane active" id="personal">
	
	 <h3>Personal Information : <?php
	  if(!empty($orderDetail)){
	 echo ucfirst($orderDetail[0]['User']['fname']." ".$orderDetail[0]['User']['lname']) ;
	 }
	 ?></h3>
	
	  <table class="table table-bordered table-hover table-striped tablesorter">
		
		
		<thead>
		     <tr>	    
			<th  class="th_checkbox">Name</th>
			<th  class="th_checkbox">Email</th>
                        <th  class="th_checkbox">Contact No.</th>
			<th  class="th_checkbox">Address</th>

		     </tr>
		</thead>
	       
	       <tbody class="dyntable">
		 <?php
		         $i = 0;			
			$class = ($i%2 == 0) ? ' class="active"' : '';
			
		    ?>
		     <tr >	    
			<td>
			 <?php
	  if(!empty($orderDetail[0]['User']['fname'])){
	 echo $orderDetail[0]['User']['fname']." ".$orderDetail[0]['User']['lname'] ;
	 }else{
		echo "NA";
	 }
	 ?>
	
			</td>
			
			<td>
			 <?php
	  if(!empty($orderDetail[0]['User']['email'])){
	 echo $orderDetail[0]['User']['email'] ;
	 }else{
		echo "NA";
	 }
	 ?>
			</td>
			<td>
			 <?php
	  if(!empty($orderDetail[0]['User']['phone'])){
	 echo $orderDetail[0]['User']['phone'] ;
	 }else{
		echo "NA";
	 }
	 ?>
			</td>
			
			<td style="width: 300px; word-wrap: break-word; word-break: break-all;">
			 <?php
	  if(!empty($orderDetail[0]['User']['address'])){
	 echo $orderDetail[0]['User']['address'] ;
	 }else{
		echo "NA";
	 }
	 ?>
			</td>
			
         
		     </tr>
		
	       </tbody>
	    </table>
	
	
	
	
	
	
    </div>
    
    <!--******************Personal information area end here***********************-->
    
    
   <!--******************Order details area start here***********************-->
    
    
    <div role="tabpanel" class="tab-pane" id="order">
	 <h3>Order History : <?php
	 $total_amount =0;
	 
	 ?></h3><h4>Total Amount : </h4>
	 <div id="assign" style="margin-top:-30px;margin-left:140px;font-size:16px;">
	 <h4><?php  echo $total_amount ; ?></h4>
	 </div>
	 <br>
	 <?php echo $this->Session->flash();?>
	 <?php
	 if(!empty($orderDetail)){
	 foreach($orderDetail as $k=>$data){
		$total_amount = $total_amount + $data['Order']['amount'];
		
		?>
		
         <div><br><br>
		<table class="table table-bordered table-hover table-striped tablesorter">
			<thead>
				<tr>
					<th class="th_checkbox" colspan="4" style="text-align:left;">
		Order Id  : <?php  echo $data['Order']['order_number'] ;?> | Cost : $<?php  if($data['Order']['coupon_discount'] >0){
			$total_amount =  $data['Order']['amount'] - $data['Order']['coupon_discount'];
			echo $total_amount;
			}else{
				
			echo $total_amount=$data['Order']['amount'];
			}?>
			<?php if($orderDetail[0]['Order']['tax_price']){ 
				echo "| Tax :$".$orderDetail[0]['Order']['tax_price'];
			} ?>
			| Status :
		
		<?php
	$status_name =  $this->requestAction('/hqreports/ajaxRequest/'.$data['Order']['order_status_id'].'');
		 
		echo $status_name;
		?>
		
		
		<br>
		Address : <?php  echo $data['DeliveryAddress']['city']." ".$data['DeliveryAddress']['address'];?>
					</th>
				</tr>
			</thead>
		</table>
	
	    <table class="table table-bordered table-hover table-striped tablesorter">
		
		
		<thead>
		     <tr>	    
			<th  class="th_checkbox">Item</th>
			<th  class="th_checkbox">Size</th>
                        <th  class="th_checkbox">Preference</th>
			<th  class="th_checkbox">Add-ons</th>
			<th  class="th_checkbox">Price($)</th>
			<th  class="th_checkbox">Tax($)</th>
		     </tr>
		</thead>
	       
	       <tbody class="dyntable">
		 <?php
		         $i = 0;$totalItemPrice=0.00;			
			foreach($data['OrderItem'] as $key => $item){
			$class = ($i%2 == 0) ? ' class="active"' : '';
			
		    ?>
		     <tr >	    
			<td>
			
	<?php echo $item['quantity'];?> x <?php echo $item['Item']['name'];?>
				<?php
				if(isset($item['OrderOffer'])){
				    echo "<br>";
				    foreach ($item['OrderOffer'] as $j => $offer){
				       $offeroitem="&nbsp;&nbsp;";
				       if(isset($offer['quantity'])){
					  $offeroitem.=$offer['quantity'];
				       }
				       if(isset($offer['Size']['size'])){
					  $offeroitem.=" ".$offer['Size']['size'];
				       }
				       if($offer['Item']['name']){
					  $offeroitem.="x ".$offer['Item']['name']."<br>";
				       }
				       
				     echo $offeroitem;
				    }
				}
				 ?>
			</td>
			
			<td>
			<?php echo ($item['Size'])?$item['Size']['size']:"-";?>
			</td>
			<td>
			<?php
			
			if(!empty($item['OrderPreference'])) {
			    $preference="";
			    $prefix = '';
			    foreach($item['OrderPreference'] as $key =>$opre){
				    $preference .= $prefix . '' .$opre['SubPreference']['name']."";
				    $prefix = ', ';
			    }
			    echo $preference;
			    
			} else { echo ' - '; } 
			//echo ($item['Type'])?$item['Type']['name']:"-";
			
			
			
			?>	
			</td>
			
			<td style="width: 300px; word-wrap: break-word; word-break: break-all;">
			<?php
			$Toppings='';
				if($item['OrderTopping']){
					$Toppings=array();
					foreach($item['OrderTopping'] as $vkey => $toppingdetails){
						if(isset($toppingdetails['Topping']['name'])){
						$Toppings[]=$toppingdetails['Topping']['name'];
						}
					}
				}
				if($Toppings){
					$alltoppings=implode(',',$Toppings);
					echo wordwrap($alltoppings,5,"<br>\n");
				}else{
					echo "-";
				}
				
			?>	
			</td>
			
			<td>
				<?php echo ($item['total_item_price'])?$item['total_item_price']:"-";
				if($item['total_item_price']){$totalItemPrice=$totalItemPrice+$item['total_item_price'];}
				
				?>
			</td>
			<td>
				<?php echo ($item['tax_price'])?$item['tax_price']:"-";?>
			</td>	
		     </tr>
		<?php $i++; } ?>
		
		
			<tr class="table-net-tr">
		        <td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="net-record-font-size"><?php echo number_format($totalItemPrice,2);?></td>
			<td class="net-record-font-size"><?php echo $orderDetail[0]['Order']['tax_price'];?></td>
		     </tr>
		     <tr class="table-net-tr">
		        <td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="net-record-font-size">Gross amount</td>
			<td class="net-record-font-size"><?php echo number_format($totalItemPrice+$orderDetail[0]['Order']['tax_price'],2);?></td>
		     </tr> 
		     <tr class="table-net-tr">
		        <td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="net-record-font-size">Coupon discount</td>
			<td class="net-record-font-size"><?php echo '-'.($orderDetail[0]['Order']['coupon_discount'])?$orderDetail[0]['Order']['coupon_discount']:'-';?></td>
		     </tr>
		     
		     <tr class="table-net-tr">
		        <td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="net-record-font-size">Service fee</td>
			<td class="net-record-font-size"><?php echo ($orderDetail[0]['Order']['service_amount'])?$orderDetail[0]['Order']['service_amount']:'-';?></td>
		     </tr>
		     <tr class="table-net-tr">
		        <td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="net-record-font-size">Delivery fee</td>
			<td class="net-record-font-size"><?php echo ($orderDetail[0]['Order']['delivery_amount'])?$orderDetail[0]['Order']['delivery_amount']:'-';?></td>
		     </tr>
		     <tr class="table-net-tr">
		        <td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="table-net-td">&nbsp;</td>
			<td class="net-record-font-size">Net payment</td>
			<td class="net-record-font-size"><?php echo $total_amount;?></td>
		     </tr> 
		
		 
	       </tbody>
	    </table>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 </div>
	 <br>
	 
	 <?php   }
	 echo $this->Form->input('total',array('type'=>'hidden','value'=>$total_amount,'id'=>'amt')) ; 
	 }else{
	echo "Record Not Found.";
		
	 }
	 
	 
	 ?>
    </div>
    
     <!--******************Order details area end here***********************-->
     
      <!--******************Review area start here***********************-->
    <div role="tabpanel" class="tab-pane" id="review">
	
	 <h3>Review </h3>
	 <br><br>
	  <table class="table table-bordered table-hover table-striped tablesorter">
		
		
		<thead>
		     <tr>	    
			<th  class="th_checkbox">Review on Item</th>
			<th  class="th_checkbox">Review</th>
                        <th  class="th_checkbox">Rating</th>
			<th  class="th_checkbox">Review Date</th>

		     </tr>
		</thead>
	       
	       <tbody class="dyntable">
		 <?php
		         $i = 0;			
			$class = ($i%2 == 0) ? ' class="active"' : '';
			
		    ?>
		      <?php if(!empty($myReviews)){ foreach($myReviews as $review) { ?>
		     <tr >	    
			<td>
			<?php echo $review['OrderItem']['Item']['name'];?>
			</td>
			<td>
			<?php echo ucfirst($review['StoreReview']['review_comment']);?>
			</td>
			<td>
			<input disabled="disabled" type="number" class="rating" min=0 max=5 data-glyphicon=0 value=<?php echo $review['StoreReview']['review_rating'];?> >
			</td>
			<td>
			 <?php echo date('d M Y -  H:i a',strtotime($review['StoreReview']['created']));?>
			</td>
						
		     </tr>
		 <?php } } else {
                                 echo '<tr><td class="text-center" colspan="6">'.__('No review found').'</td></tr>';
                                                }?>
	       </tbody>
	    </table>
	
	
	
	
    </div>
    
     <!--******************Review area end here***********************-->
    
     <!--******************Reservation area start here***********************-->
    
    <div role="tabpanel" class="tab-pane" id="reservation">
	
	
	 <h3>Reservation</h3>
	 <br><br>
	  <table class="table table-bordered table-hover table-striped tablesorter">
		
		
		<thead>
		     <tr>	    
			<th  class="th_checkbox">No. of person</th>
			<th  class="th_checkbox">Reservation Date/Time</th>
                        <th  class="th_checkbox">Special Request</th>
			<th  class="th_checkbox">Status</th>

		     </tr>
		</thead>
	       
	       <tbody class="dyntable">
		 <?php
		         $i = 0;			
			$class = ($i%2 == 0) ? ' class="active"' : '';
			
		    ?>
	 <?php if(!empty($myBookings)){ foreach($myBookings as $book) {  ?>
		     <tr >	    
			<td>
			<?php echo $book['Booking']['number_person'];?>
			</td>
			<td>
			<?php echo $book_date = date('d M Y -  H:i a',strtotime($book['Booking']['reservation_date']));?>
			</td>
			<td>
			<?php if(empty($book['Booking']['special_request'])){
                                                echo "--";
                                            } else {
                                            echo ucfirst($book['Booking']['special_request']);}?>
			</td>
			<td>
			<?php echo $book['BookingStatus']['name'];?>
			</td>
						
		     </tr>
		 <?php } } else {
                                 echo '<tr><td class="text-center" colspan="5">'.__('No reservation request found').'</td></tr>';
                                                }?>
	       </tbody>
	    </table>
	
	
	
	
    </div>
    
    <!--******************Reservation area end here***********************-->
  </div>

</div>
	
	
	 
	
	    <div class="row">
		<div class="col-lg-13">
                
		
                <div class="col-lg-4">
		
		
		
		<?php
//		foreach($statusList as $k=>$data){
//			?>
			<div style="float:left;padding:5px;">
			<?php
//                echo $this->Form->input('Order.order_status_id', array(
//			'type' => 'radio',
//			'options' => array($k=>$data),
//			'default'=>$orderDetail[0]['Order']['order_status_id']
//		      ));
		?>
			</div>
		<?php
		//} 
                   ?></div>
                
		
		</div>
		</div>
	   
	   
   
</div>
<?php echo $this->Html->css('pagination'); ?>
	 <script>
    $(document).ready(function() {	
	var total=$("#amt").val();
      $("#assign").html("$"+total);
    });
    
    $('#myTabs a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
</script>
	 <style>
$('#myTabs a[href="#profile"]').tab('show') // Select tab by name
$('#myTabs a:first').tab('show') // Select first tab
$('#myTabs a:last').tab('show') // Select last tab
$('#myTabs li:eq(2) a').tab('show') // Select third tab (0-indexed)
.rating-xs {
    float: left;
    font-size: 1.5em;
    margin-left: 15px;
}
.nav-tabs{
  background-color:#161616;
}

.nav-tabs > li > a{
  border: medium none;
}
.nav-tabs > li > a:hover{
  background-color: #303136 !important;
    border: medium none;
    border-radius: 0;
    color:#fff;
}
	 </style>