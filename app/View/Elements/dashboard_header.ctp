<!--Header Starts-->
	<section class="outer_header">
		<section class="ph-sectn">
			<!--<h3 itemprop="telephone">(515) 442-5246</h3>	  --> 
		</section>
	     <header class='header1'>
                <div class='user_info'>
                    <ul>
                        <li>Welcome:<?php echo $_SESSION['Auth']['User']['fname'];?></li>
                        <li><?php echo $this->Html->link('Logout',array('controller'=>'users','action'=>'logout'));?></li>
                        <li><?php echo $this->Html->link('Profile',array('controller'=>'users','action'=>'myProfile',$encrypted_storeId,$encrypted_merchantId));?></li>
                        <li><?php echo $this->Html->link('Place Order',array('controller'=>'users','action'=>'customerDashboard',$encrypted_storeId,$encrypted_merchantId));?></li>
                        <li><?php echo $this->Html->link('Delivery Address',array('controller'=>'users','action'=>'deliveryAddress',$encrypted_storeId,$encrypted_merchantId));?></li>
                         <li><?php echo $this->Html->link('My Booking',array('controller'=>'users','action'=>'bookingStatus',$encrypted_storeId,$encrypted_merchantId));?></li>
			<li><?php echo $this->Html->link('My Dashboard',array('controller'=>'users','action'=>'customerDashboard',$encrypted_storeId,$encrypted_merchantId));?></li>
                    
		    </ul>
                </div>	
	    </header>
	    </section>