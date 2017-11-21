<div class="placeEditDiv">
    <div class="col-lg-6">
        <h3>Add Zone</h3>
        <hr>
    </div>
    <div class="col-lg-6">
        <?php echo $this->Form->create('Zone', array('url' => array('controller' => 'zones', 'action' => 'addZoneCircle'), 'class' => "clearfix")); ?>
        <div class="form-group">
            <label for="name">
                Zone Name</label>
            <?php echo $this->Form->input('name', array('type' => 'text', "placeholder" => "Enter Zone Name", 'label' => false, 'maxlength' => '20', "class" => "form-control", 'div' => false)); ?>
        </div>
        <div class="form-group">
            <label for="fee">
                Fee</label>
            <?php echo $this->Form->input('fee', array('type' => 'text', "placeholder" => "Enter Zone Fee", 'label' => false, 'maxlength' => '20', "class" => "form-control", 'div' => false)); ?>
        </div>
<!--        <div class="form-group">
            <label for="distance">
                Distance</label>
            <?php //echo $this->Form->input('distance', array('type' => 'test', "placeholder" => "Enter Distance In Miles", 'label' => false, "class" => "form-control", 'div' => false)); ?>
        </div>-->
        <?php echo $this->Form->input('SUBMIT', array('type' => 'button', "class" => "btn btn-default", 'label' => false, 'div' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script>
    $("#ZoneCircleForm").validate({
        debug: false,
        errorClass: "error",
        errorElement: 'span',
        onkeyup: false,
        rules: {
            "data[Zone][name]": {
                required: true,
                alphanumeric: true,
                remote: "/zones/checkZoneName"
            },
            "data[Zone][fee]": {
                number: true,
                required: true,
            },
            "data[Zone][distance]": {
                number: true,
                required: true
            }
        },
        messages: {
            "data[Zone][name]": {
                remote: "Zone name already exist.",
            }
        }, highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        }
    });
</script>