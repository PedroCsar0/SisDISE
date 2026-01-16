import axios from 'axios'
import { useUserStore } from './stores/userStore';
import router from './router';

// 1. Cria a "instância" pré-configurada do Axios
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      console.warn('Sessão expirada. Redirecionando...');

      const userStore = useUserStore();
      userStore.clearUser();

      localStorage.removeItem('authToken');
      sessionStorage.removeItem('authToken');

      router.push('/login');
    }
    return Promise.reject(error);
  }
);

export default api;
