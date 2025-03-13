import Vue from 'vue'
import { ToastPlugin, ModalPlugin } from 'bootstrap-vue'
import VueCompositionAPI from '@vue/composition-api'
import VueSweetalert2 from 'vue-sweetalert2'
import VueApexCharts from 'vue-apexcharts'

import router from './router'
import store from './store'
import App from './App.vue'

// Global Components
import './global-components'

// 3rd party plugins
import '@/libs/axios'
import '@/libs/clipboard'
import '@/libs/toastification'
import '@/libs/vue-select'


Vue.use(VueSweetalert2)
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)


// BSV Plugin Registration
Vue.use(ToastPlugin)
Vue.use(ModalPlugin)

// Composition API
Vue.use(VueCompositionAPI)

// Feather font icon - For form-wizard
// * Shall remove it if not using font-icons of feather-icons - For form-wizard
require('@core/assets/fonts/feather/iconfont.css') // For form-wizard

// import core styles
require('@resources/scss/core.scss')

// import assets styles
require('@resources/assets/scss/style.scss')

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
