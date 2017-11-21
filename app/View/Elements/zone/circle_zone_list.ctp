<div><br><br>
    <?php echo $this->Session->flash(); ?> 
</div>
<div class="col-lg-6">
    <div class="table-responsive">
        <span class="blue">(List of Delivery Zones)</span> 
        <table class="table table-bordered table-hover table-striped tablesorter">
            <thead>
                <tr>	    
                    <th  class="th_checkbox">Zone name</th>
                    <th  class="th_checkbox">Delivery fee($)</th>
                    <th  class="th_checkbox">Distance(In Miles)</th>                 
                    <th  class="th_checkbox">Action</th>
                </tr>
            </thead>

            <tbody class="dyntable">
                <?php if ($zones) { ?>
                    <?php foreach ($zones as $key => $data) { ?>
                        <tr>	    
                            <td><?php echo $data['Zone']['name']; ?></td>
                            <td><?php echo $data['Zone']['fee']; ?></td>     
                            <td><?php echo round($data['Zone']['distance'] / 1609.344, 2); ?></td>   
                            <td>
                                <?php $EncryptzoneID = $this->Encryption->encode($data['Zone']['id']); ?>
                                <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'zones', 'action' => 'deletezone', $EncryptzoneID), array('confirm' => 'Are you sure to delete record?', 'escape' => false)); ?>
                                <?php //echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'zones', 'action' => 'editZone', $EncryptzoneID), array('escape' => false)); ?>
                                <?php echo $this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit", 'id' => 'editZone', 'data-id' => $EncryptzoneID)); ?>
                            </td>
                        </tr>
                        <?php
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
    </div>
</div>
<script>
    $(document).on('click', '#editZone', function () {
        var zoneId = $(this).data('id');
        $.ajax({
            type: 'post',
            url: "<?php echo $this->Html->url(array('controller' => 'zones', 'action' => 'getZoneDetail')); ?>",
            data: {'zoneId': zoneId},
            beforeSend: function () {
                $('#loading').removeClass('hidden');
            },
            complete: function () {
                $('#loading').addClass('hidden');
            },
            success: function (result) {
                if (result) {
                    $('.placeEditDiv').html(result);
                }
            }
        });
    });
</script>