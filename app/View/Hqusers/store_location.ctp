<?php
$latitude = $store_data['Store']['latitude'];
$logitude = $store_data['Store']['logitude'];
?>
<style>
    .storeTiming p{
        display: inline;
        font-size: 12px;
        margin-left: 1%;
    }

    .storeTiming label{
        width: auto;
    }

    .mapForm{
        height: 593px;
    }
    .form-layout.pickup-form.bottommargin {
        margin-bottom: 30px;
    } 
     section {
    background-color: #FFFFFF;
}
</style>
<div class="content clearfix">
    <div class="wrap">
        <section class="form-layout delivery-form mapForm hrzntal">
            <h2> <span>Store Location</span> </h2>
            <div id="map"></div>
        </section>

        <section class="form-layout pickup-form bottommargin">
            <h2> <span>Store Address</span> </h2>
            <div class="address">
                <address class="inbox">
                    <h3><?php echo $store_data['Store']['store_name']; ?></h3>
                    <p> <?php echo $store_data['Store']['address']; ?> <br> <?php echo $store_data['Store']['city'] . ' ' . $store_data['Store']['state'] . ' ' . $store_data['Store']['zipcode']; ?> <br> <?php echo $store_data['Store']['phone']; ?></p>
                </address>
            </div>
        </section>
        <?php echo $this->Html->link('Back', array('controller' => 'hqusers', 'action' => 'location', $store_data['Store']['city']), array('class' => 'btn btn-default pull-right bg-btn-color')); ?>
    </div>
</div>
<style>
    #map{
        height: 90%;
        border:1px dotted #CCC000;
    }
</style>  
<?php
$address = $store_data['Store']['address'] . '+' . $store_data['Store']['city'] . '+' . $store_data['Store']['state'] . '+' . $store_data['Store']['zipcode'];
$address = str_replace(' ', '+', $address);
?>
<?php if ($latitude && $logitude) { ?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>
        var origin1 = new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $logitude; ?>);

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: origin1,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $logitude; ?>),
            map: map,
            //draggable: true,
            icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=S|f6f6f6',
        });
    </script>    
<?php } elseif (!empty($address)) { ?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>
        function initialize() {
            geocoder = new google.maps.Geocoder();
            var mapCanvas = document.getElementById('map');
            var myLatlng;
            var mapOptions = {
                center: myLatlng,
                zoom: 14,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            var map = new google.maps.Map(mapCanvas, mapOptions);
            var address = "<?php echo $address; ?>";
            geocoder.geocode({
                "address": address
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);

                    var infowindow = new google.maps.InfoWindow({
                        content: address
                    });

                    var marker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        title: address,
                        icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=S|f6f6f6'
                    });
                    google.maps.event.addListener(marker, 'click', function () {
                        infowindow.open(map, marker);
                    });
                }
                else {
                    $('#map').html('No map found for that address.');
                    //alert('Geocode was not successful for the following reason: ' + status);
                }
                var myLatlng = results[0].geometry.location;
            });

        }
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
<?php } ?>


