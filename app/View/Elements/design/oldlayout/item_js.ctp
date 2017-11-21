<script>
    $(document).ready(function () {

        $(document).on("click", "#continue", function () {
        }

        var selectOrderTypes = $('#selectOrderTypes').simpleScrollFollow({
        min_width: 992,
                instance: true,
                limit_elem: $('.content')
    });
    var selectOrderTypesSelf = '';
    $(selectOrderTypes).each(function () {
        selectOrderTypesSelf = this;
        selectOrderTypesSelf.setEnabled(true);
    });


    var isolated = $('#isolated').simpleScrollFollow({
        min_width: 992,
        instance: true,
        limit_elem: $('.content')
    });


    var isolatedSelf = '';
    $(isolated).each(function () {
        isolatedSelf = this;
    });

    $('#toggle_scroll').click(function () {
        if ($(this).text() == 'click to disable scroll') {
            isolatedSelf.setEnabled(false);
            selectOrderTypesSelf.setEnabled(false);
            $(this).text('click to enable scroll');
        } else {
            selectOrderTypesSelf.setEnabled(true);
            isolatedSelf.setEnabled(true);
            $(this).text('click to disable scroll');
        }
    });


    /* Get Cart Count */
    function cartcount() {
        $.ajax({
            type: 'post',
            url: '/Products/getcartCount',
            data: {},
            success: function (data1) {
                if (data1) {
                    $('.numberCircle').html(data1);
                }
            }
        });
    }
    $(document).ready(function () {
        $('.cls_no_deliverable').css('display', 'none');
        $('.makeorder').click(function () {
            var total = "<?php echo $gross_amount; ?>";
<?php
$storeInfo = $this->Common->getStoreDetail($this->Session->read('store_id'));
if ($order_type == 2) {
    if ($storeInfo['Store']['is_pick_beftax']) {
        $totalwithoutTax = $gross_amount - $totaltaxPrice;
        ?>
                    total = "<?php echo $totalwithoutTax; ?>";
        <?php
    }
    $minimum_price = $storeInfo['Store']['minimum_takeaway_price'];
} else {
    if ($storeInfo['Store']['is_delivery_beftax']) {
        $totalwithoutTax = $gross_amount - $totaltaxPrice;
        ?>
                    total = "<?php echo $totalwithoutTax; ?>";
        <?php
    }
    $minimum_price = $storeInfo['Store']['minimum_order_price'];
}
?>
            var min_price =<?php echo $minimum_price; ?>;
            if ((total) >= (min_price)) {

            } else {
                //var message = "Order total should be equal or more than $<?php echo number_format($minimum_price, 2); ?> (minimum order price)";
                //$("#errorPop").modal('show');
                //$("#errorPopMsg").html(message);
//                var message = "Order total should be equal or more than $<?php echo number_format($minimum_price, 2); ?> (minimum order price)";
//                $('.cls_no_deliverable').html(message);
//                $('.cls_no_deliverable').css('display', 'block');
//                $(".cls_no_deliverable").fadeOut(6000);
                //return false;
            }

        });
        $('.remove').on('click', function () {
            $(".loader1").css('display', 'block');
            var index_id = $(this).attr('id'); //index of session array
            $.ajax({
                type: 'post',
                url: '/Products/removeItem',
                data: {'index_id': index_id},
                success: function (result) {
                    $(".loader1").css('display', 'none');
                    if (result) {
                        $('.online-order').html(result);
                    }
                }
            });
        });

        $('#mobile_continuemenu').on('click', function () {
            $(window).scrollTop($('#anchorName').offset().top);
        });



        $('.singleItemRemove').on('click', function () {
            $('#loading').show();
            var cart_index_id = $(this).parent().attr('index_id'); //index of session array
            var offer_index_id = $(this).attr('value'); //index of session arrayd
            $.ajax({
                type: 'post',
                url: '/Products/removeOfferItem',
                data: {'cart_index_id': cart_index_id, 'offer_index_id': offer_index_id},
                success: function (result) {
                    $(".loader1").css('display', 'none');

                    if (result) {

                        $('.online-order').html(result);
                    }


                }, complete: function (data1) {
                    setTimeout(function () {
                        $('#loading').hide();
                    }, 500);
                }
            });


        });
        $('#inprogress').click(function () {
            var coupon_code = $('.coupon-code').val();
            $.ajax({
                type: 'post',
                url: '/Products/fetchCoupon',
                data: {'coupon_code': coupon_code},
                success: function (result) {
                    if (result) {
                        $('.float-right').html(result);
                    }
                }
            });


        });

        $('.quantity').on('change', function () {
            $('#loading').show();
            var index_id = $(this).parent().attr('Key'); //index of session array
            var value = this.value;
            if (value <= 0) {
                return false;
            }
            $.ajax({
                type: 'post',
                url: '/Products/addQuantity',
                data: {'index_id': index_id, 'value': value},
                success: function (result) {
                    if (result) {
                        $('.online-order').html(result);
                    }
                    cartcount();
                }, complete: function (data1) {
                    setTimeout(function () {
                        $('#loading').hide();
                    }, 500);
                }
            });


        });
    });

</script>


<style>
    a:hover {
        text-decoration: none;
    }
    [data-tooltip] {
        position: relative;
        z-index: 2;
        cursor: pointer;
    }
    [data-tooltip]:before,
    [data-tooltip]:after {
        visibility: hidden;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
        filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=0)";
        opacity: 0;
        pointer-events: none;
    }
    [data-tooltip]:before {
        position: absolute;
        bottom: 150%;
        left: 50%;
        margin-bottom: 5px;
        margin-left: -80px;
        padding: 7px;
        width: 160px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        background-color: #000;
        background-color: hsla(0, 0%, 20%, 0.9);
        color: #fff;
        content: attr(data-tooltip);

        white-space: pre-line;
        text-align: left;
        font-size: 14px;
        line-height: 1.2;
    }
    [data-tooltip]:after {
        position: absolute;
        bottom: 150%;
        left: 50%;
        margin-left: -5px;
        width: 0;
        border-top: 5px solid #000;
        border-top: 5px solid hsla(0, 0%, 20%, 0.9);
        border-right: 5px solid transparent;
        border-left: 5px solid transparent;
        content: " ";
        font-size: 0;
        line-height: 0;

    }
    [data-tooltip]:hover:before,
    [data-tooltip]:hover:after {
        visibility: visible;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
        filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=100)";
        opacity: 1;
    }

    @media (max-width:55em) {
        #desktop_continue{
            display:none;
        }

        #mobile_continue{
            display:block;
        }
    }

    @media (min-width:55em) {
        #desktop_continue{
            display:block;
        }

        #mobile_continuemenu{
            display:none;
        }

        #mobile_continue{
            display:none;
        }
    }
</style>