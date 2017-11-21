<?php

if (isset($sliderImages) && !empty($sliderImages)) {
    //$encryptedStoreId=$this->Encryption->encode($storeId);
    foreach ($sliderImages as $key => $data) {
        $EncryptimageID = $this->Encryption->encode($data['StoreGallery']['id']);
        echo $this->Html->image('/sliderImages/' . $data['StoreGallery']['image'], array('alt' => 'Slider Image', 'height' => 200, 'width' => 200, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => $data['StoreGallery']['description']));
        echo $this->Html->link("X", array('controller' => 'hq', 'action' => 'deleteSliderPhoto', $EncryptimageID), array('confirm' => 'Are you sure to delete slider Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
    }
}else{
    echo "<div class='col-md-12'>No images found.</div>";
}
?>