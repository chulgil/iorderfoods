<div class="row">
    <h3>Deals</h3>
    <?php echo $this->Session->flash('form1'); ?>
    <hr>
    <?php
    echo $this->Form->create('StoreDeals', array('url' => array('controller' => 'deals', 'action' => 'deals'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'enctype' => 'multipart/form-data'));
    ?>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Title<span class="required"> * </span></label>
            <?php
            echo $this->Form->input('title', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Title', 'label' => '', 'div' => false, 'value' => @$storeDealData['StoreDeals']['title']));
            ?>
        </div>
        <div class="form-group form_spacing clearfix upload-img-wrap">
            <div class="upload-img-txt">
                <label>Upload Icon Image</label>
                <br>
                <span class="blue">Max image size 2MB (For best viewing upload images with resolution 60*60(W*H))</span>
                <?php
                echo $this->Form->input('icon_image', array('type' => 'file', 'div' => false));
                ?>
            </div>
            <?php
            $EncryptStoreDealID = $this->Encryption->encode(@$storeDealData['StoreDeals']['id']);
            ?>
            <div class="upload-img-frame">
                <?php
                if (!empty($storeDealData['StoreDeals']['icon_image'])) {
                    echo $this->Html->image('/StoreDeals-IconImage/' . $storeDealData['StoreDeals']['icon_image'], array('alt' => 'Store Featured Section Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'deals', 'action' => 'deleteStoreDealImage', $EncryptStoreDealID, 'IconImage'), array('confirm' => 'Are you sure to delete Image?', 'title' => 'Delete Photo'));
                }
                ?>
            </div>
        </div>
        <div class="form-group form_spacing clearfix">
            <div class="upload-img-txt">
                <label>Upload Background Image</label>
                <br>
                <span class="blue">Max image size 2MB (For best viewing upload images with resolution 1360*580(W*H))</span>
                <?php
                echo $this->Form->input('background_image', array('type' => 'file', 'div' => false));
                ?>
            </div>
            <div class="upload-img-frame">
                <?php
                if (!empty($storeDealData['StoreDeals']['background_image'])) {
                    echo $this->Html->image('/StoreDeals-BgImage/' . $storeDealData['StoreDeals']['background_image'], array('alt' => 'Store Featured Section Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'deals', 'action' => 'deleteStoreDealImage', $EncryptStoreDealID, 'BgImage'), array('confirm' => 'Are you sure to delete Image?', 'title' => 'Delete Photo'));
                }
                ?>
            </div>
        </div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default save-coupon')); ?>

        <br>
        <br>
        <br>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#StoreDealsAddCouponForm").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[StoreDeals][title]": {
                    required: true
                }
            },
            messages: {
                "data[StoreDeals][title]": {
                    required: "Please enter title."
                }
            },
            highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });
    });
</script>