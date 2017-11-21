<div class="row">
    <h3>Deals</h3>
    <?php echo $this->Session->flash('form1'); ?>
    <hr>
    <div class="col-sm-6">
        <?php
        echo $this->Form->create('StoreDeals', array('url' => array('controller' => 'deals', 'action' => 'deals'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'enctype' => 'multipart/form-data'));
        $this->request->data['StoreDeals']['store_id'] = $this->Session->read('deal_store_id');
        ?>
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
            echo $this->Form->input('StoreDeals.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
            ?>
        </div>
        <div id="dealForm"></div>
        <?php echo $this->Form->end(); ?>
        <br>
        <br>
        <br>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#StoreDealsIndexForm").validate({
            errorClass: "error",
            errorElement: 'span',
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
        $("#StoreDealsStoreId").change(function () {
            var storeId = $(this).val();
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'deals', 'action' => 'getDealForm')); ?>",
                type: "post",
                dataType: 'html',
                async: false,
                data: {storeId: storeId},
                success: function (result) {
                    if (result) {
                        //$("#dealForm").html(result);
                        $("#dealForm").html($(result).find('.col-sm-6').html());
                    }else{
                        $("#dealForm").html('');
                    }
                }
            });
        });
        if ($("#StoreDealsStoreId").val()) {
            $("#StoreDealsStoreId").trigger("change");
        }
    });
</script>