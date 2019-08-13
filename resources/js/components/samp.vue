<template>
	<div>
		<div class="l-box">
			<!-- v-if="modal"-->
			<div class="l-box-inner">
				<a href="javascript:;" class="l-box-close" v-on:click="closeModal()">
					<i class="fa fa-close"></i>
				</a>
				<div class="l-box-content">
					<div class="l-box-header">
						<div class="l-box-header-left">
							<p>Выбор мастера {{date}}</p>
						</div>
						<div class="l-box-header-right">
							<a v-if="newOrder.step" class="badge submit" @click="saveMaster()">Сохранить</a>
							<a v-if="!markerShow" class="badge submit" @click="showAllOrders()">Показать все заказы</a>
						</div>
					</div>
					<div class="l-box-body">
						<div class="logistic" >
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
							</div>
							<div class="logistic-map" >

								<l-map :zoom="zoom"  :center="center">
									<l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
									<template v-for="(arOrders,masterKey) in ORDERS" v-if="markerShow || currentMaster === masterKey">
										<template v-for="(order,orderKey) in arOrders">
											<l-marker
													:options="{alt: orderKey,riseOnHover:true }"
													:lat-lng="setLatLng(order.ADDRESS.UF_GEO_LAT,order.ADDRESS.UF_GEO_LON)"
													@click="test()">

												<l-tooltip :options="{permanent: true, interactive: true, direction: 'auto'}">
													<div @click="innerClick" style="text-align: center;">
														{{orderKey}} ({{masterKey}})
														<p v-show="showParagraph && paragraphID === orderKey" v-html="bindText(order)"></p>
													</div>
												</l-tooltip>


											</l-marker>
										</template>
									</template>
								</l-map>
							</div>
							<div class="logistic-masters">
								<input type="hidden" v-model="mapTrigger = ORDERS"/>
								<!--<a @click="showLongText()" href="javascript:;">dasdasd</a>-->

								<div class="logistic-masters-table" v-for="(master,mKey) in MASTERS" v-bind:class="{'active': markerShow || currentMaster === mKey}">
									<a href="javascript:;"
									   @click="showOrdersByMaster(mKey)"
									   @dblclick="showAllOrders()"
									><span>{{master.NAME}} {{master.LAST_NAME}} </span></a>
									<div class="logistic-masters-cells">
										<label class="cell drag-nest"
										       v-for="hour in hours"
										       v-bind:id="mKey+'_'+hour"
										       @dragenter="dragEnter()"
										       @drop="dragDrop(mKey,hour)"
										       @dragover="dragOver()"
										       @dragleave="dragLeave()"
										       @click="makeOrder(hour,mKey)">
											{{hour}}
											<template v-if="ORDERS">
												<template v-if="ORDERS[mKey]" v-for="(o,oK) in ORDERS[mKey]">
													<span
															v-if="o.HOURS[0] === hour"
															v-bind:id="oK"
															v-bind:style="markBusy(o.HOURS)"
															class="drag"
															@dragover="dragCancel()"
															@dragstart="dragStart(oK,o.HOURS.length)"
															draggable="true" @drop="false"
															@click="stopClick(oK)"></span>
												</template>

											</template>
											<template v-if="newOrder.master === mKey  && newOrder.start === hour">
												<em class="new-order" @click="stopClick()" @dblclick="newOrderMarkRemove()"></em>
											</template>
										</label>
									</div>
								</div>

							</div>

						</div>
					</div>
					<div class="l-box-footer"></div>
				</div>
			</div>
		</div>
	</div>

</template>

