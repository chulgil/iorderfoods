<div class="static-content-bg">
    <ul class="list-style-none col-listing clearfix">
        <li>
            <div class="col-2">
                <span class="col-left">
                    Name:
                </span>
                <span class="col-right">
                    <?php echo ucfirst($resultAddress['DeliveryAddress']['name_on_bell']); ?>
                </span>
            </div>
            <div class="col-2">
                <span class="col-left">
                    Address:
                </span>
                <span class="col-right">
                    <?php echo ucfirst($resultAddress['DeliveryAddress']['address']); ?>
                </span>
            </div>
        </li>
        <li>
            <div class="col-2">
                <span class="col-left">
                    City:</span>
                <span class="col-right">
                    <?php echo ucfirst($resultAddress['DeliveryAddress']['city']); ?>
                </span>
            </div>
            <div class="col-2">
                <span class="col-left">
                    State:
                </span>
                <span class="col-right">
                    <?php echo ucfirst($resultAddress['DeliveryAddress']['state']); ?>
                </span>
            </div>
        </li>
        <li>
            <div class="col-2">
                <span class="col-left">
                    Zip Code:
                </span>
                <span class="col-right">
                    <?php echo ucfirst($resultAddress['DeliveryAddress']['zipcode']); ?>
                </span>
            </div>
            <div class="col-2">
                <span class="col-left">
                    Phone Number:
                </span>
                <span class="col-right">
                    <?php echo $resultAddress['CountryCode']['code'] . '' . $resultAddress['DeliveryAddress']['phone']; ?>
                </span>
            </div>
        </li>
    </ul>
</div>
<!-- /CONTENT END -->
<!-- ACTION LINKS -->
<div class="text-right action-links clearfix">
    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil-square-o', 'aria-hidden' => "true")) . 'EDIT', array('controller' => 'hqusers', 'action' => 'updateAddress', $this->Encryption->encode($resultAddress['DeliveryAddress']['id'])), array('class' => '', 'escape' => false)); ?>  &nbsp;&nbsp;&nbsp;
    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o', 'aria-hidden' => "true")) . 'DELETE', array('controller' => 'hqusers', 'action' => 'deleteDeliveryAddress', $this->Encryption->encode($resultAddress['DeliveryAddress']['id'])), array('confirm' => __('Are you sure you want to delete this delivery address?'), 'class' => '', 'escape' => false)); ?>
</div>