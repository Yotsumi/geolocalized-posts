<?php ob_start(); ?>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.1/css/ol.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.1/build/ol.js"></script>
    <link href="https://unpkg.com/ol-geocoder/dist/ol-geocoder.min.css" rel="stylesheet">
    <script src="https://unpkg.com/ol-geocoder"></script>
    <style>
    .ol-control button { 
        background-color: rgba(40, 40, 40, 0.8) !important;
        font-size: 0.95em !important;
    }
    .ol-control button:hover { 
        background-color: rgba(40, 40, 40, 1) !important;
    }
    .popup {
        border-radius: 5px;
        border: 1px solid grey;
        background-color: rgba(255, 255, 255, 0.9);
        padding: 2px;
    }
    </style>
</head>

<div id="MAPPA" style="width: 1200px; height:400px"></div>
<div id="popup" class="popup"></div>
<script type="text/javascript">
    let url = window.location.href;
    function getParameterByName(name) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }
    function nearMe(){
        var geolocation = new ol.Geolocation({
            trackingOptions: {
                enableHighAccuracy: true
            }
        });
        geolocation.on('change:position', function() {
            var coordinates = geolocation.getPosition();
            if (geolocation.getAccuracy() > 250)
                alert('Attenzione, la geolocalizzazione è molto inaccurata: ' + geolocation.getAccuracy() + 'm di errore');
            window.location = location.protocol + '//' + location.host + location.pathname + '?sort=near&lon=' + coordinates[0] + '&lat=' + coordinates[1];
        });
        geolocation.on('error', function(error) {
            console.log('geotracking error: ' + error.message)
            geolocation.setTracking(false);
        });
        geolocation.setTracking(true);
    }
    history.replaceState(null, "", window.location.pathname);
    setTimeout(()=>{
        var Turin = ol.proj.fromLonLat([7.667129335409262, 45.07799857283038]);
        var view = new ol.View({
            center: Turin,
            zoom: 15
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

        var theNewCenter = [getParameterByName('lon'), getParameterByName('lat')]
        if(theNewCenter[0]){
            view = new ol.View({
                center: ol.proj.fromLonLat(theNewCenter),
                zoom: 15
            });
            let feature = new ol.Feature({
                geometry: new ol.geom.Point(ol.proj.fromLonLat(theNewCenter)),
                locationName: 'Tu sei qui'
            })
            let newStyle = new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 6,
                    fill: new ol.style.Fill({
                    color: '#3399CC'
                    }),
                    stroke: new ol.style.Stroke({
                    color: '#fff',
                    width: 2
                    })
                })
            });
            feature.setStyle(newStyle)
            vectorSource.addFeature(feature)
        }

        var places = [];
        <?php foreach($locationList as $location): ?>
            geoLoc = {
                coordinates: <?= json_encode($location->coordinates) ?>,
                name: <?= json_encode($location->name) ?>
                }
            places.push(geoLoc)
        <?php endforeach; ?>

        var features = [];
        let locName = getParameterByName('locName')
        for (var i = 0; i < places.length; i++) {
            if (locName)
                if (locName == places[i].name){
                    view = new ol.View({
                        center: ol.proj.fromLonLat(places[i].coordinates),
                        zoom: 15
                    });
                }
            var iconFeature = new ol.Feature({
                geometry: new ol.geom.Point(ol.proj.transform([places[i].coordinates[0], places[i].coordinates[1]], 'EPSG:4326', 'EPSG:3857')),
                locationName: places[i].name
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
            target: 'MAPPA',
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

        popupOverlay = new ol.Overlay({
            element: popup,
            offset: [10, 10]
        });
        map.addOverlay(popupOverlay);

        var selectHover = new ol.interaction.Select({
            style: iconStyle,
            condition: ol.events.condition.pointerMove
        });
        var selectClick = new ol.interaction.Select({
            style: iconStyle
        });
        map.addInteraction(selectHover);
        map.addInteraction(selectClick);
        selectHover.on('select', function(e) {
            let selectedFeature = e.selected[0];
            let element = popupOverlay.values_.element;
            if (!selectedFeature){
                element.hidden = true;
                document.body.style.cursor = ''
                return;
            }
            if (selectedFeature.values_.locationName != 'Tu sei qui')
                document.body.style.cursor = 'pointer'
            element.hidden = false;
            let coordinatePopup = selectedFeature.values_.geometry.flatCoordinates
            element.innerHTML = '<div>'+selectedFeature.values_.locationName+'</div>'
            popupOverlay.setPosition(coordinatePopup);
        });
        selectClick.on('select', function(e){
            let locationName = e.selected[0].values_.locationName
            if (locationName == 'Tu sei qui') return;
            window.location = location.protocol + '//' + location.host + location.pathname + '?sort=locationName&locName=' + locationName
        })

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

    }, 350)
    
</script>
<?php return ob_get_clean(); ?>