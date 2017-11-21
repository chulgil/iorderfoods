<div class="row">
        <div class="col-lg-13">
            <?php  if(!empty($order)){ echo $this->element('show_pagination_count'); }?>
	    <table class="table table-bordered table-hover table-striped tablesorter">
	       <thead>
		<?php
		
		$url=array();
			if(isset($paginationdata)){				
			      $url=$paginationdata;
			}
		
		?>
		     <tr>	    
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('User.fname', 'Customer Name',array('url'=>$url));?></th> 
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('User.email', 'Email',array('url'=>$url));?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.phone', 'Phone',array('url'=>$url));?></th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_name', 'Store Name',array('url'=>$url));?></th>
<!--                        <th  class="th_checkbox">Store Name</th>-->
			<th  class="th_checkbox">Order History</th>
			<th  class="th_checkbox"><?php echo $this->Paginator->sort('User.created', 'Created',array('url'=>$url));?></th>
		     </tr>
	       </thead>
	       
	       <tbody class="dyntable">
		  <?php
                  if(!empty($userdata)){
		         $i = 0;			
			foreach($userdata as $key => $data){
			$class = ($i%2 == 0) ? ' class="active"' : '';
			$EncryptUserID=$this->Encryption->encode($data['User']['id']); 
		     ?>
		     <tr>  
			
			<td><?php
			     echo $data['User']['fname'] ." ".$data['User']['lname'];
			
			?> </td>
			<td><?php
			
			echo $data['User']['email'];
			?> </td>
			<td><?php
			echo $data['User']['phone'];
			?> </td>
                        <td><?php
			echo $data['Store']['store_name'];
			?> </td>
			<td><?php
			$urlarray=$url;
			$urldata=array_values($urlarray);
			
			//echo  $this->Html->link("History",array('controller'=>'hqreports','action'=>'orderHistory',$EncryptUserID,substr($urldata[0],0,10),substr($urldata[1],0,10),$urldata[2],$page));
			 echo  $this->Html->link("History",array('controller'=>'hqcustomers','action'=>'orderHistory',$EncryptUserID));
			
			
			?></td>
			<td><?php  echo $this->Dateform->us_format($data['User']['created']) ; ?></td>
                   
			
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
		  if(isset($userdata)){
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
	    
