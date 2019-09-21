
import Vue from 'vue';
import * as uiv from 'uiv';
import * as  Axios from 'axios';
// import  L from 'leaflet';
Vue.use(Axios);
import DadataAddress from "./components/Dadata/DadataAddress";

Vue.component('DadataAddress', DadataAddress);

const eventBus = new Vue({
    el: '#dadataAddress',
     data: {
         date: ''
     }
});




