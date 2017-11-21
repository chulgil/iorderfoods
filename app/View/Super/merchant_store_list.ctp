<div class="row">
        <div class="col-lg-12">
	 <h3>Subscription Payment List</h3>
         <label>Merchant : </label> <?php echo $merchantDetail['Merchant']['name'];?>
	 <?php echo $this->Session->flash();?> 
            <div class="table-responsive">   
	    <?php echo $this->Form->create('Store', array('url' => array('controller' => 'super', 'action' => 'merchantStoreList', $merchantId),'id'=>'AdminId','type'=>'post'));  ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">   
                    <?php echo $this->Form->input('Store.keyword', array('value' => @$keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control', 'maxlength' => 55)); ?>
                    <span class="blue">(<b>Search by:</b>Name, Type, Location, Address, Phone no, URL)</span>
                </div>
                <div class="col-lg-4">                        
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->Html->link('Clear', array('controller' => 'super', 'action' => 'merchantStoreList', $merchantId, 'clear'), array('class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-5">
                    <?php echo $this->Html->link('Back', array('controller' => 'super', 'action' => 'merchantPaymentList'), array('class' => 'btn btn-default pull-right')); ?>
                </div>
            </div>
	    <?php echo $this->Form->end(); ?>
	    <table class="table table-bordered table-hover table-striped tablesorter">
	       <thead>
		     <tr>	    
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_name', 'Store Name');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_url', 'Store URL');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.email_id', 'Email');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.phone', 'Phone');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.address', 'Address');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.city', 'City');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.state', 'State');?></th>

	       </thead>
	       
	       <tbody class="dyntable">
		  <?php
		  if($list){
			$i = 0;			
			foreach($list as $key => $data){
			$class = ($i%2 == 0) ? ' class="active"' : '';
		     ?>
		     <tr>	    
			<td>
                            <?php echo $this->Html->link($data['Store']['store_name'], array('controller' => 'super', 'action' => 'merchantStorePaymentList', $this->Encryption->encode($data['Store']['id'])), array('escape' => false)); ?>
                        </td>
                        <td><?php echo  $data['Store']['store_url']  ; ?></td>
                        <td><?php echo  $data['Store']['email_id']  ; ?></td>
                        <td><?php echo  $data['Store']['phone']  ; ?></td>
                        <td><?php echo  $data['Store']['address']  ; ?></td>
                        <td><?php echo  $data['Store']['city']  ; ?></td>
                        <td><?php echo  $data['Store']['state']  ; ?></td>
		     </tr>
		   <?php $i++; } }else{?>
		   <tr>
		     <td colspan="7" style="text-align: center;">
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
        var merchantId = '<?php echo $merchantId;?>';
        
	$("#StoreKeyword").autocomplete({
            source: "/super/getStoreSearchValues/" + merchantId,
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        };
	    
   });
</script>