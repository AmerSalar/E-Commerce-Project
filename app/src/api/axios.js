import axios from "axios";
import { router } from "../App";

const api = axios.create({
  baseURL: import.meta.env.VITE_BACKEND_URL + "/api", // Your backend URL
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  withCredentials: true,
});

api.interceptors.response.use(
  response => response,
  error => {
    if(error.response) {
      const status = error.response.status;

      if(status === 401) {
        const isAuthCheck = error.config.url?.includes('/me');
        const isLogin = error.config.url?.includes('/login');

        if(!isAuthCheck && !isLogin) {
          router.navigate('/login', {replace: true})
        }
      }

    }
    return Promise.reject(error);
  }
)

export default api;