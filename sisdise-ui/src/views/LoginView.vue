<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">
      <div class="p-8 md:p-10">

        <div class="text-center mb-10">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
            <img class="h-10 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
          </div>
          <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Bem-vindo</h2>
          <p class="text-sm text-gray-500 mt-2">
            Faça login para continuar
          </p>
        </div>

        <form class="space-y-6" @submit.prevent="handleLogin">

          <div class="space-y-2">
            <label for="email" class="text-sm font-semibold text-gray-700 block">Endereço de e-mail</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
              </div>
              <input
                v-model="form.email"
                id="email"
                name="email"
                type="email"
                placeholder="seu@email.com"
                required
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-400"
              />
            </div>
          </div>

          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label for="password" class="text-sm font-semibold text-gray-700 block">Senha</label>
            </div>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
              </div>

              <input
                v-model="form.password"
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                required
                class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-400"
              />

              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer transition-colors"
              >
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                v-model="form.remember"
                id="remember-me"
                name="remember-me"
                type="checkbox"
                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded cursor-pointer"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900 cursor-pointer select-none">
                Lembrar
              </label>
            </div>

            <div class="text-sm">
              <router-link
                to="/forgot-password"
                class="font-medium text-primary hover:text-primary-dark transition-colors"
              >
                Esqueceu sua senha?
              </router-link>
            </div>
          </div>

          <button
            type="submit"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 transform hover:-translate-y-0.5"
          >
            Entrar no Sistema
          </button>
        </form>

        <div class="mt-8 text-center">
          <p class="text-sm text-gray-600">
            Não tem uma conta?
            <router-link to="/register" class="font-medium text-primary hover:text-primary-dark transition-colors">
              Cadastre-se aqui
            </router-link>
          </p>
        </div>
      </div>

      <div class="h-2 bg-gradient-to-r from-primary-light via-primary to-primary-dark"></div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification";

// Campos iniciam vazios para não sobrescrever o autofill do navegador
const form = reactive({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);
const router = useRouter();
const userStore = useUserStore();
const toast = useToast();

const handleLogin = async () => {
  try {
    const response = await api.post('/login', {
      email: form.email,
      password: form.password,
      // O campo remember não precisa ir para a API neste caso de token Sanctum simples,
      // a lógica é feita no frontend abaixo.
    });

    const token = response.data.token;

    // --- LÓGICA DE ARMAZENAMENTO CORRIGIDA ---
    // Limpa AMBOS primeiro para evitar conflitos
    localStorage.removeItem('authToken');
    sessionStorage.removeItem('authToken');

    if (form.remember) {
      // Se "Lembrar-me" estiver marcado -> localStorage (Persistente)
      localStorage.setItem('authToken', token);
    } else {
      // Se NÃO estiver marcado -> sessionStorage (Temporário - morre ao fechar navegador)
      sessionStorage.setItem('authToken', token);
    }
    // -----------------------------------------

    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    userStore.setUser(response.data.user);

    router.push('/dashboard');

  } catch (error) {
    console.error('Erro no login:', error);
    toast.error('E-mail ou senha inválidos. Tente novamente.');
  }
};
</script>
