
<div class="row">
        <div class="col-lg-8">
	 <h3>Category Listing</h3>
	 <?php echo $this->Session->flash();?> 
            <div class="table-responsive">   
	    <?php echo $this->Form->create('category', array('url' => array('controller' => 'categories', 'action' => 'categoryList'),'id'=>'AdminId','type'=>'post'));  ?>
	    <div class="row padding_btm_20">
		
		
	       <div class="col-lg-4">		     
		    <?php		    
		    $options=array('1'=>'Active','0'=>'Inactive');
		    echo $this->Form->input('category.is_active',array('type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$options,'empty'=>'Select Status')); ?>		
	       </div>

	       <div class="col-lg-8">		  
		  <div class="addbutton">                
                        <?php echo $this->Form->button('Add Category', array('type' => 'button','onclick'=>"window.location.href='/categories/index'",'class' => 'btn btn-default')); ?>  
                        <?php echo $this->Form->button('Upload Category', array('type' => 'button','onclick'=>"window.location.href='/categories/uploadfile'",'class' => 'btn btn-default')); ?>  
		   
                  </div>
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
	    <?php echo $this->Form->create('Category', array('url' => array('controller' => 'categories', 'action' => 'deleteMultipleCategory'),'id'=>'OrderId','type'=>'post'));  ?>
	    <table class="table table-bordered table-hover table-striped tablesorter" id="categoriesListing">
	       <thead>
		     <tr>
			<th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
			<!--<th  class="th_checkbox"><?php //echo $this->Paginator->sort('Category.name', 'Name');?></th>-->
			<th  class="th_checkbox">Name</th>
			<th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
			 <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>

	       </thead>
	       
	       <tbody class="dyntable">
		  <?php
		  if($list){
			$i = 0;			
			foreach($list as $key => $data){
			$class = ($i%2 == 0) ? ' class="active"' : '';
			$EncryptCategoryID=$this->Encryption->encode($data['Category']['id']); 
		     ?>
		     <tr <?php echo $class;?> notif-id="<?php echo $EncryptCategoryID; ?>">
		     <td class="firstCheckbox"><?php echo $this->Form->checkbox('Category.id.'.$key,array('class'=>'case','value'=>$data['Category']['id'],'style'=>'float:left;'));  ?></td>
			<td style="width:200px;"><?php echo $data['Category']['name'];?></td>

                     <td style="width:150px;">
			   <?php
			if($data['Category']['is_active']){
			   echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")),array('controller'=>'categories','action'=>'activateCategory',$EncryptCategoryID,0),array('confirm' => 'Are you sure to Deactivate Category?','escape' => false));
			}else{
			   echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")),array('controller'=>'categories','action'=>'activateCategory',$EncryptCategoryID,1),array('confirm' => 'Are you sure to Activate Category?','escape' => false));
			}
			?>
		    </td>

			<td style="width:150px;" class='sort_order'>
				<?php echo  $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")),array('controller'=>'categories','action'=>'editCategory',$EncryptCategoryID), array('escape' => false)); ?>
				<?php echo " | ";?>
				<?php echo  $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")),array('controller'=>'categories','action'=>'deleteCategory',$EncryptCategoryID),array('confirm' => 'Are you sure to delete Category?','escape' => false)); ?>
				<?php
				     echo $this->Html->image('uparrow.png', array('alt'=>"Up", 'title'=>"Up", 'class' => 'up_order', 'id' => 'upOrder'));
				     echo $this->Html->image('downarrow.png', array('alt'=>"Down", 'title'=>"Down", 'class' => 'down_order', 'id' => 'downOrder'));
				?>
                        </td> 
			
		     </tr>
		   <?php $i++; } } else { ?>
		  <tr>
		     <td colspan="11" style="text-align: center;">
		       No record available
		     </td>
		  </tr>
		    <?php } ?>
		</tbody>    
		    <?php  if($list) { ?>
		 <tfoot>
		 <tr>
                    
                    <td colspan="6">                       
                        
                         <?php
                        
echo $this->Form->button('Delete Category', array('type' => 'submit','class' => 'btn btn-default','onclick'=>'return check();'));            
                        
                         ?>                     
                        
                        
                    </td>
                   
                   </tr>
		 </tfoot>
                 <?php } ?>
	       
	    </table>
 <?php echo $this->Form->end(); ?>

<!--	    <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
		  <?php
			//echo $this->Paginator->first('First');
			//// Shows the next and previous links
			//echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
			//// Shows the page numbers
			//echo $this->Paginator->numbers(array('separator'=>''));
			//echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
			//// prints X of Y, where X is current page and Y is number of pages
			////echo $this->Paginator->counter();
			//echo $this->Paginator->last('Last');
		   ?>
	    </div>-->
	    
	    <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-2">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-2"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";    ?> </div-->

            </div>
   
</div>
         <?php //echo $this->Html->css('pagination'); ?>
	    <style>
	       
.firstCheckbox{width:10px;}
	    </style>
	    
<script>
    $(document).ready(function() {	    
	$("#categoryIsActive").change(function(){
	 var catgoryId=$("#categoryIsActive").val
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
    if (fields.length == 0 )
    { 
        alert('Please select one category to proceed.'); 
        // cancel submit
        return false;
    }
     var r =confirm("Are you sure you want to delete");
   if (r == true) {
        txt = "You pressed OK!";
    } else {
        txt = "You pressed Cancel!";
           return false;

    }
    
}
</script>

<script>
	var notifLen = $('table#categoriesListing').find('tr').length;
	$(document).ready(function() {		
	    
		// Hide up arrow from first row 
		$('table#categoriesListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
		// Hide down arrow from last row 
		$('table#categoriesListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();
	    
		var $up = $(".up_order")
		$up.click(function() {
			var $tr = $(this).parents("tr");
			if ($tr.index() != 0) {
				$tr.fadeOut().fadeIn();
				$tr.prev().before($tr);
					
			}
			updateOrder();
		});
	    
	    
	    
	    //down
	    var $down = $(".down_order");
	    var len = $down.length;
		$down.click(function() {
			var $tr = $(this).parents("tr");
			
			if ($tr.index() <= len ) {			    
			    
			    $tr.fadeOut().fadeIn();
			    $tr.next().after($tr);
			}
			updateOrder();
		});
	});
    
	function updateOrder(){
		$('img.up_order').show();
		$('img.down_order').show();		
		
		$('table#categoriesListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
		$('table#categoriesListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();
				
		var orderData = getNotifOrderKeyVal();
		
		if (orderData) {
		    $.ajax({
		    
			url:'/categories/updateOrder?' + orderData ,
			type: 'get',
			
			success: function() {
			    
			    
			}
		    });
		}
	}
	
	function getNotifOrderKeyVal() {
		if ($('table#categoriesListing tbody').eq(0).find('tr').length > 0) {
			var orderData = '';
			$('table#categoriesListing tbody').eq(0).find('tr').each(function(i) {
			    var notifId = $(this).attr('notif-id');
			    orderData += notifId + '=' + (i+1) + '&';
			});
			return orderData;
		}
		return false;
	}
</script>