<?php 

echo $this->Html->css('popup');
?>

<style>
    #map {
        height: 420px;
        width:600px;
    }
</style>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=drawing&extension=.js"></script>
<a href="dash">Second Solution</a>
<?php echo $this->element('zone/add_zone');?>
<div id="map"></div>

<div id="marker-position"></div>
<div id="marker-position1"></div>

<?php echo $this->element('zone/zone_list',$zones); ?>

<script>
    
    var mapOptions = {
        center: new google.maps.LatLng(<?php echo $latlong['Store']['latitude'].','.$latlong['Store']['logitude'];?>),
        zoom: 12,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById('map'),
    mapOptions);
    var marker = new google.maps.Marker({
        position: {lat: <?php echo $latlong['Store']['latitude']; ?>, lng: <?php echo $latlong['Store']['logitude']; ?>},
        title:"<?php echo $latlong['Store']['store_name']; ?>"

      });
    
    marker.setMap(map);
    var coordinates = [];
    var drawingManager = new google.maps.drawing.DrawingManager({
        drawingControlOptions: {
            position: google.maps.ControlPosition.LEFT_BOTTOM,
            drawingModes: [
            //google.maps.drawing.OverlayType.MARKER,
            //google.maps.drawing.OverlayType.CIRCLE,
            google.maps.drawing.OverlayType.POLYGON,
            //google.maps.drawing.OverlayType.POLYLINE,
            //google.maps.drawing.OverlayType.RECTANGLE

            ]
        },
            polygonOptions: {
                draggable: true,
                editable: true,
                fillColor: '#cccccc',
                fillOpacity: 0.5,
                strokeColor: '#000000',
            }
    });

    google.maps.event.addListener(drawingManager, 'markercomplete', function (marker) {
        var position = marker.getPosition().toUrlValue(2);
        $('#marker-position1').append(position + '<br>'+position1);
    });
    google.maps.event.addListener(drawingManager, 'circlecomplete', function (circle) {
       var radius = circle.getRadius();
       var latt = circle.getCenter().lat();
       var longg = circle.getCenter().lng();
       $('#marker-position').append(radius + '<br>'+latt+'<br>'+longg);
    });
    
    google.maps.event.addListener(drawingManager, 'rectanglecomplete', function (rectangle) {
       var radius = rectangle.getBounds();
       $('#marker-position').append(radius + '<br>');
    });
    
    google.maps.event.addListener(drawingManager, 'polygoncomplete', function (polygon) {
        var polygonBounds = polygon.getPath();
        var cordselected = [];
        for(var i = 0 ; i < polygonBounds.length ; i++){
            //alert(polygonBounds.getAt(i).lat()+'#'+polygonBounds.getAt(i).lng());
            cordselected.push({ 
                "lat" : polygonBounds.getAt(i).lat(),
                "long"  : polygonBounds.getAt(i).lng()
            });
            
            coordinates.push(polygonBounds.getAt(i).lat(), polygonBounds.getAt(i).lng());
        }
        var jsoncord=JSON.stringify(cordselected);
        $('#hiddencord').val(jsoncord);
        $('#deliveryzone').modal('show');

    });
    
    google.maps.event.addListener(drawingManager, 'polylinecomplete', function (line ) {
        var polygonBounds = line .getPath();
        for(var i = 0 ; i < polygonBounds.length ; i++){
            coordinates.push(polygonBounds.getAt(i).lat(), polygonBounds.getAt(i).lng());
            //alert(JSON.stringify(coordinates, null, 6));
        }
    });    
    drawingManager.setMap(map); 
    
    
    
    var triangleCoords = [
        {lat: 25.774, lng: -80.190},
        {lat: 18.466, lng: -66.118},
        {lat: 32.321, lng: -64.757},
        {lat: 25.774, lng: -80.190}
    ];
    // Construct the polygon.
    var bermudaTriangle = new google.maps.Polygon({
        paths: triangleCoords,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35  });
    bermudaTriangle.setMap(map);
    
    
</script>



