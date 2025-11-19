import axios from 'axios'

// 1. Cria a "instância" pré-configurada do Axios
const api = axios.create({
  // 2. Define a URL base da nossa API Laravel
  baseURL: 'http://localhost:8000/api',

  // 4. Define o header padrão que esperamos (JSON)
  headers: {
    Accept: 'application/json',
  },
});

// Tenta pegar do localStorage OU do sessionStorage
const token = localStorage.getItem('authToken') || sessionStorage.getItem('authToken');

if (token) {
  api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export default api;
