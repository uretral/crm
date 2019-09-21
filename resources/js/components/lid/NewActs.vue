<template>
    <div>
        <div class="mod" v-if="open">

            <div class="mod-header">
                <div class="form-inline legend mse" style="margin: 0 auto">
                    <dadata-address
                        :mse="true"
                        :mainAddressData="{
                        address:lid.customer.address,
                        lat:lid.customer.geo_lat,
                        lon:lid.customer.geo_lon,
                        destination:lid.customer.destination,
                        region:lid.customer.region,
                        }"
                        :regions="helpers.regions"
                        @address="setAddress"
                        @saveAddressEntity="saveAddressEntity"
                        @mainAddress="mainAddress"

                    />
                </div>

                <label @click="$emit('closeNewActs')" class="mod-close"><span>&times;</span></label>
            </div>
            <div class="mod-body">
                <div class="logistic">
                    <table class="new-volumes">

                        <thead>
                        <tr>
                            <th class="times">Кратность</th>
                            <th class="volumes-td">Настройка обьемов </th>
                            <th class="dates">Периоды исполнения</th>
                        </tr>
                        </thead>


                        <tbody>
                        <tr v-for="(act,actKey) in acts">
                            <td class="times-td">
                                <input type="number"  class="form-control" v-model="act.datesCnt" style="width: 78px" />
                            </td>
                            <td class="volumes-td">
                                <table class="volumes">
                                    <thead>
                                    <tr>
                                        <th>Предмет работ:</th>
                                        <th>Метод:</th>
                                        <th>Площадь:</th>
                                        <th>Единица площади:</th>
                                        <th>Цена гост</th>
                                        <th>Цена факт</th>
                                        <th><a class="add btn btn-success btn-sm pull-right"><i class="fa fa-save"></i>&nbsp;Добавить</a></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="act.">
                                        <td>
                                            <select class="form-control">
                                            <option v-for="pest in helpers.pests" :value="pest.id">{{pest.name}}</option>

                                        </select></td>
                                        <td><select class="form-control">
                                            <option value="1">Холодный туман</option>
                                        </select></td>
                                        <td><input type="number" class="form-control"></td>
                                        <td><select class="form-control">
                                            <option value="1">Комната</option>
                                        </select></td>
                                        <td><input type="number" class="form-control"></td>
                                        <td><input type="number" class="form-control"></td>
                                        <td><a class="remove btn btn-warning btn-sm pull-right"><i class="fa fa-trash">&nbsp;</i>Удалить</a>
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
                                <table class="times-tbl" v-if="act.datesCnt > 0">

                                    <thead>
                                    <tr>
                                        <th><i class="fa fa-calendar"></i> с:</th>
                                        <th><i class="fa fa-calendar"></i> до:</th>
                                        <th><i class="fa fa-clock-o"></i> с:</th>
                                        <th><i class="fa fa-clock-o"></i> до:</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="dates in Number(act.datesCnt)">
                                        <td><input type="date" class="form-control"></td>
                                        <td><input type="date" class="form-control"></td>
                                        <td><input type="text" class="form-control" style="width: 60px"></td>
                                        <td><input type="text" class="form-control" style="width: 60px"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>

                    </table>


                </div>
            </div>


        </div>
    </div>
</template>

<script>
    import DadataAddress from "../Dadata/DadataAddress";
    export default {
        name: 'NewActs',
        props: ['open', 'helpers', 'lid'],
        data: function () {
            return {
                address: {
                    address: "",
                    lat: "",
                    lon: "",
                    destination: "",
                    region: "",
                },
                acts: [
                    {
                        act: {
                            act_nr: '',
                            address: this.address.address,
                            booking_act_file: null,
                            booking_act_signed: 0,
                            booking_act_transferred: 0,
                            destination: this.address.destination,
                            finished: 0,
                            floating: true,
                            floating_date_from: null,
                            floating_date_to: null,
                            id: null,
                            implement_act_file: null,
                            implement_act_signed: 0,
                            lat: this.address.lat,
                            lon: this.address.lon,
                            parent: this.lid.id,
                            region: this.address.region,
                        },
                        volumes: [],
                        dates:[],
                        datesCnt: 0,
                    }
                ],
                act: {
                    act_nr: '',
                    address: "",
                    booking_act_file: null,
                    booking_act_signed: 0,
                    booking_act_transferred: 0,
                    destination: "",
                    finished: 0,
                    floating: 0,
                    floating_date_from: null,
                    floating_date_to: null,
                    id: null,
                    implement_act_file: null,
                    implement_act_signed: 0,
                    lat: "",
                    lon: "",
                    parent: this.lid.id,
                    region: "",
                },
                volume: {
                    entity: null,
                    kpi: null,
                    lid_id: this.lid.id,
                    method: null,
                    note: null,
                    parent: null,
                    pest: null,
                    price_fact: null,
                    price_standard: null,
                    square: null,
                },
                implement: {
                    end_date: "",
                    id: null,
                    lid_id: this.lid.id,
                    master: null,
                    parent: null,
                    start_date: "",
                },

            }
        },
        methods: {
            // ADDRESS
            setAddress(obj){
                this.address = obj;
            },
            saveAddressEntity(obj){
                console.log(obj);
                this.$set(this.address,obj.entity, obj.value)
            },
            mainAddress(obj){
                this.address = obj;
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

        thead {
            border-bottom: 1px solid $inverse;
            .times {
                border-right: 1px solid $inverse;

            }
        }

        tbody {}

        tr {
            .dates {
                width: 380px;
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
                border-right: 1px solid $inverse;
                padding-top: 50px;
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


</style>










