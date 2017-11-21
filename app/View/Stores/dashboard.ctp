
<style>
    /* unvisited link */
    a:link {
        color: #FFFFFF;
    }

    /* visited link */
    a:visited {
        color: #FFFFFF;
    }

    /* mouse over link */
    a:hover {
        color: #FFFFFF;
    }

    /* selected link */
    a:active {
        color: #FFFFFF;
    }
    #myTodaysOrder,#myTodayPendingOrder,#myPreOrder,#myTodayRequest,#myTodayPendingBookingRequest{
        cursor: pointer;
    }
</style>
<!-- user input entry form start here -->
<section class="ui-form ui-form-login">
    <h2>Store Dashboard<br></h2>
    <hr>
    <?php
    if (!$this->Session->check('Message.order_confirm') && !$this->Session->check('Message.link_used')) {
        echo $this->Session->flash();
    }
    ?>

    <div class="row">
        <div class="col-lg-6">
            <!--  ===== Todays's order Start ===== -->
            <?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'index'), 'id' => 'todaysOrder', 'type' => 'post')); ?>
            <div class="panel panel-primary" id="myTodaysOrder">
                <div class="panel-heading">
                    <div class="row">
                        <div class="text-center">
                            <?php $todaysOrder = $this->Common->getTodaysOrder(); ?>     
                            <div class="huge">
                                <span>
                                    <?php echo $todaysOrder; ?>
                                </span>           
                                <?php echo $this->Form->input('Order.today', array('type' => 'hidden', 'value' => 'todayorder')); ?>
                            </div>
                            <div class="fts20">Today's Orders</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <!--  ===== Todays's order End ===== -->
        </div>

        <div class="col-lg-6">
            <!--  ===== Pending order Start ===== -->
            <?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'index'), 'id' => 'todayPendingOrder', 'type' => 'post')); ?>
            <div class="panel1 panel-primary1" id="myTodayPendingOrder">
                <div class="panel panel-primary1">
                    <div class="panel-heading1">

                        <div class="row">
                            <div class="text-center">
                                <?php $todaysPendingOrder = $this->Common->getTodaysPendingOrder(); ?>        
                                <div class="huge">
                                    <span>
                                        <?php echo $todaysPendingOrder; ?>
                                    </span>           
                                    <?php echo $this->Form->input('Order.todayPendingOrder', array('type' => 'hidden', 'value' => 'todayorder')); ?>
                                </div>
                                <div class="fts20">Pending Orders</div>
                            </div>
                        </div>

                    </div>		
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <!--  ===== Pending order End ===== -->
        </div>	
    </div>


    <div class="row">
        <div class="col-lg-6">
            <!--  ===== Pre-order Start ===== -->
            <?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'index'), 'id' => 'preOrder', 'type' => 'post')); ?>
            <div class="panel panel-primary" id="myPreOrder">
                <div class="panel-heading">
                    <div class="row">
                        <div class="text-center">
                            <?php $preOrder = $this->Common->getPreOrder(); ?>     
                            <div class="huge">
                                <span>
                                    <?php echo $preOrder; ?>
                                </span>           
                                <?php echo $this->Form->input('Order.preOrder', array('type' => 'hidden', 'value' => 'preOrder')); ?>
                            </div>
                            <div class="fts20">Pre-orders</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <!--  ===== Pre-order End ===== -->
        </div>

        <div class="col-lg-6">
            <!--  ===== Today's Booking Request Start ===== -->
            <div class="panel1 panel-primary1" id="myTodayRequest">
                <div class="panel panel-primary1">
                    <div class="panel-heading1">
                        <?php echo $this->Form->create('Order', array('url' => array('controller' => 'bookings', 'action' => 'index'), 'id' => 'todayRequest', 'type' => 'post')); ?>                        
                        <div class="row">
                            <div class="text-center">
                                <?php $todaysBookingRequest = $this->Common->getTodaysBookingRequest(); ?>       
                                <div class="huge">
                                    <span>
                                        <?php echo $todaysBookingRequest; ?> 
                                    </span>           
                                    <?php echo $this->Form->input('Order.todayBookingRq', array('type' => 'hidden', 'value' => 'todayorder')); ?>
                                </div>
                                <div class="fts20">Today's Booking Requests</div>
                            </div>
                        </div>
                        <?php echo $this->Form->end(); ?>
                    </div>		
                </div>
            </div>
            <!--  ===== Today's Booking Request End ===== -->
        </div>	
    </div>

    <div class="row">
        <div class="col-lg-6">
            <!--  ===== Today's Pending Request  Start ===== -->
            <?php echo $this->Form->create('Order', array('url' => array('controller' => 'bookings', 'action' => 'index'), 'id' => 'todayPendingBookingRequest', 'type' => 'post')); ?>                         
            <div class="panel panel-primary" id="myTodayPendingBookingRequest">
                <div class="panel-heading">
                    <div class="row">
                        <div class="text-center">
                            <?php $todaysPendingBookings = $this->Common->getTodaysPendingBookings(); ?>        
                            <div class="huge">
                                <span>
                                    <?php echo $todaysPendingBookings; ?>
                                </span>           
                                <?php echo $this->Form->input('Order.pendingBookingRq', array('type' => 'hidden', 'value' => 'todayorder')); ?>
                            </div>
                            <div class="fts20">Pending Booking Requests</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <!--  ===== Today's Pending Request End ===== -->
        </div>

        <div class="col-lg-6">
            <!--  ===== Extra Start ===== -->

            <!--  ===== Extra End ===== -->
        </div>	
    </div> 

    <hr>
    <div class="row">

        <div class="col-lg-8">
            <h3>Printer Listing</h3>
            <br>
            <?php echo $this->Session->flash(); ?>
                <table class="table table-bordered table-hover table-striped tablesorter">
                    <thead>
                        <tr>
                            <th  class="th_checkbox">Machine Name</th>
                            <th  class="th_checkbox">Printer Version</th>
                            <th  class="th_checkbox">Online On/Off</th>
                    </thead>

                    <tbody class="dyntable">
                        <?php
                        if ($list) {
                            $i = 0;
                            foreach ($list as $key => $data) {
                                $class = ($i % 2 == 0) ? ' class="active"' : '';
                                $EncryptStoreID = $this->Encryption->encode($data['Store']['id']);
                                ?>
                                <tr>
                                    <td><?php echo $data['StorePrinterStatus']['machine_name']; ?></td>
                                    <td><?php echo $data['StorePrinterStatus']['current_version']; ?></td>
                                    <td>
                                        <?php
                                        if ($data['StorePrinterStatus']['is_active']) {
                                            echo '<span style="color:blue;">On</span>';
                                        } else {
                                            echo '<span style="color:red;">Off</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">
                                    No record available
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php echo $this->Form->end(); ?>


            </div>
    </div>

    <?php echo $this->element('confirm_order'); ?>

</section><!-- /user input entry form end -->
<script>
    $(document).ready(function () {
        $("#myTodaysOrder").click(function () {
            $("#todaysOrder").submit();
        });
        $("#myTodayPendingOrder").click(function () {
            $("#todayPendingOrder").submit();
        });
        $("#myPreOrder").click(function () {
            $("#preOrder").submit();
        });
        $("#myTodayRequest").click(function () {
            $("#todayRequest").submit();
        });
        $("#myTodayPendingBookingRequest").click(function () {
            $("#todayPendingBookingRequest").submit();
        });

    });
</script>