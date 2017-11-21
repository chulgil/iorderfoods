<div class="col-lg-6">
    <h3>Edit Zone</h3>
    <hr>
</div>
<div class="col-lg-6">
    <?php echo $this->Form->create('Zone', array('url' => array('controller' => 'zones', 'action' => 'editZone'), 'class' => "clearfix")); ?>
    <div class="form-group">
        <label for="name">
            Zone Name</label>
        <?php
        echo $this->Form->input('id', array('type' => 'hidden'));
        echo $this->Form->input('type', array('type' => 'hidden','value'=>0));
        echo $this->Form->input('name', array('type' => 'text', "placeholder" => "Enter Zone Name", 'label' => false, 'maxlength' => '20', "class" => "form-control", 'div' => false));
        ?>
    </div>
    <div class="form-group">
        <label for="fee">
            Fee</label>
        <?php echo $this->Form->input('fee', array('type' => 'text', "placeholder" => "Enter Zone Fee", 'label' => false, 'maxlength' => '20', "class" => "form-control", 'div' => false)); ?>
    </div>
<!--    <div class="form-group">
        <label for="distance">
            Distance</label>
        <?php //echo $this->Form->input('distance', array('type' => 'test', "placeholder" => "Enter Distance In Miles", 'label' => false, "class" => "form-control", 'div' => false)); ?>
    </div>-->
    <?php echo $this->Form->input('SUBMIT', array('type' => 'button', "class" => "btn btn-default submitEditZone", 'label' => false, 'div' => false)); ?>
    <?php echo $this->Html->link('Cancel', array('controller' => 'zones', 'action' => 'dash'), array("class" => "btn btn-default", 'escape' => false)); ?>
    <?php echo $this->Form->end(); ?>
</div>
<script>
    $("#ZoneGetZoneDetailForm").validate({
        debug: false,
        errorClass: "error",
        errorElement: 'span',
        onkeyup: false,
        rules: {
            "data[Zone][name]": {
                required: true,
                alphanumeric: true,
                remote: "/zones/checkZoneName?id=" + $('#ZoneId').val()
            },
            "data[Zone][fee]": {
                number: true,
                required: true
            },
            "data[Zone][distance]": {
                number: true,
                required: true
            }
        },
        messages: {
            "data[Zone][name]": {
                remote: "Zone name already exist."
            }
        }, highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        }
    });
    $(".submitEditZone").on('click', function (e) {
        if ($("#ZoneGetZoneDetailForm").valid()) {
            $('#ZoneGetZoneDetailForm').submit();
        } else {
            return false;
        }
        e.preventDefault();
    });
</script>