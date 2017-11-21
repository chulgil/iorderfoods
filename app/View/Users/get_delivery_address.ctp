<div class="form-group">
    <label class="control-label col-sm-3 col-xs-3">Name:</label>
    <div class="col-sm-9 col-xs-9">
        <p><?php echo ucfirst($resultAddress['DeliveryAddress']['name_on_bell']); ?></p>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-sm-3 col-xs-3">Address:</label>
    <div class="col-sm-9 col-xs-9"> 
        <p><?php echo ucfirst($resultAddress['DeliveryAddress']['address']); ?></p>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-sm-3 col-xs-3">City:</label>
    <div class="col-sm-9 col-xs-9"> 
        <p><?php echo ucfirst($resultAddress['DeliveryAddress']['city']); ?></p>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-sm-3 col-xs-3">State:</label>
    <div class="col-sm-9 col-xs-9"> 
        <p><?php echo ucfirst($resultAddress['DeliveryAddress']['state']); ?></p>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-sm-3 col-xs-3">Zip Code:</label>
    <div class="col-sm-9 col-xs-9"> 
        <p><?php echo $resultAddress['DeliveryAddress']['zipcode']; ?></p>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-sm-3 col-xs-3">Ph no:</label>
    <div class="col-sm-9 col-xs-9"> 
        <p><?php echo $resultAddress['CountryCode']['code'] . '' . $resultAddress['DeliveryAddress']['phone']; ?></p>
    </div>
</div>
<div class="form-group form-inner-action-btn">
    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil')) . 'Edit', array('controller' => 'users', 'action' => 'updateAddress', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($resultAddress['DeliveryAddress']['id'])), array('class' => 'apply-order theme-bg-1', 'escape' => false)); ?>
    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o')) . 'Delete', array('controller' => 'users', 'action' => 'deleteDeliveryAddress', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($resultAddress['DeliveryAddress']['id'])), array('confirm' => __('Are you sure you want to delete this delivery address?'), 'class' => 'apply-order theme-bg-2', 'escape' => false)); ?>
</div>