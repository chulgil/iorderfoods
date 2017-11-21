<?php 
$latitude = $store_data['Store']['latitude'];
$logitude = $store_data['Store']['logitude'];
?>

<style>
    .storeTiming p{
        display: inline-block;
        font-size: 12px;
        margin-left: 1%;
        text-transform:uppercase;
        padding-left: 5px;
    }
    
    
    /*.storeTiming label{
        width: auto;
    }*/
    
    
    .form-layout.pickup-form.bottommargin {
        margin-bottom: 43px;
    }
    .form-layout.pickup-form {
    float: right;
    padding: 8px 15px;
    }
    
</style>
<div class="pad-TP60 clearfix">
    <section class="form-layout delivery-form mapForm hrzntal">
        <h2> <span>Store Location</span> </h2>
        <div id="map"></div>
    </section>
                    
    <section class="form-layout pickup-form bottommargin">
        <h2> <span>Store Address</span> </h2>
        <div class="address">
            <address class="inbox">
                <h3><?php echo $store_data['Store']['store_name'];?></h3>
                <p> <?php echo $store_data['Store']['address'];?> <br> <?php echo $store_data['Store']['city'].' '.$store_data['Store']['state'].' '.$store_data['Store']['zipcode'];?> <br> <?php echo "Tel: ".$store_data['Store']['phone'];?><br>
                    <?php
                    if (!empty($store_data['Store']['display_fax'])) {
                        echo "Fax: ".$store_data['Store']['display_fax'];
                    }
                    ?>
                    <br>
                    <?php
                    if (!empty($store_data['Store']['display_email'])) {
                        echo "Email: ".$store_data['Store']['display_email'];
                    }
                    ?>
                   
                </p>
            </address>
        </div>
    </section>
    
    <?php if(isset($availabilityInfo) && !empty($availabilityInfo)) { ?>
        <section class="form-layout pickup-form bottommargin">
            <h2> <span>Open Hours</span> </h2>
            <?php
                $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');            
                foreach($days as $key =>$value){
            ?>
                    <div class="storeTiming">
                        <label><?php echo $value; ?></label>
                        <p>
                            <?php
                                if($availabilityInfo[$key]['StoreAvailability']['is_closed']==1){
                                    echo "Closed";
                                }else{
                                    echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['start_time'])." - ";
                                    if($store_data['Store']['is_break_time']==1){
                                        if($store_data['Store']['is_break1']==1){
                                            if($availabilityInfo[$key]['StoreBreak']['break1_start_time']!=$availabilityInfo[$key]['StoreBreak']['break1_end_time']){
                                            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_start_time']).",   ";
                                            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_end_time'])." - ";
                                            }
                                        }
                                        if($store_data['Store']['is_break2']==1){
                                            if($availabilityInfo[$key]['StoreBreak']['break2_start_time']!=$availabilityInfo[$key]['StoreBreak']['break2_end_time']){
                                            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_start_time']).",   ";
                                            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_end_time'])." - ";
                                            }
                                        }  
                                    }
                                    echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['end_time']);
                                }
                            ?>
                        </p>
                    </div>
             <?php } ?>
            
            
        </section>
    <?php } ?>
    
</div>

<style>
    #map{
        height: 90%;
        border:1px dotted #CCC000;
    }
</style>  
<?php if($latitude && $logitude){ ?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY;?>&sensor=false"></script>
<script>
var marker;
function initMap() {
    var myLatlng = new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $logitude; ?>);
    var mapOptions = {
      zoom: 14,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("map"), mapOptions);

    marker = new google.maps.Marker({
        position: myLatlng
    });
    // To add the marker to the map, call setMap();
    marker.setMap(map);
}

</script>
<?php } ?>
            
            
