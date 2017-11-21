<style>
    @media screen and (min-width: 480px) {
        .addressBox{
            border: 1px solid #d9d9d9;
            float: left;
            margin: 5px;
            padding: 7px;
            width:32%;            
        }
    }   

    @media screen and (max-width: 480px) {
        .addressBox{
            border: 1px solid #d9d9d9;
            float: left;
            margin: 5px;
            padding: 7px;
            width:95%;
        }
    } 
    h3{
        padding:5px;
        margin-bottom:10px;
        font-size:15px;
        font-weight:bold;
    }
</style>
<?php
$protocol = 'http';
if (isset($_SERVER['HTTPS'])) {
    if (strtoupper($_SERVER['HTTPS']) == 'ON') {
        $protocol = 'https';
    }
}
?>
<div class="row">
    <div class="col-md-12 clearfix">
        <div class="row">
            <div class="col-xs-6">
                <h3>Store's Information</h3>
            </div>
            <div class="col-xs-6">
                <?php echo $this->Html->link('Back', "/super/viewMerchantDetails", array("class" => "btn btn-default pull-right", 'escape' => false)); ?>
            </div>
        </div>
        <hr>
    </div>
    <div class='col-lg-12'>

        <?php
        if (!empty($storeData)) {
            foreach ($storeData as $akey => $data) {
                ?>
                <div class="col-lg-4 addressBox">
                    <div>
                        <label>Location:</label>
                        <span><?php echo $this->Html->link($data['Store']['city'], $protocol . "://" . $data['Store']['store_url']); ?></span>
                    </div>

                    <div>
                        <label>Address:</label>
                        <span><?php echo $data['Store']['address']; ?> <br> <?php echo $data['Store']['city'] . ' ' . $data['Store']['state'] . ' ' . $data['Store']['zipcode']; ?></span>
                    </div>

                    <div>
                        <label>Phone no:</label>
                        <span><?php echo $data['Store']['phone'] ?></span>
                    </div>
                    <div>
                        <label>URL:</label>
                        <span><?php echo $this->Html->link($data['Store']['store_url'], $protocol . "://" . $data['Store']['store_url'], array('target' => '_blank')); ?></span>
                    </div>

                    <div>
                        <label>Date of Creation:</label>
                        <span><?php echo $this->Dateform->us_format($data['Store']['created']); ?></span>
                    </div>
                </div> 
                <?php
            }
        }
        ?>


    </div>



</div>