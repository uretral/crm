<template>
    <div>
        <div class="mod" v-if="open">

            <div class="mod-header">
                <div class="form-inline legend mse" style="margin: 0 auto">
                    <dadata-address
                        :mse="true"
                        :main_btn="false"
                        :main="{
                        address:lid.customer.address,
                        lat:lid.customer.lat,
                        lon:lid.customer.lon,
                        destination:lid.customer.destination,
                        region:lid.customer.region,
                        }"
                        :regions="helpers.regions"
                        @setAddress="setAddress"
                        @saveAddressEntity="saveAddressEntity"
                        @mainAddress="mainAddress"
                    />
                    <div class="form-group mse">
                        <label>&nbsp;</label>
                        <btn @click="fillPull()" type="primary">Добавить обьем</btn>
                    </div>

                    <div class="form-group mse">
                        <label>&nbsp;</label>
                        <template v-if="pull.length">
                            <btn v-if="show" @click="removeActs()" type="warning">Вернуться</btn>
                            <btn v-else @click="createActs()" type="warning">Проверить</btn>
                            <btn @click="saveActs()" type="warning">Сохранить акт(ы)</btn>
                        </template>


                    </div>
                </div>


                <label @click="$emit('closeNewActs')" class="mod-close"><span>&times;</span></label>
            </div>
            <div class="mod-body">
                <div>
                    <template v-if="!show">
                        <table class="new-volumes">

                            <thead>
                            <tr>
                                <th class="times">Кратность</th>
                                <th class="volumes-td"> Обьемы по периодам</th>
                                <th class="dates">Периоды исполнения</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(pullItem,pullKey) in pull">
                                <td class="times-td">
                                    <input type="number" min="0"  class="form-control" v-model="pullItem.periodsCnt" @change="changePeriods(pullKey)" style="width: 78px" />
                                </td>
                                <td class="volumes-td">
                                    <table class="volumes">
                                        <thead>
                                        <tr>
                                            <th> <btn @click="removePullItem(pullKey)"  type="danger">Удалить обьем</btn></th>
                                            <th>Предмет работ:</th>
                                            <th>Метод:</th>
                                            <th>Площадь:</th>
                                            <th>Единица площади:</th>
                                            <th>Цена гост</th>
                                            <th>Цена факт</th>
                                            <th>
                                                <btn @click="addVolume(pullKey)" type="success">Добавить</btn>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(volume,volumeKey) in pullItem.volumes">
                                            <td colspan="2">
                                                <select v-model="volume.pest" class="form-control">
                                                    <option v-for="pest in helpers.pests" :value="pest.id">{{pest.name}}</option>
                                                </select></td>
                                            <td>
<!--                                                <select class="form-control" v-model="volume.method">
                                                <template v-if="volume.pest">
                                                    <option v-for="h in helpers.pests[volume.pest].methods" :value="helpers.methods[h].id">{{helpers.methods[h].name}}</option>
                                                </template>-->
