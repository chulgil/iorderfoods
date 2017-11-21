<?php
if ($addonList) {
    echo $this->Form->input('Topping.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $addonList, 'multiple' => false, 'empty' => "Please select add-on"));
} else {
    echo "No Add-on Available";
}
?>
<script>

    $("#ToppingId").change(function () {
        var toppingId = $("#ToppingId").val();
        var CategoryId = $("#CategoryId").val();
        if (toppingId) {
            $.ajax({url: "/toppings/getItemsByAddonCategoryIdMultiple/" + toppingId + "/" + CategoryId, success: function (result) {
                    $("#ItemsDiv").show();
                    $("#ItemsBox").show();
                    $("#ItemsBox").html(result);
                }});
        } else {
            $("#ItemsDiv").hide();
            $("#ItemsBox").hide();
            $("#ItemsBox").html('');
        }
    });

</script>