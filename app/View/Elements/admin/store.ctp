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
$userId = $this->Session->read('Auth.Admin.id');
$roleId = $this->Session->read('Auth.Admin.role_id');

//Store Admin
$staffManagementPermission = $this->Common->checkPermissionByTabName('Staff Management', $userId, $roleId);
$manageImagesPermission = $this->Common->checkPermissionByTabName('Manage Images', $userId, $roleId);
$configurationPermission = $this->Common->checkPermissionByTabName('Configuration', $userId, $roleId);
$storeTimingsPermission = $this->Common->checkPermissionByTabName('Store Timings', $userId, $roleId);
$intervalsPermission = $this->Common->checkPermissionByTabName('Time Interval', $userId, $roleId);
$categoryPermission = $this->Common->checkPermissionByTabName('Category', $userId, $roleId);
$sizePermission = $this->Common->checkPermissionByTabName('Size', $userId, $roleId);
$typePermission = $this->Common->checkPermissionByTabName('Type', $userId, $roleId);
$AddonsPermission = $this->Common->checkPermissionByTabName('Add-ons', $userId, $roleId);
$menuPermission = $this->Common->checkPermissionByTabName('Menu Builder', $userId, $roleId);
$CouponPermission = $this->Common->checkPermissionByTabName('Coupon', $userId, $roleId);
$customerManagementPermission = $this->Common->checkPermissionByTabName('Customer Management', $userId, $roleId);
$orderManagementPermission = $this->Common->checkPermissionByTabName('Order Management', $userId, $roleId);
$promotionsPermission = $this->Common->checkPermissionByTabName('Promotions', $userId, $roleId);
$reviewPermission = $this->Common->checkPermissionByTabName('Review & Ratings', $userId, $roleId);
$kitchenPermission = $this->Common->checkPermissionByTabName('Kitchen Management', $userId, $roleId);
$BookingsPermission = $this->Common->checkPermissionByTabName('Bookings', $userId, $roleId);
$transactionPermission = $this->Common->checkPermissionByTabName('Transaction', $userId, $roleId);
$staticpagesPermission = $this->Common->checkPermissionByTabName('Static Pages', $userId, $roleId);
$NewslettersPermission = $this->Common->checkPermissionByTabName('Newsletter', $userId, $roleId);
$TemplatePermission = $this->Common->checkPermissionByTabName('Template', $userId, $roleId);
$SocialMediaPermission = $this->Common->checkPermissionByTabName('Social Media', $userId, $roleId);
$SubAddOnPermission = $this->Common->checkPermissionByTabName('Sub Add-ons', $userId, $roleId);
$ReportPermission = $this->Common->checkPermissionByTabName('Report', $userId, $roleId);
$SubPreferencePermission = $this->Common->checkPermissionByTabName('Sub Preference', $userId, $roleId);
$ItemOfferPermission = $this->Common->checkPermissionByTabName('Item Offers', $userId, $roleId);
//$specialDayPermission = $this->Common->checkPermissionByTabName('Special day', $userId, $roleId);
?>
<div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav side-nav">
        <li> 
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo99"><i class="fa fa-shopping-cart"></i>&nbsp; Sales <i class="fa fa-fw fa-caret-down"></i>
            </a>
            <?php
            $aClass99 = "";
            if (in_array($this->params['controller'], array('orders', 'kitchens', 'bookings')) && in_array($this->params['action'], array('index', 'listView'))) {
                $aClass99 = "in";
            }
            ?>
            <ul id="demo99" class="collapse <?php echo $aClass99; ?>">
                <?php if ($orderManagementPermission == 1) { ?> 	
                    <li <?php if ($this->params['controller'] == 'orders' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-shopping-cart"></i>&nbsp; Order Management <i class=""></i>'), array('controller' => 'orders', 'action' => 'index'), array('escape' => false)); ?></li>     
                <?php } ?>
                <?php
                if ($kitchenPermission == 1 && $modulePermission['ModulePermission']['kitchen_dashboard_allow']) {
                    $kitchen = $this->Common->getKitchendisplayType();
                    if (isset($kitchen['Store']['kitchen_dashboard_type'])) {
                        if ($kitchen['Store']['kitchen_dashboard_type'] == 1) {
                            $kitchenparam = "listView";
                        } else {
                            $kitchenparam = "index";
                        }
                    }
                    ?>
                    <li <?php if ($this->params['controller'] == 'kitchens' && in_array($this->params['action'], array('index', 'listView'))) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;Kitchen Dashboard<i class=""></i>'), array('controller' => 'kitchens', 'action' => $kitchenparam), array('escape' => false)); ?></li>
                <?php } ?>
                <?php if ($BookingsPermission == 1) { ?> 
                    <li <?php if ($this->params['controller'] == 'bookings' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;Dine-In<i class=""></i>'), array('controller' => 'bookings', 'action' => 'index'), array('escape' => false)); ?></li>     
                <?php } ?>
            </ul>
        </li>

        <li> 
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo100"><i class="fa fa-file-text"></i>&nbsp; Report <i class="fa fa-fw fa-caret-down"></i>
            </a>
            <?php
            $aClass100 = "";
            if (in_array($this->params['controller'], array('reports', 'payments', 'orders', 'storeReports')) && in_array($this->params['action'], array('imageGallary', 'reviewRating', 'paymentList', 'moneyReport', 'orderReport', 'productReport', 'customerReport', 'index','storeReports'))) {
                $aClass100 = "in";
            }
            if (in_array($this->params['controller'], array('customers')) && in_array($this->params['action'], array('index', 'editCustomer', 'orderHistory', 'orderDetail', 'reviewDetail', 'reservationDetail', 'customerOrderDetail'))) {
                $aClass100 = "in";
            }
            ?>
            <ul id="demo100" class="collapse <?php echo $aClass100; ?>">

                <?php if ($ReportPermission == 1) { ?>
                    <!--li <?php if ($this->params['controller'] == 'reports' && ($this->params['action'] == 'moneyReport')) { ?> class="active" <?php } ?> >
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo24"><i class="fa fa-file-text"></i>&nbsp;&nbsp;Reporting<i class="fa fa-fw fa-caret-down"></i>
                        </a>
                        <?php
                        $aClass24 = "";
                        if (in_array($this->params['controller'], array('reports')) && in_array($this->params['action'], array('orderReport', 'moneyReport', 'productReport', 'customerReport'))) {
                            $aClass24 = "in";
                        }
                        ?>
                        <ul id="demo24" class="collapse <?php echo $aClass24; ?>">
                            <li <?php if ($this->params['controller'] == 'reports' && ($this->params['action'] == 'moneyReport')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('$ Report'), array('controller' => 'reports', 'action' => 'moneyReport'), array('escape' => false)); ?> </li>
                            <li <?php if ($this->params['controller'] == 'reports' && ($this->params['action'] == 'productReport')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Product Report'), array('controller' => 'reports', 'action' => 'productReport'), array('escape' => false)); ?> </li>
                            <li <?php if ($this->params['controller'] == 'reports' && ($this->params['action'] == 'orderReport')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Order Report'), array('controller' => 'reports', 'action' => 'orderReport'), array('escape' => false)); ?> </li>
                            <li <?php if ($this->params['controller'] == 'reports' && ($this->params['action'] == 'customerReport')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Customer Report'), array('controller' => 'reports', 'action' => 'customerReport'), array('escape' => false)); ?> </li>

                        </ul>
                    </li!-->
                <?php } ?>

                <?php if ($transactionPermission == 1) { ?> 
                    <li <?php if ($this->params['controller'] == 'payments' && $this->params['action'] == 'paymentList') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-file-text"></i>&nbsp;&nbsp;Transaction<i class=""></i>'), array('controller' => 'payments', 'action' => 'paymentList'), array('escape' => false)); ?></li>
                <?php } ?>

                <?php if ($reviewPermission == 1) { ?>    
                    <li <?php if ($this->params['controller'] == 'orders' && $this->params['action'] == 'reviewRating') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-file-text"></i>&nbsp; Review & Ratings<i class=""></i>'), array('controller' => 'orders', 'action' => 'reviewRating'), array('escape' => false)); ?></li>
                <?php } ?>   

                <?php if ($customerManagementPermission == 1) { ?> 
                    <li <?php if ($this->params['controller'] == 'customers' && in_array($this->params['action'], array('orderHistory', 'index', 'editCustomer', 'orderDetail', 'reviewDetail', 'reservationDetail', 'customerOrderDetail'))) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-file-text"></i>&nbsp; Customer Management <i class=""></i>'), array('controller' => 'customers', 'action' => 'index'), array('escape' => false)); ?></li>
                <?php } ?>
                <?php if ($reviewPermission == 1) { ?> 
                    <li <?php if ($this->params['controller'] == 'reports' && $this->params['action'] == 'imageGallary') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-file-text"></i>&nbsp;&nbsp;Image Gallery<i class=""></i>'), array('controller' => 'reports', 'action' => 'imageGallary'), array('escape' => false)); ?></li>
                <?php } ?>
                    
                    
                <?php if ($ReportPermission == 1) { ?>
                    <li <?php if ($this->params['controller'] == 'storeReports' && ($this->params['action'] == 'index')) { ?> class="lActive" <?php } ?> >
                        
                            <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-file-text')) . __('&nbsp;&nbsp;Reports'), array('controller' => 'storeReports', 'action' => 'index'), array('escape' => false)); ?>
                        
                        
                    </li>
                <?php } ?>

            </ul>
        </li>           



        <li> 
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo101"><i class="fa fa-star"></i>&nbsp; Promotions <i class="fa fa-fw fa-caret-down"></i>
            </a>
            <?php
            $aClass101 = "";
            if (in_array($this->params['controller'], array('coupons', 'Offers', 'offers', 'newsletters', 'itemOffers', 'features')) && in_array($this->params['action'], array('addCoupon', 'editCoupon', 'index', 'reviewRating', 'paymentList', 'moneyReport', 'orderReport', 'productReport', 'customerReport', 'addOffer', 'customerList', 'customerList', 'add', 'editOffer', 'edit', 'featuredItemList', 'edit_features', 'couponUsedList', 'uploadfile'))) {
                $aClass101 = "in";
            }
            ?>
            <ul id="demo101" class="collapse <?php echo $aClass101; ?>"> 
                <?php if ($CouponPermission == 1) { ?>     
                    <li <?php if (in_array($this->params['controller'], array('coupons', 'offers', 'itemOffers')) && in_array($this->params['action'], array('addCoupon', 'editCoupon', 'addOffer', 'editOffer', 'uploadfile', 'add', 'edit'))) { ?> class="lActive" <?php } ?> > <?php echo $this->Html->link(__('Deals'), array('controller' => 'coupons', 'action' => 'addCoupon'), array('escape' => false)); ?>
                    </li>
                <?php } ?>
                <?php if ($promotionsPermission == 1) { ?>       
                    <!--li <?php if ($this->params['controller'] == 'offers' && ($this->params['action'] == 'editOffer' || $this->params['action'] == 'addOffer')) { ?> class="lActive" <?php } ?> > <?php echo $this->Html->link(__('Promotions'), array('controller' => 'offers', 'action' => 'addOffer'), array('escape' => false)); ?>-->
                    <!--<?php
                    $aClass21 = "";
                    if (in_array($this->params['controller'], array('Offers')) && in_array($this->params['action'], array('addOffer', 'index'))) {
                        $aClass21 = "in";
                    }
                    ?>
                    <ul id="demo21" class="collapse <?php echo $aClass21; ?>">
                        <li <?php if ($this->params['controller'] == 'Offers' && $this->params['action'] == 'addOffer') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Add Promotions'), array('controller' => 'Offers', 'action' => 'addOffer'), array('escape' => false)); ?> </li>
                        <li <?php if ($this->params['controller'] == 'Offers' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View promotions'), array('controller' => 'Offers', 'action' => 'index'), array('escape' => false)); ?> </li> 
                    </ul>-->
            </li>
        <?php } ?>
        <?php if ($NewslettersPermission == 1) { ?> 
            <li <?php if ($this->params['controller'] == 'newsletters' && ($this->params['action'] == 'index')) { ?> class="active" <?php } ?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo23">Newsletter<i class="fa fa-fw fa-caret-down"></i></a>
                <?php
                $aClass23 = "";
                if (in_array($this->params['controller'], array('newsletters')) && in_array($this->params['action'], array('customerList', 'index'))) {
                    $aClass23 = "in";
                }
                ?>
                <ul id="demo23" class="collapse <?php echo $aClass23; ?>">
                    <li <?php if ($this->params['controller'] == 'newsletters' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Add/List Newsletter'), array('controller' => 'newsletters', 'action' => 'index'), array('escape' => false)); ?> </li>
    <!--                                <li><?php echo $this->Html->link(__('View Newsletter'), array('controller' => 'newsletters', 'action' => 'newsletterList'), array('escape' => false)); ?> </li>-->
                    <li <?php if ($this->params['controller'] == 'newsletters' && $this->params['action'] == 'customerList') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View Customer'), array('controller' => 'newsletters', 'action' => 'customerList'), array('escape' => false)); ?> </li>

                    <li <?php if ($this->params['controller'] == 'newsletters' && $this->params['action'] == 'special_day') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Special day'), array('controller' => 'newsletters', 'action' => 'special_day'), array('escape' => false)); ?> </li>

                </ul>
            </li>
        <?php } ?> 

        <!--<?php if ($ItemOfferPermission == 1) { ?>     
                    <li <?php if ($this->params['controller'] == 'itemOffers') { ?> class="lActive" <?php } ?> > <?php echo $this->Html->link(__('Extended Offers'), array('controller' => 'itemOffers', 'action' => 'add'), array('escape' => false)); ?>
                    </li>--> 
        <?php } ?>     
        <li <?php if ($this->params['controller'] == 'features' && ($this->params['action'] == 'index' || $this->params['action'] == 'edit_features')) { ?> class="lActive" <?php } ?> > <?php echo $this->Html->link(__('Feature List'), array('controller' => 'features', 'action' => 'index'), array('escape' => false)); ?>
        </li>
<!--                <li <?php if ($this->params['controller'] == 'features' && $this->params['action'] == 'featuredItemList') { ?> class="lActive" <?php } ?> > <?php echo $this->Html->link(__('Feature Item List'), array('controller' => 'features', 'action' => 'featuredItemList'), array('escape' => false)); ?>
        </li>-->
    </ul>
</li>

<li>
    <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo102">
        <i class="fa fa-cutlery"></i>&nbsp;&nbsp;Menu Builder<i class="fa fa-fw fa-caret-down"></i></a>
    <?php
    $aClass102 = "";
    if (in_array($this->params['controller'], array('intervals', 'categories', 'sizes', 'types', 'SubPreferences', 'items', 'toppings')) && in_array($this->params['action'], array('index', 'sizelisting', 'pageList', 'typelisting', 'addMenuItem', 'listSubTopping', 'addOnSizeList', 'intervals', 'editMenuItem'))) {
        $aClass102 = "in";
    }
    ?>
    <ul id="demo102" class="collapse <?php echo $aClass102; ?>">
        <?php if ($categoryPermission == 1) { ?>      
            <li <?php if ($this->params['controller'] == 'categories' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-cutlery"></i>&nbsp;&nbsp;Category<i class=""></i>'), array('controller' => 'categories', 'action' => 'index'), array('escape' => false)); ?></li> 
        <?php } ?>
        <?php if ($sizePermission == 1) { ?>   
            <li <?php if ($this->params['controller'] == 'sizes' && ($this->params['action'] == 'index' || $this->params['action'] == 'addSize')) { ?> class="active" <?php } ?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo16"><i class="fa fa-cutlery"></i> &nbsp;Manage Sizes <i class="fa fa-fw fa-caret-down"></i></a>
                <?php
                $aClass16 = "";
                if (in_array($this->params['controller'], array('sizes')) && in_array($this->params['action'], array('sizelisting', 'index'))) {
                    $aClass16 = "in";
                }
                ?>
                <ul id="demo16" class="collapse <?php echo $aClass16; ?>">
                    <li <?php if ($this->params['controller'] == 'sizes' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>>
                        <?php echo $this->Html->link(__('View Size'), array('controller' => 'sizes', 'action' => 'index'), array('escape' => false)); ?>
                    </li>
                    <li <?php if ($this->params['controller'] == 'sizes' && $this->params['action'] == 'sizelisting') { ?> class="lActive" <?php } ?>>
                        <?php echo $this->Html->link(__('Item sizes'), array('controller' => 'sizes', 'action' => 'sizelisting'), array('escape' => false)); ?>
                    </li>
                </ul>
            </li>
        <?php } ?>
        <?php if ($typePermission == 1) { ?>   
            <li <?php if ($this->params['controller'] == 'types' && ($this->params['action'] == 'index' || $this->params['action'] == 'addType')) { ?> class="active" <?php } ?> > <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo17"><i class="fa fa-cutlery"></i>&nbsp; Manage Preferences<i class="fa fa-fw fa-caret-down"></i></a>
                <?php
                $aClass17 = "";
                if (in_array($this->params['controller'], array('types')) && in_array($this->params['action'], array('typelisting', 'index'))) {
                    $aClass17 = "in";
                }
                ?>
                <ul id="demo17" class="collapse <?php echo $aClass17; ?>">
                    <li <?php if ($this->params['controller'] == 'types' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View preferences'), array('controller' => 'types', 'action' => 'index'), array('escape' => false)); ?> </li>
                    <li <?php if ($this->params['controller'] == 'types' && $this->params['action'] == 'typelisting') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Item preferences'), array('controller' => 'types', 'action' => 'typelisting'), array('escape' => false)); ?> </li>  
                </ul>
            </li>
        <?php } ?>
        <?php if ($SubPreferencePermission == 1) { ?>
            <li <?php if ($this->params['controller'] == 'SubPreferences' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-cutlery"></i>&nbsp;&nbsp;Sub-Preferences<i class=""></i>'), array('controller' => 'SubPreferences', 'action' => 'index'), array('escape' => false)); ?> </li>         
        <?php } ?>
        <?php if ($menuPermission == 1) { ?>   
            <li  <?php if ($this->params['controller'] == 'items' && ($this->params['action'] == 'addMenuItem' || $this->params['action'] == 'index' || $this->params['action'] == 'editMenuItem')) { ?> class="active" <?php } ?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo13"><i class="fa fa-cutlery"></i>&nbsp;&nbsp;Menu Builder <i class="fa fa-fw fa-caret-down"></i></a>
                <?php
                $aClass13 = "";
                if (in_array($this->params['controller'], array('items')) && in_array($this->params['action'], array('addMenuItem', 'index', 'editMenuItem'))) {
                    $aClass13 = "in";
                }
                ?>
                <ul id="demo13" class="collapse <?php echo $aClass13; ?>">		
                    <li <?php if ($this->params['controller'] == 'items' && $this->params['action'] == 'addMenuItem') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Add Menu Item'), array('controller' => 'items', 'action' => 'addMenuItem'), array('escape' => false)); ?> </li>
                    <li <?php if ($this->params['controller'] == 'items' && $this->params['action'] == 'index' || $this->params['action'] == 'editMenuItem') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View Items'), array('controller' => 'items', 'action' => 'index'), array('escape' => false)); ?> </li> 
                </ul>
            </li>
        <?php } ?>
        <?php if ($AddonsPermission == 1) { ?>   
            <li <?php if ($this->params['controller'] == 'toppings' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-cutlery"></i>&nbsp;&nbsp;Add-ons<i class=""></i>'), array('controller' => 'toppings', 'action' => 'index'), array('escape' => false)); ?> </li>         
    </li>
<?php } ?>

<?php if ($SubAddOnPermission == 1) { ?>  
    <li <?php if ($this->params['controller'] == 'toppings' && $this->params['action'] == 'listSubTopping') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-cutlery"></i>&nbsp;&nbsp;Sub Add-ons<i class=""></i>'), array('controller' => 'toppings', 'action' => 'listSubTopping'), array('escape' => false)); ?> </li>
<?php } ?>
<?php if ($intervalsPermission == 1) { ?>      
    <li <?php if ($this->params['controller'] == 'intervals' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-cutlery"></i>&nbsp;&nbsp;Time Interval<i class=""></i>'), array('controller' => 'intervals', 'action' => 'index'), array('escape' => false)); ?> </li>         
<?php } ?> 

</ul>
</li>





<li> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo103"><i class="fa fa-fw fa-cog"></i>&nbsp; Configuration <i class="fa fa-fw fa-caret-down"></i></a>
    <?php
    $aClass103 = "";
    if (in_array($this->params['controller'], array('stores', 'Stores', 'contents', 'templates', 'zones', 'customers')) && in_array($this->params['action'], array('circle', 'dash', 'index', 'socialMedia', 'manageTimings', 'manageSliderPhotos', 'manageThemeImages', 'pageList', 'configuration', 'manageStaff', 'staffList', 'saveTermsAndPolicies', 'customerInquiries', 'replyCustomerInquiry', 'manageTemplateCss','homePageModal'))) {
        $aClass103 = "in";
    }
    ?>
    <ul id="demo103" class="collapse <?php echo $aClass103; ?>">

        <?php if ($configurationPermission == 1) { ?> 
            <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'configuration') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp;Configuration <i class=""></i>'), array('controller' => 'stores', 'action' => 'configuration'), array('escape' => false)); ?></li>
        <?php } ?>
        <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'homePageModal') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp;Home Page Modal'), array('controller' => 'stores', 'action' => 'homePageModal'), array('escape' => false)); ?></li>
        <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'manageTemplateCss') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp;Manage Template Css<i class=""></i>'), array('controller' => 'stores', 'action' => 'manageTemplateCss'), array('escape' => false)); ?></li>
        <?php if ($staffManagementPermission == 1) { ?>
            <li  <?php if ($this->params['controller'] == 'stores' && ($this->params['action'] == 'manageStaff')) { ?> class="lActive" <?php } ?>> <?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp; Manage Staff<i class=""></i>'), array('controller' => 'stores', 'action' => 'manageStaff'), array('escape' => false)); ?>
                <!--<?php
                $aClass9 = "";
                if (in_array($this->params['controller'], array('stores')) && in_array($this->params['action'], array('manageStaff', 'staffList'))) {
                    $aClass9 = "in";
                }
                ?>
                <ul id="demo9" class="collapse <?php echo $aClass9; ?>">
                    <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'manageStaff') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Add/List Staff'), array('controller' => 'stores', 'action' => 'manageStaff'), array('escape' => false)); ?> </li> 
                    <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'staffList') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('View Staff'), array('controller' => 'stores', 'action' => 'staffList'), array('escape' => false)); ?> </li>
                </ul>-->
            </li>
        <?php } ?>
        <?php if ($staticpagesPermission == 1) { ?> 
            <li <?php if ($this->params['controller'] == 'contents' && $this->params['action'] == 'pageList') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp;&nbsp;Static Pages<i class=""></i>'), array('controller' => 'contents', 'action' => 'pageList'), array('escape' => false)); ?></li>
        <?php } ?>

        <?php if ($manageImagesPermission == 1) { ?>
            <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'manageSliderPhotos') { ?>class="lActive"<?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp;&nbsp;Manage Images <i class=""></i>'), array('controller' => 'stores', 'action' => 'manageSliderPhotos'), array('escape' => false)); ?></li>
        <?php } ?>
        <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'manageThemeImages') { ?>class="lActive"<?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp;&nbsp;Manage Theme Images <i class=""></i>'), array('controller' => 'stores', 'action' => 'manageThemeImages'), array('escape' => false)); ?></li>

        <?php if ($storeTimingsPermission == 1) { ?>   
            <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'manageTimings') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i> &nbsp;Store Hours <i class=""></i>'), array('controller' => 'stores', 'action' => 'manageTimings'), array('escape' => false)); ?></li>       
        <?php } ?>                

        <?php if ($TemplatePermission == 1) { ?> 
            <li <?php if ($this->params['controller'] == 'templates' && $this->params['action'] == 'index') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp;Manage Email Template<i class=""></i>'), array('controller' => 'templates', 'action' => 'index'), array('escape' => false)); ?></li>
        <?php } ?>

        <?php if ($SocialMediaPermission == 1 && $modulePermission['ModulePermission']['social_media_allow']) { ?>
            <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'socialMedia') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-fw fa-cog"></i>&nbsp;&nbsp;Social Media<i class=""></i>'), array('controller' => 'stores', 'action' => 'socialMedia'), array('escape' => false)); ?></li>
        <?php } ?>
        <li  <?php if ($this->params['controller'] == 'zones' && ($this->params['action'] == 'dash' || $this->params['action'] == 'circle')) { ?> class="active" <?php } ?>> <a href="javascript:void(0);" data-toggle="collapse" data-target="#demo19"><i class="fa fa-fw fa-cog"></i>&nbsp; Delivery Zones <i class="fa fa-fw fa-caret-down"></i></a>
            <?php
            $aClass19 = "";
            if (in_array($this->params['controller'], array('zones')) && in_array($this->params['action'], array('dash', 'circle'))) {
                $aClass19 = "in";
            }
            ?>
            <ul id="demo19" class="collapse <?php echo $aClass19; ?>">
                <li <?php if ($this->params['controller'] == 'zones' && $this->params['action'] == 'dash') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Draw'), array('controller' => 'zones', 'action' => 'dash'), array('escape' => false)); ?> </li> 
                <li <?php if ($this->params['controller'] == 'zones' && $this->params['action'] == 'circle') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('Circle'), array('controller' => 'zones', 'action' => 'circle'), array('escape' => false)); ?> </li>                  
            </ul>
        </li>
        <li <?php if ($this->params['controller'] == 'stores' && $this->params['action'] == 'saveTermsAndPolicies') { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-cog"></i>&nbsp; Terms & Policies<i class=""></i>'), array('controller' => 'stores', 'action' => 'saveTermsAndPolicies'), array('escape' => false)); ?></li>
        <li <?php if ($this->params['controller'] == 'customers' && ($this->params['action'] == 'customerInquiries' || $this->params['action'] == 'replyCustomerInquiry')) { ?> class="lActive" <?php } ?>><?php echo $this->Html->link(__('<i class="fa fa-cog"></i>&nbsp; Customer Inquiries<i class=""></i>'), array('controller' => 'customers', 'action' => 'customerInquiries'), array('escape' => false)); ?></li>
    </ul>
</li>




</ul>








<ul class="nav navbar-nav navbar-right navbar-user">        
    <li class="dropdown user-dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo ucfirst($_SESSION['Auth']['Admin']['fname']); ?> <b class="caret"></b></a>              
        <ul class="dropdown-menu">

            <li>

            </li>
            <li>

                <?php echo $this->Html->link('Profile', array('controller' => 'stores', 'action' => 'myProfile')); ?>


            </li>



            <li class="divider"></li>
            <li>

                <?php echo $this->Html->link('Logout', array('controller' => 'stores', 'action' => 'logout')); ?>
            </li>                
        </ul>
    </li>
</ul>   
</div>
