<?php
if ($addonList) {
    echo $this->Form->input('Topping.addon_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $addonList, 'multiple' => false, 'empty' => "Please select add-on"));
} else {
    echo "No Add-on Available";
}
?>
<script>
    $("#ToppingAddonId").change(function () {
        var toppingId = $("#ToppingAddonId").val();
        var CatgoryId = $("#CategoryId").val();
        var storeId = $("#ToppingStoreId").val();
        if (toppingId) {
            $.ajax({url: "/hqtoppings/getItemsByAddonCategoryId/" + toppingId + "/null/null/" + CatgoryId + "/" + storeId, success: function (result) {
                    $("#ItemsDiv").show();
                    $("#ItemsBox").show();
                    $("#ItemsBox").html(result);
                }});
        }
    });
</script>