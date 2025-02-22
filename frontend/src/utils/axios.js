import axios from 'axios'

const _axios = axios.create({
  baseURL: 'http://localhost:8000',
})
_axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token') || ''
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error),
)

export { _axios as axios }
