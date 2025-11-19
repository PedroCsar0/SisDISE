<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">
      <div class="p-8 md:p-10">

        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.54 14.54l-5.196 5.196a7.5 7.5 0 001.06 1.06L8.965 19.28l4.466-3.658A5.999 5.999 0 0121 8.5V7a2 2 0 01-2-2v.001z"/></svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900">Recuperar Senha</h2>
          <p class="text-sm text-gray-500 mt-2">
            Digite seu e-mail e enviaremos um link para você redefinir sua senha.
          </p>
        </div>

        <form @submit.prevent="handleForgotPassword" class="space-y-6">
          <div class="space-y-2">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 block">E-mail</label>
            <input
              v-model="email"
              type="email"
              required
              class="input-style-premium"
              placeholder="seu@email.com"
            />
          </div>

          <button
            type="submit"
            class="w-full py-3 px-4 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 disabled:opacity-70"
            :disabled="isSubmitting"
          >
            {{ isSubmitting ? 'Enviando...' : 'Enviar Link de Recuperação' }}
          </button>
        </form>

        <div class="mt-8 text-center">
          <router-link to="/login" class="text-sm font-bold text-gray-500 hover:text-primary transition-colors">
            &larr; Voltar para o Login
          </router-link>
        </div>

      </div>
      <div class="h-2 bg-gradient-to-r from-primary-light via-primary to-primary-dark"></div>
    </div>
  </div>
</template>

<style scoped>
.input-style-premium {
  width: 100%; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 10px; transition: all 0.2s;
}
.input-style-premium:focus {
  outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script setup>
import { ref } from 'vue';
import api from '@/api.js';
import { useToast } from "vue-toastification";

const email = ref('');
const isSubmitting = ref(false);
const toast = useToast();

const handleForgotPassword = async () => {
  isSubmitting.value = true;
  try {
    await api.post('/forgot-password', { email: email.value });
    toast.success("Link enviado! Verifique seu e-mail (ou log).");
    email.value = ""; // Limpa o campo
  } catch (error) {
    if (error.response && error.response.data.errors) {
      toast.error(Object.values(error.response.data.errors)[0][0]);
    } else {
      toast.error("Erro ao enviar o link. Verifique o e-mail.");
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>
