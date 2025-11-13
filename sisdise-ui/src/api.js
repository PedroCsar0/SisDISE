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

// Pega o token do localStorage se ele existir (quando recarregar a pág)
const token = localStorage.getItem('authToken');
if (token) {
  // Adiciona o header de autorização para esta sessão
  api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// 5. Exporta a instância para usarmos em outros arquivos
export default api
