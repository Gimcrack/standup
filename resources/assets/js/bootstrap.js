
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap-sass');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

let api_token = document.head.querySelector('meta[name="token"]');

if (api_token) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + api_token.content;
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'socket.io',
//     host: window.location.hostname + ':6001'
// });

require('font-awesome-webpack');
require('bootstrap-vertical-tabs');

window.sleep = require('sleep-promise');

import swal from 'sweetalert2';

window.swal = swal;

Array.prototype.$remove = function(key) {
    if ( typeof key !== "number"  )
    {
        return this.$remove( this.indexOf(key) );
    }

    Vue.delete(this, key);
    return this;
}

Array.prototype.$add = function(value) {
    if ( this.indexOf(value) === -1  )
    {
        this.push(value);
    }
    return this;
}

String.prototype.$ucfirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1).toLowerCase();
}

String.prototype.$title_case = function() {
    return this
        .split('_')
        .map( (piece) => piece.$ucfirst() )
        .join(' ');
}

String.prototype.$contains = function( search ) {
    return this.indexOf(search) !== -1;
}

import Api from './api';
window.Api = Api;

import moment from 'moment-timezone';
window.moment = moment;

window.fromNow = (dateTime) => {
    return moment(dateTime).fromNow();
}

// mixins
import Item from './components/mixins/item.js';
import Collection from './components/mixins/collection.js'

window.mixins = {
    item : Item,
    collection : Collection
}
