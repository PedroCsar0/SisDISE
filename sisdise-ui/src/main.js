import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import Toast from "vue-toastification"
import "vue-toastification/dist/index.css"
import api from './api' 
import { useUserStore } from '@/stores/userStore'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(Toast)

const userStore = useUserStore()
const token = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')

if (token) {
    // 1. Injeta o token no Axios para ele não tomar erro 401
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`
    
    // 2. Tenta buscar os dados do usuário (nome, email, etc)
    userStore.fetchUser().catch(() => {
        console.log("Token inválido ou expirado ao iniciar.")
        localStorage.removeItem('authToken')
        sessionStorage.removeItem('authToken')
        userStore.clearUser()
    })
}

app.mount('#app')
