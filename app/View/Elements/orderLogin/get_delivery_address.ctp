<li>
    <span class="title"><label>Name</label></span>
    <div class="title-box"><?php echo ucfirst($resultAddress['DeliveryAddress']['name_on_bell']); ?></div>
</li>

<li>
    <span class="title"><label>Address</label></span>
    <div class="title-box"><?php echo ucfirst($resultAddress['DeliveryAddress']['address']); ?></div>
</li>

<li>
    <span class="title"><label>City </label></span>
    <div class="title-box"><?php echo ucfirst($resultAddress['DeliveryAddress']['city']); ?></div>
</li>

<li>
    <span class="title"><label>State</label></span>
    <div class="title-box"><?php echo ucfirst($resultAddress['DeliveryAddress']['state']); ?></div>
</li>

<li>
    <span class="title"><label>Zip Code</label></span>
    <div class="title-box"><?php echo $resultAddress['DeliveryAddress']['zipcode']; ?></div>
</li>

<li>
    <span class="title"><label>Ph no.</label></span>
    <div class="title-box"><?php echo $resultAddress['CountryCode']['code'] . '' . $resultAddress['DeliveryAddress']['phone']; ?></div>
</li>

<li>

    <div class="title-box edit-link"> 
        <?php
        echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil')) . 'Edit', 'javascript:void(0)', array('class' => 'button-link', 'escape' => false, 'rel' => $this->Encryption->encode($resultAddress['DeliveryAddress']['id']), 'id' => 'editLinkID'));
        ?> 
        &nbsp;&nbsp;&nbsp;
        <?php
        echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil')) . 'Delete', 'javascript:void(0)', array('class' => 'button-link', 'escape' => false, 'rel' => $this->Encryption->encode($resultAddress['DeliveryAddress']['id']), 'id' => 'deleteLinkID'));
        if (empty($changeOrderType)) {
            ?>
            &nbsp;&nbsp;&nbsp;
            <b><?php echo $this->Html->link('Change Order Type', 'javascript:void(0)', array('class' => 'button-link hideit', 'escape' => false, 'id' => 'changeorderType')); ?></b>
        <?php } ?>
    </div>
</li>

<script>

    $('#editLinkID').click(function () {
        var addressId = $('#editLinkID').attr('rel');
        $.ajax({
            type: 'POST',
            url: '/ajaxMenus/updateAddress',
            data: {address: addressId},
            success: function (response) {
                $("#chkDeliveryAddress").html(response);
            }
        });
    });

    $('#deleteLinkID').click(function () {
        var addressId = $('#deleteLinkID').attr('rel');
        $.ajax({
            type: 'POST',
            url: '/ajaxMenus/deleteaddress',
            data: {address: addressId},
            success: function () {
                getdeliveryAddress();
            }
        });
    });

    $('#changeorderType').click(function () {
        changeTabPan('chkOrderType', 'chkDeliveryAddress');
        setDefaultStoreTime(3);
    });
</script>