<?php
$latitude = $store_data['Store']['latitude'];
$logitude = $store_data['Store']['logitude'];
if (DESIGN == 1) {
    echo $this->element('design/aaron/location');
} elseif (DESIGN == 2) {
    echo $this->element('design/chloe/location');
} elseif (DESIGN == 3) {
    echo $this->element('design/dasol/location');
} else if (DESIGN == 4) {
    echo $this->element('design/oldlayout/innerpage/store_location');
}
?>

<?php if ($latitude && $logitude) { ?>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1tbIAqN0XqcgTR1-FxYoVTVq6Is6lD98&callback=initMap">
    </script>
    <script>
        var marker;
        function initMap() {
            var myLatlng = new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $logitude; ?>);
            var mapOptions = {
                zoom: 14,
                scrollwheel: false,
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


<?php
echo $this->Html->css('remodal');
echo $this->Html->css('remodal-default-theme');
echo $this->Html->script('remodal.min');
echo $this->Html->script('js.cookie');
    $store_data=$this->Common->getStoreDetail($this->Session->read('store_id'));
    $popupMessage = '';
    switch($store_data['Store']['store_url']) {
        case "pokeocncafe.com" :
            $popupHeader = '<strong>*** Poké Served from 10:30am ***</strong>';
            $popupMessage = '
                When you make an order, please choose your pickup time
                </br>
                </br>• between 10:30am and 6:00pm (Mon.~Fri.)
                </br>• between 10:30am and 2:00pm (Sat.)
                </br></br>
                Thank you!</br>
                Poké OC
                </br>
           ';
    }
    if($popupMessage) { ?>

        <div data-remodal-id="modal2" role="dialog" aria-labelledby="modal2Title" aria-describedby="modal2Desc" >
          <div>
            <h2 id="modal2Title"><?=$popupHeader?></h2></br><hr></br>
            <p id="modal2pDesc">
              <?=$popupMessage?>
            </p>
          </div>
          <br>
           <hr></br>
            <button data-remodal-action="cancel" class="remodal-cancel">Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>

        </div>
        <script>
            var result = Cookies.get('store-popup-<?=$store_data['Store']['store_url']?>');
            if(result != 'end' ) {
                $("[data-remodal-id=modal2]").remodal({ hashTracking: false });
                $("[data-remodal-id=modal2]").remodal().open();
                $("#is_popup_confirm").val("true");
                $(document).on('cancellation', '.remodal', function () {
                    Cookies.set('store-popup-<?=$store_data['Store']['store_url']?>', 'end');
                    $("#is_popup_confirm").val("true");
                });
            }
        </script>
<?php }?>