<!--                                            </select>-->
                                                <template v-if="volume.pest">
                                                    <template v-for="h in helpers.pests[volume.pest].methods">
                                                        <input type="checkbox"

                                                               :id="pullKey+'_pest_'+volumeKey+'_'+h"
                                                               :name="pullKey+'_pest_'+volumeKey"
                                                               v-model="volume.method"
                                                               :checked="volume.method.indexOf(+helpers.methods[h].id)>-1"
                                                               :value="helpers.methods[h].id"

                                                        >
                                                        <!-- @change="addActRelation('volume',volume.id,'method',volume.method,act.id)"      -->
                                                        <label class="methods_chb" :for="pullKey+'_pest_'+volumeKey+'_'+h">{{helpers.methods[h].name}}</label>
                                                    </template>
                                                </template>


                                            </td>
                                            <td><input type="number" v-model="volume.square" class="form-control"></td>
                                            <td><select class="form-control" v-model="volume.entity">
                                                <option v-for="square in helpers.square" :value="square.id">{{square.name}}</option>
                                            </select></td>
                                            <td>
                                                <input type="number" class="form-control" pattern="\d+(\.\d{2})?" v-model="volume.price_standard"></td>
                                            <td><input type="number" class="form-control" pattern="\d+(\.\d{2})?" v-model="volume.price_fact"></td>
                                            <td>
                                                <btn type="danger" @click="removeVolume(pullKey,volumeKey)">Удалить</btn>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="7"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </td>
                                <td class="dates">
                                    <table class="times-tbl" v-if="pullItem.periodsCnt > 0">

                                        <thead>
                                        <tr>
                                            <th><i class="fa fa-calendar"></i> с:</th>
                                            <th><i class="fa fa-calendar"></i> до:</th>
                                            <th><i class="fa fa-clock-o"></i> с:</th>
                                            <th><i class="fa fa-clock-o"></i> до:</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="period in pullItem.periods">
                                            <td><input type="date" v-model="period.floating_date_from" class="form-control"></td>
                                            <td><input type="date" v-model="period.floating_date_to" class="form-control"></td>
                                            <td><input type="text" v-model="period.prefer_time_from" class="form-control" style="width: 60px"></td>
                                            <td><input type="text" v-model="period.prefer_time_to" class="form-control" style="width: 60px"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>

                        </table>
                    </template>





                    <template v-if="show">
                        <table class="volumes-preview" v-for="(act,actKey) in acts">

                            <thead>
                            <tr>
                                <th>
                                    <span>#:<input type="text" class="form-control" v-model="act.act_nr" style="width: 80px; display: inline-block;"/></span>
                                    <span>{{act.floating_date_from}} - {{act.floating_date_to}}</span>
                                    <span> | </span>
                                    <span>{{act.prefer_time_from}}<i>-</i> {{act.prefer_time_to}}</span>
                                </th>
                                <th>Предмет работ:</th>
                                <th>Метод:</th>
                                <th>Площадь:</th>
                                <th>Единица площади:</th>
                                <th>Цена гост:</th>
                                <th>Цена факт:</th>
                            </tr>
                            </thead>


                            <tbody>
                            <tr v-for="(vol,volLey) in act.volumes">
                                <td></td>
                                <td>{{helpers.pests[vol.pest].name}}</td>
                                <td>
                                    <template v-for="part in vol.method">
                                        {{helpers.methods[part].name}}
                                    </template>
                                </td>
                                <td>{{vol.square}}</td>
                                <td>{{helpers.square[vol.entity].name}}</td>
                                <td>{{vol.price_standard}}</td>
                                <td>{{vol.price_fact}}</td>
                            </tr>
                            </tbody>

                        </table>
                    </template>




                </div>
            </div>


        </div>
    </div>
</template>

