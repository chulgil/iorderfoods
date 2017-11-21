<div class="row">
    <div class="col-lg-12">
        <h3>Item Listing</h3>
        <hr />
        <span id="flashMessage"></span>
        <div class="table-responsive">   
            <?php echo $this->Form->create('Item', array('url' => array('controller' => 'features', 'action' => 'featuredItemList'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('Item.category_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$categoryList, 'empty' => 'Select Category')); ?>		
                </div>
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('keyword', array('value' => @$keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                    <span class="blue">(<b>Search by:</b>Name, Category, Description)</span>
                </div>
                <div class="col-lg-1">		 
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">		 
                    <?php echo $this->Html->link('Clear', array('controller' => 'features', 'action' => 'featuredItemList', 'clear'), array('class' => 'btn btn-default')); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php
            if (!empty($list)) {
                echo $this->element('show_pagination_count');
            }
            ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="itemListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="width: 15%;">Name</th>
                        <th  class="th_checkbox" style="width: 15%;">Category</th>
                        <th  class="th_checkbox" style="width: 15%;">Description</th> 
                        <th  class="th_checkbox" style="width: 55%;">Action</th>
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if (!empty($list)) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptItemID = $this->Encryption->encode($data['Item']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptItemID; ?>">
                                <td><?php echo $data['Item']['name']; ?></td>
                                <td><?php echo $data['Category']['name']; ?></td>
                                <td><?php
                                    $len = strlen($data['Item']['description']);
                                    if ($len > 50) {
                                        $pos = strpos($data['Item']['description'], ' ', 49);
                                        echo substr($data['Item']['description'], 0, $pos);
                                    } else {
                                     echo $data['Item']['description'];   
                                    }
                                    ?>
                                </td> 
                                <td><?php
                                    if (!empty($sfList)) {
                                        foreach ($sfList as $sfl) {
                                            $fVal = $this->Common->getFeaturedItemStatus($sfl['StoreFeaturedSection']['id'], $data['Item']['id']);
                                            if (!empty($fVal) && $fVal['FeaturedItem']['is_active'] == '1') {
                                                $check = true;
                                            } else {
                                                $check = false;
                                            }
                                            echo $this->Form->input('featured_check', array(
                                                'type' => 'checkbox',
                                                'label' => false,
                                                'before' => '<label>' . $sfl['StoreFeaturedSection']['featured_name'],
                                                'after' => '</label> &nbsp;&nbsp;&nbsp;',
                                                'data-id' => $this->Encryption->encode($sfl['StoreFeaturedSection']['id']),
                                                'data-itemid' => $EncryptItemID,
                                                'checked' => $check,
                                                'div' => false
                                            ));
                                        }
                                    }
                                    ?></td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
<?php } ?>
                </tbody>
            </table>
            <?php if (!empty($list)) { ?>
                <?php $this->element('pagination') ?>
<?php } ?>
            <div class="row padding_btm_20" style="padding-top:10px">
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#ItemCategoryId").change(function () {
            var catgoryId = $("#ItemCategoryId").val();
            $("#AdminId").submit();
        });
        $("#ItemIsActive").change(function () {
            //var catgoryId=$("#ItemCategoryId").val();
            $("#AdminId").submit();
        });
        $("#selectall").click(function () {

            var st = $("#selectall").prop('checked');
            $('.case').prop('checked', st);
        });
        // if all checkbox are selected, check the selectall checkbox
        // and viceversa
        $(".case").click(function () {
            if ($(".case").length == $(".case:checked").length) {
                $("#selectall").attr("checked", "checked");
            } else {
                $("#selectall").removeAttr("checked");
            }

        });
    });
    function check()
    {

        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select one item to proceed.');
            // cancel submit
            return false;
        }
        var r = confirm("Are you sure you want to delete");
        if (r == true) {
            txt = "You pressed OK!";
        } else {
            txt = "You pressed Cancel!";
            return false;
        }

    }
</script>
<script type="text/javascript">
    $('img.up_order').hide();
    $('img.down_order').hide();
    if ($('#ItemCategoryId').val() == '') {
        $('img.up_order').hide();
        $('img.down_order').hide();
    } else {
        $('img.up_order').show();
        $('img.down_order').show();
    }

    $('select.ItemCategoryId').change(function () {
        $('img.up_order').show();
        $('img.down_order').show();
    });</script>
<script>
    var notifLen = $('table#itemListing').find('tr').length;
    $(document).ready(function () {

        // Hide up arrow from first row 
        $('table#itemListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        // Hide down arrow from last row 
        $('table#itemListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();
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
        $('table#itemListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        $('table#itemListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();
        var orderData = getNotifOrderKeyVal();
        if (orderData) {
            $.ajax({
                url: '/items/updateOrder?' + orderData,
                type: 'get',
                success: function () {


                }
            });
        }
    }

    function getNotifOrderKeyVal() {
        if ($('table#itemListing tbody').eq(0).find('tr').length > 0) {
            var orderData = '';
            $('table#itemListing tbody').eq(0).find('tr').each(function (i) {
                var notifId = $(this).attr('notif-id');
                orderData += notifId + '=' + (i + 1) + '&';
            });
            return orderData;
        }
        return false;
    }
    $(document).on('change', '#featured_check', function () {
        var featuredId = $(this).data('id');
        var item_id = $(this).data('itemid');
        var status = $(this).is(":checked");
        if (status == true) {
            status = 1;
        } else {
            status = 0;
        }
        if (featuredId && item_id) {
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'features', 'action' => 'ajaxfeatureUpdate')); ?>",
                type: 'post',
                data: {featured_id: featuredId, item_id: item_id, status: status},
                success: function (response) {
                    if (response != '') {
                        var result = $.parseJSON(response);
                        if (result.status == 'Success') {
                            $("#flashMessage").html('<div class="alert-success">' + result.msg + '</div');
                            ;
                        } else if (result.status == 'Error') {
                            $("#flashMessage").html('<div class="alert-danger">' + result.msg + '</div');
                        }
                        setTimeout(function () {
                            $('#flashMessage').html('');
                        }, 6000);
                    }
                }
            });
        }
    });
</script>