<?php
/**
 * Created by EBankcardService.
 * User: CharlesLee
 * Date: 10/17/16
 * Time: 3:02 PM
 */

?>
<style>
   #wrap {
    max-width: 100%;
    
#continue_button {
    border-radius: 25px;
    background-color: #f3ebdd;
   
}
</style>

<form id="nzform" action="/MBServices/nzgateway" method="post" >

   <input type="hidden" name="DO_STEP_1" value="true"/>
   <input type="hidden" name="api_key" value="<?=$api_key?>"/>
   <input type="hidden" name="amount" value="<?=$order_info['amount']?>"/>
   <input type="hidden" name="user_id" value="<?=$order_info['user_id']?>"/>
   <input type="hidden" name="customer-vault-id" value="<?=$customer_vault_id?>">

   <div id="wrap">
        <div id="information">
            <div class="field" style="clear:both">
                <div class="lable_div">Name</div>
                <div id="firstname_div">
                    <input class="name" required id="firstname" name="billing-address-first-name" type="text" placeholder="First Name" value="" /></div>
                <div id="lastname_div">
                    <input class="name" required id="lastname" name="billing-address-last-name"  type="text" placeholder="Last Name" value="" /></div>
            </div>

            <div class="field">
                <div class="lable_div">Billing Adress</div>
                <div><input id="address" class="longtext" required type="text" placeholder="Address" name="billing-address-address1" value=""/></div>
                <div>
                    <div id="city_div">
                        <input class="city" id="city" type="text" placeholder="City" name="billing-address-city" value=""/></div>
                    <div id="state_div">
                        <div><input id="state" class="longtext" type="text" placeholder="State" name="billing-address-state" value="" /></div>
                    </div>
                </div>
                <div><input id="zip" class="longtext" type="text" placeholder="Zip-Code" name="billing-address-zip" value="" /></div>
            </div><br>
            <div>Amount : <?php echo '$'.$order_info['amount'];?> </div>
            <br>

            <div class="field">
                <div class="lable_div1"><div style="float:left;">
                <input id="ck_nzsafe" name="use_vault" type="checkbox" checked="true" /><label for="ck_nzsafe"></label></div>
                <div style="color:gray;"> &nbsp;&nbsp;Update nz safe credit card with the information above.</div></div>
                <div class="lable_div2">✔&nbsp;&nbsp;Your credit card information will be securely encrypted and stored in NZ safe</div>
                <div class="lable_div2">✔&nbsp;&nbsp;NZ safe is a safe and secure feature of NZ Gateway which fully supports the latest PCI security standard</div>
            </div>
            </br>

        </div>
        <div class="field">
            <div id="input_field">
                <div><input type="submit" value="Continue" id="continue_button" /></div>
            </div>
        </div>
    </div>
</form>