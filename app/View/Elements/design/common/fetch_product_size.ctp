<?php
$preferencearray = array();
$minmax = array();
$minmaxTopping = '';
//if ($productInfo['Item']['sizeOnly'] == 2) { // Type
if (isset($productInfo['ItemType']) && !empty($productInfo['ItemType'])) { // Type
    if ($productInfo['ItemType']) {
        $options = array();
        foreach ($productInfo['ItemType'] as $type) {
            if ($type['Type']) {
                if ($type['Type']['max_value'] > 0) {
                    if ($type['Type']['max_value'] > 0) {
                        if ($type['Type']['min_value'] == 0 && $type['Type']['max_value'] > 0) {
                            if ($type['Type']['max_value'] == 1) {
                                $minmax[$type['Type']['name']][] = 'Choose up to 1 item.';
                            } else {
                                $minmax[$type['Type']['name']][] = 'Choose up to ' . $type['Type']['max_value'] . ' items.';
                            }
                        } elseif ($type['Type']['min_value'] == $type['Type']['max_value']) {
                            if ($type['Type']['min_value'] == 1) {
                                $minmax[$type['Type']['name']][] = 'Choose 1 item.';
                            } else {
                                $minmax[$type['Type']['name']][] = 'Choose ' . $type['Type']['max_value'] . ' items.';
                            }
                        } else {
                            if ($type['Type']['min_value'] == 1) {
                                $minmax[$type['Type']['name']][] = 'Choose at least 1 item.';
                            } else {
                                $minmax[$type['Type']['name']][] = 'Choose at least ' . $type['Type']['min_value'] . ' items.';
                            }
                        }
                    }
                    $minmax[$type['Type']['name']][] = $type['Type']['max_value'];
                }
                if ($type['Type']['price'] <= 0) {
                    $options[$type['Type']['id']] = $type['Type']['name'];
                } else {
                    $options[$type['Type']['id']] = $type['Type']['name'] . ' ($' . number_format($type['Type']['price'], 2) . ')';
                }

                if (isset($type['Type']['SubPreference']) && !empty($type['Type']['SubPreference'])) {
                    foreach ($type['Type']['SubPreference'] as $key => $preferenceData) {
                        $preferencearray[$type['Type']['name']][$key]['subpreferncename'] = $preferenceData['name'];
                        $preferencearray[$type['Type']['name']][$key]['id'] = $preferenceData['id'];
                        $preferencearray[$type['Type']['name']][$key]['type_id'] = $preferenceData['type_id'];
                        //pr($preferenceData);
                        if (isset($preferenceData['SubPreferencePrice']) && !empty($preferenceData['SubPreferencePrice']['price'])) {
                            $preferencearray[$type['Type']['name']][$key]['price'] = $preferenceData['SubPreferencePrice']['price'];
                        }
                        /*
                          else if($preferenceData['price']>0){
                          $preferencearray[$type['Type']['name']][$key]['price']=$preferenceData['price'];
                          } */ else {
                            $preferencearray[$type['Type']['name']][$key]['price'] = 0;
                        }
                        //$preferencearray[$type['Type']['name']][$key]['price']=$preferenceData['price'];
                    }
                }
            }
        }
        ?>

        <?php if ($preferencearray) { ?>
            <div class="check-more prefernceAndAddOns">
                <h3>PREFERENCE</h3>
                <?php
                $j = 0;
                foreach ($preferencearray as $preference => $subpreferencedata) {
                    ?>
                    <div class="sub-menu-listing">
                        <h4><?php echo $preference; ?><?php if (!empty($minmax[$preference][0])) { ?>
                                <em>*</em><i>(<?php echo $minmax[$preference][0]; ?>)</i>
                            <?php } ?>
                        </h4>
                        <div class="check-more countPreferences">
                            <ul class="add-check-list clearfix">
                                <?php
                                $k = 0;
                                foreach ($subpreferencedata as $vkey => $predata) {
                                    if ($predata['price'] > 0) {
                                        $price = '$' . $predata['price'];
                                    } else {
                                        $price = '';
                                    }
                                    ?>
                                    <li>
                                        <div class="select-left sl-inner-wrap">
                                            <?php
                                            echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.id', array('type' => 'checkbox', 'label' => $predata['subpreferncename'], 'class' => 'item_type subpreference' . $predata["id"] . ' typeId_' . $predata["type_id"], 'value' => $predata['id'], 'div' => false));
                                            ?>
                                            <?php
                                            echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.type_id', array('type' => 'hidden', 'value' => $predata['type_id'], 'class' => 'subpre' . $predata['id']));
                                            echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.name', array('type' => 'hidden', 'value' => $predata['subpreferncename'], 'div' => false));
                                            echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.price', array('type' => 'hidden', 'value' => $predata['price'], 'div' => false));
                                            ?>
                                            <span class='theme-txt-col-1 subPrice subPrice<?php echo $predata["id"]; ?>' style="display:none;"><?php echo $price; ?></span>
                                        </div>
                                        <div class="select-right spinner-popup" style="display:none;">
                                            <div class="in-spinner-popup">
                                                <?php
                                                $maxVal = 50;
                                                if (!empty($minmax[$preference][1])) {
                                                    $maxVal = ($minmax[$preference][1] > 0) ? $minmax[$preference][1] : 50;
                                                }
                                                ?>
                                                <button type="button" class="btn btn-default pull-right" key="<?php echo $maxVal; ?>">
                                                    <i aria-hidden="true" class="fa fa-plus "></i>
                                                </button>
                                                <?php echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.size', array('class' => 'form-control subPreferenceSize inbox subPreferenceSize' . $predata["id"], 'value' => 1, 'label' => false, 'div' => false, 'rel' => $predata["id"], 'readonly' => 'readonly')); ?>
                                                <button type="button" class="btn btn-default pull-left" key="0">
                                                    <i aria-hidden="true" class="  fa fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                    $k++;
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>


        <?php
        // deletedt===============
    }
}
?>


<?php if (isset($productInfo['Topping']) && !empty($productInfo['Topping'])) { ?>
    <div class="check-more prefernceAndAddOns">
        <h3>ADD-ON</h3>
        <div class="add-check-list">
            <li>
                <?php
                foreach ($productInfo['Topping'] as $top) {
                    if (!empty($top['Topping'])) {
                        $minmaxTopping = '';
                        if ($top['max_value'] > 0) {
                            if ($top['min_value'] == 0 && $top['max_value'] > 0) {
                                if ($top['max_value'] == 1) {
                                    $minmaxTopping = 'Choose up to 1 item.';
                                } else {
                                    $minmaxTopping = 'Choose up to ' . $top['max_value'] . ' items.';
                                }
                            } elseif ($top['min_value'] == $top['max_value']) {
                                if ($top['min_value'] == 1) {
                                    $minmaxTopping = 'Choose 1 item.';
                                } else {
                                    $minmaxTopping = 'Choose ' . $top['max_value'] . ' items.';
                                }
                            } else {
                                if ($top['min_value'] == 1) {
                                    $minmaxTopping = 'Choose at least 1 item.';
                                } else {
                                    $minmaxTopping = 'Choose at least ' . $top['min_value'] . ' items.';
                                }
                            }
                        }
                        ?>
                        <div class="sub-menu-listing">
                            <h4><?php echo $top['name']; ?><?php if (!empty($minmaxTopping)) { ?>
                                    <em>*</em><i>(<?php echo $minmaxTopping; ?>)</i>
                                <?php } ?>
                            </h4>
                            <div class="check-more countAddOns">
                                <ul class="add-check-list clearfix">
                                    <?php
                                    foreach ($top['Topping'] as $topping) {
                                        if (isset($topping['ToppingPrice']) && !empty($topping['ToppingPrice']['price'])) {
                                            $topping['price'] = $topping['ToppingPrice']['price'];
                                        } else {
                                            $topping['price'] = 0;
                                        }
                                        if ($topping['price'] <= 0) {
                                            $top_price = '';
                                        } else {
                                            $top_price = '$' . number_format($topping['price'], 2);
                                        }
                                        ?>
                                        <?php
                                        if ($topping['ItemDefaultTopping']) {
                                            $checked = true;
                                            $top_value = 1;
                                            $top_price = '';
                                        } else {
                                            $checked = false;
                                            $top_value = 2;
                                        }
                                        ?>
                                        <li>
                                            <div class="select-left sl-inner-wrap">
                                                <?php
                                                echo $this->Form->input('Item.toppings.' . $topping['id'] . '.id', array('type' => 'checkbox', 'label' => $topping['name'], 'class' => 'toppings toppings' . $topping["id"] . ' addonId_' . $topping["addon_id"], 'value' => $topping['id'], 'checked' => $checked, 'div' => false));
                                                echo $this->Form->input('Item.toppings.' . $topping['id'] . '.type', array('type' => 'hidden', 'value' => $top_value, 'class' => 'type' . $topping["id"], 'div' => false));
                                                echo $this->Form->input('Item.toppings.' . $topping['id'] . '.addon_id', array('type' => 'hidden', 'value' => $topping['addon_id'], 'class' => 'addon' . $topping["id"]));
                                                echo $this->Form->input('Item.toppings.' . $topping['id'] . '.name', array('type' => 'hidden', 'value' => $topping['name'], 'div' => false));
                                                echo $this->Form->input('Item.toppings.' . $topping['id'] . '.price', array('type' => 'hidden', 'value' => $topping['price'], 'div' => false));
                                                ?>
                                                <span class='theme-txt-col-1 topPrice topPrice<?php echo $topping["id"]; ?>' <?php echo ($checked) ? '' : 'style=display:none;'; ?>><?php echo $top_price; ?></span>
                                            </div>
                                            <div class="select-right spinner-popup" style="<?php echo ($checked) ? '' : 'display:none;'; ?>">
                                                <div class="in-spinner-popup">
                                                    <?php
                                                    if ($topping['no_size'] == 0) {
                                                        $toppingSizes = '';
                                                        $max = ($top['max_value'] > 0) ? $top['max_value'] : 50;
                                                    } else {
                                                        $toppingSizes = 'disabled';
                                                        $max = 0;
                                                    }
                                                    ?>
                                                    <button type="button" class="btn btn-default pull-right" key="<?php echo $max; ?>" <?php echo $toppingSizes; ?>>
                                                        <i aria-hidden="true" class="fa fa-plus "></i>
                                                    </button>
                                                    <?php echo $this->Form->input('Item.toppings.' . $topping['id'] . '.size', array('value' => 1, 'class' => 'form-control toppingSize inbox toppingSize' . $topping['id'], 'label' => false, 'div' => false, 'rel' => $topping['id'])); ?>
                                                    <button type="button" class="btn btn-default pull-left" key="0" <?php echo $toppingSizes; ?>>
                                                        <i aria-hidden="true" class="fa fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php
                        }
                    }
                    ?>
            </li>
        </div>
    </div>
<?php } ?>