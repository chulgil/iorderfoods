

<?php
//wordwrap($listItem['name'], 15, "\n", TRUE)
if (isset($categoryList) && $categoryList) {
    ?>

    <div class="left-menu">
        <div class="common-title">
            <h3>MENU</h3>
        </div>
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
                        <h4 class="panel-title" rel='<?php echo $store_id; ?>' id='<?php echo $id_name; ?>' category_id='<?php echo $list['Category']['id']; ?>' size-type='<?php echo $list['Category']['is_sizeonly']; ?>'><a data-toggle="collapse" data-parent="#accordion1"                                            href="<?php echo "#collapseTwo" . $k; ?>">
                                <?php echo $list['Category']['name']; ?>
                                <span class="arrow-down"><i class="fa fa-angle-down fa-2x" aria-hidden="true"></i></span></a>
                        </h4>
                    </div>
                    <div id="<?php echo "collapseTwo" . $k; ?>" class="panel-collapse collapse <?php echo ($c == 0) ? "in" : ''; ?>">
                        <div class="panel-body nested-ac">
                            <div class="panel-group " id="accordion2">
                                <?php
                                $i = $j = 0;
                                foreach ($list['Item'] as $listItem) { // Loop for Item
                                    $j++;
                                    $id_name = "item_id" . $listItem['id'];
                                    $store_id = $decrypt_storeId;
                                    $category_id = $listItem['category_id'];
                                    $item_id = $listItem['id'];

                                    $nowDate = date("Y-m-d H:i:s");
                                    $current_date = strtotime(date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', $nowDate)))));
                                    $starDate = strtotime($listItem['start_date']);
                                    $endDate = strtotime($listItem['end_date']);
                                    if ($listItem['is_seasonal_item'] == 1) {
                                        if ($current_date >= $starDate && $current_date <= $endDate) {
                                            ?>
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <?php
                                                        echo $this->Html->link($listItem['name'], '#collaspe' . $k . $j, array('class' => 'item_link parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'], 'data-toggle' => "collapse", "data-parent" => "#accordion2", "href" => "#collapseInner" . $j));
                                                        if (!empty($listItem['Interval'])) {
                                                            echo '<span style="font-size:15px;">(' . $listItem['Interval']['name'] . ')</span>';
                                                        }
                                                        ?>
                                                    </h4>
                                                </div>
                                                <div id="<?php echo 'collaspe' . $k . $j; ?>" class="panel-collapse collapse <?php echo ($i == 0) ? "in" : ''; ?>">
                                                    <div class="panel-body">
                                                        <div class="inner-menu">
                                                            <div class="food-img">
                                                                <?php
                                                                if (isset($listItem['image']) && $listItem['image']) {
                                                                    $image = "/MenuItem-Image" . "/" . $listItem['image'];
                                                                    echo $this->Html->image($image, array('id' => 'productImage'));
                                                                    $price_class = 'product-price';
                                                                    echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 1));
                                                                    $hClass = 'ImageNotEmpty';
                                                                } else {
                                                                    echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 0));
                                                                    $price_class = 'product-price product-price-single';
                                                                    $hClass = 'ImageEmpty';
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="price clearfix">
                                                                <?php if ($hClass == 'ImageEmpty') { ?>
                                                                    <div class="extra-height"></div>
                                                                <?php } ?>
                                                                <span class="price-q">
                                                                    <?php
                                                                    echo "$" . number_format($listItem['applicablePrice'], 2);
                                                                    ?>

                                                                </span>

                                                                <?php echo $this->Html->link('ADD', 'javascript:void(0)', array('class' => 'add-menu itemprop parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'])); ?>
                                                            </div>


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
                                                                    <h3>Item Offers</h3>
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
                                                    echo $this->Html->link($listItem['name'], '#collaspe' . $k . $j, array('class' => 'item_link parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'], 'data-toggle' => "collapse", "data-parent" => "#accordion2", "href" => "#collapseInner" . $j));

                                                    if (!empty($listItem['Interval'])) {
                                                        echo '<span style="font-size:15px;">(' . $listItem['Interval']['name'] . ')</span>';
                                                    }
                                                    ?>
                                                </h4>
                                            </div>
                                            <div id="<?php echo 'collaspe' . $k . $j; ?>" class="panel-collapse collapse <?php echo ($i == 0) ? "in" : ''; ?>">
                                                <div class="panel-body">
                                                    <div class="inner-menu">
                                                        <div class="food-img">
                                                            <?php
                                                            if (isset($listItem['image']) && $listItem['image']) {
                                                                $image = "/MenuItem-Image" . "/" . $listItem['image'];
                                                                echo $this->Html->image($image, array('id' => 'productImage'));
                                                                $price_class = 'product-price';
                                                                echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 1));
                                                                $hClass = 'ImageNotEmpty';
                                                            } else {
                                                                echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 0));
                                                                $price_class = 'product-price product-price-single';
                                                                $hClass = 'ImageEmpty';
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="price clearfix">
                                                            <?php if ($hClass == 'ImageNotEmpty') { ?>
                                                                <div class="extra-height"></div>
                                                            <?php } ?>
                                                            <span class="price-q">
                                                                <?php
                                                                echo "$" . number_format($listItem['applicablePrice'], 2);
                                                                ?>
                                                            </span>

                                                            <?php echo $this->Html->link('ADD', 'javascript:void(0)', array('class' => 'add-menu itemprop parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'])); ?>
                                                        </div>


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
                                                                <h4>Item Offers</h4>
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
</div>


<script>
    $(document).ready(function () {
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












