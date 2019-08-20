<template>
    <div>

        <div class="mod static">

            <div class="mod-header">

                <span>{{date}}</span>
                <a v-if="newOrder.step" class="btn btn-success btn-sm" href="javascript:;" @click="setMaster">
                    <span>Назначить</span>
                </a>
            </div>
            <div class="mod-body">
                <div class="logistic">
                    <div class="logistic-lost">
                        <div class="logistic-lost-item">
                            <h4>Без адреса</h4>
                            <div class="logistic-lost-item-list">
                                <a class="badge warn" href="javascript:;">
                                    <b>34567</b>
                                </a>
                                <a class="badge warn" href="javascript:;">
                                    <b>34567</b>
                                </a>
                            </div>
                        </div>
                        <div class="logistic-lost-item">
                            <h4>Без мастера</h4>
                            <div class="logistic-lost-item-list">
                                <a class="badge warn" href="javascript:;">
                                    <b>34567</b>
                                </a>
                                <a class="badge warn" href="javascript:;">
                                    <b>34567</b>
                                </a>
                            </div>
                        </div>


                        <!--                        <div
                                                    class="form-check"
                                                    v-for="layer in layers"
                                                    :key="layer.id"
                                                >
                                                    <label class="form-check-label">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            v-model="layer.active"
                                                            @change="layerChanged(layer.id, layer.active)"
                                                        />
                                                        {{ layer.name }}
                                                    </label>
                                                </div>-->

                    </div>



                    <div class="logistic-map map" id="map" ref="mapElement"></div>

                    <div class="logistic-masters">

                        <div class="logistic-masters-table" v-for="(master,masterID) in res.schedule">
                            <!--Master and orders-->
                            <a href="javascript:"
                               @click="personalOrders(masterID)"
                               @dblclick="personalOrdersHide(masterID)"
                            ><span>{{master.name}} ({{masterID}})</span></a>
                            <div class="logistic-masters-cells" >
                                <!--simple hours-->
                                <label
                                    v-for="hour in res.hours"
                                    class="cell drag-nest"
                                    v-bind:id="masterID+'_'+hour"

                                    @dragenter="dragEnter()"
                                    @drop="dragDrop(masterID,hour)"
                                    @dragover="dragOver()"
                                    @dragleave="dragLeave()"
                                    @click="makeOrder(hour,masterID)">

                                    {{hour}}


                                    <template v-if="master.volumes.length">
                                        <!--orders-->
                                        <template v-for="volume in master.volumes" v-if="volume.start == hour">
											<span

                                                v-bind:id="volume.id"
                                                v-bind:style="markBusy(volume.length)"
                                                class="drag"
                                                @dragover="dragCancel()"
                                                @dragstart="dragStart(volume.id,volume.length)"
                                                draggable="true"
                                                @drop="false"
                                                @click="stopClick(masterID)">


										</span>
                                        </template>
                                    </template>
                                    <template v-if="newOrder.master === masterID  && newOrder.start === hour">
                                        <em
                                            class="new-order"
                                            @click="stopClick()"
                                            @dblclick="newOrderMarkRemove()"
                                        ></em>
                                    </template>
                                </label>

                            </div>

                        </div>


                    </div>

                </div>
            </div>


        </div>
    </div>
</template>

