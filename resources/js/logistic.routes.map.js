import Vue from 'vue';
import * as uiv from 'uiv';
import vOutsideEvents from 'vue-outside-events';
require('leaflet-routing-machine');
Vue.use(vOutsideEvents);
Vue.use(uiv);
import LogisticRoutesMap from "./components/Logistic/LogisticRoutesMap";



Vue.component('LogisticRoutesMap', LogisticRoutesMap);

const eventBus = new Vue({
    el: '#logisticRoutesMap',
    data: {
        date: ''
    }
});
