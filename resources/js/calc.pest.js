
import Vue from 'vue';
import * as uiv from 'uiv';
Vue.use(uiv);
import CalcPest from "./components/Calc/CalcPest";

Vue.component('CalcPest', CalcPest);

const eventBus = new Vue({
    el: '#calcPest',
     data: {
         date: ''
     }
});