<script>
    import Axios from 'axios';
    import L from '../../leafLet';
    import jQ from 'jquery';
    export default {
        name: 'LogisticRoutesMap',
        props: ['date'],
        data: function () {
            return {
                res: '',
                open: false, //  true
                city: 'Москва',
                region: 'Москва',
                newOrder: {
                    ready: false,
                    start: '',
                    startTime: '',
                    end: '',
                    endTime: '',
                    master: '',
                    step: false,
                    date: this.date
                },
                showParagraph: false,
                paragraphID: '',
                map: null,
                tileLayer: null,
                mapData: {
                    tileLayer: null,
                    showParagraph: false,
                    paragraphID: '',
                    zoom: 10,
                    lat: '55.77321337302965',
                    lon: '37.50043094158173',
                    // lon: document.getElementsByName('center_lon')[0].value,
                    // lat: document.getElementsByName('center_lat')[0].value,
                    // url: 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}.png',
                    url:'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
                    markerShow: true,
                    currentMaster: '',
                    mapTrigger: false,
                    mainIcon: L.icon({
                        iconUrl: '/img/logo.jpg',
                        iconSize: [32, 32],
                        iconAnchor: [32, 32],
                        popupAnchor: [0, -50],
                        // shadowUrl: 'my-icon-shadow.png',
                        // shadowSize: [68, 95],
                        // shadowAnchor: [22, 94]
                    })
                },
                markers: {},
                colors: ['#002147','#6E6EF9','#FF7F00','#0000FF','#7FFF00','#850101','#000000','#F88379','#008000','#808000','#FF0000','#00009C','#FF00FF','#FFFF00','#7F00FF','#FF007F']
            }
        },

        methods: {
            init: function (e, el) {
                this.mapData.lat = '55.77321337302965';
                this.mapData.lon = '37.50043094158173';
                Axios.get('/logistic/map/new?date=' + this.date + '&lat=' + this.mapData.lat + '&action=masters')
                    .then(response => {
                        this.res = response.data;
                        this.initOSM();
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                this.open = true;
            },
            initOSM(){
                this.initMap();
                this.initLayers();
                this.initRoutes();
            },
            onClickOutside(e, el) {
                console.log('onClickOutside');
                console.log('click heard outside element: ', el);
                // console.log('element clicked: '. e.target.te);
                console.log('event: ', e);
            },
            openClose() {
                return this.open ? 'opened' : 'closed';
            },

            markBusy: function (len) {
                return 'width:' + (len * 17) + 'px';
            },

            // DRAG
            dragStart(orderID, orderLength) {
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData("Text", event.target.getAttribute('id'));
                event.dataTransfer.setData("Order", orderID);
                event.dataTransfer.setData("Length", orderLength);
                event.dataTransfer.setDragImage(event.target, 0, 0);
                return true;
            },
            dragEnter() {
                event.preventDefault();



            },
            dragOver() {
                event.preventDefault();
                let elem = event.target;
                if (!elem.classList.contains('drag'))
                    elem.classList.add('drag-over');
                return true;
            },
            dragLeave(){
                let elem = event.target;
                elem.classList.remove('drag-over');
            },
            dragCancel() {
                return event.target.className === 'drag';
            },
            dragDrop(master, hour) {
                if (!event.target.classList.contains('drag')) {

                    if (confirm('Подтверждаете изменения?')) {
                        let id = event.dataTransfer.getData("Text");
                        let order = event.dataTransfer.getData("Order");
                        let orderLength = event.dataTransfer.getData("Length");
                        let data = {
                            action: 'master',
                            date: this.date,
                            order: order,
                            start: hour,
                            duration: orderLength,
                            master: master
                        };

                        Axios.post('/logistic/map/update', data)
                            .then(response => {
                                console.log(response.data);
                                return response.data;
                            })
                            .catch(error => {
                                return error
                            });
                        this.mapTrigger = !this.mapTrigger;
                        event.target.appendChild(document.getElementById(id));
                        event.target.classList.remove('drag-over');



                        event.stopPropagation();
                        return false;
                    } else {
                        event.target.classList.remove('drag-over');
                        return false;
                    }
                } else {
                    return false;
                }

            },

            // ORDERS
            stopClick(oK) {
                this.paragraphID = oK;
                this.showParagraph = !this.showParagraph;
                event.stopPropagation();
            },
            makeOrder: function (hour, master) {
                if (this.newOrder.step) {
                    this.newOrder.end = hour;
                    this.newOrder.endTime = this.date + ' ' + hour + ':00:00';
                    let len = hour - this.newOrder.start + 1;
                    document.querySelector('.new-order').style.width = len * 17 + 'px';
                } else {
                    this.newOrder.start = hour;
                    this.newOrder.startTime = this.date + ' ' + hour + ':00:00';
                    this.newOrder.endTime = this.date + ' ' + hour + ':00:00';
                    this.newOrder.master = master;
                    this.newOrder.masterName = this.res.schedule[master].name;
                    this.newOrder.step = true;
                }
            },

            newOrderMarkRemove: function () {
                this.newOrder = {
                    start: '',
                    startTime: '',
                    end: '',
                    endTime: '',
                    master: '',
                    masterName: '',
                    step: false,
                    date: this.date

                };
                event.stopPropagation();
            },


            setMaster:function () {

                window.newImplement = ({
                    'master':this.newOrder.master,
                    'startTime':this.newOrder.startTime,
                    'endTime':this.newOrder.endTime,
                });


                window.addEventListener('click', appendImplement);

                this.open = false;
            },

            // MAP
            layerChanged(layerId, active) {
                const layer = this.layers.find(layer => layer.id === layerId);

                layer.features.forEach((feature) => {
                    if (active) {
                        feature.leafletObject.addTo(this.map);
                    } else {
                        feature.leafletObject.removeFrom(this.map);
                    }
                });
            },
            initLayers() {

                this.markers[100000] = L.marker([this.mapData.lat, this.mapData.lon],{
                    icon: this.mapData.mainIcon,
                    'opacity': 1
                }).bindPopup("<b>Центр</b>").addTo(this.map);
                // this.markers[100000]._icon.id = key;

                let p = this.res.lid;
                for(let key in p) {

                    let masterIcon = L.divIcon({
                        className: 'icon-master',
                        html: p[key].master.join()
                    });
                    var person = p[key];
                    this.markers[key] = L.marker([p[key].customer.geo_lat, p[key].customer.geo_lon],{
                        icon: masterIcon,
                        'opacity': 1,
                        title:key
                    }).bindPopup("<b>"+p[key].customer.address+"</b>").addTo(this.map);
                    this.markers[key]._icon.id = key;
                }




                /*                this.layers.forEach((layer) => {
                                    const markerFeatures = layer.features.filter(feature => feature.type === 'marker');
                                    const polygonFeatures = layer.features.filter(feature => feature.type === 'polygon');

                                    markerFeatures.forEach((feature) => {
                                        feature.leafletObject = L.marker(feature.coords)
                                            .bindPopup(feature.name);
                                    });

                                    polygonFeatures.forEach((feature) => {
                                        feature.leafletObject = L.polygon(feature.coords)
                                            .bindPopup(feature.name);
                                    });
                                });*/
            },
            getRandomInt(min, max) {
                return Math.floor(Math.random() * (max - min)) + min;
            },
            initRoutes(){
                // console.log(this.res.schedule);
                // console.log(this.res.lid);


                for(let master in this.res.schedule){
                    if(this.res.schedule[master].volumes.length){
                        let start_lat = '55.77321337302965';
                        let start_lon = '37.50043094158173';
                        let color = this.colors[master];
                        for(let volume in this.res.schedule[master].volumes) {

                            let vol = this.res.schedule[master].volumes[volume];
                            let lid = this.res.lid[vol.lid_id];

                            Axios.get('/dadata/osm?lat='+ start_lat+'&lon='+start_lon+'&flat='+lid.customer.geo_lat+'&flon='+lid.customer.geo_lon)
                                .then(responce => {
                                    // console.log(responce.data);
                                    let coord = responce.data.coordinates;

                                    let u = [];
                                    for (let i in coord) {
                                        u[i] = [coord[i][1], coord[i][0]]
                                    }

                                    console.log(color);
                                    let polyline = L.polyline(u, {color: color}).addTo(this.map);
                                    // this.map.fitBounds(polyline.getBounds());

                                })
                                .catch(error => {
                                    console.log(error);
                                });
                            start_lat = lid.customer.geo_lat;
                            start_lon = lid.customer.geo_lon;


                            // console.log('vol',vol);
                            // console.log('lid',lid);
                        }



                    }
                }

/*
                L.Routing.control({
                    waypoints: [
                        L.latLng(57.74, 11.94),
                        L.latLng(57.6792, 11.949),
                        L.latLng(57.74, 12.94),
                    ]
                }).addTo(this.map);


                L.Routing.control({
                    waypoints: [
                        L.latLng(57.74, 12.94),
                        L.latLng(57.6792, 12.949),
                    ]
                }).addTo(this.map);*/





            },
            in_array(value, array) {
                for(var i=0; i<array.length; i++){
                    if(value == array[i]) return true;
                }
                return false;
            },
            personalOrders(masterID){
                console.log(masterID);
                let p = this.res.lid;
                let arMaster;
                for(let key in p) {
                    if(this.in_array(masterID,p[key].master)){
                        $('#'+key).css('opacity',1)
                    } else {
                        $('#'+key).css('opacity',.1)
                    }
                }
            },
            personalOrdersHide(){
                let p = this.res.lid;
                for(let key in p){
                    $('#'+key).css('opacity',1)
                }
            },
            initMap() {
                this.map = L.map('map').setView([ this.mapData.lat,  this.mapData.lon], 10);
                // this.map.layers = [grayscale, cities];
                this.tileLayer = L.tileLayer(
                    'https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}.png',
                    {
                        maxZoom: 18,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, &copy; <a href="https://carto.com/attribution">CARTO</a>',
                    }
                );
                // this.marker([55.751999, 37.617734]).addTo(map);
                this.tileLayer.addTo(this.map);


            }
        },
        watch: {
            open :  function ( val ,  oldVal )  {
                if (oldVal){
                    this.newOrder.start  = '';
                    this.newOrder.startTime  = '';
                    this.newOrder.end  = '';
                    this.newOrder.endTime  = '';
                    this.newOrder.master  = '';
                    this.newOrder.step  = false;
                    this.newOrder.date  = this.date;
                    jQ('body').css('overflow-y','initial');
                } else {
                    jQ('body').css('overflow-y','hidden');
                }
            }
        },

        created() {
            this.init();



            // this.res.lid.forEach((lid) => {
            //
            //     L.marker([lid.customer.geo_lat, lid.customer.geo_lon]).addTo(this.map);
            //     // feature.leafletObject = L.marker(feature.coords)
            //     //     .bindPopup(feature.name);
            //
            // });
        }


    }
</script>


<style></style>










