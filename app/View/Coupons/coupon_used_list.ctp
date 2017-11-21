<div class="row">
    <div class="col-lg-12">
        <h3>Coupon used by user</h3>
        <hr>
        <?php echo $this->Session->flash(); ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox">Code<?php //echo $this->Paginator->sort('Order.coupon_code', 'Code'); ?></th>
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
                            ?>
                            <tr <?php echo $class; ?>>
                                <td><?php echo $data['coupon_code']; ?></td>
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