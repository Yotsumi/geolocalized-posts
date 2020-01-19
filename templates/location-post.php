<?php ob_start(); ?>
    <script type="text/javascript">
    // if (window.PoiList == undefined){
    //     window.PoiList = [];
    // }
        function addCss(fileName) {
            var ss = document.styleSheets;
            for (var i = 0, max = ss.length; i < max; i++) {
                if (ss[i].href == fileName)
                    return;
            }
            var head = document.head;
            var link = document.createElement("link");

            link.type = "text/css";
            link.rel = "stylesheet";
            link.href = fileName;

            head.appendChild(link);
        }

        function addCDN(fileName, integrity, crossorigin) {
            var ss = document.scripts;
            for (var i = 0, max = ss.length; i < max; i++) {
                if (ss[i].src == fileName)
                    return;
            }
            var head = document.head;
            var link = document.createElement('script');

            link.src = fileName;

            if (integrity)
                link.integrity = integrity;
            if (crossorigin)
                link.crossOrigin = crossorigin;
            head.appendChild(link);
        }
        addCss('https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.1/css/ol.css')
        addCDN('https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.1.1/build/ol.js')
        addCDN('https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL')
        //window.PoiList.push({idPost: <?= $id ?>, lon: <?=$locationLon?>, lat: <?=$locationLat?>} );
        if (document.getElementById('map<?= $id ?>') == null){
            setTimeout(()=>{
                document.getElementById('map<?= $id ?>').style = "min-width: 200px; width: 20%; min-height:100px; height:150px";
                var attribution = new ol.control.Attribution({
                    collapsible: false
                });
                var Center = ol.proj.fromLonLat([<?=$locationLon?>, <?=$locationLat?>]);
                var view = new ol.View({
                    center: Center,
                    zoom: 12
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
                places.push([<?=$locationLon?>, <?=$locationLat?>]);

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
                    layers: [
                        new ol.layer.Tile({
                        preload: 3,
                        source: new ol.source.OSM(),
                        }),
                        vectorLayer,
                    ],
                    loadTilesWhileAnimating: true,
                    controls: ol.control.defaults({attribution: false}).extend([attribution]),
                    target: 'map<?= $id ?>',
                    view: view
                });
                document.getElementById('map<?= $id ?>').data = vectorSource;

                attribution.setCollapsible(true);
                attribution.setCollapsed(true);
            
            }, 300)
        }
        else {
            setTimeout(()=>{
                var iconStyle = new ol.style.Style({
                    image: new ol.style.Icon({
                    anchor: [0.5, 0.5],
                    anchorXUnits: 'fraction',
                    anchorYUnits: 'fraction',
                    src: 'http://maps.google.com/mapfiles/ms/micons/blue.png',
                    crossOrigin: 'anonymous',
                    })
                });
                let feature = new ol.Feature({
                    geometry: new ol.geom.Point(ol.proj.fromLonLat([<?=$locationLon?>, <?=$locationLat?>]))
                })
                feature.setStyle(iconStyle)
                
                let vectorSource = document.getElementById('map<?= $id ?>').data;
                vectorSource.addFeature(feature)
            }, 350);
        }
    </script>

    <div id="map<?= $id ?>" class="map"></div>
    <div><?= $locationName ?></div>

<?php return ob_get_clean(); ?>