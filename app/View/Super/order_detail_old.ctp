<style>
   .new-chkbx-wrap { float:left;padding:5px;width:30%;margin-bottom:10px;}
   .new-chkbx-wrap > input {
    float: left;
    margin-right: 5px;
    position: relative;
    top: -3px;
}
</style>
<div class="row">
        <div class="col-lg-8">
	 <h3>Order Details</h3>
	 <br>
	 <?php echo $this->Session->flash();?> 
         	
		<table class="table tablesorter">
			<thead>
				<tr>
					<th class="th_checkbox" colspan="5" style="text-align:left;">
		Order Id  : <?php  echo $orderDetail[0]['Order']['order_number'] ;?> | Cost :  $<?php if($orderDetail[0]['Order']['coupon_discount'] >0){
			$total_amount =  $orderDetail[0]['Order']['amount'] - $orderDetail[0]['Order']['coupon_discount'];
			echo $total_amount;
			}else{
				
			echo $orderDetail[0]['Order']['amount'];
			}?> | Status : <?php
		              $data =  $this->requestAction('/orders/ajaxRequest/'.$orderDetail[0]['Order']['order_status_id'].'');
		 
		echo $data;
		?>
		<br>
		Address : <?php  echo $orderDetail[0]['DeliveryAddress']['address'] ." ".$orderDetail[0]['DeliveryAddress']['city'];?>
		<br>
	       Order Type : <?php  echo $orderDetail[0]['Segment']['name'] ;?>  |  Created : &nbsp;&nbsp;<?php  echo $orderDetail[0]['Order']['created'] ;?>
					
					</th>
				</tr>
			</thead>
		</table>
	
	    <table class="table table-bordered table-hover table-striped tablesorter">
		
		
		<thead>
		     <tr>	    
			<th  class="th_checkbox">Item</th>
			<th  class="th_checkbox">Quantity</th>
			<th  class="th_checkbox">Size</th>
                        <th  class="th_checkbox">Type</th>
			<th  class="th_checkbox">Add-ons</th>

		     </tr>
		</thead>
	       
	       <tbody class="dyntable">
		 <?php
		         $i = 0;			
			foreach($orderDetail[0]['OrderItem'] as $key => $item){
			$class = ($i%2 == 0) ? ' class="active"' : '';
			
		    ?>
		     <tr >	    
			<td>
			
			<?php echo $item['Item']['name'];?>
				
			</td>
			<td>
			
			<?php echo $item['quantity'];?>
				
			</td>
			<td>
			<?php echo ($item['Size'])?$item['Size']['size']:"-";?>
			</td>
			<td>
			<?php echo ($item['Type'])?$item['Type']['name']:"-";?>	
			</td>
			
			
			<td style="width: 300px; word-wrap: break-word; word-break: break-all;">
			<?php
			$Toppings='';
				if($item['OrderTopping']){
					$Toppings=array();
					foreach($item['OrderTopping'] as $vkey => $toppingdetails){
						$Toppings[]=$toppingdetails['Topping']['name'];
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
			
         
		     </tr>
		<?php $i++; } ?>
		 
	       </tbody>
	    </table>
	   
</div>
	    <?php echo $this->Html->css('pagination'); ?>
