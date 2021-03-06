
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.absent = [];

window.Vue = require('vue');

window.Bus = new Vue();

import VueLocalStorage from 'vue-localstorage'
Vue.use( VueLocalStorage, { name : 'ls'} );

var Store = new Vue();
Store.$ls.addProperty('viewChat',Boolean,true);

window.Store = Store;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// components
Vue.component('home', require('./components/Home.vue'));
Vue.component('page', require('./components/Page.vue'));
Vue.component('item', require('./components/Item.vue'));
Vue.component('vinput', require('./components/Vinput.vue'));
Vue.component('flash', require('./components/Flash.vue'));
Vue.component('headerSortButton', require('./components/HeaderSortButton.vue'));
Vue.component('users', require('./components/Users.vue'));
Vue.component('user', require('./components/User.vue'));
Vue.component('MyTickets', require('./components/MyTickets.vue'));
Vue.component('ProblemTickets', require('./components/ProblemTickets.vue'));
Vue.component('ClosedTickets', require('./components/ClosedTickets.vue'));
Vue.component('TicketMetrics', require('./components/TicketMetrics.vue'));
Vue.component('Tickets', require('./components/Tickets.vue'));
Vue.component('ClosedTicket', require('./components/ClosedTicket.vue'));
Vue.component('Ticket', require('./components/Ticket.vue'));
Vue.component('InOutBoard', require('./components/InOutBoard.vue'));
Vue.component('Toggle', require('./components/Toggle.vue'));
Vue.component('resetPassword',require('./components/ResetPassword.vue'));
Vue.component('itemDetail',require('./components/ItemDetail.vue'));

const app = new Vue({
    el: '#app'
});

window.flash = {
    success(message) {
        Bus.$emit('flash', { message, type : 'success' } );
    },

    warning(message) {
        Bus.$emit('flash', { message, type : 'warning' } );
    },

    danger(message) {
        Bus.$emit('flash', { message, type : 'danger' } );
    },

    error(message) {
        Bus.$emit('flash', { message, type : 'danger' } );
    },

    notify(message) {
        Bus.$emit('flash', {message, type : 'notify'});
    }
}

window.mouseDown = false;


document.body.onmousedown = function(evt) {
    if (evt.button == 0);
        mouseDown = true;
}
document.body.onmouseup = function(evt) {
    if (evt.button == 0);
        mouseDown = false;
}
