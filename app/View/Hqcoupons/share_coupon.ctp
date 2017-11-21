<div class="row">
    <div class="col-lg-12">
        <h3>Share Coupon</h3>
        <hr>
        <?php echo $this->Session->flash(); ?>
        <div class="table-responsive">
            <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'hqcoupons', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="form-group">
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Coupon Code :<span class="required">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $couponId['Coupon']['coupon_code']; ?> </span></label>
                </div>
                <div class="form-group">
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Store Name :<span class="required">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $couponId['Store']['store_name']; ?> </span></label>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'hqcoupons', 'action' => 'shareCoupon'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <?php echo $this->Form->input('User.coupon_code', array('type' => 'hidden', 'value' => $couponId['Coupon']['coupon_code'])); ?>
            <?php echo $this->Form->input('User.coupon_id', array('type' => 'hidden', 'value' => $couponId['Coupon']['id'])); ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox"><input type="checkbox" id="selectall" style="float:left;"/></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.name', 'Customer Name'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.email', 'Email'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.created', 'Created'); ?></th>
                </thead>
                <tbody class="dyntable">
                    <?php
                    $i = 0;
                    foreach ($list as $key => $data) {
                        $class = ($i % 2 == 0) ? ' class="active"' : '';
                        //$EncryptCouponID=$this->Encryption->encode($data['Coupon']['id']);
                        ?>
                        <tr <?php echo $class; ?>>
                            <td><?php echo $this->Form->checkbox('User.id.' . $key, array('class' => 'case', 'value' => $data['User']['id'], 'style' => 'float:left;')); ?></td>
                            <td><?php echo $data['User']['fname'] . " " . $data['User']['lname']; ?></td>
                            <td><?php echo $data['User']['email']; ?></td>
                            <td><?php
                                echo $this->Hq->storeTimezone(null, $data['User']['created'], null, @$data['User']['store_id']);
                                //echo $this->Dateform->us_format($data['User']['created']);
                                ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <?php echo $this->Form->button('Share', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();')); ?>&nbsp;
            <?php echo $this->Html->link('Cancel', "/hqcoupons/index/", array("class" => "btn btn-default", 'escape' => false)); ?>

            <?php echo $this->Form->end(); ?>
            <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                <?php
                echo $this->Paginator->first('First');
                // Shows the next and previous links
                echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                // Shows the page numbers
                echo $this->Paginator->numbers(array('separator' => ''));
                echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                // prints X of Y, where X is current page and Y is number of pages
                //echo $this->Paginator->counter();
                echo $this->Paginator->last('Last');
                ?>
            </div>

        </div>
    </div>
</div>
<?php echo $this->Html->css('pagination'); ?>
<script>
    $(document).ready(function () {
        $("#CouponIsActive").change(function () {
            var couponId = $("#CouponIsActive").val();
            $("#AdminId").submit();
        });
    });
    // add multiple select / deselect functionality
    //$("#selectall").click(function () {
    //      $('.case').attr('checked', this.checked);
    //});
    $("#selectall").click(function () {
        var st = $("#selectall").prop('checked');
        $('.case').prop('checked', st);

    });
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
    $(".case").click(function () {
        if ($(".case").length == $(".case:checked").length) {
            $("#selectall").attr("checked", "checked");
        } else {
            $("#selectall").removeAttr("checked");
        }

    });
    function check()
    {
        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select user.');
            // cancel submit
            return false;
        }

    }
</script>
