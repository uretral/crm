sdasdas
<link rel="stylesheet" href="/js/map/osm.css">
<script src="/js/map/jq.js"></script>
<script src="/js/map/OpenLayers.js"></script>
<script src="/js/map/osm.js"></script>
<script src="/js/map/Yours.js"></script>
<script src="/js/map/ynav.js"></script>

<div id="map"></div>

<script>
    var lat=47.7;
    var lon=7.5;
    var zoom=10;
    // create a map and draw a simple route
    var MyFirstMap = new OpenLayers.Map('map');
    var ol_wms = new OpenLayers.Layer.OSM("Local Tiles", "tiles/${z}/${x}/${y}.png", {numZoomLevels: 19, alpha: true, isBaseLayer: false});
    MyFirstMap.addLayers([ol_wms]);
    MyFirstMap.addControl(new OpenLayers.Control.LayerSwitcher());

    // MyFirstMap.zoomToMaxExtent();
    MyFirstRoute = new Yours.Route(MyFirstMap);
    var flat=51.158883504779;
    var flon=3.220214843821;
    var tlat=51.241492039675;
    var tlon=4.472656250021;
    MyFirstRoute.waypoint("from").lonlat = new OpenLayers.LonLat(flon,flat);
    MyFirstRoute.waypoint("to").lonlat = new OpenLayers.LonLat(tlon,tlat);
    MyFirstRoute.draw()
    // console.log();
    // MyFirstRoute.draw(MyCallBack);
    //
    // //display the response from the route request:
    // function MyCallBack(response) {
    //     console.log(response);
    // }
</script>
