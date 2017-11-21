<?php
//Store Admin
$staffManagementPermission = $this->Common->checkPermissionByTabName('Staff Management');
$manageImagesPermission = $this->Common->checkPermissionByTabName('Manage Images');
$configurationPermission = $this->Common->checkPermissionByTabName('Configuration');
$storeTimingsPermission = $this->Common->checkPermissionByTabName('Store Timings');
$timeIntervalsPermission = $this->Common->checkPermissionByTabName('Time Interval');
$categoryPermission = $this->Common->checkPermissionByTabName('Category');
$sizePermission = $this->Common->checkPermissionByTabName('Size');
$typePermission = $this->Common->checkPermissionByTabName('Type');
$AddonsPermission = $this->Common->checkPermissionByTabName('Add-ons');
$menuPermission = $this->Common->checkPermissionByTabName('Menu Builder');
$CouponPermission = $this->Common->checkPermissionByTabName('Coupon');
$customerManagementPermission = $this->Common->checkPermissionByTabName('Customer Management');
$orderManagementPermission = $this->Common->checkPermissionByTabName('Order Management');
$promotionsPermission = $this->Common->checkPermissionByTabName('Promotions');
$reviewPermission= $this->Common->checkPermissionByTabName('Review & Ratings');
$kitchenPermission = $this->Common->checkPermissionByTabName('Kitchen Management');
$BookingsPermission = $this->Common->checkPermissionByTabName('Bookings');
$transactionPermission = $this->Common->checkPermissionByTabName('Transaction');
$staticpagesPermission = $this->Common->checkPermissionByTabName('Static Pages');
$NewslettersPermission = $this->Common->checkPermissionByTabName('Newsletter');
$TemplatePermission = $this->Common->checkPermissionByTabName('Template');
$SocialMediaPermission = $this->Common->checkPermissionByTabName('Social Media');
//Hq Admin
$hqstaffManagementPermission = $this->Common->checkPermissionByTabName('Hq Staff Management');
$StoreRequest = $this->Common->checkPermissionByTabName('Request New Store');
$manageSliderPhotos = $this->Common->checkPermissionByTabName('Manage Images');
$hqTransaction = $this->Common->checkPermissionByTabName('HQ Transaction');
$hqStorePayments = $this->Common->checkPermissionByTabName('Payments');
$ReportPermission =  $this->Common->checkPermissionByTabName('Report');
$AddOnSizePermission = $this->Common->checkPermissionByTabName('Add-ons Size');
$SubAddOnPermission = $this->Common->checkPermissionByTabName('Sub Add-ons');
$hqReview = $this->Common->checkPermissionByTabName('HQ Reviews');
$hqStaticPages = $this->Common->checkPermissionByTabName('HQ Pages');
$hqReport = $this->Common->checkPermissionByTabName('HQ Reports');
$hqBacgroundImage = $this->Common->checkPermissionByTabName('HQ Background Image');
$hqstaticPage = $this->Common->checkPermissionByTabName('HQ Static Page');

//Super Admin
$superstaffManagementPermission = $this->Common->checkPermissionByTabName('Staff Management');
$supercustomerPermission =  $this->Common->checkPermissionByTabName('Customer Listing');
$supermerchantpaymentPermission =  $this->Common->checkPermissionByTabName('Payments');
$superequestStorePermission = $this->Common->checkPermissionByTabName('Store Requested');
$superconfigurationPermission = $this->Common->checkPermissionByTabName('Configuration');
$superreportPermission =  $this->Common->checkPermissionByTabName('Report');
$superaddmerchantPermission = $this->Common->checkPermissionByTabName('Add Merchant');
$superaddstorePermission = $this->Common->checkPermissionByTabName('Add Store');
$roleId=$this->Session->read('Auth.Admin.role_id');


?>

