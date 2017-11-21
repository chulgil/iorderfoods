<!--******************Order details area start here***********************-->
<div role="tabpanel" class="tab-pane" id="order">
    <?php if (!empty($orderDetails)) { ?>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->element('pagination'); ?>
            </div>
        </div>
        <?php echo $this->element('show_pagination_count'); ?>
        <table class="table table-bordered table-hover table-striped tablesorter">
            <thead>
                <tr>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.id', 'Order No.'); ?></th>
                    <th  class="th_checkbox">Amount</th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Segment.name', 'Order Type'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderStatus.name', 'Order Status'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.created', 'Created'); ?></th>
                </tr>
            </thead>
            <tbody class="dyntable">
                <?php foreach ($orderDetails as $oData) {
                    $EncryptOrderID = $this->Encryption->encode($oData['Order']['id']);
                    $EncryptStore_ID = $this->Encryption->encode($oData['Order']['store_id']);
                    
                    ?>
                    <tr>
                        <td><?php echo $this->Html->link($oData['Order']['order_number'], array('controller' => @$this->params['controller'], 'action' => 'customerOrderDetail', $EncryptOrderID)); ?></td>
                        <td><?php echo $this->Common->amount_format($oData['Order']['amount'] - $oData['Order']['coupon_discount']); ?></td>
                        <td><?php echo $oData['Segment']['name']; ?></td>
                        <td><?php echo $oData['OrderStatus']['name']; ?></td>
                        <td><?php
                            if (!empty($oData['Order']['store_id'])) {
                                echo $this->Dateform->us_format($this->Hq->storeTimezone(null, $oData['Order']['created'], null, $oData['Order']['store_id']));
                            } else {
                                echo $this->Dateform->us_format($oData['Order']['created']);
                            }


                            //echo $this->Dateform->us_format($oData['Order']['created']);
                            ?></td>
                    </tr>
                <?php } ?>
            </tbody>

        </table>
        <?php echo $this->element('pagination'); ?>
        <?php
    } else {
        echo "No Record Found";
    }
    ?>
</div>
<!--******************Order details area end here***********************-->