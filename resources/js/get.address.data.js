import Vue from 'vue';
import * as uiv from 'uiv';
import vOutsideEvents from 'vue-outside-events';
require('leaflet-routing-machine');
Vue.use(vOutsideEvents);
Vue.use(uiv);
import GetAddressData from "./components/Logistic/GetAddressData";



Vue.component('GetAddressData', GetAddressData);

const eventBus = new Vue({
    el: '.getAddressData',
    data: {
        date: ''
    }
});
