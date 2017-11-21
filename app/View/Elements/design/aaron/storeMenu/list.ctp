<?php
//wordwrap($listItem['name'], 15, "\n", TRUE)
if (isset($categoryList) && $categoryList) {
    ?>
    <div class="panel-group" id="accordion1">
        <?php
        $list = array();
        $c = $k = 0;
        foreach ($categoryList as $key => $list) { // Loop for category
            $k++;
            if ($list['Item']) {
                if ($list['Category']['is_meal'] == 1) {
                    //echo "Zone".$currentTime;
                    $starTime = strtotime($list['Category']['start_time']);
                    $endTime = strtotime($list['Category']['end_time']);
                    //$currentTime = strtotime(date("H:i"));
                    $nowDate = date("Y-m-d H:i:s");
                    $currentTime = date("H:i:s", (strtotime($this->Common->storeTimeZoneUser('', $nowDate))));

                    $currentTime = strtotime($currentTime);
                    if ($currentTime >= $starTime && $currentTime <= $endTime) {
                        
                    } else {
                        continue;
                    }
                }
            } else {
                continue;
            }

            $id_name = "category_id" . $list['Category']['id'];
            $store_id = $decrypt_storeId;
            $category_id = $list['Category']['id'];
            ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php
                    if ($list['Category']['id'] == $decrypt_catId) {
                        $c = 0;
                    } else {
                        $c = 1;
                    }
                    $mStar = $mText = "";
                    if ($list['Category']['is_mandatory'] > 0) {
                        $mText = "Mandatory Category";
                        $mStar = "<sup style='color:red'>*</sup>";
                    }
                    ?>

                    <h4 class="panel-title" rel='<?php echo $store_id; ?>' id='<?php echo $id_name; ?>' category_id='<?php echo $list['Category']['id']; ?>' size-type='<?php echo $list['Category']['is_sizeonly']; ?>'><a data-toggle="collapse" data-parent="#accordion1"                                            href="<?php echo "#collapseTwo" . $k; ?>" title="<?php echo $mText; ?>">
                            <?php echo $list['Category']['name']; ?>
                            <span class="arrow-down"><i class="fa fa-angle-down fa-2x fa-angle-up" aria-hidden="true"></i></span>
                            <?php echo $mStar; ?>
                        </a>
                    </h4>
                </div>

                <div id="<?php echo "collapseTwo" . $k; ?>" class="panel-collapse collapse <?php echo ($c == 0) ? "in" : ''; ?>">
                    <div class="panel-body nested-ac">
                        <div class="panel-group " id="accordion2">
                            <?php
                            $i = $j = 0;
                            foreach ($list['Item'] as $listItem) {// Loop for Item
                                $j++;
                                $id_name = "item_id" . $listItem['id'];
                                $store_id = $decrypt_storeId;
                                $category_id = $listItem['category_id'];
                                $item_id = $listItem['id'];

                                $nowDate = date("Y-m-d H:i:s");
                                $current_date = strtotime(date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', $nowDate)))));
                                $starDate = strtotime($listItem['start_date']);
                                $endDate = strtotime($listItem['end_date']);
                                if ($item_id == $decrypt_itemId) {
                                    $i = 0;
                                } else {
                                    $i = 1;
                                }
                                if ($listItem['mandatory_item_units'] > 0 && $list['Category']['is_mandatory'] > 0) {
                                    //$manItemUnit = ' <p><small>' . $listItem['mandatory_item_units'] . ' unit of this item is mandatory for checkout.</small></p>';
                                    $iStar = "<sup style='color:red'>*</sup>";
                                } else {
                                    //$manItemUnit = '';
                                    $iStar = '';
                                }
                                if ($listItem['is_seasonal_item'] == 1) {
                                    if ($current_date >= $starDate && $current_date <= $endDate) {
                                        ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <?php
                                                    echo $this->Html->link($listItem['name'] . $iStar, '#collaspe' . $k . $j, array('escape' => false, 'class' => 'item_link', 'data-toggle' => "collapse", "data-parent" => "#accordion2", "href" => "#collapseInner" . $j));
                                                    if (!empty($listItem['Interval'])) {
                                                        echo '<span style="font-size:15px;">(' . $listItem['Interval']['name'] . ')</span>';
                                                    }
                                                    ?>

                                                    <span class="pos-right">
                                                        <strong>
                                                            <?php
                                                            echo "<sup>$</sup>" . number_format($listItem['applicablePrice'], 2);
                                                            ?>
                                                        </strong>      
                                                        <?php echo $this->Html->link('<i class="fa fa-plus-circle"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'itemprop', 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'])); ?>
                    <!--                                                        <a class="item_link parent_category_id5617" rel="44376" id="item_id44376" store_id="134" item_parent_id="5617" size-type="<?php echo $list['Category']['is_sizeonly'] ?>" data-toggle="collapse" data-parent="#accordion2" href="<?php echo '#collaspe' . $k . $j; ?>"><i class="fa fa-plus-circle"></i></a>-->
                                                    </span>
                                                </h4>
                                            </div>
                                            <div id="<?php echo 'collaspe' . $k . $j; ?>" class="panel-collapse collapse <?php echo ($i == 0) ? "in" : ''; ?>">
                                                <div class="panel-body">
                                                    <div class="inner-menu">
                                                        <?php if (isset($listItem['image']) && $listItem['image']) { ?>
                                                            <div class="food-img">
                                                                <?php
                                                                $image = "/MenuItem-Image" . "/" . $listItem['image'];
                                                                echo $this->Html->image($image, array('id' => 'productImage'));
                                                                $price_class = 'product-price';
                                                                echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 1));
                                                                $hClass = 'ImageNotEmpty';
                                                                ?>
                                                            </div>
                                                            <?php
                                                        } else {
                                                            echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 0));
                                                            $price_class = 'product-price product-price-single';
                                                            $hClass = 'ImageEmpty';
                                                        }
                                                        ?>
                                                        <div class="item-description extra-height">
                                                            <?php echo nl2br(@$listItem['description']); ?>
                                                        </div>
                                                        <div class="price clearfix">
                                                            <?php //if ($hClass == 'ImageEmpty') {    ?>

                                                            <?php //}    ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <!--                                                            <span class="price-q">
                                                            <?php
                                                            echo "$" . number_format($listItem['applicablePrice'], 2);
                                                            ?>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </span>-->

                                                            <?php
                                                            if (empty($listItem['offerDisplay']) && empty($listItem['ItemOfferDisplay'])) {


                                                                echo $this->Html->link('ADD', 'javascript:void(0)', array('class' => 'add-menu theme-bg-1 itemprop parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly']));
                                                            }
                                                            ?>
                                                        </div>

                                                        <div class="menu-offer-wrap">
                                                            <?php if (!empty($listItem['offerDisplay'])) { ?>
                                                                <div class="row-divide clearfix">
                                                                    <h3>Available Offers</h3>
                                                                    <ul class="checkbox-listing clearfix">
                                                                        <?php foreach ($listItem['offerDisplay'] as $doff) { ?>
                                                                            <li><i class="fa fa-pencil-square-o"></i>
                                                                                <?php echo $doff; ?>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            <?php }
                                                            ?>

                                                            <?php if (!empty($listItem['ItemOfferDisplay'])) { ?>
                                                                <div class="row-divide clearfix">
                                                                    <h3>Extended Offers</h3>
                                                                    <ul class="checkbox-listing clearfix">
                                                                        <?php foreach ($listItem['ItemOfferDisplay'] as $iOff) { ?>
                                                                            <li><i class="fa fa-pencil-square-o"></i>
                                                                                <?php echo $iOff; ?>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            <?php }
                                                            ?>
                                                            <?php
                                                            if (!empty($listItem['offerDisplay']) || !empty($listItem['ItemOfferDisplay'])) {

                                                                echo $this->Html->link('ADD', 'javascript:void(0)', array('class' => 'add-menu theme-bg-1 itemprop parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly']));
                                                            }
                                                            ?>
                                                        </div>  

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                    } else {
                                        continue;
                                    }
                                } else {
                                    ?>

                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <?php
                                                echo $this->Html->link($listItem['name'] . $iStar, '#collaspe' . $k . $j, array('escape' => false, 'class' => 'item_link parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'], 'data-toggle' => "collapse", "data-parent" => "#accordion2", "href" => "#collapseInner" . $j));

                                                if (!empty($listItem['Interval'])) {
                                                    echo '<span style="font-size:15px;">(' . $listItem['Interval']['name'] . ')</span>';
                                                }
                                                ?>
                                                <span class="pos-right">
                                                    <strong>
                                                        <?php
                                                        echo "<sup>$</sup>" . number_format($listItem['applicablePrice'], 2);
                                                        ?>
                                                    </strong>      
                                                    <?php echo $this->Html->link('<i class="fa fa-plus-circle"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'itemprop', 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'])); ?>
                <!--                                                    <a class="item_link parent_category_id5617" rel="44376" id="item_id44376" store_id="134" item_parent_id="5617" size-type="<?php echo $list['Category']['is_sizeonly'] ?>" data-toggle="collapse" data-parent="#accordion2" href="<?php echo '#collaspe' . $k . $j; ?>"><i class="fa fa-plus-circle"></i></a>-->
                                                </span>

                                            </h4>
                                        </div>
                                        <div id="<?php echo 'collaspe' . $k . $j; ?>" class="panel-collapse collapse <?php echo ($i == 0) ? "in" : ''; ?>">
                                            <div class="panel-body">
                                                <div class="inner-menu">
                                                    <?php if (isset($listItem['image']) && $listItem['image']) { ?>
                                                        <div class="food-img">
                                                            <?php
                                                            $image = "/MenuItem-Image" . "/" . $listItem['image'];
                                                            echo $this->Html->image($image, array('id' => 'productImage'));
                                                            $price_class = 'product-price';
                                                            echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 1));
                                                            $hClass = 'ImageNotEmpty';
                                                            ?>
                                                        </div>
                                                        <?php
                                                    } else {
                                                        echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 0));
                                                        $price_class = 'product-price product-price-single';
                                                        $hClass = 'ImageEmpty';
                                                    }
                                                    ?>
                                                    <div class="item-description extra-height">
                                                        <?php echo nl2br(@$listItem['description']); ?>

                                                    </div>
                                                    <div class="price clearfix">
                                                        <?php //if ($hClass == 'ImageNotEmpty') {    ?>
                                                        <?php //}   ?>
                <!--                                                        <span class="price-q">
                                                        <?php
                                                        echo "$" . number_format($listItem['applicablePrice'], 2);
                                                        ?>
                                                        </span>-->

                                                        <?php
                                                        if (empty($listItem['offerDisplay']) && empty($listItem['ItemOfferDisplay'])) {

                                                            echo $this->Html->link('ADD', 'javascript:void(0)', array('class' => 'add-menu theme-bg-1 itemprop parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly']));
                                                        }
                                                        ?>
                                                    </div>

                                                    <div class="menu-offer-wrap">
                                                        <?php if (!empty($listItem['offerDisplay'])) { ?>
                                                            <div class="available-offers">
                                                                <h4>Available Offers</h4>
                                                                <ul>
                                                                    <?php foreach ($listItem['offerDisplay'] as $doff) { ?>
                                                                        <li><i class="fa fa-pencil-square-o"></i>
                                                                            <?php echo $doff; ?>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        <?php }
                                                        ?>
                                                        <?php if (!empty($listItem['ItemOfferDisplay'])) { ?>
                                                            <div class="available-offers">
                                                                <h4>Extended Offers</h4>
                                                                <ul>
                                                                    <?php foreach ($listItem['ItemOfferDisplay'] as $iOff) { ?>
                                                                        <li><i class="fa fa-pencil-square-o"></i>
                                                                            <?php echo $iOff; ?>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        <?php }
                                                        ?>
                                                        <?php
                                                        if (!empty($listItem['offerDisplay']) || !empty($listItem['ItemOfferDisplay'])) {
                                                            echo $this->Html->link('ADD', 'javascript:void(0)', array('class' => 'add-menu theme-bg-1 itemprop parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly']));
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <?php
                                $i++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $c++;
        }
    }
    ?>

