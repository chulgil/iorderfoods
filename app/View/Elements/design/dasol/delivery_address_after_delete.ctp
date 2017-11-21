<?php
if (!empty($checkaddress)) {
    $count = count($checkaddress) - 1;
    ?>
    <ul id="delivery_address">
        <li>
            <label>Name</label>
            <?php echo ucfirst($checkaddress[$count]['DeliveryAddress']['name_on_bell']); ?>
        </li>
        <li>
            <label>Address</label>
            <?php echo ucfirst($checkaddress[$count]['DeliveryAddress']['address']); ?>
        </li>
        <li>
            <label>City </label>
            <?php echo ucfirst($checkaddress[$count]['DeliveryAddress']['city']); ?>
        </li>
        <li>
            <label>State</label>
            <?php echo ucfirst($checkaddress[$count]['DeliveryAddress']['state']); ?>
        </li>
        <li>
            <label>Zip Code</label>
            <?php echo $checkaddress[$count]['DeliveryAddress']['zipcode']; ?>
        </li>
        <li>
            <label>Ph no.</label>
            <?php echo $checkaddress[$count]['CountryCode']['code'] . '' . $checkaddress[0]['DeliveryAddress']['phone']; ?>
        </li>
        <li>
            <span class="editAddress" data-id="<?php echo $this->Encryption->encode($checkaddress[$count]['DeliveryAddress']['id']); ?>"><i class="fa fa-pencil"></i>Edit</span>
            <span class="deleteAddress" data-id="<?php echo $this->Encryption->encode($checkaddress[$count]['DeliveryAddress']['id']); ?>"><i class="fa fa-trash-o"></i>Delete</span>
        </li>
    </ul>
<?php } else { ?>
    <address>
        <h3>Please add your delivery address.</h3>
        <a class="addAddress"><i class="fa fa-plus-circle"></i>Add More Addresses</a>
    </address>
<?php } ?>

<?php if (!empty($checkaddress)) { ?> 
    <div class="address-radio-wrap clearfix">
        <?php
        $i = 0;
        foreach ($checkaddress as $address) {
            if ($address['DeliveryAddress']['default'] == 1) {
                $checked = "checked = 'checked'";
            } else {
                $checked = "";
            }
            ?>
            <?php if ($address['DeliveryAddress']['label'] == 1) { ?>
                <input type="radio" id="home" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="home"><span class="chk-span"></span>Home Address</label>
            <?php } else if ($address['DeliveryAddress']['label'] == 2) { ?>
                <input type="radio" id="work" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="work"><span class="chk-span"></span>Work Address</label>
            <?php } else if ($address['DeliveryAddress']['label'] == 3) { ?>
                <input type="radio" id="other" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other"><span class="chk-span"></span>Other Address</label>
            <?php } else if ($address['DeliveryAddress']['label'] == 4) { ?>
                <input type="radio" id="other4" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other4"><span class="chk-span"></span>Address4</label>
            <?php } else if ($address['DeliveryAddress']['label'] == 5) { ?>
                <input type="radio" id="other5" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other5"><span class="chk-span"></span>Address5</label>
                <?php
            }
            $i++;
        }
        ?>
        <?php if ($i < 5) { ?>
                <div class="" style="width:100%;float:left;"> 
                <a class="addAddress"><i class="fa fa-plus-circle"></i>Add More Addresses</a>
            </div>
        <?php } ?>
    </div>
<?php } ?> 

<?php if (!empty($addressId)) { ?>
    <script>
        $("input[value='" + <?php echo $addressId; ?> + "']").prop('checked', true);
    </script>
    <?php
}?>