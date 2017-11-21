<!-- user input entry form start here -->
<section class="ui-form ui-form-login">
    <h2>Super Admin Dashboard</h2>
    <hr>
    <?php echo $this->Session->flash(); ?>
    <div class="row">      
    </div> 
    <div class="row">
        <div class="col-lg-6">
            <a href="/super/viewStoreDetails">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="text-center">
                                <?php $totalStores = $this->Common->getStore(); ?>     
                                <div class="huge"><?php echo $totalStores; ?></div>
                                <div class="fts20">Total Store's</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="/super/customerList">
                <div class="panel1 panel-primary1">
                    <div class="panel-heading1">
                        <div class="row">
                            <div class="text-center">
                                <?php $totalCustomers = $this->Common->getCustomer(); ?>        
                                <div class="huge"><?php echo $totalCustomers; ?></div>
                                <div class="fts20">Total Customers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-9">
            <a href="/super/viewMerchantDetails">
                <div class="panel panel-primary" style="margin-left:280px;">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="text-center">
                                <?php $totalMerchants = $this->Common->getMerchant(); ?>       
                                <div class="huge"><?php echo $totalMerchants; ?></div>
                                <div class="fts20">Total Merchant(HQ)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section><!-- /user input entry form end -->

