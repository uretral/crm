<template>
    <div>
        <div class="form-group" :class="mseClass()">
            <label>&nbsp;</label>
            <a href="javascript:" @click="setMainAddressData()"  class="btn btn-default">Основной:</a></div>

        <div class="form-group" :class="mseClass()">
            <label>Дадата:</label>
           <input type="checkbox" id="dadata" v-model="dadata" >
            <label for="dadata" class="check-label"></label>
        </div>
        <div class="form-group" :class="mseClass()">
            <label>Адрес:</label>
            <div class="drop-address">
                <input id="act.address" v-model="result.address" @keyup="dadataAddress()" @focusout="save('address')" type="text" class="form-control" style="width: 600px;">
                <ul style="width: 600px; right: 0px; left: unset;" v-if="dadata">
                    <li v-for="suggestion in suggestions"><a href="javascript:" @click="checkAddress(suggestion)">{{suggestion.unrestricted_value}}</a></li>
                </ul>
            </div>

        </div>
        <div class="form-group" :class="mseClass()">
            <label>Широта:</label>
            <input type="text" v-model="result.lat" @focusout="save('lat')" class="form-control" style="width: 110px;">
        </div>
        <div class="form-group" :class="mseClass()">
            <label>Долгота:</label>
            <input type="text" v-model="result.lon" @focusout="save('lon')" class="form-control"  style="width: 110px;">
        </div>
        <div class="form-group" :class="mseClass()">
            <label>Расстояние:</label>
            <input type="text" v-model="result.destination" @focusout="save('destination')" class="form-control" style="width: 110px;">
        </div>
        <div class="form-group" :class="mseClass()">
            <label>Регион:</label>
            <input type="text" v-model="result.region" @focusout="save('region')" class="form-control">
        </div>

        <modal v-model="modal" :title="'Карта расстояний '+result.destination+' км.'"  :footer="false" :dismiss-btn="false" size="lg">
            <div class="mapHolder">
                <div id="dMap" style="height: 500px;"></div>
            </div>

        </modal>
    </div>
</template>

<script>
    import Axios from 'axios';
    import L from 'leaflet';
    export default {
        name: 'DadataAddress',
        props: ['mse','mainAddressData','regions'],
        data() {
            return {
                result: {
                    address:'',
                    lat:'',
                    lon:'',
                    destination:'',
                    region:'',
                },
                suggestions:[],
                modal:false,
                flat:'',
                flon:'',
                map: null,
                dadata:true
            }
        },
        methods: {
            mseClass(){
                return this.mse ? 'mse':'';
            },
            setMainAddressData(){
                this.result = this.mainAddressData;
                this.$emit('mainAddress',this.result)
            },
            save(entity){
                this.$emit('saveAddressEntity',{
                    entity:entity,
                    value:this.result[entity]
                })

            },
            async dadataAddress(){
                if(this.dadata){
                    // this.result = {};
                    try {
                        let response = await Axios.get('/dadata/curl?a='+event.target.value);
                        console.log(response.data.suggestions);
                        this.suggestions = response.data.suggestions;
                    } catch (error) {
                        console.log(error);
                    }
                }

            },
            async checkAddress(suggestion){

                this.suggestions = [];

                this.result.address = suggestion.unrestricted_value;
                let center_lat,center_lon;
                for(let r in this.regions)if(this.regions[r].region === suggestion.data.region){
                    this.flat = this.regions[r].center_lat;
                    this.flon = this.regions[r].center_lon;
                }

                if(suggestion.data.geo_lat && suggestion.data.geo_lon &&  this.flat && this.flon){
                    this.result.lat = suggestion.data.geo_lat;
                    this.result.lon = suggestion.data.geo_lon;
                    this.result.region = suggestion.data.region;
                    await this.OSMLayers(
                        this.result.lat ,
                        this.result.lon,
                        this.flat,
                        this.flon
                    );
                }
            },
            async OSMLayers(lat,lon,flat,flon){
                try {
                    let response = await Axios.get('/dadata/osm?lat='+lat+'&lon='+lon+'&flat='+flat+'&flon='+flon);
                    if(response.data){
                        //
                        this.modal = true;
                        this.map = await new L.map('dMap');
                        this.map.setView( [flat, flon], 11);
                        L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}.png').addTo(this.map);
                        let u = [];
                        for (let i in response.data.coordinates) {
                            u[i] = [response.data.coordinates[i][1], response.data.coordinates[i][0]]
                        }
                        let polyline = L.polyline(u, {color: 'red'}).addTo(this.map);
                        this.map.fitBounds(polyline.getBounds());
                        this.$set(this.result,'destination',response.data.properties.distance);
                    }
                } catch (error) {
                    console.log(error);
                }
            },
        },
        watch: {
            modal(n,o){
                if(o){
                    this.map = null;
                    $('.mapHolder').html('').append('<div id="dMap" style="height: 500px;"></div>');
                    this.$emit('address',this.result);
                }


            }
        }

    };
</script>
<style lang="scss">
    .drop-address {
        display: inline-block;
        position: relative;
        > ul {
            position: absolute;
            top: 34px;
            left: 43px;
            z-index: 200;
            list-style: none;
            background: white;
            border: 1px solid #dbdbdb;
            border-top: none;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
            width: calc(100% - 43px);

            li {
                padding-top: 4px;

                &:last-of-type {
                    padding: 4px 0;
                }


            }
        }
    }

</style>
