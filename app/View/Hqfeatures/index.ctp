<div class="row">
    <div class="col-lg-12">
        <h3>Featured Section Listing</h3>
        <hr>
        <?php echo $this->Session->flash(); ?>
        <div class="row">
            <div class="col-md-4 clearfix">
                <?php echo $this->Form->create('Store', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'AdminId', 'enctype' => 'multipart/form-data')); ?>
                <div class="form-group">
                    <label>Store<span class="required"> * </span></label>
                    <?php
                    $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
                    echo $this->Form->input('store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
                    ?>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">   
            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox">Default Name</th>
                        <th  class="th_checkbox">Featured Name</th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>

                </thead>
                <tbody class="dyntable" id="sortable">
                    <?php
                    if (!empty($list)) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptFeaturedID = $this->Encryption->encode($data['StoreFeaturedSection']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptFeaturedID; ?>">	    
                                <td><?php echo $data['StoreFeaturedSection']['default_name']; ?></td>
                                <td><?php echo $data['StoreFeaturedSection']['featured_name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['StoreFeaturedSection']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqfeatures', 'action' => 'activateFeature', $EncryptFeaturedID, 0), array('confirm' => 'Are you sure to Deactivate this feature?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqfeatures', 'action' => 'activateFeature', $EncryptFeaturedID, 1), array('confirm' => 'Are you sure to Activate this feature?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td class='sort_order'>		
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqfeatures', 'action' => 'edit_features', $EncryptFeaturedID), array('escape' => false)); ?>
                                    | <?php
                                    echo $this->Html->image('uparrow.png', array('alt' => "Up", 'title' => "Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                    echo $this->Html->image('downarrow.png', array('alt' => "Down", 'title' => "Down", 'class' => 'down_order', 'id' => 'downOrder'));
                                    ?>
                                </td> 
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>  
            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-1">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    var notifLen = $('table#preferenceListing').find('tr').length;
    $(document).ready(function () {
        $("#StoreStoreId").change(function () {
            $("#AdminId").submit();
        });

        // Hide up arrow from first row 
        $('table#preferenceListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        // Hide down arrow from last row 
        $('table#preferenceListing').find('tr').eq(notifLen - 1).find('td.sort_order').find('img.down_order').hide();

        var $up = $(".up_order")
        $up.click(function () {
            var $tr = $(this).parents("tr");
            if ($tr.index() != 0) {
                $tr.fadeOut().fadeIn();
                $tr.prev().before($tr);

            }
            updateOrder();
        });
        //down
        var $down = $(".down_order");
        var len = $down.length;
        $down.click(function () {
            var $tr = $(this).parents("tr");

            if ($tr.index() <= len) {
                $tr.fadeOut().fadeIn();
                $tr.next().after($tr);
            }
            updateOrder();
        });
    });

    function updateOrder() {
        $('img.up_order').show();
        $('img.down_order').show();

        $('table#preferenceListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        $('table#preferenceListing').find('tr').eq(notifLen - 1).find('td.sort_order').find('img.down_order').hide();
        var orderData = getNotifOrderKeyVal();

        if (orderData) {
            $.ajax({
                url: '/hqfeatures/updateFeaturePosition?' + orderData,
                type: 'get',
                success: function () {
                }
            });
        }
    }
    function getNotifOrderKeyVal() {
        if ($('table#preferenceListing tbody').eq(0).find('tr').length > 0) {
            var orderData = '';
            $('table#preferenceListing tbody').eq(0).find('tr').each(function (i) {
                var notifId = $(this).attr('notif-id');
                orderData += notifId + '=' + (i + 1) + '&';
            });
            return orderData;
        }
        return false;
    }
</script>