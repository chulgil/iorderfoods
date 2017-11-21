<?php
if ($Itemslist) {
    echo $this->Form->input('Topping.item_id', array('type' => 'select', 'class' => 'form-control multiOnly valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => @$Itemslist, 'multiple' => true));
} else {
    echo "No Item Available";
}
?>
<script>
    $(document).ready(function () {
        $('.multiOnly').multiselect();
    });
    $("#SizeAdd").validate({
        rules: {
            "data[Topping][item_id][]": {
                required: true,
            }

        },
        messages: {
            "data[Topping][item_id][]": {
                required: "Please select Item",
            },
        }
    });

</script>
