window.$ = window.jQuery = require('jquery');
global.$ = $;
global.jQuery = jQuery;
window._ = require('lodash');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Swiper = require('swiper');
window.enquire = require('enquire.js');

window.matchHeight = require('jquery-match-height');
$.matchHeight = require('jquery-match-height');

window.FilePond = require('filepond');
window.FilePondPluginImageExifOrientation = require('filepond-plugin-image-exif-orientation');
window.FilePondPluginFileValidateSize = require('filepond-plugin-file-validate-size');
window.FilePondPluginImageEdit = require('filepond-plugin-image-edit');
window.FilePondPluginImagePreview = require('filepond-plugin-image-preview');
window.FilePondPluginFileEncode = require('filepond-plugin-file-encode');
window.FilePondPluginFileValidateType = require('filepond-plugin-file-validate-type');

window.FilePond.registerPlugin(window.FilePondPluginFileEncode);
window.FilePond.registerPlugin(window.FilePondPluginFileValidateSize);
window.FilePond.registerPlugin(window.FilePondPluginFileValidateType);


window.EXIF = require('exif-js');

import $ from 'jquery';
import Vue from 'vue';
import Vuex from 'vuex';
import Snotify from 'vue-snotify';
import VueSilentbox from 'vue-silentbox';
import VueMasonry from 'vue-masonry-css'

Vue.use(Vuex);
Vue.use(Snotify);
Vue.use(VueMasonry);
Vue.use(VueSilentbox)

// Globally register all base components for convenience
const requireComponent = require.context('./components/', true, /\.(js|vue)$/i);

// For each matching file name...
requireComponent.keys().forEach((fileName) => {
    // Get the component config
    const componentConfig = requireComponent(fileName);
    // Get the PascalCase version of the component name
    const componentName = componentConfig.default.name || fileName
        // Remove the "./_" from the beginning
        .replace(/^\.\//, '')
        // Remove the file extension from the end
        .replace(/\.\w+$/, '')
        // Split up kebabs
        .split('-')
        // Upper case
        .map((kebab) => kebab.charAt(0).toUpperCase() + kebab.slice(1))
        // Concatenated
        .join('');

    // Globally register the component
    Vue.component(componentName, componentConfig.default || componentConfig)
});

Vue.prototype.$_debounce = function() {
    let methodName = arguments[0];
    if (typeof this[methodName] === 'undefined') {
        return;
    }
    arguments[0] = this[methodName];
    _.debounce(...arguments)();
};

Vue.directive('select2', {
    inserted(el) {
        $(el).on('select2:select', () => {
            const event = new Event('change', { bubbles: true, cancelable: true });
            el.dispatchEvent(event);
        });

        $(el).on('select2:unselect', () => {
            const event = new Event('change', { bubbles: true, cancelable: true });
            el.dispatchEvent(event)
        })
    },
});

Vue.directive('click-outside', {
    bind: function (el, binding, vNode) {
        el.__vueClickOutside__ = event => {
            // check that click was outside the el and his children
            if (!(el === event.target || el.contains(event.target))) {
                // and if it did, call method provided in attribute value
                vNode.context[binding.expression](event);
                event.stopPropagation();
            }
        };
        document.body.addEventListener('click', el.__vueClickOutside__);
    },
    unbind: function (el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
        el.__vueClickOutside__ = null;
    },
});

const store = new Vuex.Store({
    state: {
        selectFilters: {},
    },
    getters: {
        getSelectFilters: state => {
            return state.selectFilters;
        },
    },
    mutations: {
        addSelectFilter(state, payload) {
            window.vue.$set(state.selectFilters, payload.name, payload.value)
        },
        updateSelectFilters(state, payload) {
            state.selectFilters = _.isObject(payload) ? _.toArray(payload) : payload;
        },
    },
    actions: {
        updateSelectFilters(context, endpoint) {
            return new Promise(resolve => {
                axios.get(endpoint)
                    .then((data) => {
                        context.commit('updateSelectFilters', data.data.items);
                        resolve();
                    })
                    .catch(() => console.log(
                        `%c acd-pro %c Error occurred: failed getting endpoints %c`,
                        'background:#35495e ; padding: 1px; border-radius: 3px 0 0 3px;  color: #fff',
                        'background:#b41717 ; padding: 1px; border-radius: 0 3px 3px 0;  color: #fff',
                        'background:transparent'
                    ));
            });
        },
    }
})

window.vue = new Vue({
    el: '#app',
    data() {
        return {

        }
    },
    store: store
});
