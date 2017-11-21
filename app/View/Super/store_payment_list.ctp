<div class="row">
        <div class="col-lg-12">
	 <h3>Subscription Payment List</h3>
	 <?php echo $this->Session->flash();?> 
            <div class="table-responsive">   
	    <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'hq', 'action' => 'storePaymentList'),'id'=>'AdminId','type'=>'post'));  ?>
	    <div class="row padding_btm_20">
		
		
	       <div class="col-lg-3">		     
		    <?php                    
                $merchantList=$this->Common->getHQStores($merchantId);
                echo $this->Form->input('Merchant.store_id',array('label'=>false,'options'=>$merchantList,'class'=>'form-control','div'=>false,'empty'=>'Please Select Store'));
	    ?> 
	       </div>

	     
	    </div>
	    <?php echo $this->Form->end(); ?>
	    <table class="table table-bordered table-hover table-striped tablesorter">
	       <thead>
		     <tr>	    
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('StorePayment.id', 'Subscription ID');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_name', 'Store Name');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Plan.name', 'Subscription Type');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('StorePayment.payment_date', 'Payment Date');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('StorePayment.amount', 'Amount');?>&nbsp;&nbsp;($)</th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('StorePayment.payment_status', 'Status');?></th>

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
			<td><?php echo  $data['StorePayment']['id']  ; ?></td>
                        <td><?php echo  $data['Store']['store_name']  ; ?></td>
                        <td><?php echo  $data['Plan']['name']  ; ?></td>
			<td><?php echo  $this->Dateform->us_format($data['StorePayment']['payment_date'])  ; ?></td>
			<td><?php echo  $data['StorePayment']['amount']  ; ?></td>
			<td><?php echo  $data['StorePayment']['payment_status']  ; ?></td>
			
			
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