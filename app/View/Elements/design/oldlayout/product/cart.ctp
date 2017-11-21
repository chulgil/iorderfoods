
<!--<div class="col-3 mid-col form-layout float-left">-->
<div class="col-3 mid-col" id="offerItemsanchor">
    <div id="selectOrderTypes" class="isolated form-layout form-layout-fixed scroll-div float-left cartCtp">
        <?php
        if (!empty($getOffer)) {
            echo $this->Form->input('offer.hiddenid', array('type' => 'hidden', 'value' => 1));
            echo $this->element('item-offer');
            ?>                 
            <?php
        } else {
            if (isset($Currentitem)) {

                if (!empty($_SESSION['FetchProductData'])) {
                    ?>
                    <script>
                        $(document).ready(function () {
                            var item_id = '<?php echo $_SESSION['FetchProductData']['itemId']; ?>';
                            var categoryId = '<?php echo $_SESSION['FetchProductData']['categoryId']; ?>';
                            var storeId = '<?php echo $_SESSION['FetchProductData']['storeId']; ?>';
                            var sizeType = '<?php echo $_SESSION['FetchProductData']['sizeType']; ?>';
                            var zeroprice = '<?php echo $Currentitem; ?>';
                            $.ajax({
                                url: "/Products/fetchProduct",
                                type: "Post",
                                data: {item_id: item_id, categoryId: categoryId, storeId: storeId, sizeType: sizeType, itemzero: zeroprice},
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
                    <?php
                }
            } else {
                echo $this->element('item-pannel');
            }
        }
        ?>
    </div>
</div>

<div class="col-3 last-col" id="cartstart"> <div id="isolated"  class="isolated form-layout form-layout-fixed scroll-div float-right"> <?php echo $this->element('cart-element'); ?> </div> </div>

<?php if (!empty($getOffer)) { ?>

    <script>
        $(document).ready(function () {
            if (window.screen.width < 700) {
                $(window).scrollTop($('div#offerItemsanchor').offset().top - 30);
            }
        });
    </script>  

<?php } ?>




