import axios from "axios";

const api = axios.create({
  // baseURL: "https://minipos.dhcpharmacy.shop/api",
  baseURL: "http://127.0.0.1:8080/api",
  withCredentials: false,
  headers: {
    Accept: "application/json",
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) config.headers.Authorization = `Bearer ${token}`;

  config.headers["X-Tenant-ID"] = 1;
  
  return config;
});

export default api;
