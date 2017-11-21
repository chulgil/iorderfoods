<div class="row">
    <div class="col-lg-12">
        <h3>Extended Offers used by user</h3>
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
                        <th  class="th_checkbox">Item Name<?php //echo $this->Paginator->sort('Item.name', 'Item Name'); ?></th>
                        <th  class="th_checkbox">Count</th>
                        <th  class="th_checkbox">User Name<?php //echo $this->Paginator->sort('User.fname', 'User Name'); ?></th>
                        <th  class="th_checkbox">Email<?php //echo $this->Paginator->sort('User.email', 'Email'); ?></th>
                </thead>
                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            //$EncryptCouponID = $this->Encryption->encode($data['Order']['id']);
                            ?>
                            <tr <?php echo $class; ?>>
                                <td><?php echo $data['item_name']; ?></td>
                                <td><?php echo $data['count']; ?></td>
                                <td><?php echo $data['name']; ?></td>
                                <td><?php echo $data['email']; ?></td>
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
            <?php //echo $this->element('pagination') ?>
        </div>
    </div>
</div>