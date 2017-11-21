<?php
if (!empty($intervalList)) {
    foreach ($intervalList as $key => $value) {
        ?>		
        <div class="col-lg-4" style="padding-bottom:15px;">
            <div style="height:40px;font-size:12px;">
                <?php echo $this->Form->checkbox('Interval.Status.' . $key); ?>
                <?php echo $value; ?>
            </div>
            <div>
                <?php echo $this->Form->input('Interval.Price.' . $key, array('type' => 'text', 'class' => 'form-control valid intervalPriceValue', 'placeholder' => '', 'label' => false, 'div' => false)); ?>
            </div>
        </div>		
        <?php
    }
}
?>