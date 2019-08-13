
import Vue from 'vue';
import * as uiv from 'uiv';
import vOutsideEvents from 'vue-outside-events';
Vue.use(vOutsideEvents);
Vue.use(uiv);
import LogisticRoutes from "./components/Logistic/LogisticRoutes";

Vue.component('LogisticRoutes', LogisticRoutes);

const eventBus = new Vue({
    el: '#logisticRoutes',
     data: {
         date: ''
     }
});




