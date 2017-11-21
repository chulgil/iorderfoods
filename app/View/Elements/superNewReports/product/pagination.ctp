<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered table-hover table-striped tablesorter">
            <thead>
                <tr>	    
                    <th  class="th_checkbox">Product Name</th>
                    <th  class="th_checkbox">Category</th> 
<!--                    <th  class="th_checkbox">Unit Price ($)</th>-->
                    <th  class="th_checkbox"># of Items</th>
                    <th  class="th_checkbox">Revenue ($)</th>
                </tr>
            </thead>

            <tbody class="dyntable">
            <?php  
            if(!empty($productData)){
                $i = 0;
                foreach($productData as $key => $data){        
                    $class = ($i%2 == 0) ? ' class="active"' : '';
                ?>
                <tr>  
                    <td>
                        <?php echo (isset($data['Item']['name']) ? $data['Item']['name'] : '-'); ?>
                    </td>
                    <td>
                        <?php echo (isset($data['Item']['Category']['name']) ? $data['Item']['Category']['name'] : '-'); ?>
                    </td>
<!--                    <td>
                        <?php //echo (isset($data[0]['unit_price']) ? $this->Common->amount_format($data[0]['unit_price']) : '-');?>
                    </td>-->
                    <td>
                        <?php echo (isset($data[0]['number']) ? $data[0]['number'] : '-');?>
                    </td>
                    <td>
                        <?php echo (isset($data[0]['total_amount']) ? $this->Common->amount_format($data[0]['total_amount']) : '-');?>
                    </td>
                </tr>
		<?php 
                $i++;
                }
            }else{
                ?>
                <tr>
                    <td colspan="13" style="text-align: center;">
                        No record available
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</div>