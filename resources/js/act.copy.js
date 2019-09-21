
import Vue from 'vue';
import * as uiv from 'uiv';
import vOutsideEvents from 'vue-outside-events';
Vue.use(vOutsideEvents);
Vue.use(uiv);
import ActCopy from "./components/lid/ActCopy";

Vue.component('ActCopy', ActCopy);

const eventBus = new Vue({
    el: '.actCopy',
     data: {
         date: ''
     }
});




