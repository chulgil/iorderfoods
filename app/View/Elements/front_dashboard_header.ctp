  <div class="rgt-side">
	
        	<!-- header start here -->
	<?php if($this->params['controller']=='Products'&& $this->params['action']=='orderDetails'){?>

        		<?php echo $this->Html->link('Back to home',array('controller'=>'users','action'=>'customerDashboard',$encrypted_storeId,$encrypted_merchantId),array('class'=>'home'));?>
	<?php }else{?>
		<header class="clearfix">
            	
                <div class="banner">
                	&nbsp;
                </div>
                <div class="after-login-wrap clearfix">
			<ul>
				<li>Welcome:<?php echo $_SESSION['Auth']['User']['fname'];?></li>
				<li><?php echo $this->Html->link('Delivery Address',array('controller'=>'users','action'=>'deliveryAddress',$encrypted_storeId,$encrypted_merchantId));?></li>
				<li><?php echo $this->Html->link('Profile',array('controller'=>'users','action'=>'myProfile',$encrypted_storeId,$encrypted_merchantId));?></li>
<!--			        <li><?php //echo $this->Html->link('My Dashboard',array('controller'=>'users','action'=>'customerDashboard',$encrypted_storeId,$encrypted_merchantId));?></li>
-->
				<li><?php echo $this->Html->link('Place Order',array('controller'=>'users','action'=>'customerDashboard',$encrypted_storeId,$encrypted_merchantId));?></li>
				<li> <?php echo $this->Html->link(__('My Orders'),array('controller'=>'orders','action'=>'myOrders',$encrypted_storeId,$encrypted_merchantId));?></li>
                                <li><?php echo $this->Html->link(__('My Coupons'),array('controller'=>'coupons','action'=>'myCoupons',$encrypted_storeId,$encrypted_merchantId));?></li>
                
				 <li><?php echo $this->Html->link('Logout',array('controller'=>'users','action'=>'logout'));?></li>
			</ul>
		</div>
            </header><!-- /header end -->
	    <?php }?>
