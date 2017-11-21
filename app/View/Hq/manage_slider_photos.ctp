<div class="row">
    <div class="col-lg-6">
        <h3>Manage Slider Images</h3><hr> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<div class="row">             
    <?php
    echo $this->Form->create('Stores', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ImageUpload', 'enctype' => 'multipart/form-data'));
    ?>
    <div class="col-lg-6">
        <div class="form-group form-spacing">
            <label>Store<span class="required"> * </span></label> 
            <?php
            $merchantList = $this->Common->getHQStores($merchantId);
            $storeId = '';
            if ($this->Session->read('selectedStoreId')) {
                $storeId = $this->Session->read('selectedStoreId');
            }
            echo $this->Form->input('Store.store_id', array('options' => $merchantList, 'class' => 'form-control', 'label' => '', 'div' => false, 'empty' => 'Please Select Store', 'value' => $storeId));
            ?>
        </div>
        <div class="form-group form_spacing">
            <?php
            echo $this->Form->input('StoreGallery.merchant_id', array('type' => 'hidden', 'value' => $merchantId));
            //echo $this->Form->input('StoreGallery.store_id',array('type'=>'hidden','value'=>$storeId));
            ?>
            <label>Upload Image<span class="required"> * </span></label>    
            <?php
            //echo $this->Form->input('StoreGallery.image][', array('type' => 'file', 'class' => 'form-control user-detail', 'label' => false, 'div' => false, "accept" => "image/*", "multiple", 'id' => 'StoreReviewImage','style' => "box-sizing:initial;"));
            echo $this->Form->input('StoreGallery.image', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
            echo $this->Form->error('StoreGallery.image');
            ?>
            <span class="blue">Max Upload Size 2MB (For best viewing upload images with minimum resolution 1100X500)</span> 
        </div>
        <div class="form-group form-spacing">
            <label>Description</label> 
            <?php
            echo $this->Form->input('StoreGallery.description', array('type' => 'textarea', 'rows' => '6', 'cols' => '8', 'label' => '', 'class' => 'form-control'));
            ?>
        </div>
        <div class="form-group form-spacing">
            <?php
            echo $this->Form->button('Upload File', array('type' => 'submit', 'class' => 'btn btn-default'));
            echo "&nbsp;";
            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hq/dashboard'", 'class' => 'btn btn-default'));
            echo $this->Form->end();
            ?>

        </div>	  
    </div>
    <div style="clear:both;"></div>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group form-spacing showImages">

                <?php
                if (isset($sliderImages)) {
                    //$encryptedStoreId=$this->Encryption->encode($storeId);
                    foreach ($sliderImages as $key => $data) {
                        $EncryptimageID = $this->Encryption->encode($data['StoreGallery']['id']);
                        echo $this->Html->image('/sliderImages/' . $data['StoreGallery']['image'], array('alt' => 'Slider Image', 'height' => 200, 'width' => 200, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => $data['StoreGallery']['description']));
                        echo $this->Html->link("X", array('controller' => 'hq', 'action' => 'deleteSliderPhoto', $EncryptimageID), array('confirm' => 'Are you sure to delete slider Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                    }
                }
                ?>		     
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#ImageUpload").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Store][store_id]": {
                    required: true,
                },
                "data[StoreGallery][image]": {
                    required: true,
                },
            },
            messages: {
                "data[Store][store_id]": {
                    required: "Please select store.",
                },
                "data[StoreGallery][image]": {
                    required: "Please Select image to upload",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });

        $("#StoreStoreId").on('change', function () {
            var storeId = $(this).val();
            if (storeId) {
                $.ajax({
                    url: "<?php echo $this->Html->url(array('controller' => 'hq', 'action' => 'getStoreImages')); ?>",
                    type: "Post",
                    dataType: 'html',
                    beforeSend: function () {
                        //$.blockUI({message: '<h1>Please wait...</h1>'});
                    },
                    complete: function () {
                        //$.unblockUI();
                    },
                    data: {storeId: storeId},
                    success: function (response) {
                        $('.showImages').html(response);
                    }
                });
            }
        });
    });
</script>
<!--Mid Section Ends-->