<style>
    .lActive {
        background-color: #080808;
        padding: 4px;
    }
    li .lActive > a{
        color: #fff !important;
        text-decoration: none !important;
    }
    ul.side-nav>li>ul>li>a{
        text-decoration: none !important;
        color: #fff;
    }
</style>
<?php
$userId = $this->Session->read('Auth.Super.id');
$roleId = $this->Session->read('Auth.Super.role_id');
//Super Admin
$superstaffManagementPermission = $this->Common->checkPermissionByTabName('Staff Management', $userId, $roleId);
$supercustomerPermission = $this->Common->checkPermissionByTabName('Customer Listing', $userId, $roleId);
$supermerchantpaymentPermission = $this->Common->checkPermissionByTabName('Payments', $userId, $roleId);
$superequestStorePermission = $this->Common->checkPermissionByTabName('Store Requested', $userId, $roleId);
$superconfigurationPermission = $this->Common->checkPermissionByTabName('Configuration', $userId, $roleId);
$superreportPermission = $this->Common->checkPermissionByTabName('Report', $userId, $roleId);
$superaddmerchantPermission = $this->Common->checkPermissionByTabName('Add Merchant', $userId, $roleId);
$superaddstorePermission = $this->Common->checkPermissionByTabName('Add Store', $userId, $roleId);
?>

