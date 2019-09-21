import Vue from 'vue';
import * as uiv from 'uiv';
import vOutsideEvents from 'vue-outside-events';
require('leaflet-routing-machine');
Vue.use(vOutsideEvents);
Vue.use(uiv);
import MainLogisticMap from "./components/Logistic/MainLogisticMap";



Vue.component('MainLogisticMap', MainLogisticMap);

const eventBus = new Vue({
    el: '#mainLogisticMap',
    data: {
        date: ''
    }
});
