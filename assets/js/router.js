// assets/js/router.js

import Vue from 'vue';
import Router from 'vue-router';
import Pos from './components/Pos.vue'; // Adjust the path as per your project structure

Vue.use(Router);

export default new Router({
    mode: 'history', // Use HTML5 history mode for cleaner URLs
    routes: [
        {
            path: '/pos',
            name: 'pos',
            component: Pos
        },
        // Add more routes as needed
    ]
});