<script>
    import DadataAddress from "../Dadata/DadataAddress";
    import Axios from 'axios';
    export default {
        name: 'NewActs',
        props: ['open', 'helpers', 'lid','actsCount'],
        data: function () {
            return {
                show:false,
                acts:[],
                pull: [],
                actPrototype: {
                    act_nr: '',
                    address: '',
                    booking_act_file: null,
                    booking_act_signed: 0,
                    booking_act_transferred: 0,
                    destination: '',
                    finished: 0,
                    floating: true,
                    floating_date_from: null,
                    floating_date_to: null,
                    prefer_time_from: null,
                    prefer_time_to: null,
                    id: null,
                    implement_act_file: null,
                    implement_act_signed: 0,
                    lat: '',
                    lon: '',
                    parent: this.lid.id,
                    region: '',
                },
                volumePrototype: {
                    entity: null,
                    kpi: null,
                    lid_id: this.lid.id,
                    method: [],
                    note: null,
                    parent: null,
                    pest: null,
                    price_fact: null,
                    price_standard: null,
                    square: null,
                },
                implementPrototype: {
                    end_date: "",
                    id: null,
                    lid_id: this.lid.id,
                    master: null,
                    parent: null,
                    start_date: "",
                },


                sample: [{"volumes":[{"entity":1,"kpi":null,"lid_id":421,"method":[7],"note":null,"parent":null,"pest":2,"price_fact":"2000","price_standard":null,"square":"2"},{"entity":1,"kpi":null,"lid_id":421,"method":[1],"note":null,"parent":null,"pest":4,"price_fact":"3000","price_standard":null,"square":"2"}],"periods":[{"floating_date_from":"2019-09-01","floating_date_to":"2019-09-30","prefer_time_from":null,"prefer_time_to":null},{"floating_date_from":"2019-10-01","floating_date_to":"2019-10-30","prefer_time_from":null,"prefer_time_to":null},{"floating_date_from":"2019-11-01","floating_date_to":"2019-11-30","prefer_time_from":null,"prefer_time_to":null}],"periodsCnt":"3"},{"volumes":[{"entity":1,"kpi":null,"lid_id":421,"method":[20],"note":null,"parent":null,"pest":2,"price_fact":"1500","price_standard":null,"square":"2"},{"entity":4,"kpi":null,"lid_id":421,"method":[5],"note":null,"parent":null,"pest":3,"price_fact":"14000","price_standard":null,"square":"50"}],"periods":[{"floating_date_from":"2019-08-01","floating_date_to":"2019-08-30","prefer_time_from":null,"prefer_time_to":null},{"floating_date_from":"2019-10-01","floating_date_to":"2019-10-30","prefer_time_from":null,"prefer_time_to":null},{"floating_date_from":"2019-12-01","floating_date_to":"2019-12-30","prefer_time_from":null,"prefer_time_to":null}],"periodsCnt":"3"}],
            }
        },
        methods: {
            // SETTINGS
            alert (content) {
                this.$alert({
                    title: 'Внимание!!!',
                    content: content
                }, (msg) => {
                    // callback after modal dismissed
                    this.$notify(`You selected ${msg}.`)
                })
            },
            // ADDRESS
            fillAddress(obj){
                this.actPrototype.address = obj.address;
                this.actPrototype.lat = obj.lat;
                this.actPrototype.lon = obj.lon;
                this.actPrototype.destination = obj.destination;
                this.actPrototype.region = obj.region;
            },
            setAddress(obj){ this.fillAddress(obj);},
            saveAddressEntity(ent){
                this.$set(this.actPrototype,ent.entity,ent.value)
            },
            mainAddress(obj){ this.fillAddress(obj);},
            // VOLUMES
            fillPull(){
                if(this.checkAddress()) {
                    this.pull.push({
                        volumes: [],
                        periods: [],
                        periodsCnt: 0,
                    });
                }
            },
            removePullItem(pullKey){
                this.pull.splice(pullKey,1);
            },
            addVolume(pullKey){
                let tmpVolume = this.volumePrototype;
                this.pull[pullKey].volumes.push({
                    entity: null,
                    kpi: null,
                    lid_id: this.lid.id,
                    method: [],
                    note: null,
                    parent: null,
                    pest: null,
                    price_fact: null,
                    price_standard: null,
                    square: null,
                });
            },
            removeVolume(pullKey,volumeKey){
                this.pull[pullKey].volumes.splice(volumeKey,1);
            },
            changePeriods(pullKey){
                let cnt = event.target.value;
                let periods = this.pull[pullKey].periods.length;
                let diff = Number(cnt) - Number(periods);
                console.log(diff);
                if(diff > 0){ // +
                    for(let i = 0, len = diff; i < len; i++) {
                        console.log('i',i);
                        this.pull[pullKey].periods.push(
                            {
                                floating_date_from: null,
                                floating_date_to: null,
                                prefer_time_from: null,
                                prefer_time_to: null,
                            }
                        );
                    }
                } else{
                    diff = -diff;
                    for(let i = 0, len = diff; i < len; i++) {
                        this.pull[pullKey].periods.pop();
                    }

                }
            },
            checkAddress(){
                if(!this.actPrototype.address){
                    this.alert('Не установлен адрес');
                    return false;
                }
                else if(!this.actPrototype.lat){
                    this.alert('Не установлена широта');
                    return false;
                }
                else if(!this.actPrototype.lon){
                    this.alert('Не установлена долгота');
                    return false;
                }
                else if(!this.actPrototype.region){
                    this.alert('Не установлен регион');
                    return false;
                }
                else if(!this.actPrototype.destination){
                    this.alert('Не установлено расстояние');
                    return false;
                } else {
                    return true;
                }
            },
            async createActs(){
                try {
                    let response = await Axios.get('/ajax/lid/act_per_date?arr='+JSON.stringify(this.pull));
                    console.log('-',response.data);
                    this.show = true;
                    let cnt = Number(this.actsCount);

                    for(let k in response.data){
                        cnt++;
                        let datetime = k.split('::');
                      await  this.acts.push({
                            act_nr: cnt,
                            address: this.actPrototype.address,
                            booking_act_file: null,
                            booking_act_signed: 0,
                            booking_act_transferred: 0,
                            destination: this.actPrototype.destination,
                            finished: 0,
                            floating: true,
                            floating_date_from: datetime[0],
                            floating_date_to: datetime[1],
                            prefer_time_from: datetime[2]?datetime[2]:'00',
                            prefer_time_to: datetime[3]?datetime[3]:'00',
                            implement_act_file: null,
                            implement_act_signed: 0,
                            lat: this.actPrototype.lat,
                            lon: this.actPrototype.lon,
                            parent: this.lid.id,
                            region: this.actPrototype.region,
                            volumes:response.data[k]
                        });

                    }

                } catch (error) {
                    console.log(error);
                }
            },
            removeActs(){
                this.acts = [];
                this.show = false;
            },
            async saveActs(){
                try {
                    let response = await Axios.get('/ajax/lid/save_float_acts?acts='+JSON.stringify(this.acts));
                    console.log(response.data);
                    if(response.data > 0){
                        this.acts = [];
                        this.$emit('updateActs');
                    }
                } catch (error) {
                    console.log(error);
                }


            }
        },
        watch: {
            open(n, o) {
                // document.getElementsByTagName('body').style.overflow = 'hidden';
/*                if(n){

                } else {
                    document.getElementsByTagName('body').style.overflow = 'unset';
                }*/
            }
        },
        components:{
            DadataAddress
        },
        created() {

        }


    }
