<div class="row">
        <div class="col-lg-13">
            <?php  if(!empty($order)){ echo $this->element('show_pagination_count'); }?>
	    <table class="table table-bordered table-hover table-striped tablesorter">
	       <thead>
		<?php
		
		$url=array();
			if(isset($paginationdata)){				
			      @$url=$paginationdata;
			}
		
		?>
		
		      <tr>
		      
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.order_number', 'Order No.',array('url'=>@$url));?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('User.fname', 'Customer Name',array('url'=>@$url));?></th> 
                       <th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_name', 'Store Name',array('url'=>$url));?></th>
<!--                        <th  class="th_checkbox">Store Name</th>-->
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Item.name', 'Items',array('url'=>@$url));?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.amount', 'Amount',array('url'=>@$url));?></th>
<!--                        <th  class="th_checkbox">Amount<span>&nbsp;&nbsp;($)</span></th>-->
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.phone', 'Phone',array('url'=>@$url));?></th>
		        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.address', 'Address',array('url'=>@$url));?></th>			
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('User.email', 'Email',array('url'=>@$url));?></th>
			<!--<th  class="th_checkbox">Delivery/Pickup Time</th>-->
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Segment.name', 'Order Type',array('url'=>@$url));?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.pickup_time', 'Order Date',array('url'=>@$url));?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.created', 'Created',array('url'=>@$url));?></th>
		     </tr>
	       </thead>
	       
	       
	       <tbody class="dyntable">
	       
		  <?php
                  if(!empty($order)){
		         $i = 0;			
			foreach($order as $key => $data){
			$class = ($i%2 == 0) ? ' class="active"' : '';
			$EncryptOrderID=$this->Encryption->encode($data['Order']['id']); 
		     ?>
		     <tr>  
			<td>
        <?php echo  $this->Html->link($data['Order']['order_number'],array('controller'=>'hqreports','action'=>'orderDetail',$EncryptOrderID)); ?>
			
			</td>
			<td><?php
			     if($data['DeliveryAddress']['name_on_bell']){
				echo $data['DeliveryAddress']['name_on_bell'];
			     }else{
				echo $data['User']['fname'] ." ".$data['User']['lname'];

			     }
			
			?> </td>
                        <td><?php echo @$data['Store']['store_name'];?></td>
			<td><?php
			$i=0;
			$items="";
			foreach($data['OrderItem'] as $key => $item){
				if($i==0){
					$items=$item['Item']['name'];
				}else{
					$items.=", ".$item['Item']['name'];	
				}
				$i++;
			}
			echo "<span title='".$items."'>".substr($items,0,50)."</span>";
			?> </td>
			<td><?php
			if($data['Order']['coupon_discount'] >0){
			$total_amount =  $data['Order']['amount'] - $data['Order']['coupon_discount'];
			echo $total_amount;
			}else{
				
			echo $data['Order']['amount'];
			}
			?> </td> 
			<td><?php
			if(!empty($data['DeliveryAddress']['phone'])){
			echo $data['DeliveryAddress']['phone'] ;
			}
			else{
			   echo  $data['User']['phone'];	
			}
			?></td>
			<td><?php
			if(!empty($data['DeliveryAddress']['address'])){
			echo $data['DeliveryAddress']['address'] ;
			}else{
			echo $data['User']['address'] ;	
			}
			
			if($data['Segment']['id'] == 2){
				echo $data['Segment']['name'];
				
			}
			?></td>
                        <td><?php
			if(!empty($data['DeliveryAddress']['email'])){
			echo $data['DeliveryAddress']['email'] ;
			}else{
			echo $data['User']['email'] ;	
			}
			
			
		 ?></td>
			
			<td><?php  echo $data['Segment']['name'] ; ?></td>
                        <td>
			<?php
			if($data['Order']['seqment_id'] == 2){
                            $pickupTime=$this->Dateform->us_format($data['Order']['pickup_time']);
		echo ($data['Order']['pickup_time']!='0000-00-00 00:00:00' && $data['Order']['pickup_time']!='')?$pickupTime:"-";
				
			}
			if($data['Order']['seqment_id'] == 3){
			if($data['Order']['is_pre_order'] == 0){
                            $deliveryTime=$this->Dateform->us_format($data['Order']['created']);
			echo ($data['Order']['created']!='0000-00-00 00:00:00' && $data['Order']['created']!='')?$deliveryTime:"-";
			}else{
			echo ($data['Order']['pickup_time']!='0000-00-00 00:00:00' && $data['Order']['pickup_time']!='')?$this->Dateform->us_format($data['Order']['pickup_time']):"-";	
			}
			}
			
			?>
			</td>
			<td><?php  echo $this->Dateform->us_format($data['Order']['created']) ; ?></td>
			
		     </tr>
		 <?php $i++; }  }else{?>
		  <tr>
		     <td colspan="11" style="text-align: center;">
		       No record available
		     </td>
		  </tr>
		   <?php } ?>
		
	       </tbody>
	    </table>
	    <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
		  <?php
		  if(isset($order)){
			$url=array();
			if(isset($paginationdata)){				
			      $url=$paginationdata;
			}
			      echo $this->Paginator->first('First',array('url'=>$url));
			      // Shows the next and previous links
			      echo $this->Paginator->prev('Previous',array('url'=>$url));
			      // Shows the page numbers
			      echo $this->Paginator->numbers(array('url'=>$url));
			      echo $this->Paginator->next('Next',array('url'=>$url));
			      // prints X of Y, where X is current page and Y is number of pages
			      //echo $this->Paginator->counter();
			      echo $this->Paginator->last('Last',array('url'=>$url));
		  }
		   ?>
	    </div>
	    
	   
   
</div>
         </div>
    </div>
<?php echo $this->Html->css('pagination'); ?>
	    
