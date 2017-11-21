<style>
    .days{
        float: left;
        padding: 0 5px;
        text-align: center;
        width: auto;
    }
</style>

<div class="row">
    <div class="col-lg-6">
        <h3>Add Time-Interval</h3>
        <hr></hr>
        <?php echo $this->Session->flash(); ?>   
    </div>
    <div class="col-lg-6">                        
    </div>

</div>

<div class="row">        
    <?php echo $this->Form->create('Interval', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addInterval', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">

        <div class="form-group form_margin">		 
            <label>Interval Name<span class="required"> * </span></label>               
            <?php echo $this->Form->input('Interval.name', array('type' => 'text', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'placeholder' => 'Interval Name', 'maxlength' => '40')); ?>
            <?php echo $this->Form->error('Interval.name'); ?>

        </div>    

        <div class="form-group form_margin">		 
            <label>Start Time<span class="required"> * </span></label>
            <?php echo $this->Form->input('Interval.start', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $timeRange, 'empty' => 'Select start time')); ?>
            <?php echo $this->Form->error('Interval.start'); ?>
        </div>

        <div class="form-group form_margin">		 
            <label>End Time<span class="required"> * </span></label>
            <?php echo $this->Form->input('Interval.end', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $timeRange, 'empty' => 'Select end time')); ?>
            <?php echo $this->Form->error('Interval.end'); ?>
        </div>


        <div class="form-group form_spacing">		 
            <label>Status<span class="required"> * </span></label><span>&nbsp;&nbsp;</span>                  
            <?php
            $value = 1;
            if (isset($this->request->data['Interval']['is_active'])) {
                $value = $this->request->data['Interval']['is_active'];
            }
            echo $this->Form->input('Interval.is_active', array('type' => 'radio', 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;', 'value' => $value, 'options' => array('1' => 'Active', '0' => 'Inactive')));
            ?>		 
        </div>

        <div class="form-group form_spacing">		 
            <label>Day</label><span>&nbsp;&nbsp;</span>
            <div class="intervalDays" >
                <?php foreach ($daysArray as $key => $value) { ?>
                    <div class="days">
                        <div>
                            <?php echo $this->Form->checkbox('IntervalDay.' . $key); ?>	
                        </div>
                        <div>
                            <?php echo $value; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="clearfix"><br><br><br></div> 
        <div class="form-group form_spacing">			
            <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
            <?php echo $this->Html->link('Cancel', "/intervals/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $("#addInterval").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Interval][name]": {
                    required: true,
                },
                "data[Interval][start]": {
                    required: true,
                },
                "data[Interval][end]": {
                    required: true,
                },
            },
            messages: {
                "data[Interval][name]": {
                    required: "Please enter time-interval name",
                },
                "data[Interval][start]": {
                    required: "Please select start time",
                },
                "data[Interval][end]": {
                    required: "Please select end time",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
    });
</script>