<div class="collapse navbar-collapse navbar-ex1-collapse">
    <?php if($roleId==3){?>             <!--Store Admin=3-->
    <ul class="nav navbar-nav side-nav">
    <?php if($staffManagementPermission==1){?>
     <li  <?php if($this->params['controller']=='stores' && ($this->params['action']=='manageStaff' || $this->params['action']=='staffList')){?> class="active" <?php }?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo9"><i class="fa fa-fw fa-user"></i>&nbsp; Manage Staff <i class="fa fa-fw fa-caret-down"></i></a>
     
     
            <ul id="demo9" class="collapse">
                <li><?php echo $this->Html->link(__('Add New Staff'), array('controller' => 'stores', 'action' => 'manageStaff'), array('escape' => false)); ?> </li> 
                <li><?php echo $this->Html->link(__('View Staff'), array('controller' => 'stores', 'action' => 'staffList'), array('escape' => false)); ?> </li>                  
            </ul>
        </li>
    <?php } ?>
    
    <?php if($manageImagesPermission==1){?>
     <li <?php if($this->params['controller']=='stores' && $this->params['action']=='manageSliderPhotos'){?>class="active"<?php }?>><?php echo $this->Html->link(__('<i class="fa fa-picture-o"></i>&nbsp;&nbsp; Manage Images <i class=""></i>'), array('controller' => 'stores', 'action' => 'manageSliderPhotos'), array('escape' => false)); ?></li>
     <?php } ?>
     
    <?php if($configurationPermission==1){?> 
      <li <?php if($this->params['controller']=='stores' && $this->params['action']=='configuration'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp; Configuration <i class=""></i>'), array('controller' => 'stores', 'action' => 'configuration'), array('escape' => false)); ?></li>
    <?php } ?>
    
    <?php if($storeTimingsPermission==1){?>   
      <li <?php if($this->params['controller']=='stores' && $this->params['action']=='manageTimings'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-clock-o"></i> &nbsp;Store Hours <i class=""></i>'), array('controller' => 'stores', 'action' => 'manageTimings'), array('escape' => false)); ?></li>       
    <?php } ?>
    
    <?php if($timeIntervalsPermission==1){?>      
      <li <?php if($this->params['controller']=='timeIntervals' && ($this->params['action']=='index' || $this->params['action']=='addTimeInterval' || $this->params['action']=='editTimeInterval')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo14"><i class="fa fa-asterisk"></i>&nbsp;&nbsp;Manage Categories<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo14" class="collapse">
		<li><?php echo $this->Html->link(__('Add Time-Interval'), array('controller' => 'timeIntervals', 'action' => 'addTimeInterval'), array('escape' => false)); ?> </li> 
               <li><?php echo $this->Html->link(__('View Time-Interval'), array('controller' => 'timeIntervals', 'action' => 'index'), array('escape' => false)); ?> </li>         
            </ul>
      </li>
    <?php } ?>
    
    <?php if($categoryPermission==1){?>      
      <li <?php if($this->params['controller']=='categories' && ($this->params['action']=='index' || $this->params['action']=='categoryList')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo14"><i class="fa fa-asterisk"></i>&nbsp;&nbsp;Manage Categories<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo14" class="collapse">
		<li><?php echo $this->Html->link(__('Add Categories'), array('controller' => 'categories', 'action' => 'index'), array('escape' => false)); ?> </li> 
               <li><?php echo $this->Html->link(__('View Categories'), array('controller' => 'categories', 'action' => 'categoryList'), array('escape' => false)); ?> </li>         
            </ul>
      </li>
    <?php } ?>
    
    <?php if($sizePermission==1){?>   
      <li <?php if($this->params['controller']=='sizes' && ($this->params['action']=='index' || $this->params['action']=='addSize')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo16"><i class="fa fa-asterisk"></i> &nbsp;Manage Sizes <i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo16" class="collapse">
		<li><?php echo $this->Html->link(__('Add Size'), array('controller' => 'sizes', 'action' => 'addSize'), array('escape' => false)); ?> </li> 
               <li><?php echo $this->Html->link(__('View Size'), array('controller' => 'sizes', 'action' => 'index'), array('escape' => false)); ?> </li>         
            </ul>
      </li>
    <?php } ?>
    
    
    
    <?php if($typePermission==1){?>   
       <li <?php if($this->params['controller']=='types' && ($this->params['action']=='index' || $this->params['action']=='addType')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo17"><i class="fa fa-asterisk"></i>&nbsp; Manage Preferences<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo17" class="collapse">
		<li><?php echo $this->Html->link(__('Add preferences'), array('controller' => 'types', 'action' => 'addType'), array('escape' => false)); ?> </li> 
               <li><?php echo $this->Html->link(__('View preferences'), array('controller' => 'types', 'action' => 'index'), array('escape' => false)); ?> </li>         
            </ul>
      </li>
    <?php } ?>
    
    
    
    
    
    
      
    
    <?php if($AddonsPermission==1){?>   
      <li  <?php if($this->params['controller']=='toppings' && ($this->params['action']=='index' || $this->params['action']=='addTopping')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo15"><i class="fa fa-asterisk"></i>&nbsp; Manage Add-ons <i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo15" class="collapse">
		<li><?php echo $this->Html->link(__('Add Add-ons'), array('controller' => 'toppings', 'action' => 'addTopping'), array('escape' => false)); ?> </li> 
               <li><?php echo $this->Html->link(__('View Add-ons'), array('controller' => 'toppings', 'action' => 'index'), array('escape' => false)); ?> </li>         
            </ul>
      </li>
    <?php } ?>
    <?php if($SubAddOnPermission==1){?>  
            <li <?php if($this->params['controller']=='toppings' && ($this->params['action']=='addSubTopping')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo40"><i class="fa fa-star"></i>&nbsp;&nbsp;Manage Sub Add-ons<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo40" class="collapse">
		<li><?php echo $this->Html->link(__('Add Sub Add-ons'), array('controller' => 'toppings', 'action' => 'addSubTopping'), array('escape' => false)); ?> </li>
              	<li><?php echo $this->Html->link(__('View Sub Add-ons'), array('controller' => 'toppings', 'action' => 'listSubTopping'), array('escape' => false)); ?> </li>

            </ul>
      </li>
     <?php } ?>
     <?php if($AddOnSizePermission==1){?> 
         
	  <li <?php if($this->params['controller']=='sizes' && ($this->params['action']=='createAddonSize')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo25"><i class="fa fa-star"></i>&nbsp;&nbsp;Manage Add-ons Size<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo25" class="collapse">
		<li><?php echo $this->Html->link(__('Add Add-ons Size'), array('controller' => 'sizes', 'action' => 'createAddonSize'), array('escape' => false)); ?> </li>
              	<li><?php echo $this->Html->link(__('View Add-ons Size'), array('controller' => 'sizes', 'action' => 'addOnSizeList'), array('escape' => false)); ?> </li>

            </ul>
      </li>
	 	 
	  <?php } ?>
    <?php if($menuPermission==1){?>   
      <li  <?php if($this->params['controller']=='items' && ($this->params['action']=='addMenuItem' || $this->params['action']=='index')){?> class="active" <?php }?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo13"><i class="fa fa-cutlery"></i>&nbsp; &nbsp;Menu Builder <i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo13" class="collapse">		
               <li><?php echo $this->Html->link(__('Add Menu Item'), array('controller' => 'items', 'action' => 'addMenuItem'), array('escape' => false)); ?> </li>
	       <li><?php echo $this->Html->link(__('View Items'), array('controller' => 'items', 'action' => 'index'), array('escape' => false)); ?> </li> 
            </ul>
      </li>
    <?php } ?>
    
    <?php if($CouponPermission==1){?>     
        <li <?php if($this->params['controller']=='coupons' && ($this->params['action']=='index' || $this->params['action']=='addCoupon')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo18"><i class="fa fa-asterisk"></i> &nbsp;Manage Coupons<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo18" class="collapse">
		<li><?php echo $this->Html->link(__('Add Coupon'), array('controller' => 'coupons', 'action' => 'addCoupon'), array('escape' => false)); ?> </li> 
               <li><?php echo $this->Html->link(__('View Coupon'), array('controller' => 'coupons', 'action' => 'index'), array('escape' => false)); ?> </li>         
            </ul>
      </li>
    <?php } ?>
    
    <?php if($customerManagementPermission==1){?> 
	<li <?php if($this->params['controller']=='customers' && $this->params['action']=='index'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Customer Management <i class=""></i>'), array('controller' => 'customers', 'action' => 'index'), array('escape' => false)); ?></li>
    <?php } ?>
    
    <?php if($orderManagementPermission==1){?> 	
	<li <?php if($this->params['controller']=='orders' && $this->params['action']=='index'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-shopping-cart"></i>&nbsp; Order Management <i class=""></i>'), array('controller' => 'orders', 'action' => 'index'), array('escape' => false)); ?></li>     
    <?php } ?>
    
    <?php if($promotionsPermission==1){?>       
       <li <?php if($this->params['controller']=='Offers' && ($this->params['action']=='index' || $this->params['action']=='addOffer')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo21"><i class="fa fa-star"></i>&nbsp;&nbsp;Promotions<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo21" class="collapse">
		<li><?php echo $this->Html->link(__('Add Promotions'), array('controller' => 'Offers', 'action' => 'addOffer'), array('escape' => false)); ?> </li>
		<li><?php echo $this->Html->link(__('View promotions'), array('controller' => 'Offers', 'action' => 'index'), array('escape' => false)); ?> </li> 
                        
            </ul>
      </li>
    <?php } ?>
    
    <?php if($reviewPermission==1){?>    
       	<li <?php if($this->params['controller']=='orders' && $this->params['action']=='reviewRating'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Review & Ratings<i class=""></i>'), array('controller' => 'orders', 'action' => 'reviewRating'), array('escape' => false)); ?></li>
    <?php } ?>
    
    <?php if($kitchenPermission==1){
	$kitchen=$this->Common->getKitchendisplayType();
	 if(isset($kitchen['Store']['kitchen_dashboard_type'])){	
	    if($kitchen['Store']['kitchen_dashboard_type']==1){
		$kitchenparam="listView";
	    }else{
		$kitchenparam="index";
	    }	
	}
	
    ?> 
    <li <?php if($this->params['controller']=='kitchens' && $this->params['action']=='index'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Kitchen Dashboard<i class=""></i>'), array('controller' => 'kitchens', 'action' => $kitchenparam), array('escape' => false)); ?></li>
    <?php } ?>
    
      <?php if($BookingsPermission==1){?> 
      <li <?php if($this->params['controller']=='bookings' && $this->params['action']=='index'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Manage Bookings<i class=""></i>'), array('controller' => 'bookings', 'action' => 'index'), array('escape' => false)); ?></li>     
      <?php } ?>
    <?php if($NewslettersPermission==1){?> 
    <li <?php if($this->params['controller']=='newsletters' && ($this->params['action']=='index')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo23"><i class="fa fa-star"></i>&nbsp;&nbsp;Newsletter<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo23" class="collapse">
		<li><?php echo $this->Html->link(__('Add Newsletter'), array('controller' => 'newsletters', 'action' => 'index'), array('escape' => false)); ?> </li>
              	<li><?php echo $this->Html->link(__('View Newsletter'), array('controller' => 'newsletters', 'action' => 'newsletterList'), array('escape' => false)); ?> </li>
           
            </ul>
      </li>
       <?php } ?>
      <?php if($staticpagesPermission==1){?> 
    	<li <?php if($this->params['controller']=='contents' && $this->params['action']=='pageList'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Static Pages<i class=""></i>'), array('controller' => 'contents', 'action' => 'pageList'), array('escape' => false)); ?></li>
    <?php } ?>
      
       <?php if($transactionPermission==1){?> 
    	<li <?php if($this->params['controller']=='payments' && $this->params['action']=='paymentList'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Transaction<i class=""></i>'), array('controller' => 'payments', 'action' => 'paymentList'), array('escape' => false)); ?></li>
    <?php } ?>
    
     <?php if($TemplatePermission==1){?> 
    	<li <?php if($this->params['controller']=='templates' && $this->params['action']=='index'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Manage Template<i class=""></i>'), array('controller' => 'templates', 'action' => 'index'), array('escape' => false)); ?></li>
    <?php } ?>
    <?php if($SocialMediaPermission==1){?>
        <li <?php if($this->params['controller']=='stores' && $this->params['action']=='socialMedia'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Social Media<i class=""></i>'), array('controller' => 'stores', 'action' => 'socialMedia'), array('escape' => false)); ?></li>
    <?php } ?>

    <?php if($ReportPermission==1){?>
    <li <?php if($this->params['controller']=='reports' && ($this->params['action']=='moneyReport')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo24"><i class="fa fa-star"></i>&nbsp;&nbsp;Reporting<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo24" class="collapse">
		<li><?php echo $this->Html->link(__('$ Report'), array('controller' => 'reports', 'action' => 'moneyReport'), array('escape' => false)); ?> </li>
              	<li><?php echo $this->Html->link(__('Product Report'), array('controller' => 'reports', 'action' => 'productReport'), array('escape' => false)); ?> </li>
            	<li><?php echo $this->Html->link(__('Order Report'), array('controller' => 'reports', 'action' => 'orderReport'), array('escape' => false)); ?> </li>
              	<li><?php echo $this->Html->link(__('Customer Report'), array('controller' => 'reports', 'action' => 'customerReport'), array('escape' => false)); ?> </li>
              
            </ul>
      </li>
   
    <?php } ?>
    
    
    </ul>
    

    <ul class="nav navbar-nav navbar-right navbar-user">        
        <li class="dropdown user-dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo ucfirst($_SESSION['Auth']['Admin']['fname']); ?> <b class="caret"></b></a>              
            <ul class="dropdown-menu">

                <li>

                </li>
                <li>

                    <?php echo $this->Html->link('Profile',array('controller'=>'stores','action'=>'myProfile')); ?>


                </li>



                <li class="divider"></li>
                <li>

                  	<?php echo $this->Html->link('Logout',array('controller'=>'stores','action'=>'logout'));?>
                </li>                
            </ul>
        </li>
    </ul>
    <!-- HQAdmin=2 -->
    <?php }elseif($roleId==2){
	?>		
		
    <ul class="nav navbar-nav side-nav">
    <?php if($hqstaffManagementPermission==1){?>
     <li  <?php if($this->params['controller']=='hq' && ($this->params['action']=='manageStaff' || $this->params['action']=='staffList')){?> class="active" <?php }?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo9"><i class="fa fa-fw fa-user"></i> Manage Staff <i class="fa fa-fw fa-caret-down"></i></a>
     
     
            <ul id="demo9" class="collapse">
                <li><?php echo $this->Html->link(__('Add New Staff'), array('controller' => 'hq', 'action' => 'manageStaff'), array('escape' => false)); ?> </li> 
                <li><?php echo $this->Html->link(__('View Staff'), array('controller' => 'hq', 'action' => 'staffList'), array('escape' => false)); ?> </li>                  
            </ul>
        </li>
    <?php } ?>
    
    <?php if($StoreRequest==1){?> 
    	<li <?php if($this->params['controller']=='hq' && $this->params['action']=='storeRequestList'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Request New Store<i class=""></i>'), array('controller' => 'hq', 'action' => 'storeRequestList'), array('escape' => false)); ?></li>
    <?php } ?>
    
     <?php if($hqStorePayments==1){?>    
    <li  <?php if($this->params['controller']=='hq' && ($this->params['action']=='addStorePayment' || $this->params['action']=='addStorePayment')){?> class="active" <?php }?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo10"><i class="fa fa-fw fa-user"></i> Payments<i class="fa fa-fw fa-caret-down"></i></a>
     
     
            <ul id="demo10" class="collapse">
		<li><?php echo $this->Html->link(__('Subscription Payment'), array('controller' => 'hq', 'action' => 'PaymentList'), array('escape' => false)); ?> </li>                  

                <li><?php echo $this->Html->link(__('Add Payment'), array('controller' => 'hq', 'action' => 'addStorePayment'), array('escape' => false)); ?> </li> 
                <li><?php echo $this->Html->link(__('View Payment'), array('controller' => 'hq', 'action' => 'storePaymentList'), array('escape' => false)); ?> </li>                  
            </ul>
    </li>
    
    
    <?php }
    ?>
    
     <?php if($hqstaticPage==1){?> 
    	<li <?php if($this->params['controller']=='hq' && $this->params['action']=='merchantPageList'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;HQ Static Pages<i class=""></i>'), array('controller' => 'hq', 'action' => 'merchantPageList'), array('escape' => false)); ?></li> 
    <?php  } ?>
    
    <?php
    if(isset($this->params['pass']) && $this->params['pass']){
	$selectedStoreId=$this->params['pass'][0];
    }
    
    $selectedStoreId=$this->Session->read('selectedStoreId');
    //$selectedStoreId = $this->Encryption->encode($selectedStoreId);
    ?>   
    
    <?php if($manageSliderPhotos==1 && $selectedStoreId){?> 
    	<li <?php if($this->params['controller']=='hq' && $this->params['action']=='manageSliderPhotos'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Manage Images<i class=""></i>'), array('controller' => 'hq', 'action' => 'manageSliderPhotos'), array('escape' => false)); ?></li>
    <?php } ?>
    <?php if($hqTransaction==1 && $selectedStoreId){?> 
    	<li <?php if($this->params['controller']=='hq' && $this->params['action']=='transactionList'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Transaction<i class=""></i>'), array('controller' => 'hq', 'action' => 'transactionList'), array('escape' => false)); ?></li>
    <?php } ?>
     <?php if($hqReview==1 && $selectedStoreId){?> 
       	<li <?php if($this->params['controller']=='hq' && $this->params['action']=='reviewRating'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Review & Ratings<i class=""></i>'), array('controller' => 'hq', 'action' => 'reviewRating'), array('escape' => false)); ?></li>
    <?php } ?>
     <?php if($hqStaticPages==1 && $selectedStoreId){?> 
       	<li <?php if($this->params['controller']=='hq' && $this->params['action']=='pageList'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Static Pages<i class=""></i>'), array('controller' => 'hq', 'action' => 'pageList'), array('escape' => false)); ?></li>
    <?php } ?>
     <?php if($hqReport==1 && $selectedStoreId){?> 
 
     <li <?php if($this->params['controller']=='hqreports' && ($this->params['action']=='moneyReport')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo50"><i class="fa fa-star"></i>&nbsp;&nbsp;Reporting<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo50" class="collapse">
		<li><?php echo $this->Html->link(__('$ Report'), array('controller' => 'hqreports', 'action' => 'moneyReport'), array('escape' => false)); ?> </li>
              	<li><?php echo $this->Html->link(__('Product Report'), array('controller' => 'hqreports', 'action' => 'productReport'), array('escape' => false)); ?> </li>
            	<li><?php echo $this->Html->link(__('Order Report'), array('controller' => 'hqreports', 'action' => 'orderReport'), array('escape' => false)); ?> </li>
              	<li><?php echo $this->Html->link(__('Customer Report'), array('controller' => 'hqreports', 'action' => 'customerReport'), array('escape' => false)); ?> </li>
              
            </ul>
      </li>
   

    <?php } ?>
    
     <?php if($hqBacgroundImage==1){?> 
       	<li <?php if($this->params['controller']=='hq' && $this->params['action']=='backgroundImage'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Background Image<i class=""></i>'), array('controller' => 'hq', 'action' => 'backgroundImage'), array('escape' => false)); ?></li>
    <?php } ?>
    
    </ul>    
    
    <ul class="nav navbar-nav navbar-right navbar-user">        
        <li class="dropdown user-dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo ucfirst($_SESSION['Auth']['Admin']['fname']); ?> <b class="caret"></b></a>              
            <ul class="dropdown-menu">
                <li></li>
                <li><?php echo $this->Html->link('Profile',array('controller'=>'hq','action'=>'myProfile')); ?></li>
                <li class="divider"></li>
                <li><?php echo $this->Html->link('Logout',array('controller'=>'hq','action'=>'logout'));?></li>                
            </ul>
        </li>
    </ul>
    <?php } elseif($roleId==1){
	?>		
		
    <ul class="nav navbar-nav side-nav">
    <?php if($superstaffManagementPermission==1){?>
     <li  <?php if($this->params['controller']=='super' && ($this->params['action']=='manageStaff' || $this->params['action']=='staffList')){?> class="active" <?php }?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo9"><i class="fa fa-fw fa-user"></i>&nbsp; Manage Staff <i class="fa fa-fw fa-caret-down"></i></a>
     
     
            <ul id="demo9" class="collapse">
                <li><?php echo $this->Html->link(__('Add New Staff'), array('controller' => 'super', 'action' => 'manageStaff'), array('escape' => false)); ?> </li> 
                <li><?php echo $this->Html->link(__('View Staff'), array('controller' => 'super', 'action' => 'staffList'), array('escape' => false)); ?> </li>                  
            </ul>
        </li>
    <?php } ?>   
           <?php if($supercustomerPermission==1){?> 
    	<li <?php if($this->params['controller']=='super' && $this->params['action']=='customerList'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Customer List<i class=""></i>'), array('controller' => 'super', 'action' => 'customerList'), array('escape' => false)); ?></li>
    <?php } ?>
    
        <?php if($supermerchantpaymentPermission==1){?>
	      
	<li  <?php if($this->params['controller']=='super' && ($this->params['action']=='merchantPaymentList' || $this->params['action']=='merchantPaymentList')){?> class="active" <?php }?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo30"><i class="fa fa-fw fa-user"></i>&nbsp;&nbsp;Payments<i class="fa fa-fw fa-caret-down"></i></a>
     
     
            <ul id="demo30" class="collapse">
                <li><?php echo $this->Html->link(__('Add Merchant Payment'), array('controller' => 'super', 'action' => 'addMerchantPayment'), array('escape' => false)); ?> </li> 
                <li><?php echo $this->Html->link(__('View Merchant Payment'), array('controller' => 'super', 'action' => 'merchantPaymentList'), array('escape' => false)); ?> </li>                  
            </ul>
        </li>
	<?php } ?>
	
	
	<?php if($superaddmerchantPermission==1){?>	      
	<li  <?php if($this->params['controller']=='super' && $this->params['action']=='addMerchant'){?> class="active" <?php }?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo31"><i class="fa fa-fw fa-user"></i>&nbsp;&nbsp;Merchant Management<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo31" class="collapse">
                <li><?php echo $this->Html->link(__('Add Merchant details'), array('controller' => 'super', 'action' => 'addMerchant'), array('escape' => false)); ?> </li> 
                <li><?php echo $this->Html->link(__('View Merchant details'), array('controller' => 'super', 'action' => 'viewMerchantDetails'), array('escape' => false)); ?> </li>                  
            </ul>
        </li>
	<?php } ?>
	
	<?php if($superaddstorePermission==1){?>	      
	<li  <?php if($this->params['controller']=='super' && $this->params['action']=='addStore'){?> class="active" <?php }?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo32"><i class="fa fa-fw fa-user"></i>&nbsp;&nbsp;Store Management<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo32" class="collapse">
                <li><?php echo $this->Html->link(__('Add Store details'), array('controller' => 'super', 'action' => 'addStore'), array('escape' => false)); ?> </li> 
                <li><?php echo $this->Html->link(__('View Store details'), array('controller' => 'super', 'action' => 'viewStoreDetails'), array('escape' => false)); ?> </li>                  
            </ul>
        </li>
	<?php } ?>
	
	<?php if($superequestStorePermission==1){?>
	
    	<li <?php if($this->params['controller']=='super' && $this->params['action']=='storeCreateList'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp; Requested Stores<i class=""></i>'), array('controller' => 'super', 'action' => 'storeCreateList'), array('escape' => false)); ?></li>

	<?php } ?>
	
	<?php if($superconfigurationPermission==1){?>
	
    	<li <?php if($this->params['controller']=='super' && $this->params['action']=='configuration'){?> class="active" <?php }?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Configuration<i class=""></i>'), array('controller' => 'super', 'action' => 'configuration'), array('escape' => false)); ?></li>

	<?php } ?>
	<?php if($superreportPermission==1){?>
	
	 <li <?php if($this->params['controller']=='superreports' && ($this->params['action']=='moneyReport')){?> class="active" <?php }?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo51"><i class="fa fa-star"></i>&nbsp;&nbsp;Reporting<i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo51" class="collapse">
		<li><?php echo $this->Html->link(__('$ Report'), array('controller' => 'superreports', 'action' => 'moneyReport'), array('escape' => false)); ?> </li>
              	<li><?php echo $this->Html->link(__('Product Report'), array('controller' => 'superreports', 'action' => 'productReport'), array('escape' => false)); ?> </li>
            	<li><?php echo $this->Html->link(__('Order Report'), array('controller' => 'superreports', 'action' => 'orderReport'), array('escape' => false)); ?> </li>
              	<li><?php echo $this->Html->link(__('Customer Report'), array('controller' => 'superreports', 'action' => 'customerReport'), array('escape' => false)); ?> </li>
              
            </ul>
      </li>
	<?php } ?>
    </ul>    
    
    <ul class="nav navbar-nav navbar-right navbar-user">        
        <li class="dropdown user-dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo ucfirst($_SESSION['Auth']['Admin']['fname']); ?> <b class="caret"></b></a>              
            <ul class="dropdown-menu">
                <li></li>
                <li><?php echo $this->Html->link('Profile',array('controller'=>'super','action'=>'myProfile')); ?></li>
                <li class="divider"></li>
                <li><?php echo $this->Html->link('Logout',array('controller'=>'super','action'=>'logout'));?></li>                
            </ul>
        </li>
    </ul>
    <?php } ?>
    
    
    
    
    
    
</div>
