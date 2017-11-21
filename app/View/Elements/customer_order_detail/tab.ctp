<style>
    .th_checkbox.active-th a {
        color: #000;
    }
    .th_checkbox.active-th {
        background: #fff none repeat scroll 0 0;
    }
</style>
<div class="row">
    <div class="col-md-12 clearfix">
        <div class="row">
            <div class="col-xs-6">
                <h3>Customer Detail</h3>
            </div>
            <div class="col-xs-6">
                <?php
                if ($this->params['controller'] == 'hqcustomers') {
                    $action = 'index';
                } else {
                    $action = 'customerList';
                }
                echo $this->Html->link('Back', array('controller' => $this->params['controller'], 'action' => $action), array("class" => "btn btn-default pull-right", 'escape' => false));
                ?>
            </div>
        </div>
        <hr>
    </div>
    <div class="col-md-6  clearfix">
        <div>
            <!-- Nav tabs -->
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox <?php
                        if ($this->params['action'] == 'orderHistory') {
                            echo 'active-th';
                        }
                        ?>"><?php echo $this->Html->link('Personal Information', array('controller' => $this->params['controller'], 'action' => 'orderHistory', $EncryptCustomerID), array('escape' => false)) ?></th>
                        <th  class="th_checkbox <?php
                        if ($this->params['action'] == 'orderDetail') {
                            echo 'active-th';
                        }
                        ?>"><?php echo $this->Html->link('Orders', array('controller' => $this->params['controller'], 'action' => 'orderDetail?cId=' . $EncryptCustomerID), array('escape' => false)) ?></th>
                        <th  class="th_checkbox <?php
                        if ($this->params['action'] == 'reviewDetail') {
                            echo 'active-th';
                        }
                        ?>"><?php echo $this->Html->link('Reviews', array('controller' => $this->params['controller'], 'action' => 'reviewDetail?cId=' . $EncryptCustomerID), array('escape' => false)) ?></th>
                        <th  class="th_checkbox <?php
                        if ($this->params['action'] == 'reservationDetail') {
                            echo 'active-th';
                        }
                        ?>"><?php echo $this->Html->link('Reservation', array('controller' => $this->params['controller'], 'action' => 'reservationDetail?cId=' . $EncryptCustomerID), array('escape' => false)) ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="col-md-12">&nbsp;
</div>