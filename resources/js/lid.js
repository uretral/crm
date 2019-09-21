
import Vue from 'vue';
import * as uiv from 'uiv';

import vOutsideEvents from 'vue-outside-events';
Vue.use(vOutsideEvents);
Vue.use(uiv);
import Lid from "./components/lid/Lid";

Vue.component('Lid', Lid);

const eventBus = new Vue({
    el: '#lid',
     data: {
         date: ''
     }
});




