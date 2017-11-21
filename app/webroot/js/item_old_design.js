/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

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
                    $("#ItemSubpreference" + type_id + "Size").hide();
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
                        $("#ItemSubpreference" + type_id + "Size").show();
                        $('.subPrice' + type_id).show();
                    } else {
                        //subpre hide and trigger start
                        $("#ItemSubpreference" + type_id + "Size").val(1);
                        $("#ItemSubpreference" + type_id + "Size").hide();
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
                    $("#ItemToppings" + topping_id + "Size").show();
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
                        $("#ItemToppings" + topping_id + "Size").show();
                        $('.topPrice' + topping_id).show();
                    } else {
                        //subaddon hide and trigger start
                        $("#ItemToppings" + topping_id + "Size").val(1);
                        $("#ItemToppings" + topping_id + "Size").show();
                        $('.topPrice' + topping_id).show();
                        $("#ItemToppings" + topping_id + "Size").trigger('change');
                        //subaddon hide and trigger end
                    }
                }
            }
        }
    });
}
$(document).ready(function () {
    var orderId = $("#orderId").val();
    var encrypStoreID = $("#encryptedStoreId").val();
    var encrypMerchID = $("#encryptedMerchantId").val();
    if (orderId) {
        $.ajax({
            type: 'post',
            url: '/Products/reorder',
            data: {'orderId': orderId},
            success: function (result) {
                var parsedJson = $.parseJSON(result);
                if (parsedJson.count == 0) {

                    $("#errorPop").modal('show');
                    $("#errorPopMsg").html('Items are no longer available.');
                    return false;

                } else {
                    if (parsedJson.item >= 1) {
                        $("#errorPop").modal('show');
                        $("#errorPopMsg").html('Items are no longer available.');
                        return false;
                    } else {
                        $.ajax({
                            type: 'post',
                            url: '/Products/fetchReorderProduct',
                            data: {},
                            success: function (result2) {
                                if (result2 == 1) {
                                    window.location = "/Products/items/" + encrypStoreID + "/" + encrypMerchID;
                                }
                            }
                        });
                    }
                }
            }
        });
    }


    $(document).on("click", ".remove", function () {

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


    $(document).on("click", "#mobile_continuemenu", function () {
        $(window).scrollTop($('#anchorName').offset().top);
    });

    $(document).on("click", ".singleItemRemove", function () {
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

    $(document).on("blur", ".coupon-code", function () {
        var coupon_code = $(this).val();
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

    $(document).on('change blur keyup', '.subPreferenceSize', function (e)
    {
        var minSubPreferenceSize = Number($(this).attr('min'));
        var maxSubPreferenceSize = Number($(this).attr('max'));
        var subPreferenceSizeVal = Number($(this).val());
        if (!isInt(subPreferenceSizeVal))
        {
            return false;
        }
        if (subPreferenceSizeVal >= minSubPreferenceSize && subPreferenceSizeVal <= maxSubPreferenceSize)
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
                $("#ItemSubpreference" + subPreferenceId + "Size").hide();
                $('.subPrice' + subPreferenceId).hide();
            }
            //var type = $('.type' + subPreferenceId).val();
            var countSizes = $("#countSizes").val();
            var item_id = $('#ItemHiddenItemID').val();
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
                                        $("#ItemSubpreference" + subPreferenceId + "Size").hide();
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


    $(document).on('change blur keyup', ".quantity", function () {
        $('#loading').show();
        var index_id = $(this).parent().attr('Key'); //index of session array
        var value = Number(this.value);
        if (value <= 0) {
            return false;
        }

        if (!isInt(value))
        {
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

    function isInt(n) {
        return Number(n) === n && n % 1 === 0;
    }

    $(document).on('change blur keyup', ".toppingSize", function () {
        var minTopping = Number($(this).attr('min'));
        var maxTopping = Number($(this).attr('max'));
        var toppingVal = Number($(this).val());
        if (!isInt(toppingVal))
        {
            return false;
        }
        if (toppingVal >= minTopping && toppingVal <= maxTopping)
        {
            var subDefault = $("#subdefault").val();
            var countSizes = $("#countSizes").val();
            var size_id = $(this).val();
            var SelecttopID = $(this).attr('rel');
            var checkToSelect = "#ItemToppings" + SelecttopID + "Id";
            var topping_id = $(checkToSelect).val();
            var checked = $(checkToSelect).prop("checked");
            if (checked == true) {
                checked = 1;
            } else {
                checked = 0;
                $('#ItemToppings' + topping_id + 'Size').hide();
                $('.topPrice' + topping_id).hide();
            }
            var type = $('.type' + topping_id).val();
            var item_id = $('#ItemHiddenItemID').val();
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
                                        $("#ItemToppings" + topping_id + "Size").hide();
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


    $(document).on("click", ".toppings", function () {
        var subDefault = $("#subdefault").val();
        var countSizes = $("#countSizes").val();
        var topping_id = $(this).val();
        var item_id = $('#ItemHiddenItemID').val();
        var size_id = $('.toppingSize' + topping_id).val();
        if ($(this).prop("checked") == true) {
            var checked = 1;
        } else {
            var checked = 0;
            $('#ItemToppings' + topping_id + 'Size').hide();
            $('.topPrice' + topping_id).hide();
        }

        var type = $('.type' + topping_id).val();
        if ($('.item_price:checked').val()) {
            var itemSizeId = $('.item_price:checked').val();
        } else {
            var itemSizeId = 0;
        }
        var addonMainId = $('#ItemToppings' + topping_id + 'AddonId').val();
        var selected = [];
        $('.addonId_' + addonMainId + ':checkbox:checked').each(function () {
            selected.push({
                id: $(this).attr('value'),
                size: $('#ItemToppings' + $(this).attr('value') + 'Size').val()
            });
        });
        priceCheckAgainAddons(size_id, type, topping_id, item_id, checked, itemSizeId, countSizes, subDefault, addonMainId, selected)
    });

    $(document).on("click", ".item_price", function () {
        var size_id = $(this).val();
        var subdefault = $("#subdefault").val();
        var item_id = $('#ItemHiddenItemID').val();
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

    $(document).on("click", ".item_type", function () {
        var type_id = $(this).val();
        var subDefault = $("#subdefault").val();
        var item_id = $('#ItemHiddenItemID').val();
        var subPreferenceId = $(this).attr('rel');
        var countSizes = $("#countSizes").val();
        var subSizeID = $("#ItemSubpreference" + type_id + "Size").val();
        var checked = $("#ItemSubpreference" + type_id + "Id").prop("checked");
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
            $("#ItemSubpreference" + subPreferenceId + "Size").hide();
            $('.subPrice' + subPreferenceId).hide();
        }
        if ($('.item_price:checked').val()) {
            var sizeId = $('.item_price:checked').val();
        } else {
            var sizeId = 0;
        }
        priceCheckAgain(type_id, subDefault, item_id, countSizes, subSizeID, checked, sizeId, preMainId, selected);
    });

});


$(document).ready(function () {
    var middlebox = $("#selectOrderTypes").height();
    var firstbox = $("#anchorName").height();
    var lastbox = $("#isolated").height();
    var headerHeight = $('header').outerHeight();
    var largestheight = Math.max(firstbox, middlebox, lastbox);
    var totalHeight = (headerHeight + largestheight);
    $('.content').css('height', 30 + (totalHeight) + 'px');
});


function addtocart() {
    var chkloginstatus = $("#chkloginstatus").val();
    if (chkloginstatus == 'invalid')
    {
        $("#chkloginstatus").val('invalid');
        return false;
    }

    var resorder = checkOrderTime();
    if (resorder == 0) {
        return false;
    }

    var login = false;
    var Data = getItemData();//jQuery.parseJSON(getItemData());

    if (Data != 0) {
        login = true;
    }

    if (login) {
        $.ajax({
            type: 'post',
            url: '/Products/cart',
            dataType: "json",
            async: false,
            data: {mydata: Data},
            complete: function (data1) {
                $('.online-order').html(data1.responseText);
                var offerHiddenid = $("#offerHiddenid").val();
                if (offerHiddenid == 1) {
                    if (window.screen.width < 700) {
                        $(window).scrollTop($('div#offerItemsanchor').offset().top - 30);
                    } else {
                        $(window).scrollTop($('#anchorName').offset().top);
                    }
                } else {
                    if (window.screen.width < 700) {
                        $(window).scrollTop($('#cartstart').offset().top - 30);
                    } else {
                        $(window).scrollTop($('#anchorName').offset().top);
                    }
                }

                setTimeout(function () {
                    $('#loading').hide();
                }, 500);

            }
        });
    }
}
function itemzero(price) {
    if (price <= 0) {
        $(".addzero").show();
        $("#addtocart").hide();

    } else {
        $("#addtocart").show();
        $(".addzero").hide();
    }
}
function fetchProductSize() {
    var sizeId = $('.item_price:checked').val();
    var itemId = $('#ItemHiddenItemID').val();
    var categoryId = $("#ItemCategoryID").val();
    $.ajax({
        url: "/Products/fetchProductSize",
        type: "Post",
        data: {'sizeId': sizeId, 'itemId': itemId, 'categoryId': categoryId},
        success: function (result) {
            $('.prefernceAndAddOns').html(result);
        },
        complete: function (result) {
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
    });
}

function returnfalse() {
    return false;
}
function checkuserlogin() {
    var theResponse = null;
    $.ajax({
        type: 'POST',
        url: '/ajaxMenus/checklogin',
        data: {},
        async: false,
        success: function (response) {
            var data = jQuery.parseJSON(response);
            if (data == '0' || data == 0) {
                $("#chkloginstatus").val('invalid');
                return false;
            } else {
                $("#chkloginstatus").val('');
            }
        }
    });

}


/* Check Mandatory preference*/
var resonseVal = '';
function checkPreference(data) {
    $.ajax({
        type: 'POST',
        url: '/Products/checkPreference',
        data: data,
        async: false,
        success: function (response) {
            resonseVal = response;
        }
    });
    return resonseVal;
}

/* Check Order type*/
var orderVal = '';
function checkOrderType() {

    $.ajax({
        type: 'POST',
        url: '/Products/checkOrderType',
        async: false,
        success: function (response) {
            orderVal = response;
        }
    });
    return orderVal;
}
/* Get delivery address */
var deliVal = '';
function checkdeliveryadd() {
    $.ajax({
        type: 'POST',
        url: '/Products/checkdeliveryadd',
        async: false,
        success: function (response) {
            deliVal = response;
        }
    });
    return deliVal;
}

/* Get Cart Count */
function cartcount() {
    $.ajax({
        type: 'post',
        url: '/Products/getcartCount',
        data: {},
        async: false,
        success: function (data1) {
            if (data1) {
                $('.numberCircle').html(data1);
            }
        }
    });
}

/*  */
function addcart1(data) {
    $.ajax({
        type: 'post',
        url: '/products/addtosession',
        data: data,
        async: false,
        success: function () {

        }
    });
}

/*  */
function removeAddcart() {
    $.ajax({
        type: 'post',
        url: '/Products/removefrmSession',
        data: {},
        async: false,
        success: function () {

        }
    });
}


/* */
var orderTime = '';
function checkOrderTime() {
    $.ajax({
        type: 'post',
        url: '/Products/checkOrderTime',
        data: {},
        async: false,
        success: function (response) {
            orderTime = response;
        }
    });
    return orderTime;
}

/* Get item session data*/
var itemVal = '';
function getItemData() {
    $.ajax({
        type: 'POST',
        url: '/Products/getitemdata',
        async: false,
        success: function (response) {
            itemVal = response;
        }
    });
    return itemVal;
}
$(document).ready(function () {

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

});


