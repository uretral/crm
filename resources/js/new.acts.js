import Vue from 'vue';
import * as uiv from 'uiv';

Vue.use(uiv);
import NewActs from "./components/lid/NewActs";



Vue.component('NewActs', NewActs);

const eventBus = new Vue({
    el: '#newActs',
    data: {
        date: ''
    }
});
