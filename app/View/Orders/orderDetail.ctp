
<div class="row">
        <div class="col-lg-13">
	 <h3>Order Listing</h3>
	 <br>
	 <?php echo $this->Session->flash();?> 
            <div class="table-responsive">   
	    <?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'index'),'id'=>'AdminId','type'=>'post'));  ?>
	    <div class="row padding_btm_20">
		<div class="col-lg-2">		     
		    <?php echo $this->Form->input('OrderStatus.id',array('type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$statusList,'empty'=>'Select Status')); ?>		
	       </div>
		
	       <div class="col-lg-2">		     
		   	    
	<?php echo $this->Form->input('Segment.id',array('type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$typeList,'empty'=>'Select Type')); ?>		
	       </div>
		
	       <div class="col-lg-3">		     
		    <?php echo $this->Form->input('Order.keyword',array('value'=>$keyword,'label' => false,'div' => false, 'placeholder' => 'Keyword Search','class' => 'form-control'));?>
			<span class="blue">(<b>Search by:</b>Order number,Customer name,email)</span>
	       </div>
	       
	       
	      
	       <div class="col-lg-2">		 
		   <?php echo $this->Form->button('Search', array('type' => 'submit','class' => 'btn btn-default'));?>
		     <?php echo $this->Html->link('Clear',array('controller'=>'orders','action'=>'index','clear'),array('class' => 'btn btn-default'));?>
	       </div>
	       <div class="col-lg-2">		  
		  
	       </div>
	    </div>
	    <?php echo $this->Form->end(); ?>
	    <?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'orderStatus'),'id'=>'OrderId','type'=>'post'));  ?>
	    <table class="table table-bordered table-hover table-striped tablesorter">
	       <thead>
		     <tr>	    
			<th  class="th_checkbox"><input type="checkbox" id="selectall" style="float:left;"/>Check All</th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.order_number', 'Order No.');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('User.fname', 'Customer Name');?></th> 
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderItem.name', 'Items');?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.amount', 'Amount');?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.phone', 'Phone');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('DeliveryAddress.address', 'Address');?></th>			
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('User.email', 'Email');?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Segment.name', 'Order Type');?></th>
			<th  class="th_checkbox">Status</th>
			<th  class="th_checkbox">Action</th>	
		     </tr>
	       </thead>
	       
	       <tbody class="dyntable">
		  <?php
		         $i = 0;			
			foreach($list as $key => $data){
			$class = ($i%2 == 0) ? ' class="active"' : '';
			$EncryptOrderID=$this->Encryption->encode($data['Order']['id']); 
		     ?>
		     <tr >	    
			<td><?php echo $this->Form->checkbox('User.id.'.$key,array('class'=>'case','value'=>$data['Order']['id'],'style'=>'float:left;'));  ?></td>
			<td><?php  echo $data['Order']['order_number'] ; ?></td>
			<td><?php  echo $data['User']['fname'] ." ".$data['User']['lname']; ?></td>
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
			echo wordwrap($items,15,"<br>\n");
			?></td>
			<td><?php  echo $data['Order']['amount'] ; ?></td>
			<td><?php  echo $data['User']['phone'] ; ?></td>
			<td><?php  echo $data['User']['DeliveryAddress']['address'] ; ?></td>
                        <td><?php  echo $data['User']['email'] ; ?></td>
			<td><?php  echo $data['Segment']['name'] ; ?></td>
			<td><?php  echo $data['OrderStatus']['name'] ; ?></td>
			
			
           <td><?php echo  $this->Html->link($this->Html->image("store_admin/view.png", array("alt" => "Detail", "title" => "Detail")),array('controller'=>'orders','action'=>'detailOrder',$EncryptOrderID),array('escape' => false)); ?>
</td>
		     </tr>
		 <?php $i++; } ?>
		 <tr>
                    
                    <td colspan="6">                       
                        
                         <?php
                        
		        echo $this->Form->input('Order.order_status_id',array('type'=>'select','style'=>'background-color:white;text-align:left;','class'=>'btn btn-default','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$statusList,'empty'=>'Select Status')); ?>	&nbsp;&nbsp;&nbsp;&nbsp;
<?php echo $this->Form->button('Update Multiple Orders', array('type' => 'submit','class' => 'btn btn-default','onclick'=>'return check();'));            
                        
                         ?>                     
                        
                        
                    </td>
                   
                   </tr>
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
	    
	    <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-1">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-1"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";    ?> </div-->

            </div>
   
</div>
	    <style>
	       
.paging_full_numbers {
   margin-top: 5px;
   float:right;
}
.paging_full_numbers .paginate_button {
    background: url("images/buttonbg5.png") repeat-x scroll left top #EEEEEE;
    border: 1px solid #CCCCCC;
    border-radius: 3px;
    cursor: pointer;
    display: inline-block;
    margin-left: 5px;
    padding: 2px 8px;
}
.paging_full_numbers .paginate_button:hover {
    background: none repeat scroll 0 0 #EEEEEE;
    box-shadow: 1px 1px 2px #CCCCCC inset;
}
.paging_full_numbers .paginate_active, .paging_full_numbers .paginate_button:active {
    background: url("images/buttonbg3.png") repeat-x scroll left top #405A87;
    border: 1px solid #405A87;
    border-radius: 3px;
    color: #FFFFFF;
    display: inline-block;
    margin-left: 5px;
    padding: 2px 8px;
}
.paging_full_numbers .paginate_button_disabled {
    color: #999999;
}
.paging_full_numbers span {
    background: url("images/buttonbg5.png") repeat-x scroll left top #EEEEEE;
    border: 1px solid #CCCCCC;
    border-radius: 3px;
    cursor: pointer;
    display: inline-block;
    margin-left: 5px;
    padding: 2px 8px;
}
.paging_full_numbers span:hover {
    background: none repeat scroll 0 0 #EEEEEE;
    box-shadow: 1px 1px 2px #CCCCCC inset;
}
.paging_full_numbers span:active {
    background: url("images/buttonbg3.png") repeat-x scroll left top #405A87;
    border: 1px solid #405A87;
    border-radius: 3px;
    color: #FFFFFF;
    display: inline-block;
    margin-left: 5px;
    padding: 2px 8px;
}
.paging_full_numbers .disabled {
    color: #999999;
}
.paging_full_numbers span a {
    color: #000000;
}
.pagination a {
    border-radius: 3px;
}
.pagination a {
    box-shadow: 1px 1px 0 #F7F7F7;
}
.pagination a:hover {pasta
}
.pagination a:hover {
    background: none repeat scroll 0 0 #EEEEEE;
    box-shadow: 1px 1px 3px #EEEEEE inset;
    text-decoration: none;
}
.pagination a.disabled {
    border: 1px solid #CCCCCC;
    color: #999999;
}
.pagination a.disabled:hover {
    background: url("images/buttonbg5.png") repeat-x scroll left bottom rgba(0, 0, 0, 0);
    box-shadow: none;
}
.pagination a.current {
    background: url("images/buttonbg3.png") repeat-x scroll left top #333333;
    border: 1px solid #405A87;
    color: #FFFFFF;
}
.pagination a.current:hover {
    box-shadow: none;
}
.pgright {
    position: absolute;
    right: 10px;
    top: 12px;
}
.pgright a.disabled {
    border: 1px solid #CCCCCC;
}
	    </style>
	    
<script>
    $(document).ready(function() {	    
	$("#OrderStatusId").change(function(){
	    var catgoryId=$("#OrderOrderStatusId").val();
	    $("#AdminId").submit();
	});
	    
	$("#SegmentId").change(function(){
	    //var catgoryId=$("#OrderSeqmentId").val();
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
    var fields = $(".case").serializeArray();
    if (fields.length == 0) 
    { 
        alert('Please select your choice.'); 
        // cancel submit
        return false;
    } 
    else 
    { 
        alert(fields.length + " items selected"); 
    }
}
</script>