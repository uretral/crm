<template>
    <div>

        <div class="form-inline">

            <dropdown class="form-group">
                <div class="input-group">
                    <input class="form-control" type="text" v-model="date">
                    <div class="input-group-btn">
                        <btn class="dropdown-toggle"><i class="glyphicon glyphicon-calendar"></i></btn>
                    </div>
                </div>
                <template slot="dropdown">
                    <li>
                        <date-picker v-model="date"/>
                    </li>
                </template>
            </dropdown>

            <dropdown ref="dropdown">
                <btn type="primary" class="dropdown-toggle">Диапазон дней (<b class="ddd">{{days}}</b>) <span class="caret"></span></btn>
                <template slot="dropdown">
                    <li v-for="i in 5"><a role="button" @click="setDays(i)">{{i}}</a></li>
                </template>
            </dropdown>

            <dropdown ref="dropdown">
                <btn type="primary" class="dropdown-toggle">Регион (<b class="ddd">{{region.region}}</b>) <span class="caret"></span></btn>
                <template slot="dropdown">
                    <li v-for="region in res.regions"><a role="button" @click="setRegion(region)">{{region.region}}</a></li>
                </template>
            </dropdown>
        </div>

        <div class="log-head">
            <div class="log-head-equipment">
                <a v-for="equipment in res.equipment" href="javascript:">{{equipment.code}}</a>
            </div>
            <div class="log-head-masters">
                <span>Мастера</span>
            </div>
            <div class="log-head-schedule" v-for="(v,k) in res.multiSchedule" >
                <a v-bind:href="'/logistic/routes/'+k+'/edit?region='+ region.region"><span>{{k}}</span></a>
                <a href="javascript:" v-if="k !== dateActive" @click="dateActive = k">Снаряжение</a>
            </div>
        </div>

        <div class="log">
            <div class="log-table">
                <div class="log-equipment">
                    <div v-for="(masterName,masterKey) in res.masters" class="log-equipment-td">
                        <a href="javascript:" v-for="equipment in res.equipment" @click="equipmentSet(masterKey,equipment)">
                                <span class="filled" v-if="res.multiSchedule[dateActive][masterKey]['equipment'][equipment.id]">
                                    {{equipment.code}} <sup>{{objSize(res.multiSchedule[dateActive][masterKey]['equipment'][equipment.id])}}</sup>
                                </span>
                                <span v-else>
                                    {{equipment.code}}
                                </span>
                        </a>
                    </div>
                </div>
                <div class="log-masters">
                    <div class="log-masters-td" v-for="(masterName,masterId) in res.masters">{{masterName.name}} ({{masterId}})</div>
                </div>
                <div class="log-schedule" >
<!--Days-->
                    <div v-for="(mastersDay,masterDate) in res.multiSchedule" v-bind:class="{active : masterDate === dateActive }" class="log-schedule-tr">

                        <template v-for="oneMasterDay in mastersDay">
                            <div  class="log-schedule-td">


                                <label v-for="hour in 24"  class="cell drag-nest" >
                                    {{hour-1}}
                                    <template v-for="volume in oneMasterDay.volumes" v-if="Number(volume.start) === hour-1">

                                        <a v-bind:href="'/crm/lids/'+volume.lid_id+'/edit'"
                                           v-bind:id="volume.id"
                                           v-bind:style="markBusy(volume.length)"
                                           class="drag"
                                           target="_blank"
                                        ></a>
                                    </template>

                                </label>

                                <span style="left:30px; width: 16px;"></span>

                            </div>
<!--                            <div class="log-penalty">-->
<!--                                -->
<!--                            </div>-->
                        </template>


                    </div>

                </div>
            </div>
        </div>

<!--res.equipment[currentEquipment].name + '/' + -->

        <modal v-if="modalEquipment"  v-model="modalEquipment" v-bind:title="res.masters[currentMaster]" :backdrop="false">
            <div class="equipment-set">
                    <template v-for="eq in equipmentSetArray">
                        <btn v-if="eq.master && eq.master !== Number(currentMaster)" input-type="checkbox"  v-bind:input-value="eq.id" v-model="equipmentItem" disabled>{{eq.inv_nr}}</btn>
                        <btn v-else-if="eq.master === Number(currentMaster)" input-type="checkbox"  v-bind:input-value="eq.id" v-model="equipmentItem" active>{{eq.inv_nr}}</btn>
                        <btn v-else input-type="checkbox"  v-bind:input-value="eq.id" v-model="equipmentItem">{{eq.inv_nr}}</btn>
                    </template>
                    Selected: {{equipmentItem}}
            </div>
            <div slot="footer">
                <btn type="warning" @click="equipmentRefresh()">Применить</btn>
            </div>
        </modal>

    </div>

</template>


<script>
    import Moment from 'moment';
    import Axios from 'axios';

    export default {
        name: 'LogisticRoutes',
        props: {},
        data: function () {
            return {
                date:  Moment().format('YYYY-MM-DD'), //'2019-05-07',
                days: 3,
                region: {
                    city: 'Москва',
                    region: 'Москва'
                },
                regions: '',
                res: '',
                dateActive: Moment().format('YYYY-MM-DD'),
                // equipment
                modalEquipment: false,
                equipmentSetArray: [],
                equipmentItem: [],
                currentMaster: null,
                currentEquipment: null
            }
        },
        watch: {
            date: function (newDate,oldDate) {
                this.init();
            },
            days: function (newDate,oldDate) {
                this.init();
            }
        },

        methods: {
            init: function (dateActive = false) {

                Axios.get('/logistic/map/many?date=' + this.date + '&days=' + this.days + '&region=' + this.region.region)
                    .then(response => {
                        this.res = response.data;
                        this.dateActive = dateActive?dateActive:this.date;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                this.open = true;
            },
            objSize: function(obj) {
                let size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size;
            },
            // setCities: function () {
            //     Axios.get()
            //
            // },
            setDays: function (n){
                this.days = n;

            },
            setRegion: function (n) {
                return this.region = n
            },
            showVolume: function(volumes){
                console.log(volumes);
                return '';
            },
            markBusy: function (len) {
                return 'width:' + (len * 17) + 'px';
            },
            equipmentSet: function (masterID,equipment){
                Axios.get('/logistic/map/get_equipment?date_active='+this.dateActive+'&master='+masterID+'&equipment_id='+equipment.id)
                    .then(response => {
                        this.equipmentItem = [];
                        this.currentEquipment = equipment.id;
                        for(let i in response.data){
                            if(response.data[i].master === Number(masterID)){
                                this.equipmentItem.push(response.data[i].id)
                            }
                        }
                        this.equipmentSetArray = response.data;
                        this.currentMaster = masterID;
                    })
                    .catch(error => {
                        console.log(error);
                    });
                this.modalEquipment = true;

            },
            equipmentRefresh: function() {
                let curDate = this.dateActive;
                let data = {
                    master:this.currentMaster,
                    date:this.dateActive,
                    equipment:this.currentEquipment,
                    equipmentItem:this.equipmentItem
                };

                Axios.post('/logistic/map/update_equipment',data)
                    .then(responce => {
                        // console.log(responce.data);
                        this.init(curDate);
                        this.modalEquipment = false;

                    })
                    .catch(error => {
                        console.log(error);
                    });
            },

            makeDays: function () {

            }
        },
        components: {

        },
        mounted() {
            this.init();
            // console.log('sdasdas', Number('003'));



        }
    };
</script>

<style>
    .ee {
        background: #fff;
    }


</style>
