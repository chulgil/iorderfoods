<style>
    .Deladdress{
        float: left;
        font-size: 14px;
        font-weight: 500;
        padding: 7px;
        width:auto
/*        border:2px solid #A19E9E;*/
    }
    
    .Deladdress a{
        font-size:14px;
        font-weight:400;
        color:#2e2eb8;
    }
    .addressdiv{
        float:left;
        width:40%;
    }
    @media screen and (max-width: 480px) {
        .addressdiv{
            float:left;
            width:60%;
        }   
        
    }
</style>
<div class="content  single-frame">
    <div class="wrap">
        <?php echo $this->Session->flash(); ?>
            <?php echo $this->Form->create('Users', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'UsersRegistration', 'url' => array('controller' => 'users', 'action' => 'orderType', $orderId))); ?>
            <div class="clearfix">
                <section class="form-layout sign-up registration-from no-image order-type left-blank">
                    <h2> <span>Select Order Type</span> </h2> 
                    <?php 
                     $PreorderAllowed=$this->Common->checkPreorder();                     
                    if (isset($avalibilty_status) && $PreorderAllowed) {?>
            
                        <ul>
                            <li>  
                                <span>Store is closed, But you can still pre-order.</span>                       
                            </li>
                        </ul>
              
                     <?php }else if(isset($avalibilty_status) && $PreorderAllowed==0 && $store_data['Store']['is_booking_open']==1){?>                         
                        <ul>
                            <li>  
                                <span>Store is closed, But you can still make reservation</span>                       
                            </li>
                        </ul> 
                     <?php    
                     }else if(isset($avalibilty_status) && $PreorderAllowed==0 && $store_data['Store']['is_booking_open']==0){?>
                    
                        <ul>
                            <li>  
                                <span>Store is closed, please come back again!</span>
                            </li>
                        </ul> 
                     <?php } ?>
                    
                    
                    
                    <?php                    
                    if(($store_data['Store']['is_delivery'] == 0) && ($store_data['Store']['is_booking_open'] == 0) && ($store_data['Store']['is_take_away'] == 0)){
                        echo "Store is Closed";
                    } else { ?>
                    <div style="float:left;width:100%;">
                    <ul class="addressdiv">
                        <?php if($store_data['Store']['is_delivery'] == 1 && (!isset($avalibilty_status) || $PreorderAllowed)){                         
                            ?>
                        <li>
                            <span class="title blank">&nbsp;</span>
                            <div class="title-box">
                                <div class="password-remember" style="padding-top:0;"><input type="checkbox" class="ordertype" id="order_type_1"  value="3" name="data[Order][type]" checked /> <label for="order_type_1">Delivery</label></div>
                            </div>
                        </li>
                        
                        
                        
                        
                        <?php if($defaultAddress) {?>
                        <li class="hideadd">
                            <div class="Deladdress"><span>
                                <?php 
                                    echo "<b>".$defaultAddress['DeliveryAddress']['name_on_bell']."</b><br>";
                                    echo $defaultAddress['DeliveryAddress']['address']."<br>";
                                    echo $defaultAddress['DeliveryAddress']['city'].' '.$defaultAddress['DeliveryAddress']['state'].' '.$defaultAddress['DeliveryAddress']['zipcode']."<br>";
                                    echo $defaultAddress['DeliveryAddress']['phone']."<br>";

                                    echo $this->Html->link('Change Address','#',array('class' => 'button', 'id'=>'changeaddress','type'=>'link'));
                                ?>
                                </span></div>
                        </li>    
                            <?php                         
                            }else{ 
                               $userID=$this->Session->read('Auth.User.id');
                               $address=$this->Common->getFirstDeliveryAddress($userID);
                               if(!empty($address)){
                            ?>
                        <li class="hideadd">
                                <div class="Deladdress"><span>
                                    <?php 
                                        echo "<b>".$address['DeliveryAddress']['name_on_bell']."</b><br>";
                                        echo $address['DeliveryAddress']['address']."<br>";
                                        echo $address['DeliveryAddress']['city'].' '.$address['DeliveryAddress']['state'].' '.$address['DeliveryAddress']['zipcode']."<br>";
                                        echo $address['DeliveryAddress']['phone']."<br>"; 
                                        echo $this->Html->link('Change Address','#',array('class' => 'button', 'id'=>'changeaddress','type'=>'link'));
                                    ?>
                                    </span></div>
                        </li>
                               <?php } ?>
                               
                          <?php } ?>
                        
                        <?php } ?>
              
                        
                        
                        
                        
                        
                        
                        
                        
                        <?php if($store_data['Store']['is_take_away'] == 1 && (!isset($avalibilty_status) || $PreorderAllowed)){ ?>
                        <li>
                            <span class="title blank">&nbsp;</span>
                            <div class="title-box">
                                <div class="password-remember" style="padding-top:0;"><input type="checkbox" class="ordertype" id="order_type_2"  value="2" name="data[Order][type]" /> <label for="order_type_2">Pick-Up</label></div>
                            </div>
                        </li>
                        <?php }  
                        if(empty($orderId)){ 
                        if($store_data['Store']['is_booking_open'] == 1){ ?>
                        <li>
                            <span class="title blank">&nbsp;</span>
                            <div class="title-box">
                                <div class="password-remember" style="padding-top:0;"><input type="checkbox" class="ordertype" id="order_type_3"  value="1" name="data[Order][type]" /> <label for="order_type_3">Dine-In</label></div>
                                
                            </div>
                        </li>
                        <?php }  } ?>
                    </ul>
                    </div>
                    
                    
                    
                    <div class="title-box">
                    <label id="data[Order][type]-error" class="error" for="data[Order][type]"></label>
                    </div>
                    
                    <?php if(!isset($avalibilty_status) || $PreorderAllowed || $store_data['Store']['is_booking_open']){?>
                    <div class="button"> <button type="submit" class="btn green-btn">Continue</button> </div>
                    
                    <?php } } ?>
                </section>
            </div>
            <?php echo $this->Form->end(); ?>
    </div>
</div>


<script>    
    
    $(".ordertype").on('click', function () {
        var $box = $(this);
        if ($box.is(":checked")) {
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            $(group).prop("checked", false);
            $box.prop("checked", true);
        } else {
            $box.prop("checked", false);
        }
        var boxval = $(this).val();
        if(boxval==3){
            if($box.is(":checked")){
                $(".Deladdress").show();
                $(".hideadd").show();
            }else{
                $(".Deladdress").hide();
                $(".hideadd").hide();
            }
        }else{
            $(".Deladdress").hide();
            $(".hideadd").hide();
        }
    });

     $("#changeaddress").on('click', function () {
         $("#UsersRegistration").submit();
     });    


    $("#UsersRegistration").validate({
        rules: {
            "data[Order][type]": {
                required: true,
            }
        },
        messages: {
            "data[Order][type]": {
                required: "Please select order type",
            },
        }
    });
</script>
