
    <article>
	<h2><span>MENU ITEMS</span></h2>
    <?php if (isset($categoryList) && $categoryList) { 
        ?>
        <ul class="menu-list clearfix">
            <?php
            $list = array();
            foreach ($categoryList as $list) { // Loop for category
                if ($list['Item']) {
                    if ($list['Category']['is_meal'] == 1) {
                        $starTime = strtotime($list['Category']['start_time']);
                        $endTime = strtotime($list['Category']['end_time']);                        
                        $nowDate = date("Y-m-d H:i:s");
                        $currentTime = date("H:i:s", (strtotime($this->Hq->storeTimeZoneUserMerchant('', $nowDate,$decrypt_storeId))));  
                        
                        $currentTime=strtotime($currentTime);
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
                <li>
                    <h3 class='<?php echo "category_link parent_id" . $category_id; ?>' rel='<?php echo $store_id; ?>' id='<?php echo $id_name; ?>' category_id='<?php echo $list['Category']['id']; ?>' size-type='<?php echo $list['Category']['is_sizeonly']; ?>'><?php echo $list['Category']['name']; ?><span></span> </h3><ul>
                <?php
                foreach ($list['Item'] as $listItem) { // Loop for Item 
                    $current_date = strtotime(date('Y-m-d'));
                    $id_name = "item_id" . $listItem['id'];
                    $store_id = $decrypt_storeId;
                    $category_id = $listItem['category_id'];
                    $item_id = $listItem['id'];
                    $starDate = strtotime($listItem['start_date']);
                    $endDate = strtotime($listItem['end_date']);
                    if ($listItem['is_seasonal_item'] == 1) {
                        if ($current_date >= $starDate && $current_date <= $endDate) {
                            ?>
                                        
                                <li class='itemName'><?php echo $this->Html->link(wordwrap($listItem['name'],15,"\n",TRUE), 'javascript:void(0)', array('class' => 'item_link parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'])); ?></li>
                        <?php } else {
                        }
                        ?>   
                            <?php } else { ?>
                                <li class='itemName'><?php echo $this->Html->link(wordwrap($listItem['name'],15,"\n",TRUE), 'javascript:void(0)', array('class' => 'item_link parent_category_id' . $category_id, 'rel' => $item_id, 'id' => $id_name, 'store_id' => $store_id, 'item_parent_id' => $category_id, 'size-type' => $list['Category']['is_sizeonly'])); ?></li>


                            <?php }
                        } ?>

                    </ul>
                </li>
            <?php } ?>
        </ul>
        

<?php } ?>        
        </article>
	

<script>
$(document).ready(function () {
    $('.item_link').click(function () {
        var itemId = $(this).attr('rel');
        var categoryId = $(this).attr('item_parent_id');
        var storeId = $(this).attr('store_id');
        var sizeType = $(this).attr('size-type');
        
	$.ajax({
	    url: "/hqmenus/menuFetchProduct",
	    type: "Post",
	    data: {item_id: itemId, categoryId: categoryId, storeId: storeId, sizeType: sizeType},
	    success: function (result) {
		if (result) {
//		    if(window.screen.width < 700){
//			$(window).scrollTop($('#item-panel').offset().top);
//		    } else {
//			$(window).scrollTop($('#item-panel').offset().top);
//		    }
                    $('#showMenuData').removeClass('hidden');
		    $('#item-panel').html(result);
		}
	    }
	});
    });
});
</script>