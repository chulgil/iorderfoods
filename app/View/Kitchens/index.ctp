<style>
    /*#page-wrapper .container { min-width:158px; }
    .container { min-height:90% !important; }*/

    .ul-head-more-order {
        float: left;
        width: 100%;
    }

    .ul-more-order{width: 100%; }
    .li-more-order{float: left;word-wrap: break-word;}
    .kitchenDashboard{padding:5px 0 5px 0;}
    .container{
        overflow:auto;
    }

</style>

<div class="row" >
    <div class="col-xs-12">
        <div class="updateOrdersDataWrap">
            <div class="kitchenDashboard">
                <span style="font-weight:bold;">Kitchen Dashboard</span>
                <?php echo $this->Html->link('Back', array('controller' => 'kitchens', 'action' => 'listView'), array('class' => 'btn btn-default')); ?>
                <?php echo $this->Html->link('List View', array('controller' => 'kitchens', 'action' => 'listView'), array('style' => 'float:right;margin-right:50px;')); ?>
            </div>
            <div class="updateOrdersData">
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        var screenWidth = Math.floor(screen.width);
        var count = 0;
        if (screenWidth > 1280) {
            count = Math.floor(screen.width / 270);
        } else {
            count = Math.floor(screen.width / 230);
        }



        $.ajax({
            type: 'post',
            url: '/kitchens/getOrderData',
            data: {limit: count},
            success: function (result) {
                if (result) {
                    $('.updateOrdersData').html(result);
                    containerHeight(count);
                }
            }
        });
        setInterval(function () {
            getOrderData(count);
        }, 30000);

        function containerHeight(count) {
            if ($(".container").length > 0) {
                var newContainerHeight = $(window).height() - $('.kitchenDashboard').height() - 30;

                containerWidth = 0;
                var i = 1;
                $(".container").each(function () {

                    //var containerHeight=$(this).height();
                    var headheight = $(this).find(".head").height();                                                        //alert(containerHeight+"##"+newContainerHeight);
                    //if((containerHeight) > newContainerHeight){
                    var liHeight = 0;
                    var licount = 0;
                    $(this).find(".newitem").each(function () {
                        liHeight = liHeight + $(this).height();
                        licount++;
                    });
                    var paddingheight = (licount * 6);
                    var totalliHeight = headheight + liHeight + paddingheight;
                    var widthCount = 1;
                    if (totalliHeight > newContainerHeight) {
                        widthCount = Math.ceil(totalliHeight / newContainerHeight) + 1;
                        $(this).css('width', ($(this).width()) * widthCount);

                        $(this).find('.orderItemDetails .noeffect').addClass('ul-more-order');
                        $(this).find('.orderItemDetails .newitem').addClass('li-more-order');



                    }
                    var col = (100 / widthCount);
                    $(this).find(".li-more-order").css("width", col + "%");
                    //}
                    containerWidth = containerWidth + ($(this).width());
                    $(this).height(newContainerHeight);
                });
                var windowWidth = $(window).width();
                containerWidth = containerWidth + 60;
                if (containerWidth > windowWidth) {
                    var containercount = Math.floor(containerWidth / 230);
                    count = count - 1;
                    getOrderData(count);
                }
//			else if (containerWidth<windowWidth) {
//                            containerWidth=adjustwidth(containerWidth,windowWidth);
//                            var containercount = Math.floor(containerWidth/230);
//                            if(count<6){
//				count=count+1;
//				getOrderData(count);
//                            }
//			}
            }

        }

//        function adjustwidth(containerWidth,windowWidth){
//            if (containerWidth<windowWidth) {
//                 containerWidth=containerWidth+250;
//                 adjustwidth(containerWidth,windowWidth);
//            }
//            return containerWidth;
//        }


        function getOrderData(count) {
            $.ajax({
                type: 'post',
                url: '/kitchens/getOrderData',
                data: {limit: count},
                success: function (result) {
                    if (result) {
                        $('.updateOrdersData').html(result);
                        containerHeight(count);
                    }
                }
            });
        }


    });


    $(window).resize(function () {
        var windowHeight = $(window).innerHeight();
        $('.container').css('height', windowHeight - 100);
    });

</script>


