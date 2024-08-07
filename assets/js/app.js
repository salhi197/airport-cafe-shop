// assets/js/app.js

import Vue from 'vue';
import App from './App.vue'; // Assuming this is your main Vue component
import router from './router'; // Import Vue Router configuration

new Vue({
    el: '#app',
    router, // Inject the router into Vue instance
    render: h => h(App)
});
