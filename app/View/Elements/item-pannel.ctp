
<?php if (!empty($_SESSION['FetchProductData']['itemId'])) { ?>
    <script>
        $(document).ready(function () {
            var item_id = '<?php echo ($_SESSION['FetchProductData']['itemId']) ? $_SESSION['FetchProductData']['itemId'] : 0; ?>';
            var categoryId = '<?php echo ($_SESSION['FetchProductData']['categoryId']) ? $_SESSION['FetchProductData']['categoryId'] : 0; ?>';
            var storeId = '<?php echo ($_SESSION['FetchProductData']['storeId']) ? $_SESSION['FetchProductData']['storeId'] : 0; ?>';
            var sizeType = '<?php echo ($_SESSION['FetchProductData']['sizeType']) ? $_SESSION['FetchProductData']['sizeType'] : 0; ?>';
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
                            //console.log(document.getElementById('selectOrderTypes'));
                            //document.getElementById('selectOrderTypes').innerHTML =result;
                            $('.float-left').html(result);
                            if (window.screen.width < 700) {
                                $(window).scrollTop($('#cartstart').offset().top - 30);
                            } else {
                                $(window).scrollTop($('#anchorName').offset().top);
                            }
                            var middlebox = $("#selectOrderTypes").height();
                            var firstbox = $("#anchorName").height();
                            var lastbox = $("#isolated").height();
                            var headerHeight = $('header').outerHeight();
                            var largestheight = Math.max(firstbox, middlebox, lastbox);
                            var productImage = $('#ItemProductpicid').val();
                            if (window.screen.width < 700) {
                                if (productImage == 1) {
                                    var picHeight = 130;
                                } else {
                                    var picHeight = 35;
                                }
                            } else {
                                if (productImage == 1) {
                                    var picHeight = 250;
                                } else {
                                    var picHeight = 35;
                                }
                            }
                            var totalHeight = (headerHeight + largestheight + picHeight);
                            $('.content').css('height', 30 + (totalHeight) + 'px');
                        }
                    }
                }
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
<?php } else { ?>
    <h2><span>Create Your Order</span></h2>
    <div>
        <h3>3 simple steps to choose your favorite food... </h3>
        <ul class="instructions clearfix">
            <li><i class="fa fa-pencil-square-o"></i> Choose related category and food item from left menu..</li>
            <li><i class="fa fa-pencil-square-o"></i> When you select food item of preference, you can add or remove ingredients and order exactly what you want..</li>
            <li><i class="fa fa-pencil-square-o"></i> Click on Add button to add your favorite food item in your order cart..</li>
        </ul>
    </div>
<?php } ?>

<script>
//$(document).ready(function() {
//    var arr_instance = $('#selectOrderTypes').simpleScrollFollow({
//            min_width: 992,
//            instance: true,
//            limit_elem: $('.content')
//    });
//    $(arr_instance).each(function() {
//            var self = this;
//            self.setEnabled(true);
//    });
//});
</script>
