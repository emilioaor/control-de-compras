/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import TranslationPlugin from './plugins/TranslationPlugin'
import VeeValidate from 'vee-validate';

Vue.use(TranslationPlugin);
Vue.use(VeeValidate);

Vue.mixin({
    filters: {
        date(d, time = false) {
            const date = new Date(d);
            let response = '';
            let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
            let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
            let year = date.getFullYear();

            response = month + '/' + day  + '/' + year;

            if (time) {
                let meridiam = 'AM';
                let hours = date.getHours();
                let minutes = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();

                if (hours > 12) {
                    hours -= 12;
                    meridiam = 'PM';
                }

                hours = (hours < 10) ? '0' + hours : hours;

                response += ' ' + hours + ':' + minutes + ' ' + meridiam;
            }

            return response;
        }
    }
});

const app = new Vue({
    el: '#app',
});
