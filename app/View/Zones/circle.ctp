<style>
    .customBox {
        background: #b3d9ff;
        padding:2px;
        position: absolute;
        font-weight:bold;
        font-color:#FFFFFF;
    }
</style>
<div id="map" style="height: 450px;"></div>
<?php echo $this->element('zone/circle_zone_list', $zones); ?>
<?php
if (count($zones) < 5) {
    if ((!empty($storeDeliverySetting['StoreSetting']['delivery_allow'])) || (count($zones) == 0)) {
        if (!empty($storeDeliverySetting['StoreSetting']['delivery_zone_type']) || count($zones) == 0) {
            echo $this->element('zone/circle_zone_add');
        }else{?>
            <div class="placeEditDiv"></div>
        <?php }
    } else {
        ?>
        <div class="placeEditDiv"></div>
        <?php
    }
} else {
    ?>
    <div class="placeEditDiv"></div>
    <?php
}
$valueStoreName = $latlong['Store']['store_name'];
if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $valueStoreName)) {
    $valueStoreName = str_replace("'s", "", $valueStoreName);
}
?>

<!--<script src="https://maps.googleapis.com/maps/api/js?&callback=initMap&signed_in=false&libraries=drawing,geometry&v=3.exp" async defer>
</script>-->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY;?>&v=3.exp&amp;sensor=false&amp;libraries=drawing,geometry"></script>
<script>
    var storeLat = <?php echo $latlong['Store']['latitude']; ?>;
    var storeLng = <?php echo $latlong['Store']['logitude']; ?>;
    function TxtOverlay(pos, txt, cls, map) {
        // Now initialize all properties.
        this.pos = pos;
        this.txt_ = txt;
        this.cls_ = cls;
        this.map_ = map;
        // We define a property to hold the image's
        // div. We'll actually create this div
        // upon receipt of the add() method so we'll
        // leave it null for now.
        this.div_ = null;
        // Explicitly call setMap() on this overlay
        this.setMap(map);
    }

    TxtOverlay.prototype = new google.maps.OverlayView();



    TxtOverlay.prototype.onAdd = function () {
        // Note: an overlay's receipt of onAdd() indicates that
        // the map's panes are now available for attaching
        // the overlay to the map via the DOM.
        // Create the DIV and set some basic attributes.
        var div = document.createElement('DIV');
        div.className = this.cls_;
        div.innerHTML = this.txt_;
        // Set the overlay's div_ property to this DIV
        this.div_ = div;
        var overlayProjection = this.getProjection();
        var position = overlayProjection.fromLatLngToDivPixel(this.pos);
        div.style.left = position.x + 'px';
        div.style.top = position.y + 'px';
        // We add an overlay to a map via one of the map's panes.

        var panes = this.getPanes();
        panes.floatPane.appendChild(div);
    }
    TxtOverlay.prototype.draw = function () {


        var overlayProjection = this.getProjection();

        // Retrieve the southwest and northeast coordinates of this overlay
        // in latlngs and convert them to pixels coordinates.
        // We'll use these coordinates to resize the DIV.
        var position = overlayProjection.fromLatLngToDivPixel(this.pos);


        var div = this.div_;
        div.style.left = position.x + 'px';
        div.style.top = position.y + 'px';



    }
    //Optional: helper methods for removing and toggling the text overlay.  
    TxtOverlay.prototype.onRemove = function () {
        this.div_.parentNode.removeChild(this.div_);
        this.div_ = null;
    }
    TxtOverlay.prototype.hide = function () {
        if (this.div_) {
            this.div_.style.visibility = "hidden";
        }
    }

    TxtOverlay.prototype.show = function () {
        if (this.div_) {
            this.div_.style.visibility = "visible";
        }
    }

    TxtOverlay.prototype.toggle = function () {
        if (this.div_) {
            if (this.div_.style.visibility == "hidden") {
                this.show();
            } else {
                this.hide();
            }
        }
    }

    TxtOverlay.prototype.toggleDOM = function () {
        if (this.getMap()) {
            this.setMap(null);
        } else {
            this.setMap(this.map_);
        }
    }
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: storeLat, lng: storeLng},
            zoom: 12
        });
<?php
if (!empty($zones)) {
    $colorArray = array('#FF0000', '#2582E2', '#F6A46B', '#F6A46D', '#2582E4');
    foreach ($zones as $key => $zone) {
        ?>
                var cityCircle = new google.maps.Circle({
                    strokeColor: "<?php echo $colorArray[$key]; ?>",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "<?php echo $colorArray[$key]; ?>",
                    fillOpacity: 0.35,
                    map: map,
                    center: {lat: storeLat, lng: storeLng},
                    radius: <?php echo $zone['Zone']['distance']; ?>,
                    editable: false,
                    draggable: false
                });
                //                var centerlatlng = map.getCenter();
                //                var customTxt = '<?php echo $zone['Zone']['name']; ?>';
                //                txt = new TxtOverlay(centerlatlng, customTxt, "customBox", map);
                google.maps.event.addListener(cityCircle, 'mouseover', function () {
                    this.getMap().getDiv().setAttribute('title', "<?php echo $zone['Zone']['name']; ?>");
                });

                google.maps.event.addListener(cityCircle, 'mouseout', function () {
                    this.getMap().getDiv().removeAttribute('title');
                });
        <?php
    }
}
?>

        markerAPosition = new google.maps.LatLng(storeLat, storeLng),
                markerA = new google.maps.Marker({
                    map: map,
                    position: markerAPosition,
                    title: '<?php echo $valueStoreName; ?>'
                });

    }
    (function () {
        initMap();
    })();
</script>
