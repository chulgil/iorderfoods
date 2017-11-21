<div class="ajaxSetPrice"><div class="preferncehead">Preferences</div>
        <div class="col-lg-12">
            <?php if(isset($itemPreference) && !empty($itemPreference)){ ?>
                <?php foreach($itemPreference as $preference){ ?>
                    <?php if(count($preference['Type']['SubPreference'])>0){ ?>
                        <div class="row">
                            <div class="preferncename"><?php echo $preference['Type']['name']; ?></div>
                                <?php foreach($preference['Type']['SubPreference'] as $subPreference){
                                        $flag=true;
                                ?>
                                <div class="divbox">                                     
                                <div class="divboxname"><?php echo $subPreference['name']; ?></div>
                                <div class="divboxinput">                        

                                <?php
                                    if(isset($subPreferencePriceDetail) && !empty($subPreferencePriceDetail)){
                                            foreach($subPreferencePriceDetail as $subPreferencePrice){
                                                    if($subPreference['id']==$subPreferencePrice['SubPreferencePrice']['sub_preference_id']) { 
                                                            $flag=false;
                                                            $price= ($subPreferencePrice['SubPreferencePrice']['price']!=0) ? $subPreferencePrice['SubPreferencePrice']['price'] : '';
                                                            echo $this->Form->input('SubPreferencePrice.EditPrice.'.$subPreferencePrice['SubPreferencePrice']['id'],array('type'=>'text','class'=>'form-control price-input', 'value'=>$price,'label'=>false,'div'=>false));
                                                    }
                                            }
                                    }
                                    if($flag){
                                            echo $this->Form->input('SubPreferencePrice.AddPrice.'.$subPreference['id'],array('type'=>'text','class'=>'form-control price-input','label'=>false,'div'=>false));
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
            <?php } ?>
            <hr />
                <hr />
</div>



<div class="ajaxSetPrice"><div class="preferncehead">Add-ons</div>
        <div class="col-lg-12">    
                <?php if(isset($itemAddOns) && !empty($itemAddOns)){ ?>
                        <?php foreach($itemAddOns as $addOns){ ?>
                        <div class="row">
                                <?php if(count($addOns['SubAddOns'])>0){ ?>
                                        
                            <div class="preferncename"><?php echo $addOns['Topping']['name']; ?></div>
                                                    
                                <?php foreach($addOns['SubAddOns'] as $subAddOns){
                                        $flag=true;
                                        ?>
                                <div class="divbox"> 
                                <div class="divboxname">        
                                        <?php echo $subAddOns['name']; ?></div>
                                <div class="divboxinput">
                                        <?php
                                                if(isset($subAddOnsPriceDetail) && !empty($subAddOnsPriceDetail)){
                                                        foreach($subAddOnsPriceDetail as $subAddOnsPrice){
                                                                if($subAddOns['id']==$subAddOnsPrice['ToppingPrice']['topping_id']) { 
                                                                        $flag=false;
                                                                        $price = ($subAddOnsPrice['ToppingPrice']['price']!=0) ? $subAddOnsPrice['ToppingPrice']['price'] : '';
                                                                        echo $this->Form->input('SubAddOnsPrice.EditPrice.'.$subAddOnsPrice['ToppingPrice']['id'],array('type'=>'text','class'=>'form-control price-input', 'value'=>$price,'label'=>false,'div'=>false));
                                                                }
                                                        } 
                                                }
                                                if($flag){
                                                        echo $this->Form->input('SubAddOnsPrice.AddPrice.'.$subAddOns['id'],array('type'=>'text','class'=>'form-control price-input','label'=>false,'div'=>false));
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
    </div>