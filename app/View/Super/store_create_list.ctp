<div class="row">
        <div class="col-lg-12">
	 <h3>Requested Stores</h3>
	 <?php echo $this->Session->flash();?>
	 <br>
            <div class="table-responsive">   
	    <?php echo $this->Form->create('Super', array('url' => array('controller' => 'super', 'action' => 'storeCreateList'),'id'=>'AdminId','type'=>'post'));  ?>
	    <div class="row padding_btm_20">
		<div class="col-lg-2">		     
		    <?php echo $this->Form->input('Status.id',array('type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>array('3'=>'Inprogress','1'=>'Done','2'=>'Deny',),'empty'=>'Select Status')); ?>		
	       </div>
		
	       <div class="col-lg-3">		     
		    <?php echo $this->Form->input('MerchantStoreRequest.keyword',array('value'=>$keyword,'label' => false,'div' => false, 'placeholder' => 'Keyword Search','class' => 'form-control'));?>
			<span class="blue">(<b>Search by:</b>Store Name)</span>
	       </div>
	       
	       
	      
	       <div class="col-lg-2">		 
		   <?php echo $this->Form->button('Search', array('type' => 'submit','class' => 'btn btn-default'));?>
		     <?php echo $this->Html->link('Clear',array('controller'=>'super','action'=>'storeCreateList','clear'),array('class' => 'btn btn-default'));?>
	       </div>
	       <div class="col-lg-2">		  
		  
	       </div>
	    </div>
	    <?php echo $this->Form->end(); ?>
                <div class="row">
                    <div class="col-sm-6">
                        <?php echo $this->Paginator->counter('Page {:page} of {:pages}');?> 
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php echo $this->Paginator->counter('showing {:current} records out of {:count} total');?> 
                    </div>
                </div>
	    <?php echo $this->Form->create('Order', array('url' => array('controller' => 'super', 'action' => 'UpdaterequestStatus'),'id'=>'OrderId','type'=>'post'));  ?>
	    <table class="table table-bordered table-hover table-striped tablesorter">
	       <thead>
		     <tr>	    
			<!--<th  class="th_checkbox"><input type="checkbox" id="selectall"/></th>-->
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantStoreRequest.store_name', 'Store Name');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Merchant.name', 'Merchant Name');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantStoreRequest.email', 'Email');?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantStoreRequest.phone', 'Contact No.');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantStoreRequest.request_text', 'Requested Text');?></th>
			<th  class="th_checkbox">Status</th>
		        <th  class="th_checkbox">Action</th>

			<!--<th  class="th_checkbox">Action</th>-->	
		     </tr>
	       </thead>
	       
	       <tbody class="dyntable">
		  <?php
                  if($list){
		         $i = 0;			
			foreach($list as $key => $data){
			$class = ($i%2 == 0) ? ' class="active"' : '';
			$EncryptRequestedID=$this->Encryption->encode($data['MerchantStoreRequest']['id']); 
		     ?>
		     <tr >	    
			<!--<td><?php //echo $this->Form->checkbox('Order.id.'.$key,array('class'=>'case','value'=>$data['Order']['id'],'style'=>'float:left;'));  ?></td>-->
		<td>   <?php echo  $data['MerchantStoreRequest']['store_name']; ?></td>
                <td>   <?php echo  $data['Merchant']['name']; ?></td>
		<td>   <?php echo  $data['MerchantStoreRequest']['email']; ?></td>
		<td>   <?php echo  $data['MerchantStoreRequest']['phone']; ?></td>
		<td>   <?php echo  $data['MerchantStoreRequest']['request_text']; ?></td>
		<td>   <?php
                      if($data['MerchantStoreRequest']['request_status'] == 1){
		echo  "Done";
		      }elseif($data['MerchantStoreRequest']['request_status'] == 2){
			
	        echo  "Deny";
		      }
		      else{
		echo  "In Progress";
		      }
		?></td>
		<td>
			
		 <?php

			  if($data['MerchantStoreRequest']['request_status'] == 3){
			echo $this->Html->link("Approved",array('controller'=>'super','action'=>'approvedRequest',$EncryptRequestedID,1),array('confirm' => 'Are you sure to Approve Request?','escape' => false));
			      }else{
                         echo "NA";
			      }
			   echo " ";
			   echo "|";
			   echo " ";
			   
			    if($data['MerchantStoreRequest']['request_status'] == 3){
			 echo $this->Html->link("Disapproved",array('controller'=>'super','action'=>'approvedRequest',$EncryptRequestedID,2),array('confirm' => 'Are you sure to Disapprove Request?','escape' => false));
			      }else{
                                 echo "NA";
			      }	  
			
			  ?>	
			
		</td>

		     </tr>
		 <?php $i++; }  }else{?>
		  <tr>
		     <td colspan="11" style="text-align: center;">
		       No record available
		     </td>
		  </tr>
		   <?php } if($list){?>
		   
		<!-- <tr>
                    
                    <td colspan="6">                       
                        
                         <?php
                        
		        //echo $this->Form->input('Order.order_status_id',array('type'=>'select','style'=>'background-color:white;text-align:left;','class'=>'btn btn-default','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$statusList,'empty'=>'Select Status')); ?>	&nbsp;&nbsp;&nbsp;&nbsp;
<?php //echo $this->Form->button('Update Multiple Orders', array('type' => 'submit','class' => 'btn btn-default','onclick'=>'return check();'));            
                        
                         ?>                     
                        
                        
                    </td>
                   
                   </tr>-->
                 <?php } ?>
	       </tbody>
	    </table>
	    <?php echo $this->Form->end(); ?>
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
	$("#StatusId").change(function(){
	   // var catgoryId=$("#OrderOrderStatusId").val();
	    $("#AdminId").submit();
	});
	    
	
	
	$("#selectall").click(function(){
	var st = $("#selectall").prop('checked');
	$('.case').prop('checked',st);

	});
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
    $(".case").click(function(){
        if($(".case").length == $(".case:checked").length) {
            $("#selectall").attr("checked", "checked");
        } else {
            $("#selectall").removeAttr("checked");
        }
 
    });
  
   });
    function check() 
{

   var statusId=$("#OrderOrderStatusId").val();
  
    var fields = $(".case").serializeArray();
    if (fields.length == 0 )
    { 
        alert('Please select one order to proceed.'); 
        // cancel submit
        return false;
    }
    if (statusId == '') {
	 alert('Please select status.'); 
        return false;
    }

    
}


</script>