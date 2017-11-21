<?php //pr($zones);              ?>
<p id="map-canvas" style="height: 450px;"></p>  


<?php echo $this->element('zone/zone_list', $zones); ?>
<?php
//if (count($zones) < 5) {
//    echo $this->element('zone/dash_zone_add');
//} else {
?>
<div class="placeEditDiv"></div>
<?php //} ?>


<style>
    img { max-width:100%; }
    #map-canvas img { max-width:none; }
    .gm-style-iw input {
        margin-bottom: 9px;
        width: 177px;
    }
    .polybtn {
        margin-left: 74px;
    }
</style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY;?>&v=3.exp&amp;sensor=false&amp;libraries=drawing,geometry"></script>


<script>
    function addzone() {
        var zname = $("#zname").val();
        var zfee = $("#zfee").val();
        var zcoords = $("#zcoords").val();
        if (zname == '') {
            $("#zname").after('<br/><span id="zname-error" class="error" style="display: inline;">This field is required.</span>');
            return false;
        }
        if (zfee == '') {
            $("#zfee").after('<br/><span id="zfee-error" class="error" style="display: inline;">This field is required.</span>');
            return false;
        }
        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'zones', 'action' => 'add_zone')); ?>",
            type: "Post",
            async: false,
            data: {zname: zname, zfee: zfee, zcoords: zcoords},
            success: function () {
                myField.setOptions({
                    editable: false,
                    draggable: false
                });
                myInfoWindow.close();
            }
        });
        location.reload();
    }

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

</script>





