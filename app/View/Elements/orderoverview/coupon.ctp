<?php if (empty($_SESSION['Coupon'])) { ?>
    <tr class="seperator-box">
        <td colspan="2">Coupon Code</td>
        <td width="300">
            <input class="inbox cp-code error-coupon" type="text" name="coupon code" placeholder="" />
            <?php
            if (!empty($coupon_data)) {
                if ($coupon_data == 1) {
                    echo '<span style="float:left;color:red;">Please enter valid coupon code</span>';
                } elseif ($coupon_data == 2) {
                    echo '<span style="float:left;color:red;">Coupon has been expired.</span>';
                }
            }
            ?>
        </td>
    </tr>
    <script type="text/javascript">
        $(document).on("blur", '.cp-code', function () {
            var coupon_code = $(this).val();
            $.ajax({
                type: 'post',
                url: '/products/addCouponToCart',
                async: false,
                data: {'coupon_code': coupon_code, 'page': 'order_overview'},
                success: function (result) {
                    if (result) {
                        var obj = jQuery.parseJSON(result);
                        if (obj.status == 'Success') {
                            window.location = window.location;
                        } else if (obj.status == 'Error') {
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(obj.msg);
                            return false;
                        }
                    }
                }
            });
        });
    </script>
    <?php
}?>