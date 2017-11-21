<?php //pr($categoryList); exit;         ?>
<div class="col-3-structure clearfix" id='anchorName'>
    <div class="col-3 form-layout">
        <article>
            <h2><span>MENU ITEMS</span></h2>
            <?php if (isset($categoryList) && $categoryList) { ?>
                <ul class="menu-list clearfix">
                    <?php
                    $list = array();
                    foreach ($categoryList as $list) { // Loop for category
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
                        if ($list['Category']['is_mandatory'] > 0) {
                            $cTest = 'Mandatory Category';
                            $cText = "<sup style='color:red'>*</sup>";
                        } else {
                            $cText = $cTest = "";
                        }
                        ?>
                        <li>
                            <h3 title="<?php echo $cTest; ?>" class='<?php echo "category_link parent_id" . $category_id; ?>' rel='<?php echo $store_id; ?>' id='<?php echo $id_name; ?>' category_id='<?php echo $list['Category']['id']; ?>' size-type='<?php echo $list['Category']['is_sizeonly']; ?>'><?php echo $list['Category']['name']; ?><span></span> <?php echo $cText; ?></h3><ul>
                                <?php
                                foreach ($list['Item'] as $listItem) { // Loop for Item 
                                    $current_date = strtotime(date('Y-m-d'));
                                    $id_name = "item_id" . $listItem['id'];
                                    $store_id = $decrypt_storeId;
                                    $category_id = $listItem['category_id'];
                                    $item_id = $listItem['id'];
                                    $starDate = strtotime($listItem['start_date']);
                                    $endDate = strtotime($listItem['end_date']);
                                    if ($listItem['mandatory_item_units'] > 0 && $list['Category']['is_mandatory'] > 0) {
                                        $tTest = $listItem['mandatory_item_units'] . ' Unit is mandatory for checkout.';
                                        $mText = "<sup style='color:red'>*</sup>";
                                    } else {
                                        $mText = $tTest = "";
                                    }
                                    if ($listItem['is_seasonal_item'] == 1) {
                                        if ($current_date >= $starDate && $current_date <= $endDate) {
                                            ?>
                                            <li class='itemName'><?php echo $this->Html->link(wordwrap($listItem['name'], 15, "\n", false), 'javascript:void(0)', array('title'=>$tTest,'class' => 'item_link parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'])); ?><?php echo $mText;?></li>
                                            <?php
                                        } else {
                                            
                                        }
                                        ?>   
                                    <?php } else { ?>
                                        <li class='itemName'><?php echo $this->Html->link(wordwrap($listItem['name'], 15, "\n", false), 'javascript:void(0)', array('title'=>$tTest,'class' => 'item_link parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'])); ?><?php echo $mText;?></li>
                                        <?php
                                    }
                                }
                                ?>

                            </ul>
                        </li>
                    <?php } ?>
                </ul>


            <?php } ?>
            <div class="call-us-btn text-center"  id="anchorName1">
                <a href="javascript:void(0);" class="green-btn pink-btn btn">Call us @ <?php echo $_SESSION['store_phone']; ?></a>
            </div>
        </article>
        <div id="ItemlistEnd"></div>
    </div>

    <script>

        document.onreadystatechange = function () {
            var state = document.readyState
            if (state == 'interactive') {
                $('#loading').show();
            } else if (state == 'complete') {
                setTimeout(function () {
                    $('#loading').hide();
                }, 500);
            }
        }

        $(document).ready(function () {
            $('.item_link').click(function () {
                $('#loading').show();
                var item_id = $(this).attr('rel');
                var categoryId = $(this).attr('item_parent_id');
                var storeId = $(this).attr('store_id');
                var sizeType = $(this).attr('size-type');
                $.ajax({
                    type: 'post',
                    url: '/Products/fetchCategoryInfo',
                    data: {categoryId: categoryId, storeId: storeId},
                    success: function (result) {
                        $('.float-left').html(result);
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
                                        if (window.screen.width < 700) {

                                            $(window).scrollTop($('div#ItemlistEnd').offset().top + 25);
                                            //$(window).scrollTop($('div#selectOrderTypes').offset().top+20);
                                        } else {
                                            $(window).scrollTop($('#anchorName').offset().top);
                                        }
                                        $('.float-left').html(result);
                                    }
                                }
                            }
                        });
                    }
                });
                setTimeout(function () {
                    $('#loading').hide();
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