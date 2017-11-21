
<table class="table table-bordered table-hover table-striped tablesorter">
    <thead>
        <tr>
            <th  class="th_checkbox">Order No.</th>
            <th  class="th_checkbox">Items</th>
            <th  class="th_checkbox">Order Type</th>
            <th  class="th_checkbox">Order Date</th>
              <th  class="th_checkbox">Created Date</th>
        </tr>
    </thead>



    <tbody class="dyntable">
        <?php
        if ($list) {
            $i = 0;
            foreach ($list as $key => $data) {
                $class = ($i % 2 == 0) ? ' class="active"' : '';
                $EncryptOrderID = $this->Encryption->encode($data['Order']['id']);
                ?>
                <tr>
                    <td>
                        <?php echo $this->Html->link($data['Order']['order_number'], array('controller' => 'kitchens', 'action' => 'orderDetail', $EncryptOrderID)); ?>
                    </td>

                    <td><?php
                        $i = 0;
                        $items = "";
                        foreach ($data['OrderItem'] as $key => $item) {
                            if ($i == 0) {
                                $items = $item['Item']['name'];
                            } else {
                                $items.=", " . $item['Item']['name'];
                            }
                            $i++;
                        }
                        echo "<span title='" . $items . "'>" . substr($items, 0, 50) . "</span>";
                        ?></td>


                    <td><?php
                        if ($data['OrderPayment']['payment_gateway'] == 'COD') {
                            if ($data['Order']['seqment_id'] == 3) {
                                $paymentStatus = "UNPAID";
                            } else {
                                $paymentStatus = "UNPAID";
                            }
                        } else {
                            $paymentStatus = "PAID";
                        }


                        echo $data['Segment']['name'] . '-' . $paymentStatus;
                        ?></td>
                    <td><?php
                        //echo $this->Common->storeTimeFormate($this->Common->storeTimezone('',$data['Order']['created']),true);
                        echo $this->Common->storeTimeFormate($data['Order']['pickup_time'], true);
                        ?></td>
                         <td><?php
                        echo $this->Common->storeTimeFormate($this->Common->storeTimezone('',$data['Order']['created']),true);
                        //echo $this->Common->storeTimeFormate($data['Order']['pickup_time'], true);
                        ?></td>
                </tr>
                <?php
                $i++;
            }
        } else {
            ?>
            <tr>
                <td colspan="4" style="text-align: center;">
                    No record available
                </td>
            </tr>
<?php } ?>
    </tbody>
</table>