</script>


<style lang="scss">
    /*@import "/public/less/vars.scss";*/
    $primary: #0275d8;
    $success: #5cb85c;
    $info: #5bc0de;
    $warning: #f0ad4e;
    $danger: #d9534f;
    $inverse: #292b2c;
    $faded: #f7f7f7;
    $important: #007bff;
    $secondary: #6c757d;
    $dark: #343a40;
    /*body {*/
    /*    overflow: hidden;*/
    /*}*/
    .mod-header {
        height: 100px;
    }
    .mod-body {
        /*overflow-y: visible;*/
    }
    input {
        &[type="date"] {
            width: 144px!important;
        }
        &.time {
            width: 60px;
        }
    }
    .new-volumes {
        width: 100%;
        text-align: left;

       > thead {
            border-bottom: 1px solid $inverse;
            .times {
                border-right: 1px solid $inverse;

            }
        }

       > tbody {
            > tr {
                border-bottom: 1px solid $inverse;

                .dates {
                    width: 460px;
                }

                th {
                    padding: 5px;
                    vertical-align: middle;
                }

                td {
                    padding: 5px;
                    vertical-align: top;
                }
                .volumes-td {
                    border-right: 1px solid $inverse;
                }
                .times-td {
                    width: 170px;
                    border-right: 1px solid $inverse;
                    padding-top: 50px;
                }
            }
        }


    }
    .volumes {
        thead {
            border: none;
            th {
                opacity: .6;
            }
        }
    }
    .times-tbl {
        thead {
            th {
                text-align: center;
                height: 38px;
            }

        }
    }
    .volumes-preview {
        margin: 0 auto 20px;
        > thead {
            border-bottom: 1px solid $inverse;
            font-size: 12px;

            > tr {
                > th {
                    padding: 5px;
                    height: 36px;
                    span {

                        display: inline-block;
                        padding-right: 10px;
                        i {
                            padding-left: 5px;
                            font-size: inherit;
                            font-weight: bold;
                        }
                    }
                }
                > th:nth-of-type(1) {
                    width: 380px;
                    background: $secondary;
                    color: $faded;
                    font-size: 15px;
                }
                > th:nth-of-type(2) {
                    width: 200px;
                }
                > th:nth-of-type(3) {
                    width: 200px;
                }
                > th:nth-of-type(4) {
                    width: 200px;
                }
                > th:nth-of-type(5) {
                    width: 200px;
                }
                > th:nth-of-type(6) {
                    width: 200px;
                }
                > th:nth-of-type(7) {
                    width: 200px;
                }
            }
        }
        > tbody {
            > tr {
                > td {
                    padding: 5px;
                }
            }
        }
    }


</style>










