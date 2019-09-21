<template>
	<div>
		<div class="mod" v-if="open && date">

			<div class="mod-header">
				<span>{{date}}</span>
				<a v-if="newOrder.step" class="btn btn-success btn-sm" href="javascript:;" @click="setMaster">
                    <span>Назначить</span>
                </a>

                <label @click="$emit('closeMap')" class="mod-close"><span>&times;</span></label>
			</div>
			<div class="mod-body">
				<div class="logistic">
                  <!--	<div class="logistic-lost">
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



					<div class="logistic-map map" id="map" ref="mapElement"></div>

					<div class="logistic-masters">

						<div class="logistic-masters-table" v-for="(master,masterID) in res.schedule">
<!--Master and orders-->
							<a href="javascript:"
                               @click="personalOrders(masterID)"
                               @dblclick="personalOrdersHide(masterID)"
                            ><span>{{master.name.name}} ({{masterID}})</span></a>
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
    import L from '../leafLet';
    import jQ from 'jquery';
    export default {
        name: 'ImplementMap',
        props: ['date','region','lat','lon','open'],
        data: function () {
            return {
                // date: '',
                res: '',
                city: '',
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
                    lat: '',
                    lon: '',
                    pointLat: '',
                    pointLon: '',
                    pointAddress: '',
                    // url: 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}.png',
                    url:'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
                    markerShow: true,
                    currentMaster: '',
                    mapTrigger: false,
	            },
                markers: {}
            }
        },

        methods: {
            init: function (e, el) {
                if(this.date){
                    Axios.get('/logistic/map/new?date=' + this.date + '&region=' + this.region + '&action=masters')
                        .then(response => {
                            this.res = response.data;
                            this.initOSM(response.data);
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }

            },
	        initOSM(){
                this.initMap();
                this.initLayers();
	        },
            onClickOutside(e, el) {
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
            convertHour(hour){
                hour < 10 ? hour = '0'+hour : hour;
                return hour;
            },
            makeOrder: function (hour, master) {
                if (this.newOrder.step) {
                    this.newOrder.end = hour;
                    this.newOrder.endTime = this.date + 'T' + this.convertHour(hour) + ':00';
                    let len = Number(hour) - this.newOrder.start + 1;
                    document.querySelector('.new-order').style.width = len * 17 + 'px';
                } else {
                    this.newOrder.start = hour;
                    this.newOrder.startTime = this.date + 'T' + this.convertHour(hour) + ':00';
                    this.newOrder.endTime = this.date + 'T' + this.convertHour(hour) + ':00';
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
                this.$emit('newOrder',this.newOrder);
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
                let currentPointIcon = L.divIcon({
                    className:'icon-current',
                });
                this.markers['100000'] = L.marker([this.mapData.pointLat,this.mapData.pointLon],{
                    'opacity': 1,
                    icon: currentPointIcon
                }).bindPopup("<b>"+this.mapData.pointAddress+"</b>").addTo(this.map);
                this.markers['100000']._icon.id = 100000;

                let p = this.res.lid;
                for(let key in p) {
                    var person = p[key];
                    this.markers[key] = L.marker([p[key].customer.geo_lat, p[key].customer.geo_lon],{
                        'opacity': 1
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
            in_array(value, array) {
                for(var i=0; i<array.length; i++){
                    if(value == array[i]) return true;
                }
                return false;
            },
            personalOrders(masterID){
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
                this.map = L.map('map').setView([ this.lat,  this.lon], 10);
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
            date :  function ( val ,  oldVal )  {
                this.init();
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










