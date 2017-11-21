<!--Header Starts-->
	<section class="outer_header">
		<section class="ph-sectn">
			<!--<h3 itemprop="telephone">(515) 442-5246</h3>	  --> 
		</section>
	     <header class='header1'>
                <div class='user_info'>
                    <ul>
                        <li>Welcome:<?php echo ucfirst($_SESSION['Auth']['User']['fname']);?></li>
                        <li><?php echo $this->Html->link('Logout',array('controller'=>'stores','action'=>'logout'));?></li>
                        <li><?php
			
			$encrypted_storeId=$this->Encryption->encode($this->Session->read('store_id')); // Encrypted Store Id
                                    
                        $encrypted_merchantId=$this->Encryption->encode(AuthComponent::User('merchant_id'));// Encrypted Merchant Id
			
			echo $this->Html->link('My Profile',array('controller'=>'stores','action'=>'myProfile',$encrypted_storeId,$encrypted_merchantId));
			
			
			?></li>
                        <li><?php echo $this->Html->link('Manage Staff',array('controller'=>'stores','action'=>'manageStaff'));?></li>
                        <li><?php echo $this->Html->link('Staff List',array('controller'=>'stores','action'=>'staffList'));?></li>
			<li><?php echo $this->Html->link('Manage Images',array('controller'=>'stores','action'=>'manageSliderPhotos'));?></li>
			<li><?php echo $this->Html->link('Configuration',array('controller'=>'stores','action'=>'configuration'));?></li>
			<li><?php echo $this->Html->link('Store Timings',array('controller'=>'stores','action'=>'manageTimings'));?></li>
                    </ul>
                </div>	
	    </header>
	    </section>