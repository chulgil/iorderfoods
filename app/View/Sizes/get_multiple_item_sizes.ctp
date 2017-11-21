<?php
if ($sizeList) {
    $checkarr = array();
    foreach ($sizeList as $itemkey => $Sizesoptions) {
        if (isset($this->data['OfferDetails']) && $this->data['OfferDetails']) {
            foreach ($this->data['OfferDetails'] as $key => $existingOfferdetails) {
                $checkarr[] = $existingOfferdetails['item_id'];
            }
            foreach ($this->data['OfferDetails'] as $key => $existingOfferdetails) {
                if ($existingOfferdetails['item_id'] == $itemkey) {
                    //echo $existingOfferdetails['item_id']."#".$itemkey;
                    $itemdetails = $this->Common->getitemdetals($itemkey);
                    echo "<hr>";
                    echo "<p><strong>(" . $itemdetails['Item']['name'] . ")</strong></p>";
                    $value = (isset($existingOfferdetails['id'])) ? $existingOfferdetails['id'] : 0;
                    echo $this->Form->input('OfferDetails.' . $itemkey . '.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'value' => $value, 'class' => 'serialize'));
                    echo $this->Form->input('OfferDetails.' . $itemkey . '.item_id', array('type' => 'hidden', 'label' => false, 'div' => false, 'value' => $itemkey, 'class' => 'serialize'));
                    if ($Sizesoptions) {
                        $value = (isset($existingOfferdetails['offerSize'])) ? $existingOfferdetails['offerSize'] : 0;
                        echo $this->Form->input('OfferDetails.' . $itemkey . '.offerSize', array('type' => 'select', 'class' => 'form-control valid serialize', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $Sizesoptions, 'label' => 'Size', 'value' => $value));
                    }
                    echo $this->Form->input('OfferDetails.' . $itemkey . '.discountAmt', array('type' => 'text', 'class' => 'form-control valid serialize priceVal', 'placeholder' => 'Enter Price', 'label' => 'Price', 'div' => false, 'value' => $existingOfferdetails['discountAmt'], 'required' => true));
                    echo '<span class="blue">(Enter price for item if applicable)</span>';
                } elseif (!in_array($itemkey, $checkarr)) {
                    $checkarr[] = $itemkey;
                    $itemdetails = $this->Common->getitemdetals($itemkey);
                    echo "<hr>";
                    echo "<p><strong>(" . $itemdetails['Item']['name'] . ")</strong></p>";
                    echo $this->Form->input('OfferDetails.' . $itemkey . '.item_id', array('type' => 'hidden', 'label' => false, 'div' => false, 'value' => $itemkey, 'class' => 'serialize'));
                    if ($Sizesoptions) {
                        echo $this->Form->input('OfferDetails.' . $itemkey . '.offerSize', array('type' => 'select', 'class' => 'form-control valid serialize', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $Sizesoptions, 'label' => 'Size'));
                    }
                    echo $this->Form->input('OfferDetails.' . $itemkey . '.discountAmt', array('type' => 'text', 'class' => 'form-control valid serialize priceVal', 'placeholder' => 'Enter Price', 'label' => 'Price', 'div' => false, 'required' => true));
                    echo '<span class="blue">(Enter price for item if applicable)</span>';
                }
            }
        } else {
            $itemdetails = $this->Common->getitemdetals($itemkey);
            echo "<hr>";
            echo "<p><strong>(" . $itemdetails['Item']['name'] . ")</strong></p>";
            echo $this->Form->input('OfferDetails.' . $itemkey . '.item_id', array('type' => 'hidden', 'label' => false, 'div' => false, 'value' => $itemkey, 'class' => 'serialize'));
            if ($Sizesoptions) {
                echo $this->Form->input('OfferDetails.' . $itemkey . '.offerSize', array('type' => 'select', 'class' => 'form-control valid serialize', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $Sizesoptions, 'label' => 'Size'));
            }
            echo $this->Form->input('OfferDetails.' . $itemkey . '.discountAmt', array('type' => 'text', 'class' => 'form-control valid serialize priceVal', 'placeholder' => 'Enter Price', 'label' => 'Price', 'div' => false, 'required' => true));
            echo '<span class="blue">(Enter price for item if applicable)</span>';
        }
    }
} else {
    echo "No size Available";
}
?>
<script>
    $('.priceVal').each(function () {
        $(this).rules("add", {
            required: true,
            number: true,
            maxlength: 8
        });
    });
    $('.priceVal').keyup(function () {
        this.value = this.value.replace(/[^0-9.,]/g, '');
    });
</script>