<div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav side-nav">
        <?php if ($superstaffManagementPermission == 1) { ?>
            <li  <?php if ($this->params['controller'] == 'super' && ($this->params['action'] == 'manageStaff')) { ?> class="active" <?php } ?>> <?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Manage Staff<i class=""></i>'), array('controller' => 'super', 'action' => 'manageStaff'), array('escape' => false)); ?>
                <!--<?php
                $aClass99 = "";
                if (in_array($this->params['controller'], array('super')) && in_array($this->params['action'], array('manageStaff', 'staffList'))) {
                    $aClass99 = "in";
                }
                ?>
                <ul id="demo9" class="collapse <?php echo $aClass99; ?>">
                    <li <?php if ($this->params['controller'] == 'super' && in_array($this->params['action'], array('manageStaff'))) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Add New Staff'), array('controller' => 'super', 'action' => 'manageStaff'), array('escape' => false)); ?> </li> 
                    <li <?php if ($this->params['controller'] == 'super' && in_array($this->params['action'], array('staffList'))) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View Staff'), array('controller' => 'super', 'action' => 'staffList'), array('escape' => false)); ?> </li>                  
                </ul>-->
            </li>
        <?php } ?>   
        <?php if ($supercustomerPermission == 1) { ?> 
            <li <?php if ($this->params['controller'] == 'super' && in_array($this->params['action'], array('customerList', 'editCustomer', 'orderHistory', 'orderDetail', 'reviewDetail', 'reservationDetail', 'customerOrderDetail'))) { ?> class="active" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Customer List<i class=""></i>'), array('controller' => 'super', 'action' => 'customerList'), array('escape' => false)); ?></li>
        <?php } ?>
        <?php if ($supermerchantpaymentPermission == 1) { ?>
            <li  <?php if ($this->params['controller'] == 'super' && ($this->params['action'] == 'addMerchantPayment' || $this->params['action'] == 'merchantPaymentList' || $this->params['action'] == 'updateMerchantPayment')) { ?> class="active" <?php } ?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo30"><i class="fa fa-fw fa-user"></i>&nbsp;&nbsp;Payments<i class="fa fa-fw fa-caret-down"></i></a>
                <?php
                $aClass30 = "";
                if (in_array($this->params['controller'], array('super')) && in_array($this->params['action'], array('addMerchantPayment', 'merchantPaymentList'))) {
                    $aClass30 = "in";
                }
                ?>
                <ul id="demo30" class="collapse <?php echo $aClass30; ?>">
                    <li <?php if ($this->params['controller'] == 'super' && ($this->params['action'] == 'addMerchantPayment')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Add Merchant Payment'), array('controller' => 'super', 'action' => 'addMerchantPayment'), array('escape' => false)); ?> </li> 
                    <li <?php if ($this->params['controller'] == 'super' && ($this->params['action'] == 'merchantPaymentList')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View Merchant Payment'), array('controller' => 'super', 'action' => 'merchantPaymentList'), array('escape' => false)); ?> </li>                  
                </ul>
            </li>
        <?php } ?>
        <?php if ($superaddmerchantPermission == 1) { ?>	      
            <li  <?php if ($this->params['controller'] == 'super' && in_array($this->params['action'], array('addMerchant', 'viewMerchantDetails', 'transaction_permission', 'editMerchant'))) { ?> class="active" <?php } ?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo31"><i class="fa fa-fw fa-user"></i>&nbsp;&nbsp;Merchant Management<i class="fa fa-fw fa-caret-down"></i></a>
                <?php
                $aClass31 = "";
                if (in_array($this->params['controller'], array('super')) && in_array($this->params['action'], array('addMerchant', 'viewMerchantDetails', 'transaction_permission'))) {
                    $aClass31 = "in";
                }
                ?>
                <ul id="demo31" class="collapse <?php echo $aClass31; ?>">
                    <li <?php if ($this->params['controller'] == 'super' && $this->params['action'] == 'addMerchant') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Add Merchant details'), array('controller' => 'super', 'action' => 'addMerchant'), array('escape' => false)); ?> </li> 
                    <li <?php if ($this->params['controller'] == 'super' && $this->params['action'] == 'viewMerchantDetails') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View Merchant details'), array('controller' => 'super', 'action' => 'viewMerchantDetails'), array('escape' => false)); ?> </li> 
                    <li <?php if ($this->params['controller'] == 'super' && $this->params['action'] == 'transaction_permission') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Transaction Permisssion'), array('controller' => 'super', 'action' => 'transaction_permission'), array('escape' => false)); ?> </li> 
                </ul>
            </li>
        <?php } ?>
        <?php if ($superaddstorePermission == 1) { ?>	      
            <li  <?php if (in_array($this->params['controller'], array('super')) && in_array($this->params['action'], array('addStore', 'viewStoreDetails', 'editStore','storeConfiguration'))) { ?> class="active" <?php } ?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo32"><i class="fa fa-fw fa-user"></i>&nbsp;&nbsp;Store Management<i class="fa fa-fw fa-caret-down"></i></a>
                <?php
                $aClass32 = "";
                if (in_array($this->params['controller'], array('super')) && in_array($this->params['action'], array('addStore', 'viewStoreDetails','storeConfiguration'))) {
                    $aClass32 = "in";
                }
                ?>
                <ul id="demo32" class="collapse <?php echo $aClass32; ?>">
                    <li <?php if ($this->params['controller'] == 'super' && $this->params['action'] == 'addStore') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Add Store details'), array('controller' => 'super', 'action' => 'addStore'), array('escape' => false)); ?> </li> 
                    <li <?php if ($this->params['controller'] == 'super' && in_array($this->params['action'] ,array('viewStoreDetails','storeConfiguration'))) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View Store details'), array('controller' => 'super', 'action' => 'viewStoreDetails'), array('escape' => false)); ?> </li>
                    <li <?php if ($this->params['controller'] == 'super' && in_array($this->params['action'] ,'viewStorePrinter')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View Store Printer'), array('controller' => 'super', 'action' => 'viewStorePrinter'), array('escape' => false)); ?> </li>
                </ul>
            </li>
        <?php } ?>
        <?php if ($superequestStorePermission == 1) { ?>
            <li <?php if ($this->params['controller'] == 'super' && $this->params['action'] == 'storeCreateList') { ?> class="active" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp; Requested Stores<i class=""></i>'), array('controller' => 'super', 'action' => 'storeCreateList'), array('escape' => false)); ?></li>
        <?php } ?>

        <?php if ($superconfigurationPermission == 1) { ?>
            <li <?php if ($this->params['controller'] == 'super' && ($this->params['action'] == 'configuration')) { ?> class="active" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp; Configuration'), array('controller' => 'super', 'action' => 'configuration'), array('escape' => false)); ?>
                <?php
//                $aClass61 = "";
//                if (in_array($this->params['controller'], array('super')) && in_array($this->params['action'], array('configuration'))) {
//                    $aClass61 = "in";
//                }
//                ?>
                <!--ul id="demo61" class="collapse //<?php echo $aClass61; ?>">
                    <li <?php if ($this->params['controller'] == 'super' && ($this->params['action'] == 'configuration')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Configuration'), array('controller' => 'super', 'action' => 'configuration'), array('escape' => false)); ?> </li> 
                    <li <?php if ($this->params['controller'] == 'super' && ($this->params['action'] == 'storeConfiguration')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Store Configuration'), array('controller' => 'super', 'action' => 'storeConfiguration'), array('escape' => false)); ?> </li>                  
                </ul-->

            </li>
        <?php } ?>

        <?php if ($superreportPermission == 1) { ?>
            <li <?php if($this->params['controller']=='superNewReports' && ($this->params['action']=='index')){?> class="active" <?php }?> > 
                        <?php echo $this->Html->link(__('Reports'), array('controller' => 'superNewReports', 'action' => 'index'), array('escape' => false)); ?>
                   </li>
            <!--li <?php if (in_array($this->params['controller'], array('superreports', 'superNewReports')) && in_array($this->params['action'], array('moneyReport', 'productReport', 'orderReport', 'customerReport', 'index'))) { ?> class="active" <?php } ?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo51"><i class="fa fa-star"></i>&nbsp;&nbsp;Reporting<i class="fa fa-fw fa-caret-down"></i></a>
                <?php
                $aClass51 = "";
                if (in_array($this->params['controller'], array('superreports')) && in_array($this->params['action'], array('moneyReport', 'productReport', 'orderReport', 'customerReport'))) {
                    $aClass51 = "in";
                }
                
                $aClass52 = "";
                if (in_array($this->params['controller'], array('superNewReports')) && in_array($this->params['action'], array('index'))) {
                    $aClass52 = "in";
                }
                ?>
                <ul id="demo51" class="collapse <?php echo $aClass51; ?>">
                    <li <?php if ($this->params['controller'] == 'superreports' && $this->params['action'] == 'moneyReport') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('$ Report'), array('controller' => 'superreports', 'action' => 'moneyReport'), array('escape' => false)); ?> </li>
                    <li <?php if ($this->params['controller'] == 'superreports' && $this->params['action'] == 'productReport') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Product Report'), array('controller' => 'superreports', 'action' => 'productReport'), array('escape' => false)); ?> </li>
                    <li <?php if ($this->params['controller'] == 'superreports' && $this->params['action'] == 'orderReport') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Order Report'), array('controller' => 'superreports', 'action' => 'orderReport'), array('escape' => false)); ?> </li>
                    <li <?php if ($this->params['controller'] == 'superreports' && $this->params['action'] == 'customerReport') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Customer Report'), array('controller' => 'superreports', 'action' => 'customerReport'), array('escape' => false)); ?> </li>
                    <li <?php if($this->params['controller']=='superNewReports' && ($this->params['action']=='index')){?> class="active" <?php }?> > 
                        <?php echo $this->Html->link(__('Reports'), array('controller' => 'superNewReports', 'action' => 'index'), array('escape' => false)); ?>
                   </li>
                </ul>
            </li!-->
        <?php } ?>
    </ul>    
    <ul class="nav navbar-nav navbar-right navbar-user">        
        <li class="dropdown user-dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo ucfirst($_SESSION['Auth']['Super']['fname']); ?> <b class="caret"></b></a>              
            <ul class="dropdown-menu">
                <li></li>
                <li><?php echo $this->Html->link('Profile', array('controller' => 'super', 'action' => 'myProfile')); ?></li>
                <li class="divider"></li>
                <li><?php echo $this->Html->link('Logout', array('controller' => 'super', 'action' => 'logout')); ?></li>                
            </ul>
        </li>
    </ul>
</div>
