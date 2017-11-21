<div class="row">
        <div class="col-lg-12">
         <label>Merchant : </label> <?php echo $merchantDetail['Merchant']['name'];?>
	 <h3>Payment List <?php echo (isset($storeDetail['Store']['store_name']) ? '(' . $storeDetail['Store']['store_name'] . ')' : '')?></h3>
	 <?php echo $this->Session->flash();?> 
            <div class="table-responsive">   
	    <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'super', 'action' => 'merchantStorePaymentList', $storeId),'id'=>'AdminId','type'=>'post'));  ?>
	    <div class="row">
                
	    </div>
            <br>
            <div class="row padding_btm_20">
                <div class="col-lg-2">		     
                    <?php
                    $statusList = array('Paid' => 'Paid', 'Invoice Created' => 'Invoice Created', 'Not Paid' => 'Not Paid');
                    echo $this->Form->input('StorePayment.payment_status', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $statusList, 'empty' => 'Select Status'));
                    ?>		
                </div>
                <div class="col-lg-2">
                    <?php
                    echo $this->Form->input('StorePayment.from', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'From'));
                    ?>
                </div>
                <div class="col-lg-2">

                    <?php
                    echo $this->Form->input('StorePayment.to', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'To'));
                    ?>
                </div>

                <div class="col-lg-4">                        
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->Html->link('Clear', array('controller' => 'super', 'action' => 'merchantStorePaymentList', $storeId, 'clear'), array('class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Html->link('Back', array('controller' => 'super', 'action' => 'merchantStoreList', $this->Encryption->encode($merchantDetail['Merchant']['id'])), array('class' => 'btn btn-default pull-right')); ?>
                </div>
            </div>
	    <?php echo $this->Form->end(); ?>
	    <table class="table table-bordered table-hover table-striped tablesorter">
	       <thead>
		     <tr>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_url', 'Store URL');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Plan.name', 'Subscription Type');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('StorePayment.payment_date', 'Payment Date');?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('StorePayment.payment_type', 'Payment Type'); ?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('StorePayment.amount', 'Amount');?>&nbsp;&nbsp;($)</th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('StorePayment.payment_status', 'Status');?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('StorePayment.comments', 'Comments');?></th>
	       </thead>
	       
	       <tbody class="dyntable">
		  <?php
		  if($list){
			$i = 0;			
			foreach($list as $key => $data){
			$class = ($i%2 == 0) ? ' class="active"' : '';
		     ?>
		     <tr>
                        <td><?php echo  $data['Store']['store_url'];?></td>
                        <td><?php echo  $data['Plan']['name']  ; ?></td>
			<td><?php echo ($data['StorePayment']['payment_date'] != null && !empty($data['StorePayment']['payment_date']) ? $this->Dateform->us_format($data['StorePayment']['payment_date']) : '-');?></td>
                        <td>
                            <?php 
                            if($data['StorePayment']['payment_type'] == 1)
                            {
                                echo 'One-Time';
                            }
                            else if($data['StorePayment']['payment_type'] == 2)
                            {
                                echo 'Recurring';
                            }
                            else 
                            {
                                echo '-';
                            }
                            ?>
                        </td>
			<td><?php echo  $data['StorePayment']['amount']  ; ?></td>
			<td><?php echo  $data['StorePayment']['payment_status']  ; ?></td>
			<td>
                            <?php
                            $comments = (strlen($data['StorePayment']['comments']) > 30 ? substr($data['StorePayment']['comments'], 0, 30) . '...' : $data['StorePayment']['comments']);
                            ?>
                            <p title="<?php echo $data['StorePayment']['comments'];?>" data-toggle="tooltip"><?php echo $comments;?></p>
                        </td>
			
			
		     </tr>
		   <?php $i++; } }else{?>
		   <tr>
		     <td colspan="8" style="text-align: center;">
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
	
        $("#StorePaymentPaymentStatus").change(function () {
//var couponId=$("#CouponIsActive").val
        $("#AdminId").submit();
    });
    $('#StorePaymentFrom').datepicker({
        dateFormat: 'mm-dd-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2010:' + new Date().getFullYear(),
    });
    $('#StorePaymentTo').datepicker({
        dateFormat: 'mm-dd-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2010:' + new Date().getFullYear(),
    });
   });
</script>