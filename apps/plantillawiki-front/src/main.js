import { createApp } from 'vue'
import './style.css'
import router from './router/router'
import vuetify from './plugins/vuetify'
import { createPinia } from 'pinia'
import permission from './directives/permission.js'
import roles from './directives/role.js'
import App from './App.vue'


const pinia = createPinia()
const app = createApp(App)
app.directive('permission', permission)
app.directive('role', roles)
app.use(router)
app.use(vuetify)
app.use(pinia)
app.mount('#app')
