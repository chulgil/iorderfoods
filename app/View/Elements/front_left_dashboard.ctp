<?php

//echo $this->params['controller'];die;
if($this->params['controller']=='users' || $this->params['controller']=='Users'  || $this->params['controller']=='Orders' || $this->params['controller']=='orders'|| $this->params['controller']=='Coupons' || $this->params['controller']=='coupons'){
     
       ?>
<div class="lft-side">
    <h2><?php
		
		if($this->Session->read('storeName')){
		   echo $this->Session->read('storeName');
		}else{
		     echo "Food Ordering";
		}
		?></h2>


    <div class="side-menu demo">
        <ul>
            <li><a href="javascript:void(0);">Menus</a></li>
            <li><a href="javascript:void(0);">Stores</a></li>
            <li><a href="javascript:void(0);">Photogallery</a></li>
            <li><a href="javascript:void(0);">About us</a></li>
        </ul>
        <h5>Call us @111110</h5>
    </div>
</div>
<?php }else{?>


<!-- left side start here -->
<div class="lft-side">
        	<?php //echo $this->Html->link('Back to home',array('contoller'=>'users','action'=>'customerDashboard'));?>
    <div class="side-menu">
	      <?php
	     //print_r($categoryList);die;
	     //$categoryList="";
	      if(isset($categoryList) && $categoryList){?>
        <div id="accordian">

            <ul>
		       <?php
					  $list=array();
					  foreach($categoryList as $list){ // Loop for category
					  if($list['Item']){
					  if($list['Category']['is_meal']==1){
					  
					  $starTime=strtotime($list['Category']['start_time']);
					  $endTime=strtotime($list['Category']['end_time']);
					  $currentTime=strtotime(date("H:i"));
					  if($currentTime >= $starTime && $currentTime <=$endTime){
					  
					  }else{
					   continue; 
					  }
					  
					  }
					  }else{
					    continue;	 
					  }
					  
					  $id_name="category_id".$list['Category']['id'];
					  $store_id=$decrypt_storeId;
					  $category_id=$list['Category']['id'];
			    ?>
                <li class="">
                    <h3 class='<?php echo "category_link parent_id".$category_id;?>' rel='<?php echo $store_id;?>' id='<?php echo $id_name;?>' category_id='<?php echo $list['Category']['id'];?>' size-type='<?php echo $list['Category']['is_sizeonly'];?>'><?php echo $list['Category']['name'];?><span></span> </h3><ul>
		    <?php
			    //echo "<pre>"; print_r($list['Item']);die;
			    foreach ($list['Item'] as $listItem){ // Loop for Item 
                                   
				   $current_date=strtotime(date('Y-m-d'));
				   $id_name="item_id".$listItem['id'];
				   $store_id=$decrypt_storeId;
				   $category_id=$listItem['category_id'];
				   $item_id=$listItem['id'];
				   $starDate=strtotime($listItem['start_date']);
				   $endDate=strtotime($listItem['end_date']);
				   if($listItem['is_seasonal_item']==1){
					  if($current_date >= $starDate && $current_date <=$endDate){?>
                        <li class='itemName'><?php echo $this->Html->link($listItem['name'],'javascript:void(0)',array('class'=>'item_link parent_category_id'.$category_id,'rel'=>$item_id,'id'=>$id_name,'store_id'=>$store_id,'item_parent_id'=>$category_id,'size-type'=>$list['Category']['is_sizeonly']));?></li>

				   <?php }else{
					  
				   }?>   
                           <?php }else{?>

                        <li class='itemName'><?php echo $this->Html->link($listItem['name'],'javascript:void(0)',array('class'=>'item_link parent_category_id'.$category_id,'rel'=>$item_id,'id'=>$id_name,'store_id'=>$store_id,'item_parent_id'=>$category_id,'size-type'=>$list['Category']['is_sizeonly']));?></li>


			  <?php  }}?>

                    </ul>
                </li>
                <!-- we will keep this LI open by default -->

                       <?php }?>

            </ul>

        </div>
		<?php //}?>
        <h5>Call us @111110</h5>
    </div>
</div><!-- /left side end -->








<?php }}?>

<script>

    $(document).ready(function () {

        $("#accordian h3").click(function () {
            $("#accordian ul ul").slideUp();
            if (!$(this).next().is(":visible"))
            {
                $(this).next().slideDown();
            }
        });

        $('div.demo ul li').click(function () {
            alert("Static Page InProgress");
            return false;


        });

        $('.item_link').click(function () {
            var item_id = $(this).attr('rel');
            var categoryId = $(this).attr('item_parent_id');
            var storeId = $(this).attr('store_id');
            var sizeType = $(this).attr('size-type');
            $(".loader").css('display', 'block');
            $.ajax({
                type: 'post',
                url: '/Products/fetchCategoryInfo',
                data: {categoryId: categoryId, storeId: storeId},
                success: function (result) {
                    $('.float-left').html(result);
                    $.ajax({
                        url: "/Products/fetchProduct",
                        type: "Post",
                        data: {item_id: item_id, categoryId: categoryId, storeId: storeId, sizeType: sizeType},
                        success: function (result) {
                            if (result) {
                                setTimeout(loader(), 8000);
                                $('.float-left').html(result);
                            }
                        }
                    });
                }
            });
        });

        $('.category_link ').on('click', function () {

            var categoryId = $(this).attr('category_id');
            var storeId = $(this).attr('rel');

            $(".loader").css('display', 'block');

            $.ajax({
                url: "/Products/fetchCategoryInfo",
                type: "Post",
                data: {categoryId: categoryId, storeId: storeId},
                success: function (result) {
                    if (result) {


                        setTimeout(loader(), 8000);
                        //$(".loader").css('display','none');

                        // $('.row-divide').css('dispaly','none');
                        $('.float-left').html(result);
                    }

                }
            });



        });

        function loader() {
            $(".loader").css('display', 'none');
        }
    });
</script>