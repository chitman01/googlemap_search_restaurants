require('./bootstrap');

window.Vue = require('vue');

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('index-component', require('./components/IndexComponent.vue').default);

const app = new Vue({
    el: '#app',
});
