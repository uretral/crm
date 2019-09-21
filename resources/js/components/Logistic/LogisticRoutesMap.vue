<template>
    <div>

        <modal v-model="warn" title="Внимание" auto-focus :dismiss-btn="false">
            <p v-html="warnText"></p>
        </modal>



        <modal v-model="modal" title="Акты" auto-focus :dismiss-btn="false">
            <table class="acts_tbl">
                <tbody>
                    <tr>
                        <td>Договор</td>
                        <td  v-for="act in acts">
                            <template v-if="act.lid">{{act.lid.contract}}</template>
                            <template v-else>не заполнен</template>
                        </td>
                    </tr>
                    <tr>
                        <td>№ акта</td>
                        <td  v-for="act in acts">
                            <template v-if="act.act_nr">{{act.act_nr}}</template>
                            <template v-else>не заполнен</template>
                        </td>
                    </tr>
                    <tr>
                        <td>Адрес</td>
                        <td  v-for="act in acts">
                            <template v-if="act.address">{{act.address}}</template>
                            <template v-else>не заполнен</template>
                        </td>
                    </tr>
                </tbody>

            </table>
            <div slot="footer">
                <btn @click="deactivateActs">Отмена</btn>
                <btn @click="ordersToMasters(acts)"  type="warning">Назначить мастера</btn>
            </div>
        </modal>

        <div class="mod static">

            <div class="mod-header">

                <span>{{date}}</span>

                <div class="current-acts" v-if="floatingActive">
                    <a class="btn btn-success btn-sm" href="javascript:" @click="setMaster" v-if="Object.keys(arNewOrders).length">
                        <span>Назначить</span>
                    </a>
                    <a  class="btn btn-warning btn-sm" href="javascript:" @click="deactivateActs">
                        <span>Деактивировать</span>
                    </a>
                    <a href="javascript:">
                        <span v-for="act in acts" v-if="act.lid">{{act.lid.contract}}</span>
                    </a>
                </div>
                <div class="actions" v-if="Object.keys(actionAct).length">
                    <div class="separator">
                        <b>Просмотреть акт(ы): </b>
                        <a target="_blank" class="btn btn-default"  v-for="(act,actID) in actionAct" v-bind:href="'/crm_entity/acts/'+actID+'/edit'">{{actID}}</a>
                    </div>
                    <div class="separator">
                        <a href="javascript:" @click="actsFulfilled()" class="btn btn-block"><span>Закрыть акты (выполнено)</span></a>
                    </div>
                    <div class="separator">
                        <a href="javascript:" @click="actionAct = {}" class="btn btn-warning"> Отмена </a>
                    </div>
                </div>
                <div v-if="print">
                    <div class="separator">
                        <a href="javascript:" @click="aside = !aside" class="btn btn-block"><span>Расписание</span></a>
                    </div>
                </div>
            </div>
            <div class="mod-body">
                <div class="logistic">
                   <!-- <div class="logistic-lost">
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

                    </div>-->

                    <div class="logistic-aside" v-if="aside">

                        <a href="javascript:"></a>
                        <!--style="display: none"-->
                        <div class="print" v-if="Object.keys(printMasterSchedule).length">
                            <table class="master-plan table " >
                                <thead>
                                <tr>
                                    <th>{{printMasterSchedule.name.name}}</th>
                                    <th colspan="5">{{date}}</th>
                                </tr>
                                <tr>
                                    <th>№ <br/> п/п</th>
                                    <th>Адрес обьекта <br/> Контактные данные <br/> Договор/оплата</th>
                                    <th>Описание обьекта <br/> (Тип площадь)</th>
                                    <th>Время</th>
                                    <th>Виды работ</th>
                                    <th>Дез средства</th>
                                    <th>Примечание</th>
                                </tr>
                                </thead>

                                <tbody>
                                <div class="hidden">{{nbr = 1}}</div>

                                <tr v-for="point in printMasterSchedule.volumes">
                                    <td> {{nbr++}}</td>
                                    <td>
                                        адрес: {{point.address}} <br/>
                                        имя: {{point.customer.name}} <br/>
                                        тел: {{point.customer.phone}} <br/>
                                        д-р №: {{point.lid.contract}} <br/>
                                        Оплата {{point.lid.customer_payment?'Нал':'Безнал'}}

                                    </td>
                                    <td>
                                        <template v-for="vol in point.volume">
                                            <template v-if="vol.pest">
                                                {{res.helpers.pests[vol.pest]['name']}}
                                            </template>
                                            <template v-if="vol.square">
                                                / {{vol.square}}
                                            </template>
                                            <template v-if="vol.entity">
                                                / {{res.helpers.square[vol.entity]['name']}} <br/>
                                            </template>
                                        </template>
                                    </td>
                                    <td>
                                        c: {{point.start}} - до: {{point.end}}

                                    </td>
                                    <td>
                                        <template v-for="meth in point.volume" v-if="meth.method">
                                             {{res.helpers.methods[meth.method]['name']}} <br/>
                                        </template>
                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>

                                </tr>
                                </tbody>

                            </table>
                        </div>


                    </div>


                    <div class="logistic-map map" id="map" ref="mapElement" style="height: 737px"></div>

                    <div class="logistic-masters">

                        <div class="logistic-masters-table" v-for="(master,masterID) in res.schedule" v-bind:id="'master_'+masterID">
                            <!--Master and orders-->

                            <a href="javascript:"
                               @click="personalOrders(masterID)"
                               @dblclick="personalOrdersHide(masterID)"
                            ><span>{{master.name.name}} ({{masterID}}) :: &nbsp;
                                                        <template v-if="Object.keys(res.schedule[masterID].equipment).length">
                                <i v-for="(equipment,equipmentID) in res.schedule[masterID].equipment">{{res.equipmentList[equipmentID]}}</i>
                            </template>
                            </span></a>


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
                                    @click="arMakeOrder(hour,masterID)">

                                    {{hour}}


                                    <template v-if="Object.keys(master.volumes).length">

                                        <template v-for="(volume,geoKey) in master.volumes" v-if="volume.start === hour">

											<span
                                                v-bind:id="volume.ids.join(':')"
                                                v-bind:style="markBusy(volume.length)"
                                                class="drag"
                                                @dragover="dragCancel()"
                                                @dragstart="dragStart(volume.ids.join(':'),volume.length)"
                                                draggable="true"
                                                @drop="false"
                                                :rel="geoKey"
                                                @dblclick="actClick(geoKey,masterID)"
                                                @click="stopClick(masterID,true)">

                                                <i v-for="acts in volume.acts"
                                                   @drop="false"
                                                   @dragover="dragCancel()"
                                                   @click="stopClick(masterID)">