<script>

    /*
     This Javascript file allows creation of polygon on Google Maps using API v3
     I have used this script in <body></body> of an HTML page, but you could use
     it in the <head></head> for better effects. Should you desire to do that, 
     scroll way at the bottom of the page and change the anonymous function 
     */

    // declare variables that will be used in this example
    var myMap;                  // holds the map object drawn on the 
    var myDrawingManager;       // holds drawing tools
    var myField;                // holds the polygon we draw using drawing tools
    var myInfoWindow;           // when our polygon is clicked, a dialog box 
    // will open up. This variable holds that info
    var centerpoint;            // center point of the map
    var coordinates = [];
    var dbcoord = [];
    /**
     * Initialization function that sets up the map
     */
    function initialize() {
        // build the map's center poiint
        centerpoint = new google.maps.LatLng(<?php echo $latlong['Store']['latitude'] . ',' . $latlong['Store']['logitude']; ?>);

        // assign map the options of zoom, center point and set the map to
        // SATELLITE
        var mapOptions = {
            zoom: 12,
            center: centerpoint,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        // on our web page should be a <div> or <p> tag with id map-canvas
        // show the map in that element with the options listed above
        myMap = new google.maps.Map(
                document.getElementById('map-canvas'),
                mapOptions
                );


<?php
foreach ($zones as $key => $zonec) {
    ?>
            dbcoord[<?php echo $key; ?>] = new google.maps.Polygon({
                paths: [
    <?php
    foreach ($zonec['ZoneCoordinate'] as $zkey => $value) {
        ?>
                        new google.maps.LatLng(<?php echo $value['lat']; ?>, <?php echo $value['long']; ?>),
        <?php
    }
    ?>
                ],
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#FF0000",
                fillOpacity: 0.3});
    <?php
}
?>



<?php
foreach ($zones as $key => $zonec) {
    ?>
            var bounds = new google.maps.LatLngBounds();
    <?php
    foreach ($zonec['ZoneCoordinate'] as $zkey => $value) {
        ?>

                bounds.extend(new google.maps.LatLng(<?php echo $value['lat']; ?>, <?php echo $value['long']; ?>));
        <?php
    }
    ?>
            var centerlatlng = bounds.getCenter();
            //alert(centerlatlng);
            var customTxt = '<?php echo $zonec['Zone']['name']; ?>';
            txt = new TxtOverlay(centerlatlng, customTxt, "customBox", myMap);
            dbcoord[<?php echo $key; ?>].setMap(myMap);
    <?php
}
?>


        var marker = new google.maps.Marker({
            position: {lat: <?php echo $latlong['Store']['latitude']; ?>, lng: <?php echo $latlong['Store']['logitude']; ?>},
            title: "<?php echo $latlong['Store']['store_name']; ?>"

        });




        marker.setMap(myMap);
        // create a dialog box but don't bind it to anything yet
        myInfoWindow = new google.maps.InfoWindow();

        // show drawing tools
<?php
if (!empty($storeDeliverySetting['StoreSetting']['delivery_allow']) || count($zones) == 0) {
    if (!empty($storeDeliverySetting['StoreSetting']['delivery_zone_type']) || (count($zones) == 0)) {
        ?>
                DrawingTools();
        <?php
    }
}
?>

    }


    /**
     * Show drawing tools
     */
    function DrawingTools() {

        // drawingMode of NULL, which means that the map drawing tools will
        // have no default drawing tool selected. If drawingMode was set to 
        // google.maps.drawing.OverlayType.POLYGON, polygon would be auto-
        // selected
        // drawingModes can have multiple information. Over here only the
        // polygon capability is added along with the default of hand icon
        // Moreover, polygonOptions are specified as defaults
        myDrawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: null,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_RIGHT,
                drawingModes: [
                    google.maps.drawing.OverlayType.POLYGON
                ]
            },
            polygonOptions: {
                draggable: true,
                editable: true,
                fillColor: '#cccccc',
                fillOpacity: 0.5,
                strokeColor: '#000000'
            }
        });
        myDrawingManager.setMap(myMap);

        // when polygon drawing is complete, an event is raised by the map
        // this function will listen to the event and work appropriately
        FieldDrawingCompletionListener();
    }

    /**
     * Using the drawing tools, when a polygon is drawn an event is raised. 
     * This function catches that event and hides the drawing tool. It also
     * makes the polygon non-draggable and non-editable. It adds custom 
     * properties to the polygon and generates a listener to listen to click
     * events on the created polygon
     */
    function FieldDrawingCompletionListener() {
        // capture the field, set selector back to hand, remove drawing
        google.maps.event.addListener(
                myDrawingManager,
                'polygoncomplete',
                function (polygon) {
                    myField = polygon;
                    ShowDrawingTools(true);
//                PolygonEditable(true);
//                AddPropertyToField();
                    FieldClickListener();
                }
        );
    }

    /**
     * Show or hide drawing tools
     */
    function ShowDrawingTools(val) {
        myDrawingManager.setOptions({
            drawingMode: null,
            drawingControl: val
        });
    }

    /**
     * Allow or disallow polygon to be editable and draggable 
     */
    function PolygonEditable(val) {
        alert();
        myField.setOptions({
            editable: val,
            draggable: val
        });
        myInfoWindow.close();
        return false;
    }

    /**
     * Add custom property to the polygon
     */
    function AddPropertyToField() {
        var obj = {
            'id': 5,
            'grower': 'Joe',
            'farm': 'Dream Farm'
        };
        myField.objInfo = obj;
    }

    /**
     * Attach an event listener to the polygon. When a user clicks on the 
     * polygon, get a formatted message that contains links to re-edit the 
     * polygon, mark the polygon as complete, or delete the polygon. The message
     * appears as a dialog box
     */
    function FieldClickListener() {
        google.maps.event.addListener(
                myField,
                'click',
                function (event) {
                    var message = GetMessage(myField);
                    myInfoWindow.setOptions({content: message});
                    myInfoWindow.setPosition(event.latLng);
                    myInfoWindow.open(myMap);
                }
        );
    }

    /**
     * Delete the polygon and show the drawing tools so that new polygon can be
     * created
     */
    function DeleteField() {
        myInfoWindow.close();
        myField.setMap(null);
        ShowDrawingTools(true);
    }

    /**
     * Get coordinates of the polygon and display information that should 
     * appear in the polygon's dialog box when it is clicked
     */

    function GetMessage(polygon) {
        //var coordinates = polygon.getPath().getArray();

        var polygonBounds = polygon.getPath();
        var cordselected = [];
        for (var i = 0; i < polygonBounds.length; i++) {
            //alert(polygonBounds.getAt(i).lat()+'#'+polygonBounds.getAt(i).lng());
            cordselected.push({
                "lat": polygonBounds.getAt(i).lat(),
                "long": polygonBounds.getAt(i).lng()
            });

            coordinates.push(polygonBounds.getAt(i).lat(), polygonBounds.getAt(i).lng());
        }
        var jsoncord = JSON.stringify(cordselected);


        var message = '';

        if (typeof myField != 'undefined') {
            message += '<h3 style="color:#000">Delivery Zone</h3>';
        }

        var coordinateMessage = '<form id="form-id"><table><tr><td><label>Zone name :- </label></td><td><input type="text" name="name" placeholder="Enter Delivery Zone Name" id="zname" "required"/></td><tr><td><label>Fee :-</label></td><td><input type="text" name="fee" placeholder="Enter Fee" id="zfee" "required"/></td></tr>';
        coordinateMessage += "<input type='hidden' name='coords' value='" + jsoncord + "' id='zcoords'/><table>";

        message += coordinateMessage + '<br><div class="polybtn"> ' + '<a href="#" onclick="addzone();" id="addzone" class="btn btn-primary">Done</a> '
                + '<a href="#" onclick="DeleteField(myField)" class="btn btn-primary">Delete</a></div></form>';

        return message;
    }


    /**
     * Get area of the drawn polygon
     */
    function GetArea(poly) {
        var result = parseFloat(google.maps.geometry.spherical.computeArea(poly.getPath())) * 0.000247105;
        return result.toFixed(4);
    }






    // if this script is invoked from the <body>, invoke the initialize 
    // function now
    (function () {
        initialize();
    })();

    // use the code below if you are using this script in <head></head> tag
    // google.maps.event.addDomListener(window, 'load', initialize);



</script>




<style>
    .customBox {
        background: #b3d9ff;
        padding:2px;
        /*      border: 1px solid black;*/
        position: absolute;
        font-weight:bold;
        font-color:#FFFFFF;
    }
</style>
