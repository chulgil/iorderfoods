<div class="col-lg-10">
	 <h3>Transaction Listing</h3>
	 <?php echo $this->Session->flash();?> 
            <div class="table-responsive">   
	    <?php
	     //$encryptedStoreId = $this->Encryption->encode($storeId);
	    echo $this->Form->create('Payment', array('url' => array('controller' => 'hq', 'action' => 'transactionList'),'id'=>'AdminId','type'=>'post'));  ?>
	    <div class="row padding_btm_20">
		
		
	       <div class="col-lg-3">		     
		    <?php		    
		    $options=array('Paid'=>'Paid','Cash on Delivery'=>'Cash on Delivery');
		    echo $this->Form->input('Payment.is_active',array('type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$options,'empty'=>'Select Status')); ?>		
	       </div>
	       <div class="col-lg-2">
			 
		    <?php
				        echo $this->Form->input('User.from',array('label' => false,'type'=>'text','class'=>'form-control','maxlength'=>'50','div'=>false,'readonly'=>true, 'placeholder' => 'From'));
				  echo $this->Form->input('Merchant.store_id',array('type'=>'hidden','value'=>$storeId));     
				       
				       ?>
	       </div>&nbsp;&nbsp;
		    <div class="col-lg-2">
			
		    <?php
				        echo $this->Form->input('User.to',array('label' => false,'type'=>'text','class'=>'form-control','maxlength'=>'50','div'=>false,'readonly'=>true, 'placeholder' => 'To'));
				       ?>
	       </div>
               <div class="col-lg-3">		 
		   <?php echo $this->Form->button('Search', array('type' => 'submit','class' => 'btn btn-default'));?>
		     <?php
		     
		    
		     echo $this->Html->link('Clear',array('controller'=>'hq','action'=>'transactionList','clear'),array('class' => 'btn btn-default'));?>
	       </div>
	    </div>
	    <?php echo $this->Form->end(); ?>
	    <table class="table table-bordered table-hover table-striped tablesorter">
	       <thead>
		     <tr>	    
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.order_number', 'Order Id');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderPayment.transection_id', 'Transaction Id');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderPayment.amount', 'Amount ($)');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderPayment.created', 'Date');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderPayment.payment_gateway', 'Payment Type');?></th>
			<th  class="th_checkbox">Payment Status</th>
                        <th  class="th_checkbox">Reason</th>

	       </thead>
	       
	       <tbody class="dyntable">
		  <?php
		  if($list){
			$i = 0;			
			foreach($list as $key => $data){
			$class = ($i%2 == 0) ? ' class="active"' : '';
			$EncryptOrderID=$this->Encryption->encode($data['OrderPayment']['order_id']); 

		     ?>
		     <tr <?php echo $class;?>>	    
			<td><?php
			if(!empty($data['Order']['order_number'])){
			echo $data['Order']['order_number'];
			}else{
                        echo "-";	
			}
			?></td>
                        <td><?php echo $data['OrderPayment']['transection_id'];  ?></td>
			<td><?php echo $data['OrderPayment']['amount'];  ?></td>
                        <td><?php echo $data['OrderPayment']['created'];  ?></td>
                        <td><?php echo $data['OrderPayment']['payment_gateway']; ?></td>
			<td><?php echo $data['OrderPayment']['payment_status'];  ?></td>
			<td>NA
			   
                        </td>
			   
			
		     </tr>
		   <?php $i++; } }else{?>
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
			echo $this->Paginator->first('First');
			// Shows the next and previous links
			echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
			// Shows the page numbers
			echo $this->Paginator->numbers(array('separator'=>''));
			echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
			// prints X of Y, where X is current page and Y is number of pages
			//echo $this->Paginator->counter();
			echo $this->Paginator->last('Last');
		   ?>
	    </div>
            </div>
   
</div>
	    <?php echo $this->Html->css('pagination'); ?>
	    
<script>
    $(document).ready(function() {	    
	$("#PaymentIsActive").change(function(){
	 var transactionId=$("#PaymentIsActive").val
	    $("#AdminId").submit();
	});
	    
   });
    $('#UserFrom').datepicker({
	       
	       dateFormat: 'mm-dd-yy',		
	         changeMonth: true,
            changeYear: true,
	    yearRange: '1950:2015',
	       
	   });
	  $('#UserTo').datepicker({
	       
	       dateFormat: 'mm-dd-yy',		
	         changeMonth: true,
            changeYear: true,
	    yearRange: '1950:2015',
	       
	   });
</script>
