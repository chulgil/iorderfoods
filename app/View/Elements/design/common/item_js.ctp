<?php //echo $this->element('modal/error_popup_old')                                                           ?>
<script>
    function priceCheckAgain(type_id, subDefault, item_id, countSizes, subSizeID, checked, sizeId, preMainId, checkBox) {
        $.ajax({
            url: "/Products/ajaxSubPreferencePrice",
            type: "Post",
            data: {subPreferenceId: type_id, itemId: item_id, sizeId: sizeId, checked: checked, subSizeID: subSizeID, countSizes: countSizes, subDefault: subDefault, preMainId: preMainId, checkBox: checkBox},
            success: function (result) {
                checkJson = IsJsonString(result);
                if (checkJson) {
                    var obj = jQuery.parseJSON(result);
                    if (obj.status == 'Error') {
                        $("#ItemSubpreference" + type_id + "Id").attr('checked', false);
                        //subpre hide and trigger start
                        $("#ItemSubpreference" + type_id + "Size").val(1);
                        $("#ItemSubpreference" + type_id + "Size").parent().parent().hide();
                        $('.subPrice' + type_id).hide();
                        $("#ItemSubpreference" + type_id + "Size").trigger('change');
                        //subpre hide and trigger end
                        $("#errorPop").modal('show');
                        $("#errorPopMsg").html(obj.msg);
                        return false;
                    } else if (obj.status == 'Success') {
                        itemzero(obj.price);
                        result = '$' + parseFloat(obj.price).toFixed(2);
                        $('.product-price').html(result);
                        if (checked) {
                            $("#ItemSubpreference" + type_id + "Size").parent().parent().show();
                            $('.subPrice' + type_id).show();
                        } else {
                            //subpre hide and trigger start
                            $("#ItemSubpreference" + type_id + "Size").val(1);
                            $("#ItemSubpreference" + type_id + "Size").parent().parent().hide();
                            $('.subPrice' + type_id).hide();
                            $("#ItemSubpreference" + type_id + "Size").trigger('change');
                            //subpre hide and trigger end
                        }
                    }
                }
            }
        });
    }
    function priceCheckAgainAddons(size_id, type, topping_id, item_id, checked, itemSizeId, countSizes, subDefault, addonMainId, selected) {
        $.ajax({
            url: "/Products/fetchToppingPrice",
            type: "Post",
            data: {sizeId: size_id, type: type, toppingId: topping_id, itemId: item_id, checked: checked, itemSizeId: itemSizeId, countSizes: countSizes, subDefault: subDefault, addonMainId: addonMainId, checkBox: selected},
            success: function (result) {
                checkJson = IsJsonString(result);
                if (checkJson) {
                    var obj = jQuery.parseJSON(result);
                    if (obj.status == 'Error') {
                        $("#ItemToppings" + topping_id + "Id").attr('checked', false);
                        //subaddon hide and trigger start
                        $("#ItemToppings" + topping_id + "Size").val(1);
                        $("#ItemToppings" + topping_id + "Size").parent().parent().hide();
                        $('.topPrice' + topping_id).show();
                        $("#ItemToppings" + topping_id + "Size").trigger('change');
                        //subaddon hide and trigger end
                        $("#errorPop").modal('show');
                        $("#errorPopMsg").html(obj.msg);
                        return false;
                    } else if (obj.status == 'Success') {
                        itemzero(obj.price);
                        result = '$' + parseFloat(obj.price).toFixed(2);
                        $('.product-price').html(result);
                        if (checked) {
                            $("#ItemToppings" + topping_id + "Size").parent().parent().show();
                            $('.topPrice' + topping_id).show();
                        } else {
                            //subaddon hide and trigger start
                            $("#ItemToppings" + topping_id + "Size").val(1);
                            $("#ItemToppings" + topping_id + "Size").parent().parent().hide();
                            $('.topPrice' + topping_id).show();
                            $("#ItemToppings" + topping_id + "Size").trigger('change');
                            //subaddon hide and trigger end
                        }
                    }
                }
            }
        });
    }
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


    function itemQuantity(currElement) {
        var index_id = currElement.attr('Key'); //index of session array
        //alert(currElement.attr('Key'));return false;
        var value = currElement.val();
        $.ajax({
            type: 'post',
            url: '/Products/addQuantity',
            data: {'index_id': index_id, 'value': value},
            async: false,
            success: function (data1) {
                var check = data1.charAt(0);
                data1 = data1.substr(1);
                if (check == '1') {
                    $('#item-modal').html(data1);
                    $('#item-modal').modal('show');
                } else
                {
                    $('#item-modal').html('');
                    $('#item-modal').css('display', 'none');
                    $('#item-modal').modal('hide');
                    $('#ordercart').html(data1);
                }

            }
        });
    }

    $(document).ready(function () {
        // number spinner //

        (function ($) {
            $(document).on('click', '.spinner-popup .btn:first-of-type', function () {
                var currElement = $(this).next();
                var maxLimit = $(this).attr('key');
                var qval = parseInt(currElement.val(), 10) + 1;
                if (qval <= 0 || qval > maxLimit) {
                    return false;
                }
                currElement.val(qval);
                //itemQuantity(currElement);
                currElement.trigger("change");
            });

            $(document).on('click', '.spinner-popup .btn:last-of-type', function () {
                var currElement = $(this).prev();
                var qval = parseInt(currElement.val(), 10) - 1;
                if (qval <= 0) {
                    return false;
                }
                currElement.val(qval);
                //itemQuantity(currElement);
                currElement.trigger("change");
            });

        })(jQuery);
        
        $(document).on('change blur keyup', ".quantity", function () {
            var index_id = $(this).parent().attr('Key'); //index of session array
            var value = Number(this.value);
            if (value <= 0) {
                return false;
            }
            
            if(!isInt(value)) {
                return false;
            }
            $.ajax({
                type: 'post',
                dataType: 'text',
                url: '/Products/addQuantity',
                data: {'index_id': index_id, 'value': value},
                async: false,
                success: function (data1) {
                    var check = data1.charAt(0);
                    data1 = data1.substr(1);
                    if (check == '1') {
                        $('#item-modal').html(data1);
                        $('#item-modal').modal('show');
                    } else
                    {
                        $('#item-modal').html('');
                        $('#item-modal').css('display', 'none');
                        $('#item-modal').modal('hide');
                        $('#ordercart').html(data1);
                    }

                }
            });
        });





        $(document).on('click', '.remove', function () {
            var index_id = $(this).attr('id'); //index of session array
            $.ajax({
                type: 'post',
                url: '/Products/removeItem',
                data: {'index_id': index_id},
                success: function (result) {
                    var check = result.charAt(0);
                    result = result.substr(1);
                    if (result) {
                        $('#ordercart').html(result);
                    }
                }
            });
        });


        $(document).on("click", '#mobile_continuemenu', function () {
            $(window).scrollTop($('#anchorName').offset().top);
        });


        $(document).on("click", '.singleItemRemove', function () {
            var cart_index_id = $(this).parent().attr('index_id'); //index of session array
            var offer_index_id = $(this).attr('value'); //index of session arrayd
            $.ajax({
                type: 'post',
                url: '/Products/removeOfferItem',
                data: {'cart_index_id': cart_index_id, 'offer_index_id': offer_index_id},
                success: function (result) {
                    if (result) {
                        $('#ordercart').html(result);
                    }
                }
            });
        });


        $(document).on("blur", ".coupon-code, .cp-code", function () {
            var coupon_code = $(this).val();
            $.ajax({
                type: 'post',
                url: '/Products/fetchCoupon',
                data: {'coupon_code': coupon_code},
                success: function (result) {
                    if (result) {
                        $('#ordercart').html(result);
                    }
                }
            });
        });
        $(document).on('change blur keyup', '.subPreferenceSize', function () 
        {
            var minSubPreferenceSize = Number($(this).attr('min'));
            var maxSubPreferenceSize = Number($(this).attr('max'));
            var subPreferenceSizeVal = Number($(this).val());
            if(!isInt(subPreferenceSizeVal))
            {
                return false;
            }
            if(subPreferenceSizeVal >= minSubPreferenceSize && subPreferenceSizeVal <= maxSubPreferenceSize)
            {
                var subDefault = $("#subdefault").val();
                var size_id = $(this).val();
                var subPreferenceId = $(this).attr('rel');
                var subSizeID = size_id = $("#ItemSubpreference" + subPreferenceId + "Size").val();
                var checked = $("#ItemSubpreference" + subPreferenceId + "Id").prop("checked");
                if (checked == true) {
                    checked = 1;
                } else {
                    checked = 0;
                    $("#ItemSubpreference" + subPreferenceId + "Size").parent().parent().hide();
                    $('.subPrice' + subPreferenceId).hide();
                }
                //var type = $('.type' + subPreferenceId).val();
                var countSizes = $("#countSizes").val();
                var item_id = $('#itemIdpopup').val();
                if ($('.item_price:checked').val()) {
                    var itemSizeId = $('.item_price:checked').val();
                } else {
                    var itemSizeId = 0;
                }
                $.ajax({
                    url: "/Products/ajaxFetchSubPrefrenceSizePrice",
                    type: "Post",
                    data: {sizeId: size_id, subPreferenceId: subPreferenceId, itemId: item_id, checked: checked, itemSizeId: itemSizeId, countSizes: countSizes, subDefault: subDefault},
                    success: function (result) {
                        if (result == '' || result == 0) {
                            $('.subPrice' + subPreferenceId).html('');
                        } else {
                            result = '$' + parseFloat(result).toFixed(2);
                            $('.subPrice' + subPreferenceId).html(result);
                        }
                        if (checked == 1) {
                            var preMainId = $("#ItemSubpreference" + subPreferenceId + "TypeId").val();
                            var chClass = '.typeId_' + preMainId;
                            var selected = [];
                            $(chClass + ':checkbox:checked').each(function () {
                                selected.push({
                                    id: $(this).attr('value'),
                                    size: $('#ItemSubpreference' + $(this).attr('value') + 'Size').val()
                                });

                            });
                            $.ajax({
                                url: "/Products/ajaxSubPreferencePrice",
                                type: "Post",
                                data: {subPreferenceId: subPreferenceId, itemId: item_id, sizeId: itemSizeId, checked: checked, subSizeID: size_id, countSizes: countSizes, subDefault: subDefault, preMainId: preMainId, checkBox: selected},
                                success: function (result) {
                                    checkJson = IsJsonString(result);
                                    if (checkJson) {
                                        var obj = jQuery.parseJSON(result);
                                        if (obj.status == 'Error') {
                                            $("#ItemSubpreference" + subPreferenceId + "Id").attr('checked', false);
                                            $("#errorPop").modal('show');
                                            $("#errorPopMsg").html(obj.msg);
                                            priceCheckAgain(subPreferenceId, subDefault, item_id, countSizes, size_id, 0, itemSizeId, 0, 0);
                                            $("#ItemSubpreference" + subPreferenceId + "Size").parent().parent().hide();
                                            $('.subPrice' + subPreferenceId).hide();
                                            return false;
                                        } else if (obj.status == 'Success') {
                                            itemzero(obj.price);
                                            result = '$' + parseFloat(obj.price).toFixed(2);
                                            $('.product-price').html(result);
                                        }
                                    }
                                }
                            });
                        }
                    }
                });
            }
        });

        function isInt(n){
            return Number(n) === n && n % 1 === 0;
        }
        
        $(document).on('change blur keyup', ".toppingSize", function () {
            var minTopping = Number($(this).attr('min'));
            var maxTopping = Number($(this).attr('max'));
            var toppingVal = Number($(this).val());
            if(!isInt(toppingVal))
            {
                return false;
            }
            if(toppingVal >= minTopping && toppingVal <= maxTopping)
            {
                var subDefault = $("#subdefault").val();
                var countSizes = $("#countSizes").val();
                var size_id = $(this).val();
                var topping_id = $(this).attr('rel');
                var checked = $("#ItemToppings" + topping_id + "Id").prop("checked");
                if (checked == true) {
                    checked = 1;
                } else {
                    checked = 0;
                    $('#ItemToppings' + topping_id + 'Size').parent().parent().hide();
                    $('.topPrice' + topping_id).hide();
                }
                var type = $('.type' + topping_id).val();
                var item_id = $('#itemIdpopup').val();
                if ($('.item_price:checked').val()) {
                    var itemSizeId = $('.item_price:checked').val();
                } else {
                    var itemSizeId = 0;
                }
                $.ajax({
                    url: "/Products/fetchToppingSizePrice",
                    type: "Post",
                    data: {sizeId: size_id, type: type, toppingId: topping_id, itemId: item_id, checked: checked, itemSizeId: itemSizeId, countSizes: countSizes, subDefault: subDefault},
                    success: function (result) {
                        if (result == '' || result == 0) {
                            $('.topPrice' + topping_id).html('');
                        } else {
                            result = '$' + parseFloat(result).toFixed(2);
                            $('.topPrice' + topping_id).html(result);
                        }
                        if (checked == 1) {
                            var addonMainId = $('#ItemToppings' + topping_id + 'AddonId').val();
                            var selected = [];
                            $('.addonId_' + addonMainId + ':checkbox:checked').each(function () {
                                selected.push({
                                    id: $(this).attr('value'),
                                    size: $('#ItemToppings' + $(this).attr('value') + 'Size').val()
                                });

                            });
                            $.ajax({
                                url: "/Products/fetchToppingPrice",
                                type: "Post",
                                data: {sizeId: size_id, type: type, toppingId: topping_id, itemId: item_id, checked: checked, itemSizeId: itemSizeId, countSizes: countSizes, subDefault: subDefault, addonMainId: addonMainId, checkBox: selected},
                                success: function (result) {
                                    checkJson = IsJsonString(result);
                                    if (checkJson) {
                                        var obj = jQuery.parseJSON(result);
                                        if (obj.status == 'Error') {
                                            $("#ItemToppings" + topping_id + "Id").attr('checked', false);
                                            $("#errorPop").modal('show');
                                            $("#errorPopMsg").html(obj.msg);
                                            priceCheckAgainAddons(size_id, type, topping_id, item_id, 0, itemSizeId, countSizes, subDefault, 0, 0)
                                            $("#ItemToppings" + topping_id + "Size").parent().parent().hide();
                                            $('.topPrice' + topping_id).hide();
                                            return false;
                                        } else if (obj.status == 'Success') {
                                            itemzero(obj.price);
                                            result = '$' + parseFloat(obj.price).toFixed(2);
                                            $('.product-price').html(result);
                                        }
                                    }
                                }
                            });
                        }
                    }
                });
            }
        });


        $(document).on('click', '.toppings', function () {
            var subDefault = $("#subdefault").val();
            var countSizes = $("#countSizes").val();
            var topping_id = $(this).val();
            var item_id = $('#itemIdpopup').val();
            if ($(this).prop("checked") == true) {
                var checked = 1;
            } else {
                var checked = 0;
            }
            var size_id = $('.toppingSize' + topping_id).val();
            var type = $('.type' + topping_id).val();
            var addonMainId = $('#ItemToppings' + topping_id + 'AddonId').val();
            if ($('.item_price:checked').val()) {
                var itemSizeId = $('.item_price:checked').val();
            } else {
                var itemSizeId = 0;
            }
            var selected = [];
            $('.addonId_' + addonMainId + ':checkbox:checked').each(function () {
                selected.push({
                    id: $(this).attr('value'),
                    size: $('#ItemToppings' + $(this).attr('value') + 'Size').val()
                });
            });
            priceCheckAgainAddons(size_id, type, topping_id, item_id, checked, itemSizeId, countSizes, subDefault, addonMainId, selected);
        });


        $(document).on('click', '.item_price', function () {
            var size_id = $(this).val();
            var subdefault = $("#subdefault").val();
            var item_id = $('#itemIdpopup').val();
            $.ajax({
                url: "/Products/sizePrice",
                type: "Post",
                data: {sizeId: size_id, itemId: item_id},
                success: function (result) {
                    itemzero(result);
                    result = '$' + parseFloat(result).toFixed(2);
                    $('.product-price').html(result);
                    $(".prefernceAndAddOns").find(':input').each(function () {
                        this.checked = false;
                    });
                }
            });

            var categoryId = $("#ItemCategoryID").val();
            if ($("#countSizes").val() > 0) {
                if (subdefault == 0) {
                    fetchProductSize();
                }
            }
        });


        $(document).on('click', '.item_type', function () {
            var type_id = $(this).val();
            var subDefault = $("#subdefault").val();
            var item_id = $('#itemIdpopup').val();
            var subPreferenceId = $(this).attr('rel');
            var countSizes = $("#countSizes").val();
            var checked = $("#ItemSubpreference" + type_id + "Id").prop("checked");
            var subSizeID = $("#ItemSubpreference" + type_id + "Size").val();
            var preMainId = $("#ItemSubpreference" + type_id + "TypeId").val();
            var chClass = '.typeId_' + preMainId;
            var selected = [];
            $(chClass + ':checkbox:checked').each(function () {
                selected.push({
                    id: $(this).attr('value'),
                    size: $('#ItemSubpreference' + $(this).attr('value') + 'Size').val()
                });

            });
            if (checked == true) {
                var checked = 1;
            } else {
                var checked = 0;
            }
            if ($('.item_price:checked').val()) {
                var sizeId = $('.item_price:checked').val();
            } else {
                var sizeId = 0;
            }
            priceCheckAgain(type_id, subDefault, item_id, countSizes, subSizeID, checked, sizeId, preMainId, selected);
        });



        $(document).on('submit', "#ItemFetchProductForm", function (e) {
            e.preventDefault();

            //$('input[type="submit"]').attr('disabled', 'disabled');
            var Data = $(this).serialize();
            var checkSetting = true;
            $.ajax({
                type: 'post',
                url: '/products/checkMandatoryPrefAddons',
                data: Data,
                async: false,
                success: function (mData) {
                    checkJson = IsJsonString(mData);
                    if (checkJson) {
                        var obj = jQuery.parseJSON(mData);
                        if (obj.status == 'Error') {
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(obj.msg);
                            checkSetting = false;
                            return false;
                        }
                    }
                }
            });
            if (checkSetting) {
                $.ajax({
                    type: 'post',
                    url: '/Products/checkCombination',
                    data: Data,
                    async: false,
                    success: function (result) {
                        $('input[type="submit"]').removeAttr('disabled');
                        if (result == 0) {
                            $.ajax({
                                type: 'post',
                                url: '/Products/cart',
                                data: Data,
                                success: function (data1) {
                                    checkJson = IsJsonString(data1);
                                    if (checkJson) {
                                        var obj = jQuery.parseJSON(data1);
                                        if (obj.status == 'Error') {
                                            $("#errorPop").modal('show');
                                            $("#errorPopMsg").html(obj.msg);
                                            /*$('#' + obj.type).html('<div class="message message-success alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + obj.msg + '</div>');
                                             $("#item-modal").animate({
                                             scrollTop: $('#' + obj.type).offset().top
                                             }, 500);*/
                                            return false;
                                        }
                                    } else {
                                        if (data1) {
                                            var check = data1.charAt(0);
                                            data1 = data1.substr(1);
                                            if (check == '1') {
                                                $('#item-modal').html(data1);
                                            } else {
                                                $('#item-modal').html('');
                                                $('#item-modal').css('display', 'none');
                                                $('#item-modal').modal('hide');
                                                $('#ordercart').html(data1);
                                            }
                                        }
                                    }
                                    cartcount();
                                }
                            });
                        } else {
                            var obj = jQuery.parseJSON(result);
                            var index_id = obj.index; //index of session array
                            var value = parseInt(obj.quantity) + 1;
                            $.ajax({
                                type: 'post',
                                url: '/Products/addQuantity',
                                data: {'index_id': index_id, 'value': value},
                                success: function (data1) {
                                    var check = data1.charAt(0);
                                    data1 = data1.substr(1);
                                    if (check == '1')
                                    {
                                        $('#item-modal').html(data1);
                                    } else
                                    {
                                        $('#item-modal').html('');
                                        $('#item-modal').css('display', 'none');
                                        $('#item-modal').modal('hide');
                                        $('#ordercart').html(data1);
                                    }
                                }
                            });
                        }
                    }
                });
            }
        });


        $(document).on('click', ".makeorder", function (e) {
            e.preventDefault();
            result = {"status": "Success"};
            if (document.getElementById('test1')) {
                //$('input[type="submit"]').attr('disabled', 'disabled');
                var orderdate = $("#StorePickupDate").val();
                var orderhour = $("#StorePickuphour").val();
                var ordermin = $("#StorePickupmin").val();
                var preOrder = $("input[name='data[pickup][type]']:checked").val();
//            if (preOrder == '' || typeof preOrder == 'undefined') {
//                preOrder = 0;
//            }
                var ordertype = $('input[name="data[Order][type]"]:checked').val();
                if (!ordertype) {
                    $("#errorPop").modal('show');
                    $("#errorPopMsg").html("Please select order type.");
                    return false;
                }
                $.ajax({
                    type: 'post',
                    url: '/orderOverviews/saveOrderTime',
                    data: {'orderdate': orderdate, 'orderhour': orderhour, 'ordermin': ordermin, 'preOrder': preOrder, 'ordertype': ordertype},
                    async: false,
                    success: function (response) {
                        result = jQuery.parseJSON(response);
                        if (result.status == 'Error') {
                            //alert(result.msg);
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(result.msg);
                            return false;
                        }
                    }
                });
            }
            if (result.status == 'Success') {
                $.ajax({
                    type: 'POST',
                    url: '/orderOverviews/checkMendatoryItem',
                    async: false,
                    success: function (response) {
                        result = jQuery.parseJSON(response);
                        if (result.status == 'Error') {
                            //alert(result.msg);
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(result.msg);
                            return false;
                        }
                        $("#CartInfoItemsForm").submit();
                    }
                });
            }
        });
        $('#item-modal').on('hidden.bs.modal', function () {
            $("#item-modal").html("");
        });
    });



    $(document).ready(function () {
        $(".carousel").carousel({
            interval: 4000
        });
        $(document).on("slid", ".carousel", function () {
            var to_slide;
            to_slide = $(".carousel-item.active").attr("data-slide-no");
            $(".myCarousel-target.active").removeClass("active");
            $(".carousel-indicators [data-slide-to=" + to_slide + "]").addClass("active");
        });
        $(document).on("click", ".myCarousel-target", function () {
            $(this).preventDefault();
            $(".carousel").carousel(parseInt($(this).attr("data-slide-to")));
            $(".myCarousel-target.active").removeClass("active");
            $(this).addClass("active");
        });

        // number spinner //

        (function ($) {

            $(document).on('click', '.spinner .btn:first-of-type', function () {
                var currElement = $(this).next();
                var qval = parseInt(currElement.val(), 10) + 1;
                if (qval <= 0) {
                    return false;
                }
                currElement.val(qval);
                itemQuantity(currElement);
            });

            $(document).on('click', '.spinner .btn:last-of-type', function () {
                var currElement = $(this).prev();
                var qval = parseInt(currElement.val(), 10) - 1;
                if (qval <= 0) {
                    return false;
                }
                currElement.val(qval);
                itemQuantity(currElement);
            });

        })(jQuery);

//js for hamburger menu//

        (function () {

            "use strict";

            var toggles = document.querySelectorAll(".c-hamburger");

            for (var i = toggles.length - 1; i >= 0; i--) {
                var toggle = toggles[i];
                toggleHandler(toggle);
            }
            ;

            function toggleHandler(toggle) {
                toggle.addEventListener("click", function (e) {
                    e.preventDefault();
                    (this.classList.contains("is-active") === true) ? this.classList.remove("is-active") : this.classList.add("is-active");
                });
            }

        })();

        $(document).on('click', '#vt-hambug', function () {
            $(".vt-header").toggleClass('slide-in-out');

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

    $(document).ready(function () {
        $(document).on('click', '.left-menu .panel-title > a', function () {
            $(this).children('.fa').toggleClass('fa-angle-up');
        });
    });
</script>