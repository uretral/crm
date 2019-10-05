
import Vue from 'vue';
import * as uiv from 'uiv';
Vue.use(uiv);
import Pusher from "./components/Pusher/Pusher";

Vue.component('Pusher', Pusher);

const eventBus = new Vue({
    el: '#pusher',
     data: {
         date: ''
     }
});




