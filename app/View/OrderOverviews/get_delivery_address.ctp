<li>
    <label>Name</label>
    <?php echo ucfirst($resultAddress['DeliveryAddress']['name_on_bell']); ?>
</li>
<li>
    <label>Address</label>
    <?php echo ucfirst($resultAddress['DeliveryAddress']['address']); ?>
</li>
<li>
    <label>City</label>
    <?php echo ucfirst($resultAddress['DeliveryAddress']['city']); ?>
</li>
<li>
    <label>State</label>
    <?php echo ucfirst($resultAddress['DeliveryAddress']['state']); ?>
</li>
<li>
    <label>Zip Code</label>
    <?php echo $resultAddress['DeliveryAddress']['zipcode']; ?>
</li>
<li>
    <label>Ph no.</label>
    <?php echo $resultAddress['CountryCode']['code'] . '' . $resultAddress['DeliveryAddress']['phone']; ?>
</li>
<li>
    <span class="editAddress chk-span" data-id="<?php echo $this->Encryption->encode($resultAddress['DeliveryAddress']['id']); ?>"><i class="fa fa-pencil"></i>Edit</span>
    <span class="deleteAddress chk-span" data-id="<?php echo $this->Encryption->encode($resultAddress['DeliveryAddress']['id']); ?>"><i class="fa fa-trash-o"></i>Delete</span>
</li>