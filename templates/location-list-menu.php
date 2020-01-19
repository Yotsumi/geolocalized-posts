<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.1/css/ol.css" type="text/css">
    <style>
      .map {
        height: 400px;
        width: 100%;
      }
      .mapHover:hover {
        cursor: url('http://maps.google.com/mapfiles/ms/micons/blue.png') 16 16, auto;
      }
    </style>
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.1/build/ol.js"></script>
    <link href="https://unpkg.com/ol-geocoder/dist/ol-geocoder.min.css" rel="stylesheet">
    <script src="https://unpkg.com/ol-geocoder"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <title>OpenLayers example</title>
  </head>
  <body>

    <div class="form-group">
      <label for="sel1">Points of Interest: </label>
      <select class="form-control" id="sel1">
        <?php foreach($locationList as $location): ?>
          <option value="<?= json_encode($location->coordinates) ?>"><?= $location->name ?></option>
        <?php endforeach; ?>
      </select>
    </div> 

    <button type="button" onclick="enableAdd()">Add</button>
    <button type="button" onclick="deletePoi()">Delete</button>

    <h2>My Map</h2>
    <div id="map" class="map"></div>
    <script type="text/javascript">

  let enabledAdd = false;
  function enableAdd(){
    enabledAdd = enabledAdd ? false : true;
    if (enabledAdd)
      jQuery('#map').addClass('mapHover');
    else
      jQuery('#map').removeClass('mapHover');
    
  }

  function deletePoi(){
    LonLan = JSON.parse(jQuery('#sel1').val());
    var data = {
        'action': 'delete_poi',
        'lat': LonLan[1],
        'lon': LonLan[0]
      };
    jQuery.post(ajaxurl, data, function(response) {}).done(()=>{ location.reload() }).fail(()=>{alert('Database error')});
  }

  var Turin = ol.proj.fromLonLat([7.667129335409262, 45.07799857283038]);
  var view = new ol.View({
    center: Turin,
    zoom: 15 // 5
  });

  var vectorSource = new ol.source.Vector({});
  var iconStyle = new ol.style.Style({
      image: new ol.style.Icon({
        anchor: [0.5, 0.5],
        anchorXUnits: 'fraction',
        anchorYUnits: 'fraction',
        src: 'http://maps.google.com/mapfiles/ms/micons/blue.png',
        crossOrigin: 'anonymous',
      })
    });
  var places = [];
  <?php foreach($locationList as $location): ?>
    places.push(<?= json_encode($location->coordinates) ?>)
  <?php endforeach; ?>

  var features = [];
  for (var i = 0; i < places.length; i++) {
    var iconFeature = new ol.Feature({
      geometry: new ol.geom.Point(ol.proj.transform([places[i][0], places[i][1]], 'EPSG:4326', 'EPSG:3857')),
    });
    
    iconFeature.setStyle(iconStyle);
    vectorSource.addFeature(iconFeature);
  }

  var vectorLayer = new ol.layer.Vector({
    source: vectorSource,
    updateWhileAnimating: true,
    updateWhileInteracting: true,
  });

  var map = new ol.Map({
    target: 'map',
    view: view,
    layers: [
      new ol.layer.Tile({
        preload: 3,
        source: new ol.source.OSM(),
      }),
      vectorLayer,
    ],
    loadTilesWhileAnimating: true,
  });

  //Instantiate with some options and add the Control
  var geocoder = new Geocoder('nominatim', {
    provider: 'osm',
    lang: 'en',
    placeholder: 'Search for ...',
    limit: 5,
    debug: false,
    autoComplete: true,
    keepOpen: true
  });
  map.addControl(geocoder);

  map.on('singleclick', function (evt) {
    if (!enabledAdd) return;
      swal({
        text: 'Insert tag name',
        content: "input",
      }).then(value => {
        if (value == undefined || value == '') return;
        enableAdd()
        // convert coordinate to EPSG-4326
        let coordinates = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326')
        let feature = new ol.Feature({
          geometry: new ol.geom.Point(ol.proj.fromLonLat(coordinates))
        })
        feature.setStyle(iconStyle)
        vectorSource.addFeature(feature)
        var data = {
          'action': 'set_poi',
          'lat': coordinates[1],
          'lon': coordinates[0],
          'tagName': value
        };

        jQuery.post(ajaxurl, data, function(response) {}).fail(()=>{alert('Database error')});
      });
  });

  jQuery('#sel1').on('change', (selected) => {
    let coord = JSON.parse(jQuery( "#sel1 option:selected" ).val());
    map.getView().animate({
      center: ol.proj.fromLonLat(coord),
      zoom: 15,
      duration: 2000
    });
  })
      
    </script>
  </body>
</html>