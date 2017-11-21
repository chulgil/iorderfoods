<div class="content single-frame">
    <div class="wrap">
        <?php //echo $this->Session->flash(); ?>
        <div class="clearfix">
            <section class="form-layout sign-up registration-from no-image editable-form del-add">
                <h2> <span>My Delivery Address</span></h2>    	
                <?php if ($checkaddress) { ?>
                    <ul class="clearfix margin-bt-0 clear-clearfix vertical_delivery" id="delivery_address" >
                        <li>
                            <span class="title"><label>Name</label></span>
                            <div class="title-box"><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['name_on_bell']); ?></div>
                        </li>

                        <li>
                            <span class="title"><label>Address</label></span>
                            <div class="title-box"><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['address']); ?></div>
                        </li>

                        <li>
                            <span class="title"><label>City </label></span>
                            <div class="title-box"><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['city']); ?></div>
                        </li>

                        <li>
                            <span class="title"><label>State</label></span>
                            <div class="title-box"><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['state']); ?></div>
                        </li>

                        <li>
                            <span class="title"><label>Zip Code</label></span>
                            <div class="title-box"><?php echo $checkaddress[0]['DeliveryAddress']['zipcode']; ?></div>
                        </li>

                        <li>
                            <span class="title"><label>Ph no.</label></span>
                            <div class="title-box"><?php echo $checkaddress[0]['CountryCode']['code'].''.$checkaddress[0]['DeliveryAddress']['phone']; ?></div>
                        </li>

                        <li>
                            <span class="title blank">&nbsp;</span>
                            <div class="title-box edit-link"> 
                                <?php echo $this->Html->link($this->Html->tag('i','',array('class'=>'fa fa-pencil')).'Edit', array('controller' => 'users', 'action' => 'updateAddress', $encrypted_storeId, $encrypted_merchantId,$this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id'])), array('class' => 'button-link','escape'=>false)); ?> &nbsp;&nbsp;&nbsp;
                                <?php echo $this->Html->link($this->Html->tag('i','',array('class'=>'fa fa-trash-o')).'Delete',array('controller'=>'users','action'=>'deleteDeliveryAddress',$encrypted_storeId,$encrypted_merchantId,$this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id'])),array('confirm' => __('Are you sure you want to delete this delivery address?'),'class'=>'delete','escape'=>false)); ?>
                            </div>
                        </li>
                    </ul>
                <?php } else { ?>
                    <div class="address">
                        <address class="inbox">
                            <h3>Please add your delivery address.</h3>
                            <div class="title-box edit-link"> 
                                <?php echo $this->Html->link($this->Html->tag('i','',array('class'=>'fa fa-plus-circle')).'Add Address', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => 'button-link','escape'=>false)); ?> 
                            </div>
                        </address>
                    </div>
                <?php } ?>
                
                <?php if ($checkaddress) { ?> 
                    
                <div class="radio-btn space20 delivery-address-option">
                <?php $i=0; foreach($checkaddress as $address) {
                    if ($address['DeliveryAddress']['default']==1){
                        $checked = "checked = 'checked'";
                    } else {
                        $checked = "";
                    } ?>
                    
                        <?php if($address['DeliveryAddress']['label'] == 1) { ?>
                            <input type="radio" id="home" name="data[DeliveryAddress][id]" <?php echo $checked;?> value="<?php echo $address['DeliveryAddress']['id'];?>" class="deladdress"/> <label for="home"><span></span>Home Address</label>
                        <?php } else if($address['DeliveryAddress']['label'] == 2){ ?>
                            <input type="radio" id="work" name="data[DeliveryAddress][id]" <?php echo $checked;?> value="<?php echo $address['DeliveryAddress']['id'];?>" class="deladdress"/> <label for="work"><span></span>Work Address</label>
                        <?php } else if($address['DeliveryAddress']['label'] == 3){ ?>
                            <input type="radio" id="other" name="data[DeliveryAddress][id]" <?php echo $checked;?> value="<?php echo $address['DeliveryAddress']['id'];?>" class="deladdress"/> <label for="other"><span></span>Other Address</label>
                        <?php } else if($address['DeliveryAddress']['label'] == 4){ ?>
                            <input type="radio" id="other4" name="data[DeliveryAddress][id]" <?php echo $checked;?> value="<?php echo $address['DeliveryAddress']['id'];?>" class="deladdress"/> <label for="other4"><span></span>Address4</label>
                        <?php } else if($address['DeliveryAddress']['label'] == 5){ ?>
                            <input type="radio" id="other5" name="data[DeliveryAddress][id]" <?php echo $checked;?> value="<?php echo $address['DeliveryAddress']['id'];?>" class="deladdress"/> <label for="other5"><span></span>Address5</label>
                        <?php }
                    $i++; } ?>
                   
                    <?php if($i < 5){ ?>
                   
                    <div class="edit-link add-more-line"> 
                        <?php echo $this->Html->link($this->Html->tag('i','',array('class'=>'fa fa-plus-circle')).'Add More Addresses', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => 'button-link','escape'=>false)); ?> 
                    </div>
                             <?php } ?>
                      </div>
                     <?php } ?> 
            </section>
        </div>
    </div>
</div>
<script>
    
    function getDefaultAddress(){
        var deliveryId = $("input[type='radio'][class='deladdress']:checked").val();
        var storeId = '<?php echo $encrypted_storeId;?>';
        var merchantId = '<?php echo $encrypted_merchantId ; ?>';
        $.ajax({
            type: 'post',
            url: '/Users/getDeliveryAddress',
            data: {'deliveryId': deliveryId,'storeId':storeId,'merchantId':merchantId},					    
            success:function(result){
                if(result){
                   $('#delivery_address').html(result);
                }
            }
        });
    }
    getDefaultAddress();   
    $(document).ready(function () {

        $('.date-select').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: 1,
        });

        $("#Deliveryaddress").validate({
            rules: {
                "data[Store][pickup_time]": {
                    required: true,
                },
                "data[Store][pickup_date]": {
                    required: true,
                },
            },
            messages: {
                "data[Store][pickup_time]": {
                    required: "Please select pickup time"
                },
                "data[Store][pickup_date]": {
                    required: "Please enter your pickup date",
                }
            }
        });
        $('#pickupdata').css('display', 'none');
        $('#StorePickupTime').css('display', 'none');
        $('#StorePickupDate').css('display', 'none');
        $("#pre-order").on('click', function () { // To Show
            $('#pickupdata').css('display', 'block');
            $('#StorePickupTime').css('display', 'block');
            $('#StorePickupDate').css('display', 'block');
        });
        $("#now").on('click', function () {// To hide
            $('#pickupdata').css('display', 'none');
            $('#StorePickupTime').css('display', 'none');
            $('#StorePickupDate').css('display', 'none');
        });
    });
    
    $("input[name='data[DeliveryAddress][id]']:radio").change(function() { 
        var deliveryId = $(this).val();
        var storeId = '<?php echo $encrypted_storeId;?>';
        var merchantId = '<?php echo $encrypted_merchantId ; ?>';
        $.ajax({
            type: 'post',
            url: '/Users/getDeliveryAddress',
            data: {'deliveryId': deliveryId,'storeId':storeId,'merchantId':merchantId},					    
            success:function(result){
                if(result){
                   $('#delivery_address').html(result);
                }
            }
        });
    });
    
    $('#StorePickupDate').on('change', function () {
            var date = $(this).val();
            var type1 = 'Store';
            var type2 = 'pickup_time';
            var type3 = 'StorePickupTime';
            var storeId = '<?php echo $encrypted_storeId; ?>';
            var merchantId = '<?php echo $encrypted_merchantId; ?>';
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3},
                success: function (result) {
                    $('#resvTime').html(result);
                }
            });
        });

</script>