<!--                                                    {{acts.id}}-->
                                                </i>
										</span>
                                        </template>
                                    </template>
                                    <template v-if="floatingActive && arNewOrders[masterID] && arNewOrders[masterID].start === hour">

                                        <em
                                            class="new-order"
                                            @click="stopClick()"
                                            @dblclick="newOrderMarkRemove(masterID)"
                                            v-bind:style="{width:arNewOrders[masterID].len}"
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
        props: ['date','region'],
        data: function () {
            return {
                res: '',
                floating: '',
                floatingPointID: '',
                floatingActive: false,
                acts: [],
                arActsID: [],
                wait: false, //  true
                open: false, //  true
                modal: false, //  true
                warn: false, //  true
                actionAct: {}, //  true
                warnText: '', //  true
                // city: 'Москва',
                // region: 'Москва',
                arNewOrders: {},
                showParagraph: false,
                paragraphID: '',
                print: false,
                aside: false,
                printMasterSchedule: {},
                map: null,
                tileLayer: null,
                // geoPoints: [],
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
                markersPoints: {},
                polyline: {},
                routes: [],
                stateColors: {
                    'done': 'green',
                    'doing': 'orange',
                    'todo': 'blue',
                    'late': 'red',
                    'error': 'brown',
                }
            }
        },

        methods: {
            init: function (e, el) {
                this.mapData.lat = '55.77321337302965';
                this.mapData.lon = '37.50043094158173';
                Axios.get('/logistic/map/get_by_geo?date=' + this.date + '&region=' + this.region + '&action=masters')
                    .then(response => {
                        this.res = response.data;
                        this.initOSM();
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                this.open = true;

                // ' + this.date + '
                Axios.get('/logistic/map/floating?date=' + this.date + '&region=' + this.region + '&action=masters')
                    .then(response => {
                        this.floating = response.data;
                        this.wait = !this.wait;
                    })
                    .catch(function (error) {
                        console.log('error',error);
                    });
            },
            initOSM(){
                this.initMap();
                //this.initFloatingLayers();
                this.initRoutes();
                this.initLayers();

            },
            onClickOutside(e, el) {
                // console.log('onClickOutside');
                // console.log('click heard outside element: ', el);
                // console.log('element clicked: '. e.target.te);
                // console.log('event: ', e);
            },
            openClose() {
                return this.open ? 'opened' : 'closed';
            },
            ordersToMasters(acts){
                this.floatingActive = true;
                this.modal = false;
            },
            markBusy: function (len) {
                return 'width:' + (len * 17) + 'px';
            },
            whileLoop(volume,hour){
                // // console.log(volume);
                // if(volume.start === hour){
                //     return true;
                // } else {
                //
                // }
                return true;

            },

            // DRAG
            dragStart(orderID, orderLength) {
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData("Text", event.target.getAttribute('id'));
                event.dataTransfer.setData("Order", orderID);
                event.dataTransfer.setData("Length", orderLength);
                event.dataTransfer.setData("Geo", event.target.getAttribute('rel'));
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

                        Axios.post('/logistic/map/update_implement', data)
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
            // Guess
            guess(){

            },

            // ORDERS
            actsFulfilled(){
                Axios.post('/logistic/map/acts_fulfilled',{data:JSON.stringify(this.actionAct)})
                    .then(response => {
                        if(response.data){
                            console.log('eee');
                            this.map.remove();
                            this.init();
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            stopClick(oK,act = false) {
                this.paragraphID = oK;
                this.showParagraph = !this.showParagraph;
                event.stopPropagation();
            },
            clearPolylineStyle(){
                for( let line in this.polyline){

                    this.polyline[line].setStyle({
                        weight: 3
                    });
                }
            },
            actClick(id,master){
                this.markersPoints[id].openPopup();
                this.actionAct = this.res.schedule[master].volumes[id].acts;
                this.clearPolylineStyle();

                this.polyline[id].setStyle({
                    weight:8
                });
                event.stopPropagation();
                // console.log();
                // console.log(master,id);
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
            arMakeOrder: function (hour, master) {
                if(this.acts.length === 0){
                    this.warnText = 'Сначала выберите акт';
                    this.warn = true;
                    return false;
                }

                if (this.arNewOrders[master] && 'step' in this.arNewOrders[master]) {
                    this.arNewOrders[master].end = hour;
                    this.arNewOrders[master].endTime = this.date + ' ' + (Number(hour) + 1) + ':00:00';
                    let len = hour - this.arNewOrders[master].start + 1;
                    this.arNewOrders[master].len = len * 17+ 'px';
                    // document.querySelector('.new-order').style.width = len * 17 + 'px';
                } else {
                    let Obj = {
                        start: hour,
                        startTime: this.date + ' ' + hour + ':00:00',
                        endTime: this.date + ' ' + hour + ':00:00',
                        master: master,
                        masterName: this.res.schedule[master].name.name,
                        step: true,
                        date: this.date,
                        end: '',
                        arActs: this.arActsID,
                        len: 17+ 'px',
                    };
                    this.$set(this.arNewOrders,master,Obj);
                }
            },
            newOrderMarkRemove: function (masterID) {
                this.$delete(this.arNewOrders,masterID);
                event.stopPropagation();
            },


            setMaster () {
                Axios.get('/logistic/map/update_implements?masters='+ JSON.stringify(this.arNewOrders)+'&acts='+JSON.stringify(this.arActsID))
                    .then(response => {
                        console.log('update_implements',response.data);
                        if (response.data == 'saved'){
                            // location.reload();
                            this.map.remove();
                            this.init();
                            this.floatingActive = false;

                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });


                // window.addEventListener('click', appendImplement);

                this.open = true;
            },
            deactivateActs(){
                this.acts = [];
                this.arActsID = [];
                this.arNewOrders = {};
                this.modal=false;
                this.floatingActive = false;
                $(document).find('.icon-floating').removeClass('active');
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
                this.markersPoints['main'] = L.marker([this.mapData.lat, this.mapData.lon],{
                    icon: this.mapData.mainIcon,
                    'opacity': 1
                }).bindPopup("<b>Центр</b>").addTo(this.map);
                // this.markers[100000]._icon.id = key;
                let markerMaster = {};
                for(let master in this.res.schedule){
                    for( let acts in this.res.schedule[master].volumes){
                        if(acts in markerMaster){
                            markerMaster[acts].push(this.res.schedule[master].volumes[acts]);
                        } else {
                            markerMaster[acts] = [this.res.schedule[master].volumes[acts]];
                        }
                    }
                }

                // console.log(markerMaster);

                for(let mark in markerMaster) {

                    console.log('mark',mark);

                    let contract_signed;
                    let contract_transferred; // Равиль
                    let contract_file;

                    let master = [];
                    let adress = '';
                    for( let acts in markerMaster[mark]){
                        master.push(markerMaster[mark][acts].master);

                    }

                    let html = `
                    <span class="contract"></span>
                    <span class="account-act"></span>
                    <span class="act"></span>
                    `;

                    let masterIcon = L.divIcon({
                        className: 'icon-master '+ markerMaster[mark][0].state,
                        html: master.join() + html
                    });
                    this.markersPoints[mark] = L.marker(mark.split('-'),{
                        icon: masterIcon,
                        'opacity': 1,
                        title: master.join(),
                        zIndexOffset: 1000000
                    }).
                    bindPopup("<b>"+markerMaster[mark][0].address+"</b>").addTo(this.map);
                    this.markersPoints[mark]._icon.id = mark;
                }


            },
            initFloatingLayers(){

                let that = this;
                for(let n in this.floating){

                    let floating = this.floating[n];
                    let latLon = n.split('-');
                    let html = '';
                    let arID = [];
                    if (floating.length > 1){
                        for(let f in floating) {
                            arID.push(floating[f].id);
                            html += '<span class="siblings">'+floating[f].id+'</span>'
                        }
                    } else {
                        arID.push(floating[0].id);
                        html = '<span>'+floating[0].id+'</span>';
                    }

                        let iconFloating = L.divIcon({
                            className: 'icon-floating',
                            html: html
                        });
                        this.markers[n] = L.marker(n.split('-'),{
                            icon: iconFloating,
                            'opacity': .2,
                            title: arID
                        })
                            .on('click', function(ev){
                                $(document).find('.icon-floating').removeClass('active');
                                ev.target._icon.classList.add('active');
                            that.$emit('floatingArrID', ev.target.options.title);
                        })
                            .addTo(this.map);
                            // .bindPopup('<b>'+floating[0].address+'</b>')
                        this.markers[n]._icon.id = n;
                }

            },
            markerClick(arID){
                    Axios.get('/logistic/map/get_acts?acts='+arID.join())
                    .then(request => {
                        this.acts = request.data;
                        this.arActsID = arID;
                        this.modal = true;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getRandomInt(min, max) {
                return Math.floor(Math.random() * (max - min)) + min;
            },
            initRoutes(){
                for(let master in this.res.schedule){

                    if(Object.keys(this.res.schedule[master].volumes).length){
                        let start_lat = '55.77321337302965';
                        let start_lon = '37.50043094158173';

                        let color = this.res.masters[master].color;

                        for(let volume in this.res.schedule[master].volumes) {
                            let arLatLon = volume.split('-');

                            let vol = this.res.schedule[master].volumes[volume];
                            let lid = this.res.lid[vol.lid_id];
                            let vilID = volume;

                            Axios.get('/dadata/osm?lat='+ start_lat+'&lon='+start_lon+'&flat='+arLatLon[0]+'&flon='+arLatLon[1])
                                .then(responce => {
                                    let coord = responce.data.coordinates;
                                    // let polyline = [];

                                    let u = [];
                                    for (let i in coord) {
                                        u[i] = [coord[i][1], coord[i][0]]
                                    }
                                    //vol.master

                                    this.polyline[vilID] = L.polyline(u, {
                                        color: color,
                                        // weight:1
                                    }).addTo(this.map);

                                    // this.map.fitBounds(polyline.getBounds());

                                })
                                .catch(error => {
                                    console.log(error);
                                });
                            start_lat = arLatLon[0];
                            start_lon = arLatLon[1];
                        }
                    }
                }

            },
            in_array(value, array) {
                for(var i=0; i<array.length; i++){
                    if(value == array[i]) return true;
                }
                return false;
            },
            checkIt (a) {
                if (a === undefined) return '';
                else if (a === null) return '';
                else return a;
            },
            changeOpacity(masterID,clear){
                for(let master in this.res.masters) {
                    if(clear){
                        $('#master_'+master).css('opacity',1)
                    } else {
                        master === masterID? $('#master_'+master).css('opacity',1) : $('#master_'+master).css('opacity',.4)
                    }

                }
            },
            personalOrders(masterID){
                this.clearPolylineStyle();
                for(let order in this.res.schedule[masterID].volumes) {
                    this.polyline[order].setStyle({
                        weight:10
                    })
                }
                this.changeOpacity(masterID,false);
                this.printMasterSchedule = this.res.schedule[masterID];
                this.print = true;

            },
            // printSchedule(){
            //     t
            //
            //
            // },
            personalOrdersHide(masterID){
                // let p = this.res.lid;
                // for(let key in p){
                //     $('#'+key).css('opacity',1)
                // }
                this.changeOpacity(masterID,true);
                this.clearPolylineStyle();
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
            wait: function () {
                this.initFloatingLayers()
            },
        },

        created() {
            this.init();
            this.$on('floatingArrID', (ev) => {
                this.markerClick(ev)
            });
        }


    }
</script>


<style></style>










