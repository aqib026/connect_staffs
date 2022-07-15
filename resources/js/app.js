/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require("./bootstrap");
import Vue from "vue";
window.Vue = Vue;

import Gate from "./Gate";
Vue.prototype.$gate = new Gate(window.user);

import { BootstrapVue } from "bootstrap-vue";
import vClickOutside from "v-click-outside";
import VueMask from "v-mask";
import Vuelidate from "vuelidate";
import VueSweetalert2 from "vue-sweetalert2";
import i18n from "./i18n";
import VueRouter from 'vue-router';
import axios from 'axios'

Vue.prototype.$isDev = process.env.MIX_APP_ENV !== "production";
Vue.config.devtools = Vue.prototype.$isDev;
Vue.config.debug = Vue.prototype.$isDev;
Vue.config.silent = !Vue.prototype.$isDev;
//import tinymce from "vue-tinymce-editor";

Vue.use(BootstrapVue);
Vue.use(vClickOutside);
Vue.use(VueMask);
Vue.use(Vuelidate);
Vue.use(VueSweetalert2);
Vue.use(VueRouter);
window.axios = require('axios');

Vue.component(
    "dynamic-component",
    require("./components/dynamic-component").default
);

import routes from './routes';

const router = new VueRouter({
    mode: 'history',
    routes
});
// Routes End

import "./views";
import Layouts from "./mixins/layouts.mixin";

const app = new Vue({
    el: "#app",
    router,
    mixins: [Layouts],
    i18n,
});

import moment from 'moment';

Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('ddd, MMM-D-yyyy hh:mm A')
    }
});
