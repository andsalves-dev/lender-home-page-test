import Vue from "vue";
import VueRouter from "vue-router";
import * as axios from 'axios';
import BootstrapVue from 'bootstrap-vue';
import Vuex from 'vuex';
import LoadingOverlay from 'vue-loading-overlay';
import toastr from 'toastr';
import {axiosSetJwt} from "./constants/authHelper";

window.toastr = toastr;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
axios['defaults'].headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */
let csrfToken = document.head.querySelector('meta[name="csrf-token"]');

if (csrfToken) {
    axios['defaults'].headers.common['X-CSRF-TOKEN'] = csrfToken.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

let jwtToken = window.localStorage.getItem('jwt');

if (jwtToken) {
    axiosSetJwt(jwtToken);
}

Vue.use(VueRouter);
Vue.use(Vuex);
Vue.use(BootstrapVue);

Vue.component('loading-overlay', LoadingOverlay);
