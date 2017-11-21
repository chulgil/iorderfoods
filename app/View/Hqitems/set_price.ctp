<?php
echo $this->Html->script('bootstrap-multiselect');
echo $this->Html->css('bootstrap-multiselect');
?>
<style>
    input[type="radio"], input[type="checkbox"] {
        line-height: normal;
        margin: 4px 1% 0;
    }
    input[type="text"]{
        /*line-height: normal;*/

        width:100%;
    }
    .price-input{
        /*display: inline;*/
        text-align: center;
        height: 32px;
        padding: 4px;
    }
    td{

        height: 50px;
        padding: 4px;
    }
    .label-font-size{
        font-size: 16px;
    }

    .td-price{
        text-align: left;
        font-size: 12px;
        width: 100px;
        word-break: break-all;
    }
    /*table { border: 2px ridge; }*/
    /*tr { border: 1px ridge; }*/

    .noFound{
        margin-left: 50px;
    }
    .table-border{
        border:1px solid #ccc;
    }
    /***********Ranjeet Css*************/
    label { font-weight: 500;}

    th { font-size: 13px;  
         font-weight: 400; 
         word-break: keep-all;
         text-align: center;
    }

    .table thead { 
        background: #222222 none repeat scroll 0 0;  
        color: #fff;  height: 65px;
    }
    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
        border-top: 1px solid #ddd;  line-height: 1.1;  padding: 6px;  vertical-align: top !important;}

    .divbox{
        float:left;
        width:105px;
        height:100px;
        padding:3px;
        background-color:#cccccc;
        border:1px solid #808080;
        margin:4px;
        font-size:12px;
    }
    .divboxname{
        height:60%;
        word-break:keep-all;
    }
    .divboxinput{
        height:30%;
        margin-top:2%;
    }
    .preferncename{
        font-size:13px;
        font-weight:bold;
        padding:2px;
    }
    .preferncehead{
        font-size:16px;
        font-weight:bold;
        width:100px;       
        padding:2px;
        margin-bottom:5px;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <h3>Set Item Price</h3>		
        <hr>
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>
<div class="row">    
    <?php echo $this->Form->create('Item', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'setItemPrice')); ?>
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Store</label>
                    <?php
                    $merchantList = $this->Common->getHQStores($this->Session->read('merchantId'));
                    echo $this->Form->input('Item.store_id', array('default'=>@$itemDetail['Item']['store_id'],'options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Please Select Store', 'required' => true,'disabled'=>true));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">		 
            <h2><?php echo $itemDetail['Item']['name']; ?></h2>
            <?php echo $this->Form->input('Item.id', array('value' => $itemDetail['Item']['id'], 'type' => 'hidden', 'label' => false, 'div' => false)); ?>
        </div>

        <div>
            <div class="preferncehead">Size</div>
            <div>
                <?php
                $sizeOption = array();
                foreach ($itemDetail['ItemPrice'] as $size) {
                    $sizeOption[$size['Size']['id']] = $size['Size']['size'];
                }

                echo $this->Form->input('Item.size_id', array('type' => 'radio', 'options' => $sizeOption, 'label' => false, 'div' => false));
                ?>
            </div><hr />
            <div class="ajaxSetPrice"><div class="preferncehead">Preferences</div>

                <div class="col-lg-12">
                    <?php if (isset($itemPreference) && !empty($itemPreference)) { ?>
                        <?php foreach ($itemPreference as $preference) { ?>
                            <?php if (count($preference['Type']['SubPreference']) > 0) { ?>

                                <div class="row">
                                    <div class="preferncename">
                                        <?php echo $preference['Type']['name']; ?>
                                    </div>

                                    <?php
                                    foreach ($preference['Type']['SubPreference'] as $subPreference) {
                                        $flag = true;
                                        ?>

                                        <div class="divbox"> 
                                            <div class="divboxname"><?php echo $subPreference['name']; ?></div>
                                            <div class="divboxinput">
                                                <?php
                                                if (isset($subPreferencePriceDetail) && !empty($subPreferencePriceDetail)) {
                                                    foreach ($subPreferencePriceDetail as $subPreferencePrice) {
                                                        if ($subPreference['id'] == $subPreferencePrice['SubPreferencePrice']['sub_preference_id']) {
                                                            $flag = false;
                                                            $price = ($subPreferencePrice['SubPreferencePrice']['price'] != 0) ? $subPreferencePrice['SubPreferencePrice']['price'] : '';
                                                            echo $this->Form->input('SubPreferencePrice.EditPrice.' . $subPreferencePrice['SubPreferencePrice']['id'], array('type' => 'text', 'class' => 'form-control price-input', 'value' => $price, 'label' => false, 'div' => false));
                                                        }
                                                    }
                                                }
                                                if ($flag) {
                                                    echo $this->Form->input('SubPreferencePrice.AddPrice.' . $subPreference['id'], array('type' => 'text', 'class' => 'form-control price-input', 'label' => false, 'div' => false));
                                                }
                                                ?>

                                            </div>
                                        </div>

                                    <?php } ?>
                                </div>  
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="noFound">
                            <label>No preferences is found.. </label>
                        </div>
                    <?php } ?><hr />
                    <hr />
                </div>

                <div class="preferncehead">Add-ons</div>

                <div class="col-lg-12">                     

                    <?php if (isset($itemAddOns) && !empty($itemAddOns)) { ?>
                        <?php foreach ($itemAddOns as $addOns) { ?>	
                            <div class="row">
                                <?php if (count($addOns['SubAddOns']) > 0) { ?> 
                                    <div class="preferncename"><?php echo $addOns['Topping']['name']; ?></div>                                                 <?php
                                    foreach ($addOns['SubAddOns'] as $subAddOns) {
                                        $flag = true;
                                        ?>  
                                        <div class="divbox"> 
                                            <div class="divboxname"><?php echo $subAddOns['name']; ?> </div>
                                            <div class="divboxinput">
                                                <?php
                                                if (isset($subAddOnsPriceDetail) && !empty($subAddOnsPriceDetail)) {
                                                    foreach ($subAddOnsPriceDetail as $subAddOnsPrice) {
                                                        if ($subAddOns['id'] == $subAddOnsPrice['ToppingPrice']['topping_id']) {
                                                            $flag = false;
                                                            $price = ($subAddOnsPrice['ToppingPrice']['price'] != 0) ? $subAddOnsPrice['ToppingPrice']['price'] : '';
                                                            echo $this->Form->input('SubAddOnsPrice.EditPrice.' . $subAddOnsPrice['ToppingPrice']['id'], array('type' => 'text', 'class' => 'form-control price-input', 'value' => $price, 'label' => false, 'div' => false));
                                                        }
                                                    }
                                                }
                                                if ($flag) {
                                                    echo $this->Form->input('SubAddOnsPrice.AddPrice.' . $subAddOns['id'], array('type' => 'text', 'class' => 'form-control price-input', 'label' => false, 'div' => false));
                                                }
                                                ?>
                                            </div>
                                        </div>    

                                    <?php } ?>                                                        
                                <?php } ?>
                            </div>
                        <?php } ?>

                    <?php } else { ?>
                        <div class="noFound">
                            <label>No add-ons is found.. </label>
                        </div>
                    <?php } ?>                        
                </div>
            </div></div>
        <div class="col-lg-12"> <br> </div>
        <div class="col-lg-12">              
            <div class="row">
                <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
                <?php echo $this->Html->link('Cancel', "/hqitems/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
            </div>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->

<script>
    $("input[name='data[Item][size_id]']:radio").change(function () {
        var sizeId = $("[name='data[Item][size_id]']:checked").val();
        var itemId = $("#ItemId").val();
        var storeId = $("#ItemStoreId").val();
        if (sizeId) {
            $.ajax({
                type: 'post',
                url: '/hqitems/ajaxSetPrice',
                data: {'itemId': itemId, 'sizeId': sizeId,'storeId':storeId},
                success: function (result) {
                    if (result) {
                        $(".ajaxSetPrice").html(result);

                    }
                }
            });
        }
    });
</script>