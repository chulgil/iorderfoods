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
$userId = $this->Session->read('Auth.hq.id');
$roleId = $this->Session->read('Auth.hq.role_id');
//Hq Admin
$hqstaffManagementPermission = $this->Common->checkPermissionByTabName('Hq Staff Management', $userId, $roleId);
$StoreRequest = $this->Common->checkPermissionByTabName('Request New Store', $userId, $roleId);
$manageSliderPhotos = $this->Common->checkPermissionByTabName('Manage Images', $userId, $roleId);
$hqTransaction = $this->Common->checkPermissionByTabName('HQ Transaction', $userId, $roleId);
$hqStorePayments = $this->Common->checkPermissionByTabName('Payments', $userId, $roleId);
$hqReview = $this->Common->checkPermissionByTabName('HQ Reviews', $userId, $roleId);
$hqStaticPages = $this->Common->checkPermissionByTabName('HQ Pages', $userId, $roleId);
$hqReport = $this->Common->checkPermissionByTabName('HQ Reports', $userId, $roleId);
$hqBacgroundImage = $this->Common->checkPermissionByTabName('HQ Background Image', $userId, $roleId);
$hqstaticPage = $this->Common->checkPermissionByTabName('HQ Static Page', $userId, $roleId);
$hqmanageImage = $this->Common->checkPermissionByTabName('HQ Manage Images', $userId, $roleId);
$hqmanageLocation = $this->Common->checkPermissionByTabName('Store Locations', $userId, $roleId);
$hqTransactionAllowPermission = $this->Common->checkTransactionAllowPermission($this->Session->read('merchantId'));
$hqmenuBuilder = $this->Common->checkPermissionByTabName('Hq Menu Builder', $userId, $roleId);
?>
<div class="collapse navbar-collapse navbar-ex1-collapse">				
    <ul class="nav navbar-nav side-nav">
        <!--sale-->
        <li> 
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo201">
                <i class="fa fa-shopping-cart"></i>&nbsp; Sales <i class="fa fa-fw fa-caret-down"></i>
            </a>
            <?php
            $aClass201 = "";
            if (($this->params['controller'] == 'hqorders' || $this->params['controller'] == 'hq') && ($this->params['action'] == 'index' || $this->params['action'] == 'transactionList')) {
                $aClass201 = "in";
            }
            ?>
            <ul id="demo201" class="collapse <?php echo $aClass201; ?>">
                <?php //if($hqstaffManagementPermission==1){   ?> 	
                <li <?php if ($this->params['controller'] == 'hqorders' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-shopping-cart"></i>&nbsp; Order Management <i class=""></i>'), array('controller' => 'hqorders', 'action' => 'index'), array('escape' => false)); ?></li>     
                <?php //}   ?>
                <?php if ($hqTransaction == 1 && $hqTransactionAllowPermission == 1 /* && $selectedStoreId */) { ?> 
                    <li <?php if ($this->params['controller'] == 'hq' && $this->params['action'] == 'transactionList') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Transaction<i class=""></i>'), array('controller' => 'hq', 'action' => 'transactionList'), array('escape' => false)); ?></li>
                <?php } ?>
            </ul>
        </li>
        <!--sale end-->

        <?php if ($hqstaffManagementPermission == 1) { ?>
            <!--Manage Staff-->
            <li>
            <li  <?php if ($this->params['controller'] == 'hq' && ($this->params['action'] == 'manageStaff')) { ?> class="active" <?php } ?>> <?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Manage Staff<i class=""></i>'), array('controller' => 'hq', 'action' => 'manageStaff'), array('escape' => false)); ?>
            </li>
            <!--Manage Staff End-->
        <?php } ?>

        <?php if ($StoreRequest == 1) { ?>
            <!--Request New Store-->
            <li <?php if ($this->params['controller'] == 'hq' && ($this->params['action'] == 'storeRequestList' || $this->params['action'] == 'requestNewStore')) { ?> class="active" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Request New Store<i class=""></i>'), array('controller' => 'hq', 'action' => 'storeRequestList'), array('escape' => false)); ?></li>
            <!--Request New Store End-->
        <?php } ?>

        <?php if ($hqStorePayments == 1) { ?> 
            <!--Payment-->
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo10"><i class="fa fa-fw fa-user"></i> Payments<i class="fa fa-fw fa-caret-down"></i>
                </a>
                <?php
                $aClass10 = "";
                if (($this->params['controller'] == 'hq') && ($this->params['action'] == 'PaymentList' || $this->params['action'] == 'addStorePayment' || $this->params['action'] == 'storePaymentList' )) {
                    $aClass10 = "in";
                }
                ?>
                <ul id="demo10" class="collapse <?php echo $aClass10; ?>">
                    <li<?php
                    if ($this->params['controller'] == 'hq' && ($this->params['action'] == 'PaymentList')) {
                        echo " class=lActive";
                    }
                    ?>>
                            <?php echo $this->Html->link(__('Subscription Payment'), array('controller' => 'hq', 'action' => 'PaymentList'), array('escape' => false)); ?>
                    </li>                  
                    <li<?php
                    if ($this->params['controller'] == 'hq' && ($this->params['action'] == 'addStorePayment')) {
                        echo " class=lActive";
                    }
                    ?>>
                            <?php echo $this->Html->link(__('Add Payment'), array('controller' => 'hq', 'action' => 'addStorePayment'), array('escape' => false)); ?>
                    </li> 
                    <li<?php
                    if ($this->params['controller'] == 'hq' && ($this->params['action'] == 'storePaymentList')) {
                        echo " class=lActive";
                    }
                    ?>>
                            <?php echo $this->Html->link(__('View Payment'), array('controller' => 'hq', 'action' => 'storePaymentList'), array('escape' => false)); ?>
                    </li>                  
                </ul>
            </li>
            <!--Payment End-->
        <?php }
        ?>
        <?php
        if (isset($this->params['pass']) && $this->params['pass']) {
            $selectedStoreId = $this->params['pass'][0];
        }

        //$selectedStoreId=$this->Session->read('selectedStoreId');
        //$selectedStoreId = $this->Encryption->encode($selectedStoreId);
        ?>   
        <!--Merchant Configuration-->    
        <li> 
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo13"><i class="fa fa-fw fa-user"></i> Merchant Configuration <i class="fa fa-fw fa-caret-down"></i>
            </a>
            <?php
            $aClass13 = "";
            if ((in_array($this->params['controller'], array('hq', 'hqtemplates', 'hqnewsletters', 'hqsettings', 'hqconfigurations'))) && (in_array($this->params['action'], array('merchantPageList', 'backgroundImage', 'merchantManageSliderPhotos', 'viewStoreDetails', 'index', 'socialMedia', 'manageTimings', 'enquiryMessages', 'merchant_design', 'addNewsletter', 'logoPosition', 'htmlModule', 'htmlLayout', 'saveTermsAndPolicies', 'homePageModal', 'editNewsletter')))) {
                $aClass13 = "in";
            }
            ?>
            <ul id="demo13" class="collapse <?php echo $aClass13; ?>">
                <li <?php if ($this->params['controller'] == 'hq' && $this->params['action'] == 'homePageModal') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-hand-o-right"></i>Home Page Modal'), array('controller' => 'hq', 'action' => 'homePageModal'), array('escape' => false)); ?></li>
                <?php if ($hqstaticPage == 1) { ?> 
                    <li <?php if ($this->params['controller'] == 'hq' && $this->params['action'] == 'merchantPageList') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-hand-o-right"></i>HQ Static Pages'), array('controller' => 'hq', 'action' => 'merchantPageList'), array('escape' => false)); ?></li> 
                <?php } ?>

                <?php if ($hqBacgroundImage == 1) { ?> 
                    <li <?php if ($this->params['controller'] == 'hq' && $this->params['action'] == 'backgroundImage') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-gear"></i>Configuration'), array('controller' => 'hq', 'action' => 'backgroundImage'), array('escape' => false)); ?></li>
                <?php } ?>

                <?php if ($hqmanageImage == 1) { ?> 
                    <li <?php if ($this->params['controller'] == 'hq' && $this->params['action'] == 'merchantManageSliderPhotos') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-file"></i>Manage Slider Images'), array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'), array('escape' => false)); ?></li>
                <?php } ?>
                <?php if ($hqmanageLocation == 1) { ?> 
                    <li <?php if ($this->params['controller'] == 'hq' && $this->params['action'] == 'viewStoreDetails') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-location-arrow"></i>Manage Store Locations'), array('controller' => 'hq', 'action' => 'viewStoreDetails'), array('escape' => false)); ?></li>
                <?php } ?>
                <li <?php if ($this->params['controller'] == 'hqtemplates' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-sitemap"></i>Email Templates'), array('controller' => 'hqtemplates', 'action' => 'index'), array('escape' => false)); ?></li>
                <li <?php if ($this->params['controller'] == 'hqsettings' && $this->params['action'] == 'socialMedia') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-hand-o-right"></i>Social Media'), array('controller' => 'hqsettings', 'action' => 'socialMedia'), array('escape' => false)); ?></li>
                <li <?php if ($this->params['controller'] == 'hqtemplates' && $this->params['action'] == 'enquiryMessages') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-hand-o-right"></i>Inquiry Messages'), array('controller' => 'hqtemplates', 'action' => 'enquiryMessages'), array('escape' => false)); ?></li>
                <li <?php if ($this->params['controller'] == 'hqnewsletters' && (in_array($this->params['action'], array('addNewsletter', 'editNewsletter')))) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-hand-o-right"></i>Newsletter'), array('controller' => 'hqnewsletters', 'action' => 'addNewsletter'), array('escape' => false)); ?></li>

                <li <?php if ($this->params['controller'] == 'hqtemplates' && $this->params['action'] == 'merchant_design') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-user"></i>Manage Css'), array('controller' => 'hqtemplates', 'action' => 'merchant_design'), array('escape' => false)); ?></li>

                <!--                Gallery Images Upload functionality Start-->
                <li <?php if ($this->params['controller'] == 'hqreports' && in_array($this->params['action'], array('orderHistory', 'index', 'editCustomer', 'orderDetail', 'reviewDetail', 'reservationDetail', 'customerOrderDetail', 'imageGallary'))) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Image Gallery<i class=""></i>'), array('controller' => 'hqreports', 'action' => 'imageGallary'), array('escape' => false)); ?></li>
                <!--                Gallery Images Upload functionality End -->

            </ul>
        </li>
        <!--Merchant Configuration End--> 
        <!--Store Setting--> 
        <li> 
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo11"><i class="fa fa-fw fa-user"></i> Store Setting <i class="fa fa-fw fa-caret-down"></i>
            </a>
            <?php
            $aClass11 = "";
            if (in_array($this->params['controller'], array('hq', 'hqstores', 'hqcoupons', 'hqnewsletters', 'hqfeatures', 'hqoffers', 'hqitemoffers', 'hqtoppings', 'hqintervals', 'hqcategories', 'hqsizes', 'hqtypes', 'hqsubpreferences', 'hqitems', 'hqnewsletters')) && in_array($this->params['action'], array('manageSliderPhotos', 'reviewRating', 'pageList', 'index', 'newsLetterAdd', 'customerList', 'editCoupon', 'editOffer', 'edit', 'newsLetterEdit', 'addOnSize', 'subTopping', 'typelisting', 'index', 'sizelisting', 'addMenuItem', 'editCategory', 'storeNewsletterList'))) {
                $aClass11 = "in";
            }
            ?>
            <ul id="demo11" class="collapse <?php echo $aClass11; ?>">
                <?php if ($manageSliderPhotos == 1 /* && $selectedStoreId */) { ?> 
                    <li <?php if ($this->params['controller'] == 'hq' && $this->params['action'] == 'manageSliderPhotos') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp;&nbsp;Manage Images<i class=""></i>'), array('controller' => 'hq', 'action' => 'manageSliderPhotos'), array('escape' => false)); ?></li>
                <?php } ?>
                <?php if ($hqReview == 1 /* && $selectedStoreId */) { ?> 
                    <li <?php if ($this->params['controller'] == 'hq' && $this->params['action'] == 'reviewRating') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Review & Ratings<i class=""></i>'), array('controller' => 'hq', 'action' => 'reviewRating'), array('escape' => false)); ?></li>
                <?php } ?>
                <?php if ($hqStaticPages == 1 /* && $selectedStoreId */) { ?> 
                    <li <?php if ($this->params['controller'] == 'hq' && $this->params['action'] == 'pageList') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Static Pages<i class=""></i>'), array('controller' => 'hq', 'action' => 'pageList'), array('escape' => false)); ?></li>
                <?php } ?>
                <li <?php if ($this->params['controller'] == 'hqstores' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Store Hours<i class=""></i>'), array('controller' => 'hqstores', 'action' => 'index'), array('escape' => false)); ?></li>
                <!--Manage Promotions-->
                <li> 
                    <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo19"><i class="fa fa-fw fa-user"></i> Manage Promotions <i class="fa fa-fw fa-caret-down"></i>
                    </a>
                    <?php
                    $aClass19 = "";
                    if (in_array($this->params['controller'], array('hqtoppings', 'hqcoupons', 'hqoffers', 'hqnewsletters', 'hqitemoffers', 'hqfeatures', 'hqnewsletters')) && in_array($this->params['action'], array('index', 'editCoupon', 'newsLetterAdd', 'customerList', 'featuredItemList', 'edit_features', 'shareCoupon', 'shareOffer', 'shareExtendedOffer', 'uploadfile', 'editOffer', 'edit', 'newsLetterEdit', 'storeNewsletterList'))) {
                        $aClass19 = "in";
                    }
                    ?>
                    <ul id="demo19" class="collapse <?php echo $aClass19; ?>">
                        <li <?php if (in_array($this->params['controller'], array('hqcoupons', 'hqoffers', 'hqitemoffers')) && (in_array($this->params['action'], array('index', 'editCoupon', 'shareCoupon', 'shareOffer', 'shareExtendedOffer', 'uploadfile', 'editOffer', 'edit', 'storeNewsletterList')))) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Deals'), array('controller' => 'hqcoupons', 'action' => 'index'), array('escape' => false)); ?> </li> 
                        <li> 
                            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo20">Newsletter <i class="fa fa-fw fa-caret-down"></i>
                            </a>
                            <?php
                            $aClass20 = "";
                            if (($this->params['controller'] == 'hqnewsletters') && in_array($this->params['action'], array('newsLetterAdd', 'customerList', 'newsLetterEdit', 'storeNewsletterList'))) {
                                $aClass20 = "in";
                            }
                            ?>
                            <ul id="demo20" class="collapse <?php echo $aClass20; ?>">
                                <li <?php if ($this->params['controller'] == 'hqnewsletters' && $this->params['action'] == 'newsLetterAdd') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Newsletter'), array('controller' => 'hqnewsletters', 'action' => 'newsLetterAdd'), array('escape' => false)); ?></li>
                                <li <?php if ($this->params['controller'] == 'hqnewsletters' && $this->params['action'] == 'customerList') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View Customer'), array('controller' => 'hqnewsletters', 'action' => 'customerList'), array('escape' => false)); ?></li>
<!--                                <li <?php if ($this->params['controller'] == 'hqnewsletters' && $this->params['action'] == 'storeNewsletterList') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-list"></i>Store Newsletters'), array('controller' => 'hqnewsletters', 'action' => 'storeNewsletterList'), array('escape' => false)); ?></li>-->
                            </ul>
                        </li>
                        <li <?php if ($this->params['controller'] == 'hqfeatures' && ($this->params['action'] == 'index' || $this->params['action'] == 'edit_features')) { ?> class="lActive" <?php } ?> > <?php echo $this->Html->link(__('Feature List'), array('controller' => 'hqfeatures', 'action' => 'index'), array('escape' => false)); ?>
                        </li>
                    </ul>
                </li>
                <!--Manage Promotions End-->
                <!--Menu Builder-->
                <li>
                    <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo102">
                        <i class="fa fa-cutlery"></i>&nbsp;&nbsp;Menu Builder<i class="fa fa-fw fa-caret-down"></i>
                    </a>
                    <?php
                    $aClass102 = "";
                    if (in_array($this->params['controller'], array('hqtoppings', 'hqintervals', 'hqcategories', 'hqsizes', 'hqtypes', 'hqsubpreferences', 'hqitems')) && in_array($this->params['action'], array('addOnSize', 'subTopping', 'typelisting', 'index', 'sizelisting', 'addMenuItem', 'editCategory'))) {
                        $aClass102 = "in";
                    }
                    ?>
                    <ul id="demo102" class="collapse <?php echo $aClass102; ?>">
                        <li <?php if ($this->params['controller'] == 'hqcategories' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Category<i class=""></i>'), array('controller' => 'hqcategories', 'action' => 'index'), array('escape' => false)); ?></li>
                        <li> 
                            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo14"><i class="fa fa-fw fa-user"></i> Manage Sizes <i class="fa fa-fw fa-caret-down"></i>
                            </a>
                            <?php
                            $aClass14 = "";
                            if (($this->params['controller'] == 'hqsizes') && in_array($this->params['action'], array('index', 'sizelisting'))) {
                                $aClass14 = "in";
                            }
                            ?>
                            <ul id="demo14" class="collapse <?php echo $aClass14; ?>">
                                <li <?php if ($this->params['controller'] == 'hqsizes' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; View Size<i class=""></i>'), array('controller' => 'hqsizes', 'action' => 'index'), array('escape' => false)); ?></li>
                                <li <?php if ($this->params['controller'] == 'hqsizes' && $this->params['action'] == 'sizelisting') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Item Sizes<i class=""></i>'), array('controller' => 'hqsizes', 'action' => 'sizelisting'), array('escape' => false)); ?></li>
                            </ul>
                        </li>
                        <li> 
                            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo15"><i class="fa fa-fw fa-user"></i> Manage Preferences <i class="fa fa-fw fa-caret-down"></i>
                            </a>
                            <?php
                            $aClass15 = "";
                            if (($this->params['controller'] == 'hqtypes') && in_array($this->params['action'], array('typelisting', 'index'))) {
                                $aClass15 = "in";
                            }
                            ?>
                            <ul id="demo15" class="collapse <?php echo $aClass15; ?>">
                                <li <?php if ($this->params['controller'] == 'hqtypes' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Add Preferences<i class=""></i>'), array('controller' => 'hqtypes', 'action' => 'index'), array('escape' => false)); ?></li>
                                <li <?php if ($this->params['controller'] == 'hqtypes' && $this->params['action'] == 'typelisting') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Item Preferences<i class=""></i>'), array('controller' => 'hqtypes', 'action' => 'typelisting'), array('escape' => false)); ?></li>
                            </ul>
                        </li>
                        <li <?php if ($this->params['controller'] == 'hqsubpreferences' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Sub-Preferences<i class=""></i>'), array('controller' => 'hqsubpreferences', 'action' => 'index'), array('escape' => false)); ?></li>
                        <li> 
                            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo16"><i class="fa fa-fw fa-user"></i> Menu Builder <i class="fa fa-fw fa-caret-down"></i>
                            </a>
                            <?php
                            $aClass16 = "";
                            if (($this->params['controller'] == 'hqitems') && in_array($this->params['action'], array('addMenuItem', 'index'))) {
                                $aClass16 = "in";
                            }
                            ?>
                            <ul id="demo16" class="collapse <?php echo $aClass16; ?>">
                                <li <?php if ($this->params['controller'] == 'hqitems' && $this->params['action'] == 'addMenuItem') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Add Menu Item<i class=""></i>'), array('controller' => 'hqitems', 'action' => 'addMenuItem'), array('escape' => false)); ?></li>
                                <li <?php if ($this->params['controller'] == 'hqitems' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; View Items<i class=""></i>'), array('controller' => 'hqitems', 'action' => 'index'), array('escape' => false)); ?></li>
                            </ul>
                        </li>
                        <li <?php if ($this->params['controller'] == 'hqtoppings' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Add-ons<i class=""></i>'), array('controller' => 'hqtoppings', 'action' => 'index'), array('escape' => false)); ?></li>
                        <li <?php if ($this->params['controller'] == 'hqtoppings' && $this->params['action'] == 'subTopping') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Sub Add-ons<i class=""></i>'), array('controller' => 'hqtoppings', 'action' => 'subTopping'), array('escape' => false)); ?></li>
			<li <?php if ($this->params['controller'] == 'hqintervals' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Time Interval<i class=""></i>'), array('controller' => 'hqintervals', 'action' => 'index'), array('escape' => false)); ?></li>
                    </ul>
                </li>
            </ul>
        </li>
        <!--Store Setting End--> 
        
        <!--Report-->
        <li>
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo190"><i class="fa fa-fw fa-user"></i> Report <i class="fa fa-fw fa-caret-down"></i>
            </a>
            <?php
            $aClass190 = "";
            if (in_array($this->params['controller'], array('hqsalesreports', 'hqreports', 'hqcustomers')) && in_array($this->params['action'], array('moneyReport', 'productReport', 'customerReport', 'orderReport', 'index', 'orderHistory', 'editCustomer', 'orderDetail', 'reviewDetail', 'reservationDetail', 'customerOrderDetail'))) {
                $aClass190 = "in";
            }
            ?>
            <ul id="demo190" class="collapse <?php echo $aClass190; ?>">
                <li <?php if ($this->params['controller'] == 'hqcustomers' && in_array($this->params['action'], array('orderHistory', 'index', 'editCustomer', 'orderDetail', 'reviewDetail', 'reservationDetail', 'customerOrderDetail'))) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-user"></i>&nbsp; Customer Management<i class=""></i>'), array('controller' => 'hqcustomers', 'action' => 'index'), array('escape' => false)); ?></li>
                <!--New Reporting Section Start-->
                <li <?php if ($this->params['controller'] == 'hqsalesreports' && ($this->params['action'] == 'index')) { ?> class="lActive" <?php } ?> >
                    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-star')) . '&nbsp;&nbsp;' . __('Reports'), array('controller' => 'hqsalesreports', 'action' => 'index'), array('escape' => false)); ?></li>
                <!--New Reporting Section End-->
            </ul>
        </li>
        <!--Report End-->
        
    </ul>    
    <ul class="nav navbar-nav navbar-right navbar-user">        
        <li class="dropdown user-dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo ucfirst($_SESSION['Auth']['hq']['fname']); ?> <b class="caret"></b></a>              
            <ul class="dropdown-menu">
                <li></li>
                <li><?php echo $this->Html->link('Profile', array('controller' => 'hq', 'action' => 'myProfile')); ?></li>
                <li class="divider"></li>
                <li><?php echo $this->Html->link('Logout', array('controller' => 'hq', 'action' => 'logout')); ?></li>                
            </ul>
        </li>
    </ul>
</div>
