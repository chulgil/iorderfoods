<div class="row">
    <div class="col-lg-12">
        <div class="col-sm-6">
            <h3>Subscription Payment List</h3>
        </div>
        <div class="col-sm-6">
            <?php echo $this->Html->link('Download Excel', array('controller' => 'super', 'action' => 'paymentDownload', @$this->request->data['MerchantPayment']['merchant_id']), array('class' => 'btn btn-default pull-right')); ?>
        </div>
        <hr>
    </div>
    <div class="col-lg-12">
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('MerchantPayment', array('url' => array('controller' => 'super', 'action' => 'merchantPaymentList'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-3">		     
                        <?php
                        $merchantList = $this->Common->getListMerchant();
                        echo $this->Form->input('MerchantPayment.merchant_id', array('options' => $merchantList, 'label' => false, 'class' => 'form-control', 'div' => false, 'empty' => 'Please select Merchant'));
                        ?>  
                    </div>
                    <div class="col-lg-2">		     
                        <?php
                        $statusList = array('Paid' => 'Paid', 'Invoice Created' => 'Invoice Created', 'Not Paid' => 'Not Paid');
                        echo $this->Form->input('MerchantPayment.payment_status', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $statusList, 'empty' => 'Select Status'));
                        ?>		
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-3">   
                        <?php echo $this->Form->input('MerchantPayment.keyword', array('value' => @$keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control', 'maxlength' => 55)); ?>
                        <span class="blue">(<b>Search by:</b>Name, Type, Location, Address, Phone no, URL)</span>
                    </div>
                    <div class="col-lg-2">
                        <?php
                        echo $this->Form->input('MerchantPayment.from', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'From'));
                        ?>
                    </div>&nbsp;&nbsp;
                    <div class="col-lg-2">

                        <?php
                        echo $this->Form->input('MerchantPayment.to', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'To'));
                        ?>
                    </div>

                    <div class="col-lg-4">                        
                        <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                        <?php echo $this->Html->link('Clear', array('controller' => 'super', 'action' => 'merchantPaymentList', 'clear'), array('class' => 'btn btn-default')); ?>
                    </div>

                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php echo $this->Paginator->counter('Page {:page} of {:pages}'); ?> 
                </div>
                <div class="col-sm-6 text-right">
                    <?php echo $this->Paginator->counter('showing {:current} records out of {:count} total'); ?> 
                </div>
            </div>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Merchant.store_name', 'Merchant Name'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Plan.name', 'Subscription Type'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantPayment.payment_date', 'Payment Date'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantPayment.payment_type', 'Payment Type'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantPayment.amount', 'Amount'); ?>&nbsp;&nbsp;($)</th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantPayment.payment_status', 'Status'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantPayment.comments', 'Comments'); ?></th>
                        <th  class="th_checkbox">Action</th>
                </thead>
                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            //$EncryptCouponID=$this->Encryption->encode($data['Coupon']['id']); 
                            ?>
                            <tr>	    
                                <td>
                                    <?php echo $this->Html->link($data['Merchant']['name'], array('controller' => 'super', 'action' => 'merchantStoreList', $this->Encryption->encode($data['MerchantPayment']['merchant_id'])), array('escape' => false)); ?>
                                </td>
                                <td><?php echo $data['Plan']['name']; ?></td>
                                <td>
                                    <?php echo ($data['MerchantPayment']['payment_date'] != null && !empty($data['MerchantPayment']['payment_date']) ? $this->Dateform->us_format($data['MerchantPayment']['payment_date']) : '-');?>
                                </td>
                                <td>
                                    <?php 
                                    if($data['MerchantPayment']['payment_type'] == 1) {
                                        echo 'One-Time';
                                    } else if($data['MerchantPayment']['payment_type'] == 2) {
                                        echo 'Recurring';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td><?php echo $data['MerchantPayment']['amount']; ?></td>
                                <td><?php echo $data['MerchantPayment']['payment_status']; ?></td>
                                <td>
                                    <?php
                                    $comments = (strlen($data['MerchantPayment']['comments']) > 30 ? substr($data['MerchantPayment']['comments'], 0, 30) . '...' : $data['MerchantPayment']['comments']);
                                    ?>
                                    <p title="<?php echo $data['MerchantPayment']['comments'];?>" data-toggle="tooltip"><?php echo $comments;?></p>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Update", "title" => "Update")), array('controller' => 'super', 'action' => 'updateMerchantPayment', $this->Encryption->encode($data['MerchantPayment']['id'])), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'super', 'action' => 'deleteMerchantPayment', $this->Encryption->encode($data['MerchantPayment']['id'])), array('confirm' => 'Are you sure to delete?', 'escape' => false)); ?>  	
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
        $("#MerchantPaymentKeyword").autocomplete({
            source: "/super/getSearchValues",
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        };
        
        $("#MerchantPaymentMerchantId,#MerchantPaymentPaymentStatus").change(function () {
//var couponId=$("#CouponIsActive").val
            $("#AdminId").submit();
        });
        $('#MerchantPaymentFrom').datepicker({
            dateFormat: 'mm-dd-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2010:' + new Date().getFullYear(),
        });
        $('#MerchantPaymentTo').datepicker({
            dateFormat: 'mm-dd-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2010:' + new Date().getFullYear(),
        });

    });
</script>