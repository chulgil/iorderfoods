<div class="row">
    <div class="col-lg-12">
        <h3>Offer used by user</h3>
        <?php //echo $this->Html->link('Back', array('controller' => 'payments', 'action' => 'paymentList'), array('class' => 'btn btn-default pull-right', 'style' => 'margin-top: -28px;')); ?>
        <hr>
        <?php echo $this->Session->flash(); ?>
        <div class="table-responsive">
            <div class="row">
                <div class="col-sm-6">
                    <?php //echo $this->Paginator->counter('Page {:page} of {:pages}'); ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?php //echo $this->Paginator->counter('showing {:current} records out of {:count} total'); ?>
                </div>
            </div>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox">Offer Description</th>
                        <th  class="th_checkbox">Item Name</th>
                        <th  class="th_checkbox">Offered Item Price</th>
                        <th  class="th_checkbox">User Name</th>
                        <th  class="th_checkbox">Email</th>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $offerNewArray = array();
                        $i = 0;
                        $totalOferedPrice = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                                ?>
                                <tr <?php echo $class; ?>>
                                    <td><?php echo @$data['description']; ?></td>
                                    <td><?php echo @$data['offered_item_name'];?></td>
                                    <td style="text-align: center;">
                                        <?php
                                        if($data['is_fixed_price'])
                                        {
                                            $totalOferedPrice += $data['offerprice'];
                                            echo $this->Common->amount_format($data['offerprice']);
                                        } else {
                                            if($data['offer_item'])
                                            {
                                                foreach ($data['offer_item'] as $key => $value)
                                                {
                                                    if($value['offerItemID'] == $data['order_offer_item_id'])
                                                    {
                                                        $totalOferedPrice += $value['discountAmt'];
                                                        echo $this->Common->amount_format($value['discountAmt']);
                                                    }
                                                }
                                            }
                                        }

                                        ?>
                                    </td>
                                    <td><?php echo @$data['name']; ?></td>
                                    <td><?php echo @$data['email']; ?></td>
                                </tr>
                                <?php
                                $i++;
                            //}
                        }
                        /*if($totalOferedPrice > 0)
                        {
                            ?>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td style="text-align: center;"><strong><?php echo $this->Common->amount_format($totalOferedPrice);?></strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                        }*/
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
        </div>
    </div>
</div>