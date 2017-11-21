<div class="row">
        <div class="col-lg-8">
	 <h3>Payment List</h3>
	 <?php echo $this->Session->flash();?> 
            <div class="table-responsive">   
	    <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'hq', 'action' => 'storePaymentList'),'id'=>'AdminId','type'=>'post'));  ?>
	    <div class="row padding_btm_20">
		
	    </div>
	    <?php echo $this->Form->end(); ?>
	    <table class="table table-bordered table-hover table-striped tablesorter">
	       <thead>
		     <tr>	    
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantPayment.id', 'Subscription ID');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Plan.name', 'Subscription Type');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantPayment.payment_date', 'Payment Date');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantPayment.amount', 'Amount');?>&nbsp;&nbsp;($)</th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantPayment.payment_status', 'Status');?></th>

	       </thead>
	       
	       <tbody class="dyntable">
		  <?php
		  if($list){
			$i = 0;			
			foreach($list as $key => $data){
			$class = ($i%2 == 0) ? ' class="active"' : '';
			//$EncryptCouponID=$this->Encryption->encode($data['Coupon']['id']); 
		     ?>
		     <tr>	    
			<td><?php echo  $data['MerchantPayment']['id']  ; ?></td>
                        <td><?php echo  $data['Plan']['name']  ; ?></td>
			<td><?php echo  $data['MerchantPayment']['payment_date']  ; ?></td>
			<td><?php echo  $data['MerchantPayment']['amount']  ; ?></td>
			<td><?php echo  $data['MerchantPayment']['payment_status']  ; ?></td>
			
			
		     </tr>
		   <?php $i++; } }else{?>
		   <tr>
		     <td colspan="6" style="text-align: center;">
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
	   <?php echo $this->Html->css('pagination'); ?>
	    
<script>
    $(document).ready(function() {	    
	$("#MerchantStoreId").change(function(){
//var couponId=$("#CouponIsActive").val
	    $("#AdminId").submit();
	});
	    
   });
</script>