</div>



<script>
    $(document).ready(function () {
        "<?php if ($decrypt_itemId) { ?>"
            var id = 'item_id' + "<?php echo $decrypt_itemId; ?>"
            $('html, body').animate({
                scrollTop: $("#" + id).offset().top - 18
            }, '50');
            "<?php } ?>"
        $('.itemprop').click(function () {
            //$('#loading').show();
            var item_id = $(this).attr('rel');
            $('#itemIdpopup').val(item_id);
            var categoryId = $(this).attr('item_parent_id');
            var storeId = $(this).attr('store_id');
            var sizeType = $(this).attr('size-type');
            $.ajax({
                url: "/Products/fetchProduct",
                type: "Post",
                data: {'item_id': item_id, 'categoryId': categoryId, 'storeId': storeId, 'sizeType': sizeType},
                success: function (result) {
                    checkJson = IsJsonString(result);
                    if (checkJson) {
                        var obj = jQuery.parseJSON(result);
                        if (obj.status == 'Error') {
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(obj.msg);
                            return false;
                        }
                    } else {
                        if (result) {
                            $('#item-modal').html(result);
                            $('#item-modal').modal('show');
                        }
                    }
                }
            });
            setTimeout(function () {
                //$('#loading').hide();
            }, 700);
        });
    });
    function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

</script>