<script>
    import Axios from 'axios';
    import {mapGetters} from 'vuex';
    import {LMap, LTileLayer, LMarker, LPopup, LTooltip} from 'vue2-leaflet';
    export default {
        name: "MapSetMaster",
        props: {
            modal: false,
            date: String
        },
        data() {
            return {
                hours: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
                newOrder: {
                    start: '',
                    startTime: '',
                    end: '',
                    endTime: '',
                    master: '',
                    step: false,
                    date: this.date
                },
                masters: {},
                // map
                map: null,
                tileLayer: null,

                showParagraph: false,
                paragraphID: '',

                zoom: 10,
                center: L.latLng(55.773216, 37.500298),
                url: 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}.png',
                // url:'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',

                markerShow: true,
                currentMaster: '',

                mapTrigger: false,

            }
        },
        computed: {
            ...mapGetters(['MASTERS', 'ORDERS']),

        },
        components: {
            LMap,
            LTileLayer,
            LMarker,
            LPopup,
            LTooltip
        },
        methods: {
            setLatLng(lat,lng){
                return L.latLng(lat,lng)
            },
            bindText(order){
                let str = '';
                for(let o in order.VOLUMES){
                    str +=  order.VOLUMES[o].THING +' '+ order.VOLUMES[o].AREA +' '+ order.VOLUMES[o].UF_VOLUME_AREA +' '+ order.VOLUMES[o].METHOD + '<br>';
                }
                return str;
            },
            showOrdersByMaster(key){
                this.markerShow = false;
                this.currentMaster = key;
            },
            showAllOrders(){
                this.markerShow = true;
                this.currentMaster = '';
            },
            innerClick() {
                alert('Click!');
            },
            stopClick(oK) {
                this.paragraphID = oK;
                this.showParagraph = !this.showParagraph;
                event.stopPropagation();
            },

            showLongText() {
                this.showParagraph = !this.showParagraph;
            },
            fetchMastersData: function () {
            },
            closeModal: function () {
                this.$emit('closeModal')
            },
            markBusy: function (arHours) {
                return 'width:' + ((arHours.length * 18) - 2) + 'px';
            },
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
                let elem = event.target;
                // if (!elem.classList.contains('drag'))
                //     elem.classList.add('drag-over');
                console.log('dragEnter');
                return true;
            },

            dragOver() {
                event.preventDefault();



            },
            dragLeave() {
                console.log('dragLeave');
                let elem = event.target;
                // elem.classList.remove('drag-over');
                // console.log('dragLeave');
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
                            date: '08.08.2018',//this.date,
                            order: order,
                            start: hour,
                            duration: orderLength,
                            master: master
                        };

                        Axios.post('https://www.mse-crm24.ru/crm/field/?tpl=ajax', data)
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
            makeOrder: function (hour, master) {
                if (this.newOrder.step) {
                    this.newOrder.end = hour;
                    this.newOrder.endTime = this.date + ' ' + hour + ':00:00';
                    let len = hour - this.newOrder.start + 1;
                    document.querySelector('.new-order').style.width = len * 17 + len - 2 + 'px';
                } else {
                    this.newOrder.start = hour;
                    this.newOrder.startTime = this.date + ' ' + hour + ':00:00';
                    this.newOrder.master = master;
                    this.newOrder.masterName = this.MASTERS[master].NAME + ' ' + this.MASTERS[master].LAST_NAME;
                    this.newOrder.step = true;
                }
            },
            orderStyle(){
                let len = this.newOrder.end - this.newOrder.start + 1;
                return len * 17 + len - 2 + 'px';
            },
            saveMaster: function () {
                this.$store.commit('SET_NEW_MASTER_ORDER', this.newOrder);
                this.closeModal();
            },
        },
        mounted() {
            this.fetchMastersData();
            Axios.get('https://www.mse-crm24.ru/crm/masters/?tpl=ajax&date=' + this.date)
                .then(response => {
                    this.$store.commit('SET_ORDERS', response.data.orders);
                })
                .catch(error => (console.log(error)));
        }
    }
</script>



<style>
	.disappear {
		opacity: .4;
	}

	#leafletmap {
		width: 100%;
		height: 100%;
	}

	.busy {
		background: #7d5be2;

	}

	.busy > i {
		color: #fff;
		font-weight: bold;
	}

	.drag {
		min-width: 17px;
		height: 17px;
		background: #7d5be2;
		position: absolute;
		z-index: 200;
		top: 0;
		left: 0;
		opacity: .8;
	}

	.drag.active {
		box-shadow: 0px 0px 7px 0px rgba(255, 0, 0, 0.79);
		-webkit-box-shadow: 0px 0px 7px 0px rgba(255, 0, 0, 0.79);
		-moz-box-shadow: 0px 0px 7px 0px rgba(255, 0, 0, 0.79);
	}

	.drag-nest {
		position: relative;
		user-select: none;
	}
    .drag-nest:hover {
        border: 1px solid red;
    }



	.drag-nest .drag {
		display: block;
		margin: 0;
		padding: 0;
		position: absolute;
		border: 2px solid #1990ea;

		top: -1px;
		left: -1px;
		z-index: 2;
		height: 15px;
		overflow: hidden;
		background: rgba(84, 166, 222, .6);
		cursor: pointer;
	}

	.drag-over {
		border: 1px solid red;

	}

	.new-order {
		display: block;
		margin: 0;
		padding: 0;
		position: absolute;
		border: 2px solid #fd9a18;

		top: -1px;
		left: -1px;
		z-index: 2;
		height: 15px;
		min-width: 15px;
		overflow: hidden;
		background: rgba(255, 155, 29, .6);
		cursor: pointer;
	}
</style>





