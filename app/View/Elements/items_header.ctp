<?PHP if(($this->params['controller']=='Products' || $this->params['controller']=='Payments' )&& ($this->params['action']=='orderDetails' || $this->params['action']=='success' || $this->params['action']=='status')){?>

  <div class="rgt-side order_review">
	      <div class="text-right clearfix">
			    <?php echo $this->Html->link('Back to home',array('controller'=>'users','action'=>'customerDashboard',$encrypted_storeId,$encrypted_merchantId),array('class'=>'home'));?>
	      </div>
<?php }else{?>

 <div class="rgt-side">
	       <div class="text-right clearfix">
           	<?php echo $this->Html->link('Back to home',array('controller'=>'users','action'=>'customerDashboard',$encrypted_storeId,$encrypted_merchantId),array('class'=>'home'));?>
	       </div>


<?php }?>