import axios from 'axios';
const token = localStorage.getItem('token');

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL + "/api",
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
    },
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api;
